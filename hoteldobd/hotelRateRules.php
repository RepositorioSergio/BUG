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
echo "COMECOU RATE RULES";
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
$affiliate_id_hoteldo = 0;
$branch_filter = "";
$sql = "select value from settings where name='HotelDouser' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDouser = $row_settings['value'];
}
$sql = "select value from settings where name='HotelDoMarkup' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDoMarkup = (double) $row_settings['value'];
} else {
    $HotelDoMarkup = 0;
}
$sql = "select value from settings where name='HotelDoserviceURL ' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDoserviceURL = $row_settings['value'];
}
echo $return;
echo $HotelDoserviceURL;
echo $return;
    
$config = new \Zend\Config\Config(include '../config/autoload/global.getaroom.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$ipaddress = '';
if ($_SERVER['HTTP_CLIENT_IP']) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
} else if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else if ($_SERVER['HTTP_X_FORWARDED']) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
} else if ($_SERVER['HTTP_FORWARDED_FOR']) {
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
} else if ($_SERVER['HTTP_FORWARDED']) {
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
} else if ($_SERVER['REMOTE_ADDR']) {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
} else {
    $ipaddress = 'UNKNOWN';
    $ipaddress = "142.44.216.144";
}

$raw = '/GetHotelRateRules?a=' . $HotelDouser . '&ip=' . $ipaddress . '&co=MX&c=pe&d=2&l=esp&rk=U1RELVpaTUFZT1JJU1QxWlpBdXRvbWF0aWNNYWls&sd=20200208&ed=20200211&h=509&ci=1&mi=MAYORIST&it=BESTDAY&ri=STD&mp=ZZ&r1a=2&r1k=1&r1k1a=1&r2k2a=2';
echo $HotelDoserviceURL . $raw ."<br/>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $HotelDoserviceURL . $raw);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
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
$HotelRateRules = $inputDoc->getElementsByTagName("HotelRateRules");
if ($HotelRateRules->length > 0) {
    $HotelRate = $HotelRateRules->getElementsByTagName("HotelRate");
    if ($HotelRate->length > 0) {
        $Id = $HotelRate->item(0)->getElementsByTagName('Id');
        if ($Id->length > 0) {
            $Id = $Id->item(0)->nodeValue;
        } else {
            $Id = "";
        }
        $Name = $HotelRate->item(0)->getElementsByTagName('Name');
        if ($Name->length > 0) {
            $Name = $Name->item(0)->nodeValue;
        } else {
            $Name = "";
        }
        $Description = $HotelRate->item(0)->getElementsByTagName('Description');
        if ($Description->length > 0) {
            $Description = $Description->item(0)->nodeValue;
        } else {
            $Description = "";
        }
        $HotelPath = $HotelRate->item(0)->getElementsByTagName('HotelPath');
        if ($HotelPath->length > 0) {
            $HotelPath = $HotelPath->item(0)->nodeValue;
        } else {
            $HotelPath = "";
        }
        $CheckIn = $HotelRate->item(0)->getElementsByTagName('CheckIn');
        if ($CheckIn->length > 0) {
            $CheckIn = $CheckIn->item(0)->nodeValue;
        } else {
            $CheckIn = "";
        }
        $CheckOut = $HotelRate->item(0)->getElementsByTagName('CheckOut');
        if ($CheckOut->length > 0) {
            $CheckOut = $CheckOut->item(0)->nodeValue;
        } else {
            $CheckOut = "";
        }
        $TotalRooms = $HotelRate->item(0)->getElementsByTagName('TotalRooms');
        if ($TotalRooms->length > 0) {
            $TotalRooms = $TotalRooms->item(0)->nodeValue;
        } else {
            $TotalRooms = "";
        }
        $Image = $HotelRate->item(0)->getElementsByTagName('Image');
        if ($Image->length > 0) {
            $Image = $Image->item(0)->nodeValue;
        } else {
            $Image = "";
        }
        $AdultOnly = $HotelRate->item(0)->getElementsByTagName('AdultOnly');
        if ($AdultOnly->length > 0) {
            $AdultOnly = $AdultOnly->item(0)->nodeValue;
        } else {
            $AdultOnly = "";
        }
        $AdditionalCharges = $HotelRate->item(0)->getElementsByTagName('AdditionalCharges');
        if ($AdditionalCharges->length > 0) {
            $AdditionalCharges = $AdditionalCharges->item(0)->nodeValue;
        } else {
            $AdditionalCharges = "";
        }
        $Category = $HotelRate->item(0)->getElementsByTagName('Category');
        if ($Category->length > 0) {
            $Category = $Category->item(0)->nodeValue;
        } else {
            $Category = "";
        }
        $DestinationId = $HotelRate->item(0)->getElementsByTagName('DestinationId');
        if ($DestinationId->length > 0) {
            $DestinationId = $DestinationId->item(0)->nodeValue;
        } else {
            $DestinationId = "";
        }

        //Themes
        $Themes = $HotelRate->item(0)->getElementsByTagName('Themes');
        if ($Themes->length > 0) {
            $Theme = $Themes->item(0)->getElementsByTagName('Theme');
            if ($Theme->length > 0) {
                $ThemeId = $Theme->item(0)->getElementsByTagName('Id');
                if ($ThemeId->length > 0) {
                    $ThemeId = $ThemeId->item(0)->nodeValue;
                } else {
                    $ThemeId = "";
                }
            }
        }
        //AirportReference
        $AirportReference = $HotelRate->item(0)->getElementsByTagName('AirportReference');
        if ($AirportReference->length > 0) {
            $DistanceTo = $AirportReference->item(0)->getElementsByTagName('DistanceTo');
            if ($DistanceTo->length > 0) {
                $DistanceTo = $DistanceTo->item(0)->nodeValue;
            } else {
                $DistanceTo = "";
            }
            $MinutesTo = $AirportReference->item(0)->getElementsByTagName('MinutesTo');
            if ($MinutesTo->length > 0) {
                $MinutesTo = $MinutesTo->item(0)->nodeValue;
            } else {
                $MinutesTo = "";
            }
        }
        //Address
        $Address = $HotelRate->item(0)->getElementsByTagName('Address');
        if ($Address->length > 0) {
            $Neighborhood = $Address->item(0)->getElementsByTagName('Neighborhood');
            if ($Neighborhood->length > 0) {
                $Neighborhood = $Neighborhood->item(0)->nodeValue;
            } else {
                $Neighborhood = "";
            }
            $Street = $Address->item(0)->getElementsByTagName('Street');
            if ($Street->length > 0) {
                $Street = $Street->item(0)->nodeValue;
            } else {
                $Street = "";
            }
            $ZipCode = $Address->item(0)->getElementsByTagName('ZipCode');
            if ($ZipCode->length > 0) {
                $ZipCode = $ZipCode->item(0)->nodeValue;
            } else {
                $ZipCode = "";
            }
            //City
            $City = $Address->item(0)->getElementsByTagName('City');
            if ($City->length > 0) {
                $CityId = $City->item(0)->getElementsByTagName('Id');
                if ($CityId->length > 0) {
                    $CityId = $CityId->item(0)->nodeValue;
                } else {
                    $CityId = "";
                }
                $CityName = $City->item(0)->getElementsByTagName('Name');
                if ($CityName->length > 0) {
                    $CityName = $CityName->item(0)->nodeValue;
                } else {
                    $CityName = "";
                }
            }
            //Country
            $Country = $Address->item(0)->getElementsByTagName('Country');
            if ($Country->length > 0) {
                $CountryId = $Country->item(0)->getElementsByTagName('Id');
                if ($CountryId->length > 0) {
                    $CountryId = $CountryId->item(0)->nodeValue;
                } else {
                    $CountryId = "";
                }
                $CountryName = $Country->item(0)->getElementsByTagName('Name');
                if ($CountryName->length > 0) {
                    $CountryName = $CountryName->item(0)->nodeValue;
                } else {
                    $CountryName = "";
                }
            }
            //GeoLocation
            $GeoLocation = $Address->item(0)->getElementsByTagName('GeoLocation');
            if ($GeoLocation->length > 0) {
                $Distance = $GeoLocation->item(0)->getElementsByTagName('Distance');
                if ($Distance->length > 0) {
                    $Distance = $Distance->item(0)->nodeValue;
                } else {
                    $Distance = "";
                }
                $DistanceUnit = $GeoLocation->item(0)->getElementsByTagName('DistanceUnit');
                if ($DistanceUnit->length > 0) {
                    $DistanceUnit = $DistanceUnit->item(0)->nodeValue;
                } else {
                    $DistanceUnit = "";
                }
                $Latitude = $GeoLocation->item(0)->getElementsByTagName('Latitude');
                if ($Latitude->length > 0) {
                    $Latitude = $Latitude->item(0)->nodeValue;
                } else {
                    $Latitude = "";
                }
                $Longitude = $GeoLocation->item(0)->getElementsByTagName('Longitude');
                if ($Longitude->length > 0) {
                    $Longitude = $Longitude->item(0)->nodeValue;
                } else {
                    $Longitude = "";
                }
            }
            //State
            $State = $Address->item(0)->getElementsByTagName('State');
            if ($State->length > 0) {
                $StateId = $State->item(0)->getElementsByTagName('Id');
                if ($StateId->length > 0) {
                    $StateId = $StateId->item(0)->nodeValue;
                } else {
                    $StateId = "";
                }
                $StateName = $State->item(0)->getElementsByTagName('Name');
                if ($StateName->length > 0) {
                    $StateName = $StateName->item(0)->nodeValue;
                } else {
                    $StateName = "";
                }
            }
        }
        //Available
        $Available = $HotelRate->item(0)->getElementsByTagName('Available');
        if ($Available->length > 0) {
            $AvailableId = $Available->item(0)->getElementsByTagName('Id');
            if ($AvailableId->length > 0) {
                $AvailableId = $AvailableId->item(0)->nodeValue;
            } else {
                $AvailableId = "";
            }
            $Information = $Available->item(0)->getElementsByTagName('Information');
            if ($Information->length > 0) {
                $Information = $Information->item(0)->nodeValue;
            } else {
                $Information = "";
            }
            $Status = $Available->item(0)->getElementsByTagName('Status');
            if ($Status->length > 0) {
                $Status = $Status->item(0)->nodeValue;
            } else {
                $Status = "";
            }
        }
        //Contact
        $Contact = $HotelRate->item(0)->getElementsByTagName('Contact');
        if ($Contact->length > 0) {
            $Contact = $Contact->item(0)->getElementsByTagName('Contact');
            if ($Contact->length > 0) {
                $ContactId = $Contact->item(0)->getAttribute('Id');
                $ContactEmail = $Contact->item(0)->getAttribute('Email');
                $ContactAge = $Contact->item(0)->getAttribute('Age');
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