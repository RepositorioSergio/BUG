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
echo "COMECOU ITINERARY STEPS<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.costa.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = 'itinerary_steps.xml';
$response = file_get_contents($filename);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$CostaItineraryCatalog = $inputDoc->getElementsByTagName("CostaItineraryCatalog");
if ($CostaItineraryCatalog->length > 0) {
    $Destination = $CostaItineraryCatalog->item(0)->getElementsByTagName("Destination");
    if ($Destination->length > 0) {
        for ($i=0; $i < $Destination->length; $i++) { 
            $Code = $Destination->item($i)->getAttribute("Code");
            $DisplayName = $Destination->item($i)->getAttribute("DisplayName");
            $Itinerary = $Destination->item($i)->getElementsByTagName("Itinerary");
            if ($Itinerary->length > 0) {
                for ($j=0; $j < $Itinerary->length; $j++) { 
                    $ItineraryCode = $Itinerary->item($j)->getAttribute("Code");
                    $ItineraryDisplayName = $Itinerary->item($j)->getAttribute("DisplayName");
                    $Steps = $Itinerary->item($j)->getElementsByTagName("Steps");
                    if ($Steps->length > 0) {
                        $Step = $Steps->item(0)->getElementsByTagName("Step");
                        if ($Step->length > 0) {
                            for ($k=0; $k < $Step->length; $k++) { 
                                $CodeDeparturePort = $Step->item($k)->getAttribute("CodeDeparturePort");
                                $DeparturePortDescription = $Step->item($k)->getAttribute("DeparturePortDescription");
                                $CodeArrivelPort = $Step->item($k)->getAttribute("CodeArrivelPort");
                                $ArrivelPortDescrption = $Step->item($k)->getAttribute("ArrivelPortDescrption");
                                $DepartureTime = $Step->item($k)->getAttribute("DepartureTime");
                                $ArrivalTime = $Step->item($k)->getAttribute("ArrivalTime");
                                $DepartureDay = $Step->item($k)->getAttribute("DepartureDay");
                                $ArrivalDay = $Step->item($k)->getAttribute("ArrivalDay");
    
                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('itinerary_steps');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'codedepartureport' => $CodeDeparturePort,
                                        'departureportdescription' => $DeparturePortDescription,
                                        'codearrivelport' => $CodeArrivelPort,
                                        'arrivelportdescription' => $ArrivelPortDescrption,
                                        'departuretime' => $DepartureTime,
                                        'arrivaltime' => $ArrivalTime,
                                        'departureday' => $DepartureDay,
                                        'arrivalday' => $ArrivalDay,
                                        'itinerarycode' => $ItineraryCode,
                                        'destinationcode' => $Code
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
echo '<br/>Done';
?>