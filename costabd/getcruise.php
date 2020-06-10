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
echo "COMECOU GET CRUISE<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$code = '38290196';
$agency = 'Costamar';
$password = 'C0sT2m2R';
$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <Agency xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Code>' . $code . '</Code>
    </Agency>
    <Partner xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Name>' . $agency . '</Name>
      <Password>' . $password . '</Password>
    </Partner>
  </soap:Header>
  <soap:Body>
    <GetCruise xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <CruiseCode>PA07201001</CruiseCode>
    </GetCruise>
  </soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/GetCruise",
    "Content-length: ".strlen($raw)
));
$url = "https://training.costaclick.net/WAWS_1_9/Availability.asmx";

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
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
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
$GetCruiseResponse = $Body->item(0)->getElementsByTagName("GetCruiseResponse");
if ($GetCruiseResponse->length > 0) {
    $GetCruiseResult = $GetCruiseResponse->item(0)->getElementsByTagName("GetCruiseResult");
    if ($GetCruiseResult->length > 0) {
        $Code = $GetCruiseResult->item(0)->getElementsByTagName("Code");
        if ($Code->length > 0) {
            $Code = $Code->item(0)->nodeValue;
        } else {
            $Code = "";
        }
        $Description = $GetCruiseResult->item(0)->getElementsByTagName("Description");
        if ($Description->length > 0) {
            $Description = $Description->item(0)->nodeValue;
        } else {
            $Description = "";
        }
        $Availability = $GetCruiseResult->item(0)->getElementsByTagName("Availability");
        if ($Availability->length > 0) {
            $Availability = $Availability->item(0)->nodeValue;
        } else {
            $Availability = "";
        }
        $Sellability = $GetCruiseResult->item(0)->getElementsByTagName("Sellability");
        if ($Sellability->length > 0) {
            $Sellability = $Sellability->item(0)->nodeValue;
        } else {
            $Sellability = "";
        }
        $DepartureDate = $GetCruiseResult->item(0)->getElementsByTagName("DepartureDate");
        if ($DepartureDate->length > 0) {
            $DepartureDate = $DepartureDate->item(0)->nodeValue;
        } else {
            $DepartureDate = "";
        }
        $Duration = $GetCruiseResult->item(0)->getElementsByTagName("Duration");
        if ($Duration->length > 0) {
            $Duration = $Duration->item(0)->nodeValue;
        } else {
            $Duration = "";
        }
        $MaxOccupancy = $GetCruiseResult->item(0)->getElementsByTagName("MaxOccupancy");
        if ($MaxOccupancy->length > 0) {
            $MaxOccupancy = $MaxOccupancy->item(0)->nodeValue;
        } else {
            $MaxOccupancy = "";
        }
        $Destination = $GetCruiseResult->item(0)->getElementsByTagName("Destination");
        if ($Destination->length > 0) {
            $DestinationCode = $Destination->item(0)->getElementsByTagName("Code");
            if ($DestinationCode->length > 0) {
                $DestinationCode = $DestinationCode->item(0)->nodeValue;
            } else {
                $DestinationCode = "";
            }
            $DestinationDescription = $Destination->item(0)->getElementsByTagName("Description");
            if ($DestinationDescription->length > 0) {
                $DestinationDescription = $DestinationDescription->item(0)->nodeValue;
            } else {
                $DestinationDescription = "";
            }
        }
        $DeparturePort = $GetCruiseResult->item(0)->getElementsByTagName("DeparturePort");
        if ($DeparturePort->length > 0) {
            $DeparturePortCode = $DeparturePort->item(0)->getElementsByTagName("Code");
            if ($DeparturePortCode->length > 0) {
                $DeparturePortCode = $DeparturePortCode->item(0)->nodeValue;
            } else {
                $DeparturePortCode = "";
            }
            $DeparturePortDescription = $DeparturePort->item(0)->getElementsByTagName("Description");
            if ($DeparturePortDescription->length > 0) {
                $DeparturePortDescription = $DeparturePortDescription->item(0)->nodeValue;
            } else {
                $DeparturePortDescription = "";
            }
        }
        $ArrivalPort = $GetCruiseResult->item(0)->getElementsByTagName("ArrivalPort");
        if ($ArrivalPort->length > 0) {
            $ArrivalPortCode = $ArrivalPort->item(0)->getElementsByTagName("Code");
            if ($ArrivalPortCode->length > 0) {
                $ArrivalPortCode = $ArrivalPortCode->item(0)->nodeValue;
            } else {
                $ArrivalPortCode = "";
            }
            $ArrivalPortDescription = $ArrivalPort->item(0)->getElementsByTagName("Description");
            if ($ArrivalPortDescription->length > 0) {
                $ArrivalPortDescription = $ArrivalPortDescription->item(0)->nodeValue;
            } else {
                $ArrivalPortDescription = "";
            }
        }
        $Ship = $GetCruiseResult->item(0)->getElementsByTagName("Ship");
        if ($Ship->length > 0) {
            $ShipCode = $Ship->item(0)->getElementsByTagName("Code");
            if ($ShipCode->length > 0) {
                $ShipCode = $ShipCode->item(0)->nodeValue;
            } else {
                $ShipCode = "";
            }
            $ShipName = $Ship->item(0)->getElementsByTagName("Name");
            if ($ShipName->length > 0) {
                $ShipName = $ShipName->item(0)->nodeValue;
            } else {
                $ShipName = "";
            }
            $ShipURL = $Ship->item(0)->getElementsByTagName("URL");
            if ($ShipURL->length > 0) {
                $ShipURL = $ShipURL->item(0)->nodeValue;
            } else {
                $ShipURL = "";
            }
            $ShipCabins = $Ship->item(0)->getElementsByTagName("Cabins");
            if ($ShipCabins->length > 0) {
                $ShipCabins = $ShipCabins->item(0)->nodeValue;
            } else {
                $ShipCabins = "";
            }
            $ShipCrew = $Ship->item(0)->getElementsByTagName("Crew");
            if ($ShipCrew->length > 0) {
                $ShipCrew = $ShipCrew->item(0)->nodeValue;
            } else {
                $ShipCrew = "";
            }
            $ShipGuests = $Ship->item(0)->getElementsByTagName("Guests");
            if ($ShipGuests->length > 0) {
                $ShipGuests = $ShipGuests->item(0)->nodeValue;
            } else {
                $ShipGuests = "";
            }
            $ShipWidth = $Ship->item(0)->getElementsByTagName("Width");
            if ($ShipWidth->length > 0) {
                $ShipWidth = $ShipWidth->item(0)->nodeValue;
            } else {
                $ShipWidth = "";
            }
            $ShipLength = $Ship->item(0)->getElementsByTagName("Length");
            if ($ShipLength->length > 0) {
                $ShipLength = $ShipLength->item(0)->nodeValue;
            } else {
                $ShipLength = "";
            }
            $ShipTonnage = $Ship->item(0)->getElementsByTagName("Tonnage");
            if ($ShipTonnage->length > 0) {
                $ShipTonnage = $ShipTonnage->item(0)->nodeValue;
            } else {
                $ShipTonnage = "";
            }
            $ShipMaxSpeed = $Ship->item(0)->getElementsByTagName("MaxSpeed");
            if ($ShipMaxSpeed->length > 0) {
                $ShipMaxSpeed = $ShipMaxSpeed->item(0)->nodeValue;
            } else {
                $ShipMaxSpeed = "";
            }
            $ShipYearOfLaunch = $Ship->item(0)->getElementsByTagName("YearOfLaunch");
            if ($ShipYearOfLaunch->length > 0) {
                $ShipYearOfLaunch = $ShipYearOfLaunch->item(0)->nodeValue;
            } else {
                $ShipYearOfLaunch = "";
            }
            $ShipMonthOfLaunch = $Ship->item(0)->getElementsByTagName("MonthOfLaunch");
            if ($ShipMonthOfLaunch->length > 0) {
                $ShipMonthOfLaunch = $ShipMonthOfLaunch->item(0)->nodeValue;
            } else {
                $ShipMonthOfLaunch = "";
            }
        }
        $ImmediateConfirm = $GetCruiseResult->item(0)->getElementsByTagName("ImmediateConfirm");
        if ($ImmediateConfirm->length > 0) {
            $IsImmediateConfirm = $ImmediateConfirm->item(0)->getElementsByTagName("IsImmediateConfirm");
            if ($IsImmediateConfirm->length > 0) {
                $IsImmediateConfirm = $IsImmediateConfirm->item(0)->nodeValue;
            } else {
                $IsImmediateConfirm = "";
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