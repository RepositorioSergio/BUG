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
echo "COMECOU DROPOFF CITY<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.bookingdotcom.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT id, city, country FROM pickuplocations";
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
        $locationid = $row->id;
        $city = $row->city;
        $country = $row->country;
        echo $return;
        echo $locationid . "<br/>";
        echo $return;

        $url = "https://xml.rentalcars.com/service/ServiceRequest.do";

        $raw = '<DropOffCityListRQ version="1.1" preflang="en"> 
        <Credentials username="club1hot944" password="club1hot944" remoteIp="91.151.7.6"/> 
        <Location id="' . $locationid . '" country="' . $country . '" city="' . $city . '"/>
        <Country>' . $country . '</Country>
         </DropOffCityListRQ>';
        echo '<xmp>';
        var_dump($raw);
        echo '</xmp>';

        $headers = array(
            "Content-type: application/xml",
            "Content-length: " . strlen($raw)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2000);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        echo "<br/>RESPONSE";
        echo '<xmp>';
        var_dump($response);
        echo '</xmp>';

        $config = new \Zend\Config\Config(include '../config/autoload/global.bookingdotcom.php');
        $config = [
            'driver' => $config->db->driver,
            'database' => $config->db->database,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'hostname' => $config->db->hostname
        ];
        $db = new \Zend\Db\Adapter\Adapter($config);

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $DropOffCityListRS = $inputDoc->getElementsByTagName("DropOffCityListRS");
        $CityList = $DropOffCityListRS->item(0)->getElementsByTagName("CityList");
        if ($CityList->length > 0) {
            $City = $CityList->item(0)->getElementsByTagName("City");
            if ($City->length > 0) {
                $nameofcity = "";
                for ($i=0; $i < $City->length; $i++) { 
                    $nameofcity = $City->item($i)->nodeValue;

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('dropoffcities');
                        $select->where(array(
                            'name' => $nameofcity
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $nameofcity = (string)$data['name'];
                            if ($nameofcity != "") {
                                $config = new \Zend\Config\Config(include '../config/autoload/global.bookingdotcom.php');
                                $config = [
                                    'driver' => $config->db->driver,
                                    'database' => $config->db->database,
                                    'username' => $config->db->username,
                                    'password' => $config->db->password,
                                    'hostname' => $config->db->hostname
                                ];
                                $dbUpdate = new \Zend\Db\Adapter\Adapter($config);

                                $data = array(
                                    'name' => $nameofcity
                                    );
                                
                                $sql    = new Sql($dbUpdate);
                                $update = $sql->update();
                                $update->table('dropoffcities');
                                $update->set($data);
                                $update->where(array('name' => $nameofcity));
        
                                $statement = $sql->prepareStatementForSqlObject($update);
                                $results = $statement->execute();
                                $dbUpdate->getDriver()
                                ->getConnection()
                                ->disconnect(); 
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('dropoffcities');
                                $insert->values(array(
                                    'name' => $nameofcity,
                                    'country' => $country
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
                            $insert->into('dropoffcities');
                            $insert->values(array(
                                'name' => $nameofcity,
                                'country' => $country
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        }
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO: ". $e;
                        echo $return;
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