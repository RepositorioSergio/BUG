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
echo "COMECOU HOTELS";
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

$ipaddress = "142.44.216.144";


$raw = '<Request Type="Reservation" Version="1.0">
    <affiliateid>DIVISAXML</affiliateid>
    <language>esp</language>
    <currency>PE</currency>
    <ip>' . $ipaddress . '</ip>
    <uid>m2r32b14es10socxtxs4y4ht</uid>
    <firstname>Alvaro</firstname>
    <lastname>prueba</lastname>
    <emailaddress>alvaro.prueba@bestday.com</emailaddress>
    <clientcountry>MX</clientcountry>
    <country>MX</country>
    <address>bonampak 4</address>
    <city>cancun</city>
    <state>QROO</state>
    <zip>77500</zip>
    <total>6118.8876953125</total>
    <naturalperson>
        <gender/>
        <nationality/>
        <number/>
        <type/>
    </naturalperson>
    <legalperson>
        <businessname/>
        <number/>
        <type/>
    </legalperson>
    <phones>
        <phone>
            <type>2</type>
            <number>9981454221</number>
        </phone>
    </phones>
    <hotels>
        <hotel>
            <hotelid>5622</hotelid>
            <roomtype>APTOSUP2</roomtype>
            <mealplan>ZZ</mealplan>
            <datearrival>20200108</datearrival>
            <datedeparture>20200111</datedeparture>
            <marketid>MOBILE</marketid>
            <contractid>20000</contractid>
            <dutypercent>0</dutypercent>
            <rooms>
                <room>
                    <name>Alvaro</name>
                    <lastname>prueba</lastname>
                    <amount>6118.8876953125</amount>
                    <status>AV</status>
                    <ratekey>APTOSUP2ZZ</ratekey>
                    <adults>4</adults>
                    <kids>0</kids>
                    <k1a>0</k1a>
                </room>
            </rooms>
        </hotel>
    </hotels>
<payments>
<agencycreditpayment>
    <type></type>
    <currency>PE</currency>
    <amount>6118.8876953125</amount>
</agencycreditpayment>
</payments>
</Request>';

echo "<xmp>";
var_dump($raw);
echo "</xmp>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $HotelDoserviceURL . '/Book');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: text/xml",
    "Content-type: text/xml;charset=\"utf-8\"",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>";

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
$BookingResponse = $inputDoc->getElementsByTagName("BookingResponse");
if ($BookingResponse->length > 0) {
    $confirmationid = $BookingResponse->item(0)->getElementsByTagName('confirmationid');
    if ($confirmationid->length > 0) {
        $confirmationid = $confirmationid->item(0)->nodeValue;
    } else {
        $confirmationid = "";
    }
    $airlinepnr = $BookingResponse->item(0)->getElementsByTagName('airlinepnr');
    if ($airlinepnr->length > 0) {
        $airlinepnr = $airlinepnr->item(0)->nodeValue;
    } else {
        $airlinepnr = "";
    }
    $currency = $BookingResponse->item(0)->getElementsByTagName('currency');
    if ($currency->length > 0) {
        $currency = $currency->item(0)->nodeValue;
    } else {
        $currency = "";
    }
    $total = $BookingResponse->item(0)->getElementsByTagName('total');
    if ($total->length > 0) {
        $total = $total->item(0)->nodeValue;
    } else {
        $total = "";
    }
    $statusinternet = $BookingResponse->item(0)->getElementsByTagName('statusinternet');
    if ($statusinternet->length > 0) {
        $statusinternet = $statusinternet->item(0)->nodeValue;
    } else {
        $statusinternet = "";
    }
    $statusbooking = $BookingResponse->item(0)->getElementsByTagName('statusbooking');
    if ($statusbooking->length > 0) {
        $statusbooking = $statusbooking->item(0)->nodeValue;
    } else {
        $statusbooking = "";
    }
    $statuspayment = $BookingResponse->item(0)->getElementsByTagName('statuspayment');
    if ($statuspayment->length > 0) {
        $statuspayment = $statuspayment->item(0)->nodeValue;
    } else {
        $statuspayment = "";
    }
    $effective = $BookingResponse->item(0)->getElementsByTagName('effective');
    if ($effective->length > 0) {
        $effective = $effective->item(0)->nodeValue;
    } else {
        $effective = "";
    }
    $operatorname = $BookingResponse->item(0)->getElementsByTagName('operatorname');
    if ($operatorname->length > 0) {
        $operatorname = $operatorname->item(0)->nodeValue;
    } else {
        $operatorname = "";
    }
    $operatoremail = $BookingResponse->item(0)->getElementsByTagName('operatoremail');
    if ($operatoremail->length > 0) {
        $operatoremail = $operatoremail->item(0)->nodeValue;
    } else {
        $operatoremail = "";
    }
    //Rooms
    $Rooms = $BookingResponse->item(0)->getElementsByTagName('Rooms');
    if ($Rooms->length > 0) {
        $Room = $Rooms->item(0)->getElementsByTagName('Room');
        if ($Room->length > 0) {
            $Id = $Room->item(0)->getElementsByTagName('Id');
            if ($Id->length > 0) {
                $Id = $Id->item(0)->nodeValue;
            } else {
                $Id = "";
            }
            $MealPlanId = $Room->item(0)->getElementsByTagName('MealPlanId');
            if ($MealPlanId->length > 0) {
                $MealPlanId = $MealPlanId->item(0)->nodeValue;
            } else {
                $MealPlanId = "";
            }
            $PaxId = $Room->item(0)->getElementsByTagName('PaxId');
            if ($PaxId->length > 0) {
                $PaxId = $PaxId->item(0)->nodeValue;
            } else {
                $PaxId = "";
            }
            //CancellationPolicy
            $CancellationPolicy = $Room->item(0)->getElementsByTagName('CancellationPolicy');
            if ($CancellationPolicy->length > 0) {
                $CPId = $CancellationPolicy->item(0)->getElementsByTagName('Id');
                if ($CPId->length > 0) {
                    $CPId = $CPId->item(0)->nodeValue;
                } else {
                    $CPId = "";
                }
                $CPDescription = $CancellationPolicy->item(0)->getElementsByTagName('Description');
                if ($CPDescription->length > 0) {
                    $CPDescription = $CPDescription->item(0)->nodeValue;
                } else {
                    $CPDescription = "";
                }
                $CPAmount = $CancellationPolicy->item(0)->getElementsByTagName('Amount');
                if ($CPAmount->length > 0) {
                    $CPAmount = $CPAmount->item(0)->nodeValue;
                } else {
                    $CPAmount = "";
                }
                $DaysToApplyCancellation = $CancellationPolicy->item(0)->getElementsByTagName('DaysToApplyCancellation');
                if ($DaysToApplyCancellation->length > 0) {
                    $DaysToApplyCancellation = $DaysToApplyCancellation->item(0)->nodeValue;
                } else {
                    $DaysToApplyCancellation = "";
                }
                $PaymentLimitDay = $CancellationPolicy->item(0)->getElementsByTagName('PaymentLimitDay');
                if ($PaymentLimitDay->length > 0) {
                    $PaymentLimitDay = $PaymentLimitDay->item(0)->nodeValue;
                } else {
                    $PaymentLimitDay = "";
                }
                $NightsPenalty = $CancellationPolicy->item(0)->getElementsByTagName('NightsPenalty');
                if ($NightsPenalty->length > 0) {
                    $NightsPenalty = $NightsPenalty->item(0)->nodeValue;
                } else {
                    $NightsPenalty = "";
                }
                $IsNonRefundable = $CancellationPolicy->item(0)->getElementsByTagName('IsNonRefundable');
                if ($CPDescription->length > 0) {
                    $IsNonRefundable = $IsNonRefundable->item(0)->nodeValue;
                } else {
                    $IsNonRefundable = "";
                }
                $IdPolicyInterface = $CancellationPolicy->item(0)->getElementsByTagName('IdPolicyInterface');
                if ($IdPolicyInterface->length > 0) {
                    $IdPolicyInterface = $IdPolicyInterface->item(0)->nodeValue;
                } else {
                    $IdPolicyInterface = "";
                }
                $NoShow = $CancellationPolicy->item(0)->getElementsByTagName('NoShow');
                if ($NoShow->length > 0) {
                    $DateFrom = $NoShow->item(0)->getElementsByTagName('DateFrom');
                    if ($DateFrom->length > 0) {
                        $DateFrom = $DateFrom->item(0)->nodeValue;
                    } else {
                        $DateFrom = "";
                    }
                    $Amount = $NoShow->item(0)->getElementsByTagName('Amount');
                    if ($Amount->length > 0) {
                        $Amount = $Amount->item(0)->nodeValue;
                    } else {
                        $Amount = "";
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