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
echo "COMECOU READ XML<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.roomsxml.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = 'HotelDetailXML/132039467.xml';
$response = file_get_contents($filename);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$HotelElement = $inputDoc->getElementsByTagName("HotelElement");
if ($HotelElement->length > 0) {
    $Id = $HotelElement->item(0)->getElementsByTagName('Id');
    if ($Id->length > 0) {
        $Id = $Id->item(0)->nodeValue;
    } else {
        $Id = "";
    }
    $Name = $HotelElement->item(0)->getElementsByTagName('Name');
    if ($Name->length > 0) {
        $Name = $Name->item(0)->nodeValue;
    } else {
        $Name = "";
    }
    $Type = $HotelElement->item(0)->getElementsByTagName('Type');
    if ($Type->length > 0) {
        $Type = $Type->item(0)->nodeValue;
    } else {
        $Type = "";
    }
    $Stars = $HotelElement->item(0)->getElementsByTagName('Stars');
    if ($Stars->length > 0) {
        $Stars = $Stars->item(0)->nodeValue;
    } else {
        $Stars = "";
    }
    $Rank = $HotelElement->item(0)->getElementsByTagName('Rank');
    if ($Rank->length > 0) {
        $Rank = $Rank->item(0)->nodeValue;
    } else {
        $Rank = "";
    }
    $Region = $HotelElement->item(0)->getElementsByTagName('Region');
    if ($Region->length > 0) {
        $RegionId = $Region->item(0)->getElementsByTagName('Id');
        if ($RegionId->length > 0) {
            $RegionId = $RegionId->item(0)->nodeValue;
        } else {
            $RegionId = "";
        }
        $RegionName = $Region->item(0)->getElementsByTagName('Name');
        if ($RegionName->length > 0) {
            $RegionName = $RegionName->item(0)->nodeValue;
        } else {
            $RegionName = "";
        }
        $CityId = $Region->item(0)->getElementsByTagName('CityId');
        if ($CityId->length > 0) {
            $CityId = $CityId->item(0)->nodeValue;
        } else {
            $CityId = "";
        }
    }
    $Address = $HotelElement->item(0)->getElementsByTagName('Address');
    if ($Address->length > 0) {
        $Address1 = $Address->item(0)->getElementsByTagName('Address1');
        if ($Address1->length > 0) {
            $Address1 = $Address1->item(0)->nodeValue;
        } else {
            $Address1 = "";
        }
        $Address2 = $Address->item(0)->getElementsByTagName('Address2');
        if ($Address2->length > 0) {
            $Address2 = $Address2->item(0)->nodeValue;
        } else {
            $Address2 = "";
        }
        $Address3 = $Address->item(0)->getElementsByTagName('Address3');
        if ($Address3->length > 0) {
            $Address3 = $Address3->item(0)->nodeValue;
        } else {
            $Address3 = "";
        }
        $City = $Address->item(0)->getElementsByTagName('City');
        if ($City->length > 0) {
            $City = $City->item(0)->nodeValue;
        } else {
            $City = "";
        }
        $State = $Address->item(0)->getElementsByTagName('State');
        if ($State->length > 0) {
            $State = $State->item(0)->nodeValue;
        } else {
            $State = "";
        }
        $Zip = $Address->item(0)->getElementsByTagName('Zip');
        if ($Zip->length > 0) {
            $Zip = $Zip->item(0)->nodeValue;
        } else {
            $Zip = "";
        }
        $Country = $Address->item(0)->getElementsByTagName('Country');
        if ($Country->length > 0) {
            $Country = $Country->item(0)->nodeValue;
        } else {
            $Country = "";
        }
        $Tel = $Address->item(0)->getElementsByTagName('Tel');
        if ($Tel->length > 0) {
            $Tel = $Tel->item(0)->nodeValue;
        } else {
            $Tel = "";
        }
        $Fax = $Address->item(0)->getElementsByTagName('Fax');
        if ($Fax->length > 0) {
            $Fax = $Fax->item(0)->nodeValue;
        } else {
            $Fax = "";
        }
        $Email = $Address->item(0)->getElementsByTagName('Email');
        if ($Email->length > 0) {
            $Email = $Email->item(0)->nodeValue;
        } else {
            $Email = "";
        }
        $Url = $Address->item(0)->getElementsByTagName('Url');
        if ($Url->length > 0) {
            $Url = $Url->item(0)->nodeValue;
        } else {
            $Url = "";
        }
    }
    $GeneralInfo = $HotelElement->item(0)->getElementsByTagName('GeneralInfo');
    if ($GeneralInfo->length > 0) {
        $CountRooms = $GeneralInfo->item(0)->getElementsByTagName('CountRooms');
        if ($CountRooms->length > 0) {
            $CountRooms = $CountRooms->item(0)->nodeValue;
        } else {
            $CountRooms = "";
        }
        $Latitude = $GeneralInfo->item(0)->getElementsByTagName('Latitude');
        if ($Latitude->length > 0) {
            $Latitude = $Latitude->item(0)->nodeValue;
        } else {
            $Latitude = "";
        }
        $Longitude = $GeneralInfo->item(0)->getElementsByTagName('Longitude');
        if ($Longitude->length > 0) {
            $Longitude = $Longitude->item(0)->nodeValue;
        } else {
            $Longitude = "";
        }
    }
    $Rating = $HotelElement->item(0)->getElementsByTagName('Rating');
    if ($Rating->length > 0) {
        $System = $Rating->item(0)->getElementsByTagName('System');
        if ($System->length > 0) {
            $System = $System->item(0)->nodeValue;
        } else {
            $System = "";
        }
        $Score = $Rating->item(0)->getElementsByTagName('Score');
        if ($Score->length > 0) {
            $Score = $Score->item(0)->nodeValue;
        } else {
            $Score = "";
        }
        $Description = $Rating->item(0)->getElementsByTagName('Description');
        if ($Description->length > 0) {
            $Description = $Description->item(0)->nodeValue;
        } else {
            $Description = "";
        }
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('roomsxml_hotels');
        $insert->values(array(
            'id' => $Id,
            'name' => $Name,
            'type' => $Type,
            'stars' => $Stars,
            'rank' => $Rank,
            'regionid' => $RegionId,
            'regionname' => $RegionName,
            'cityid' => $CityId,
            'address1' => $Address1,
            'address2' => $Address2,
            'address3' => $Address3,
            'city' => $City,
            'state' => $State,
            'zip' => $Zip,
            'country' => $Country,
            'tel' => $Tel,
            'fax' => $Fax,
            'email' => $Email,
            'url' => $Url,
            'countrooms' => $CountRooms,
            'latitude' => $Latitude,
            'longitude' => $Longitude,
            'system' => $System,
            'score' => $Score,
            'description' => $Description
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error 1: " . $e;
        echo $return;
    }

    $Photo = $HotelElement->item(0)->getElementsByTagName('Photo');
    if ($Photo->length > 0) {
        for ($i=0; $i < $Photo->length; $i++) { 
            $Url = $Photo->item($i)->getElementsByTagName('Url');
            if ($Url->length > 0) {
                $Url = $Url->item(0)->nodeValue;
            } else {
                $Url = "";
            }
            $Width = $Photo->item($i)->getElementsByTagName('Width');
            if ($Width->length > 0) {
                $Width = $Width->item(0)->nodeValue;
            } else {
                $Width = "";
            }
            $Height = $Photo->item($i)->getElementsByTagName('Height');
            if ($Height->length > 0) {
                $Height = $Height->item(0)->nodeValue;
            } else {
                $Height = "";
            }
            $Bytes = $Photo->item($i)->getElementsByTagName('Bytes');
            if ($Bytes->length > 0) {
                $Bytes = $Bytes->item(0)->nodeValue;
            } else {
                $Bytes = "";
            }
            $Caption = $Photo->item($i)->getElementsByTagName('Caption');
            if ($Caption->length > 0) {
                $Caption = $Caption->item(0)->nodeValue;
            } else {
                $Caption = "";
            }
            $ThumbnailUrl = $Photo->item($i)->getElementsByTagName('ThumbnailUrl');
            if ($ThumbnailUrl->length > 0) {
                $ThumbnailUrl = $ThumbnailUrl->item(0)->nodeValue;
            } else {
                $ThumbnailUrl = "";
            }
            $ThumbnailWidth = $Photo->item($i)->getElementsByTagName('ThumbnailWidth');
            if ($ThumbnailWidth->length > 0) {
                $ThumbnailWidth = $ThumbnailWidth->item(0)->nodeValue;
            } else {
                $ThumbnailWidth = "";
            }
            $ThumbnailHeight = $Photo->item($i)->getElementsByTagName('ThumbnailHeight');
            if ($ThumbnailHeight->length > 0) {
                $ThumbnailHeight = $ThumbnailHeight->item(0)->nodeValue;
            } else {
                $ThumbnailHeight = "";
            }
            $ThumbnailBytes = $Photo->item($i)->getElementsByTagName('ThumbnailBytes');
            if ($ThumbnailBytes->length > 0) {
                $ThumbnailBytes = $ThumbnailBytes->item(0)->nodeValue;
            } else {
                $ThumbnailBytes = "";
            }
            $PhotoType = $Photo->item($i)->getElementsByTagName('PhotoType');
            if ($PhotoType->length > 0) {
                $PhotoType = $PhotoType->item(0)->nodeValue;
            } else {
                $PhotoType = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('roomsxml_hotels_photo');
                $insert->values(array(
                    'url' => $Url,
                    'width' => $Width,
                    'height' => $Height,
                    'bytes' => $Bytes,
                    'thumbnailurl' => $ThumbnailUrl,
                    'thumbnailwidth' => $ThumbnailWidth,
                    'thumbnailheight' => $ThumbnailHeight,
                    'thumbnailbytes' => $ThumbnailBytes,
                    'phototype' => $PhotoType,
                    'hotelid' => $Id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 2: " . $e;
                echo $return;
            }
        }
    }
    $Description = $HotelElement->item(0)->getElementsByTagName('Description');
    if ($Description->length > 0) {
        for ($j=0; $j < $Description->length; $j++) { 
            $Language = $Description->item($j)->getElementsByTagName('Language');
            if ($Language->length > 0) {
                $Language = $Language->item(0)->nodeValue;
            } else {
                $Language = "";
            }
            $Type = $Description->item($j)->getElementsByTagName('Type');
            if ($Type->length > 0) {
                $Type = $Type->item(0)->nodeValue;
            } else {
                $Type = "";
            }
            $Text = $Description->item($j)->getElementsByTagName('Text');
            if ($Text->length > 0) {
                $Text = $Text->item(0)->nodeValue;
            } else {
                $Text = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('roomsxml_hotels_description');
                $insert->values(array(
                    'language' => $Language,
                    'type' => $Type,
                    'description' => $Text,
                    'hotelid' => $Id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 3: " . $e;
                echo $return;
            }
        }
    }
    $Amenity = $HotelElement->item(0)->getElementsByTagName('Amenity');
    if ($Amenity->length > 0) {
        for ($i=0; $i < $Amenity->length; $i++) { 
            $Code = $Amenity->item($k)->getElementsByTagName('Code');
            if ($Code->length > 0) {
                $Code = $Code->item(0)->nodeValue;
            } else {
                $Code = "";
            }
            $Text = $Amenity->item($k)->getElementsByTagName('Text');
            if ($Text->length > 0) {
                $Text = $Text->item(0)->nodeValue;
            } else {
                $Text = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('roomsxml_hotels_amenity');
                $insert->values(array(
                    'code' => $Code,
                    'description' => $Text,
                    'hotelid' => $Id
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 4: " . $e;
                echo $return;
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
