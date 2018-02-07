<?php

namespace App\Http\ViewMails;

use App\Models\Provider;
use App\Models\PurchaseOrder;
use App\Models\Contract;
use App\Models\PurchaseOrderDetail;

class ViewMail{

    public static function OcCreada($folio_orden,$server_name){

        $ordenes_compra = "";
        $titulo = "Nueva Orden de Compra";
        $mensaje = "Se ha creado una nueva Orden de Compra ";

        $folio_number = "";

        foreach ($folio_orden as $folio){
            $ordenes_compra .='<li> <a href="http://'.$server_name.'/detailPurchaseOrder/'.$folio.'/validate">'.$folio.'</a>';
            /** @var PurchaseOrderDetail $details_order */
            $details_order = PurchaseOrderDetail::getOrderDetails($folio);

            $ordenes_compra .='<ul>';
            foreach ($details_order as $detail){
                $ordenes_compra .= '<li>'.$detail->description.'</li>';
            }
            $ordenes_compra .= '</ul></li>';

            $folio_number = $folio;
        }
        $cantidad_oc = count($folio_orden);

        $orden = PurchaseOrder::find($folio_number);
        /** @var Contract $contract */
        $contract = Contract::findBy('id_contract',$orden->id_contract);
        $provider = Provider::find($contract->id_provider);

        $proveedor_mensaje = "para el proveedor ".$provider->name_provider." con número de contrato ".$contract->contract_number." (".$contract->description.")";

        if ($cantidad_oc >1){
            $titulo = "Nuevas Ordenes de Compra";
            $mensaje = "Se han creado nuevas Ordenes de Compra ";
        }
        $mensaje .= $proveedor_mensaje;

        return  '
        <html>
        <head>
            <title>Nueva OC</title>
        </head>
        
        <body link="#00a5b5" vlink="#00a5b5" alink="#00a5b5">
        
        <table class=" main contenttable" align="center" style="font-weight: normal;border-collapse: collapse;border: 0;margin-left: auto;margin-right: auto;padding: 0;font-family: Arial, sans-serif;color: #555559;background-color: white;font-size: 16px;line-height: 26px;width: 600px;">
            <tr>
                <td class="border" style="border-collapse: collapse;border: 1px solid #d4d5d6;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;">
                    <table style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;">
                        <tr>
                            <td colspan="4" valign="top" class="image-section" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background-color: #fff;border-bottom: 4px solid #00a5b5">
                                <p style="float: left;padding-left: 20px;padding-top: 4px;"><img style="width: 29px" src="https://drive.google.com/uc?export=download&id=0ByzroDp6iLIjQ3NfcExvS3hUYWs"></p>
                                <p style="float: left;padding-left: 10px;font-size: x-large;"><b>Sistema Ordenes Compra Yapo.cl</b></p>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" class="side title" style="border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;vertical-align: top;background-color: white;border-top: none;">
                                <table style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;">
                                    <tr>
                                        <td class="head-title" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 28px;line-height: 34px;font-weight: bold; text-align: center;">
                                            <div class="mktEditable" id="main_title">
                                                <div style="">
                                                    <div><img style="padding-top: 7px;padding-right: 11px;" width="65px" src="https://drive.google.com/uc?export=download&id=0ByzroDp6iLIjS2NnZnYxazZWekU"></div>
                                                    <div style="color: green">'.$titulo.'</div>
                                                </div>
        
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="sub-title" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;padding-top:5px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 18px;line-height: 29px;font-weight: bold;text-align: center;">
                                            <div class="mktEditable"  id="intro_title">
                                                '.$mensaje.'
                                            </div></td>
                                    </tr>
                                    <tr>
                                        <td class="top-padding" style="border-collapse: collapse;border: 0;margin: 0;padding: 5px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;"></td>
                                    </tr>
                                    <tr>
                                        <td class="grey-block" style="border-collapse: collapse;border: 0;margin: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background-color: #fff; text-align:center;">
                                            <div class="mktEditable" style="text-align:left" id="cta">
                                                <ul>'.$ordenes_compra.'</ul>                                               
                                            </div>
                                        </td>
                                    </tr>
        
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor="#fff" style="border-top: 4px solid #00a5b5;">
                            <td valign="top" class="footer" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background: #fff;text-align: center;">
                                <table style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;">
                                    <tr>
                                        <td class="inside-footer" align="center" valign="middle" style="border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 12px;line-height: 16px;vertical-align: middle;text-align: center;width: 580px;">
                                            <div id="address" class="mktEditable">
                                                <b>Yapo.cl</b><br>
                                                © 2017 <br>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </body>
        
        </html>
        ';
    }

    public static function OcAprobada($oc,$server_name) {
        return  '
        <html>
        <head>
            <title>OC Aprobada</title>
        </head>
        
        <body link="#00a5b5" vlink="#00a5b5" alink="#00a5b5">
        
        <table class=" main contenttable" align="center" style="font-weight: normal;border-collapse: collapse;border: 0;margin-left: auto;margin-right: auto;padding: 0;font-family: Arial, sans-serif;color: #555559;background-color: white;font-size: 16px;line-height: 26px;width: 600px;">
            <tr>
                <td class="border" style="border-collapse: collapse;border: 1px solid #d4d5d6;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;">
                    <table style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;">
                        <tr>
                            <td colspan="4" valign="top" class="image-section" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background-color: #fff;border-bottom: 4px solid #00a5b5">
                                <p style="float: left;padding-left: 20px;padding-top: 4px;"><img style="width: 29px" src="https://drive.google.com/uc?export=download&id=0ByzroDp6iLIjQ3NfcExvS3hUYWs"></p>
                                <p style="float: left;padding-left: 10px;font-size: x-large;"><b>Sistema Ordenes Compra Yapo.cl</b></p>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" class="side title" style="border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;vertical-align: top;background-color: white;border-top: none;">
                                <table style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;">
                                    <tr>
                                        <td class="head-title" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 28px;line-height: 34px;font-weight: bold; text-align: center;">
                                            <div class="mktEditable" id="main_title">
                                                <div style="">
                                                    <div><img style="padding-top: 7px;padding-right: 11px;" width="65px" src="https://drive.google.com/uc?export=download&id=0ByzroDp6iLIjdUhCSmNZTTU2VVU"></div>
                                                    <div style="color: green">OC Aprobada</div>
                                                </div>
        
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="sub-title" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;padding-top:5px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 18px;line-height: 29px;font-weight: bold;text-align: center;">
                                            <div class="mktEditable" id="intro_title">
                                                Se le informa que mediante el Sistema de Ordenes de Compra de Yapo.cl la siguiente orden ha sido aprobada:
                                            </div></td>
                                    </tr>
                                    <tr>
                                        <td class="top-padding" style="border-collapse: collapse;border: 0;margin: 0;padding: 5px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;"></td>
                                    </tr>
                                    <tr>
                                        <td class="grey-block" style="border-collapse: collapse;border: 0;margin: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background-color: #fff; text-align:center;">
                                            <div class="mktEditable" id="cta" style="text-align:left">
                                                <ul>
                                                    <li><a href="http://'.$server_name.'/detailPurchaseOrder/'.$oc.'">'.$oc.'</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
        
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor="#fff" style="border-top: 4px solid #00a5b5;">
                            <td valign="top" class="footer" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background: #fff;text-align: center;">
                                <table style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;">
                                    <tr>
                                        <td class="inside-footer" align="center" valign="middle" style="border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 12px;line-height: 16px;vertical-align: middle;text-align: center;width: 580px;">
                                            <div id="address" class="mktEditable">
                                                <b>Yapo.cl</b><br>
                                                © 2017 <br>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </body>
        
        </html>
        ';
    }

    public static function OcRechazada($oc,$server_name, $motivo){
        return  '
        <html>
        <head>
            <title>OC Rechazada</title>
        </head>
        
        <body link="#00a5b5" vlink="#00a5b5" alink="#00a5b5">
        
        <table class=" main contenttable" align="center" style="font-weight: normal;border-collapse: collapse;border: 0;margin-left: auto;margin-right: auto;padding: 0;font-family: Arial, sans-serif;color: #555559;background-color: white;font-size: 16px;line-height: 26px;width: 600px;">
            <tr>
                <td class="border" style="border-collapse: collapse;border: 1px solid #d4d5d6;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;">
                    <table style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;">
                        <tr>
                            <td colspan="4" valign="top" class="image-section" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background-color: #fff;border-bottom: 4px solid #00a5b5">
                                <p style="float: left;padding-left: 20px;padding-top: 4px;"><img style="width: 29px" src="https://drive.google.com/uc?export=download&id=0ByzroDp6iLIjQ3NfcExvS3hUYWs"></p>
                                <p style="float: left;padding-left: 10px;font-size: x-large;"><b>Sistema Ordenes Compra Yapo.cl</b></p>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" class="side title" style="border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;vertical-align: top;background-color: white;border-top: none;">
                                <table style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;">
                                    <tr>
                                        <td class="head-title" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 28px;line-height: 34px;font-weight: bold; text-align: center;">
                                            <div class="mktEditable" id="main_title">
                                                <div style="">
                                                    <div><img style="padding-top: 7px;padding-right: 11px;" width="65px" src="https://drive.google.com/uc?export=download&id=0ByzroDp6iLIjS1V1cXpKQnAtb28"></div>
                                                    <div style="color: red">OC Rechazada</div>
                                                </div>
        
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="sub-title" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;padding-top:5px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 18px;line-height: 29px;font-weight: bold;text-align: center;">
                                            <div class="mktEditable" id="intro_title">
                                                Se le informa que mediante el Sistema de Ordenes de Compra de Yapo.cl la siguiente orden ha sido rechazada:
                                            </div></td>
                                    </tr>
                                    <tr>
                                        <td class="top-padding" style="border-collapse: collapse;border: 0;margin: 0;padding: 5px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;"></td>
                                    </tr>
                                    <tr>
                                        <td class="grey-block" style="border-collapse: collapse;border: 0;margin: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background-color: #fff; text-align:center;">
                                            <div class="mktEditable" id="cta" style="text-align:left">
                                                <ul>
                                                    <li><a href="http://'.$server_name.'/detailPurchaseOrder/'.$oc.'">'.$oc.'</a></li>
                                                </ul>
                                                <p><b>Motivo de rechazo:</b></p>
                                                <p>'.$motivo.'</p>
                                            </div>
                                        </td>
                                    </tr>
        
                                </table>
                            </td>
                        </tr>
                        <tr bgcolor="#fff" style="border-top: 4px solid #00a5b5;">
                            <td valign="top" class="footer" style="border-collapse: collapse;border: 0;margin: 0;padding: 0;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;background: #fff;text-align: center;">
                                <table style="font-weight: normal;border-collapse: collapse;border: 0;margin: 0;padding: 0;font-family: Arial, sans-serif;">
                                    <tr>
                                        <td class="inside-footer" align="center" valign="middle" style="border-collapse: collapse;border: 0;margin: 0;padding: 20px;-webkit-text-size-adjust: none;color: #555559;font-family: Arial, sans-serif;font-size: 12px;line-height: 16px;vertical-align: middle;text-align: center;width: 580px;">
                                            <div id="address" class="mktEditable">
                                                <b>Yapo.cl</b><br>
                                                © 2017 <br>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </body>
        
        </html>
        ';
    }

}
