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
echo "COMECOU READ JSON<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.airdata2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$string = file_get_contents("airplanes.json");
$response = json_decode($string, true);
foreach ($response as $key => $value) {
    $airplaneId = $value['airplaneId'];
    $airplaneIataType = $value['airplaneIataType'];
    $codeIataAirline = $value['codeIataAirline'];
    $codeIataPlaneLong = $value['codeIataPlaneLong'];
    $codeIataPlaneShort = $value['codeIataPlaneShort'];
    $codeIcaoAirline = $value['codeIcaoAirline'];
    $constructionNumber = $value['constructionNumber'];
    $deliveryDate = $value['deliveryDate'];
    $enginesCount = $value['enginesCount'];
    $enginesType = $value['enginesType'];
    $firstFlight = $value['firstFlight'];
    $hexIcaoAirplane = $value['hexIcaoAirplane'];
    $lineNumber = $value['lineNumber'];
    $modelCode = $value['modelCode'];
    $numberRegistration = $value['numberRegistration'];
    $numberTestRgistration = $value['numberTestRgistration'];
    $planeAge = $value['planeAge'];
    $planeClass = $value['planeClass'];
    $planeModel = $value['planeModel'];
    $planeOwner = $value['planeOwner'];
    $planeSeries = $value['planeSeries'];
    $planeStatus = $value['planeStatus'];
    $productionLine = $value['productionLine'];
    $registrationDate = $value['registrationDate'];
    $rolloutDate = $value['rolloutDate'];

    try {               
        $sql = new Sql($db);
        $insert = $sql->insert();
    $insert->into('airplanes');
        $insert->values(array(
            'airplaneid' => $airplaneId,
            'airplaneiatatype' => $airplaneIataType,
            'codeiataairline' => $codeIataAirline,
            'codeiataplanelong' => $codeIataPlaneLong,
            'codeiataplaneshort' => $codeIataPlaneShort,
            'codeicaoairline' => $codeIcaoAirline,
            'constructionnumber' => $constructionNumber,
            'deliverydate' => $deliveryDate,
            'enginescount' => $enginesCount,
            'enginestype' => $enginesType,
            'firstflight' => $firstFlight,
            'hexicaoairplane' => $hexIcaoAirplane,
            'linenumber' => $lineNumber,
            'modelcode' => $modelCode,
            'numberregistration' => $numberRegistration,
            'numbertestrgistration' => $numberTestRgistration,
            'planeage' => $planeAge,
            'planeclass' => $planeClass,
            'planemodel' => $planeModel,
            'planeowner' => $planeOwner,
            'planeseries' => $planeSeries,
            'planestatus' => $planeStatus,
            'productionline' => $productionLine,
            'registrationdate' => $registrationDate,
            'rolloutdate' => $rolloutDate
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO: ". $e;
        echo $return;
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>