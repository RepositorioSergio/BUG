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
echo "COMECOU CRUISE SAIL AVAIL<br/>";
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

$username = 'CONCTMM';
$password = 'u73ecKBu73ecKB!';

$url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/SailingList";

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sail="http://services.rccl.com/Interfaces/SailingList" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
<soapenv:Header/>
<soapenv:Body>
   <sail:getSailingList>
      <alp:OTA_CruiseSailAvailRQ TimeStamp="2008-07-17T12:44:44.866-04:00" Target="Test" Version="1.0" SequenceNmbr="1" PrimaryLangID="en" RetransmissionIndicator="false" MoreIndicator="true" MaxResponses="50">
         <alp:POS>
            <!--1 to 10 repetitions:-->
            <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <alp:RequestorID ID="313917" ID_Context="AGENCY1" Type="5"/>
                <alp:BookingChannel Type="7">
                    <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                </alp:BookingChannel>
            </alp:Source>
            <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <alp:RequestorID ID="313917" ID_Context="AGENCY2" Type="5"/>
                <alp:BookingChannel Type="7">
                    <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                </alp:BookingChannel>
            </alp:Source>
            <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <alp:RequestorID ID="313917" ID_Context="AGENT1" Type="5"/>
                <alp:BookingChannel Type="7">
                    <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                </alp:BookingChannel>
            </alp:Source>
         </alp:POS>
         <!--Optional:-->
         <alp:GuestCounts>
            <alp:GuestCount Quantity="1"/>
            <alp:GuestCount Quantity="1"/>
        </alp:GuestCounts>
        <alp:SailingDateRange Start="2020-08-08" End="2020-08-15" MinDuration="P6N" MaxDuration="P8N"/>
      </alp:OTA_CruiseSailAvailRQ>
   </sail:getSailingList>
</soapenv:Body>
</soapenv:Envelope>';


echo $raw;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
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

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$getGuestServiceListResponse = $Body->item(0)->getElementsByTagName("getGuestServiceListResponse");
$OTA_CruiseSpecialServiceAvailRS = $getGuestServiceListResponse->item(0)->getElementsByTagName("OTA_CruiseSpecialServiceAvailRS");
$SpecialServices = $OTA_CruiseSpecialServiceAvailRS->item(0)->getElementsByTagName("SpecialServices");
$node = $SpecialServices->item(0)->getElementsByTagName("SpecialService");
for ($i=0; $i < $node->length; $i++) { 
    $Code = $node->item($i)->getAttribute("Code");
    $Description = $node->item($i)->getAttribute("Description");
    $MinGuestsRequired = $node->item($i)->getAttribute("MinGuestsRequired");
    $NbrOfYearsRequiredInd = $node->item($i)->getAttribute("NbrOfYearsRequiredInd");
    $ServiceDateRequiredInd = $node->item($i)->getAttribute("ServiceDateRequiredInd");
    $UserRemarkRequiredInd = $node->item($i)->getAttribute("UserRemarkRequiredInd");


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('serviceList');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'Code' => $Code,
            'Description' => $Description,
            'MinGuestsRequired' => $MinGuestsRequired,
            'NbrOfYearsRequiredInd' => $NbrOfYearsRequiredInd,
            'ServiceDateRequiredInd' => $ServiceDateRequiredInd,
            'UserRemarkRequiredInd' => $UserRemarkRequiredInd
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
