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

$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];


$url = 'http://testxml.youtravel.com/webservicestest/get_hotel_list.asp?LangID=EN&Username=xmltestme&Password=testme';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'text/xml;charset=ISO-8859-1',
    'Content-Length' => '0'
));
$client->setUri($url);
$client->setMethod('POST');
//$client->setRawBody($raw);
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

echo "RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
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
$HtSearchRq = $inputDoc->getElementsByTagName("HtSearchRq");
$Country = $HtSearchRq->item(0)->getElementsByTagName("Country");
if ($Country->length > 0) {
    for ($i=0; $i < $Country->length; $i++) { 
        $ID = $Country->item($i)->getAttribute("ID");
        $Name = $Country->item($i)->getAttribute("Name");
        $Code = $Country->item($i)->getAttribute("Code");

        $Destination = $Country->item($i)->getElementsByTagName("Destination");
        if ($Destination->length > 0) {
            for ($j=0; $j < $Destination->length; $j++) { 
                $ID = $Destination->item($j)->getAttribute("ID");
                $name = $Destination->item($j)->getAttribute("name");

                $ISO_Codes = $Destination->item($j)->getElementsByTagName("ISO_Codes");
                if ($ISO_Codes->length > 0) {
                    $Code_1 = $ISO_Codes->item(0)->getAttribute("Code_1");
                    $Code_2 = $ISO_Codes->item(0)->getAttribute("Code_2");
                    $Code_3 = $ISO_Codes->item(0)->getAttribute("Code_3");
                } else {
                    $Code_1 = "";
                    $Code_2 = "";
                    $Code_3 = "";
                }

                $Resort = $Destination->item($j)->getElementsByTagName("Resort");
                if ($Resort->length > 0) {
                    for ($k=0; $k < $Resort->length; $k++) { 
                        $ID = $Resort->item($k)->getAttribute("ID");

                        $Resort_Name = $Resort->item($k)->getElementsByTagName("Resort_Name");
                        if ($Resort_Name->length > 0) {
                            $Resort_Name = $Resort_Name->item(0)->nodeValue;
                        } else {
                            $Resort_Name = "";
                        }

                        $Hotel = $Resort->item($k)->getElementsByTagName("Hotel");
                        if ($Hotel->length > 0) {
                            $Hotel_ID = $Hotel->item(0)->getElementsByTagName("Hotel_ID");
                            if ($Hotel_ID->length > 0) {
                                $Hotel_ID = $Hotel_ID->item(0)->nodeValue;
                            } else {
                                $Hotel_ID = "";
                            }
                            $Hotel_Name = $Hotel->item(0)->getElementsByTagName("Hotel_Name");
                            if ($Hotel_Name->length > 0) {
                                $Hotel_Name = $Hotel_Name->item(0)->nodeValue;
                            } else {
                                $Hotel_Name = "";
                            }

                            $Mapping = $Hotel->item(0)->getElementsByTagName("Mapping");
                            if ($Mapping->length > 0) {
                                $Latitude = $Mapping->item(0)->getElementsByTagName("Latitude");
                                if ($Latitude->length > 0) {
                                    $Latitude = $Latitude->item(0)->nodeValue;
                                } else {
                                    $Latitude = "";
                                }
                                $Longitude = $Mapping->item(0)->getElementsByTagName("Longitude");
                                if ($Longitude->length > 0) {
                                    $Longitude = $Longitude->item(0)->nodeValue;
                                } else {
                                    $Longitude = "";
                                }
                            }
                        }
                    }
                } else {
                    $Code_1 = "";
                }
            }
        }
    }
}

/* try {
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('hoteis');
    $select->where(array(
        'id' => $id
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = (int)$data['id'];
        if ($id > 0) {
            $sql = new Sql($db);
            $data = array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'recomended' => $recomended,
                'stars' => $stars
            );
            $where['id = ?'] = $id;
            $update = $sql->update('hoteis', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hoteis');
            $insert->values(array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'recomended' => $recomended,
                'stars' => $stars
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
    } else {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('hoteis');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'name' => $name,
            'city' => $city,
            'country' => $country,
            'recomended' => $recomended,
            'stars' => $stars
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
} catch (Exception $ex) {
    echo $return;
    echo "ERRO: " . $ex;
    echo $return;
} */

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>