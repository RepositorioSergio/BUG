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
echo "COMECOU ITINERARY DETAILS<br/>";
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
$access_token = "T1RLAQKTZ6gz/L9XvyVf3594eqV2cYxNiRDFg+aM/TaC/ik+hv83n/HiAADAMSIaX+ZwEYI5a3KjNzFm+l/bxubRBTCcLV+ol2SRZ/74CB5na3cN0iQ9qF85oUYZ5798sHNLP65AxeynE9Y07bUfEMA3/O7MNmGRCTSipPyLvUT6NZ7O97UIZmdNslCn7EkeE0hqMHR+vxcibsOIh6hGzaAmbQ1n37BXTghtesY8fjBBJKyDWFDCxhrnRgF7Vu7U5fblLk57izQARUU/dof5CzFz3vZAgui2rJ+Fn6InHqumtkMDgFfBBHkcDLp+";

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/offers/getItineraryDetails';

$raw = '{
    "agencyPOS": {
      "pcc": "BJ1G",
      "currencyCode": "USD"
    },
    "voyageId": "MA01201002MA03S135",
    "vendorCode": "RC"
  }';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$headers = array(
    "Accept: application/json",
    "Content-Type: application/json",
    "Accept-Encoding: gzip",
    'Authorization: Bearer ' . $access_token,
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_USERPWD, $ipcc . ":" . $password);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo '<br/>RESPONSE: ' . $response;

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

$itineraryImage = $response['itineraryImage'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('itinerarydetails');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
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

$itineraryItems = $response['itineraryItems'];
if (count($itineraryItems) > 0) {
    for ($iAux=0; $iAux < count($itineraryItems); $iAux++) { 
        $itinRemarkText = $itineraryItems[$iAux]['itinRemarkText'];
        $portCode = $itineraryItems[$iAux]['portCode'];
        $portDesc = $itineraryItems[$iAux]['portDesc'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('itinerarydetails_itineraryitems');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'itinremarktext' => $itinRemarkText,
                'portcode' => $portCode,
                'portdesc' => $portDesc
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
                    $insert->into('itinerarydetails_itineraryitems_datetimedesc');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'datedetails' => $dateDetails,
                        'qualifiercode' => $qualifierCode,
                        'timedetails' => $timeDetails
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


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
