<?php
require '../vendor/autoload.php';
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Config;
use Zend\Log\Logger;
use Zend\Log\Writer;
use SoapClient;
echo "COMECOU CONSULTAR VOUCHER<br/>";
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}

$url = 'https://wwwp.assistcard.net/ws/services/AssistCardService?wsdl';

$raw = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://services.ws.icard.com">
   <soapenv:Header/>
   <soapenv:Body>
      <ser:consultarVoucher soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
        <pais>540</pais> 
        <codigoVoucher>10338666</codigoVoucher>
        <usuario>WSTEST</usuario> 
        <password>123456</password>
      </ser:consultarVoucher>
   </soapenv:Body>
</soapenv:Envelope>';

$headers = array(
    "Content-type: text/xml;charset=UTF-8",
    "Accept: text/xml",
    "Accept-Encoding: gzip,deflate",
    "SOAPAction: ''",
    "User-Agent: Apache-HttpClient/4.1.1 (java 1.5)",
    "Connection: Keep-Alive",
    "Content-length: " . strlen($raw)
);

// POST https://www.assist-card.net/ws/services/AssistCardService HTTP/1.1
$url = 'https://wwwp.assist-card.net/ws/services/AssistCardService';
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo $response;

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$respuesta = $inputDoc->getElementsByTagName("respuesta");
$informacionEmision = $respuesta->item(0)->getElementsByTagName('informacionEmision');
if ($informacionEmision->length > 0) {
    $idModalidad = $informacionEmision->item(0)->getElementsByTagName('idModalidad');
    if ($idModalidad->length > 0) {
        $idModalidad = $idModalidad->item(0)->nodeValue;
    } else {
        $idModalidad = "";
    }
    $valorTasaUso = $informacionEmision->item(0)->getElementsByTagName('valorTasaUso');
    if ($valorTasaUso->length > 0) {
        $valorTasaUso = $valorTasaUso->item(0)->nodeValue;
    } else {
        $valorTasaUso = "";
    }
    $caserSparame = $informacionEmision->item(0)->getElementsByTagName('caserSparame');
    if ($caserSparame->length > 0) {
        $caserSparame = $caserSparame->item(0)->nodeValue;
    } else {
        $caserSparame = "";
    }
    $agenciaNombreComercial = $informacionEmision->item(0)->getElementsByTagName('agenciaNombreComercial');
    if ($agenciaNombreComercial->length > 0) {
        $agenciaNombreComercial = $agenciaNombreComercial->item(0)->nodeValue;
    } else {
        $agenciaNombreComercial = "";
    }
    $agenciaRazonSocial = $informacionEmision->item(0)->getElementsByTagName('agenciaRazonSocial');
    if ($agenciaRazonSocial->length > 0) {
        $agenciaRazonSocial = $agenciaRazonSocial->item(0)->nodeValue;
    } else {
        $agenciaRazonSocial = "";
    }
    $totalAbonado = $informacionEmision->item(0)->getElementsByTagName('totalAbonado');
    if ($totalAbonado->length > 0) {
        $totalAbonado = $totalAbonado->item(0)->nodeValue;
    } else {
        $totalAbonado = "";
    }
    $prefijoAImprimir = $informacionEmision->item(0)->getElementsByTagName('prefijoAImprimir');
    if ($prefijoAImprimir->length > 0) {
        $prefijoAImprimir = $prefijoAImprimir->item(0)->nodeValue;
    } else {
        $prefijoAImprimir = "";
    }
    $nombreAseguradora = $informacionEmision->item(0)->getElementsByTagName('nombreAseguradora');
    if ($nombreAseguradora->length > 0) {
        $nombreAseguradora = $nombreAseguradora->item(0)->nodeValue;
    } else {
        $nombreAseguradora = "";
    }
    $codigoCompletoVoucher = $informacionEmision->item(0)->getElementsByTagName('codigoCompletoVoucher');
    if ($codigoCompletoVoucher->length > 0) {
        $codigoCompletoVoucher = $codigoCompletoVoucher->item(0)->nodeValue;
    } else {
        $codigoCompletoVoucher = "";
    }
    //
    $producto = $informacionEmision->item(0)->getElementsByTagName('producto');
    if ($producto->length > 0) {
        $pais = $producto->item(0)->getElementsByTagName('pais');
        if ($pais->length > 0) {
            $pais = $pais->item(0)->nodeValue;
        } else {
            $pais = "";
        }
        $codigo = $producto->item(0)->getElementsByTagName('codigo');
        if ($codigo->length > 0) {
            $codigo = $codigo->item(0)->nodeValue;
        } else {
            $codigo = "";
        }
        $rubro = $producto->item(0)->getElementsByTagName('rubro');
        if ($rubro->length > 0) {
            $rubro = $rubro->item(0)->nodeValue;
        } else {
            $rubro = "";
        }
        $nombre = $producto->item(0)->getElementsByTagName('nombre');
        if ($nombre->length > 0) {
            $nombre = $nombre->item(0)->nodeValue;
        } else {
            $nombre = "";
        }
        $leyendaImpresion = $producto->item(0)->getElementsByTagName('leyendaImpresion');
        if ($leyendaImpresion->length > 0) {
            $leyendaImpresion = $leyendaImpresion->item(0)->nodeValue;
        } else {
            $leyendaImpresion = "";
        }
        $subfijoAImprimir = $producto->item(0)->getElementsByTagName('subfijoAImprimir');
        if ($subfijoAImprimir->length > 0) {
            $subfijoAImprimir = $subfijoAImprimir->item(0)->nodeValue;
        } else {
            $subfijoAImprimir = "";
        }
        $debitoAutomatico = $producto->item(0)->getElementsByTagName('debitoAutomatico');
        if ($debitoAutomatico->length > 0) {
            $debitoAutomatico = $debitoAutomatico->item(0)->nodeValue;
        } else {
            $debitoAutomatico = "";
        }
        $roadAssistance = $producto->item(0)->getElementsByTagName('roadAssistance');
        if ($roadAssistance->length > 0) {
            $roadAssistance = $roadAssistance->item(0)->nodeValue;
        } else {
            $roadAssistance = "";
        }
        $medicinaPrepaga = $producto->item(0)->getElementsByTagName('medicinaPrepaga');
        if ($medicinaPrepaga->length > 0) {
            $medicinaPrepaga = $medicinaPrepaga->item(0)->nodeValue;
        } else {
            $medicinaPrepaga = "";
        }
        $grupoFamiliar = $producto->item(0)->getElementsByTagName('grupoFamiliar');
        if ($grupoFamiliar->length > 0) {
            $grupoFamiliar = $grupoFamiliar->item(0)->nodeValue;
        } else {
            $grupoFamiliar = "";
        }
        $corporativo = $producto->item(0)->getElementsByTagName('corporativo');
        if ($corporativo->length > 0) {
            $corporativo = $corporativo->item(0)->nodeValue;
        } else {
            $corporativo = "";
        }
        $activaSeguro = $producto->item(0)->getElementsByTagName('activaSeguro');
        if ($activaSeguro->length > 0) {
            $activaSeguro = $activaSeguro->item(0)->nodeValue;
        } else {
            $activaSeguro = "";
        }
        $monedaImpresion = $producto->item(0)->getElementsByTagName('monedaImpresion');
        if ($monedaImpresion->length > 0) {
            $monedaImpresion = $monedaImpresion->item(0)->nodeValue;
        } else {
            $monedaImpresion = "";
        }
        $monedaFactura = $producto->item(0)->getElementsByTagName('monedaFactura');
        if ($monedaFactura->length > 0) {
            $monedaFactura = $monedaFactura->item(0)->nodeValue;
        } else {
            $monedaFactura = "";
        }
        $darBaja = $producto->item(0)->getElementsByTagName('darBaja');
        if ($darBaja->length > 0) {
            $darBaja = $darBaja->item(0)->nodeValue;
        } else {
            $darBaja = "";
        }
        $categoriaProducto = $producto->item(0)->getElementsByTagName('categoriaProducto');
        if ($categoriaProducto->length > 0) {
            $categoriaProducto = $categoriaProducto->item(0)->nodeValue;
        } else {
            $categoriaProducto = "";
        }
        $tipoProducto = $producto->item(0)->getElementsByTagName('tipoProducto');
        if ($tipoProducto->length > 0) {
            $tipoProducto = $tipoProducto->item(0)->nodeValue;
        } else {
            $tipoProducto = "";
        }
        $familia = $producto->item(0)->getElementsByTagName('familia');
        if ($familia->length > 0) {
            $familia = $familia->item(0)->nodeValue;
        } else {
            $familia = "";
        }
    }
    //
    $poliza = $informacionEmision->item(0)->getElementsByTagName('poliza');
    if ($poliza->length > 0) {
        $premioLiquido = $poliza->item(0)->getElementsByTagName('premioLiquido');
        if ($premioLiquido->length > 0) {
            $premioLiquido = $premioLiquido->item(0)->nodeValue;
        } else {
            $premioLiquido = "";
        }
        $iof = $poliza->item(0)->getElementsByTagName('iof');
        if ($iof->length > 0) {
            $iof = $iof->item(0)->nodeValue;
        } else {
            $iof = "";
        }
        $adicionalFraccionamiento = $poliza->item(0)->getElementsByTagName('adicionalFraccionamiento');
        if ($adicionalFraccionamiento->length > 0) {
            $adicionalFraccionamiento = $adicionalFraccionamiento->item(0)->nodeValue;
        } else {
            $adicionalFraccionamiento = "";
        }
        $costoPoliza = $poliza->item(0)->getElementsByTagName('costoPoliza');
        if ($costoPoliza->length > 0) {
            $costoPoliza = $costoPoliza->item(0)->nodeValue;
        } else {
            $costoPoliza = "";
        }
        $costoAsistencia = $poliza->item(0)->getElementsByTagName('costoAsistencia');
        if ($costoAsistencia->length > 0) {
            $costoAsistencia = $costoAsistencia->item(0)->nodeValue;
        } else {
            $costoAsistencia = "";
        }
        $premioTotal = $poliza->item(0)->getElementsByTagName('premioTotal');
        if ($premioTotal->length > 0) {
            $premioTotal = $premioTotal->item(0)->nodeValue;
        } else {
            $premioTotal = "";
        }
        $linkImpresion = $poliza->item(0)->getElementsByTagName('linkImpresion');
        if ($linkImpresion->length > 0) {
            $linkImpresion = $linkImpresion->item(0)->nodeValue;
        } else {
            $linkImpresion = "";
        }
    }
    //
    $voucher = $informacionEmision->item(0)->getElementsByTagName('voucher');
    if ($voucher->length > 0) {
        $pais = $voucher->item(0)->getElementsByTagName('pais');
        if ($pais->length > 0) {
            $pais = $pais->item(0)->nodeValue;
        } else {
            $pais = "";
        }
        $codigo = $voucher->item(0)->getElementsByTagName('codigo');
        if ($codigo->length > 0) {
            $codigo = $codigo->item(0)->nodeValue;
        } else {
            $codigo = "";
        }
        $tipoPaxVoucher = $voucher->item(0)->getElementsByTagName('tipoPaxVoucher');
        if ($tipoPaxVoucher->length > 0) {
            $tipoPaxVoucher = $tipoPaxVoucher->item(0)->nodeValue;
        } else {
            $tipoPaxVoucher = "";
        }
        $sufijoVoucher = $voucher->item(0)->getElementsByTagName('sufijoVoucher');
        if ($sufijoVoucher->length > 0) {
            $sufijoVoucher = $sufijoVoucher->item(0)->nodeValue;
        } else {
            $sufijoVoucher = "";
        }
        $fechaEmision = $voucher->item(0)->getElementsByTagName('fechaEmision');
        if ($fechaEmision->length > 0) {
            $fechaEmision = $fechaEmision->item(0)->nodeValue;
        } else {
            $fechaEmision = "";
        }
        $cliente = $voucher->item(0)->getElementsByTagName('cliente');
        if ($cliente->length > 0) {
            $cliente = $cliente->item(0)->nodeValue;
        } else {
            $cliente = "";
        }
        $agencia = $voucher->item(0)->getElementsByTagName('agencia');
        if ($agencia->length > 0) {
            $agencia = $agencia->item(0)->nodeValue;
        } else {
            $agencia = "";
        }
        $promotor = $voucher->item(0)->getElementsByTagName('promotor');
        if ($promotor->length > 0) {
            $promotor = $promotor->item(0)->nodeValue;
        } else {
            $promotor = "";
        }
        $producto = $voucher->item(0)->getElementsByTagName('producto');
        if ($producto->length > 0) {
            $producto = $producto->item(0)->nodeValue;
        } else {
            $producto = "";
        }
        $codTarifa = $voucher->item(0)->getElementsByTagName('codTarifa');
        if ($codTarifa->length > 0) {
            $codTarifa = $codTarifa->item(0)->nodeValue;
        } else {
            $codTarifa = "";
        }
        $cantDias = $voucher->item(0)->getElementsByTagName('cantDias');
        if ($cantDias->length > 0) {
            $cantDias = $cantDias->item(0)->nodeValue;
        } else {
            $cantDias = "";
        }
        $tarifaEmitida = $voucher->item(0)->getElementsByTagName('tarifaEmitida');
        if ($tarifaEmitida->length > 0) {
            $tarifaEmitida = $tarifaEmitida->item(0)->nodeValue;
        } else {
            $tarifaEmitida = "";
        }
        $taxEmitida = $voucher->item(0)->getElementsByTagName('taxEmitida');
        if ($taxEmitida->length > 0) {
            $taxEmitida = $taxEmitida->item(0)->nodeValue;
        } else {
            $taxEmitida = "";
        }
        $remesaEmitida = $voucher->item(0)->getElementsByTagName('remesaEmitida');
        if ($remesaEmitida->length > 0) {
            $remesaEmitida = $remesaEmitida->item(0)->nodeValue;
        } else {
            $remesaEmitida = "";
        }
        $tarifaFull = $voucher->item(0)->getElementsByTagName('tarifaFull');
        if ($tarifaFull->length > 0) {
            $tarifaFull = $tarifaFull->item(0)->nodeValue;
        } else {
            $tarifaFull = "";
        }
        $taxFull = $voucher->item(0)->getElementsByTagName('taxFull');
        if ($taxFull->length > 0) {
            $taxFull = $taxFull->item(0)->nodeValue;
        } else {
            $taxFull = "";
        }
        $remesaFull = $voucher->item(0)->getElementsByTagName('remesaFull');
        if ($remesaFull->length > 0) {
            $remesaFull = $remesaFull->item(0)->nodeValue;
        } else {
            $remesaFull = "";
        }
        $tarifaImpresa = $voucher->item(0)->getElementsByTagName('tarifaImpresa');
        if ($tarifaImpresa->length > 0) {
            $tarifaImpresa = $tarifaImpresa->item(0)->nodeValue;
        } else {
            $tarifaImpresa = "";
        }
        $taxImpresa = $voucher->item(0)->getElementsByTagName('taxImpresa');
        if ($taxImpresa->length > 0) {
            $taxImpresa = $taxImpresa->item(0)->nodeValue;
        } else {
            $taxImpresa = "";
        }
        $remesaImpresa = $voucher->item(0)->getElementsByTagName('remesaImpresa');
        if ($remesaImpresa->length > 0) {
            $remesaImpresa = $remesaImpresa->item(0)->nodeValue;
        } else {
            $remesaImpresa = "";
        }
        $tarifaFactura = $voucher->item(0)->getElementsByTagName('tarifaFactura');
        if ($tarifaFactura->length > 0) {
            $tarifaFactura = $tarifaFactura->item(0)->nodeValue;
        } else {
            $tarifaFactura = "";
        }
        $taxFactura = $voucher->item(0)->getElementsByTagName('taxFactura');
        if ($taxFactura->length > 0) {
            $taxFactura = $taxFactura->item(0)->nodeValue;
        } else {
            $taxFactura = "";
        }
        $remesaFactura = $voucher->item(0)->getElementsByTagName('remesaFactura');
        if ($remesaFactura->length > 0) {
            $remesaFactura = $remesaFactura->item(0)->nodeValue;
        } else {
            $remesaFactura = "";
        }
        $cambioDolar = $voucher->item(0)->getElementsByTagName('cambioDolar');
        if ($cambioDolar->length > 0) {
            $cambioDolar = $cambioDolar->item(0)->nodeValue;
        } else {
            $cambioDolar = "";
        }
        $monedaEmision = $voucher->item(0)->getElementsByTagName('monedaEmision');
        if ($monedaEmision->length > 0) {
            $monedaEmision = $monedaEmision->item(0)->nodeValue;
        } else {
            $monedaEmision = "";
        }
        $codBonificacion = $voucher->item(0)->getElementsByTagName('codBonificacion');
        if ($codBonificacion->length > 0) {
            $codBonificacion = $codBonificacion->item(0)->nodeValue;
        } else {
            $codBonificacion = "";
        }
        $porcBonificacion = $voucher->item(0)->getElementsByTagName('porcBonificacion');
        if ($porcBonificacion->length > 0) {
            $porcBonificacion = $porcBonificacion->item(0)->nodeValue;
        } else {
            $porcBonificacion = "";
        }
        $fecVigInic = $voucher->item(0)->getElementsByTagName('fecVigInic');
        if ($fecVigInic->length > 0) {
            $fecVigInic = $fecVigInic->item(0)->nodeValue;
        } else {
            $fecVigInic = "";
        }
        $fecVifFin = $voucher->item(0)->getElementsByTagName('fecVifFin');
        if ($fecVifFin->length > 0) {
            $fecVifFin = $fecVifFin->item(0)->nodeValue;
        } else {
            $fecVifFin = "";
        }
        $area = $voucher->item(0)->getElementsByTagName('area');
        if ($area->length > 0) {
            $area = $area->item(0)->nodeValue;
        } else {
            $area = "";
        }
        $datosAdicionales = $voucher->item(0)->getElementsByTagName('datosAdicionales');
        if ($datosAdicionales->length > 0) {
            $datosAdicionales = $datosAdicionales->item(0)->nodeValue;
        } else {
            $datosAdicionales = "";
        }
        $planFamilia = $voucher->item(0)->getElementsByTagName('planFamilia');
        if ($planFamilia->length > 0) {
            $planFamilia = $planFamilia->item(0)->nodeValue;
        } else {
            $planFamilia = "";
        }
        $tipoUsuario = $voucher->item(0)->getElementsByTagName('tipoUsuario');
        if ($tipoUsuario->length > 0) {
            $tipoUsuario = $tipoUsuario->item(0)->nodeValue;
        } else {
            $tipoUsuario = "";
        }
        $usuario = $voucher->item(0)->getElementsByTagName('usuario');
        if ($usuario->length > 0) {
            $usuario = $usuario->item(0)->nodeValue;
        } else {
            $usuario = "";
        }
        $codVerificador = $voucher->item(0)->getElementsByTagName('codVerificador');
        if ($codVerificador->length > 0) {
            $codVerificador = $codVerificador->item(0)->nodeValue;
        } else {
            $codVerificador = "";
        }
        $grupoVoucher = $voucher->item(0)->getElementsByTagName('grupoVoucher');
        if ($grupoVoucher->length > 0) {
            $grupoVoucher = $grupoVoucher->item(0)->nodeValue;
        } else {
            $grupoVoucher = "";
        }
        $sucAgencia = $voucher->item(0)->getElementsByTagName('sucAgencia');
        if ($sucAgencia->length > 0) {
            $sucAgencia = $sucAgencia->item(0)->nodeValue;
        } else {
            $sucAgencia = "";
        }
        $paisCom = $voucher->item(0)->getElementsByTagName('paisCom');
        if ($paisCom->length > 0) {
            $paisCom = $paisCom->item(0)->nodeValue;
        } else {
            $paisCom = "";
        }
        $fee = $voucher->item(0)->getElementsByTagName('fee');
        if ($fee->length > 0) {
            $fee = $fee->item(0)->nodeValue;
        } else {
            $fee = "";
        }
        $free = $voucher->item(0)->getElementsByTagName('free');
        if ($free->length > 0) {
            $free = $free->item(0)->nodeValue;
        } else {
            $free = "";
        }
        $donacionPax = $voucher->item(0)->getElementsByTagName('donacionPax');
        if ($donacionPax->length > 0) {
            $donacionPax = $donacionPax->item(0)->nodeValue;
        } else {
            $donacionPax = "";
        }
        $donacionAgencia = $voucher->item(0)->getElementsByTagName('donacionAgencia');
        if ($donacionAgencia->length > 0) {
            $donacionAgencia = $donacionAgencia->item(0)->nodeValue;
        } else {
            $donacionAgencia = "";
        }
        $precompra = $voucher->item(0)->getElementsByTagName('precompra');
        if ($precompra->length > 0) {
            $precompra = $precompra->item(0)->nodeValue;
        } else {
            $precompra = "";
        }
        $datoAdic1 = $voucher->item(0)->getElementsByTagName('datoAdic1');
        if ($datoAdic1->length > 0) {
            $datoAdic1 = $datoAdic1->item(0)->nodeValue;
        } else {
            $datoAdic1 = "";
        }
        $fecAlta = $voucher->item(0)->getElementsByTagName('fecAlta');
        if ($fecAlta->length > 0) {
            $fecAlta = $fecAlta->item(0)->nodeValue;
        } else {
            $fecAlta = "";
        }
        $tipoCarga = $voucher->item(0)->getElementsByTagName('tipoCarga');
        if ($tipoCarga->length > 0) {
            $tipoCarga = $tipoCarga->item(0)->nodeValue;
        } else {
            $tipoCarga = "";
        }
        $codigoCounter = $voucher->item(0)->getElementsByTagName('codigoCounter');
        if ($codigoCounter->length > 0) {
            $codigoCounter = $codigoCounter->item(0)->nodeValue;
        } else {
            $codigoCounter = "";
        }
        $idPromocion = $voucher->item(0)->getElementsByTagName('idPromocion');
        if ($idPromocion->length > 0) {
            $idPromocion = $idPromocion->item(0)->nodeValue;
        } else {
            $idPromocion = "";
        }
        $primeroDeGrupo = $voucher->item(0)->getElementsByTagName('primeroDeGrupo');
        if ($primeroDeGrupo->length > 0) {
            $primeroDeGrupo = $primeroDeGrupo->item(0)->nodeValue;
        } else {
            $primeroDeGrupo = "";
        }
        $monedaImpresion = $voucher->item(0)->getElementsByTagName('monedaImpresion');
        if ($monedaImpresion->length > 0) {
            $monedaImpresion = $monedaImpresion->item(0)->nodeValue;
        } else {
            $monedaImpresion = "";
        }
        $monedaFactura = $voucher->item(0)->getElementsByTagName('monedaFactura');
        if ($monedaFactura->length > 0) {
            $monedaFactura = $monedaFactura->item(0)->nodeValue;
        } else {
            $monedaFactura = "";
        }
        $cambioImpresion = $voucher->item(0)->getElementsByTagName('cambioImpresion');
        if ($cambioImpresion->length > 0) {
            $cambioImpresion = $cambioImpresion->item(0)->nodeValue;
        } else {
            $cambioImpresion = "";
        }
        $cambioFactura = $voucher->item(0)->getElementsByTagName('cambioFactura');
        if ($cambioFactura->length > 0) {
            $cambioFactura = $cambioFactura->item(0)->nodeValue;
        } else {
            $cambioFactura = "";
        }
        $canalEmisor = $voucher->item(0)->getElementsByTagName('canalEmisor');
        if ($canalEmisor->length > 0) {
            $canalEmisor = $canalEmisor->item(0)->nodeValue;
        } else {
            $canalEmisor = "";
        }
        $paisTriangulado = $voucher->item(0)->getElementsByTagName('paisTriangulado');
        if ($paisTriangulado->length > 0) {
            $paisTriangulado = $paisTriangulado->item(0)->nodeValue;
        } else {
            $paisTriangulado = "";
        }
        $sucursalTriangulada = $voucher->item(0)->getElementsByTagName('sucursalTriangulada');
        if ($sucursalTriangulada->length > 0) {
            $sucursalTriangulada = $sucursalTriangulada->item(0)->nodeValue;
        } else {
            $sucursalTriangulada = "";
        }
        $dniCounter = $voucher->item(0)->getElementsByTagName('dniCounter');
        if ($dniCounter->length > 0) {
            $dniCounter = $dniCounter->item(0)->nodeValue;
        } else {
            $dniCounter = "";
        }
        $provider = $voucher->item(0)->getElementsByTagName('provider');
        if ($provider->length > 0) {
            $provider = $provider->item(0)->nodeValue;
        } else {
            $provider = "";
        }
        $effectiveDays = $voucher->item(0)->getElementsByTagName('effectiveDays');
        if ($effectiveDays->length > 0) {
            $effectiveDays = $effectiveDays->item(0)->nodeValue;
        } else {
            $effectiveDays = "";
        }
        $amadeusUniqueId = $voucher->item(0)->getElementsByTagName('amadeusUniqueId');
        if ($amadeusUniqueId->length > 0) {
            $amadeusUniqueId = $amadeusUniqueId->item(0)->nodeValue;
        } else {
            $amadeusUniqueId = "";
        }
        $costoViaje = $voucher->item(0)->getElementsByTagName('costoViaje');
        if ($costoViaje->length > 0) {
            $costoViaje = $costoViaje->item(0)->nodeValue;
        } else {
            $costoViaje = "";
        }
        $icardNro = $voucher->item(0)->getElementsByTagName('icardNro');
        if ($icardNro->length > 0) {
            $icardNro = $icardNro->item(0)->nodeValue;
        } else {
            $icardNro = "";
        }
        $cargoEnvio = $voucher->item(0)->getElementsByTagName('cargoEnvio');
        if ($cargoEnvio->length > 0) {
            $cargoEnvio = $cargoEnvio->item(0)->nodeValue;
        } else {
            $cargoEnvio = "";
        }
        $incrementoInteresFinanciero = $voucher->item(0)->getElementsByTagName('incrementoInteresFinanciero');
        if ($incrementoInteresFinanciero->length > 0) {
            $incrementoInteresFinanciero = $incrementoInteresFinanciero->item(0)->nodeValue;
        } else {
            $incrementoInteresFinanciero = "";
        }
        $idTarjetaCredito = $voucher->item(0)->getElementsByTagName('idTarjetaCredito');
        if ($idTarjetaCredito->length > 0) {
            $idTarjetaCredito = $idTarjetaCredito->item(0)->nodeValue;
        } else {
            $idTarjetaCredito = "";
        }
        $cuotasPagoTarjeta = $voucher->item(0)->getElementsByTagName('cuotasPagoTarjeta');
        if ($cuotasPagoTarjeta->length > 0) {
            $cuotasPagoTarjeta = $cuotasPagoTarjeta->item(0)->nodeValue;
        } else {
            $cuotasPagoTarjeta = "";
        }
        $idClienteUnico = $voucher->item(0)->getElementsByTagName('idClienteUnico');
        if ($idClienteUnico->length > 0) {
            $idClienteUnico = $idClienteUnico->item(0)->nodeValue;
        } else {
            $idClienteUnico = "";
        }
        $cantDiasTravel = $voucher->item(0)->getElementsByTagName('cantDiasTravel');
        if ($cantDiasTravel->length > 0) {
            $cantDiasTravel = $cantDiasTravel->item(0)->nodeValue;
        } else {
            $cantDiasTravel = "";
        }
        $codigoPpalGrupoFamiliar = $voucher->item(0)->getElementsByTagName('codigoPpalGrupoFamiliar');
        if ($codigoPpalGrupoFamiliar->length > 0) {
            $codigoPpalGrupoFamiliar = $codigoPpalGrupoFamiliar->item(0)->nodeValue;
        } else {
            $codigoPpalGrupoFamiliar = "";
        }
        $origenEmision = $voucher->item(0)->getElementsByTagName('origenEmision');
        if ($origenEmision->length > 0) {
            $origenEmision = $origenEmision->item(0)->nodeValue;
        } else {
            $origenEmision = "";
        }
    }
    //
    $cliente = $informacionEmision->item(0)->getElementsByTagName('cliente');
    if ($cliente->length > 0) {
        $pais = $cliente->item(0)->getElementsByTagName('pais');
        if ($pais->length > 0) {
            $pais = $pais->item(0)->nodeValue;
        } else {
            $pais = "";
        }
        $codigo = $cliente->item(0)->getElementsByTagName('codigo');
        if ($codigo->length > 0) {
            $codigo = $codigo->item(0)->nodeValue;
        } else {
            $codigo = "";
        }
        $apellido = $cliente->item(0)->getElementsByTagName('apellido');
        if ($apellido->length > 0) {
            $apellido = $apellido->item(0)->nodeValue;
        } else {
            $apellido = "";
        }
        $nombre = $cliente->item(0)->getElementsByTagName('nombre');
        if ($nombre->length > 0) {
            $nombre = $nombre->item(0)->nodeValue;
        } else {
            $nombre = "";
        }
        $fecNacimiento = $cliente->item(0)->getElementsByTagName('fecNacimiento');
        if ($fecNacimiento->length > 0) {
            $fecNacimiento = $fecNacimiento->item(0)->nodeValue;
        } else {
            $fecNacimiento = "";
        }
        $estadoCivil = $cliente->item(0)->getElementsByTagName('estadoCivil');
        if ($estadoCivil->length > 0) {
            $estadoCivil = $estadoCivil->item(0)->nodeValue;
        } else {
            $estadoCivil = "";
        }
        $pasaporte = $cliente->item(0)->getElementsByTagName('pasaporte');
        if ($pasaporte->length > 0) {
            $pasaporte = $pasaporte->item(0)->nodeValue;
        } else {
            $pasaporte = "";
        }
        $tipoDocumento = $cliente->item(0)->getElementsByTagName('tipoDocumento');
        if ($tipoDocumento->length > 0) {
            $tipoDocumento = $tipoDocumento->item(0)->nodeValue;
        } else {
            $tipoDocumento = "";
        }
        $nroDocumento = $cliente->item(0)->getElementsByTagName('nroDocumento');
        if ($nroDocumento->length > 0) {
            $nroDocumento = $nroDocumento->item(0)->nodeValue;
        } else {
            $nroDocumento = "";
        }
        $domCalle = $cliente->item(0)->getElementsByTagName('domCalle');
        if ($domCalle->length > 0) {
            $domCalle = $domCalle->item(0)->nodeValue;
        } else {
            $domCalle = "";
        }
        $domCp = $cliente->item(0)->getElementsByTagName('domCp');
        if ($domCp->length > 0) {
            $domCp = $domCp->item(0)->nodeValue;
        } else {
            $domCp = "";
        }
        $domCiudad = $cliente->item(0)->getElementsByTagName('domCiudad');
        if ($domCiudad->length > 0) {
            $domCiudad = $domCiudad->item(0)->nodeValue;
        } else {
            $domCiudad = "";
        }
        $domProvincia = $cliente->item(0)->getElementsByTagName('domProvincia');
        if ($domProvincia->length > 0) {
            $domProvincia = $domProvincia->item(0)->nodeValue;
        } else {
            $domProvincia = "";
        }
        $domPais = $cliente->item(0)->getElementsByTagName('domPais');
        if ($domPais->length > 0) {
            $domPais = $domPais->item(0)->nodeValue;
        } else {
            $domPais = "";
        }
        $nacionalidad = $cliente->item(0)->getElementsByTagName('nacionalidad');
        if ($nacionalidad->length > 0) {
            $nacionalidad = $nacionalidad->item(0)->nodeValue;
        } else {
            $nacionalidad = "";
        }
        $telParticular = $cliente->item(0)->getElementsByTagName('telParticular');
        if ($telParticular->length > 0) {
            $telParticular = $telParticular->item(0)->nodeValue;
        } else {
            $telParticular = "";
        }
        $email = $cliente->item(0)->getElementsByTagName('email');
        if ($email->length > 0) {
            $email = $email->item(0)->nodeValue;
        } else {
            $email = "";
        }
        $emergContacto = $cliente->item(0)->getElementsByTagName('emergContacto');
        if ($emergContacto->length > 0) {
            $emergContacto = $emergContacto->item(0)->nodeValue;
        } else {
            $emergContacto = "";
        }
        $emergNro = $cliente->item(0)->getElementsByTagName('emergNro');
        if ($emergNro->length > 0) {
            $emergNro = $emergNro->item(0)->nodeValue;
        } else {
            $emergNro = "";
        }
        $emergPais = $cliente->item(0)->getElementsByTagName('emergPais');
        if ($emergPais->length > 0) {
            $emergPais = $emergPais->item(0)->nodeValue;
        } else {
            $emergPais = "";
        }
        $emergTel1 = $cliente->item(0)->getElementsByTagName('emergTel1');
        if ($emergTel1->length > 0) {
            $emergTel1 = $emergTel1->item(0)->nodeValue;
        } else {
            $emergTel1 = "";
        }
        $emergTel2 = $cliente->item(0)->getElementsByTagName('emergTel2');
        if ($emergTel2->length > 0) {
            $emergTel2 = $emergTel2->item(0)->nodeValue;
        } else {
            $emergTel2 = "";
        }
        $datosAdicionales = $cliente->item(0)->getElementsByTagName('datosAdicionales');
        if ($datosAdicionales->length > 0) {
            $datosAdicionales = $datosAdicionales->item(0)->nodeValue;
        } else {
            $datosAdicionales = "";
        }
        $datosAdicionales2 = $cliente->item(0)->getElementsByTagName('datosAdicionales2');
        if ($datosAdicionales2->length > 0) {
            $datosAdicionales2 = $datosAdicionales2->item(0)->nodeValue;
        } else {
            $datosAdicionales2 = "";
        }
        $nroSocioMillage = $cliente->item(0)->getElementsByTagName('nroSocioMillage');
        if ($nroSocioMillage->length > 0) {
            $nroSocioMillage = $nroSocioMillage->item(0)->nodeValue;
        } else {
            $nroSocioMillage = "";
        }
        $automovilMarca = $cliente->item(0)->getElementsByTagName('automovilMarca');
        if ($automovilMarca->length > 0) {
            $automovilMarca = $automovilMarca->item(0)->nodeValue;
        } else {
            $automovilMarca = "";
        }
        $automovilModelo = $cliente->item(0)->getElementsByTagName('automovilModelo');
        if ($automovilModelo->length > 0) {
            $automovilModelo = $automovilModelo->item(0)->nodeValue;
        } else {
            $automovilModelo = "";
        }
        $automovilPatente = $cliente->item(0)->getElementsByTagName('automovilPatente');
        if ($automovilPatente->length > 0) {
            $automovilPatente = $automovilPatente->item(0)->nodeValue;
        } else {
            $automovilPatente = "";
        }
        $medPrepagaNroRecibo = $cliente->item(0)->getElementsByTagName('medPrepagaNroRecibo');
        if ($medPrepagaNroRecibo->length > 0) {
            $medPrepagaNroRecibo = $medPrepagaNroRecibo->item(0)->nodeValue;
        } else {
            $medPrepagaNroRecibo = "";
        }
        $nroCertificado = $cliente->item(0)->getElementsByTagName('nroCertificado');
        if ($nroCertificado->length > 0) {
            $nroCertificado = $nroCertificado->item(0)->nodeValue;
        } else {
            $nroCertificado = "";
        }
        $edad = $cliente->item(0)->getElementsByTagName('edad');
        if ($edad->length > 0) {
            $edad = $edad->item(0)->nodeValue;
        } else {
            $edad = "";
        }
        $exist = $cliente->item(0)->getElementsByTagName('exist');
        if ($exist->length > 0) {
            $exist = $exist->item(0)->nodeValue;
        } else {
            $exist = "";
        }
        $idCuenta = $cliente->item(0)->getElementsByTagName('idCuenta');
        if ($idCuenta->length > 0) {
            $idCuenta = $idCuenta->item(0)->nodeValue;
        } else {
            $idCuenta = "";
        }
        $cantDias = $cliente->item(0)->getElementsByTagName('cantDias');
        if ($cantDias->length > 0) {
            $cantDias = $cantDias->item(0)->nodeValue;
        } else {
            $cantDias = "";
        }
    }
}


echo '<br/>Done';
?>