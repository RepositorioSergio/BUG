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

$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];


$url = 'http://testxml.youtravel.com/webservicestest/bookings.asp?Session_ID=1086201679825201929234914&LangID=EN&HID=20155&Username=xmltestme&Password=testme&Nights=7&Checkin_Date=10/10/2019&Rooms=1&ADLTS_1=2&CHILD_1=0&RID=1121064&Room1_Rate=340.33&Customer_title=Mr&Customer_firstname=Charles&Customer_Lastname=Bill&Email=charlesbill@gmail.com';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'text/xml;charset=ISO-8859-1',
    'Content-Length' => '0'
));
$client->setUri($url);
$client->setMethod('POST');
//$client->setRawBody($raw);
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

echo "RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
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
$HtSearchRq = $inputDoc->getElementsByTagName("HtSearchRq");

$Booking_ref = $HtSearchRq->item(0)->getElementsByTagName("Booking_ref");
if ($Booking_ref->length > 0) {
    $Booking_ref = $Booking_ref->item(0)->nodeValue;
} else {
    $Booking_ref = "";
}
$Voucher_Url = $HtSearchRq->item(0)->getElementsByTagName("Voucher_Url");
if ($Voucher_Url->length > 0) {
    $Voucher_Url = $Voucher_Url->item(0)->nodeValue;
} else {
    $Voucher_Url = "";
}

/* try {
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('hoteis');
    $select->where(array(
        'id' => $id
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = (int)$data['id'];
        if ($id > 0) {
            $sql = new Sql($db);
            $data = array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'recomended' => $recomended,
                'stars' => $stars
            );
            $where['id = ?'] = $id;
            $update = $sql->update('hoteis', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hoteis');
            $insert->values(array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'recomended' => $recomended,
                'stars' => $stars
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
        $insert->into('hoteis');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'name' => $name,
            'city' => $city,
            'country' => $country,
            'recomended' => $recomended,
            'stars' => $stars
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
} catch (Exception $ex) {
    echo $return;
    echo "ERRO: " . $ex;
    echo $return;
} */

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>