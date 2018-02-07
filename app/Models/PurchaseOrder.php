<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;

use DB;

/**
 * App\Models\PurchaseOrder
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @mixin \Eloquent
 * @property int $folio_number
 * @property int $id_area
 * @property int $id_user
 * @property int $id_payment_condition
 * @property int $id_payment_method
 * @property string $contract_number
 * @property string $quotation_number
 * @property float $total_price
 * @property float $total_iva_price
 * @property int $id_currency
 * @property int $is_visible
 * @property string $date_purchase
 * @property int $paid_type
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereFolioNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereIdArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereIdUser($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereIdPaymentCondition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereIdPaymentMethod($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereContractNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereQuotationNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereTotalPrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereTotalIvaPrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereIdCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereIsVisible($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereDatePurchase($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder wherePaidType($value)
 * @property string $order_state
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereOrderState($value)
 * @property int $id_contract
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereIdContract($value)
 * @property float $exchange_rate
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereExchangeRate($value)
 * @property string $old_folio_number
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereOldFolioNumber($value)
 */

class PurchaseOrder extends RossModel
{
    use Auditable;

    protected $primaryKey = 'folio_number';

    public $incrementing = false;

    protected $table = 'purchase_order';

    protected $fillable = [
        'folio_number',
        'id_area',
        'id_user',
        'id_contract',
        'id_payment_condition',
        'id_payment_method',
        'contract_number',
        'quotation_number',
        'total_price',
        'total_iva_price',
        'id_currency',
        'is_visible',
        'date_purchase',
        'order_state',
        'exchange_rate',
        'old_folio_number',
        'paid_type',
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];


    const AREAS_NAME = 1;
    const USERNAME= 2;
    const FOLIO_NUMBER= 3;

    const TOTAL_PRICE = 4;
    const DATE_PURCHASE =  5;
    const ORDER_STATE =  6;

    const FILTER_PROVIDER_NAME =  4;

    const FILTER_TOTAL_PRICE = 5;
    const FILTER_DATE_PURCHASE =  6;
    const FILTER_ORDER_STATE =  7;
    

    protected function getDateFormat()
    {
        return 'd.m.Y H:i:s';
    }

    public static function getCountPurchaseOrders($states, $search, $filterArea, $filterUser){

        $count = DB::table('purchase_order')->leftJoin('areas', 'purchase_order.id_area', '=', 'areas.id_area')
            ->leftJoin('users', 'users.id_user', '=','purchase_order.id_user')
            ->leftJoin('currency', 'currency.id_currency', '=','purchase_order.id_currency')->distinct();

        if($filterUser !==null){
            $count->where('users.id_user','=', $filterUser);
        }

        if($states !== null){
            $count->whereIn('purchase_order.order_state', $states);
        }else{
            $count->whereIn('purchase_order.order_state', ["Emitida"]);
        }

        if($search !== null){

            $count->where(function($query) use ($search){
                $query->where('areas.long_name','like', '%'.$search.'%')
                    ->orWhere('users.firstname','like','%'.$search.'%')
                    ->orWhere('purchase_order.folio_number','like','%'.$search.'%')
                    ->orWhere(DB::raw("CONCAT(CAST(FORMAT(purchase_order.total_price,2,'de_DE') as char),' ',currency.short_name) "),'like','%'.$search.'%')
                    ->orWhere(DB::raw("DATE_FORMAT(purchase_order.date_purchase, '%d-%m-%Y')"),'like','%'.$search.'%')
                    ->orWhere('purchase_order.order_state','like','%'.$search.'%');
            });
        }

        if($filterArea !==null){
            $count->whereIn('areas.id_area', $filterArea);
        }

        return $count->count();
    }

    public static function PurchaseOrders($start, $len, $search, $column, $dir, $filterArea, $filterUser, $states){

        


        $orders =  DB::table('purchase_order')->leftJoin('areas', 'purchase_order.id_area', '=', 'areas.id_area')
            ->leftJoin('users', 'users.id_user', '=','purchase_order.id_user')
            ->leftJoin('currency', 'currency.id_currency', '=','purchase_order.id_currency')
            ->select('purchase_order.folio_number','areas.long_name','users.firstname' , 'purchase_order.folio_number as a', DB::raw("CONCAT(CAST(FORMAT(purchase_order.total_price,2,'de_DE') as char),' ',currency.short_name) AS full_name"), DB::raw("DATE_FORMAT(purchase_order.date_purchase, '%d-%m-%Y') as date_purchase"), 'order_state');

        if($filterUser !==null){
            $orders->where('users.id_user','=', $filterUser);
        }

        if($start!== null && $len !== null){
            $orders->skip($start)->limit($len);
        }

        if($search !== null){

            $orders->where(function($query) use ($search){
               $query->where('areas.long_name','like', '%'.$search.'%')
                    ->orWhere('users.firstname','like','%'.$search.'%')
                    ->orWhere('purchase_order.folio_number','like','%'.$search.'%')
                    ->orWhere(DB::raw("CONCAT(CAST(FORMAT(purchase_order.total_price,2,'de_DE') as char),' ',currency.short_name) "),'like','%'.$search.'%')
                    ->orWhere(DB::raw("DATE_FORMAT(purchase_order.date_purchase, '%d-%m-%Y')"),'like','%'.$search.'%')
                    ->orWhere('purchase_order.order_state','like','%'.$search.'%');
            });
        }

        if($filterArea !==null){
            $orders->whereIn('areas.id_area', $filterArea);
        }

        if($states !== null){
            $orders->whereIn('purchase_order.order_state', $states);
        }else{
            $orders->whereIn('purchase_order.order_state', ["Emitida"]);
        }

        if($column!== null && $dir !== null){
            switch($column){
                case self::AREAS_NAME:
                    $orders->orderBy('areas.long_name', $dir);
                    break;
                case self::USERNAME:
                    $orders->orderBy('users.firstname', $dir);
                    break;
                case self::FOLIO_NUMBER:
                    $orders->orderBy('purchase_order.folio_number', $dir);
                    break;
                case self::TOTAL_PRICE:
                    $orders->orderBy('purchase_order.total_price', $dir);
                    break;
                case self::DATE_PURCHASE:
                    $orders->orderBy('purchase_order.date_purchase', $dir);
                    break;
                case self::ORDER_STATE:
                    $orders->orderBy('purchase_order.order_state', $dir);
                    break;
                default:
                    if($states !== null){
                        $orders->orderBy('purchase_order.order_state', 'asc')
                                ->orderBy('purchase_order.date_purchase', 'des')
                                ->orderBy('purchase_order.folio_number', 'des');
                    }else{
                        $orders->orderBy('purchase_order.date_purchase', 'des')
                                ->orderBy('purchase_order.folio_number', 'des');
                    }

                    break;
            }
        }else{

            if($states !== null){
                $orders->orderBy('purchase_order.order_state', 'asc')
                       ->orderBy('purchase_order.date_purchase', 'des')
                       ->orderBy('purchase_order.folio_number', 'des');
            }else{
                $orders->orderBy('purchase_order.date_purchase', 'des')
                       ->orderBy('purchase_order.folio_number', 'des');
            }
        }

        $result =  $orders->get();

        return $result;
    }

    public static function getFirstOrderByAreaContracts($id_area,$id_contract){
        return PurchaseOrder::where('id_area','=',$id_area)
            ->where('id_contract','=',$id_contract)
            ->first();
    }

    public static function getOrdersByAreaAndContract($name_area,$id_contract){
        return DB::table('purchase_order')
            ->leftJoin('areas', 'purchase_order.id_area', '=', 'areas.id_area')
            ->where('areas.long_name', '=', $name_area)
            ->where('purchase_order.id_contract', "=", $id_contract)
            ->whereIn('purchase_order.order_state', ["Aprobada", "Emitida"])
            ->get();
    }


    public static function getCountFilteredPurchaseOrders( $search, $filterArea){

        if($search == null || $search == ""){

            return 0;

        }

        $count = DB::table('purchase_order')->leftJoin('areas', 'purchase_order.id_area', '=', 'areas.id_area')
            ->leftJoin('users', 'users.id_user', '=','purchase_order.id_user')
            ->leftJoin('currency', 'currency.id_currency', '=','purchase_order.id_currency')
            ->leftJoin('contract', 'contract.id_contract', '=','purchase_order.id_contract')
            ->leftJoin('provider', 'provider.id_provider', '=','contract.id_provider')
            ->leftJoin('purchase_order_detail', 'purchase_order.folio_number', '=','purchase_order_detail.id_purchase_order')->distinct();



        if($search !== null){

            $count->where(function($query) use ($search){
                $query->where('areas.long_name','like', '%'.$search.'%')
                    ->orWhere('users.firstname','like','%'.$search.'%')
                    ->orWhere('purchase_order.folio_number','like','%'.$search.'%')
                    ->orWhere('purchase_order_detail.description','like','%'.$search.'%')
                    ->orWhere('provider.name_provider','like','%'.$search.'%')
                    ->orWhere(DB::raw("CONCAT(CAST(FORMAT(purchase_order.total_price,2,'de_DE') as char),' ',currency.short_name) "),'like','%'.$search.'%')
                    ->orWhere(DB::raw("DATE_FORMAT(purchase_order.date_purchase, '%d-%m-%Y')"),'like','%'.$search.'%')
                    ->orWhere('purchase_order.order_state','like','%'.$search.'%');
            });
        }

        if($filterArea !==null){
            $count->whereIn('areas.id_area', $filterArea);
        }
        $count->whereNotIn('purchase_order.order_state', ['Eliminada']);

        return $count->count('purchase_order.folio_number');
    }

    public static function FilteredPurchaseOrders($start, $len, $search, $column, $dir, $filterArea){

        if($search == null || $search == ""){
            return [];
        }

        $orders =  DB::table('purchase_order')->leftJoin('areas', 'purchase_order.id_area', '=', 'areas.id_area')
            ->leftJoin('users', 'users.id_user', '=','purchase_order.id_user')
            ->leftJoin('currency', 'currency.id_currency', '=','purchase_order.id_currency')
            ->leftJoin('contract', 'contract.id_contract', '=','purchase_order.id_contract')
            ->leftJoin('provider', 'provider.id_provider', '=','contract.id_provider')
            ->leftJoin('purchase_order_detail', 'purchase_order.folio_number', '=','purchase_order_detail.id_purchase_order')
            ->select('purchase_order.folio_number','areas.long_name','users.firstname' , 'purchase_order.folio_number as a',  'provider.name_provider',DB::raw("CONCAT(CAST(FORMAT(purchase_order.total_price,2,'de_DE') as char),' ',currency.short_name) AS full_name"), DB::raw("DATE_FORMAT(purchase_order.date_purchase, '%d-%m-%Y') as date_purchase"), 'order_state')->distinct();

        if($start!== null && $len !== null){
            $orders->skip($start)->limit($len);
        }

        if($search !== null){

            $orders->where(function($query) use ($search){
                $query->where('areas.long_name','like', '%'.$search.'%')
                    ->orWhere('users.firstname','like','%'.$search.'%')
                    ->orWhere('purchase_order.folio_number','like','%'.$search.'%')
                    ->orWhere('purchase_order_detail.description','like','%'.$search.'%')
                    ->orWhere('provider.name_provider','like','%'.$search.'%')
                    ->orWhere(DB::raw("CONCAT(CAST(FORMAT(purchase_order.total_price,2,'de_DE') as char),' ',currency.short_name) "),'like','%'.$search.'%')
                    ->orWhere(DB::raw("DATE_FORMAT(purchase_order.date_purchase, '%d-%m-%Y')"),'like','%'.$search.'%')
                    ->orWhere('purchase_order.order_state','like','%'.$search.'%');
            });
        }

        if($filterArea !==null){
            $orders->whereIn('areas.id_area', $filterArea);
        }

        $orders->whereNotIn('purchase_order.order_state', ['Eliminada']);


        if($column!== null && $dir !== null){
            switch($column){
                case self::AREAS_NAME:
                    $orders->orderBy('areas.long_name', $dir);
                    break;
                case self::USERNAME:
                    $orders->orderBy('users.firstname', $dir);
                    break;
                case self::FOLIO_NUMBER:
                    $orders->orderBy('purchase_order.folio_number', $dir);
                    break;
                case self::FILTER_PROVIDER_NAME:
                    $orders->orderBy('provider.name_provider', $dir);
                    break;
                case self::FILTER_TOTAL_PRICE:
                    $orders->orderBy('purchase_order.total_price', $dir);
                    break;
                case self::FILTER_DATE_PURCHASE:
                    $orders->orderBy('purchase_order.date_purchase', $dir);
                    break;
                case self::FILTER_ORDER_STATE:
                    $orders->orderBy('purchase_order.order_state', $dir);
                    break;
                default:
                    $orders->orderBy('purchase_order.date_purchase', 'des')
                           ->orderBy('purchase_order.folio_number', 'des');
                    break;
            }
        }else{

            $orders->orderBy('purchase_order.date_purchase', 'des')
                   ->orderBy('purchase_order.folio_number', 'des');

        }

        $result =  $orders->get();

        return $result;
    }

    public static function getCountPurchaseOrderToBill($search, $filterArea, $filterMonthIni, $filterMonthEnd){

        if($search == null || $search == ""){
            return 0;
        }

        $count = DB::table('purchase_order')->leftJoin('areas', 'purchase_order.id_area', '=', 'areas.id_area')
            ->leftJoin('currency', 'currency.id_currency', '=','purchase_order.id_currency')
            ->leftJoin('contract', 'contract.id_contract', '=','purchase_order.id_contract')
            ->leftJoin('provider', 'provider.id_provider', '=','contract.id_provider')
            ->leftJoin('invoices_orders', 'purchase_order.folio_number', '=','invoices_orders.id_purchase_order')
            ->leftJoin('purchase_order_detail', 'purchase_order.folio_number', '=','purchase_order_detail.id_purchase_order')->distinct();



        if($search !== null){

            $count->where(function($query) use ($search){
                $query->where('purchase_order.folio_number','like','%'.$search.'%')
                    ->orWhere('purchase_order_detail.description','like','%'.$search.'%')
                    ->orWhere('provider.name_provider','like','%'.$search.'%');
            });
        }

        if($filterMonthIni != null){
            $count->where(function($query) use ($filterMonthIni){
                $query->where(DB::raw("substr(folio_number, length(folio_number)-1, length(folio_number))"),'>=',$filterMonthIni);

            });
        }

        if($filterMonthEnd != null){
            $count->where(function($query) use ($filterMonthEnd){
                $query->where(DB::raw("substr(folio_number, length(folio_number)-1, length(folio_number))"),'<=',$filterMonthEnd);

            });
        }

        if($filterArea !==null){
            $count->whereIn('areas.id_area', $filterArea);
        }
        $count->whereIn('purchase_order.order_state', ['Aprobada']);


        return $count->count('purchase_order.folio_number');
    }

    public static function PurchaseOrderToBill($start, $len, $search, $column, $dir, $filterArea, $filterMonthIni, $filterMonthEnd){

        if($search == null || $search == ""){
            return [];
        }

        $orders =  DB::table('purchase_order')->leftJoin('areas', 'purchase_order.id_area', '=', 'areas.id_area')
            ->leftJoin('currency', 'currency.id_currency', '=','purchase_order.id_currency')
            ->leftJoin('contract', 'contract.id_contract', '=','purchase_order.id_contract')
            ->leftJoin('provider', 'provider.id_provider', '=','contract.id_provider')
            ->leftJoin('purchase_order_detail', 'purchase_order.folio_number', '=','purchase_order_detail.id_purchase_order')
            ->leftJoin('invoices_orders', 'purchase_order.folio_number', '=','invoices_orders.id_purchase_order')
            ->select(DB::raw("CONCAT('','') AS a"),'purchase_order.folio_number','purchase_order.folio_number as detail','provider.name_provider',
                DB::raw("CONCAT(CAST(FORMAT(purchase_order.total_price,2,'de_DE') as char),' ',currency.short_name) AS full_name"),
                DB::raw("CONCAT(CAST(FORMAT(purchase_order.total_price - IFNULL(sum(invoices_orders.subtotal_po_currency),0),2,'de_DE')as char),' ',currency.short_name) as disp"))->distinct();
//                DB::raw("substr(folio_number, length(folio_number)-1, length(folio_number)) as month"))->distinct();

        if($start!== null && $len !== null){
            $orders->skip($start)->limit($len);
        }

        if($search !== null){
            $orders->where(function($query) use ($search){
                $query->where('purchase_order.folio_number','like','%'.$search.'%')
                    ->orWhere('purchase_order_detail.description','like','%'.$search.'%')
                    ->orWhere('provider.name_provider','like','%'.$search.'%');
            });
        }

        if($filterMonthIni != null){
            $orders->where(function($query) use ($filterMonthIni){
                    $query->where(DB::raw("substr(folio_number, length(folio_number)-1, length(folio_number))"),'>=',$filterMonthIni);

            });
        }

        if($filterMonthEnd != null){
            $orders->where(function($query) use ($filterMonthEnd){
                $query->where(DB::raw("substr(folio_number, length(folio_number)-1, length(folio_number))"),'<=',$filterMonthEnd);

            });
        }

        if($filterArea !==null){
            $orders->whereIn('areas.id_area', $filterArea);
        }

        $orders->whereIn('purchase_order.order_state', ['Aprobada']);

        $orders->groupBy("purchase_order.folio_number"); //AGREGADO POR EL JOIN CON INVOICES

        if($column!== null && $dir !== null){
            switch($column){
                case 2:
                    $orders->orderBy('purchase_order.folio_number', $dir);
                    break;
                case 3:
                    $orders->orderBy('provider.name_provider', $dir);
                    break;
                case 4:
                    $orders->orderBy('purchase_order.total_price', $dir);
                    break;
                default:
                    $orders->orderBy('purchase_order.date_purchase', 'des')
                        ->orderBy('purchase_order.folio_number', 'des');
                    break;
            }
        }else{

            $orders->orderBy('purchase_order.date_purchase', 'des')
                ->orderBy('purchase_order.folio_number', 'des');

        }

        return $orders->get();

    }


    public static function getTotalByMonth($id_area = null){


        $orders = DB::table('months')
                ->select(DB::raw("ifnull(sum(oc.total),0) as total"),"months.id_month")
                ->leftJoin(DB::raw("(select sum(po.total_price) as total, 
                                 convert(substr(po.folio_number, length(po.folio_number)-1, length(po.folio_number)), UNSIGNED INTEGER) as mes
                                 from purchase_order po, areas a 
                                 where a.id_area = po.id_area 
                                 and po.order_state not in ('Eliminada', 'Rechazada')
                                 and po.is_visible = 1
                                 and po.id_area = ?
                                 group by mes) as oc"),function($join) {
                    $join->on("months.id_month", "=", "oc.mes");
                })->addBinding($id_area,'select')
                ->groupBy("months.id_month");


        $orders = $orders->orderBy('months.id_month');

        return $orders->get();


    }


}
