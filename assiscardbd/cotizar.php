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
echo "COMECOU COTIZAR<br/>";
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
      <ser:cotizar soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
         <xml>
            <![CDATA[
<cotizacion>
<pais>510</pais>
<codigoAgencia>87819</codigoAgencia>
<numeroSucursal>0</numeroSucursal>
<cantidadDias>10</cantidadDias>
<fechaInicio>10/12/2021</fechaInicio>
<fechaFin>19/12/2021</fechaFin>
<planFamiliar>false</planFamiliar>
<destino>02</destino>
<clientes>
<clienteCotizacion>
<nombre>pablo</nombre>
<apellido>test</apellido>
<edad>30</edad>
<fechaNacimiento>01/01/1990</fechaNacimiento>
</clienteCotizacion>
</clientes>
</cotizacion>]]>
         </xml>
         <usuario xsi:type="xsd:string">WSTEST</usuario>
         <password xsi:type="xsd:string">123456</password>
      </ser:cotizar>
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
$cotizacionDolar = $respuesta->item(0)->getElementsByTagName('cotizacionDolar');
if ($cotizacionDolar->length > 0) {
    $cotizacionDolar = $cotizacionDolar->item(0)->nodeValue;
} else {
    $cotizacionDolar = "";
}
$cotizaciones = $respuesta->item(0)->getElementsByTagName('cotizaciones');
if ($cotizaciones->length > 0) {
    $cotizacion = $cotizaciones->item(0)->getElementsByTagName('cotizacion');
    if ($cotizacion->length > 0) {
        for ($i=0; $i < $cotizacion->length; $i++) { 
            $pais = $cotizacion->item($i)->getElementsByTagName('pais');
            if ($pais->length > 0) {
                $pais = $pais->item(0)->nodeValue;
            } else {
                $pais = "";
            }
            $codigo = $cotizacion->item($i)->getElementsByTagName('codigo');
            if ($codigo->length > 0) {
                $codigo = $codigo->item(0)->nodeValue;
            } else {
                $codigo = "";
            }
            $codigoTarifa = $cotizacion->item($i)->getElementsByTagName('codigoTarifa');
            if ($codigoTarifa->length > 0) {
                $codigoTarifa = $codigoTarifa->item(0)->nodeValue;
            } else {
                $codigoTarifa = "";
            }
            $nombreTarifa = $cotizacion->item($i)->getElementsByTagName('nombreTarifa');
            if ($nombreTarifa->length > 0) {
                $nombreTarifa = $nombreTarifa->item(0)->nodeValue;
            } else {
                $nombreTarifa = "";
            }
            $categoriaTarifa = $cotizacion->item($i)->getElementsByTagName('categoriaTarifa');
            if ($categoriaTarifa->length > 0) {
                $categoriaTarifa = $categoriaTarifa->item(0)->nodeValue;
            } else {
                $categoriaTarifa = "";
            }
            $cantidadDias = $cotizacion->item($i)->getElementsByTagName('cantidadDias');
            if ($cantidadDias->length > 0) {
                $cantidadDias = $cantidadDias->item(0)->nodeValue;
            } else {
                $cantidadDias = "";
            }
            $nombreUpgrade = $cotizacion->item($i)->getElementsByTagName('nombreUpgrade');
            if ($nombreUpgrade->length > 0) {
                $nombreUpgrade = $nombreUpgrade->item(0)->nodeValue;
            } else {
                $nombreUpgrade = "";
            }
            $nombreTarifaUpgrade = $cotizacion->item($i)->getElementsByTagName('nombreTarifaUpgrade');
            if ($nombreTarifaUpgrade->length > 0) {
                $nombreTarifaUpgrade = $nombreTarifaUpgrade->item(0)->nodeValue;
            } else {
                $nombreTarifaUpgrade = "";
            }
            $esUpgrade = $cotizacion->item($i)->getElementsByTagName('esUpgrade');
            if ($esUpgrade->length > 0) {
                $esUpgrade = $esUpgrade->item(0)->nodeValue;
            } else {
                $esUpgrade = "";
            }
            $moneda = $cotizacion->item($i)->getElementsByTagName('moneda');
            if ($moneda->length > 0) {
                $moneda = $moneda->item(0)->nodeValue;
            } else {
                $moneda = "";
            }
            $esPlanFamiliar = $cotizacion->item($i)->getElementsByTagName('esPlanFamiliar');
            if ($esPlanFamiliar->length > 0) {
                $esPlanFamiliar = $esPlanFamiliar->item(0)->nodeValue;
            } else {
                $esPlanFamiliar = "";
            }
            $esModalidadTTR = $cotizacion->item($i)->getElementsByTagName('esModalidadTTR');
            if ($esModalidadTTR->length > 0) {
                $esModalidadTTR = $esModalidadTTR->item(0)->nodeValue;
            } else {
                $esModalidadTTR = "";
            }
            $modalidad = $cotizacion->item($i)->getElementsByTagName('modalidad');
            if ($modalidad->length > 0) {
                $modalidad = $modalidad->item(0)->nodeValue;
            } else {
                $modalidad = "";
            }
            $tarifaBruta = $cotizacion->item($i)->getElementsByTagName('tarifaBruta');
            if ($tarifaBruta->length > 0) {
                $tarifaBruta = $tarifaBruta->item(0)->nodeValue;
            } else {
                $tarifaBruta = "";
            }
            $tarifaPrecompra = $cotizacion->item($i)->getElementsByTagName('tarifaPrecompra');
            if ($tarifaPrecompra->length > 0) {
                $tarifaPrecompra = $tarifaPrecompra->item(0)->nodeValue;
            } else {
                $tarifaPrecompra = "";
            }
            $nombreProducto = $cotizacion->item($i)->getElementsByTagName('nombreProducto');
            if ($nombreProducto->length > 0) {
                $nombreProducto = $nombreProducto->item(0)->nodeValue;
            } else {
                $nombreProducto = "";
            }
            $tarifaLeyenda = $cotizacion->item($i)->getElementsByTagName('tarifaLeyenda');
            if ($tarifaLeyenda->length > 0) {
                $tarifaLeyenda = $tarifaLeyenda->item(0)->nodeValue;
            } else {
                $tarifaLeyenda = "";
            }
            $clientesCotizados = $cotizacion->item($i)->getElementsByTagName('clientesCotizados');
            if ($clientesCotizados->length > 0) {
                $clienteCotizacion = $clientesCotizados->item(0)->getElementsByTagName('clienteCotizacion');
                if ($clienteCotizacion->length > 0) {
                    $nombre = $clienteCotizacion->item(0)->getElementsByTagName('nombre');
                    if ($nombre->length > 0) {
                        $nombre = $nombre->item(0)->nodeValue;
                    } else {
                        $nombre = "";
                    }
                    $apellido = $clienteCotizacion->item(0)->getElementsByTagName('apellido');
                    if ($apellido->length > 0) {
                        $apellido = $apellido->item(0)->nodeValue;
                    } else {
                        $apellido = "";
                    }
                    $edad = $clienteCotizacion->item(0)->getElementsByTagName('edad');
                    if ($edad->length > 0) {
                        $edad = $edad->item(0)->nodeValue;
                    } else {
                        $edad = "";
                    }
                    $pais = $clienteCotizacion->item(0)->getElementsByTagName('pais');
                    if ($pais->length > 0) {
                        $pais = $pais->item(0)->nodeValue;
                    } else {
                        $pais = "";
                    }
                    $codigo = $clienteCotizacion->item(0)->getElementsByTagName('codigo');
                    if ($codigo->length > 0) {
                        $codigo = $codigo->item(0)->nodeValue;
                    } else {
                        $codigo = "";
                    }
                    $fechaNacimiento = $clienteCotizacion->item(0)->getElementsByTagName('fechaNacimiento');
                    if ($fechaNacimiento->length > 0) {
                        $fechaNacimiento = $fechaNacimiento->item(0)->nodeValue;
                    } else {
                        $fechaNacimiento = "";
                    }
                    $valorTotal = $clienteCotizacion->item(0)->getElementsByTagName('valorTotal');
                    if ($valorTotal->length > 0) {
                        $valorTotal = $valorTotal->item(0)->nodeValue;
                    } else {
                        $valorTotal = "";
                    }
                    $valorPoliza = $clienteCotizacion->item(0)->getElementsByTagName('valorPoliza');
                    if ($valorPoliza->length > 0) {
                        $valorPoliza = $valorPoliza->item(0)->nodeValue;
                    } else {
                        $valorPoliza = "";
                    }
                    $valorAsistencia = $clienteCotizacion->item(0)->getElementsByTagName('valorAsistencia');
                    if ($valorAsistencia->length > 0) {
                        $valorAsistencia = $valorAsistencia->item(0)->nodeValue;
                    } else {
                        $valorAsistencia = "";
                    }
                    $valorIof = $clienteCotizacion->item(0)->getElementsByTagName('valorIof');
                    if ($valorIof->length > 0) {
                        $valorIof = $valorIof->item(0)->nodeValue;
                    } else {
                        $valorIof = "";
                    }
                    $valorTotalOriginal = $clienteCotizacion->item(0)->getElementsByTagName('valorTotalOriginal');
                    if ($valorTotalOriginal->length > 0) {
                        $valorTotalOriginal = $valorTotalOriginal->item(0)->nodeValue;
                    } else {
                        $valorTotalOriginal = "";
                    }
                    $valorTasaDeUso = $clienteCotizacion->item(0)->getElementsByTagName('valorTasaDeUso');
                    if ($valorTasaDeUso->length > 0) {
                        $valorTasaDeUso = $valorTasaDeUso->item(0)->nodeValue;
                    } else {
                        $valorTasaDeUso = "";
                    }
                    $aplica = $clienteCotizacion->item(0)->getElementsByTagName('aplica');
                    if ($aplica->length > 0) {
                        $aplica = $aplica->item(0)->nodeValue;
                    } else {
                        $aplica = "";
                    }
                }
            }
        }
    }
}

echo '<br/>Done';
?>