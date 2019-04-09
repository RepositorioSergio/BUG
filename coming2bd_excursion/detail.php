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
echo "COMECOU DETAIL";
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
$sql = "select value from settings where name='enablecoming2' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_coming2 = $affiliate_id;
} else {
    $affiliate_id_coming2 = 0;
}
$sql = "select value from settings where name='coming2login' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2login = $row_settings['value'];
}
$sql = "select value from settings where name='coming2password' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2password = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='coming2ServiceURL' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2ServiceURL = $row_settings['value'];
}
echo $return;
echo $coming2ServiceURL;
echo $return;
$sql = "select value from settings where name='coming2Company' and affiliate_id=$affiliate_id_coming2";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $coming2Company = $row_settings['value'];
}


$config = new \Zend\Config\Config(include '../config/autoload/global.comming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$raw = '{
    "token":"token",
    "language":"ES",
    "destination":"PUJ",
    "adults":5,
    "kids":2,
    "babies":0,
    "initialDate":"20190601",
    "finalDate":"20190608"
}';

$passuser = "COSTAMAR:COSTAMAR";
$auth = base64_encode($passuser);
echo $return;
echo $auth;
echo $return;
$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: application/json",
    "Accept: application/json",
    "Content-length: " . strlen($raw)
));

$client->setUri($coming2ServiceURL . '/excursion/availability');
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

$response = json_decode($response, true);

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.comming2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$excursionList = $response['excursionList'];
for ($i=0; $i < count($excursionList); $i++) { 
    $recomended = $excursionList[$i]['recomended'];
    $groupInfo = $excursionList[$i]['groupInfo'];
    $code = $groupInfo['code'];
    $title = $groupInfo['title'];
    $subtitle = $groupInfo['subtitle'];


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('detail');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'recomended' => $recomended,
            'code' => $code,
            'title' => $title,
            'subtitle' => $subtitle
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error LIST: " . $e;
        echo $return;
    }

    $themesList = $groupInfo['themesList'];
    for ($iAux=0; $iAux < count($themesList); $iAux++) { 
        $themesListcode = $themesList[$iAux]['code'];
        $name = $themesList[$iAux]['name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('detail_themesList');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'code' => $themesListcode,
                'name' => $name,
                'codeGroup' => $code
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error TH: " . $e;
            echo $return;
        }
    }

    $featureList = $groupInfo['featureList'];
    for ($iAux=0; $iAux < count($featureList); $iAux++) { 
        $featureListcode = $featureList[$iAux]['code'];
        $name = $featureList[$iAux]['name'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('detail_featureList');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'code' => $featureListcode,
                'name' => $name,
                'codeGroup' => $code
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error FEA: " . $e;
            echo $return;
        }
    }

    $image = "";
    $imageList = $groupInfo['imageList'];
    for ($iAux=0; $iAux < count($imageList); $iAux++) { 
        $image = $imageList[$iAux];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('detail_imageList');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'image' => $image,
                'codeGroup' => $code
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error IMG: " . $e;
            echo $return;
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>