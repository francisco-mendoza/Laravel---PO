<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;


use OwenIt\Auditing\Auditable;

/**
 * App\Models\InvoicesOrders
 *
 * @property int $id_invoice
 * @property string $id_purchase_order
 * @property float $subtotal
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InvoicesOrders whereIdInvoice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InvoicesOrders whereIdPurchaseOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InvoicesOrders whereSubtotal($value)
 * @mixin \Eloquent
 * @property float $exchange_rate
 * @property float $subtotal_po_currency
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InvoicesOrders whereExchangeRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\InvoicesOrders whereSubtotalPoCurrency($value)
 */
class InvoicesOrders extends RossModel
{
    use Auditable;

    protected $table = 'invoices_orders';
    protected $primaryKey = 'id_invoice';
    protected $fillable = [
        'id_invoice',
        'id_purchase_order',
        'subtotal',
        'exchange_rate',
        'subtotal_po_currency'
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];

    public static function findPurchaseOrdersByInvoiceId($id){

        $orders = DB::table('invoices_orders')
            ->leftJoin('purchase_order', 'purchase_order.folio_number', '=', 'invoices_orders.id_purchase_order')
            ->leftJoin('currency', 'currency.id_currency', '=','purchase_order.id_currency')
            ->where("invoices_orders.id_invoice", "=", $id)
            ->select('purchase_order.folio_number as folio_number',
                DB::raw("CONCAT(CAST(FORMAT(purchase_order.total_price,2,'de_DE') as char),' ',currency.short_name) AS total"),
                DB::raw("CONCAT(CAST(FORMAT(invoices_orders.subtotal_po_currency,2,'de_DE') as char),' ',currency.short_name) AS subtotal"));

        return $orders->get();

    }

    public static function findPurchaseOrdersIdsByInvoiceId($id){
        $orders = InvoicesOrders::where("invoices_orders.id_invoice", "=", $id)
            ->pluck('id_purchase_order');

        return $orders->toArray();
    }

    public static function getTotalInvoiced($id_invoice){
        return DB::table('invoices_orders')->where("invoices_orders.id_invoice", "=", $id_invoice)
                ->select(  DB::raw("ifnull(sum(subtotal),0) as total"))->first();
    }

    public static function getPurchaseOrdersByInvoiceId($id){

        $orders =  DB::table('invoices_orders')->leftJoin('purchase_order', 'purchase_order.folio_number', '=', 'invoices_orders.id_purchase_order')
            ->leftJoin('currency', 'currency.id_currency', '=','purchase_order.id_currency')
            ->where("invoices_orders.id_invoice", "=", $id)
            ->select('purchase_order.folio_number as id_purchase_order',
                DB::raw("FORMAT(invoices_orders.exchange_rate,4,'de_DE') AS exchange_rate"),
                DB::raw("FORMAT(invoices_orders.subtotal,2,'de_DE') AS subtotal"),
                DB::raw("CONCAT(CAST(FORMAT(invoices_orders.subtotal_po_currency,2,'de_DE') as char),' ',currency.short_name) AS subtotal_po_currency"),
                DB::raw("CONCAT(CAST(FORMAT(purchase_order.total_price - IFNULL(sum(invoices_orders.subtotal_po_currency),0),2,'de_DE')as char),' ',currency.short_name) as disp"))->distinct();

        $orders->groupBy("purchase_order.folio_number");

        return $orders->get();

    }


    public static function getInvoiceOrder($id_invoice, $id_purchase_order){
        $order = InvoicesOrders::where('id_invoice', "=", $id_invoice)
                    ->where('id_purchase_order', "=", $id_purchase_order)->first();

        return $order;
    }

    public static function updateInvoiceOrder($id_invoice, $id_purchase_order, $subtotal, $rate, $calculated){
        DB::table('invoices_orders')
            ->where('id_invoice', $id_invoice)
            ->where('id_purchase_order', $id_purchase_order)
            ->update(['subtotal' => $subtotal,
                      'exchange_rate' => $rate,
                      'subtotal_po_currency' => $calculated]);
    }

    public static function deleteOrdersByInvoiceId($id_invoice){
        DB::table('invoices_orders')->where('id_invoice', '=', $id_invoice)->delete();
    }

    public static function deleteOrdersByOCId($id_purchase_order){
        DB::table('invoices_orders')->where('id_purchase_order', '=', $id_purchase_order)->delete();
    }

    public static function deleteAreasInvoices($invoice){
        $result = DB::table('invoices_areas')->where('id_invoice', $invoice);
        $result->delete();
        return true;
    }

}
