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
echo "COMECOU HOTELS COMPLETE";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.hoteldo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = 'http://xml.e-tsw.com/AffiliateService/V1.0/AffiliateService.svc/restful/GetHotelsComplete?a=DIVISAXML';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.hoteldo.php');
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
$Hotels = $inputDoc->getElementsByTagName("Hotels");
if ($Hotels->length > 0) {
    $Hotel = $Hotels->item(0)->getElementsByTagName("Hotel");
    if ($Hotel->length > 0) {
        for ($i=0; $i < $Hotel->length; $i++) { 
            $Id = $Hotel->item($i)->getElementsByTagName('Id');
            if ($Id->length > 0) {
                $Id = $Id->item(0)->nodeValue;
            } else {
                $Id = "";
            }
            $Name = $Hotel->item($i)->getElementsByTagName('Name');
            if ($Name->length > 0) {
                $Name = $Name->item(0)->nodeValue;
            } else {
                $Name = "";
            }
            $Active = $Hotel->item($i)->getElementsByTagName('Active');
            if ($Active->length > 0) {
                $Active = $Active->item(0)->nodeValue;
            } else {
                $Active = "";
            }
            $CityId = $Hotel->item($i)->getElementsByTagName('CityId');
            if ($CityId->length > 0) {
                $CityId = $CityId->item(0)->nodeValue;
            } else {
                $CityId = "";
            }
            $CityName = $Hotel->item($i)->getElementsByTagName('CityName');
            if ($CityName->length > 0) {
                $CityName = $CityName->item(0)->nodeValue;
            } else {
                $CityName = "";
            }
            $DestinationId = $Hotel->item($i)->getElementsByTagName('DestinationId');
            if ($DestinationId->length > 0) {
                $DestinationId = $DestinationId->item(0)->nodeValue;
            } else {
                $DestinationId = "";
            }
            $DestinationName = $Hotel->item($i)->getElementsByTagName('DestinationName');
            if ($DestinationName->length > 0) {
                $DestinationName = $DestinationName->item(0)->nodeValue;
            } else {
                $DestinationName = "";
            }
            $Category = $Hotel->item($i)->getElementsByTagName('Category');
            if ($Category->length > 0) {
                $Category = $Category->item(0)->nodeValue;
            } else {
                $Category = "";
            }
            $Address = $Hotel->item($i)->getElementsByTagName('Address');
            if ($Address->length > 0) {
                $Address = $Address->item(0)->nodeValue;
            } else {
                $Address = "";
            }
            $Latitude = $Hotel->item($i)->getElementsByTagName('Latitude');
            if ($Latitude->length > 0) {
                $Latitude = $Latitude->item(0)->nodeValue;
            } else {
                $Latitude = "";
            }
            $Longitude = $Hotel->item($i)->getElementsByTagName('Longitude');
            if ($Longitude->length > 0) {
                $Longitude = $Longitude->item(0)->nodeValue;
            } else {
                $Longitude = "";
            }
            $CountryCode = $Hotel->item($i)->getElementsByTagName('CountryCode');
            if ($CountryCode->length > 0) {
                $CountryCode = $CountryCode->item(0)->nodeValue;
            } else {
                $CountryCode = "";
            }
    
            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('hotelscomplete');
                $select->where(array(
                    'id' => $Id
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
                            'id' => $Id,
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'name' => $Name,
                            'active' => $Active,
                            'cityid' => $CityId,
                            'cityname' => $CityName,
                            'destinationid' => $DestinationId,
                            'destinationname' => $DestinationName,
                            'category' => $Category,
                            'address' => $Address,
                            'latitude' => $Latitude,
                            'longitude' => $Longitude,
                            'countrycode' => $CountryCode 
                        );
                        $where['id = ?'] = $Id;
                        $update = $sql->update('hotelscomplete', $data, $where);
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hotelscomplete');
                        $insert->values(array(
                            'id' => $Id,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'name' => $Name,
                            'active' => $Active,
                            'cityid' => $CityId,
                            'cityname' => $CityName,
                            'destinationid' => $DestinationId,
                            'destinationname' => $DestinationName,
                            'category' => $Category,
                            'address' => $Address,
                            'latitude' => $Latitude,
                            'longitude' => $Longitude,
                            'countrycode' => $CountryCode 
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
                    $insert->into('hotelscomplete');
                    $insert->values(array(
                        'id' => $Id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'name' => $Name,
                        'active' => $Active,
                        'cityid' => $CityId,
                        'cityname' => $CityName,
                        'destinationid' => $DestinationId,
                        'destinationname' => $DestinationName,
                        'category' => $Category,
                        'address' => $Address,
                        'latitude' => $Latitude,
                        'longitude' => $Longitude,
                        'countrycode' => $CountryCode 
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                }
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO: ". $e;
                echo $return;
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>
