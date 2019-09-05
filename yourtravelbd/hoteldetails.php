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


$url = 'http://testxml.youtravel.com/webservicestest/get_hoteldetails.asp?LangID=EN&HID=2970&Username=xmltestme&Password=testme&sha=1';

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

$Destination = $HtSearchRq->item(0)->getElementsByTagName("Destination");
if ($Destination->length > 0) {
    $Destination = $Destination->item(0)->nodeValue;
} else {
    $Destination = "";
}
$HID = $HtSearchRq->item(0)->getElementsByTagName("HID");
if ($HID->length > 0) {
    $HID = $HID->item(0)->nodeValue;
} else {
    $HID = "";
}

$Hotel = $HtSearchRq->item(0)->getElementsByTagName("Hotel");
if ($Hotel->length > 0) {
    $Name = $Hotel->item(0)->getAttribute("Name");

    $Youtravel_Rating = $Hotel->item(0)->getElementsByTagName("Youtravel_Rating");
    if ($Youtravel_Rating->length > 0) {
        $Youtravel_Rating = $Youtravel_Rating->item(0)->nodeValue;
    } else {
        $Youtravel_Rating = "";
    }
    $Official_Rating = $Hotel->item(0)->getElementsByTagName("Official_Rating");
    if ($Official_Rating->length > 0) {
        $Official_Rating = $Official_Rating->item(0)->nodeValue;
    } else {
        $Official_Rating = "";
    }
    $Board_Type = $Hotel->item(0)->getElementsByTagName("Board_Type");
    if ($Board_Type->length > 0) {
        $Board_Type = $Board_Type->item(0)->nodeValue;
    } else {
        $Board_Type = "";
    }
    $Hotel_Desc = $Hotel->item(0)->getElementsByTagName("Hotel_Desc");
    if ($Hotel_Desc->length > 0) {
        $Hotel_Desc = $Hotel_Desc->item(0)->nodeValue;
    } else {
        $Hotel_Desc = "";
    }
    $AI_Type = $Hotel->item(0)->getElementsByTagName("AI_Type");
    if ($AI_Type->length > 0) {
        $AI_Type = $AI_Type->item(0)->nodeValue;
    } else {
        $AI_Type = "";
    }
    $AI_Desc = $Hotel->item(0)->getElementsByTagName("AI_Desc");
    if ($AI_Desc->length > 0) {
        $AI_Desc = $AI_Desc->item(0)->nodeValue;
    } else {
        $AI_Desc = "";
    }
    $AI_Facilities = $Hotel->item(0)->getElementsByTagName("AI_Facilities");
    if ($AI_Facilities->length > 0) {
        $AI_Facilities = $AI_Facilities->item(0)->nodeValue;
    } else {
        $AI_Facilities = "";
    }
    $Erratas = $Hotel->item(0)->getElementsByTagName("Erratas");
    if ($Erratas->length > 0) {
        $Erratas = $Erratas->item(0)->nodeValue;
    } else {
        $Erratas = "";
    }

    $Hotel_Address = $Hotel->item(0)->getElementsByTagName("Hotel_Address");
    if ($Hotel_Address->length > 0) {
        $Address = $Hotel_Address->item(0)->getElementsByTagName("Address");
        if ($Address->length > 0) {
            $Address = $Address->item(0)->nodeValue;
        } else {
            $Address = "";
        }
        $Post_Code = $Hotel_Address->item(0)->getElementsByTagName("Post_Code");
        if ($Post_Code->length > 0) {
            $Post_Code = $Post_Code->item(0)->nodeValue;
        } else {
            $Post_Code = "";
        }
        $City = $Hotel_Address->item(0)->getElementsByTagName("City");
        if ($City->length > 0) {
            $City = $City->item(0)->nodeValue;
        } else {
            $City = "";
        }
        $Phone = $Hotel_Address->item(0)->getElementsByTagName("Phone");
        if ($Phone->length > 0) {
            $Phone = $Phone->item(0)->nodeValue;
        } else {
            $Phone = "";
        }
        $Fax = $Hotel_Address->item(0)->getElementsByTagName("Fax");
        if ($Fax->length > 0) {
            $Fax = $Fax->item(0)->nodeValue;
        } else {
            $Fax = "";
        }
    }

    $foto = "";
    $Hotel_Photos = $Hotel->item(0)->getElementsByTagName("Hotel_Photos");
    if ($Hotel_Photos->length > 0) {
        $Photo = $Hotel_Photos->item(0)->getElementsByTagName("Photo");
        if ($Photo->length > 0) {
            for ($i=0; $i < $Photo->length; $i++) { 
                $foto = $Photo->item($i)->nodeValue;
                echo $return;
                echo "FOTO: " . $foto;
                echo $return;
            }
        }
    }

    $facility = "";
    $Hotel_Facilities = $Hotel->item(0)->getElementsByTagName("Hotel_Facilities");
    if ($Hotel_Facilities->length > 0) {
        $Facility = $Hotel_Facilities->item(0)->getElementsByTagName("Facility");
        if ($Facility->length > 0) {
            for ($j=0; $j < $Facility->length; $j++) { 
                $facility = $Facility->item($j)->nodeValue;
            }
        }
    }

    $room = "";
    $Room_Types = $Hotel->item(0)->getElementsByTagName("Room_Types");
    if ($Room_Types->length > 0) {
        $Room = $Room_Types->item(0)->getElementsByTagName("Room");
        if ($Room->length > 0) {
            for ($k=0; $k < $Room->length; $k++) { 
                $name = $Room->item($k)->getAttribute("name");

                $Facility = $Room->item(0)->getElementsByTagName("Facility");
                if ($Facility->length > 0) {
                    for ($j=0; $j < $Facility->length; $j++) { 
                        $room = $Facility->item($j)->nodeValue;
                    }
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