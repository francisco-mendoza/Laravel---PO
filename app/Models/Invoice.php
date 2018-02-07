<?php

namespace App\Models;

use DB;


use OwenIt\Auditing\Auditable;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Invoice
 *
 * @property int $id_invoice
 * @property string $id_document
 * @property int $id_provider
 * @property string $billing_month
 * @property string $billing_year
 * @property float $total
 * @property float $total_iva
 * @property int $id_currency
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereBillingMonth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereBillingYear($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereIdCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereIdInvoice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereIdDocument($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereIdProvider($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereTotal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereTotalIva($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Area[] $areas
 * @property string $billing_day
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereBillingDay($value)
 */
class Invoice extends RossModel
{

    use Auditable;

    protected $table = 'invoices';
    protected $primaryKey = 'id_invoice';
    protected $fillable = [
        'id_invoice',
        'id_document',
        'id_currency',
        'id_provider',
        'billing_month',
        'billing_year',
        'billing_day',
        'total',
        'total_iva'
    ];

//    public $incrementing = false;

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];

    const INVOICE_ID = 0;
    const PROVIDER_NAME= 1;
    const TOTAL_INVOICE= 2;
    const TOTAL_OC= 3;
    const DATE_INVOICE = 4;


    /**
     * Many-to-Many relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function areas(){
        $result =  $this->belongsToMany('App\Models\Area','invoices_areas',
            'id_invoice','id_area');
        return $result;
    }

    public static function getInvoices($start, $len, $search, $column, $dir, $pending)
    {
        if(!$pending){
            $invoices = DB::table('invoices')
                ->leftJoin('provider', 'provider.id_provider', '=','invoices.id_provider')
                ->leftJoin('currency', 'currency.id_currency', '=','invoices.id_currency')
                ->select('invoices.id_document','provider.name_provider', DB::raw("CONCAT(CAST(FORMAT(invoices.total,2,'de_DE') as char),' ',currency.short_name) AS total"),'invoices.id_invoice')
                ->distinct();
        }
        else{
            $invoicesTotal = DB::table(DB::raw("(select i.id_invoice as id_invoice, ifnull(sum(io.subtotal),0) as facturado, i.total as total
                                             from invoices i
                                             left outer join invoices_orders io on i.id_invoice = io.id_invoice
                                             group by i.id_invoice, i.total) as fact"));

            $invoices = DB::table(DB::raw("({$invoicesTotal->toSql()}) as result"))
                ->leftJoin('invoices', 'invoices.id_invoice', '=','result.id_invoice')
                ->leftJoin('provider', 'provider.id_provider', '=','invoices.id_provider')
                ->leftJoin('currency', 'currency.id_currency', '=','invoices.id_currency')
                ->select('invoices.id_document','provider.name_provider', DB::raw("CONCAT(CAST(FORMAT(result.total,2,'de_DE') as char),' ',currency.short_name) AS total"),
                    DB::raw("CONCAT(CAST(FORMAT(result.facturado,2,'de_DE') as char),' ',currency.short_name) AS facturado"),
                    DB::raw("CONCAT(LPAD(invoices.billing_day,2,'0'),'-', LPAD(invoices.billing_month, 2,'0'), '-', invoices.billing_year) as invoice_purchase"), 'invoices.id_invoice')
                ->mergeBindings($invoicesTotal)
                ->whereRaw("result.facturado <= result.total")
                ->distinct();
        }


        if($start!== null && $len !== null){
            $invoices->skip($start)->limit($len);
        }

        if($search !== null){

            $invoices->where(function($query) use ($search){
                $query->where('id_document','like', '%'.$search.'%')
                    ->orWhere(DB::raw("CONCAT(CAST(FORMAT(invoices.total,2,'de_DE') as char),' ',currency.short_name)"),'like','%'.$search.'%')
                    ->orWhere('provider.name_provider','like', '%'.$search.'%');
            });

            if($pending){
                $invoices->orWhere(function($query) use ($search){
                    $query->where(DB::raw("CONCAT(LPAD(invoices.billing_day,2,'0'),'-', LPAD(invoices.billing_month, 2,'0'), '-', invoices.billing_year)"),'like', '%'.$search.'%');
                });
            }

        }

        if($column!== null && $dir !== null){
            switch($column){
                case self::INVOICE_ID:
                    $invoices->orderBy('id_document', $dir);
                    break;
                case self::PROVIDER_NAME:
                    $invoices->orderBy('provider.name_provider', $dir);
                    break;
                case self::TOTAL_INVOICE:
                    if(!$pending){
                        $invoices->orderBy('invoices.total', $dir);
                    }else{
                        $invoices->orderBy('result.total', $dir);
                    }
                    break;
                case self::TOTAL_OC:
                    $invoices->orderBy('result.facturado', $dir);
                    break;
                case self::DATE_INVOICE:
                    $invoices->orderBy( DB::raw("STR_TO_DATE(CONCAT(invoices.billing_day,'-', invoices.billing_month, '-', invoices.billing_year), '%d-%m-%Y') "), $dir);
                    break;
                default:
                    $invoices->orderBy('provider.name_provider', $dir);
                    break;
            }
        }else{

            $invoices->orderBy('provider.name_provider', 'asc');
        }

        return $invoices->get();
    }


    public static function getCountInvoice($search, $pending){

        if(!$pending){
            $count = DB::table('invoices')
                ->leftJoin('provider', 'provider.id_provider', '=','invoices.id_provider')
                ->leftJoin('currency', 'currency.id_currency', '=','invoices.id_currency')
                ->select('invoices.id_invoice','provider.name_provider', DB::raw("CONCAT(CAST(FORMAT(invoices.total,2,'de_DE') as char),' ',currency.short_name) AS full_name"))
                ->distinct();
        }
        else{
            $invoicesTotal = DB::table(DB::raw("(select i.id_invoice as id_invoice, ifnull(sum(io.subtotal),0) as facturado, i.total as total
                                             from invoices i
                                             left outer join invoices_orders io on i.id_invoice = io.id_invoice
                                             group by i.id_invoice, i.total) as fact"));

            $count = DB::table(DB::raw("({$invoicesTotal->toSql()}) as result"))
                ->leftJoin('invoices', 'invoices.id_invoice', '=','result.id_invoice')
                ->leftJoin('provider', 'provider.id_provider', '=','invoices.id_provider')
                ->leftJoin('currency', 'currency.id_currency', '=','invoices.id_currency')
                ->select('invoices.id_document','provider.name_provider', DB::raw("CONCAT(CAST(FORMAT(result.total,2,'de_DE') as char),' ',currency.short_name) AS total"),
                    DB::raw("CONCAT(CAST(FORMAT(result.facturado,2,'de_DE') as char),' ',currency.short_name) AS facturado"))
                ->mergeBindings($invoicesTotal)
                ->whereRaw("result.facturado < result.total")
                ->distinct();
        }



        if($search !== null){

            $count->where(function($query) use ($search){
                $query->where('id_document','like', '%'.$search.'%')
                    ->orWhere(DB::raw("CONCAT(CAST(FORMAT(invoices.total,2,'de_DE') as char),' ',currency.short_name)"),'like','%'.$search.'%')
                    ->orWhere('provider.name_provider','like', '%'.$search.'%');
            });
        }

        return $count->count();

    }




}
