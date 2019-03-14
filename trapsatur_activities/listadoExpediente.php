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
$sql = "select value from settings where name='enabletrapsaturpackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_trapsaturpackages = $affiliate_id;
} else {
    $affiliate_id_trapsaturpackages = 0;
}
$sql = "select value from settings where name='trapsaturpackagesLogin' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $trapsaturpackagesLogin = $row_settings['value'];
}
$sql = "select value from settings where name='trapsaturpackagesPassword' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $trapsaturpackagesPassword = base64_decode($row_settings['value']);
}
echo $return;
echo "trapsaturpackagesPassword: " . $trapsaturpackagesPassword;
echo $return;
$sql = "select value from settings where name='trapsaturpackagesServiceURL' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $trapsaturpackagesServiceURL = $row['value'];
}
$sql = "select value from settings where name='trapsaturpackagesEntityKey' and affiliate_id=$affiliate_id_trapsaturpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $trapsaturpackagesEntityKey = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();


try {
    $client = new SoapClient($trapsaturpackagesServiceURL, array(
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
    "claveEntidad" => $trapsaturpackagesEntityKey,
    "login" => $trapsaturpackagesLogin,
    "password" => $trapsaturpackagesPassword,
    "idioma" => "es",
    "fechaDesde" => "2019-06-01",
    "fechaHasta" => "2019-12-20",
    "tipoPeriodo" => "A"
);
try {
    $client->__soapCall('listadoPExpedientes', array(
        $params
    ));
} catch (\Exception $e) {
        var_dump($e);
    die();
}
    //var_dump($client);
$xmlrequest = $client->__getLastRequest();
$xmlresult = $client->__getLastResponse();
echo $xmlresult;

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
$node = $inputDoc->getElementsByTagName("expedientes");
$node = $inputDoc->getElementsByTagName("multiRef");
echo $return;
echo "TAM: " . $node->length;
echo $return;
for ($j=0; $j < $node->length; $j++) { 
    $number = $node->item($j)->getElementsByTagName('number');
    if ($number->length > 0) {
        $number = $number->item(0)->nodeValue;
    } else {
        $number = "";
    }
    echo $return;
    echo "number: " . $number;
    echo $return;
    $namePassenger = $node->item($j)->getElementsByTagName('namePassenger');
    if ($namePassenger->length > 0) {
        $namePassenger = $namePassenger->item(0)->nodeValue;
    } else {
        $namePassenger = "";
    }
    echo $return;
    echo "namePassenger: " . $namePassenger;
    echo $return;
    $dateCheckin = $node->item($j)->getElementsByTagName('dateCheckin');
    if ($dateCheckin->length > 0) {
        $dateCheckin = $dateCheckin->item(0)->nodeValue;
    } else {
        $dateCheckin = "";
    }
    echo $return;
    echo "dateCheckin: " . $dateCheckin;
    echo $return;
    $dateCreation = $node->item($j)->getElementsByTagName('dateCreation');
    if ($dateCreation->length > 0) {
        $dateCreation = $dateCreation->item(0)->nodeValue;
    } else {
        $dateCreation = "";
    }
    $user = $node->item($j)->getElementsByTagName('user');
    if ($user->length > 0) {
        $user = $user->item(0)->nodeValue;
    } else {
        $user = "";
    }
    $nameUser = $node->item($j)->getElementsByTagName('nameUser');
    if ($nameUser->length > 0) {
        $nameUser = $nameUser->item(0)->nodeValue;
    } else {
        $nameUser = "";
    }
    $paxNumber = $node->item($j)->getElementsByTagName('paxNumber');
    if ($paxNumber->length > 0) {
        $paxNumber = $paxNumber->item(0)->nodeValue;
    } else {
        $paxNumber = "";
    }
    echo $return;
    echo "paxNumber: " . $paxNumber;
    echo $return;
    $dateCancellation = $node->item($j)->getElementsByTagName('dateCancellation');
    if ($dateCancellation->length > 0) {
        $dateCancellation = $dateCancellation->item(0)->nodeValue;
    } else {
        $dateCancellation = "";
    }
    $suReferencia = $node->item($j)->getElementsByTagName('suReferencia');
    if ($suReferencia->length > 0) {
        $suReferencia = $suReferencia->item(0)->nodeValue;
    } else {
        $suReferencia = "";
    }
    echo $return;
    echo "suReferencia: " . $suReferencia;
    echo $return;
    $status = $node->item($j)->getElementsByTagName('status');
    if ($status->length > 0) {
        $status = $status->item(0)->nodeValue;
    } else {
        $status = "";
    }
    echo $return;
    echo "status: " . $status;
    echo $return;
    $pago = $node->item($j)->getElementsByTagName('pago');
    if ($pago->length > 0) {
        $pago = $pago->item(0)->nodeValue;
    } else {
        $pago = "";
    }
    echo $return;
    echo "pago: " . $pago;
    echo $return;
    $ammountDollars = $node->item($j)->getElementsByTagName('ammountDollars');
    if ($ammountDollars->length > 0) {
        $ammountDollars = $ammountDollars->item(0)->nodeValue;
    } else {
        $ammountDollars = "";
    }
    $ammountEuros = $node->item($j)->getElementsByTagName('ammountEuros');
    if ($ammountEuros->length > 0) {
        $ammountEuros = $ammountEuros->item(0)->nodeValue;
    } else {
        $ammountEuros = "";
    }
    echo $return;
    echo "ammountEuros: " . $ammountEuros;
    echo $return;

    /* try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('expedientes');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'number' => $number,
            'namePassenger' => $namePassenger,
            'dateCheckin' => $dateCheckin,
            'dateCreation' => $dateCreation,
            'user' => $user,
            'nameUser' => $nameUser,
            'paxNumber' => $paxNumber,
            'dateCancellation' => $dateCancellation,
            'suReferencia' => $suReferencia,
            'status' => $status,
            'pago' => $pago,
            'ammountDollars' => $ammountDollars,
            'ammountEuros' => $ammountEuros
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
    } */


    $prestaciones = $node->item($j)->getElementsByTagName('prestaciones');
    for ($jAux=0; $jAux < $prestaciones->length; $jAux++) { 
        $typeService = $prestaciones->item($jAux)->getElementsByTagName('typeService');
        if ($typeService->length > 0) {
            $typeService = $typeService->item(0)->nodeValue;
        } else {
            $typeService = "";
        }
        echo $return;
        echo "typeService: " . $typeService;
        echo $return;
        $dateCheckIn = $prestaciones->item($jAux)->getElementsByTagName('dateCheckIn');
        if ($dateCheckIn->length > 0) {
            $dateCheckIn = $dateCheckIn->item(0)->nodeValue;
        } else {
            $dateCheckIn = "";
        }
        $locator = $prestaciones->item($jAux)->getElementsByTagName('locator');
        if ($locator->length > 0) {
            $locator = $locator->item(0)->nodeValue;
        } else {
            $locator = "";
        }
        $service = $prestaciones->item($jAux)->getElementsByTagName('service');
        if ($service->length > 0) {
            $service = $service->item(0)->nodeValue;
        } else {
            $service = "";
        }

        /* try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('expedientes_prestaciones');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'typeService' => $typeService,
                'dateCheckIn' => $dateCheckIn,
                'locator' => $locator,
                'service' => $service
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
        } */

    }


}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>


