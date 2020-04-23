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
echo "COMECOU HOTEL INFORMATION";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.hoteldo.php');
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

$url = 'http://xml.e-tsw.com/AffiliateService/V1.0/AffiliateService.svc/restful/GetHotelInformation?a=DIVISAXML&ip=' . $ipaddress . '&c=us&l=esp&h=5&hash=ha:true';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo $return;
echo $error;
echo $return;

echo "<xmp>";
var_dump($response);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.hoteldo.php');
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
$HotelInformation = $inputDoc->getElementsByTagName("HotelInformation");
if ($HotelInformation->length > 0) {
    $Hotel = $HotelInformation->item(0)->getElementsByTagName("Hotel");
    if ($Hotel->length > 0) {
        $Id = $Hotel->item(0)->getElementsByTagName('Id');
        if ($Id->length > 0) {
            $Id = $Id->item(0)->nodeValue;
        } else {
            $Id = "";
        }
        $Name = $Hotel->item(0)->getElementsByTagName('Name');
        if ($Name->length > 0) {
            $Name = $Name->item(0)->nodeValue;
        } else {
            $Name = "";
        }
        $Description = $Hotel->item(0)->getElementsByTagName('Description');
        if ($Description->length > 0) {
            $Description = $Description->item(0)->nodeValue;
        } else {
            $Description = "";
        }
        $HotelPath = $Hotel->item(0)->getElementsByTagName('HotelPath');
        if ($HotelPath->length > 0) {
            $HotelPath = $HotelPath->item(0)->nodeValue;
        } else {
            $HotelPath = "";
        }
        $ThumbnailUrl = $Hotel->item(0)->getElementsByTagName('ThumbnailUrl');
        if ($ThumbnailUrl->length > 0) {
            $ThumbnailUrl = $ThumbnailUrl->item(0)->nodeValue;
        } else {
            $ThumbnailUrl = "";
        }
        $ShortDescription = $Hotel->item(0)->getElementsByTagName('ShortDescription');
        if ($ShortDescription->length > 0) {
            $ShortDescription = $ShortDescription->item(0)->nodeValue;
        } else {
            $ShortDescription = "";
        }
        $CheckIn = $Hotel->item(0)->getElementsByTagName('CheckIn');
        if ($CheckIn->length > 0) {
            $CheckIn = $CheckIn->item(0)->nodeValue;
        } else {
            $CheckIn = "";
        }
        $CheckOut = $Hotel->item(0)->getElementsByTagName('CheckOut');
        if ($CheckOut->length > 0) {
            $CheckOut = $CheckOut->item(0)->nodeValue;
        } else {
            $CheckOut = "";
        }
        $TotalRooms = $Hotel->item(0)->getElementsByTagName('TotalRooms');
        if ($TotalRooms->length > 0) {
            $TotalRooms = $TotalRooms->item(0)->nodeValue;
        } else {
            $TotalRooms = "";
        }
        $Image = $Hotel->item(0)->getElementsByTagName('Image');
        if ($Image->length > 0) {
            $Image = $Image->item(0)->nodeValue;
        } else {
            $Image = "";
        }
        $AdultOnly = $Hotel->item(0)->getElementsByTagName('AdultOnly');
        if ($AdultOnly->length > 0) {
            $AdultOnly = $AdultOnly->item(0)->nodeValue;
        } else {
            $AdultOnly = "";
        }
        $DestinationPath = $Hotel->item(0)->getElementsByTagName('DestinationPath');
        if ($DestinationPath->length > 0) {
            $DestinationPath = $DestinationPath->item(0)->nodeValue;
        } else {
            $DestinationPath = "";
        }
        $AdditionalCharges = $Hotel->item(0)->getElementsByTagName('AdditionalCharges');
        if ($AdditionalCharges->length > 0) {
            $AdditionalCharges = $AdditionalCharges->item(0)->nodeValue;
        } else {
            $AdditionalCharges = "";
        }
        $Category = $Hotel->item(0)->getElementsByTagName('Category');
        if ($Category->length > 0) {
            $Category = $Category->item(0)->nodeValue;
        } else {
            $Category = "";
        }
        $DestinationId = $Hotel->item(0)->getElementsByTagName('DestinationId');
        if ($DestinationId->length > 0) {
            $DestinationId = $DestinationId->item(0)->nodeValue;
        } else {
            $DestinationId = "";
        }
        $Chain = $Hotel->item(0)->getElementsByTagName('Chain');
        if ($Chain->length > 0) {
            $ChainId = $Chain->item(0)->getElementsByTagName('Id');
            if ($ChainId->length > 0) {
                $ChainId = $ChainId->item(0)->nodeValue;
            } else {
                $ChainId = "";
            }
            $ChainName = $Chain->item(0)->getElementsByTagName('Name');
            if ($ChainName->length > 0) {
                $ChainName = $ChainName->item(0)->nodeValue;
            } else {
                $ChainName = "";
            }
            $ChainPath = $Chain->item(0)->getElementsByTagName('Path');
            if ($ChainPath->length > 0) {
                $ChainPath = $ChainPath->item(0)->nodeValue;
            } else {
                $ChainPath = "";
            }
        }
        $Address = $Hotel->item(0)->getElementsByTagName('Address');
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
        }
        $AirportReference = $Hotel->item(0)->getElementsByTagName('AirportReference');
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
        $Location = $Hotel->item(0)->getElementsByTagName('Location');
        if ($Location->length > 0) {
            $LocationId = $Location->item(0)->getElementsByTagName('Id');
            if ($LocationId->length > 0) {
                $LocationId = $LocationId->item(0)->nodeValue;
            } else {
                $LocationId = "";
            }
            $LocationName = $Location->item(0)->getElementsByTagName('Name');
            if ($LocationName->length > 0) {
                $LocationName = $LocationName->item(0)->nodeValue;
            } else {
                $LocationName = "";
            }
            $LocationDescription = $Location->item(0)->getElementsByTagName('Description');
            if ($LocationDescription->length > 0) {
                $LocationDescription = $LocationDescription->item(0)->nodeValue;
            } else {
                $LocationDescription = "";
            }
        } 
        $Activities = $Hotel->item(0)->getElementsByTagName('Activities');
        if ($Activities->length > 0) {
            $Activity = $Activities->item(0)->getElementsByTagName('Activity');
            if ($Activity->length > 0) {
                $ActivityId = $Activity->item(0)->getElementsByTagName('Id');
                if ($ActivityId->length > 0) {
                    $ActivityId = $ActivityId->item(0)->nodeValue;
                } else {
                    $ActivityId = "";
                }
                $ActivityName = $Activity->item(0)->getElementsByTagName('Name');
                if ($ActivityName->length > 0) {
                    $ActivityName = $ActivityName->item(0)->nodeValue;
                } else {
                    $ActivityName = "";
                }
                $ActivityDescription = $Activity->item(0)->getElementsByTagName('Description');
                if ($ActivityDescription->length > 0) {
                    $ActivityDescription = $ActivityDescription->item(0)->nodeValue;
                } else {
                    $ActivityDescription = "";
                }
                $ActivityExtraCharge = $Activity->item(0)->getElementsByTagName('ExtraCharge');
                if ($ActivityExtraCharge->length > 0) {
                    $ActivityExtraCharge = $ActivityExtraCharge->item(0)->nodeValue;
                } else {
                    $ActivityExtraCharge = "";
                }
                $ActivityOrder = $Activity->item(0)->getElementsByTagName('Order');
                if ($ActivityOrder->length > 0) {
                    $ActivityOrder = $ActivityOrder->item(0)->nodeValue;
                } else {
                    $ActivityOrder = "";
                }
            }
        }
        $AllInclusive = $Hotel->item(0)->getElementsByTagName('AllInclusive');
        if ($AllInclusive->length > 0) {
            $MealsDescription = $AllInclusive->item(0)->getElementsByTagName('MealsDescription');
            if ($MealsDescription->length > 0) {
                $MealsDescription = $MealsDescription->item(0)->nodeValue;
            } else {
                $MealsDescription = "";
            }
            $BeverageDescription = $AllInclusive->item(0)->getElementsByTagName('BeverageDescription');
            if ($BeverageDescription->length > 0) {
                $BeverageDescription = $BeverageDescription->item(0)->nodeValue;
            } else {
                $BeverageDescription = "";
            }
            $ActivitiesDescription = $AllInclusive->item(0)->getElementsByTagName('ActivitiesDescription');
            if ($ActivitiesDescription->length > 0) {
                $ActivitiesDescription = $ActivitiesDescription->item(0)->nodeValue;
            } else {
                $ActivitiesDescription = "";
            }
            $EntertainmentDescription = $AllInclusive->item(0)->getElementsByTagName('EntertainmentDescription');
            if ($EntertainmentDescription->length > 0) {
                $EntertainmentDescription = $EntertainmentDescription->item(0)->nodeValue;
            } else {
                $EntertainmentDescription = "";
            }
            $ServicesDescription = $AllInclusive->item(0)->getElementsByTagName('ServicesDescription');
            if ($ServicesDescription->length > 0) {
                $ServicesDescription = $ServicesDescription->item(0)->nodeValue;
            } else {
                $ServicesDescription = "";
            }
            $Taxes = $AllInclusive->item(0)->getElementsByTagName('Taxes');
            if ($Taxes->length > 0) {
                $Taxes = $Taxes->item(0)->nodeValue;
            } else {
                $Taxes = "";
            }
            $Limitations = $AllInclusive->item(0)->getElementsByTagName('Limitations');
            if ($Limitations->length > 0) {
                $Limitations = $Limitations->item(0)->nodeValue;
            } else {
                $Limitations = "";
            }
        }
        $Reviews = $Hotel->item(0)->getElementsByTagName('Reviews');
        if ($Reviews->length > 0) {
            $Review = $Reviews->item(0)->getElementsByTagName('Review');
            if ($Review->length > 0) {
                $Rating = $Review->item(0)->getElementsByTagName('Rating');
                if ($Rating->length > 0) {
                    $Rating = $Rating->item(0)->nodeValue;
                } else {
                    $Rating = "";
                }
                $Source = $Review->item(0)->getElementsByTagName('Source');
                if ($Source->length > 0) {
                    $Source = $Source->item(0)->nodeValue;
                } else {
                    $Source = "";
                }
                $Count = $Review->item(0)->getElementsByTagName('Count');
                if ($Count->length > 0) {
                    $Count = $Count->item(0)->nodeValue;
                } else {
                    $Count = "";
                }
            }
        }

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('hotelinformation');
            $select->where(array(
                'id' => $Id
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = (int)$data['id'];
                if ($id > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'id' => $Id,
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'name' => $Name,
                        'hotelpath' => $HotelPath,
                        'thumbnailurl' => $ThumbnailUrl,
                        'description' => $Description,
                        'shortdescription' => $ShortDescription,
                        'checkin' => $CheckIn,
                        'checkout' => $CheckOut,
                        'totalrooms' => $TotalRooms,
                        'image' => $Image,
                        'adultonly' => $AdultOnly,
                        'destinationpath' => $DestinationPath,
                        'additionalcharges' => $AdditionalCharges,
                        'category' => $Category,
                        'destinationid' => $DestinationId,
                        'chainid' => $ChainId,
                        'chainname' => $ChainName,
                        'chainpath' => $ChainPath,
                        'neighborhood' => $Neighborhood,
                        'street' => $Street,
                        'zipcode' => $ZipCode,
                        'cityid' => $CityId,
                        'cityname' => $CityName,
                        'countryid' => $CountryId,
                        'countryname' => $CountryName,
                        'stateid' => $StateId,
                        'statename' => $StateName,
                        'distance' => $Distance,
                        'distanceunit' => $DistanceUnit,
                        'latitude' => $Latitude,
                        'longitude' => $Longitude,
                        'distanceto' => $DistanceTo,
                        'minutesto' => $MinutesTo,
                        'locationid' => $LocationId,
                        'locationname' => $LocationName,
                        'locationdescription' => $LocationDescription,
                        'activityid' => $ActivityId,
                        'activityname' => $ActivityName,
                        'activitydescription' => $ActivityDescription,
                        'activityextracharge' => $ActivityExtraCharge,
                        'activityorder' => $ActivityOrder,
                        'mealsdescription' => $MealsDescription,
                        'beveragedescription' => $BeverageDescription,
                        'activitiesdescription' => $ActivitiesDescription,
                        'entertainmentdescription' => $EntertainmentDescription,
                        'servicesdescription' => $ServicesDescription,
                        'taxes' => $Taxes,
                        'limitations' => $Limitations,
                        'rating' => $Rating,
                        'source' => $Source,
                        'count' => $Count  
                    );
                    $where['id = ?'] = $Id;
                    $update = $sql->update('hotelinformation', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hotelinformation');
                    $insert->values(array(
                        'id' => $Id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'name' => $Name,
                        'hotelpath' => $HotelPath,
                        'thumbnailurl' => $ThumbnailUrl,
                        'description' => $Description,
                        'shortdescription' => $ShortDescription,
                        'checkin' => $CheckIn,
                        'checkout' => $CheckOut,
                        'totalrooms' => $TotalRooms,
                        'image' => $Image,
                        'adultonly' => $AdultOnly,
                        'destinationpath' => $DestinationPath,
                        'additionalcharges' => $AdditionalCharges,
                        'category' => $Category,
                        'destinationid' => $DestinationId,
                        'chainid' => $ChainId,
                        'chainname' => $ChainName,
                        'chainpath' => $ChainPath,
                        'neighborhood' => $Neighborhood,
                        'street' => $Street,
                        'zipcode' => $ZipCode,
                        'cityid' => $CityId,
                        'cityname' => $CityName,
                        'countryid' => $CountryId,
                        'countryname' => $CountryName,
                        'stateid' => $StateId,
                        'statename' => $StateName,
                        'distance' => $Distance,
                        'distanceunit' => $DistanceUnit,
                        'latitude' => $Latitude,
                        'longitude' => $Longitude,
                        'distanceto' => $DistanceTo,
                        'minutesto' => $MinutesTo,
                        'locationid' => $LocationId,
                        'locationname' => $LocationName,
                        'locationdescription' => $LocationDescription,
                        'activityid' => $ActivityId,
                        'activityname' => $ActivityName,
                        'activitydescription' => $ActivityDescription,
                        'activityextracharge' => $ActivityExtraCharge,
                        'activityorder' => $ActivityOrder,
                        'mealsdescription' => $MealsDescription,
                        'beveragedescription' => $BeverageDescription,
                        'activitiesdescription' => $ActivitiesDescription,
                        'entertainmentdescription' => $EntertainmentDescription,
                        'servicesdescription' => $ServicesDescription,
                        'taxes' => $Taxes,
                        'limitations' => $Limitations,
                        'rating' => $Rating,
                        'source' => $Source,
                        'count' => $Count  
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                }
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hotelinformation');
                $insert->values(array(
                    'id' => $Id,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'name' => $Name,
                    'hotelpath' => $HotelPath,
                    'thumbnailurl' => $ThumbnailUrl,
                    'description' => $Description,
                    'shortdescription' => $ShortDescription,
                    'checkin' => $CheckIn,
                    'checkout' => $CheckOut,
                    'totalrooms' => $TotalRooms,
                    'image' => $Image,
                    'adultonly' => $AdultOnly,
                    'destinationpath' => $DestinationPath,
                    'additionalcharges' => $AdditionalCharges,
                    'category' => $Category,
                    'destinationid' => $DestinationId,
                    'chainid' => $ChainId,
                    'chainname' => $ChainName,
                    'chainpath' => $ChainPath,
                    'neighborhood' => $Neighborhood,
                    'street' => $Street,
                    'zipcode' => $ZipCode,
                    'cityid' => $CityId,
                    'cityname' => $CityName,
                    'countryid' => $CountryId,
                    'countryname' => $CountryName,
                    'stateid' => $StateId,
                    'statename' => $StateName,
                    'distance' => $Distance,
                    'distanceunit' => $DistanceUnit,
                    'latitude' => $Latitude,
                    'longitude' => $Longitude,
                    'distanceto' => $DistanceTo,
                    'minutesto' => $MinutesTo,
                    'locationid' => $LocationId,
                    'locationname' => $LocationName,
                    'locationdescription' => $LocationDescription,
                    'activityid' => $ActivityId,
                    'activityname' => $ActivityName,
                    'activitydescription' => $ActivityDescription,
                    'activityextracharge' => $ActivityExtraCharge,
                    'activityorder' => $ActivityOrder,
                    'mealsdescription' => $MealsDescription,
                    'beveragedescription' => $BeverageDescription,
                    'activitiesdescription' => $ActivitiesDescription,
                    'entertainmentdescription' => $EntertainmentDescription,
                    'servicesdescription' => $ServicesDescription,
                    'taxes' => $Taxes,
                    'limitations' => $Limitations,
                    'rating' => $Rating,
                    'source' => $Source,
                    'count' => $Count   
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            }
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO 1: ". $e;
            echo $return;
        }

        $Bars = $Hotel->item(0)->getElementsByTagName('Bars');
        if ($Bars->length > 0) {
            $Bar = $Bars->item(0)->getElementsByTagName('Bar');
            if ($Bar->length > 0) {
                for ($i=0; $i < $Bar->length; $i++) { 
                    $BarId = $Bar->item($i)->getElementsByTagName('Id');
                    if ($BarId->length > 0) {
                        $BarId = $BarId->item(0)->nodeValue;
                    } else {
                        $BarId = "";
                    }
                    $Name = $Bar->item($i)->getElementsByTagName('Name');
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    $Description = $Bar->item($i)->getElementsByTagName('Description');
                    if ($Description->length > 0) {
                        $Description = $Description->item(0)->nodeValue;
                    } else {
                        $Description = "";
                    }
                    $Schedule = $Bar->item($i)->getElementsByTagName('Schedule');
                    if ($Schedule->length > 0) {
                        $Schedule = $Schedule->item(0)->nodeValue;
                    } else {
                        $Schedule = "";
                    }
                    $Image = $Bar->item($i)->getElementsByTagName('Image');
                    if ($Image->length > 0) {
                        $Image = $Image->item(0)->nodeValue;
                    } else {
                        $Image = "";
                    }
    
                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('hotelinformation_bars');
                        $select->where(array(
                            'id' => $BarId
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $id = (string)$data['id'];
                            if ($id != "") {
                                $sql = new Sql($db);
                                $data = array(
                                    'id' => $BarId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 1,
                                    'name' => $Name,
                                    'description' => $Description,
                                    'schedule' => $Schedule,
                                    'image' => $Image,
                                    'hotelid' => $Id 
                                );
                                $where['id = ?'] = $BarId;
                                $update = $sql->update('hotelinformation_bars', $data, $where);
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('hotelinformation_bars');
                                $insert->values(array(
                                    'id' => $BarId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name,
                                    'description' => $Description,
                                    'schedule' => $Schedule,
                                    'image' => $Image,
                                    'hotelid' => $Id 
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            }
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hotelinformation_bars');
                            $insert->values(array(
                                'id' => $BarId,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $Name,
                                'description' => $Description,
                                'schedule' => $Schedule,
                                'image' => $Image,
                                'hotelid' => $Id,
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        }
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 2: ". $e;
                        echo $return;
                    }
                }
            }
        }
        $Restaurants = $Hotel->item(0)->getElementsByTagName('Restaurants');
        if ($Restaurants->length > 0) {
            $Restaurant = $Restaurants->item(0)->getElementsByTagName('Restaurant');
            if ($Restaurant->length > 0) {
                for ($r=0; $r < $Restaurant->length; $r++) { 
                    $RestaurantId = $Restaurant->item($r)->getElementsByTagName('Id');
                    if ($RestaurantId->length > 0) {
                        $RestaurantId = $RestaurantId->item(0)->nodeValue;
                    } else {
                        $RestaurantId = "";
                    }
                    $Name = $Restaurant->item($r)->getElementsByTagName('Name');
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    $Description = $Restaurant->item($r)->getElementsByTagName('Description');
                    if ($Description->length > 0) {
                        $Description = $Description->item(0)->nodeValue;
                    } else {
                        $Description = "";
                    }
                    $Schedule = $Restaurant->item($r)->getElementsByTagName('Schedule');
                    if ($Schedule->length > 0) {
                        $Schedule = $Schedule->item(0)->nodeValue;
                    } else {
                        $Schedule = "";
                    }
                    $Image = $Restaurant->item($r)->getElementsByTagName('Image');
                    if ($Image->length > 0) {
                        $Image = $Image->item(0)->nodeValue;
                    } else {
                        $Image = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('hotelinformation_restaurants');
                        $select->where(array(
                            'id' => $RestaurantId
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $id = (string)$data['id'];
                            if ($id != "") {
                                $sql = new Sql($db);
                                $data = array(
                                    'id' => $BarId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 1,
                                    'name' => $Name,
                                    'description' => $Description,
                                    'schedule' => $Schedule,
                                    'image' => $Image,
                                    'hotelid' => $Id 
                                );
                                $where['id = ?'] = $RestaurantId;
                                $update = $sql->update('hotelinformation_restaurants', $data, $where);
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('hotelinformation_restaurants');
                                $insert->values(array(
                                    'id' => $RestaurantId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name,
                                    'description' => $Description,
                                    'schedule' => $Schedule,
                                    'image' => $Image,
                                    'hotelid' => $Id 
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            }
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hotelinformation_restaurants');
                            $insert->values(array(
                                'id' => $RestaurantId,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $Name,
                                'description' => $Description,
                                'schedule' => $Schedule,
                                'image' => $Image,
                                'hotelid' => $Id,
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        }
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 2: ". $e;
                        echo $return;
                    }
                }
            }
        }
        $Themes = $Hotel->item(0)->getElementsByTagName('Themes');
        if ($Themes->length > 0) {
            $Theme = $Themes->item(0)->getElementsByTagName('Theme');
            if ($Theme->length > 0) {
                for ($j=0; $j < $Theme->length; $j++) { 
                    $ThemeId = $Theme->item($j)->getElementsByTagName('Id');
                    if ($ThemeId->length > 0) {
                        $ThemeId = $ThemeId->item(0)->nodeValue;
                    } else {
                        $ThemeId = "";
                    }
                    $Name = $Theme->item($j)->getElementsByTagName('Name');
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    $Path = $Theme->item($j)->getElementsByTagName('Path');
                    if ($Path->length > 0) {
                        $Path = $Path->item(0)->nodeValue;
                    } else {
                        $Path = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('hotelinformation_themes');
                        $select->where(array(
                            'id' => $ThemeId
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $id = (string)$data['id'];
                            if ($id != "") {
                                $sql = new Sql($db);
                                $data = array(
                                    'id' => $ThemeId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 1,
                                    'name' => $Name,
                                    'path' => $Path,
                                    'hotelid' => $Id 
                                );
                                $where['id = ?'] = $ThemeId;
                                $update = $sql->update('hotelinformation_themes', $data, $where);
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('hotelinformation_themes');
                                $insert->values(array(
                                    'id' => $ThemeId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name,
                                    'path' => $Path,
                                    'hotelid' => $Id 
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            }
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hotelinformation_themes');
                            $insert->values(array(
                                'id' => $ThemeId,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $Name,
                                'path' => $Path,
                                'hotelid' => $Id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        }
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 3: ". $e;
                        echo $return;
                    }
                }
            }
        }
        $Services = $Hotel->item(0)->getElementsByTagName('Services');
        if ($Services->length > 0) {
            $Service = $Services->item(0)->getElementsByTagName('Service');
            if ($Service->length > 0) {
                for ($s=0; $s < $Service->length; $s++) { 
                    $ServiceId = $Service->item($s)->getElementsByTagName('Id');
                    if ($ServiceId->length > 0) {
                        $ServiceId = $ServiceId->item(0)->nodeValue;
                    } else {
                        $ServiceId = "";
                    }
                    $Name = $Service->item($s)->getElementsByTagName('Name');
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    $Description = $Service->item($s)->getElementsByTagName('Description');
                    if ($Description->length > 0) {
                        $Description = $Description->item(0)->nodeValue;
                    } else {
                        $Description = "";
                    }
                    $ExtraCharge = $Service->item($s)->getElementsByTagName('ExtraCharge');
                    if ($ExtraCharge->length > 0) {
                        $ExtraCharge = $ExtraCharge->item(0)->nodeValue;
                    } else {
                        $ExtraCharge = "";
                    }
                    $Image = $Service->item($s)->getElementsByTagName('Image');
                    if ($Image->length > 0) {
                        $Image = $Image->item(0)->nodeValue;
                    } else {
                        $Image = "";
                    }
                    $Order = $Service->item($s)->getElementsByTagName('Order');
                    if ($Order->length > 0) {
                        $Order = $Order->item(0)->nodeValue;
                    } else {
                        $Order = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('hotelinformation_services');
                        $select->where(array(
                            'id' => $ServiceId
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $id = (string)$data['id'];
                            if ($id != "") {
                                $sql = new Sql($db);
                                $data = array(
                                    'id' => $ServiceId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 1,
                                    'name' => $Name,
                                    'description' => $Description,
                                    'extracharge' => $ExtraCharge,
                                    'image' => $Image,
                                    'order' => $Order,
                                    'hotelid' => $Id
                                );
                                $where['id = ?'] = $ServiceId;
                                $update = $sql->update('hotelinformation_services', $data, $where);
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('hotelinformation_services');
                                $insert->values(array(
                                    'id' => $ServiceId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name,
                                    'description' => $Description,
                                    'extracharge' => $ExtraCharge,
                                    'image' => $Image,
                                    'order' => $Order,
                                    'hotelid' => $Id
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            }
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hotelinformation_services');
                            $insert->values(array(
                                'id' => $ServiceId,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $Name,
                                'description' => $Description,
                                'extracharge' => $ExtraCharge,
                                'image' => $Image,
                                'order' => $Order,
                                'hotelid' => $Id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        }
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 4: ". $e;
                        echo $return;
                    }
                }
            }
        }
        $PointsOfInterest = $Hotel->item(0)->getElementsByTagName('PointsOfInterest');
        if ($PointsOfInterest->length > 0) {
            $PointOfInterest = $PointsOfInterest->item(0)->getElementsByTagName('PointOfInterest');
            if ($PointOfInterest->length > 0) {
                for ($k=0; $k < $PointOfInterest->length; $k++) { 
                    $PointOfInterestId = $PointOfInterest->item($k)->getElementsByTagName('Id');
                    if ($PointOfInterestId->length > 0) {
                        $PointOfInterestId = $PointOfInterestId->item(0)->nodeValue;
                    } else {
                        $PointOfInterestId = "";
                    }
                    $Name = $PointOfInterest->item($k)->getElementsByTagName('Name');
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    $Address = $PointOfInterest->item($k)->getElementsByTagName('Address');
                    if ($Address->length > 0) {
                        $City = $Address->item(0)->getElementsByTagName('City');
                        if ($City->length > 0) {
                            $CityName = $City->item(0)->getElementsByTagName('Name');
                            if ($CityName->length > 0) {
                                $CityName = $CityName->item(0)->nodeValue;
                            } else {
                                $CityName = "";
                            }
                        }
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
                    }
                    $Type = $PointOfInterest->item($k)->getElementsByTagName('Type');
                    if ($Type->length > 0) {
                        $TypeName = $Type->item(0)->getElementsByTagName('Name');
                        if ($TypeName->length > 0) {
                            $TypeName = $TypeName->item(0)->nodeValue;
                        } else {
                            $TypeName = "";
                        }
                    }

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('hotelinformation_pointsofinterest');
                        $select->where(array(
                            'id' => $PointOfInterestId
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $id = (int)$data['id'];
                            if ($id > 0) {
                                $sql = new Sql($db);
                                $data = array(
                                    'id' => $PointOfInterestId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 1,
                                    'name' => $Name,
                                    'cityname' => $CityName,
                                    'distance' => $Distance,
                                    'distanceunit' => $DistanceUnit,
                                    'latitude' => $Latitude,
                                    'longitude' => $Longitude,
                                    'typename' => $TypeName,
                                    'hotelid' => $Id
                                );
                                $where['id = ?'] = $PointOfInterestId;
                                $update = $sql->update('hotelinformation_pointsofinterest', $data, $where);
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('hotelinformation_pointsofinterest');
                                $insert->values(array(
                                    'id' => $PointOfInterestId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name,
                                    'cityname' => $CityName,
                                    'distance' => $Distance,
                                    'distanceunit' => $DistanceUnit,
                                    'latitude' => $Latitude,
                                    'longitude' => $Longitude,
                                    'typename' => $TypeName,
                                    'hotelid' => $Id
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            }
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hotelinformation_pointsofinterest');
                            $insert->values(array(
                                'id' => $PointOfInterestId,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $Name,
                                'cityname' => $CityName,
                                'distance' => $Distance,
                                'distanceunit' => $DistanceUnit,
                                'latitude' => $Latitude,
                                'longitude' => $Longitude,
                                'typename' => $TypeName,
                                'hotelid' => $Id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        }
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 5: ". $e;
                        echo $return;
                    }
                }
            }
        }
        $Facilities = $Hotel->item(0)->getElementsByTagName('Facilities');
        if ($Facilities->length > 0) {
            $Comfort = $Facilities->item(0)->getElementsByTagName('Comfort');
            if ($Comfort->length > 0) {
                for ($x=0; $x < $Comfort->length; $x++) { 
                    $FacilityId = $Comfort->item($x)->getElementsByTagName('Id');
                    if ($FacilityId->length > 0) {
                        $FacilityId = $FacilityId->item(0)->nodeValue;
                    } else {
                        $FacilityId = "";
                    }
                    $Name = $Comfort->item($x)->getElementsByTagName('Name');
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    $Description = $Comfort->item($x)->getElementsByTagName('Description');
                    if ($Description->length > 0) {
                        $Description = $Description->item(0)->nodeValue;
                    } else {
                        $Description = "";
                    }
                    $ExtraCharge = $Comfort->item($x)->getElementsByTagName('ExtraCharge');
                    if ($ExtraCharge->length > 0) {
                        $ExtraCharge = $ExtraCharge->item(0)->nodeValue;
                    } else {
                        $ExtraCharge = "";
                    }
                    $Image = $Comfort->item($x)->getElementsByTagName('Image');
                    if ($Image->length > 0) {
                        $Image = $Image->item(0)->nodeValue;
                    } else {
                        $Image = "";
                    }
                    $Order = $Comfort->item($x)->getElementsByTagName('Order');
                    if ($Order->length > 0) {
                        $Order = $Order->item(0)->nodeValue;
                    } else {
                        $Order = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('hotelinformation_facilities');
                        $select->where(array(
                            'id' => $FacilityId
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $id = (string)$data['id'];
                            if ($id != "") {
                                $sql = new Sql($db);
                                $data = array(
                                    'id' => $FacilityId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 1,
                                    'name' => $Name,
                                    'description' => $Description,
                                    'extracharge' => $ExtraCharge,
                                    'image' => $Image,
                                    'order' => $Order,
                                    'hotelid' => $Id
                                );
                                $where['id = ?'] = $FacilityId;
                                $update = $sql->update('hotelinformation_facilities', $data, $where);
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('hotelinformation_facilities');
                                $insert->values(array(
                                    'id' => $FacilityId,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name,
                                    'description' => $Description,
                                    'extracharge' => $ExtraCharge,
                                    'image' => $Image,
                                    'order' => $Order,
                                    'hotelid' => $Id
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            }
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hotelinformation_facilities');
                            $insert->values(array(
                                'id' => $FacilityId,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $Name,
                                'description' => $Description,
                                'extracharge' => $ExtraCharge,
                                'image' => $Image,
                                'order' => $Order,
                                'hotelid' => $Id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        }
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 6: ". $e;
                        echo $return;
                    }
                }
            }
        }
        $Galleries = $Hotel->item(0)->getElementsByTagName('Galleries');
        if ($Galleries->length > 0) {
            $Image = $Galleries->item(0)->getElementsByTagName('Image');
            if ($Image->length > 0) {
                for ($y=0; $y < $Image->length; $y++) { 
                    $Description = $Image->item($y)->getElementsByTagName('Description');
                    if ($Description->length > 0) {
                        $Description = $Description->item(0)->nodeValue;
                    } else {
                        $Description = "";
                    }
                    $Title = $Image->item($y)->getElementsByTagName('Title');
                    if ($Title->length > 0) {
                        $Title = $Title->item(0)->nodeValue;
                    } else {
                        $Title = "";
                    }
                    $URL = $Image->item($y)->getElementsByTagName('URL');
                    if ($URL->length > 0) {
                        $URL = $URL->item(0)->nodeValue;
                    } else {
                        $URL = "";
                    }
                    $GroupId = $Image->item($y)->getElementsByTagName('GroupId');
                    if ($GroupId->length > 0) {
                        $GroupId = $GroupId->item(0)->nodeValue;
                    } else {
                        $GroupId = "";
                    }
                    $GroupName = $Image->item($y)->getElementsByTagName('GroupName');
                    if ($GroupName->length > 0) {
                        $GroupName = $GroupName->item(0)->nodeValue;
                    } else {
                        $GroupName = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hotelinformation_images');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'description' => $Description,
                            'title' => $Title,
                            'url' => $URL,
                            'groupid' => $GroupId,
                            'groupname' => $GroupName,
                            'hotelid' => $Id
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 7: ". $e;
                        echo $return;
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
echo 'Done';
?>
