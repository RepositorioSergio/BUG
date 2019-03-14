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
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}
$config = new \Zend\Config\Config(include '../config/autoload/global.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
// Start
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enableactivitiestrapsatur' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_trapsaturpackages = $affiliate_id;
} else {
    $affiliate_id_trapsaturpackages = 0;
}
$sql = "select value from settings where name='activitiestrapsaturLogin' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $activitiestrapsaturLogin = $row_settings['value'];
}
$sql = "select value from settings where name='activitiestrapsaturPassword' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $activitiestrapsaturPassword = $row_settings['value'];
}
echo $return;
echo "activitiestrapsaturPassword: " . $activitiestrapsaturPassword;
echo $return;
$sql = "select value from settings where name='activitiestrapsaturServiceURL' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $activitiestrapsaturServiceURL = $row['value'];
}
$sql = "select value from settings where name='activitiestrapsaturEntityKey' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $activitiestrapsaturEntityKey = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

try {
    $client = new SoapClient($activitiestrapsaturServiceURL, array(
        'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        "trace" => 1,
        "exceptions" => true,
        'soap_version' => SOAP_1_1,
        "connection_timeout" => 100
    ));
} catch (\Exception $e) {
    var_dump($e);
    die();
}
    var_dump($client);

$params = array(
    "claveEntidad" => $activitiestrapsaturEntityKey,
    "login" => $activitiestrapsaturLogin,
    "password" => $activitiestrapsaturPassword,
    "idioma" => "es",
);
try {
    $client->__soapCall('serviciosCiudad', array(
        $params
    ));
} catch (\Exception $e) {
        var_dump($e);
    die();
}
    //var_dump($client);
$xmlrequest = $client->__getLastRequest();
$xmlresult = $client->__getLastResponse();

//echo $xmlresult;
$config = new \Zend\Config\Config(include '../config/autoload/global.trapsatur.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult);
$node = $inputDoc->getElementsByTagName("servicios");
$node = $inputDoc->getElementsByTagName("multiRef");

for ($j=0; $j < $node->length; $j++) { 
    $cityCode = $node->item($j)->getElementsByTagName('cityCode');
    if ($cityCode->length > 0) {
        $cityCode = $cityCode->item(0)->nodeValue;
    } else {
        $cityCode = "";
    }
    echo $return;
    echo "cityCode: " . $cityCode;
    echo $return;
    $cityName = $node->item($j)->getElementsByTagName('cityName');
    if ($cityName->length > 0) {
        $cityName = $cityName->item(0)->nodeValue;
    } else {
        $cityName = "";
    }
    $countryCode = $node->item($j)->getElementsByTagName('countryCode');
    if ($countryCode->length > 0) {
        $countryCode = $countryCode->item(0)->nodeValue;
    } else {
        $countryCode = "";
    }
    $countryName = $node->item($j)->getElementsByTagName('countryName');
    if ($countryName->length > 0) {
        $countryName = $countryName->item(0)->nodeValue;
    } else {
        $countryName = "";
    }
    $serviceCode = $node->item($j)->getElementsByTagName('serviceCode');
    if ($serviceCode->length > 0) {
        $serviceCode = $serviceCode->item(0)->nodeValue;
    } else {
        $serviceCode = "";
    }
    $serviceName = $node->item($j)->getElementsByTagName('serviceName');
    if ($serviceName->length > 0) {
        $serviceName = $serviceName->item(0)->nodeValue;
    } else {
        $serviceName = "";
    }
    $typeCode = $node->item($j)->getElementsByTagName('typeCode');
    if ($typeCode->length > 0) {
        $typeCode = $typeCode->item(0)->nodeValue;
    } else {
        $typeCode = "";
    }
    $typeName = $node->item($j)->getElementsByTagName('typeName');
    if ($typeName->length > 0) {
        $typeName = $typeName->item(0)->nodeValue;
    } else {
        $typeName = "";
    }
    $longDesription = $node->item($j)->getElementsByTagName('longDesription');
    if ($longDesription->length > 0) {
        $longDesription = $longDesription->item(0)->nodeValue;
    } else {
        $longDesription = "";
    }
    $tinyDescripcion = $node->item($j)->getElementsByTagName('tinyDescripcion');
    if ($tinyDescripcion->length > 0) {
        $tinyDescripcion = $tinyDescripcion->item(0)->nodeValue;
    } else {
        $tinyDescripcion = "";
    }
    $photoUrl = $node->item($j)->getElementsByTagName('photoUrl');
    if ($photoUrl->length > 0) {
        $photoUrl = $photoUrl->item(0)->nodeValue;
    } else {
        $photoUrl = "";
    }
    $basePrice = $node->item($j)->getElementsByTagName('basePrice');
    if ($basePrice->length > 0) {
        $basePrice = $basePrice->item(0)->nodeValue;
    } else {
        $basePrice = "";
    }
    $totPaxBasePrice = $node->item($j)->getElementsByTagName('totPaxBasePrice');
    if ($totPaxBasePrice->length > 0) {
        $totPaxBasePrice = $totPaxBasePrice->item(0)->nodeValue;
    } else {
        $totPaxBasePrice = "";
    }
    


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('servicos_cidades');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'cityCode' => $cityCode,
            'cityName' => $cityName,
            'countryCode' => $countryCode,
            'countryName' => $countryName,
            'serviceCode' => $serviceCode,
            'serviceName' => $serviceName,
            'typeCode' => $typeCode,
            'typeName' => $typeName,
            'longDesription' => $longDesription,
            'tinyDescripcion' => $tinyDescripcion,
            'photoUrl' => $photoUrl,
            'basePrice' => $basePrice,
            'totPaxBasePrice' => $totPaxBasePrice
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (Exception $e) {
        echo $return;
        echo "Exception: " . $e;
        echo $return;
    }


    $precioServicio = $node->item($j)->getElementsByTagName('precioServicio');
    for ($jAux=0; $jAux < $precioServicio->length; $jAux++) { 
        $fromPax = $precioServicio->item($jAux)->getElementsByTagName('fromPax');
        if ($fromPax->length > 0) {
            $fromPax = $fromPax->item(0)->nodeValue;
        } else {
            $fromPax = "";
        }
        $toPax = $precioServicio->item($jAux)->getElementsByTagName('toPax');
        if ($toPax->length > 0) {
            $toPax = $toPax->item(0)->nodeValue;
        } else {
            $toPax = "";
        }
        $priceEuros = $precioServicio->item($jAux)->getElementsByTagName('priceEuros');
        if ($priceEuros->length > 0) {
            $priceEuros = $priceEuros->item(0)->nodeValue;
        } else {
            $priceEuros = "";
        }
        $priceDollars = $precioServicio->item($jAux)->getElementsByTagName('priceDollars');
        if ($priceDollars->length > 0) {
            $priceDollars = $priceDollars->item(0)->nodeValue;
        } else {
            $priceDollars = "";
        }
        $typePrice = $precioServicio->item($jAux)->getElementsByTagName('typePrice');
        if ($typePrice->length > 0) {
            $typePrice = $typePrice->item(0)->nodeValue;
        } else {
            $typePrice = "";
        }



        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('servicoscidades_preco');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'fromPax' => $fromPax,
                'toPax' => $toPax,
                'priceEuros' => $priceEuros,
                'priceDollars' => $priceDollars,
                'typePrice' => $typePrice
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (Exception $e) {
            echo $return;
            echo "Exception: " . $e;
            echo $return;
        }

    }

}

$node2 = $inputDoc->getElementsByTagName("terminalesTte");
$node2 = $inputDoc->getElementsByTagName("multiRef");
for ($k=0; $k < $node2->length; $k++) { 
    $codigo = $node2->item($k)->getElementsByTagName('codigo');
    if ($codigo->length > 0) {
        $codigo = $codigo->item(0)->nodeValue;
    } else {
        $codigo = "";
    }
    $descripcion = $node2->item($k)->getElementsByTagName('descripcion');
    if ($descripcion->length > 0) {
        $descripcion = $descripcion->item(0)->nodeValue;
    } else {
        $descripcion = "";
    }
    $codigoCiudad = $node2->item($k)->getElementsByTagName('codigoCiudad');
    if ($codigoCiudad->length > 0) {
        $codigoCiudad = $codigoCiudad->item(0)->nodeValue;
    } else {
        $codigoCiudad = "";
    }
    $descripcionCiudad = $node2->item($k)->getElementsByTagName('descripcionCiudad');
    if ($descripcionCiudad->length > 0) {
        $descripcionCiudad = $descripcionCiudad->item(0)->nodeValue;
    } else {
        $descripcionCiudad = "";
    }
    $tipo = $node2->item($k)->getElementsByTagName('tipo');
    if ($tipo->length > 0) {
        $tipo = $tipo->item(0)->nodeValue;
    } else {
        $tipo = "";
    }
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('servicoscidade_terminais');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codigo' => $codigo,
            'descripcion' => $descripcion,
            'codigoCiudad' => $codigoCiudad,
            'descripcionCiudad' => $descripcionCiudad,
            'tipo' => $tipo
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (Exception $e) {
        echo $return;
        echo "Exception: " . $e;
        echo $return;
    }

}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>


