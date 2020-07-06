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

$string = file_get_contents("benchmark_airlines.json");
$response = json_decode($string, true);
foreach ($response as $key => $value) {
    $benchmarkAirlinesId = $value['benchmarkAirlinesId'];
    $ageFleet = $value['ageFleet'];
    $codeIataAirline = $value['codeIataAirline'];
    $codeIcaoAirline = $value['codeIcaoAirline'];
    $codeIso2Country = $value['codeIso2Country'];
    $facebook = $value['facebook'];
    $founding = $value['founding'];
    $nameAirline = $value['nameAirline'];
    $nameCountry = $value['nameCountry'];
    $sizeAirline = $value['sizeAirline'];
    $statusAirline = $value['statusAirline'];
    $twitter = $value['twitter'];
    $type = $value['type'];
    $website = $value['website'];
    echo $benchmarkAirlinesId . "ID<br/>";

    try {               
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('benchmark_airlines');
        $insert->values(array(
            'benchmarkairlinesid' => $benchmarkAirlinesId,
            'agefleet' => $ageFleet,
            'codeiataairline' => $codeIataAirline,
            'codeicaoairline' => $codeIcaoAirline,
            'codeiso2country' => $codeIso2Country,
            'founding' => $founding,
            'nameairline' => $nameAirline,
            'namecountry' => $nameCountry,
            'sizeairline' => $sizeAirline,
            'statusairline' => $statusAirline,
            'type' => $type,
            'website' => $website,
            'facebook' => $facebook,
            'twitter' => $twitter
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