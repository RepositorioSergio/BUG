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
echo "COMECOU ESTABLISHMENT DATA EXTENDS<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://test.xtravelsystem.com/public/v1_0rc1/hotelBookingHandler';

$email = 'paulo@corp.bug-software.com';
$password = 'xA2d@a1X';

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/hotel/types">
    <soapenv:Header/>
    <soapenv:Body>
        <typ:getEstablishmentDataExtends>
            <EstablishmentDataRQ_1>
                <agencyCode>613</agencyCode>
                <brandCode>1</brandCode>
                <pointOfSaleId>1</pointOfSaleId>
                <establishmentId>1258</establishmentId>
                <language>en</language>
            </EstablishmentDataRQ_1>
        </typ:getEstablishmentDataExtends>
    </soapenv:Body>
</soapenv:Envelope>';

$headers = array(
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

echo $return;
echo $error;
echo $return;
echo "<xmp>";
var_dump($response);
echo "</xmp>"; 

$config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
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
$getEstablishmentDataExtendsResponse = $Body->item(0)->getElementsByTagName("getEstablishmentDataExtendsResponse");
if ($getEstablishmentDataExtendsResponse->length > 0) {
    $result = $getEstablishmentDataExtendsResponse->item(0)->getElementsByTagName("result");
    if ($result->length > 0) {
        $id = $result->item(0)->getElementsByTagName("id");
        if ($id->length > 0) {
            $id = $id->item(0)->nodeValue;
        } else {
            $id = "";
        }
        $name = $result->item(0)->getElementsByTagName("name");
        if ($name->length > 0) {
            $name = $name->item(0)->nodeValue;
        } else {
            $name = "";
        }
        $categoryCode = $result->item(0)->getElementsByTagName("categoryCode");
        if ($categoryCode->length > 0) {
            $categoryCode = $categoryCode->item(0)->nodeValue;
        } else {
            $categoryCode = "";
        }
        $categoryName = $result->item(0)->getElementsByTagName("categoryName");
        if ($categoryName->length > 0) {
            $categoryName = $categoryName->item(0)->nodeValue;
        } else {
            $categoryName = "";
        }
        $date = $result->item(0)->getElementsByTagName("date");
        if ($date->length > 0) {
            $date = $date->item(0)->nodeValue;
        } else {
            $date = "";
        }
        $latitude = $result->item(0)->getElementsByTagName("latitude");
        if ($latitude->length > 0) {
            $latitude = $latitude->item(0)->nodeValue;
        } else {
            $latitude = "";
        }
        $longitude = $result->item(0)->getElementsByTagName("longitude");
        if ($longitude->length > 0) {
            $longitude = $longitude->item(0)->nodeValue;
        } else {
            $longitude = "";
        }
        $longDescription = $result->item(0)->getElementsByTagName("longDescription");
        if ($longDescription->length > 0) {
            $longDescription = $longDescription->item(0)->nodeValue;
        } else {
            $longDescription = "";
        }
        $shortDescription = $result->item(0)->getElementsByTagName("shortDescription");
        if ($shortDescription->length > 0) {
            $shortDescription = $shortDescription->item(0)->nodeValue;
        } else {
            $shortDescription = "";
        }
        $typeCode = $result->item(0)->getElementsByTagName("typeCode");
        if ($typeCode->length > 0) {
            $typeCode = $typeCode->item(0)->nodeValue;
        } else {
            $typeCode = "";
        }
        $typeName = $result->item(0)->getElementsByTagName("typeName");
        if ($typeName->length > 0) {
            $typeName = $typeName->item(0)->nodeValue;
        } else {
            $typeName = "";
        }
        $additionalInformation = $result->item(0)->getElementsByTagName("additionalInformation");
        if ($additionalInformation->length > 0) {
            $additionalInformation = $additionalInformation->item(0)->nodeValue;
        } else {
            $additionalInformation = "";
        }
        $directions = $result->item(0)->getElementsByTagName("directions");
        if ($directions->length > 0) {
            $directions = $directions->item(0)->nodeValue;
        } else {
            $directions = "";
        }
        $locationInformation = $result->item(0)->getElementsByTagName("locationInformation");
        if ($locationInformation->length > 0) {
            $locationInformation = $locationInformation->item(0)->nodeValue;
        } else {
            $locationInformation = "";
        }
        $roomsInformation = $result->item(0)->getElementsByTagName("roomsInformation");
        if ($roomsInformation->length > 0) {
            $roomsInformation = $roomsInformation->item(0)->nodeValue;
        } else {
            $roomsInformation = "";
        }
        $xmlUri = $result->item(0)->getElementsByTagName("xmlUri");
        if ($xmlUri->length > 0) {
            $xmlUri = $xmlUri->item(0)->nodeValue;
        } else {
            $xmlUri = "";
        }
        $address = $result->item(0)->getElementsByTagName("address");
        if ($address->length > 0) {
            $address2 = $address->item(0)->getElementsByTagName("address");
            if ($address2->length > 0) {
                $address2 = $address2->item(0)->nodeValue;
            } else {
                $address2 = "";
            }
            $cityCode = $address->item(0)->getElementsByTagName("cityCode");
            if ($cityCode->length > 0) {
                $cityCode = $cityCode->item(0)->nodeValue;
            } else {
                $cityCode = "";
            }
            $cityName = $address->item(0)->getElementsByTagName("cityName");
            if ($cityName->length > 0) {
                $cityName = $cityName->item(0)->nodeValue;
            } else {
                $cityName = "";
            }
            $countryCode = $address->item(0)->getElementsByTagName("countryCode");
            if ($countryCode->length > 0) {
                $countryCode = $countryCode->item(0)->nodeValue;
            } else {
                $countryCode = "";
            }
            $countryName = $address->item(0)->getElementsByTagName("countryName");
            if ($countryName->length > 0) {
                $countryName = $countryName->item(0)->nodeValue;
            } else {
                $countryName = "";
            }
            $email = $address->item(0)->getElementsByTagName("email");
            if ($email->length > 0) {
                $email = $email->item(0)->nodeValue;
            } else {
                $email = "";
            }
            $fax = $address->item(0)->getElementsByTagName("fax");
            if ($fax->length > 0) {
                $fax = $fax->item(0)->nodeValue;
            } else {
                $fax = "";
            }
            $name = $address->item(0)->getElementsByTagName("name");
            if ($name->length > 0) {
                $name = $name->item(0)->nodeValue;
            } else {
                $name = "";
            }
            $stateCode = $address->item(0)->getElementsByTagName("stateCode");
            if ($stateCode->length > 0) {
                $stateCode = $stateCode->item(0)->nodeValue;
            } else {
                $stateCode = "";
            }
            $stateName = $address->item(0)->getElementsByTagName("stateName");
            if ($stateName->length > 0) {
                $stateName = $stateName->item(0)->nodeValue;
            } else {
                $stateName = "";
            }
            $telephone = $address->item(0)->getElementsByTagName("telephone");
            if ($telephone->length > 0) {
                $telephone = $telephone->item(0)->nodeValue;
            } else {
                $telephone = "";
            }
            $zipCode = $address->item(0)->getElementsByTagName("zipCode");
            if ($zipCode->length > 0) {
                $zipCode = $zipCode->item(0)->nodeValue;
            } else {
                $zipCode = "";
            }
        }
        $imageGroups = $result->item(0)->getElementsByTagName("imageGroups");
        if ($imageGroups->length > 0) {
            $imageGroups_code = $imageGroups->item(0)->getElementsByTagName("code");
            if ($imageGroups_code->length > 0) {
                $imageGroups_code = $imageGroups_code->item(0)->nodeValue;
            } else {
                $imageGroups_code = "";
            }
            $imageGroups_name = $imageGroups->item(0)->getElementsByTagName("name");
            if ($imageGroups_name->length > 0) {
                $imageGroups_name = $imageGroups_name->item(0)->nodeValue;
            } else {
                $imageGroups_name = "";
            }
            $images = $imageGroups->item(0)->getElementsByTagName("images");
            if ($images->length > 0) {
                for ($i=0; $i < $images->length; $i++) { 
                    $date = $images->item($i)->getElementsByTagName("date");
                    if ($date->length > 0) {
                        $date = $date->item(0)->nodeValue;
                    } else {
                        $date = "";
                    }
                    $source = $images->item($i)->getElementsByTagName("source");
                    if ($source->length > 0) {
                        $source = $source->item(0)->nodeValue;
                    } else {
                        $source = "";
                    }
                    $uri = $images->item($i)->getElementsByTagName("uri");
                    if ($uri->length > 0) {
                        $uri = $uri->item(0)->nodeValue;
                    } else {
                        $uri = "";
                    }
                }
            }
        }
        $propertiesGroups = $result->item(0)->getElementsByTagName("propertiesGroups");
        if ($propertiesGroups->length > 0) {
            for ($j=0; $j < $propertiesGroups->length; $j++) { 
                $code = $propertiesGroups->item($j)->getElementsByTagName("code");
                if ($code->length > 0) {
                    $code = $code->item(0)->nodeValue;
                } else {
                    $code = "";
                }
                $name = $propertiesGroups->item($j)->getElementsByTagName("name");
                if ($name->length > 0) {
                    $name = $name->item(0)->nodeValue;
                } else {
                    $name = "";
                }
                $properties = $propertiesGroups->item($j)->getElementsByTagName("properties");
                if ($properties->length > 0) {
                    for ($jAux=0; $jAux < $properties->length; $jAux++) { 
                        $code = $properties->item($jAux)->getElementsByTagName("code");
                        if ($code->length > 0) {
                            $code = $code->item(0)->nodeValue;
                        } else {
                            $code = "";
                        }
                        $name = $properties->item($jAux)->getElementsByTagName("name");
                        if ($name->length > 0) {
                            $name = $name->item(0)->nodeValue;
                        } else {
                            $name = "";
                        }
                        $value = $properties->item($jAux)->getElementsByTagName("value");
                        if ($value->length > 0) {
                            $value = $value->item(0)->nodeValue;
                        } else {
                            $value = "";
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