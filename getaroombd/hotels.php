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
//echo "COMECOU CITIES";
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
$sql = "select value from settings where name='enableGetaroom' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_getaroom = $affiliate_id;
} else {
    $affiliate_id_getaroom = 0;
}
$sql = "select value from settings where name='GetaroomAuth' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAuth = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomAPIKey' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAPIKey = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomMarkup' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomMarkup = (double) $row_settings['value'];
} else {
    $GetaroomMarkup = 0;
}
$sql = "select value from settings where name='GetaroomContentServiceURL' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomContentServiceURL = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomAuthorizationToken' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAuthorizationToken = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomAuth' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAuth = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
    
$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

//CITIES
$raw = 'api/properties.xml?api_key=' . $GetaroomAPIKey . '&auth_token=' . $GetaroomAuth . '';

$startTime = microtime();
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $GetaroomContentServiceURL . $raw);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$endTime = microtime();
echo $return;
echo $response;
echo $return;
//$response = json_decode($response, true);
/* echo "<xmp>";
var_dump($response);
echo "</xmp>"; */

$config = new \Zend\Config\Config(include '../config/autoload/global.getaroom.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$count = count($response);
/* echo $return;
echo $count;
echo $return */;
for ($i=0; $i < $count; $i++) { 
    $CityLongName_CN = $Cities[$i]['CityLongName_CN'];
    $CityName_CN = $Cities[$i]['CityName_CN'];
    $CityLongName = $Cities[$i]['CityLongName'];
    $CityName = $Cities[$i]['CityName'];
    $CityCode = $Cities[$i]['CityCode'];
    $CountryCode = $Cities[$i]['CountryCode'];

    /* $sql = new Sql($db);
    $select = $sql->select();
    $select->from('didatravel_cities');
    $select->where(array(
        'CityCode' => $CityCode
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = (int) $data['CityCode'];
        if ($id > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'CityLongName_CN' => $CityLongName_CN,
                'CityName_CN' => $CityName_CN,
                'CityLongName' => $CityLongName,
                'CityName' => $CityName,
                'CityCode' => $CityCode,
                'CountryCode' => $CountryCode
                 );
                $where['CityCode = ?']  = $CityCode;
            $update = $sql->update('didatravel_cities', $data, $where);
            $db->getDriver()
            ->getConnection()
            ->disconnect();   
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('didatravel_properties');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'CityLongName_CN' => $CityLongName_CN,
                'CityName_CN' => $CityName_CN,
                'CityLongName' => $CityLongName,
                'CityName' => $CityName,
                'CityCode' => $CityCode,
                'CountryCode' => $CountryCode
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        }
    } else {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('didatravel_cities');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'CityLongName_CN' => $CityLongName_CN,
            'CityName_CN' => $CityName_CN,
            'CityLongName' => $CityLongName,
            'CityName' => $CityName,
            'CityCode' => $CityCode,
            'CountryCode' => $CountryCode
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } */
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>