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
echo "COMECOU HOTEL AVAILABILITY";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.comming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$passuser = "COSTAMAR:COSTAMAR";
$auth = base64_encode($passuser);

$raw = '{
    "AvailabilityId": "",
    "Language": "ES",
    "CurrencyCode": "EUR",
    "Customer": "",
    "FromDate": "2019-09-10",
    "ToDate": "2019-09-17",
    "Hotels": [
        "H80863"
    ],
    "Areas": [],
    "Rooms": [
        {
            "RoomCandidateId": "1",
            "Paxes": [
                {
                    "PaxType": "Adult",
                    "Age": 30
                },
                {
                    "PaxType": "Adult",
                    "Age": 30
                }
            ]
        },
        {
            "RoomCandidateId": "2",
            "Paxes": [
                {
                    "PaxType": "Adult",
                    "Age": 30
                },
                {
                    "PaxType": "Adult",
                    "Age": 30
                },
                {
                    "PaxType": "Child",
                    "Age": 8
                },
                {
                    "PaxType": "Enfant",
                    "Age": 1
                }
            ]
        }
    ],
    "Skip": 0,
    "Limit": 50,
    "Filter": {
        "MinPrice": 0,
        "MaxPrice": 0,
        "PackageRates": "All",
        "ResidentRates": "Yes",
        "SeniorRates": "No",
        "NonRefundableRates": "All"
    },
    "OrderBy": {
        "Direction": "Ascending",
        "Field": "Price"
    }
}';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: application/json",
    "Accept: application/json",
    "Authorization: Basic " . $auth,
    "Content-length: " . strlen($raw)
));
$client->setUri('http://services-pre.bedbank.coming2.com/hotel-api/api/Hotel/Availability');
$client->setMethod('POST');
$client->setRawBody($raw);
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

$response = json_decode($response, true);

echo "<xmp>";
var_dump($response);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.comming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

for ($i=0; $i < count($response); $i++) { 
    $RequestId = $response[$i]['RequestId'];
    $Language = $response[$i]['Language'];
    $Market = $response[$i]['Market'];
    $Nationality = $response[$i]['Nationality'];
    $RequestUser = $response[$i]['RequestUser'];
    $Customer = $response[$i]['Customer'];
    $Country = $response[$i]['Country'];
    $FromDate = $response[$i]['FromDate'];
    $ToDate = $response[$i]['ToDate'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('hotelavailability');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'RequestId' => $RequestId,
            'Language' => $Language,
            'Market' => $Market,
            'Nationality' => $Nationality,
            'RequestUser' => $RequestUser,
            'Customer' => $Customer,
            'Country' => $Country,
            'FromDate' => $FromDate,
            'ToDate' => $ToDate
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error: " . $e;
        echo $return;
    }

    $Hotels = $response[$i]['Hotels'];
    for ($j=0; $j < count($Hotels); $j++) { 
        $Code = $Hotels[$j]['Code'];
        $Name = $Hotels[$j]['Name'];
        $CategoryCode = $Hotels[$j]['CategoryCode'];
        $CategoryName = $Hotels[$j]['CategoryName'];
        $Description = $Hotels[$j]['Description'];
        $Latitude = $Hotels[$j]['Latitude'];
        $Longitude = $Hotels[$j]['Longitude'];
        $Address = $Hotels[$j]['Address'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hotelavailability_Hotels');
            $insert->values(array(
                'Code' => $Code,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'Name' => $Name,
                'CategoryCode' => $CategoryCode,
                'CategoryName' => $CategoryName,
                'Description' => $Description,
                'Latitude' => $Latitude,
                'Longitude' => $Longitude,
                'Address' => $Address,
                'RequestId' => $RequestId
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error IMG: " . $e;
            echo $return;
        }

        $MealPlans = $Hotels[$j]['MealPlans'];
        for ($jAux=0; $jAux < count($MealPlans); $jAux++) { 
            $CodeMealPlans = $MealPlans[$jAux]['Code'];
            $Name = $MealPlans[$jAux]['Name'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hotelavailability_MealPlans');
                $insert->values(array(
                    'Code' => $CodeMealPlans,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Name' => $Name,
                    'CodeHotels' => $Code
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error MEAL: " . $e;
                echo $return;
            }

            $Options = $MealPlans[$jAux]['Options'];
            for ($jAux2=0; $jAux2 < count($Options); $jAux2++) { 
                $RoomCandidateId = $Options[$jAux2]['RoomCandidateId'];
                $Status = $Options[$jAux2]['Status'];
                $Adults = $Options[$jAux2]['Adults'];
                $Childs = $Options[$jAux2]['Childs'];
                $Enfants = $Options[$jAux2]['Enfants'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hotelavailability_Options');
                    $insert->values(array(
                        'RoomCandidateId' => $RoomCandidateId,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Status' => $Status,
                        'Adults' => $Adults,
                        'Childs' => $Childs,
                        'Enfants' => $Enfants,
                        'CodeMealPlans' => $CodeMealPlans
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error OPT: " . $e;
                    echo $return;
                }

                $Rooms = $Options[$jAux2]['Rooms'];
                for ($jAux3=0; $jAux3 < count($Rooms); $jAux3++) { 
                    $Id = $Rooms[$jAux3]['Id'];
                    $Code = $Rooms[$jAux3]['Code'];
                    $Name = $Rooms[$jAux3]['Name'];
                    $RateCode = $Rooms[$jAux3]['RateCode'];
                    $RateName = $Rooms[$jAux3]['RateName'];
                    $NonRefundable = $Rooms[$jAux3]['NonRefundable'];
                    $Package = $Rooms[$jAux3]['Package'];
                    $Senior = $Rooms[$jAux3]['Senior'];
                    $Residents = $Rooms[$jAux3]['Residents'];
                    $Remarks = $Rooms[$jAux3]['Remarks'];
                    $Price = $Rooms[$jAux3]['Price'];
                    if (count($Price) > 0) {
                        $CurrencyCode = $Price['CurrencyCode'];
                        $Amount = $Price['Amount'];
                        $Commission = $Price['Commission'];
                        $Binding = $Price['Binding'];
                    } else {
                        $CurrencyCode = "";
                        $Amount = "";
                        $Commission = "";
                        $Binding = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hotelavailability_Rooms');
                        $insert->values(array(
                            'Id' => $Id,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Code' => $Code,
                            'Name' => $Name,
                            'RateCode' => $RateCode,
                            'RateName' => $RateName,
                            'NonRefundable' => $NonRefundable,
                            'Package' => $Package,
                            'Senior' => $Senior,
                            'Residents' => $Residents,
                            'Remarks' => $Remarks,
                            'CurrencyCode' => $CurrencyCode,
                            'Amount' => $Amount,
                            'Commission' => $Commission,
                            'Binding' => $Binding,
                            'RoomCandidateId' => $RoomCandidateId
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error ROOM: " . $e;
                        echo $return;
                    }

                    $CancelPenalties = $Rooms[$jAux3]['CancelPenalties'];
                    for ($iAux4=0; $iAux4 < count($CancelPenalties); $iAux4++) { 
                        $HoursBefore = $CancelPenalties[$jAux4]['HoursBefore'];
                        $Description = $CancelPenalties[$jAux4]['Description'];

                        $Penalty = $CancelPenalties[$jAux4]['Penalty'];
                        if (count($Penalty) > 0) {
                            $PenaltyType = $Penalty['PenaltyType'];
                            $CurrencyCode = $Penalty['CurrencyCode'];
                            $Value = $Penalty['Value'];
                            $IsNetPrice = $Penalty['IsNetPrice'];
                        } else {
                            $PenaltyType = "";
                            $CurrencyCode = "";
                            $Value = "";
                            $IsNetPrice = "";
                        }

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hotelavailability_CancelPenalties');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'HoursBefore' => $HoursBefore,
                                'Description' => $Description,
                                'PenaltyType' => $PenaltyType,
                                'CurrencyCode' => $CurrencyCode,
                                'Value' => $Value,
                                'IsNetPrice' => $IsNetPrice,
                                'IdRooms' => $Id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error ROOM: " . $e;
                            echo $return;
                        }
                        
                    }
                    
                }
            }

        }
    }

    $Areas = $response[$i]['Areas'];
    for ($k=0; $k < count($Areas); $k++) { 
        $Type = $Areas[$k]['Type'];
        $CodeAreas = $Areas[$k]['Code'];
        $Name = $Areas[$k]['Name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hotelAreas');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'Code' => $CodeAreas,
                'Type' => $Type,
                'Name' => $Name,
                'CodeHotel' => $Code
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error AREAS: " . $e;
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