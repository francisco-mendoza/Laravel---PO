<?php
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Currency
 *
 * @mixin \Eloquent
 * @property int $id_currency
 * @property string $name_currency
 * @property string $short_name
 * @property string $code
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Currency whereIdCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Currency whereNameCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Currency whereShortName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Currency whereCode($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 */
	class Currency extends \Eloquent {}
}

namespace App\Models{
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
 * @property string $order_state
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereOrderState($value)
 * @property int $id_contract
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PurchaseOrder whereIdContract($value)
 */
	class PurchaseOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Provider
 *
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @property int $id_provider
 * @property string $name_provider
 * @property string $business
 * @property string $rut
 * @property string $address
 * @property string $phone
 * @property string $contact_name
 * @property string $contact_area
 * @property string $contact_email
 * @property string $contact_phone
 * @property int $is_visible
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereIdProvider($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereNameProvider($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereBusiness($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereRut($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereContactName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereContactArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereContactEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereContactPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereIsVisible($value)
 * @property int $payment_conditions
 * @property int $payment_method
 * @property string $bank
 * @property string $type_account
 * @property int $number_account
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider wherePaymentConditions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider wherePaymentMethod($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereTypeAccount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereNumberAccount($value)
 */
	class Provider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentCondition
 *
 * @mixin \Eloquent
 * @property int $id_payment_conditions
 * @property string $name_condition
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PaymentCondition whereIdPaymentConditions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PaymentCondition whereNameCondition($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 */
	class PaymentCondition extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FontAwesome
 *
 * @mixin \Eloquent
 */
	class FontAwesome extends \Eloquent {}
}

namespace App\Models{
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
	class PurchaseOrderDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Contract
 *
 * @property int $id_contract
 * @property int $id_provider
 * @property string $start_date
 * @property string $end_date
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereIdContract($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereIdProvider($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereStartDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereEndDate($value)
 * @mixin \Eloquent
 * @property string $contract_number
 * @property string $description
 * @property string $contract_area
 * @property int $is_active
 * @property string $contract_pdf
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereContractNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereContractArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereIsActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereContractPdf($value)
 */
	class Contract extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MenuOption
 *
 * @mixin \Eloquent
 * @property int $id_menu
 * @property string $name_option
 * @property int $order_option
 * @property string $option_route
 * @property string $option_icon
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOption whereIdMenu($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOption whereNameOption($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOption whereOrderOption($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOption whereOptionRoute($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOption whereOptionIcon($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 */
	class MenuOption extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Role
 *
 * @mixin \Eloquent
 * @property int $id_role
 * @property string $description
 * @property bool $is_default
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereIdRole($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Role whereIsDefault($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
    /**
     * App\Models\Role
     *
     * @mixin \Eloquent
     * @property int $id_contract
     * @property int $id_area
     * @property string $account_code
     * @property string $account_year
     * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountContract whereAccountCode($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountContract whereAccountYear($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountContract whereIdArea($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountContract whereIdContract($value)
     * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
     */
    class AccountContract extends \Eloquent {}
}


namespace App\Models{
/**
 * App\Models\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @mixin \Eloquent
 * @property int $id_user
 * @property string $username
 * @property string $password
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $remember_token
 * @property int $id_area
 * @property string $social_provider_id
 * @property string $social_provider
 * @property string $url_avatar
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereIdUser($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereFirstname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereLastname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereIdArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereSocialProviderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereSocialProvider($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereUrlAvatar($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @property string $rut
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereRut($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Area
 *
 * @mixin \Eloquent
 * @property int $id_area
 * @property string $short_name
 * @property string $long_name
 * @property string $manager_name
 * @property string $manager_position
 * @property int $id_user
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereIdArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereShortName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereLongName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereManagerName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereManagerPosition($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereIdUser($value)
 */
	class Area extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Menu_option
 *
 * @property int $id_menu
 * @property string $name_option
 * @property int $order_option
 * @property string $option_route
 * @property string $option_icon
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu_option whereIdMenu($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu_option whereNameOption($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu_option whereOrderOption($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu_option whereOptionRoute($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Menu_option whereOptionIcon($value)
 * @mixin \Eloquent
 */
	class Menu_option extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentMethod
 *
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @property int $id_payment_method
 * @property string $name_method
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PaymentMethod whereIdPaymentMethod($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PaymentMethod whereNameMethod($value)
 */
	class PaymentMethod extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MenuOptionsRole
 *
 * @mixin \Eloquent
 * @property int $id_menu
 * @property int $id_role
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOptionsRole whereIdMenu($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOptionsRole whereIdRole($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 */
	class MenuOptionsRole extends \Eloquent {}
}

namespace App\Models{
    /**
     * App\Models\Invoice
     *
     * @property int $id_invoice
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
     * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereIdProvider($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereTotal($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Models\Invoice whereTotalIva($value)
     * @mixin \Eloquent
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Area[] $areas
     */
    class Invoice extends \Eloquent {}
}

namespace App\Models{
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
    class InvoicesOrders extends \Eloquent {}
}

