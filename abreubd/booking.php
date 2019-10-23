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
echo "COMECOU BOOKING";
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

$elementId = 281644;
$elementId2 = 252507;
$packageId = 28891;
$currencyCode = "USD";
$startDate = "2019-11-08";
$packageDepartureId = 661692;
$bookingCode = "UNKZ0SSaPGjyfPwQnwudwqaYGkS4vZb0GR3Gv1FnNPKh0OlKdaBrFqWuYmtr8Q9OCLbzLPrC3icJ3ixdb1XZSh7T4lRl3WhLwT6r+4fC5Io6PJyhg1myI5iBpZbIu8YAV9C0kOrvATKWsDq4dfrKcehLOnhBZ66k6ZmMHDDLxNf3sVFJ13yIbYDUmAVuSswU4RTGqLuSe77CGe/grmTCm91tv07tHNyKTRrTRimdNCF6PuxN7b5G5u8RkveSKXZE9zBDbyub0Cj/y88K9vH71ZKR856uBFt0IP+H94m7YlAYBd0BstrYQRkxbInTFiM=";
$bookingCode2 = "phfa6ij1I1TWgKTqP9IEgw+VTSQkA1bRtBWZ9kVM8gTkOdlaaFbn0QCeyoI3/WoIQoznmj8drAlOGvFoT37g6YxYssDtmlfectSkUe3LrpO+l9tAxiiZJgc3mOsuHi1krlNOi6vazkftxj9MQJB9AfFFOnEEmLNWsnXQcOhaiHkssXn1KvSThhfL97Dl7ja1d0kwU4sZvEvFa/q6+bpNyq2IQuGeFM/wN8NT8tbuAFa6EtAUEQyRL136jznqmUYVzgB+uPy4mg0nm7g5FDepcwBB5osE8/D0FlEfMT8O/7SrVpp7XetcJlKJ5/8avh8vVJu8OoWkLlQoUF74NVsg";


$raw = '{   "username": "' . $abreupackagesuser . '",   "password": "' . $abreupackagespassword . '",   "language": "ES",   "agentBookingReference": "AGH",   "passengersInfo": [ {       "title": "Mr",       "type": "Adult",       "firstName": "Antonio",       "lastName": "Leal",       "dateofbirth": "1989-04-05",       "documentType": "DNI",       "documentNumber": "123456",       "passportNumber": "A123456",       "passportExpirationDate": "2021-05-21",       "passportCreationDate": "2018-05-21",       "telephone": "914445522",       "email": "ajja@skks.com",       "nationality": "ES",       "passportCountry": "ES",       "elementId": ' . $elementId2 . '  
}, {       "title": "Mrs",       "type": "Adult",       "firstName": "Maria",       "lastName": "Leal",       "dateofbirth": "1990-04-05",       "documentType": "DNI",       "documentNumber": "123456",       "passportNumber": "A123456",       "passportExpirationDate": "2021-05-21",       "passportCreationDate": "2018-05-21",       "telephone": "914445522",       "email": "ajja@skks.com",       "nationality": "ES",       "passportCountry": "ES",       "elementId": ' . $elementId2 . '  
}, {       "title": "Mrs",       "type": "Adult",       "firstName": "Matilde",       "lastName": "Leal",       "dateofbirth": "1991-04-05",       "documentType": "DNI",       "documentNumber": "123456",       "passportNumber": "A123456",       "passportExpirationDate": "2021-05-21",       "passportCreationDate": "2018-05-21",       "telephone": "914445522",       "email": "ajja@skks.com",       "nationality": "ES",       "passportCountry": "ES",       "elementId": ' . $elementId2 . '  
}],"packageId": ' . $packageId . ',   "departureId": ' . $packageDepartureId . ',   "departureDate": "' . $startDate . '",   "currencyCode": "' . $currencyCode . '",   "elements": [  {       "bookingCode": "' . $bookingCode2 . '",       "elementId": ' . $elementId2 . ',       "elementQuantity": 1,       "elementNAdults": 3,       "elementNChildren": 0     }    ] }';
echo $return;
echo $raw;
echo $return;
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
$client->setUri($abreupackagesserviceURL . 'Booking/Book');
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
    die();
}
$response = iconv('UTF-8', 'ASCII//TRANSLIT', $response);
echo $return;
echo $response;
echo $return; 
$response = json_decode($response, true);

if ($response === false || $response === null) {
    echo $return;
    echo "NOT DECODE";
    echo $return;
}
die();
if (json_last_error() == 0) {
    echo '- Nao houve erro! O parsing foi perfeito';
} else {
    echo 'Erro!<br/>';
    switch (json_last_error()) {
        
        case JSON_ERROR_DEPTH:
            echo ' - profundidade maxima excedida';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - state mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Caracter de controle encontrado';
            break;
        case JSON_ERROR_SYNTAX:
            echo ' - Erro de sintaxe! String JSON mal-formada!';
            break;
        case JSON_ERROR_UTF8:
            echo ' - Erro na codificação UTF-8';
            break;
        default:
            echo ' – Erro desconhecido';
            break;
    }
}

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$bookingReferenceNumber = $response['bookingReferenceNumber'];
if ($bookingReferenceNumber != "") {
    $bookingTotalAmount = $response['bookingTotalAmount'];
    $bookingCurrency = $response['bookingCurrency'];
    echo $return;
    echo $bookingCurrency;
    echo $return;

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('booking');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'bookingReferenceNumber' => $bookingReferenceNumber,
            'bookingTotalAmount' => $bookingTotalAmount,
            'bookingCurrency' => $bookingCurrency
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>