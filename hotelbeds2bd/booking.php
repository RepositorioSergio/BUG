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
echo "COMECOU HOTELBEDS BOOKINGLIST";
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
$config = new \Zend\Config\Config(include '../config/autoload/global.hotelbeds.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = '{
  "clientReference": "string",
  "holder": {
    "email": "paulo@corp.bug-software.com",
    "name": "Paulo",
    "phone": "3057484914",
    "surname": "Andrade",
    "title": "MR",
    "type": "ADULT"
  },
  "language": "en",
  "remark": "some remark",
  "transfers": [
    {
      "detail": {
        "arrivalFlightNumber": "XR1234",
        "arrivalShipName": "TAP",
        "arrivalTrainInfo": {
          "trainCompanyName": "TAP",
          "trainNumber": "XR1234"
        },
        "departureFlightNumber": "XR1235",
        "departureShipName": "TAP",
        "departureTrainInfo": {
          "trainCompanyName": "TAP",
          "trainNumber": "XR12345"
        }
      },
      "rateKey": "DEPARTURE|ATLAS|1523|IATA|PMI|2019-10-28T09:55|2~1~0|3|108473|TAB-PVT-PRM-X|108473|TAB-PVT-PRM-X|1|PRVT|X|CR|PRM|53.25|CPASTILLA|AEROPUERTO|13171|2246|PMI",
      "transferDetails": [
        {
          "code": "XR1234",
          "companyName": "TAP",
          "direction": "ARRIVAL",
          "type": "FLIGHT"
        }
      ]
    }
  ],
  "welcomeMessage": "string"
}';
// echo $return;
// echo "RAW: " . $raw;
// echo $return;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.test.hotelbeds.com/transfer-api/1.0/booking');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Api-key: qz8j9xgymx97tmd5srx94mru",
    "X-Signature: " . hash("sha256", "qz8j9xgymx97tmd5srx94mru" . time()),
    "Content-Type: application/json",
    "Accept: application/json",
    "Accept-Encoding: gzip",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);

if ($error != "") {
    echo $return;
    echo "ERRO: " . $error;
    echo $return;
} else {
    echo $return;
    echo "NAO TEM ERROS.";
    echo $return;
}

curl_close($ch);

echo $return;
echo $response;
echo $return;

$response = json_decode($response, true);
if ($response === false || $response === null) {
    echo $return;
    echo "NOT DECODE";
    echo $return;
}

if (json_last_error() == 0) {
    echo '- Nao houve erro! O parsing foi perfeito';
} else {
    echo 'Erro!<br/>';
    switch (json_last_error()) {
        
        case JSON_ERROR_DEPTH:
            echo ' - profundidade maxima excedida';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - state mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Caracter de controle encontrado';
            break;
        case JSON_ERROR_SYNTAX:
            echo ' - Erro de sintaxe! String JSON mal-formada!';
            break;
        case JSON_ERROR_UTF8:
            echo ' - Erro na codificação UTF-8';
            break;
        default:
            echo ' – Erro desconhecido';
            break;
    }
}

echo "<xmp>";
var_dump($response);
echo "</xmp>";


$config = new \Zend\Config\Config(include '../config/autoload/global.hotelbeds.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$bookings = $response['bookings'];

for ($i = 0; $i < count($bookings); $i ++) {
    $reference = $bookings[$i]['reference'];
    $bookingFileId = $bookings[$i]['bookingFileId'];
    $creationDate = $bookings[$i]['creationDate'];
    $status = $bookings[$i]['status'];
    $clientReference = $bookings[$i]['clientReference'];
    $remark = $bookings[$i]['remark'];
    $totalAmount = $bookings[$i]['totalAmount'];
    echo $return;
    echo "totalAmount: " . $totalAmount;
    echo $return;
    $totalNetAmount = $bookings[$i]['totalNetAmount'];
    $pendingAmount = $bookings[$i]['pendingAmount'];
    $currency = $bookings[$i]['currency'];
    //modificationsPolicies
    $modificationsPolicies = $bookings[$i]['modificationsPolicies'];
    $cancellation = $modificationsPolicies['cancellation'];
    $modification = $modificationsPolicies['modification'];
    //holder
    $holder = $bookings[$i]['holder'];
    $title = $holder['title'];
    $name = $holder['name'];
    $surname = $holder['surname'];
    $email = $holder['email'];
    $phone = $holder['phone'];
    //invoiceCompany
    $invoiceCompany = $bookings[$i]['invoiceCompany'];
    $code = $invoiceCompany['code'];
    //supplier
    $supplier = $bookings[$i]['supplier'];
    $namesupplier = $supplier['name'];
    $vatNumber = $supplier['vatNumber'];
    
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('bookings');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'reference' => $reference,
            'bookingFileId' => $bookingFileId,
            'creationDate' => $creationDate,
            'status' => $status,
            'clientReference' => $clientReference,
            'remark' => $remark,
            'totalAmount' => $totalAmount,
            'totalNetAmount' => $totalNetAmount,
            'pendingAmount' => $pendingAmount,
            'currency' => $currency,
            'cancellation' => $cancellation,
            'modification' => $modification,
            'title' => $title,
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'phone' => $phone,
            'code' => $code,
            'namesupplier' => $namesupplier,
            'vatNumber' => $vatNumber
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error 1: " . $e;
        echo $return;
    }

    //transfers
    $transfers = $bookings[$i]['transfers'];
    for ($j=0; $j < count($transfers); $j++) { 
        $id = $transfers[$j]['id'];
        $status = $transfers[$j]['status'];
        $direction = $transfers[$j]['direction'];
        $transferType = $transfers[$j]['transferType'];
        $factsheetId = $transfers[$j]['factsheetId'];
        $arrivalFlightNumber = $transfers[$j]['arrivalFlightNumber'];
        $departureFlightNumber = $transfers[$j]['departureFlightNumber'];
        $arrivalShipName = $transfers[$j]['arrivalShipName'];
        $departureShipName = $transfers[$j]['departureShipName'];
        $arrivalTrainInfo = $transfers[$j]['arrivalTrainInfo'];
        $departureTrainInfo = $transfers[$j]['departureTrainInfo'];
        $sourceMarketEmergencyNumber = $transfers[$j]['sourceMarketEmergencyNumber'];
        //vehicle
        $vehicle = $transfers[$j]['vehicle'];
        $codevehicle = $vehicle['code'];
        $namevehicle = $vehicle['name'];
        //category
        $category = $transfers[$j]['category'];
        $codecategory = $category['code'];
        $namecategory = $category['name'];
        //pickupInformation
        $pickupInformation = $transfers[$j]['pickupInformation'];
        $date = $pickupInformation['date'];
        $time = $pickupInformation['time'];
        //from
        $from = $pickupInformation['from'];
        $codefrom = $from['code'];
        $descriptionfrom = $from['description'];
        $typefrom = $from['type'];
        //to
        $to = $pickupInformation['to'];
        $codeto = $to['code'];
        $descriptionto = $to['description'];
        $typeto = $to['type'];
        //pickup
        $pickup = $pickupInformation['pickup'];
        $address = $pickup['address'];
        $number = $pickup['number'];
        $town = $pickup['town'];
        $zip = $pickup['zip'];
        $description = $pickup['description'];
        $altitude = $pickup['altitude'];
        $latitude = $pickup['latitude'];
        $longitude = $pickup['longitude'];
        $pickupId = $pickup['pickupId'];
        $stopName = $pickup['stopName'];
        $image = $pickup['image'];
        $checkPickup = $pickup['checkPickup'];
        $mustCheckPickupTime = $checkPickup['mustCheckPickupTime'];
        $url = $checkPickup['url'];
        $hoursBeforeConsulting = $checkPickup['hoursBeforeConsulting'];

        $price = $transfers[$j]['price'];
        $totalAmount = $price['totalAmount'];
        $netAmount = $price['netAmount'];
        $currencyId = $price['currencyId'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('transfers');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idTransfers' => $id,
                'status' => $status,
                'direction' => $direction,
                'transferType' => $transferType,
                'factsheetId' => $factsheetId,
                'arrivalFlightNumber' => $arrivalFlightNumber,
                'departureFlightNumber' => $departureFlightNumber,
                'arrivalShipName' => $arrivalShipName,
                'departureShipName' => $departureShipName,
                'arrivalTrainInfo' => $arrivalTrainInfo,
                'departureTrainInfo' => $departureTrainInfo,
                'sourceMarketEmergencyNumber' => $sourceMarketEmergencyNumber,
                'codevehicle' => $codevehicle,
                'namevehicle' => $namevehicle,
                'codecategory' => $codecategory,
                'namecategory' => $namecategory,
                'date' => $date,
                'time' => $time,
                'codefrom' => $codefrom,
                'descriptionfrom' => $descriptionfrom,
                'typefrom' => $typefrom,
                'codeto' => $codeto,
                'descriptionto' => $descriptionto,
                'typeto' => $typeto,
                'address' => $address,
                'number' => $number,
                'town' => $town,
                'zip' => $zip,
                'description' => $description,
                'altitude' => $altitude,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'pickupId' => $pickupId,
                'stopName' => $stopName,
                'image' => $image,
                'mustCheckPickupTime' => $mustCheckPickupTime,
                'url' => $url,
                'hoursBeforeConsulting' => $hoursBeforeConsulting,
                'totalAmount' => $totalAmount,
                'netAmount' => $netAmount,
                'currencyId' => $currencyId
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 12: " . $e;
            echo $return;
        }


        //content
        $content = $transfers[$j]['content'];
        //vehicle
        $vehicle = $content['vehicle'];
        $codevehicle = $vehicle['code'];
        $namevehicle = $vehicle['name'];
        //category
        $category = $content['category'];
        $codecategory = $category['code'];
        $namecategory = $category['name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('content_transfers');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codevehicle' => $codevehicle,
                'namevehicle' => $namevehicle,
                'codecategory' => $codecategory,
                'namecategory' => $namecategory,
                'idTransfers' => $id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 2: " . $e;
            echo $return;
        }

        //images
        $images = $content['images'];
        for ($j=0; $j < count($images); $j++) { 
            $url = $images[$j]['url'];
            $type = $images[$j]['type'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('images_transfers');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'url' => $url,
                    'type' => $type,
                    'idTransfers' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 3: " . $e;
                echo $return;
            }
        }
        //transferDetailInfo
        $transferDetailInfo = $content['transferDetailInfo'];
        for ($k=0; $k < count($transferDetailInfo); $k++) { 
            $idtransfer = $transferDetailInfo[$k]['id'];
            $name = $transferDetailInfo[$k]['name'];
            $description = $transferDetailInfo[$k]['description'];
            $type = $transferDetailInfo[$k]['type'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('transferDetailInfo_transfers');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'idtransferDetail' => $idtransfer,
                    'name' => $name,
                    'description' => $description,
                    'type' => $type,
                    'idTransfers' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 4: " . $e;
                echo $return;
            }
        }
        //customerTransferTimeInfo
        $customerTransferTimeInfo = $content['customerTransferTimeInfo'];
        for ($l=0; $l < count($customerTransferTimeInfo); $l++) { 
            $value = $customerTransferTimeInfo[$l]['value'];
            $type = $customerTransferTimeInfo[$l]['type'];
            $metric = $customerTransferTimeInfo[$l]['metric'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('customerTransferTimeInfo_transfers');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'value' => $value,
                    'type' => $type,
                    'metric' => $metric,
                    'idTransfers' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 5: " . $e;
                echo $return;
            }
        }
        //supplierTransferTimeInfo
        $supplierTransferTimeInfo = $content['supplierTransferTimeInfo'];
        for ($m=0; $m < count($supplierTransferTimeInfo); $m++) { 
            $value = $supplierTransferTimeInfo[$m]['value'];
            $type = $supplierTransferTimeInfo[$m]['type'];
            $metric = $supplierTransferTimeInfo[$m]['metric'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('supplierTransferTimeInfo_transfers');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'value' => $value,
                    'type' => $type,
                    'metric' => $metric,
                    'idTransfers' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 6: " . $e;
                echo $return;
            }
        }

        //transferRemarks
        $transferRemarks = $content['transferRemarks'];
        for ($r=0; $r < count($transferRemarks); $r++) { 
            $type = $transferRemarks[$r]['type'];
            $description = $transferRemarks[$r]['description'];
            $mandatory = $transferRemarks[$r]['mandatory'];
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('transferRemarks_transfers');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'type' => $type,
                    'description' => $description,
                    'mandatory' => $mandatory,
                    'idTransfers' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 7: " . $e;
                echo $return;
            }
        }

        //paxes
        $paxes = $transfers[$j]['paxes'];
        for ($c=0; $c < count($paxes); $c++) { 
            $type = $paxes[$c]['type'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('paxes_transfers');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'type' => $type,
                    'idTransfers' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 11: " . $e;
                echo $return;
            }
        }

        //cancellationPolicies
        $cancellationPolicies = $transfers[$j]['cancellationPolicies'];
        for ($s=0; $s < count($cancellationPolicies); $s++) { 
            $amount = $cancellationPolicies[$s]['amount'];
            $from = $cancellationPolicies[$s]['from'];
            $currencyId = $cancellationPolicies[$s]['currencyId'];
    
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('cancellationPolicies_transfers');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'amount' => $amount,
                    'from' => $from,
                    'currencyId' => $currencyId,
                    'idTransfers' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 8: " . $e;
                echo $return;
            }
        }

        //transferDetails
        $transferDetails = $transfers[$j]['transferDetails'];
        for ($y=0; $y < count($transferDetails); $y++) { 
            $type = $transferDetails[$y]['type'];
            $direction = $transferDetails[$y]['direction'];
            $code = $transferDetails[$y]['code'];
            $companyName = $transferDetails[$y]['companyName'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('transferDetails');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'type' => $type,
                    'direction' => $direction,
                    'code' => $code,
                    'companyName' => $companyName,
                    'idTransfers' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 10: " . $e;
                echo $return;
            }
        }

        //links
        $links = $transfers[$j]['links'];
        for ($x=0; $x < count($links); $x++) { 
            $rel = $links[$x]['rel'];
            $href = $links[$x]['href'];
            $method = $links[$x]['method'];
    
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('links_transfers');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'rel' => $rel,
                    'href' => $href,
                    'method' => $method,
                    'idTransfers' => $id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 9: " . $e;
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