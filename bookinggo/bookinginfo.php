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
echo "COMECOU CONVERT QUOTE<br/>";
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

$url = "https://xmlsandbox.rentalcars.com/service/ServiceRequest.do";

$raw = '<BookingInfoRQ version="1.1">
<Credentials username="club1hot944" password="club1hot944" remoteIp="91.151.7.6"/>
<Booking id="585225261"></Booking>
<Email>example@exampleemail.com</Email>
</BookingInfoRQ>';

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
$BookingInfoRS = $inputDoc->getElementsByTagName("BookingInfoRS");
$Booking = $BookingInfoRS->item(0)->getElementsByTagName("Booking");
if ($Booking->length > 0) {
    $id = $Booking->item(0)->getAttribute("id");
    $AirlineInfo = $Booking->item(0)->getElementsByTagName("AirlineInfo");
    if ($AirlineInfo->length > 0) {
        $AirlineInfo = $AirlineInfo->item(0)->nodeValue;
    } else {
        $AirlineInfo = "";
    }
    $PickUp = $Booking->item(0)->getElementsByTagName("PickUp");
    if ($PickUp->length > 0) {
        $Location = $PickUp->item(0)->getElementsByTagName("Location");
        if ($Location->length > 0) {
            $Location_id = $Location->item(0)->getAttribute("id");
            $Location_country = $Location->item(0)->getAttribute("country");
            $Location_city = $Location->item(0)->getAttribute("city");
            $Location_onAirport = $Location->item(0)->getAttribute("onAirport");
            $Location_address = $Location->item(0)->getAttribute("address");
            $Location_telephone = $Location->item(0)->getAttribute("telephone");
            $Location = $Location->item(0)->nodeValue;
        } else {
            $Location = "";
        }
        $Date = $PickUp->item(0)->getElementsByTagName("Date");
        if ($Date->length > 0) {
            $year = $Date->item(0)->getAttribute("year");
            $month = $Date->item(0)->getAttribute("month");
            $day = $Date->item(0)->getAttribute("day");
            $hour = $Date->item(0)->getAttribute("hour");
            $minute = $Date->item(0)->getAttribute("minute");
        }
    }
    $DropOff = $Booking->item(0)->getElementsByTagName("DropOff");
    if ($DropOff->length > 0) {
        $Location = $DropOff->item(0)->getElementsByTagName("Location");
        if ($Location->length > 0) {
            $Location_id = $Location->item(0)->getAttribute("id");
            $Location_country = $Location->item(0)->getAttribute("country");
            $Location_city = $Location->item(0)->getAttribute("city");
            $Location_onAirport = $Location->item(0)->getAttribute("onAirport");
            $Location_address = $Location->item(0)->getAttribute("address");
            $Location_telephone = $Location->item(0)->getAttribute("telephone");
            $Location = $Location->item(0)->nodeValue;
        } else {
            $Location = "";
        }
        $Date = $DropOff->item(0)->getElementsByTagName("Date");
        if ($Date->length > 0) {
            $year = $Date->item(0)->getAttribute("year");
            $month = $Date->item(0)->getAttribute("month");
            $day = $Date->item(0)->getAttribute("day");
            $hour = $Date->item(0)->getAttribute("hour");
            $minute = $Date->item(0)->getAttribute("minute");
        }
    }
    $Vehicle = $Booking->item(0)->getElementsByTagName("Vehicle");
    if ($Vehicle->length > 0) {
        $id = $Vehicle->item(0)->getAttribute("id");
        $automatic = $Vehicle->item(0)->getAttribute("automatic");
        $aircon = $Vehicle->item(0)->getAttribute("aircon");
        $airbag = $Vehicle->item(0)->getAttribute("airbag");
        $petrol = $Vehicle->item(0)->getAttribute("petrol");
        $group = $Vehicle->item(0)->getAttribute("group");
        $doors = $Vehicle->item(0)->getAttribute("doors");
        $seats = $Vehicle->item(0)->getAttribute("seats");
        $fuelPolicy = $Vehicle->item(0)->getAttribute("fuelPolicy");
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
        $Description = $Vehicle->item(0)->getElementsByTagName("Description");
        if ($Description->length > 0) {
            $Description = $Description->item(0)->nodeValue;
        } else {
            $Description = "";
        }
    }
    $Supplier = $Booking->item(0)->getElementsByTagName("Supplier");
    if ($Supplier->length > 0) {
        $accountNumber = $Supplier->item(0)->getAttribute("accountNumber");
        $confirmationNumber = $Supplier->item(0)->getAttribute("confirmationNumber");
        $Supplier = $Supplier->item(0)->nodeValue;
    } else {
        $Supplier = "";
    }
    $DriverInfo = $Booking->item(0)->getElementsByTagName("DriverInfo");
    if ($DriverInfo->length > 0) {
        $Email = $DriverInfo->item(0)->getElementsByTagName("Email");
        if ($Email->length > 0) {
            $Email = $Email->item(0)->nodeValue;
        } else {
            $Email = "";
        }
        $Telephone = $DriverInfo->item(0)->getElementsByTagName("Telephone");
        if ($Telephone->length > 0) {
            $Telephone = $Telephone->item(0)->nodeValue;
        } else {
            $Telephone = "";
        }
        $Fax = $DriverInfo->item(0)->getElementsByTagName("Fax");
        if ($Fax->length > 0) {
            $Fax = $Fax->item(0)->nodeValue;
        } else {
            $Fax = "";
        }
        $DriverName = $DriverInfo->item(0)->getElementsByTagName("DriverName");
        if ($DriverName->length > 0) {
            $title = $DriverName->item(0)->getAttribute("title");
            $firstname = $DriverName->item(0)->getAttribute("firstname");
            $lastname = $DriverName->item(0)->getAttribute("lastname");
        }
        $Address = $DriverInfo->item(0)->getElementsByTagName("Address");
        if ($Address->length > 0) {
            $country = $Address->item(0)->getAttribute("country");
            $city = $Address->item(0)->getAttribute("city");
            $street = $Address->item(0)->getAttribute("street");
            $postcode = $Address->item(0)->getAttribute("postcode");
        }
    }
    $AdditionalInfo = $Booking->item(0)->getElementsByTagName("AdditionalInfo");
    if ($AdditionalInfo->length > 0) {
        $Comments = $AdditionalInfo->item(0)->getElementsByTagName("Comments");
        if ($Comments->length > 0) {
            $Comments = $Comments->item(0)->nodeValue;
        } else {
            $Comments = "";
        }
        $PickUpService = $AdditionalInfo->item(0)->getElementsByTagName("PickUpService");
        if ($PickUpService->length > 0) {
            $PickUpService = $PickUpService->item(0)->nodeValue;
        } else {
            $PickUpService = "";
        }
        $DropOffService = $AdditionalInfo->item(0)->getElementsByTagName("DropOffService");
        if ($DropOffService->length > 0) {
            $DropOffService = $DropOffService->item(0)->nodeValue;
        } else {
            $DropOffService = "";
        }
    }
    $Price = $Booking->item(0)->getElementsByTagName("Price");
    if ($Price->length > 0) {
        $currency = $Price->item(0)->getAttribute("currency");
        $deposit = $Price->item(0)->getAttribute("deposit");
        $discount = $Price->item(0)->getAttribute("discount");
        $Price = $Price->item(0)->nodeValue;
    } else {
        $Price = "";
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>