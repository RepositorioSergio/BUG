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
echo "COMECOU PAYMENT METHOD<br/>";
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

$sql = "SELECT id FROM pickuplocations";
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

        $url = "https://xml.rentalcars.com/service/ServiceRequest.do";

        $raw = '<PaymentMethodListRQ version="1.1">
            <Credentials username="club1hot944" password="club1hot944" remoteIp="91.151.7.6"/> 
            <Location id="' . $locationid . '"/>
        </PaymentMethodListRQ>';

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
        $PaymentMethodListRS = $inputDoc->getElementsByTagName("PaymentMethodListRS");
        $PaymentMethodList = $PaymentMethodListRS->item(0)->getElementsByTagName("PaymentMethodList");
        if ($PaymentMethodList->length > 0) {
            $PaymentMethod = $PaymentMethodList->item(0)->getElementsByTagName("PaymentMethod");
            if ($PaymentMethod->length > 0) {
                $method = "";
                for ($i=0; $i < $PaymentMethod->length; $i++) { 
                    $id = $PaymentMethod->item($i)->getAttribute("id");
                    $minimumLeadTime = $PaymentMethod->item($i)->getAttribute("minimumLeadTime");
                    $type = $PaymentMethod->item($i)->getAttribute("type");
                    $method = $PaymentMethod->item($i)->nodeValue;

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('paymentmethods');
                        $insert->values(array(
                            'id' => $id,
                            'paymentmethod' => $method,
                            'minimumleadtime' => $minimumLeadTime,
                            'type' => $type
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