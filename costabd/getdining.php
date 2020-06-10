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
echo "COMECOU DINING<br/>";
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
      <Culture/>
    </Agency>
    <Partner xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Name>' . $agency . '</Name>
      <Password>' . $password . '</Password>
    </Partner>
  </soap:Header>
  <soap:Body>
    <GetDining xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <bookingNumber>26965789</bookingNumber>
    </GetDining>
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
    "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/GetDining",
    "Content-length: ".strlen($raw)
));
$url = "https://training.costaclick.net/WAWS_1_9/Booking.asmx";

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
$GetDiningResponse = $Body->item(0)->getElementsByTagName("GetDiningResponse");
if ($GetDiningResponse->length > 0) {
    $GetDiningResult = $GetDiningResponse->item(0)->getElementsByTagName("GetDiningResult");
    if ($GetDiningResult->length > 0) {
        $Cabin = $GetDiningResult->item(0)->getElementsByTagName("Cabin");
        if ($Cabin->length > 0) {
            $Number = $Cabin->item(0)->getElementsByTagName("Number");
            if ($Number->length > 0) {
                $Number = $Number->item(0)->nodeValue;
            } else {
                $Number = "";
            }
            $Status = $Cabin->item(0)->getElementsByTagName("Status");
            if ($Status->length > 0) {
                $Status = $Status->item(0)->nodeValue;
            } else {
                $Status = "";
            }
            $MinOccupancy = $Cabin->item(0)->getElementsByTagName("MinOccupancy");
            if ($MinOccupancy->length > 0) {
                $MinOccupancy = $MinOccupancy->item(0)->nodeValue;
            } else {
                $MinOccupancy = "";
            }
            $MaxOccupancy = $Cabin->item(0)->getElementsByTagName("MaxOccupancy");
            if ($MaxOccupancy->length > 0) {
                $MaxOccupancy = $MaxOccupancy->item(0)->nodeValue;
            } else {
                $MaxOccupancy = "";
            }
            $Facility = $Cabin->item(0)->getElementsByTagName("Facility");
            if ($Facility->length > 0) {
                $Facility = $Facility->item(0)->nodeValue;
            } else {
                $Facility = "";
            }
            $DiningSatisfaction = $Cabin->item(0)->getElementsByTagName("DiningSatisfaction");
            if ($DiningPreference->length > 0) {
                $DiningSatisfaction = $DiningSatisfaction->item(0)->nodeValue;
            } else {
                $DiningSatisfaction = "";
            }
            $Category = $Cabin->item(0)->getElementsByTagName("Category");
            if ($Category->length > 0) {
                $Code = $Category->item(0)->getElementsByTagName("Code");
                if ($Code->length > 0) {
                    $Code = $Code->item(0)->nodeValue;
                } else {
                    $Code = "";
                }
                $Name = $Category->item(0)->getElementsByTagName("Name");
                if ($Name->length > 0) {
                    $Name = $Name->item(0)->nodeValue;
                } else {
                    $Name = "";
                }
                $Availability = $Category->item(0)->getElementsByTagName("Availability");
                if ($Availability->length > 0) {
                    $Availability = $Availability->item(0)->nodeValue;
                } else {
                    $Availability = "";
                }
                $StatusCode = $Category->item(0)->getElementsByTagName("StatusCode");
                if ($StatusCode->length > 0) {
                    $StatusCode = $StatusCode->item(0)->nodeValue;
                } else {
                    $StatusCode = "";
                }
                $CabinLocation = $Category->item(0)->getElementsByTagName("CabinLocation");
                if ($CabinLocation->length > 0) {
                    $CabinLocation = $CabinLocation->item(0)->nodeValue;
                } else {
                    $CabinLocation = "";
                }
                $URL = $Category->item(0)->getElementsByTagName("URL");
                if ($URL->length > 0) {
                    $URL = $URL->item(0)->nodeValue;
                } else {
                    $URL = "";
                }
                $IsSingleCabin = $Category->item(0)->getElementsByTagName("IsSingleCabin");
                if ($IsSingleCabin->length > 0) {
                    $IsSingleCabin = $IsSingleCabin->item(0)->nodeValue;
                } else {
                    $IsSingleCabin = "";
                }
                $MaxOccupancy = $Category->item(0)->getElementsByTagName("MaxOccupancy");
                if ($MaxOccupancy->length > 0) {
                    $MaxOccupancy = $MaxOccupancy->item(0)->nodeValue;
                } else {
                    $MaxOccupancy = "";
                }
                $MinOccupancy = $Category->item(0)->getElementsByTagName("MinOccupancy");
                if ($MinOccupancy->length > 0) {
                    $MinOccupancy = $MinOccupancy->item(0)->nodeValue;
                } else {
                    $MinOccupancy = "";
                }
                $Order = $Category->item(0)->getElementsByTagName("Order");
                if ($Order->length > 0) {
                    $Order = $Order->item(0)->nodeValue;
                } else {
                    $Order = "";
                }
                $AdditionalDescription = $Category->item(0)->getElementsByTagName("AdditionalDescription");
                if ($AdditionalDescription->length > 0) {
                    $AdditionalDescription = $Order->item(0)->nodeValue;
                } else {
                    $AdditionalDescription = "";
                }
                $SuperCategoryType = $Category->item(0)->getElementsByTagName("SuperCategoryType");
                if ($SuperCategoryType->length > 0) {
                    $SuperCategoryType = $SuperCategoryType->item(0)->nodeValue;
                } else {
                    $SuperCategoryType = "";
                }
                $CurrencyCode = $Category->item(0)->getElementsByTagName("CurrencyCode");
                if ($CurrencyCode->length > 0) {
                    $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
                } else {
                    $CurrencyCode = "";
                }
                $Ship = $Category->item(0)->getElementsByTagName("Ship");
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
            }
            $Cruise = $Cabin->item(0)->getElementsByTagName("Cruise");
            if ($Cruise->length > 0) {
                $Code = $Cruise->item(0)->getElementsByTagName("Code");
                if ($Code->length > 0) {
                    $Code = $Code->item(0)->nodeValue;
                } else {
                    $Code = "";
                }
                $Description = $Cruise->item(0)->getElementsByTagName("Description");
                if ($Description->length > 0) {
                    $Description = $Description->item(0)->nodeValue;
                } else {
                    $Description = "";
                }
                $Availability = $Cruise->item(0)->getElementsByTagName("Availability");
                if ($Availability->length > 0) {
                    $Availability = $Availability->item(0)->nodeValue;
                } else {
                    $Availability = "";
                }
                $Sellability = $Cruise->item(0)->getElementsByTagName("Sellability");
                if ($Sellability->length > 0) {
                    $Sellability = $Sellability->item(0)->nodeValue;
                } else {
                    $Sellability = "";
                }
                $DepartureDate = $Cruise->item(0)->getElementsByTagName("DepartureDate");
                if ($DepartureDate->length > 0) {
                    $DepartureDate = $DepartureDate->item(0)->nodeValue;
                } else {
                    $DepartureDate = "";
                }
                $Duration = $Cruise->item(0)->getElementsByTagName("Duration");
                if ($Duration->length > 0) {
                    $Duration = $Duration->item(0)->nodeValue;
                } else {
                    $Duration = "";
                }
                $MaxOccupancy = $Cruise->item(0)->getElementsByTagName("MaxOccupancy");
                if ($MaxOccupancy->length > 0) {
                    $MaxOccupancy = $MaxOccupancy->item(0)->nodeValue;
                } else {
                    $MaxOccupancy = "";
                }
                $Destination = $Cruise->item(0)->getElementsByTagName("Destination");
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
                $DeparturePort = $Cruise->item(0)->getElementsByTagName("DeparturePort");
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
                $Ship = $Cruise->item(0)->getElementsByTagName("Ship");
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
                $ImmediateConfirm = $Cruise->item(0)->getElementsByTagName("ImmediateConfirm");
                if ($ImmediateConfirm->length > 0) {
                    $IsImmediateConfirm = $ImmediateConfirm->item(0)->getElementsByTagName("IsImmediateConfirm");
                    if ($IsImmediateConfirm->length > 0) {
                        $IsImmediateConfirm = $IsImmediateConfirm->item(0)->nodeValue;
                    } else {
                        $IsImmediateConfirm = "";
                    }
                }
            }
            $RestaurantInfo = $Cabin->item(0)->getElementsByTagName("RestaurantInfo");
            if ($RestaurantInfo->length > 0) {
                $TableSize = $RestaurantInfo->item(0)->getElementsByTagName("TableSize");
                if ($TableSize->length > 0) {
                    $TableSize = $TableSize->item(0)->nodeValue;
                } else {
                    $TableSize = "";
                }
                $DefaultRestaurant = $RestaurantInfo->item(0)->getElementsByTagName("DefaultRestaurant");
                if ($DefaultRestaurant->length > 0) {
                    $DefaultRestaurant = $DefaultRestaurant->item(0)->nodeValue;
                } else {
                    $DefaultRestaurant = "";
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