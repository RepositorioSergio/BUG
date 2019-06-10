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
echo "COMECOU OPTIONS";
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

$sql = "SELECT circuitDetailsId, circuitCode FROM cache";
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
        $circuitDetailsId = $row->circuitDetailsId;
        $circuitCode = $row->circuitCode;
        echo $return;
        echo $circuitCode;
        echo $return;

        $sql = "SELECT departureDate, departureEndDate FROM cache_circuitDepartures WHERE circuitDetailsId='$circuitDetailsId'";
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
                $departureDate = $row->departureDate;
                $departureEndDate = $row->departureEndDate;
                echo $return;
                echo $departureEndDate;
                echo $return;

                $raw = '{   "username": "' . $abreupackagesuser . '",   "password": "' . $abreupackagespassword . '",   "language": "ES",   "circuitCode": "' . $circuitCode . '",   "fromDate": "' . $departureDate . '",   "toDate": "' . $departureEndDate . '",   "passengers": [     {       "nAdults": 2,       "nChildren": 1,       "Ages": [ 5 ]     }]}';

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
                $client->setUri($abreupackagesserviceURL . 'CircuitDetails/PackageOptions');
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
                    //die();
                }
                $response = iconv('UTF-8', 'ASCII//TRANSLIT', $response);
                echo $return;
                echo $response;
                echo $return;
                $response = json_decode($response, true);

                $config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
                $config = [
                    'driver' => $config->db->driver,
                    'database' => $config->db->database,
                    'username' => $config->db->username,
                    'password' => $config->db->password,
                    'hostname' => $config->db->hostname
                ];
                $db = new \Zend\Db\Adapter\Adapter($config);


                if ($response !== false || $response !== null) {
                    
                    $packageName = $response['packageName'];
                    if ($packageName != "") {       
                        $circuitType = $response['circuitType'];
                        $packageId = $response['packageId'];
                        $packageDepartureId = $response['packageDepartureId'];
                        $startDate = $response['startDate'];
                        $endDate = $response['endDate'];
                        $currencyCode = $response['currencyCode'];
                        echo $return;
                        echo $packageId;
                        echo $return;

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('package');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'packageName' => $packageName,
                                'circuitType' => $circuitType,
                                'packageId' => $packageId,
                                'packageDepartureId' => $packageDepartureId,
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                                'currencyCode' => $currencyCode
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO PACK: " . $e;
                            echo $return;
                        }

                        $mainElements = $response['mainElements'];
                        for ($i=0; $i < count($mainElements); $i++) { 
                            $bookingCode = $mainElements[$i]['bookingCode'];
                            $elementName = $mainElements[$i]['elementName'];
                            $elementOccupancy = $mainElements[$i]['elementOccupancy'];
                            $elementOccupancyAdultCapacity = $mainElements[$i]['elementOccupancyAdultCapacity'];
                            $elementOccupancyChildCapacity = $mainElements[$i]['elementOccupancyChildCapacity'];
                            $elementPricePerAdult = $mainElements[$i]['elementPricePerAdult'];
                            $elementCurrency = $mainElements[$i]['elementCurrency'];
                            $elementAvailability = $mainElements[$i]['elementAvailability'];
                            $elementId = $mainElements[$i]['elementId'];
                            echo $return;
                            echo $bookingCode;
                            echo $return;

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('package_mainElements');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'bookingCode' => $bookingCode,
                                    'elementName' => $elementName,
                                    'elementOccupancy' => $elementOccupancy,
                                    'elementOccupancyAdultCapacity' => $elementOccupancyAdultCapacity,
                                    'elementOccupancyChildCapacity' => $elementOccupancyChildCapacity,
                                    'elementPricePerAdult' => $elementPricePerAdult,
                                    'elementCurrency' => $elementCurrency,
                                    'elementAvailability' => $elementAvailability,
                                    'elementId' => $elementId,
                                    'packageId' => $packageId
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();

                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO MAIN: " . $e;
                                echo $return;
                            }

                            //elementPricePerChildren
                            $elementPricePerChildren = $mainElements[$i]['elementPricePerChildren'];
                            if (count($elementPricePerChildren) > 0) {
                                for ($j=0; $j < count($elementPricePerChildren); $j++) { 
                                    $childrenAge = $elementPricePerChildren[$j]['childrenAge'];
                                    $pricePerChildren = $elementPricePerChildren[$j]['pricePerChildren'];

                                    try {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('package_PricePerChildren');
                                        $insert->values(array(
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'childrenAge' => $childrenAge,
                                            'pricePerChildren' => $pricePerChildren,
                                            'elementId' => $elementId
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "ERRO CHILD: " . $e;
                                        echo $return;
                                    }

                                }
                            }

                            //mandatoryServices
                            $mandatoryServices = $mainElements[$i]['mandatoryServices'];
                            if (count($mandatoryServices) > 0) {
                                for ($k=0; $k < count($mandatoryServices); $k++) { 
                                    $mandatoryServiceName = $mandatoryServices[$k]['mandatoryServiceName'];
                                    $mandatoryServiceAvailability = $mandatoryServices[$k]['mandatoryServiceAvailability'];

                                    try {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('package_mandatoryServices');
                                        $insert->values(array(
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'mandatoryServiceName' => $mandatoryServiceName,
                                            'mandatoryServiceAvailability' => $mandatoryServiceAvailability,
                                            'elementId' => $elementId
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "ERRO SERVICE: " . $e;
                                        echo $return;
                                    }

                                    $mandatoryOptions = $mandatoryServices[$k]['mandatoryOptions'];
                                    for ($s=0; $s < count($mandatoryOptions); $s++) { 
                                        $mandatoryOptionName = $mandatoryOptions[$s]['mandatoryOptionName'];
                                        $mandatoryOptionType = $mandatoryOptions[$s]['mandatoryOptionType'];

                                        try {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('package_mandatoryOptions');
                                            $insert->values(array(
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'mandatoryOptionName' => $mandatoryOptionName,
                                                'mandatoryOptionType' => $mandatoryOptionType,
                                                'elementId' => $elementId
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "ERRO OPTIONS: " . $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
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