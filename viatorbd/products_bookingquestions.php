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
// echo "COMECOU CITIES";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = 'https://api.viator.com/partner/products/booking-questions';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'exp-api-key' => '5364bbaf-e4f7-4727-9e91-317e794dfbaa',
    'Accept-Language' => 'en-US',
    'Accept' => 'application/json;version=2.0'
));
$client->setUri($url);
$client->setMethod('GET');
// $client->setRawBody($raw);
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

echo $return;
echo $response;
echo $return;

$response = json_decode($response, true);

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$bookingQuestions = $response['bookingQuestions'];
if (count($bookingQuestions) > 0) {
    for ($i=0; $i < count($bookingQuestions); $i++) { 
        $legacyBookingQuestionId = $bookingQuestions[$i]['legacyBookingQuestionId'];
        $id = $bookingQuestions[$i]['id'];
        $type = $bookingQuestions[$i]['type'];
        $group = $bookingQuestions[$i]['group'];
        $label = $bookingQuestions[$i]['label'];
        $hint = $bookingQuestions[$i]['hint'];
        $required = $bookingQuestions[$i]['required'];
        $maxLength = $bookingQuestions[$i]['maxLength'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('viator_productsbookingquestions');
            $insert->values(array(
                'id' => $id,
                'datetime_updated' => time(),
                'legacybookingquestionid' => $legacyBookingQuestionId,
                'type' => $type,
                'group' => $group,
                'label' => $label,
                'hint' => $hint,
                'required' => $required,
                'maxlength' => $maxLength
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 1: " . $e;
            echo $return;
        }

        $units = $bookingQuestions[$i]['units'];
        if (count($units) > 0) {
            $unit = "";
            for ($iAux=0; $iAux < count($units); $iAux++) { 
                $unit = $units[$iAux];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_productsbookingquestions_units');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'unit' => $unit,
                        'bookingquestionid' => $id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 2: " . $e;
                    echo $return;
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