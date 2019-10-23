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
echo "COMECOU PREBOOKING";
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
$sql = "select value from settings where name='enableabreupackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_abreu = $affiliate_id;
} else {
    $affiliate_id_abreu = 0;
}
echo "<br/> affiliate_id_abreu " . $affiliate_id_abreu;
$sql = "select value from settings where name='abreupackagesuser' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagesuser = $row_settings['value'];
}
echo "<br/> abreupackagesuser " . $abreupackagesuser;
$sql = "select value from settings where name='abreupackagespassword' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagespassword = base64_decode($row_settings['value']);
}
echo "<br/> abreupackagespassword " . $abreupackagespassword;
$sql = "select value from settings where name='abreupackagesserviceURL' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $abreupackagesserviceURL = $row_settings['value'];
}
echo "<br/> abreupackagesserviceURL " . $abreupackagesserviceURL;
$db->getDriver()
    ->getConnection()
    ->disconnect();
    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

/* $sql = "SELECT packageId, packageDepartureId, startDate, currencyCode FROM package";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}


$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $packageId = $row->packageId;
        $packageDepartureId = $row->packageDepartureId;
        $startDate = $row->startDate;
        $currencyCode = $row->currencyCode;
        echo $return;
        echo $packageDepartureId;
        echo $return;

        $sql = "SELECT bookingCode, elementId, elementOccupancyAdultCapacity, elementOccupancyChildCapacity FROM package_mainElements WHERE packageId='$packageId'";
        $statement = $db->createStatement($sql);
        try {
            $statement->prepare();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }


        $result = $statement->execute();
        $result->buffer();
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new ResultSet();
            $resultSet->initialize($result);
            foreach ($resultSet as $row) {
                $bookingCode = $row->bookingCode;
                $elementId = $row->elementId;
                echo $return;
                echo $elementId;
                echo $return; */
                $elementId = 281644;
                $elementId2 = 252507;
$packageId = 28891;
$currencyCode = "USD";
$startDate = "2019-11-08";
$packageDepartureId = 661692;
$bookingCode = "UNKZ0SSaPGjyfPwQnwudwqaYGkS4vZb0GR3Gv1FnNPKh0OlKdaBrFqWuYmtr8Q9OCLbzLPrC3icJ3ixdb1XZSh7T4lRl3WhLwT6r+4fC5Io6PJyhg1myI5iBpZbIu8YAV9C0kOrvATKWsDq4dfrKcehLOnhBZ66k6ZmMHDDLxNf3sVFJ13yIbYDUmAVuSswU4RTGqLuSe77CGe/grmTCm91tv07tHNyKTRrTRimdNCF6PuxN7b5G5u8RkveSKXZE9zBDbyub0Cj/y88K9vH71ZKR856uBFt0IP+H94m7YlAYBd0BstrYQRkxbInTFiM=";
$bookingCode2 = "phfa6ij1I1TWgKTqP9IEgw+VTSQkA1bRtBWZ9kVM8gTkOdlaaFbn0QCeyoI3/WoIQoznmj8drAlOGvFoT37g6YxYssDtmlfectSkUe3LrpO+l9tAxiiZJgc3mOsuHi1krlNOi6vazkftxj9MQJB9AfFFOnEEmLNWsnXQcOhaiHkssXn1KvSThhfL97Dl7ja1d0kwU4sZvEvFa/q6+bpNyq2IQuGeFM/wN8NT8tbuAFa6EtAUEQyRL136jznqmUYVzgB+uPy4mg0nm7g5FDepcwBB5osE8/D0FlEfMT8O/7SrVpp7XetcJlKJ5/8avh8vVJu8OoWkLlQoUF74NVsg";

                $raw = '{   "username": "' . $abreupackagesuser . '",   "password": "' . $abreupackagespassword . '",   "language": "ES",   "passengers": [ {       "nAdults": 3, "nChildren": 0,    "elementId": ' . $elementId2 . '     }  
            ],   "packageId": ' . $packageId . ',   "departureId": ' . $packageDepartureId . ',   "departureDate": "' . $startDate . '",   "currencyCode": "' . $currencyCode . '",   "elements": [  {       "bookingCode": "' . $bookingCode2 . '",       "elementId": ' . $elementId2 . ',       "elementQuantity": 1,       "elementNAdults": 3,       "elementNChildren": 0     }  
            ] }';
            echo $return;
                echo $raw;
                echo $return;

                $client = new Client();
                $client->setOptions(array(
                    'timeout' => 100,
                    'sslverifypeer' => false,
                    'sslverifyhost' => false
                ));
                $client->setHeaders(array(
                    'Accept-Encoding' => 'gzip,deflate',
                    'X-Powered-By' => 'Zend Framework',
                    'Content-Length' => strlen($raw),
                    'Content-Type' => 'application/json;charset=utf-8'
                ));
                $client->setUri($abreupackagesserviceURL . 'Booking/Preview');
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
                $response = iconv('UTF-8', 'ASCII//TRANSLIT', $response);
                echo $return;
                echo $response;
                echo $return; 

                $response = json_decode($response, true);
                if ($response === false || $response === null) {
                    echo $return;
                    echo "NOT DECODE";
                    echo $return;
                }
die();
                $config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
                $config = [
                    'driver' => $config->db->driver,
                    'database' => $config->db->database,
                    'username' => $config->db->username,
                    'password' => $config->db->password,
                    'hostname' => $config->db->hostname
                ];
                $db = new \Zend\Db\Adapter\Adapter($config);


                
                    $amount = $response['amount'];
                    $packageName = $response['packageName'];
                if ($packageName != "") {
                    $bookingStartDate = $response['bookingStartDate'];
                    echo $return;
                    echo $packageName;
                    echo $return;

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('prebooking');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'amount' => $amount,
                            'packageName' => $packageName,
                            'bookingStartDate' => $bookingStartDate
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO PRE: " . $e;
                        echo $return;
                    } 

                    //bookedElements
                    $bookedElements = $response['bookedElements'];
                    for ($i=0; $i < count($bookedElements); $i++) { 
                        $elementId = $bookedElements[$i]['elementId'];
                        $elementAmountPerAdult = $bookedElements[$i]['elementAmountPerAdult'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('bookedElements');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'elementId' => $elementId,
                                'elementAmountPerAdult' => $elementAmountPerAdult,
                                'packageName' => $packageName
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO BOOK: " . $e;
                            echo $return;
                        }

                        $elementAmountPerChildren = $bookedElements[$i]['elementAmountPerChildren'];
                        if (count($elementAmountPerChildren) > 0) {
                            for ($iAux=0; $iAux < count($elementAmountPerChildren); $iAux++) { 
                                $childrenAge = $elementAmountPerChildren[$iAux]['childrenAge'];
                                $amountPerChildren = $elementAmountPerChildren[$iAux]['amountPerChildren'];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('prebooking_amountPerChildren');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'childrenAge' => $childrenAge,
                                        'amountPerChildren' => $amountPerChildren,
                                        'elementId' => $elementId
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO BOOK: " . $e;
                                    echo $return;
                                }

                            }
                        }
                    }

                    //bookedServices
                    $bookedServices = $response['bookedServices'];
                    for ($j=0; $j < count($bookedServices); $j++) { 
                        $serviceName = $bookedServices[$j]['serviceName'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('bookedServices');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'serviceName' => $serviceName,
                                'packageName' => $packageName
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO BOOKED: " . $e;
                            echo $return;
                        }

                        $bookedOptions = $bookedServices[$j]['bookedOptions'];
                        for ($jAux=0; $jAux < count($bookedOptions); $jAux++) { 
                            $quantity = $bookedOptions[$jAux]['quantity'];
                            $nAdults = $bookedOptions[$jAux]['nAdults'];
                            $nChildren = $bookedOptions[$jAux]['nChildren'];
                            $fromDate = $bookedOptions[$jAux]['fromDate'];
                            $toDate = $bookedOptions[$jAux]['toDate'];
                            $amount = $bookedOptions[$jAux]['amount'];
                            $currencyCode = $bookedOptions[$jAux]['currencyCode'];
                            $status = $bookedOptions[$jAux]['status'];
                            $optionName = $bookedOptions[$jAux]['optionName'];

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('bookedOptions');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'quantity' => $quantity,
                                    'nAdults' => $nAdults,
                                    'nChildren' => $nChildren,
                                    'fromDate' => $fromDate,
                                    'toDate' => $toDate,
                                    'amount' => $amount,
                                    'currencyCode' => $currencyCode,
                                    'status' => $status,
                                    'optionName' => $optionName,
                                    'serviceName' => $serviceName
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO BOOKEDOPT: " . $e;
                                echo $return;
                            }

                        }
                    }

                    //cancellationCharges
                    $cancellationCharges = $response['cancellationCharges'];
                    for ($k=0; $k < count($cancellationCharges); $k++) { 
                        $cancellationCharges2 = $cancellationCharges[$k]['cancellationCharges'];
                        $chargeType = $cancellationCharges[$k]['chargeType'];
                        $days = $cancellationCharges[$k]['days'];
                        $cancellationPolicyCalculateUsing = $cancellationCharges[$k]['cancellationPolicyCalculateUsing'];
                        $cancellationPolicyApplication = $cancellationCharges[$k]['cancellationPolicyApplication'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('cancellationCharges');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'cancellationCharges' => $cancellationCharges2,
                                'chargeType' => $chargeType,
                                'days' => $days,
                                'cancellationPolicyCalculateUsing' => $cancellationPolicyCalculateUsing,
                                'cancellationPolicyApplication' => $cancellationPolicyApplication,
                                'packageName' => $packageName
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO CANCEL: " . $e;
                            echo $return;
                        }

                    }

                }
/*             }
        }

    }
} */ 

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>