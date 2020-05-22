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
echo "COMECOU SEARCH<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.sabre.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$ipcc = 'BJ1G';
$credentials = "V1:7971:BJ1G:AA";
$username = '7971';
$password = 'WS121414';
$client_id = 'V1:43r2fu5rh3m4eh9l:DEVCENTER:EXT';
$client_secret = 'Sc6P5vpK';
$secret = base64_encode($client_secret);
$cred = base64_encode(base64_encode($client_id) . ":" . $secret);
echo "<br/>CREDENTIALS " . $cred;

$ch = curl_init("https://api-crt.cert.havail.sabre.com/v2/auth/token");
$vars = "grant_type=client_credentials";
$headers = array(
    'Authorization: Basic ' . $cred,
    'Accept: */*',
    'Content-Type: application/x-www-form-urlencoded'
);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

$result = json_decode($result,true);
$access_token = $result['access_token'];
echo "<br/>TOKEN  " . $access_token;

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/offers/searchSailings';

$raw = '{
    "agencyPOS": {
      "pcc": "BJ1G",
      "currencyCode": "USD"
    },
    "itineraryQualifier": {
        "sailingMediaFlag": true,
        "voyagePrefs": [
          {
            "voyageId": "SC01200701SC04I497",
            "vendorCode": "RC"
          }
        ]
      }
  }';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';


$headers = array(
    "Accept: application/json",
    "Content-Type: application/json",
    "Accept-Encoding: gzip",
    "Authorization: Bearer " . $access_token,
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_USERPWD, $ipcc . ":" . $password);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "RESPONSE: " . $response;
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.sabre.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$response = json_decode($response, true);

$sailingInfo = $response['sailingInfo'];
$totalEntries = $sailingInfo['totalEntries'];
$totalPages = $sailingInfo['totalPages'];
$sailOptions = $sailingInfo['sailOptions'];
if (count($sailOptions) > 0) {
    for ($i=0; $i < count($sailOptions); $i++) { 
        $vendorCode = $sailOptions[$i]['vendorCode'];
        $voyageId = $sailOptions[$i]['voyageId'];
        $shipCode = $sailOptions[$i]['shipCode'];
        $departureDate = $sailOptions[$i]['departureDate'];
        $arrivalDate = $sailOptions[$i]['arrivalDate'];
        $duration = $sailOptions[$i]['duration'];
        $cruiseTour = $sailOptions[$i]['cruiseTour'];
        $currencyCode = $sailOptions[$i]['currencyCode'];
        $embarkationPort = $sailOptions[$i]['embarkationPort'];
        $disembarkationPort = $sailOptions[$i]['disembarkationPort'];
        $portsOfCallQty = $sailOptions[$i]['portsOfCallQty'];
        $sailingDesc = $sailOptions[$i]['sailingDesc'];
        $shipImage = $sailOptions[$i]['shipImage'];
        $themeCode = $sailOptions[$i]['themeCode'];
        $status = $sailOptions[$i]['status'];
        $itineraryInfo = $sailOptions[$i]['itineraryInfo'];
        $itineraryImage = $itineraryInfo['itineraryImage'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cruiseinfo');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'vendorcode' => $vendorCode,
                'voyageid' => $voyageId,
                'shipcode' => $shipCode,
                'departuredate' => $departureDate,
                'arrivaldate' => $arrivalDate,
                'duration' => $duration,
                'cruisetour' => $cruiseTour,
                'currencycode' => $currencyCode,
                'embarkationport' => $embarkationPort,
                'disembarkationport' => $disembarkationPort,
                'portsofcallqty' => $portsOfCallQty,
                'sailingdesc' => $sailingDesc,
                'shipimage' => $shipImage,
                'themecode' => $themeCode,
                'status' => $status,
                'itineraryinfo' => $itineraryInfo,
                'itineraryimage' => $itineraryImage
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO 1: " . $e;
            echo $return;
        }

        $itineraryItems = $itineraryInfo['itineraryItems'];
        if (count($itineraryItems) > 0) {
            for ($iAux=0; $iAux < count($itineraryItems); $iAux++) { 
                $itinRemarkText = $itineraryItems[$iAux]['itinRemarkText'];
                $portCode = $itineraryItems[$iAux]['portCode'];
                $portDesc = $itineraryItems[$iAux]['portDesc'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('cruiseinfo_itineraryitems');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'itinremarktext' => $itinRemarkText,
                        'portcode' => $portCode,
                        'portdesc' => $portDesc,
                        'shipcode' => $shipCode
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 2: " . $e;
                    echo $return;
                }

                $dateTimeDesc = $itineraryItems[$iAux]['dateTimeDesc'];
                if (count($dateTimeDesc) > 0) {
                    for ($iAux2=0; $iAux2 < count($dateTimeDesc); $iAux2++) { 
                        $dateDetails = $dateTimeDesc[$iAux2]['dateDetails'];
                        $qualifierCode = $dateTimeDesc[$iAux2]['qualifierCode'];
                        $timeDetails = $dateTimeDesc[$iAux2]['timeDetails'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('cruiseinfo_itineraryitems_datetimedesc');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'datedetails' => $dateDetails,
                                'qualifiercode' => $qualifierCode,
                                'timedetails' => $timeDetails,
                                'shipcode' => $shipCode
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO 3: " . $e;
                            echo $return;
                        }
                    }
                }
            }
        }
        $agencyGroupInfos = $sailOptions[$i]['agencyGroupInfos'];
        if (count($agencyGroupInfos) > 0) {
            for ($iAux3=0; $iAux3 < count($agencyGroupInfos); $iAux3++) { 
                $groupId = $agencyGroupInfos[$iAux3]['groupId'];
                $groupName = $agencyGroupInfos[$iAux3]['groupName'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('cruiseinfo_agencygroupinfos');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'groupid' => $groupId,
                        'groupname' => $groupName,
                        'shipcode' => $shipCode
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 4: " . $e;
                    echo $return;
                }
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
