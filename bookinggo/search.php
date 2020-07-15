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
echo "COMECOU SEARCH<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.infinitastravel.php');
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
    "Content-type: application/xml",
    "Content-length: ".strlen($raw)
));

$url = "https://xml.rentalcars.com/service/ServiceRequest.do";

$raw = '<SearchRQ version="1.1">
    <Credentials username="club1hot944" password="club1hot944" remoteIp="91.151.7.6"/> 
    <PickUp>
        <Location id="3806"/>
        <Date year="2020" month="11" day="21" hour="12" minute="30"/> 
    </PickUp>
    <DropOff>
        <Location id="3806"/>
        <Date year="2020" month="11" day="28" hour="12" minute="30"/>
    </DropOff>
    <DriverAge>32</DriverAge>
</SearchRQ>';

$client->setUri($url);
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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.infinitastravel.php');
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
$SearchRS = $inputDoc->getElementsByTagName("SearchRS");
$MatchList = $SearchRS->item(0)->getElementsByTagName("MatchList");
if ($MatchList->length > 0) {
    $Match = $MatchList->item(0)->getElementsByTagName("Match");
    if ($Match->length > 0) {
        for ($i=0; $i < $Match->length; $i++) { 
            $ForwardURL = $Match->item($i)->getElementsByTagName("ForwardURL");
            if ($ForwardURL->length > 0) {
                $ForwardURL = $ForwardURL->item(0)->nodeValue;
            } else {
                $ForwardURL = "";
            }
            $Vehicle = $Match->item($i)->getElementsByTagName("Vehicle");
            if ($Vehicle->length > 0) {
                $id = $Vehicle->item(0)->getAttribute("id");
                $propositionType = $Vehicle->item(0)->getAttribute("propositionType");
                $automatic = $Vehicle->item(0)->getAttribute("automatic");
                $aircon = $Vehicle->item(0)->getAttribute("aircon");
                $airbag = $Vehicle->item(0)->getAttribute("airbag");
                $petrol = $Vehicle->item(0)->getAttribute("petrol");
                $group = $Vehicle->item(0)->getAttribute("group");
                $doors = $Vehicle->item(0)->getAttribute("doors");
                $seats = $Vehicle->item(0)->getAttribute("seats");
                $bigSuitcase = $Vehicle->item(0)->getAttribute("bigSuitcase");
                $smallSuitcase = $Vehicle->item(0)->getAttribute("smallSuitcase");
                $suitcases = $Vehicle->item(0)->getAttribute("suitcases");
                $fuelPolicy = $Vehicle->item(0)->getAttribute("fuelPolicy");
                $cmaCompliant = $Vehicle->item(0)->getAttribute("cmaCompliant");
                $insurancePkg = $Vehicle->item(0)->getAttribute("insurancePkg");
                $display = $Vehicle->item(0)->getAttribute("display");
                $order = $Vehicle->item(0)->getAttribute("order");
                $freeCancellation = $Vehicle->item(0)->getAttribute("freeCancellation");
                $unlimitedMileage = $Vehicle->item(0)->getAttribute("unlimitedMileage");
                $Name = $Vehicle->item(0)->getElementsByTagName("Name");
                if ($Name->length > 0) {
                    $Name = $Name->item(0)->nodeValue;
                } else {
                    $Name = "";
                }
                $ImageURL = $Vehicle->item(0)->getElementsByTagName("ImageURL");
                if ($ImageURL->length > 0) {
                    $ImageURL = $ImageURL->item(0)->nodeValue;
                } else {
                    $ImageURL = "";
                }
                $LargeImageURL = $Vehicle->item(0)->getElementsByTagName("LargeImageURL");
                if ($LargeImageURL->length > 0) {
                    $LargeImageURL = $LargeImageURL->item(0)->nodeValue;
                } else {
                    $LargeImageURL = "";
                }
            }
            $Fees = $Match->item($i)->getElementsByTagName("Fees");
            if ($Fees->length > 0) {
                $DepositExcessFees = $Fees->item(0)->getElementsByTagName("DepositExcessFees");
                if ($DepositExcessFees->length > 0) {
                    $TheftExcess = $DepositExcessFees->item(0)->getElementsByTagName("TheftExcess");
                    if ($TheftExcess->length > 0) {
                        $TheftExcess_amount = $TheftExcess->item(0)->getAttribute("amount");
                        $TheftExcess_currency = $TheftExcess->item(0)->getAttribute("currency");
                        $TheftExcess_taxIncluded = $TheftExcess->item(0)->getAttribute("taxIncluded");
                    }
                    $DamageExcess = $DepositExcessFees->item(0)->getElementsByTagName("DamageExcess");
                    if ($DamageExcess->length > 0) {
                        $DamageExcess_amount = $DamageExcess->item(0)->getAttribute("amount");
                        $DamageExcess_currency = $DamageExcess->item(0)->getAttribute("currency");
                        $DamageExcess_taxIncluded = $DamageExcess->item(0)->getAttribute("taxIncluded");
                    }
                    $Deposit = $DepositExcessFees->item(0)->getElementsByTagName("Deposit");
                    if ($Deposit->length > 0) {
                        $Deposit_amount = $Deposit->item(0)->getAttribute("amount");
                        $Deposit_currency = $Deposit->item(0)->getAttribute("currency");
                        $Deposit_taxIncluded = $Deposit->item(0)->getAttribute("taxIncluded");
                    }
                }
                $FuelPolicy = $Fees->item(0)->getElementsByTagName("FuelPolicy");
                if ($FuelPolicy->length > 0) {
                    $FuelPolicy_type = $FuelPolicy->item(0)->getAttribute("type");
                }
                $KnownFees = $Fees->item(0)->getElementsByTagName("KnownFees");
                if ($KnownFees->length > 0) {
                    $Fee = $KnownFees->item(0)->getElementsByTagName("Fee");
                    if ($Fee->length > 0) {
                        for ($iAux=0; $iAux < $Fee->length; $iAux++) { 
                            $feeTypeName = $Fee->item($iAux)->getAttribute("feeTypeName");
                            $alwaysPayable = $Fee->item($iAux)->getAttribute("alwaysPayable");
                            $minAmount = $Fee->item($iAux)->getAttribute("minAmount");
                            $maxAmount = $Fee->item($iAux)->getAttribute("maxAmount");
                            $currency = $Fee->item($iAux)->getAttribute("currency");
                            $taxIncluded = $Fee->item($iAux)->getAttribute("taxIncluded");
                            $perDuration = $Fee->item($iAux)->getAttribute("perDuration");
                            $feeDistance = $Fee->item($iAux)->getAttribute("feeDistance");
                            $distance = $Fee->item($iAux)->getAttribute("distance");
                            $iskM = $Fee->item($iAux)->getAttribute("iskM");
                            $unlimited = $Fee->item($iAux)->getAttribute("unlimited");
                        }
                    }
                }
            }
            $Price = $Match->item($i)->getElementsByTagName("Price");
            if ($Price->length > 0) {
                $currency = $Price->item(0)->getAttribute("currency");
                $baseCurrency = $Price->item(0)->getAttribute("baseCurrency");
                $basePrice = $Price->item(0)->getAttribute("basePrice");
                $deposit = $Price->item(0)->getAttribute("deposit");
                $baseDeposit = $Price->item(0)->getAttribute("baseDeposit");
                $discount = $Price->item(0)->getAttribute("discount");
                $driveAwayPrice = $Price->item(0)->getAttribute("driveAwayPrice");
                $quoteAllowed = $Price->item(0)->getAttribute("quoteAllowed");
                $Price = $Price->item(0)->nodeValue;
            } else {
                $Price = "";
            }
            $Route = $Match->item($i)->getElementsByTagName("Route");
            if ($Route->length > 0) {
                $PickUp = $Route->item(0)->getElementsByTagName("PickUp");
                if ($PickUp->length > 0) {
                    $Location = $PickUp->item(0)->getElementsByTagName("Location");
                    if ($Location->length > 0) {
                        $Location_id = $Location->item(0)->getAttribute("id");
                        $Location_locCode = $Location->item(0)->getAttribute("locCode");
                        $Location_locName = $Location->item(0)->getAttribute("locName");
                        $Location_onAirport = $Location->item(0)->getAttribute("onAirport");
                    }
                }
                $DropOff = $Route->item(0)->getElementsByTagName("DropOff");
                if ($DropOff->length > 0) {
                    $Location = $DropOff->item(0)->getElementsByTagName("Location");
                    if ($Location->length > 0) {
                        $Location_id = $Location->item(0)->getAttribute("id");
                        $Location_locCode = $Location->item(0)->getAttribute("locCode");
                        $Location_locName = $Location->item(0)->getAttribute("locName");
                        $Location_onAirport = $Location->item(0)->getAttribute("onAirport");
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