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
echo "COMECOU BOOKING<br/>";
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


$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

date_default_timezone_set('UTC');
//$date = date("Y-m-d H:i:s");
$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");

$url = "http://xml.sunhotels.net/15/PostGet/NonStaticXMLAPI.asmx?op=BookV2";

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <BookV2 xmlns="http://xml.sunhotels.net/15/">
      <userName>testagent</userName>
      <password>785623</password>
      <currency>USD</currency>
      <language>en</language>
      <email>alexis.sanchez@gmail.com</email>
      <checkInDate>2020-06-12</checkInDate>
      <checkOutDate>2020-06-17</checkOutDate>
      <roomId>14656827</roomId>
      <rooms>1</rooms>
      <adults>2</adults>
      <children>0</children>
      <infant>0</infant>
      <yourRef>jk12aah</yourRef>
      <specialrequest></specialrequest>
      <mealId>1</mealId>
      <adultGuest1FirstName>Alexis</adultGuest1FirstName>
      <adultGuest1LastName>Sanchez</adultGuest1LastName>
      <adultGuest2FirstName>Martina</adultGuest2FirstName>
      <adultGuest2LastName>Sanchez</adultGuest2LastName>
      <adultGuest3FirstName></adultGuest3FirstName>
      <adultGuest3LastName></adultGuest3LastName>
      <adultGuest4FirstName></adultGuest4FirstName>
      <adultGuest4LastName></adultGuest4LastName>
      <adultGuest5FirstName></adultGuest5FirstName>
      <adultGuest5LastName></adultGuest5LastName>
      <adultGuest6FirstName></adultGuest6FirstName>
      <adultGuest6LastName></adultGuest6LastName>
      <adultGuest7FirstName></adultGuest7FirstName>
      <adultGuest7LastName></adultGuest7LastName>
      <adultGuest8FirstName></adultGuest8FirstName>
      <adultGuest8LastName></adultGuest8LastName>
      <adultGuest9FirstName></adultGuest9FirstName>
      <adultGuest9LastName></adultGuest9LastName>
      <childrenGuest1FirstName></childrenGuest1FirstName>
      <childrenGuest1LastName></childrenGuest1LastName>
      <childrenGuestAge1></childrenGuestAge1>
      <childrenGuest2FirstName></childrenGuest2FirstName>
      <childrenGuest2LastName></childrenGuest2LastName>
      <childrenGuestAge2></childrenGuestAge2>
      <childrenGuest3FirstName></childrenGuest3FirstName>
      <childrenGuest3LastName></childrenGuest3LastName>
      <childrenGuestAge3></childrenGuestAge3>
      <childrenGuest4FirstName></childrenGuest4FirstName>
      <childrenGuest4LastName></childrenGuest4LastName>
      <childrenGuestAge4></childrenGuestAge4>
      <childrenGuest5FirstName></childrenGuest5FirstName>
      <childrenGuest5LastName></childrenGuest5LastName>
      <childrenGuestAge5></childrenGuestAge5>
      <childrenGuest6FirstName></childrenGuest6FirstName>
      <childrenGuest6LastName></childrenGuest6LastName>
      <childrenGuestAge6></childrenGuestAge6>
      <childrenGuest7FirstName></childrenGuest7FirstName>
      <childrenGuest7LastName></childrenGuest7LastName>
      <childrenGuestAge7></childrenGuestAge7>
      <childrenGuest8FirstName></childrenGuest8FirstName>
      <childrenGuest8LastName></childrenGuest8LastName>
      <childrenGuestAge8></childrenGuestAge8>
      <childrenGuest9FirstName></childrenGuest9FirstName>
      <childrenGuest9LastName></childrenGuest9LastName>
      <childrenGuestAge9></childrenGuestAge9>
      <paymentMethodId>1</paymentMethodId>
      <creditCardType></creditCardType>
      <creditCardNumber></creditCardNumber>
      <creditCardHolder></creditCardHolder>
      <creditCardCVV2></creditCardCVV2>
      <creditCardExpYear></creditCardExpYear>
      <creditCardExpMonth></creditCardExpMonth>
      <customerEmail>alexis.sanchez@gmail.com</customerEmail>
      <invoiceRef></invoiceRef>
      <customerCountry>es</customerCountry>
      <b2c>0</b2c>
      <commissionAmountInHotelCurrency></commissionAmountInHotelCurrency>
      <preBookCode>cd52830d-7062-4660-9e03-32f871aaf011</preBookCode>
    </BookV2>
  </soap:Body>
</soap:Envelope>';

$headers = array(
    'Accept-Encoding: gzip,deflate',
    'Host: xml.sunhotels.net',
    'Content-Type: text/xml; charset=utf-8',
    'SOAPAction: http://xml.sunhotels.net/15/BookV2',
    'Content-Length: ' . strlen($raw)
); 

$ch = curl_init();
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip,deflate");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
echo $response;
echo "</xmp>";

die();
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response2);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");

$BookV2Response = $Body->item(0)->getElementsByTagName("BookV2Response");
if ($BookV2Response->length > 0) {
    $bookingnumber = $BookV2Response->item(0)->getElementsByTagName("bookingnumber");
    if ($bookingnumber->length > 0) {
        $bookingnumber = $bookingnumber->item(0)->nodeValue;
    } else {
        $bookingnumber = "";
    }
    $hotelid = $BookV2Response->item(0)->getElementsByTagName("hotel.id");
    if ($hotelid->length > 0) {
        $hotelid = $hotelid->item(0)->nodeValue;
    } else {
        $hotelid = "";
    }
    $hotelname = $BookV2Response->item(0)->getElementsByTagName("hotel.name");
    if ($hotelname->length > 0) {
        $hotelname = $hotelname->item(0)->nodeValue;
    } else {
        $hotelname = "";
    }
    $hoteladdress = $BookV2Response->item(0)->getElementsByTagName("hotel.address");
    if ($hoteladdress->length > 0) {
        $hoteladdress = $hoteladdress->item(0)->nodeValue;
    } else {
        $hoteladdress = "";
    }
    $hotelphone = $BookV2Response->item(0)->getElementsByTagName("hotel.phone");
    if ($hotelphone->length > 0) {
        $hotelphone = $hotelphone->item(0)->nodeValue;
    } else {
        $hotelphone = "";
    }
    $numberofrooms = $BookV2Response->item(0)->getElementsByTagName("numberofrooms");
    if ($numberofrooms->length > 0) {
        $numberofrooms = $numberofrooms->item(0)->nodeValue;
    } else {
        $numberofrooms = "";
    }
    $roomtype = $BookV2Response->item(0)->getElementsByTagName("room.type");
    if ($roomtype->length > 0) {
        $roomtype = $roomtype->item(0)->nodeValue;
    } else {
        $roomtype = "";
    }
    $room_englishType = $BookV2Response->item(0)->getElementsByTagName("room.englishType");
    if ($room_englishType->length > 0) {
        $room_englishType = $room_englishType->item(0)->nodeValue;
    } else {
        $room_englishType = "";
    }
    $mealId = $BookV2Response->item(0)->getElementsByTagName("mealId");
    if ($mealId->length > 0) {
        $mealId = $mealId->item(0)->nodeValue;
    } else {
        $mealId = "";
    }
    $meal = $BookV2Response->item(0)->getElementsByTagName("meal");
    if ($meal->length > 0) {
        $meal = $meal->item(0)->nodeValue;
    } else {
        $meal = "";
    }
    $mealLabel = $BookV2Response->item(0)->getElementsByTagName("mealLabel");
    if ($mealLabel->length > 0) {
        $mealLabel = $mealLabel->item(0)->nodeValue;
    } else {
        $mealLabel = "";
    }
    $englishMeal = $BookV2Response->item(0)->getElementsByTagName("englishMeal");
    if ($englishMeal->length > 0) {
        $englishMeal = $englishMeal->item(0)->nodeValue;
    } else {
        $englishMeal = "";
    }
    $englishMealLabel = $BookV2Response->item(0)->getElementsByTagName("englishMealLabel");
    if ($englishMealLabel->length > 0) {
        $englishMealLabel = $englishMealLabel->item(0)->nodeValue;
    } else {
        $englishMealLabel = "";
    }
    $checkindate = $BookV2Response->item(0)->getElementsByTagName("checkindate");
    if ($checkindate->length > 0) {
        $checkindate = $checkindate->item(0)->nodeValue;
    } else {
        $checkindate = "";
    }
    $checkoutdate = $BookV2Response->item(0)->getElementsByTagName("checkoutdate");
    if ($checkoutdate->length > 0) {
        $checkoutdate = $checkoutdate->item(0)->nodeValue;
    } else {
        $checkoutdate = "";
    }
    $currency = $BookV2Response->item(0)->getElementsByTagName("currency");
    if ($currency->length > 0) {
        $currency = $currency->item(0)->nodeValue;
    } else {
        $currency = "";
    }
    $bookingdate = $BookV2Response->item(0)->getElementsByTagName("bookingdate");
    if ($bookingdate->length > 0) {
        $bookingdate = $bookingdate->item(0)->nodeValue;
    } else {
        $bookingdate = "";
    }
    $bookingdate_timezone = $BookV2Response->item(0)->getElementsByTagName("bookingdate.timezone");
    if ($bookingdate_timezone->length > 0) {
        $bookingdate_timezone = $bookingdate_timezone->item(0)->nodeValue;
    } else {
        $bookingdate_timezone = "";
    }
    $earliestNonFreeCancellationDate_CET = $BookV2Response->item(0)->getElementsByTagName("earliestNonFreeCancellationDate.CET");
    if ($earliestNonFreeCancellationDate_CET->length > 0) {
        $earliestNonFreeCancellationDate_CET = $earliestNonFreeCancellationDate_CET->item(0)->nodeValue;
    } else {
        $earliestNonFreeCancellationDate_CET = "";
    }
    $earliestNonFreeCancellationDate_Local = $BookV2Response->item(0)->getElementsByTagName("earliestNonFreeCancellationDate.Local");
    if ($earliestNonFreeCancellationDate_Local->length > 0) {
        $earliestNonFreeCancellationDate_Local = $earliestNonFreeCancellationDate_Local->item(0)->nodeValue;
    } else {
        $earliestNonFreeCancellationDate_Local = "";
    }
    $yourref = $BookV2Response->item(0)->getElementsByTagName("yourref");
    if ($yourref->length > 0) {
        $yourref = $yourref->item(0)->nodeValue;
    } else {
        $yourref = "";
    }
    $voucher = $BookV2Response->item(0)->getElementsByTagName("voucher");
    if ($voucher->length > 0) {
        $voucher = $voucher->item(0)->nodeValue;
    } else {
        $voucher = "";
    }
    $bookedBy = $BookV2Response->item(0)->getElementsByTagName("bookedBy");
    if ($bookedBy->length > 0) {
        $bookedBy = $bookedBy->item(0)->nodeValue;
    } else {
        $bookedBy = "";
    }
    $transferbooked = $BookV2Response->item(0)->getElementsByTagName("transferbooked");
    if ($transferbooked->length > 0) {
        $transferbooked = $transferbooked->item(0)->nodeValue;
    } else {
        $transferbooked = "";
    }
    $roomNotes = $BookV2Response->item(0)->getElementsByTagName("roomNotes");
    if ($roomNotes->length > 0) {
        $roomNotes = $roomNotes->item(0)->nodeValue;
    } else {
        $roomNotes = "";
    }
    $englishRoomNotes = $BookV2Response->item(0)->getElementsByTagName("englishRoomNotes");
    if ($englishRoomNotes->length > 0) {
        $englishRoomNotes = $englishRoomNotes->item(0)->nodeValue;
    } else {
        $englishRoomNotes = "";
    }
    //paymentmethod
    $paymentmethod = $BookV2Response->item(0)->getElementsByTagName("paymentmethod");
    if ($paymentmethod->length > 0) {
        $paymentmethodid = $paymentmethod->item(0)->getAttribute("id");
        $paymentmethodname = $paymentmethod->item(0)->getAttribute("name");
    } else {
        $paymentmethodid = "";
        $paymentmethodname = "";
    }
    //prices
    $price2 = 0;
    $prices = $BookV2Response->item(0)->getElementsByTagName("prices");
    if ($prices->length > 0) {
        $price = $prices->item(0)->getElementsByTagName("price");
        if ($price->length > 0) {
            for ($i=0; $i < $price->length; $i++) { 
                $paymentMethods = $price->item($i)->getAttribute("paymentMethods");
                $currency = $price->item($i)->getAttribute("currency");
                $price2 = $price->item($i)->nodeValue;
            }
        }
    }
    //cancellationpolicies
    $cancellationpolicies = $BookV2Response->item(0)->getElementsByTagName("cancellationpolicies");
    if ($cancellationpolicies->length > 0) {
        for ($j=0; $j < $cancellationpolicies->length; $j++) { 
            $deadline = $cancellationpolicies->item($j)->getElementsByTagName("deadline");
            if ($deadline->length > 0) {
                $deadline = $deadline->item(0)->nodeValue;
            } else {
                $deadline = "";
            }
            $percentage = $cancellationpolicies->item($j)->getElementsByTagName("percentage");
            if ($percentage->length > 0) {
                $percentage = $percentage->item(0)->nodeValue;
            } else {
                $percentage = "";
            }
            $text = $cancellationpolicies->item($j)->getElementsByTagName("text");
            if ($text->length > 0) {
                $text = $text->item(0)->nodeValue;
            } else {
                $text = "";
            }
        }
    }
    //hotelNotes
    $hotelNotes = $BookV2Response->item(0)->getElementsByTagName("hotelNotes");
    if ($hotelNotes->length > 0) {
        $hotelNote = $hotelNotes->item(0)->getElementsByTagName("hotelNote");
        if ($hotelNote->length > 0) {
            for ($k=0; $k < $hotelNote->length; $k++) { 
                $end_date = $hotelNote->item($k)->getAttribute("end_date");
                $start_date = $hotelNote->item($k)->getAttribute("start_date");
                $text = $hotelNote->item($k)->getElementsByTagName("text");
                if ($text->length > 0) {
                    $text = $text->item(0)->nodeValue;
                } else {
                    $text = "";
                }
            }
        }
    }
    //englishHotelNotes
    $englishHotelNotes = $BookV2Response->item(0)->getElementsByTagName("englishHotelNotes");
    if ($englishHotelNotes->length > 0) {
        $englishHotelNote = $englishHotelNotes->item(0)->getElementsByTagName("englishHotelNote");
        if ($englishHotelNote->length > 0) {
            for ($x=0; $x < $englishHotelNote->length; $x++) { 
                $end_date = $englishHotelNote->item($x)->getAttribute("end_date");
                $start_date = $englishHotelNote->item($x)->getAttribute("start_date");
                $text = $englishHotelNote->item($x)->getElementsByTagName("text");
                if ($text->length > 0) {
                    $text = $text->item(0)->nodeValue;
                } else {
                    $text = "";
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