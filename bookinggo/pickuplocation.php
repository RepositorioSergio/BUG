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
echo "COMECOU PICKUP LOCATION<br/>";
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

$sql = "SELECT name, country FROM pickupcities";
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
        $city = $row->name;
        $country = $row->country;

        $url = "https://xml.rentalcars.com/service/ServiceRequest.do";

        $raw = '<PickUpLocationListRQ version="1.1" preflang="en"> 
        <Credentials username="club1hot944" password="club1hot944"/>
        <Country>' . $country . '</Country>
        <City>' . $city . '</City>
        </PickUpLocationListRQ>';

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
        $PickUpLocationListRS = $inputDoc->getElementsByTagName("PickUpLocationListRS");
        $LocationList = $PickUpLocationListRS->item(0)->getElementsByTagName("LocationList");
        if ($LocationList->length > 0) {
            $Location = $LocationList->item(0)->getElementsByTagName("Location");
            if ($Location->length > 0) {
                $nameoflocation = "";
                for ($i=0; $i < $Location->length; $i++) { 
                    $id = $Location->item($i)->getAttribute("id");
                    $nameoflocation = $Location->item($i)->nodeValue;

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('pickuplocations');
                        $insert->values(array(
                            'id' => $id,
                            'name' => $nameoflocation,
                            'city' => $city,
                            'country' => $country 
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO: " . $e;
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