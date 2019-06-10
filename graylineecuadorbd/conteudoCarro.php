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
echo "COMECOU CONTEUDO CARRO<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.graylineecuador.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));

$url = "http://demo.gl-tours.com/web_service_Cart.php?userCart=user1780375154&Lang=eng&User=TEST&Pass=1234";

$client->setUri($url);
$client->setMethod('POST');
$response = $client->send();
if ($response->isSuccess()) {
$response = $response->getBody();
} else {
$logger = new Logger();
$writer = new Writer\Stream('/srv/www/htdocs/error_log');
$logger->addWriter($writer);
$logger->info($client->getUri());
$logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
echo $return;
echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
echo $return;
die();
}
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.graylineecuador.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$response2 = $inputDoc->getElementsByTagName("response");
$process = $response2->item(0)->getElementsByTagName("process");

$Cart = $response2->item(0)->getElementsByTagName("Cart");
if ($Cart->length > 0) {
    for ($i=0; $i < $Cart->length; $i++) { 
        $id = $Cart->item($i)->getElementsByTagName("id");
        if ($id->length > 0) {
            $id = $id->item(0)->nodeValue;
        } else {
            $id = "";
        }
        $packageid = $Cart->item($i)->getElementsByTagName("packageid");
        if ($packageid->length > 0) {
            $packageid = $packageid->item(0)->nodeValue;
        } else {
            $packageid = "";
        }
        $optionalid = $Cart->item($i)->getElementsByTagName("optionalid");
        if ($optionalid->length > 0) {
            $optionalid = $optionalid->item(0)->nodeValue;
        } else {
            $optionalid = "";
        }
        $Package = $Cart->item($i)->getElementsByTagName("Package");
        if ($Package->length > 0) {
            $Package = $Package->item(0)->nodeValue;
        } else {
            $Package = "";
        }
        $Date = $Cart->item($i)->getElementsByTagName("Date");
        if ($Date->length > 0) {
            $Date = $Date->item(0)->nodeValue;
        } else {
            $Date = "";
        }
        $Schedule = $Cart->item($i)->getElementsByTagName("Schedule");
        if ($Schedule->length > 0) {
            $Schedule = $Schedule->item(0)->nodeValue;
        } else {
            $Schedule = "";
        }
        $Pick = $Cart->item($i)->getElementsByTagName("Pick");
        if ($Pick->length > 0) {
            $Pick = $Pick->item(0)->nodeValue;
        } else {
            $Pick = "";
        }
        $QtyA = $Cart->item($i)->getElementsByTagName("QtyA");
        if ($QtyA->length > 0) {
            $QtyA = $QtyA->item(0)->nodeValue;
        } else {
            $QtyA = "";
        }
        $QtyC = $Cart->item($i)->getElementsByTagName("QtyC");
        if ($QtyC->length > 0) {
            $QtyC = $QtyC->item(0)->nodeValue;
        } else {
            $QtyC = "";
        }
        $QtyI = $Cart->item($i)->getElementsByTagName("QtyI");
        if ($QtyI->length > 0) {
            $QtyI = $QtyI->item(0)->nodeValue;
        } else {
            $QtyI = "";
        }
        $Qty = $Cart->item($i)->getElementsByTagName("Qty");
        if ($Qty->length > 0) {
            $Qty = $Qty->item(0)->nodeValue;
        } else {
            $Qty = "";
        }
        $Cost = $Cart->item($i)->getElementsByTagName("Cost");
        if ($Cost->length > 0) {
            $Cost = $Cost->item(0)->nodeValue;
        } else {
            $Cost = "";
        }
        $Price = $Cart->item($i)->getElementsByTagName("Price");
        if ($Price->length > 0) {
            $Price = $Price->item(0)->nodeValue;
        } else {
            $Price = "";
        }
        $Type = $Cart->item($i)->getElementsByTagName("Type");
        if ($Type->length > 0) {
            $Type = $Type->item(0)->nodeValue;
        } else {
            $Type = "";
        }
        $duration = $Cart->item($i)->getElementsByTagName("duration");
        if ($duration->length > 0) {
            $duration = $duration->item(0)->nodeValue;
        } else {
            $duration = "";
        }
        $thumb = $Cart->item($i)->getElementsByTagName("thumb");
        if ($thumb->length > 0) {
            $thumb = $thumb->item(0)->nodeValue;
        } else {
            $thumb = "";
        }
        $destination = $Cart->item($i)->getElementsByTagName("destination");
        if ($destination->length > 0) {
            $destinationcode = $destination->item(0)->getAttribute("code");
            $destination = $destination->item(0)->nodeValue;
        } else {
            $destination = "";
        }
        $cancelaciones = $Cart->item($i)->getElementsByTagName("cancelaciones");
        if ($cancelaciones->length > 0) {
            $cancelacion = $cancelaciones->item(0)->getElementsByTagName("cancelacion");
            if ($cancelacion->length > 0) {
                $from = $cancelacion->item(0)->getElementsByTagName("from");
                if ($from->length > 0) {
                    $from = $from->item(0)->nodeValue;
                } else {
                    $from = "";
                }
                $to = $cancelacion->item(0)->getElementsByTagName("to");
                if ($to->length > 0) {
                    $to = $to->item(0)->nodeValue;
                } else {
                    $to = "";
                }
                $unit = $cancelacion->item(0)->getElementsByTagName("unit");
                if ($unit->length > 0) {
                    $unit = $unit->item(0)->nodeValue;
                } else {
                    $unit = "";
                }
                $percent = $cancelacion->item(0)->getElementsByTagName("percent");
                if ($percent->length > 0) {
                    $percent = $percent->item(0)->nodeValue;
                } else {
                    $percent = "";
                }
                $gmt = $cancelacion->item(0)->getElementsByTagName("gmt");
                if ($gmt->length > 0) {
                    $gmt = $gmt->item(0)->nodeValue;
                } else {
                    $gmt = "";
                }
            }
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('conteudoCarroCompras');
            $insert->values(array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'packageid' => $packageid,
                'optionalid' => $optionalid,
                'Package' => $Package,
                'Date' => $Date,
                'Schedule' => $Schedule,
                'Pick' => $Pick,
                'QtyA' => $QtyA,
                'QtyC' => $QtyC,
                'QtyI' => $QtyI,
                'Qty' => $Qty,
                'Cost' => $Cost,
                'Price' => $Price,
                'Type' => $Type,
                'duration' => $duration,
                'thumb' => $thumb,
                'destinationcode' => $destinationcode,
                'destination' => $destination,
                'from' => $from,
                'to' => $to,
                'unit' => $unit,
                'percent' => $percent,
                'gmt' => $gmt
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO: " . $e;
            echo $return;
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>