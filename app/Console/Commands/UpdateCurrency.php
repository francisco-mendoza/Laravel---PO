<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Models\CurrencyRate;
use App\Models\Currency;
use Mockery\Exception;
use Validator;



class UpdateCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los valores de monedas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("Se estan actualizando las tazas de cambio");

        $cantidad = 1;
        $dateNow = date('Y-m-d');
        $currencies = Currency::all();
        $currencyTarget = 2;

        foreach($currencies as $currency) {

            if($currency->code != 'CLP') {
                $validate = CurrencyRate::validateCurrencyRate([
                    'date'               => $dateNow,
                    'id_currency'        => $currency->id_currency,
                    'id_currency_target' => $currencyTarget
                ]);

                if($validate) {
                    $this->warn($currency->name_currency.' -> Ya actualizada el dia de hoy '.$dateNow);
                } else {
                    $nuevo_valor = Currency::apiConversorMoneda($currency->code,'CLP' , $cantidad);
                    if(!$nuevo_valor){
                        throw new Exception('Ha ocurrido un error con la conversion ');
                    }
                    try {
                        $currencyRate = new CurrencyRate();
                        $currencyRate->date = $dateNow;
                        $currencyRate->id_currency = $currency->id_currency;
                        $currencyRate->id_currency_target = 2;
                        $currencyRate->exchange_rate = $nuevo_valor;
                        $currencyRate->save();
                        $this->info('Nuevo Valor de '. $currency->name_currency. ' es: '.$nuevo_valor);
                    } catch (Exception $e) {
                        $this->error('Error: '.$e->getMessage());
                    }

                }

            }

        }

    }
}
