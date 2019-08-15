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
echo "COMECOU AVAILABILITY ALL HOTELS<br/>";
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
$affiliate_id_palace = 0;
$branch_filter = "";
$sql = "select value from settings where name='palaceresortswebserviceurl' and affiliate_id=$affiliate_id_palace";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $palaceresortswebserviceurl = $row_settings['value'];
}


/* $sql = "select value from settings where name='palaceresortslogin' and affiliate_id=$affiliate_id_palace";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $palaceresortslogin = $row_settings['value'];
}
$sql = "select value from settings where name='palaceresortspassword' and affiliate_id=$affiliate_id_palace";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $palaceresortspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='palaceresortsMarkup' and affiliate_id=$affiliate_id_palace";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $palaceresortsMarkup = (double) $row_settings['value'];
} else {
    $palaceresortsMarkup = 0;
}

$sql = "select value from settings where name='palaceresortsAgencyCode' and affiliate_id=$affiliate_id_palace";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $palaceresortsAgencyCode = $row_settings['value'];
}
$sql = "select value from settings where name='palaceresortsSecurityCode' and affiliate_id=$affiliate_id_palace";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $palaceresortsSecurityCode = $row_settings['value'];
} */

$url = "https://api.palaceresorts.com/EnterpriseServiceInterface/ServiceInterface.asmx";

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://localhost/pr_xmlschemas/hotel/01-03-2006/availability.xsd" xmlns:ns2="http://localhost/pr_xmlschemas/hotel/01-03-2006/availabilityRequest.xsd" xmlns:ns3="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd" xmlns:ns4="http://localhost/xmlschemas/enterpriseservice/16-07-2009/">
  <SOAP-ENV:Body>
    <ns4:GetAvailability_AllHotels>
      <ns2:availabilityRequest>
        <ns2:data>
          <ns1:hotel>MPG</ns1:hotel>
          <ns1:room_type>RV</ns1:room_type>
          <ns1:bed_type></ns1:bed_type>
          <ns1:arrival_date>2019-09-23</ns1:arrival_date>
          <ns1:departure_date>2019-09-28</ns1:departure_date>
          <ns1:adultos>2</ns1:adultos>
          <ns1:menores>0</ns1:menores>
          <ns1:baby>0</ns1:baby>
          <ns1:child>0</ns1:child>
          <ns1:kid>0</ns1:kid>
          <ns1:rate_plan></ns1:rate_plan>
          <ns1:group_code></ns1:group_code>
          <ns1:promotion_code></ns1:promotion_code>
          <ns1:idioma></ns1:idioma>
          <ns1:agency_cd>CTM-PERU</ns1:agency_cd>
        </ns2:data>
        <ns2:Tag></ns2:Tag>
        <ns2:AuthInfo>
          <ns3:Recnum>0</ns3:Recnum>
          <ns3:Ent_User>CTM-PERU</ns3:Ent_User>
          <ns3:Ent_Pass>x4Mg82k9WS</ns3:Ent_Pass>
          <ns3:Ent_Term>CTM-PERU</ns3:Ent_Term>
        </ns2:AuthInfo>
  </ns2:availabilityRequest>
</ns4:GetAvailability_AllHotels>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';


$raw = '<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://localhost/pr_xmlschemas/hotel/01-03-2006/availability.xsd" xmlns:ns2="http://localhost/pr_xmlschemas/hotel/01-03-2006/availabilityRequest.xsd" xmlns:ns3="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd" xmlns:ns4="http://localhost/xmlschemas/enterpriseservice/16-07-2009/">
  <SOAP-ENV:Body>
    <ns4:GetAvailability_AllHotels>
      <ns2:availabilityRequest>
        <ns2:data>
          <ns1:hotel></ns1:hotel>
          <ns1:room_type>RV</ns1:room_type>
          <ns1:bed_type></ns1:bed_type>
          <ns1:arrival_date>2019-09-23</ns1:arrival_date>
          <ns1:departure_date>2019-09-28</ns1:departure_date>
          <ns1:adultos>2</ns1:adultos>
          <ns1:menores>0</ns1:menores>
          <ns1:baby>0</ns1:baby>
          <ns1:child>0</ns1:child>
          <ns1:kid>0</ns1:kid>
          <ns1:rate_plan></ns1:rate_plan>
          <ns1:group_code></ns1:group_code>
          <ns1:promotion_code></ns1:promotion_code>
          <ns1:idioma></ns1:idioma>
          <ns1:agency_cd>CTM-PERU</ns1:agency_cd>
        </ns2:data>
        <ns2:Tag>?</ns2:Tag>
        <ns2:AuthInfo>
          <ns3:Recnum>0</ns3:Recnum>
          <ns3:Ent_User>CTM-PERU</ns3:Ent_User>
          <ns3:Ent_Pass>x4Mg82k9WS</ns3:Ent_Pass>
          <ns3:Ent_Term>CTM-PERU</ns3:Ent_Term>
        </ns2:AuthInfo>
      </ns2:availabilityRequest>
    </ns4:GetAvailability_AllHotels>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

$headers = array(
    "Content-type: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Host: api.palaceresorts.com",
    "Content-length: " . strlen($raw)
); // SOAPAction: your op URL

$startTime = microtime();
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $palaceresortswebserviceurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);

echo $response;

$error = curl_error($ch);
$headers = curl_getinfo($ch);
if ($response === false) {
    echo $return;
    echo "ERRO: " . curl_error($ch);
    echo $return;
} else {
    echo $return;
    echo "Operation completed without any errors.";
    echo $return;
}
curl_close($ch);


echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>'; 
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
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

$GetAvailabilityResponse = $Body->item(0)->getElementsByTagName("GetAvailabilityResponse");
if ($GetAvailabilityResponse->length > 0) {
    $roomAvailabilityResponse = $GetAvailabilityResponse->item(0)->getElementsByTagName("roomAvailabilityResponse");
    if ($roomAvailabilityResponse->length > 0) {
        $Hotel = $roomAvailabilityResponse->item(0)->getElementsByTagName('Hotel');
        if ($Hotel->length > 0) {
            $Hotel = $Hotel->item(0)->nodeValue;
        } else {
            $Hotel = "";
        }
        $shid = $Hotel;
        $sfilter[] = " sid='$Hotel' ";
        $TotalAmount = $roomAvailabilityResponse->item(0)->getElementsByTagName('TotalAmount');
        if ($TotalAmount->length > 0) {
            $TotalAmount = $TotalAmount->item(0)->nodeValue;
        } else {
            $TotalAmount = "";
        }
        $Moneda = $roomAvailabilityResponse->item(0)->getElementsByTagName('Moneda');
        if ($Moneda->length > 0) {
            $Moneda = $Moneda->item(0)->nodeValue;
        } else {
            $Moneda = "";
        }
        $TipoCambio = $roomAvailabilityResponse->item(0)->getElementsByTagName('TipoCambio');
        if ($TipoCambio->length > 0) {
            $TipoCambio = $TipoCambio->item(0)->nodeValue;
        } else {
            $TipoCambio = "";
        }
        $Tarifa1raNoche = $roomAvailabilityResponse->item(0)->getElementsByTagName('Tarifa1raNoche');
        if ($Tarifa1raNoche->length > 0) {
            $Tarifa1raNoche = $Tarifa1raNoche->item(0)->nodeValue;
        } else {
            $Tarifa1raNoche = "";
        }
        $RateCode = $roomAvailabilityResponse->item(0)->getElementsByTagName('RateCode');
        if ($RateCode->length > 0) {
            $RateCode = $RateCode->item(0)->nodeValue;
        } else {
            $RateCode = "";
        }
        $DescripcionTarifa = $roomAvailabilityResponse->item(0)->getElementsByTagName('DescripcionTarifa');
        if ($DescripcionTarifa->length > 0) {
            $DescripcionTarifa = $DescripcionTarifa->item(0)->nodeValue;
        } else {
            $DescripcionTarifa = "";
        }
        
        $Data = $roomAvailabilityResponse->item(0)->getElementsByTagName('Data');
        if ($Data->length > 0) {
            $Availability = $Data->item(0)->getElementsByTagName('Availability');
            if ($Availability->length > 0) {
                $dayAvailable = $Availability->item(0)->getElementsByTagName('dayAvailable');
                if ($dayAvailable->length > 0) {
                    for ($i = 0; $i < $dayAvailable->length; $i ++) {
                        $Day = $dayAvailable->item($i)->getElementsByTagName('Day');
                        if ($Day->length > 0) {
                            $Day = $Day->item(0)->nodeValue;
                        } else {
                            $Day = "";
                        }
                        $Available = $dayAvailable->item($i)->getElementsByTagName('Available');
                        if ($Available->length > 0) {
                            $Available = $Available->item(0)->nodeValue;
                        } else {
                            $Available = "";
                        }
                        $Rate = $dayAvailable->item($i)->getElementsByTagName('Rate');
                        if ($Rate->length > 0) {
                            $Rate = $Rate->item(0)->nodeValue;
                        } else {
                            $Rate = "";
                        }
                        $RateCode = $dayAvailable->item($i)->getElementsByTagName('RateCode');
                        if ($RateCode->length > 0) {
                            $RateCode = $RateCode->item(0)->nodeValue;
                        } else {
                            $RateCode = "";
                        }
                        $RateCodeDescription = $dayAvailable->item($i)->getElementsByTagName('RateCodeDescription');
                        if ($Day->length > 0) {
                            $RateCodeDescription = $RateCodeDescription->item(0)->nodeValue;
                        } else {
                            $RateCodeDescription = "";
                        }
                        $Currency = $dayAvailable->item($i)->getElementsByTagName('Currency');
                        if ($Currency->length > 0) {
                            $Currency = $Currency->item(0)->nodeValue;
                        } else {
                            $Currency = "";
                        }
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