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
// Start
$affiliate_id_palace = 0;
$branch_filter = "";


$config = new \Zend\Config\Config(include '../config/autoload/global.symrooms.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = "https://api.travelgatex.com/";

$raw = '{"query":"{\n  hotelX {\n    search( criteria: {\n                checkIn: \"2019-12-23\",\n                checkOut: \"2019-12-24\",\n                hotels: [\"1\",\"2\",\"23\",\"1610\"],\n                occupancies: [ {paxes: [{age: 30}, {age: 30}]}]},\n                settings: {\n                      client: \"Demo_Client\",\n                      testMode: true,\n                      context: \"HOTELTEST\"}) {\n      options {\n        id\n        supplierCode\n        hotelCode\n        hotelName\n        boardCode\n    paymentType\n    status\n    rooms {\n    occupancyRefId\n     code\n   description\n    refundable\n    units\n    roomPrice {\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n    }\n    }\n    }\n  beds {\n    type\n    description\n    count\n    shared\n    }\n    ratePlans {\n    code\n    name\n    effectiveDate\n  expireDate\n   }\n    promotions {\n    code\n    name\n    effectiveDate\n    expireDate\n  }\n  }\n  supplements {\n   code\n    name\n    description\n    supplementType\n    chargeType\n    mandatory\n    durationType\n    quantity\n    unit\n    effectiveDate\n    expireDate\n    resort {\n    code\n    name\n    description\n    }\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n  }\n  }\n  }\n   surcharges {\n    chargeType\n    description\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n  }\n  }\n  }\n    rateRules \n    cancelPolicy {\n    refundable\n    cancelPenalties {\n    hoursBefore\n    penaltyType\n    currency\n   value\n  }\n  }\n      price {\n          net\n          currency\n        }\n    remarks\n    token\n    id\n      }\n      errors {\n        code\n        type\n        description\n      }\n      warnings {\n        code\n        type\n        description\n      }\n    }\n  }\n}"}';

 $headers = array(
    'Authorization: Apikey 64780338-49c8-4439-7c7d-d03c2033b145',
	'Accept-Encoding: gzip, deflate, br',
	'Content-Type: application/json',
	'Accept: application/json',
	'Connection: keep-alive',
	'DNT: 1',
	'Origin: https://api.travelgatex.com'
); 

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);

echo $response;

$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

$response = json_decode($response, true);
/* echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>'; */ 
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.symrooms.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raterule = "";

$data = $response['data'];
$hotelX = $data['hotelX'];
$search = $hotelX['search'];

//options
$options = $search['options'];
for ($i=0; $i < count($options); $i++) { 
    $id = $options[$i]['id'];
    $supplierCode = $options[$i]['supplierCode'];
    $hotelCode = $options[$i]['hotelCode'];
    $hotelName = $options[$i]['hotelCode'];
    $boardCode = $options[$i]['boardCode'];
    $paymentType = $options[$i]['paymentType'];
    $status = $options[$i]['status'];
    $token = $options[$i]['token'];

    //supplements
    $supplements = $options[$i]['supplements'];
    //surcharges
    $surcharges = $options[$i]['surcharges'];
    if (count($surcharges) > 0) {
        for ($j=0; $j < count($surcharges); $j++) { 
            $chargeType = $surcharges[$j]['chargeType'];
            $scdescription = $surcharges[$j]['description'];
            $price = $surcharges['price'];
            $sccurrency = $price['currency'];
            $scbinding = $price['binding'];
            $scnet = $price['net'];
            $scgross = $price['gross'];
            $exchange = $price['exchange'];
            $excurrency = $exchange['currency'];
            $exrate = $exchange['rate'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('surcharges_search');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'chargeType' => $chargeType,
                    'scdescription' => $scdescription,
                    'sccurrency' => $sccurrency,
                    'scbinding' => $scbinding,
                    'scnet' => $scnet,
                    'scgross' => $scgross,
                    'excurrency' => $exchange,
                    'exrate' => $exrate,
                    'optionsid' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (Exception $ex) {
                echo $return;
                echo "ERRO2: " . $ex;
                echo $return;
            }

        }
    }
    //rateRules
    $rateRules = $options[$i]['rateRules'];
    if (count($rateRules) > 0) {
        for ($j=0; $j < count($rateRules); $j++) { 
            $raterule = $rateRules[$j];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('raterules_search');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'raterule' => $raterule,
                    'optionsid' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (Exception $ex) {
                echo $return;
                echo "ERRO3: " . $ex;
                echo $return;
            }
        }
    }

    $price = $options[$i]['price'];
    $net = $price['net'];
    $currency = $price['currency'];

    //cancelPolicy
    $cancelPolicy = $options[$i]['cancelPolicy'];
    $CPrefundable = $cancelPolicy['refundable'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('search');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'suppliercode' => $supplierCode,
            'hotelcode' => $hotelCode,
            'hotelname' => $hotelName,
            'boardcode' => $boardCode,
            'paymenttype' => $paymentType,
            'status' => $status,
            'token' => $token,
            'net' => $net,
            'currency' => $currency,
            'CPrefundable' => $CPrefundable
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $ex) {
        echo $return;
        echo "ERRO1: " . $ex;
        echo $return;
    }

    //cancelPenalties
    $cancelPenalties = $cancelPolicy['cancelPenalties'];
    for ($c=0; $c < count($cancelPenalties); $c++) { 
        $hoursBefore = $cancelPenalties[$c]['hoursBefore'];
        $penaltyType = $cancelPenalties[$c]['penaltyType'];
        $currency = $cancelPenalties[$c]['currency'];
        $value = $cancelPenalties[$c]['value'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('cancelPenalties_search');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'hoursBefore' => $hoursBefore,
                'penaltyType' => $penaltyType,
                'currency' => $currency,
                'value' => $value,
                'optionsid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (Exception $ex) {
            echo $return;
            echo "ERRO4: " . $ex;
            echo $return;
        }
    }

    //rooms
    $rooms = $options[$i]['rooms'];
    for ($r=0; $r < count($rooms); $r++) { 
        $occupancyRefId = $rooms[$r]['occupancyRefId'];
        $room_code = $rooms[$r]['code'];
        $description = $rooms[$r]['description'];
        $refundable = $rooms[$r]['refundable'];
        $units = $rooms[$r]['units'];

        //roomPrice
        $roomPrice = $rooms[$r]['roomPrice'];
        $price = $roomPrice['price'];
        $currency = $price['currency'];
        $binding = $price['binding'];
        $net = $price['net'];
        $gross = $price['gross'];
        $exchange = $price['exchange'];
        $excurrency = $exchange['currency'];
        $exrate = $exchange['rate'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('rooms_search');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'occupancyRefId' => $occupancyRefId,
                'room_code' => $room_code,
                'description' => $description,
                'refundable' => $refundable,
                'units' => $units,
                'currency' => $currency,
                'binding' => $binding,
                'net' => $net,
                'gross' => $gross,
                'excurrency' => $excurrency,
                'exrate' => $exrate,
                'optionsid' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (Exception $ex) {
            echo $return;
            echo "ERRO5: " . $ex;
            echo $return;
        }

        $promotions = $rooms[$r]['promotions'];
        if (count($promotions) > 0) {
            for ($l=0; $l < count($promotions); $l++) { 
                $promotionscode = $promotions[$l]['code'];
                $promotionsname = $promotions[$l]['name'];
                $promotionseffectiveDate = $promotions[$l]['effectiveDate'];
                $promotionscodeexpireDate = $promotions[$l]['expireDate'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('promotions_search');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'promotionscode' => $promotionscode,
                        'promotionsname' => $promotionsname,
                        'promotionseffectivedate' => $promotionseffectiveDate,
                        'promotionscodeexpiredate' => $promotionscodeexpireDate,
                        'occupancyRefId' => $occupancyRefId,
                        'optionsid' => $id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (Exception $ex) {
                    echo $return;
                    echo "ERRO6: " . $ex;
                    echo $return;
                }
            }
        }

        //beds
        $beds = $rooms[$r]['beds'];
        for ($k=0; $k < count($beds); $k++) { 
            $type = $beds[$k]['type'];
            $descriptionbeds = $beds[$k]['description'];
            $count = $beds[$k]['count'];
            $shared = $beds[$k]['shared'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('beds_search');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'type' => $type,
                    'descriptionbeds' => $descriptionbeds,
                    'count' => $count,
                    'shared' => $shared,
                    'occupancyRefId' => $occupancyRefId,
                    'optionsid' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (Exception $ex) {
                echo $return;
                echo "ERRO7: " . $ex;
                echo $return;
            }
        }

        $ratePlans = $rooms[$r]['ratePlans'];
        for ($y=0; $y < count($ratePlans); $y++) { 
            $ratePlanscode = $ratePlans[$y]['code'];
            $name = $ratePlans[$y]['name'];
            $effectiveDate = $ratePlans[$y]['effectiveDate'];
            $expireDate = $ratePlans[$y]['expireDate'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('rateplans_search');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'rateplanscode' => $ratePlanscode,
                    'name' => $name,
                    'effectivedate' => $effectiveDate,
                    'expiredate' => $expireDate,
                    'occupancyRefId' => $occupancyRefId,
                    'optionsid' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (Exception $ex) {
                echo $return;
                echo "ERRO8: " . $ex;
                echo $return;
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