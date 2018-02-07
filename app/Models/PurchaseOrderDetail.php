<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;

use DB;

/**
 * App\Models\PurchaseOrderDetail
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @mixin \Eloquent
 * @property int $id_purchase_order_detail
 * @property string $id_purchase_order
 * @property string $description
 * @property int $quantity
 * @property float $price
 * @property int $has_iva
 * @property float $price_iva
 * @property int $id_currency
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrderDetail whereIdPurchaseOrderDetail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrderDetail whereIdPurchaseOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrderDetail whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrderDetail whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrderDetail wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrderDetail whereHasIva($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrderDetail wherePriceIva($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrderDetail whereIdCurrency($value)
 */
class PurchaseOrderDetail extends RossModel
{
    use Auditable;

    protected $table = 'purchase_order_detail';

    protected $primaryKey = 'id_purchase_order_detail';
    protected $fillable = [
        'id_purchase_order_detail',
        'id_purchase_order',
        'description',
        'quantity',
        'price',
        'has_iva',
        'price_iva',
        'id_currency'
    ];


    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];

    public static function getOrderDetails($orderId){

        return PurchaseOrderDetail::where("id_purchase_order", "=", $orderId)->get();

    }

    public static function getFirstOrderDetails($orderId){

        return PurchaseOrderDetail::where("id_purchase_order", "=", $orderId)->first();

    }

    public static function deleteOrderDetails($orderId){

        return PurchaseOrderDetail::where("id_purchase_order", "=", $orderId)->delete();

    }

}
