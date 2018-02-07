<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CurrencyRate extends RossModel
{
    protected $table = 'currency_rates';
    protected $fillable = [
        'date',
        'id_currency',
        'id_currency_target',
        'exchange_rate'
    ];
    public $incrementing = false;
    public $timestamps = false;

    public static function validateCurrencyRate($whereData) {
        return CurrencyRate::where($whereData)->exists();
    }

    public static function getLastCurrencyRate($id_currency) {
        return CurrencyRate::where('id_currency', '=', $id_currency)
            ->orderBy('date', 'desc')
            ->select('currency_rates.exchange_rate')
            ->first();
    }

    public static function getCurrencyRateByDate($id_currency, $date) {
        return CurrencyRate::where('id_currency', '=', $id_currency)
            ->where('date', '=', $date)
            ->first();
    }
}
