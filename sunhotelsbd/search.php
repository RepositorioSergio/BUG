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
echo "COMECOU SEARCH AGORA<br/>";
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

$url = "http://xml.sunhotels.net/15/PostGet/NonStaticXMLAPI.asmx/SearchV2?userName=testagent&password=785623&language=en&currencies=USD&checkInDate=2019-12-12&checkOutDate=2019-12-14&numberOfRooms=1&destination=&destinationID=695&hotelIDs=&resortIDs=&accommodationTypes=&numberOfAdults=2&numberOfChildren=0&childrenAges=&infant=0&sortBy=&sortOrder=&exactDestinationMatch=&blockSuperdeal=&showTransfer=&mealIds=&showCoordinates=&showReviews=&referencePointLatitude=&referencePointLongitude=&maxDistanceFromReferencePoint=&minStarRating=&maxStarRating=&featureIds=&minPrice=&maxPrice=&themeIds=&excludeSharedRooms=&excludeSharedFacilities=&prioritizedHotelIds=&totalRoomsInBatch=&paymentMethodId=&CustomerCountry=gb&B2C=";

$headers = array(
    'Accept-Encoding: gzip,deflate',
    'Host: xml.sunhotels.net',
    'Content-Length: 0'
); 

$ch = curl_init();
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
//curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip,deflate");
//curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
echo $response;
echo "</xmp>";

//die();
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
$inputDoc->loadXML($response);
$searchresult = $inputDoc->getElementsByTagName("searchresult");
$hotels = $searchresult->item(0)->getElementsByTagName("hotels");
$hotel = $hotels->item(0)->getElementsByTagName("hotel");
if ($hotel->length > 0) {
    for ($i=0; $i < $hotel->length; $i++) { 
        $hotelid = $hotel->item($i)->getElementsByTagName("hotel.id");
        if ($hotelid->length > 0) {
            $hotelid = $hotelid->item(0)->nodeValue;
        } else {
            $hotelid = "";
        }
        $destination_id = $hotel->item($i)->getElementsByTagName("destination_id");
        if ($destination_id->length > 0) {
            $destination_id = $destination_id->item(0)->nodeValue;
        } else {
            $destination_id = "";
        }
        $resort_id = $hotel->item($i)->getElementsByTagName("resort_id");
        if ($resort_id->length > 0) {
            $resort_id = $resort_id->item(0)->nodeValue;
        } else {
            $resort_id = "";
        }
        $transfer = $hotel->item($i)->getElementsByTagName("transfer");
        if ($transfer->length > 0) {
            $transfer = $transfer->item(0)->nodeValue;
        } else {
            $transfer = "";
        }
        $notes = $hotel->item($i)->getElementsByTagName("notes");
        if ($notes->length > 0) {
            $notes = $notes->item(0)->nodeValue;
        } else {
            $notes = "";
        }
        $codes = $hotel->item($i)->getElementsByTagName("codes");
        if ($codes->length > 0) {
            $code = $codes->item(0)->getElementsByTagName("code");
            if ($code->length > 0) {
                $codetype = $code->item(0)->getAttribute("type");
                $codevalue = $code->item(0)->getAttribute("value");
            } else {
                $codetype = "";
                $codevalue = "";
            }
        }
        $distance = $hotel->item($i)->getElementsByTagName("distance");
        if ($distance->length > 0) {
            $distance = $distance->item(0)->nodeValue;
        } else {
            $distance = "";
        }

        /* try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('search');
            $insert->values(array(
                'hotelid' => $hotelid,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'destination_id' => $destination_id,
                'resort_id' => $resort_id,
                'transfer' => $transfer,
                'notes' => $notes,
                'codetype' => $codetype,
                'codevalue' => $codevalue,
                'distance' => $distance
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO 1: " . $e;
            echo $return;
        } */

        $roomtypes = $hotel->item($i)->getElementsByTagName("roomtypes");
        if ($roomtypes->length > 0) {
            $roomtype = $roomtypes->item(0)->getElementsByTagName("roomtype");
            if ($roomtype->length > 0) {
                for ($j=0; $j < $roomtype->length; $j++) { 
                    $roomtypeid = $roomtype->item($j)->getElementsByTagName("roomtype.ID");
                    if ($roomtypeid->length > 0) {
                        $roomtypeid = $roomtypeid->item(0)->nodeValue;
                    } else {
                        $roomtypeid = "";
                    }

                    $rooms = $roomtype->item($j)->getElementsByTagName("rooms");
                    if ($rooms->length > 0) {
                        $room = $rooms->item(0)->getElementsByTagName("room");
                        if ($room->length > 0) {
                            for ($jAux=0; $jAux < $room->length; $jAux++) { 
                                $roomid = $room->item($jAux)->getElementsByTagName("id");
                                if ($roomid->length > 0) {
                                    $roomid = $roomid->item(0)->nodeValue;
                                } else {
                                    $roomid = "";
                                }
                                $beds = $room->item($jAux)->getElementsByTagName("beds");
                                if ($beds->length > 0) {
                                    $beds = $beds->item(0)->nodeValue;
                                } else {
                                    $beds = "";
                                }
                                $extrabeds = $room->item($jAux)->getElementsByTagName("extrabeds");
                                if ($extrabeds->length > 0) {
                                    $extrabeds = $extrabeds->item(0)->nodeValue;
                                } else {
                                    $extrabeds = "";
                                }
                                $notes = $room->item($jAux)->getElementsByTagName("notes");
                                if ($notes->length > 0) {
                                    $note = $notes->item(0)->getElementsByTagName("note");
                                    if ($note->length > 0) {
                                        $notestart_date = $note->item(0)->getAttribute("start_date");
                                        $noteend_date = $note->item(0)->getAttribute("end_date");
                                        $text = $note->item(0)->getElementsByTagName("text");
                                        if ($text->length > 0) {
                                            $text = $text->item(0)->nodeValue;
                                        } else {
                                            $text = "";
                                        }
                                    }
                                }
                                $isSuperDeal = $room->item($jAux)->getElementsByTagName("isSuperDeal");
                                if ($isSuperDeal->length > 0) {
                                    $isSuperDeal = $isSuperDeal->item(0)->nodeValue;
                                } else {
                                    $isSuperDeal = "";
                                }
                                $isBestBuy = $room->item($jAux)->getElementsByTagName("isBestBuy");
                                if ($isBestBuy->length > 0) {
                                    $isBestBuy = $isBestBuy->item(0)->nodeValue;
                                } else {
                                    $isBestBuy = "";
                                }
                                $cancellation_policies = $room->item($jAux)->getElementsByTagName("cancellation_policies");
                                if ($cancellation_policies->length > 0) {
                                    $cancellation_policy = $cancellation_policies->item(0)->getElementsByTagName("cancellation_policy");
                                    if ($cancellation_policy->length > 0) {
                                        $deadline = $cancellation_policy->item(0)->getElementsByTagName("deadline");
                                        if ($deadline->length > 0) {
                                            $deadline = $deadline->item(0)->nodeValue;
                                        } else {
                                            $deadline = "";
                                        }
                                        $percentage = $cancellation_policy->item(0)->getElementsByTagName("percentage");
                                        if ($percentage->length > 0) {
                                            $percentage = $percentage->item(0)->nodeValue;
                                        } else {
                                            $percentage = "";
                                        }
                                    }
                                }
                                $paymentMethods = $room->item($jAux)->getElementsByTagName("paymentMethods");
                                if ($paymentMethods->length > 0) {
                                    $paymentMethod = $paymentMethods->item(0)->getElementsByTagName("paymentMethod");
                                    if ($paymentMethod->length > 0) {
                                        $paymentMethodid = $paymentMethod->item(0)->getAttribute("id");
                                    }
                                }

                                /* try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('rooms_search');
                                    $insert->values(array(
                                        'roomid' => $roomid,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'beds' => $beds,
                                        'extrabeds' => $extrabeds,
                                        'notestart_date' => $notestart_date,
                                        'noteend_date' => $noteend_date,
                                        'text' => $text,
                                        'isSuperDeal' => $isSuperDeal,
                                        'isBestBuy' => $isBestBuy,
                                        'deadline' => $deadline,
                                        'percentage' => $percentage,
                                        'paymentMethodid' => $paymentMethodid,
                                        'roomtypeid' => $roomtypeid,
                                        'hotelid' => $hotelid
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO 2: " . $e;
                                    echo $return;
                                } */

                                $meals = $room->item($jAux)->getElementsByTagName("meals");
                                if ($meals->length > 0) {
                                    $meal = $meals->item(0)->getElementsByTagName("meal");
                                    if ($meal->length > 0) {
                                        for ($jAux2=0; $jAux2 < $meal->length; $jAux2++) { 
                                            $mealid = $meal->item($jAux2)->getElementsByTagName("id");
                                            if ($mealid->length > 0) {
                                                $mealid = $mealid->item(0)->nodeValue;
                                            } else {
                                                $mealid = "";
                                            }
                                            $labelId = $meal->item($jAux2)->getElementsByTagName("labelId");
                                            if ($labelId->length > 0) {
                                                $labelId = $labelId->item(0)->nodeValue;
                                            } else {
                                                $labelId = "";
                                            }
                                            $discount = $meal->item($jAux2)->getElementsByTagName("discount");
                                            if ($discount->length > 0) {
                                                $typeId = $discount->item(0)->getElementsByTagName("typeId");
                                                if ($typeId->length > 0) {
                                                    $typeId = $typeId->item(0)->nodeValue;
                                                } else {
                                                    $typeId = "";
                                                }
                                                $amounts = $discount->item(0)->getElementsByTagName("amounts");
                                                if ($amounts->length > 0) {
                                                    $amount = $amounts->item(0)->getElementsByTagName("amount");
                                                    if ($amount->length > 0) {
                                                        $amountcurrency = $amount->item(0)->getAttribute("currency");
                                                        $amountpaymentMethods = $amount->item(0)->getAttribute("paymentMethods");
                                                    }
                                                }
                                            }
                                            $prices = $meal->item($jAux2)->getElementsByTagName("prices");
                                            if ($prices->length > 0) {
                                                $price = $prices->item(0)->getElementsByTagName("price");
                                                if ($price->length > 0) {
                                                    $paymentMethods = $price->item(0)->getAttribute("paymentMethods");
                                                    $currency = $price->item(0)->getAttribute("currency");
                                                    $price = $price->item(0)->nodeValue;
                                                } else {
                                                    $paymentMethods = "";
                                                    $currency = "";
                                                    $price = 0;
                                                }
                                            }

                                            /* try {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('meals_search');
                                                $insert->values(array(
                                                    'datetime_created' => time(),
                                                    'datetime_updated' => 0,
                                                    'mealid' => $mealid,
                                                    'labelid' => $labelId,
                                                    'typeid' => $typeId,
                                                    'amountcurrency' => $amountcurrency,
                                                    'amountpaymentmethods' => $amountpaymentMethods,
                                                    'paymentmethods' => $paymentMethods,
                                                    'currency' => $currency,
                                                    'price' => $price,
                                                    'roomid' => $roomid
                                                ), $insert::VALUES_MERGE);
                                                $statement = $sql->prepareStatementForSqlObject($insert);
                                                $results = $statement->execute();
                                                $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                            } catch (\Exception $e) {
                                                echo $return;
                                                echo "ERRO 3: " . $e;
                                                echo $return;
                                            } */
                                        }
                                    }
                                }
                            }
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
echo 'Done';
?>