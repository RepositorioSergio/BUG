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
echo "COMECOU OPTION DETAIL<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));

$url = "https://stage.services.rccl.com/";

$raw ='<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<m:getOptionDetail xmlns:m="http://services.rccl.com/Interfaces/OptionDetail">
    <OTA_CruiseSpecialServiceAvailRQ MaxResponses="30" MoreDataEchoToken="1" RetransmissionIndicator="false" SequenceNmbr="1" Target="Test" TimeStamp="2008-05-21T13:26:38" Version="2.0">
    <POS>
        <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
            <RequestorID ID="u73ecKBu73ecKB!" ID_Context="CONCTMM" Type="5"/>
            <BookingChannel Type="7">
                <CompanyName CompanyShortName="IST"/>
            </BookingChannel>
        </Source>
        <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
            <RequestorID ID="u73ecKBu73ecKB!" ID_Context="CONCTMM" Type="5"/>
            <BookingChannel Type="7">
                <CompanyName CompanyShortName="IST"/>
            </BookingChannel>
        </Source>
        <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
            <RequestorID ID="u73ecKBu73ecKB!" ID_Context="CONCTMM" Type="5"/>
            <BookingChannel Type="7">
                <CompanyName CompanyShortName="IST"/>
            </BookingChannel>
        </Source>
    </POS>
    <SailingInfo>
        <SelectedSailing Start="2019-10-02">
        <CruiseLine ShipCode="FR"/>
        </SelectedSailing>
    </SailingInfo>
    <SpecialService Code="PPGR"/>
    </OTA_CruiseSpecialServiceAvailRQ>
</m:getOptionDetail>
</soap:Body>
</soap:Envelope>';

/* $client->setUri($url);
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
} */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
if ($response === false) {
    echo $return;
    echo "ERRO: " . $error;
    echo $return;
} else {
    echo $return;
    echo "Operation completed without any errors";
    echo $return;
}
curl_close($ch);

echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$detail = "";

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$getOptionDetailResponse = $Body->item(0)->getElementsByTagName("getOptionDetailResponse");
$OTA_ScreenTextRS = $getOptionDetailResponse->item(0)->getElementsByTagName("OTA_ScreenTextRS");
$TextScreens = $OTA_ScreenTextRS->item(0)->getElementsByTagName("TextScreens");
$TextScreen = $TextScreens->item(0)->getElementsByTagName("TextScreen");
$node = $TextScreen->item(0)->getElementsByTagName("TextData");
for ($i=0; $i < $node->length; $i++) { 
    $detail = $node->item($i)->nodeValue;

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('optionDetail');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'detail' => $detail
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
echo '<br/>Done';
?>