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
echo "COMECOU CONFIRM<br/>";
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
        <typ:confirmExtendsV22>
            <ConfirmRQV22_1>
                <agencyCode>613</agencyCode>
                <brandCode>1</brandCode>
                <pointOfSaleId>1</pointOfSaleId>
                <checkin>2018-04-22T00:00:00.000Z</checkin>
                <checkout>2018-04-27T00:00:00.000Z</checkout>
                <establishmentId>245479</establishmentId>
                <language>en</language>
                <occupancies>
                    <adults>2</adults>
                    <children>0</children>
                    <numberOfRooms>1</numberOfRooms>
                    <ratePlanCode>AP1:FB:STD::PVqMBzMGVmZGExZDMtYzkzMi00MjM5LTlhNjUtNmVmNGE0MTMyN2Fi</ratePlanCode>
                </occupancies>
                <onlyOnline>true</onlyOnline>
                <extendedLogin>
                    <channel>B2C</channel>
                    <loginCountry>ES</loginCountry>
                    <mainNationality>ES</mainNationality>
                </extendedLogin>
                <agencyReference>testtestttest</agencyReference>
                <closeBasket>false</closeBasket>
                <paxList>
                    <pax>
                        <age>30</age>
                        <name>TEST</name>
                        <nationality>GB</nationality>
                        <residenceCountry/>
                    </pax>
                </paxList>
                <titular>test</titular>
            </ConfirmRQV22_1>
        </typ:confirmExtendsV22>
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
$confirmExtendsV22Response = $Body->item(0)->getElementsByTagName("confirmExtendsV22Response");
if ($confirmExtendsV22Response->length > 0) {
    $result = $confirmExtendsV22Response->item(0)->getElementsByTagName("result");
    if ($result->length > 0) {
        $description = $result->item(0)->getElementsByTagName("description");
        if ($description->length > 0) {
            $description = $description->item(0)->nodeValue;
        } else {
            $description = "";
        }
        $okDate = $result->item(0)->getElementsByTagName("okDate");
        if ($okDate->length > 0) {
            $okDate = $okDate->item(0)->nodeValue;
        } else {
            $okDate = "";
        }
        $pointOfSaleId = $result->item(0)->getElementsByTagName("pointOfSaleId");
        if ($pointOfSaleId->length > 0) {
            $pointOfSaleId = $pointOfSaleId->item(0)->nodeValue;
        } else {
            $pointOfSaleId = "";
        }
        $positionInBasket = $result->item(0)->getElementsByTagName("positionInBasket");
        if ($positionInBasket->length > 0) {
            $positionInBasket = $positionInBasket->item(0)->nodeValue;
        } else {
            $positionInBasket = "";
        }
        $requestDate = $result->item(0)->getElementsByTagName("requestDate");
        if ($requestDate->length > 0) {
            $requestDate = $requestDate->item(0)->nodeValue;
        } else {
            $requestDate = "";
        }
        $serviceId = $result->item(0)->getElementsByTagName("serviceId");
        if ($serviceId->length > 0) {
            $serviceId = $serviceId->item(0)->nodeValue;
        } else {
            $serviceId = "";
        }
        $status = $result->item(0)->getElementsByTagName("status");
        if ($status->length > 0) {
            $status = $status->item(0)->nodeValue;
        } else {
            $status = "";
        }
        $checkin = $result->item(0)->getElementsByTagName("checkin");
        if ($checkin->length > 0) {
            $checkin = $checkin->item(0)->nodeValue;
        } else {
            $checkin = "";
        }
        $checkout = $result->item(0)->getElementsByTagName("checkout");
        if ($checkout->length > 0) {
            $checkout = $checkout->item(0)->nodeValue;
        } else {
            $checkout = "";
        }
        $establishmentId = $result->item(0)->getElementsByTagName("establishmentId");
        if ($establishmentId->length > 0) {
            $establishmentId = $establishmentId->item(0)->nodeValue;
        } else {
            $establishmentId = "";
        }
        $pricingAgencyCode = $result->item(0)->getElementsByTagName("pricingAgencyCode");
        if ($pricingAgencyCode->length > 0) {
            $pricingAgencyCode = $pricingAgencyCode->item(0)->nodeValue;
        } else {
            $pricingAgencyCode = "";
        }
        $userId = $result->item(0)->getElementsByTagName("userId");
        if ($userId->length > 0) {
            $userId = $userId->item(0)->nodeValue;
        } else {
            $userId = "";
        }
        $voucherUrl = $result->item(0)->getElementsByTagName("voucherUrl");
        if ($voucherUrl->length > 0) {
            $voucherUrl = $voucherUrl->item(0)->nodeValue;
        } else {
            $voucherUrl = "";
        }
        $cancellationDate = $result->item(0)->getElementsByTagName("cancellationDate");
        if ($cancellationDate->length > 0) {
            $cancellationDate = $cancellationDate->item(0)->nodeValue;
        } else {
            $cancellationDate = "";
        }
        $commision = $result->item(0)->getElementsByTagName("commision");
        if ($commision->length > 0) {
            $commision = $commision->item(0)->nodeValue;
        } else {
            $commision = "";
        }
        $contractId = $result->item(0)->getElementsByTagName("contractId");
        if ($contractId->length > 0) {
            $contractId = $contractId->item(0)->nodeValue;
        } else {
            $contractId = "";
        }
        $basket = $result->item(0)->getElementsByTagName("basket");
        if ($basket->length > 0) {
            $basketId = $basket->item(0)->getElementsByTagName("basketId");
            if ($basketId->length > 0) {
                $basketId = $basketId->item(0)->nodeValue;
            } else {
                $basketId = "";
            }
            $cancelled = $basket->item(0)->getElementsByTagName("cancelled");
            if ($cancelled->length > 0) {
                $cancelled = $cancelled->item(0)->nodeValue;
            } else {
                $cancelled = "";
            }
            $closed = $basket->item(0)->getElementsByTagName("closed");
            if ($closed->length > 0) {
                $closed = $closed->item(0)->nodeValue;
            } else {
                $closed = "";
            }
            $opened = $basket->item(0)->getElementsByTagName("opened");
            if ($opened->length > 0) {
                $opened = $opened->item(0)->nodeValue;
            } else {
                $opened = "";
            }
            $titular = $basket->item(0)->getElementsByTagName("titular");
            if ($titular->length > 0) {
                $titular = $titular->item(0)->nodeValue;
            } else {
                $titular = "";
            }
        }
        $total = $result->item(0)->getElementsByTagName("total");
        if ($total->length > 0) {
            $total_currencyCode = $total->item(0)->getElementsByTagName("currencyCode");
            if ($total_currencyCode->length > 0) {
                $total_currencyCode = $total_currencyCode->item(0)->nodeValue;
            } else {
                $total_currencyCode = "";
            }
            $total_value = $total->item(0)->getElementsByTagName("value");
            if ($total_value->length > 0) {
                $total_value = $total_value->item(0)->nodeValue;
            } else {
                $total_value = "";
            }
        }
        $comments = $result->item(0)->getElementsByTagName("comments");
        if ($comments->length > 0) {
            for ($i=0; $i < $comments->length; $i++) { 
                $text = $comments->item($i)->getElementsByTagName("text");
                if ($text->length > 0) {
                    $text = $text->item(0)->nodeValue;
                } else {
                    $text = "";
                }
                $type = $comments->item($i)->getElementsByTagName("type");
                if ($type->length > 0) {
                    $type = $type->item(0)->nodeValue;
                } else {
                    $type = "";
                }
            }
        }
        $occupations = $result->item(0)->getElementsByTagName("occupations");
        if ($occupations->length > 0) {
            $adults = $occupations->item(0)->getElementsByTagName("adults");
            if ($adults->length > 0) {
                $adults = $adults->item(0)->nodeValue;
            } else {
                $adults = "";
            }
            $children = $occupations->item(0)->getElementsByTagName("children");
            if ($children->length > 0) {
                $children = $children->item(0)->nodeValue;
            } else {
                $children = "";
            }
            $boardTypeCode = $occupations->item(0)->getElementsByTagName("boardTypeCode");
            if ($boardTypeCode->length > 0) {
                $boardTypeCode = $boardTypeCode->item(0)->nodeValue;
            } else {
                $boardTypeCode = "";
            }
            $boardTypeName = $occupations->item(0)->getElementsByTagName("boardTypeName");
            if ($boardTypeName->length > 0) {
                $boardTypeName = $boardTypeName->item(0)->nodeValue;
            } else {
                $boardTypeName = "";
            }
            $numberOfDays = $occupations->item(0)->getElementsByTagName("numberOfDays");
            if ($numberOfDays->length > 0) {
                $numberOfDays = $numberOfDays->item(0)->nodeValue;
            } else {
                $numberOfDays = "";
            }
            $numberOfRooms = $occupations->item(0)->getElementsByTagName("numberOfRooms");
            if ($numberOfRooms->length > 0) {
                $numberOfRooms = $numberOfRooms->item(0)->nodeValue;
            } else {
                $numberOfRooms = "";
            }
            $roomTypeCode = $occupations->item(0)->getElementsByTagName("roomTypeCode");
            if ($roomTypeCode->length > 0) {
                $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
            } else {
                $roomTypeCode = "";
            }
            $roomTypeName = $occupations->item(0)->getElementsByTagName("roomTypeName");
            if ($roomTypeName->length > 0) {
                $roomTypeName = $roomTypeName->item(0)->nodeValue;
            } else {
                $roomTypeName = "";
            }
            $fromDate = $occupations->item(0)->getElementsByTagName("fromDate");
            if ($fromDate->length > 0) {
                $fromDate = $fromDate->item(0)->nodeValue;
            } else {
                $fromDate = "";
            }
            $toDate = $occupations->item(0)->getElementsByTagName("toDate");
            if ($toDate->length > 0) {
                $toDate = $toDate->item(0)->nodeValue;
            } else {
                $toDate = "";
            }
            $onRequest = $occupations->item(0)->getElementsByTagName("onRequest");
            if ($onRequest->length > 0) {
                $onRequest = $onRequest->item(0)->nodeValue;
            } else {
                $onRequest = "";
            }
            $description = $occupations->item(0)->getElementsByTagName("description");
            if ($description->length > 0) {
                $description = $description->item(0)->nodeValue;
            } else {
                $description = "";
            }
            $ratePlanCode = $occupations->item(0)->getElementsByTagName("ratePlanCode");
            if ($ratePlanCode->length > 0) {
                $ratePlanCode = $ratePlanCode->item(0)->nodeValue;
            } else {
                $ratePlanCode = "";
            }
            $amount = $occupations->item(0)->getElementsByTagName("amount");
            if ($amount->length > 0) {
                $currencyCode = $amount->item(0)->getElementsByTagName("currencyCode");
                if ($currencyCode->length > 0) {
                    $currencyCode = $currencyCode->item(0)->nodeValue;
                } else {
                    $currencyCode = "";
                }
                $value = $amount->item(0)->getElementsByTagName("value");
                if ($value->length > 0) {
                    $value = $value->item(0)->nodeValue;
                } else {
                    $value = "";
                }
            }
            $rates = $occupations->item(0)->getElementsByTagName("rates");
            if ($rates->length > 0) {
                $rate = $rates->item(0)->getElementsByTagName("rate");
                if ($rate->length > 0) {
                    $rate = $rate->item(0)->nodeValue;
                } else {
                    $rate = "";
                }
            }
            $comments = $occupations->item(0)->getElementsByTagName("comments");
            if ($comments->length > 0) {
                $from = $comments->item(0)->getElementsByTagName("from");
                if ($from->length > 0) {
                    $from = $from->item(0)->nodeValue;
                } else {
                    $from = "";
                }
                $to = $comments->item(0)->getElementsByTagName("to");
                if ($to->length > 0) {
                    $to = $to->item(0)->nodeValue;
                } else {
                    $to = "";
                }
                $text = $comments->item(0)->getElementsByTagName("text");
                if ($text->length > 0) {
                    $text = $text->item(0)->nodeValue;
                } else {
                    $text = "";
                }
                $type = $comments->item(0)->getElementsByTagName("type");
                if ($type->length > 0) {
                    $type = $type->item(0)->nodeValue;
                } else {
                    $type = "";
                }
                $conditions = $comments->item(0)->getElementsByTagName("conditions");
                if ($conditions->length > 0) {
                    $conditions = $conditions->item(0)->nodeValue;
                } else {
                    $conditions = "";
                }
                $errataType = $comments->item(0)->getElementsByTagName("errataType");
                if ($errataType->length > 0) {
                    $errataType = $errataType->item(0)->nodeValue;
                } else {
                    $errataType = "";
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