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

$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];


$url = 'http://testxml.youtravel.com/webservicestest/confirmations/get_agent_bookings.aspx?LangID=EN&Username=xmltestme&Password=testme&res_from=28/08/2019&res_to=11/10/2019';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'text/xml;charset=ISO-8859-1',
    'Content-Length' => '0'
));
$client->setUri($url);
$client->setMethod('POST');
//$client->setRawBody($raw);
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

echo "RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
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
$HtSearchRq = $inputDoc->getElementsByTagName("HtSearchRq");

$Booking_info = $HtSearchRq->item(0)->getElementsByTagName("Booking_info");
if ($Booking_info->length > 0) {
    $Booking = $Booking_info->item(0)->getElementsByTagName("Booking");
    if ($Booking->length > 0) {
        for ($i=0; $i < $Booking->length; $i++) { 
            $CountryCode = $Booking->item($i)->getAttribute("CountryCode");
            $Num_of_rooms = $Booking->item($i)->getAttribute("Num_of_rooms");
            $To_day = $Booking->item($i)->getAttribute("To_day");
            $From_day = $Booking->item($i)->getAttribute("From_day");
            $Res_Date = $Booking->item($i)->getAttribute("Res_Date");
            $ref = $Booking->item($i)->getAttribute("ref");

            $Booking_Status = $Booking->item($i)->getElementsByTagName("Booking_Status");
            if ($Booking_Status->length > 0) {
                $Booking_Status = $Booking_Status->item(0)->nodeValue;
            } else {
                $Booking_Status = "";
            }
            $Conf_Status = $Booking->item($i)->getElementsByTagName("Conf_Status");
            if ($Conf_Status->length > 0) {
                $Conf_Status = $Conf_Status->item(0)->nodeValue;
            } else {
                $Conf_Status = "";
            }
            $Currency = $Booking->item($i)->getElementsByTagName("Currency");
            if ($Currency->length > 0) {
                $Currency = $Currency->item(0)->nodeValue;
            } else {
                $Currency = "";
            }
            $Exchange_rate = $Booking->item($i)->getElementsByTagName("Exchange_rate");
            if ($Exchange_rate->length > 0) {
                $Exchange_rate = $Exchange_rate->item(0)->nodeValue;
            } else {
                $Exchange_rate = "";
            }
            $Final_Rate = $Booking->item($i)->getElementsByTagName("Final_Rate");
            if ($Final_Rate->length > 0) {
                $Final_Rate = $Final_Rate->item(0)->nodeValue;
            } else {
                $Final_Rate = "";
            }

            $FlightDetails = $Booking->item($i)->getElementsByTagName("FlightDetails");
            if ($FlightDetails->length > 0) {
                $ArrivalInfo = $FlightDetails->item(0)->getElementsByTagName("ArrivalInfo");
                if ($ArrivalInfo->length > 0) {
                    $ArrivalInfo = $ArrivalInfo->item(0)->nodeValue;
                } else {
                    $ArrivalInfo = "";
                }
                $ArrivalAirportCode = $FlightDetails->item(0)->getElementsByTagName("ArrivalAirportCode");
                if ($ArrivalAirportCode->length > 0) {
                    $ArrivalAirportCode = $ArrivalAirportCode->item(0)->nodeValue;
                } else {
                    $ArrivalAirportCode = "";
                }
                $DepartureInfo = $FlightDetails->item(0)->getElementsByTagName("DepartureInfo");
                if ($DepartureInfo->length > 0) {
                    $DepartureInfo = $DepartureInfo->item(0)->nodeValue;
                } else {
                    $DepartureInfo = "";
                }
                $DepartureAirportCode = $FlightDetails->item(0)->getElementsByTagName("DepartureAirportCode");
                if ($DepartureAirportCode->length > 0) {
                    $DepartureAirportCode = $DepartureAirportCode->item(0)->nodeValue;
                } else {
                    $DepartureAirportCode = "";
                }
            }

            $Hotel = $Booking->item($i)->getElementsByTagName("Hotel");
            if ($Hotel->length > 0) {
                $ID = $Hotel->item(0)->getAttribute("ID");
                $Name = $Hotel->item(0)->getAttribute("Name");

                $GWGHotelID = $Hotel->item(0)->getElementsByTagName("GWGHotelID");
                if ($GWGHotelID->length > 0) {
                    $GWGHotelID = $GWGHotelID->item(0)->nodeValue;
                } else {
                    $GWGHotelID = "";
                }

                $Hotel_Address = $Hotel->item(0)->getElementsByTagName("Hotel_Address");
                if ($Hotel_Address->length > 0) {
                    $Address = $Hotel_Address->item(0)->getElementsByTagName("Address");
                    if ($Address->length > 0) {
                        $Address = $Address->item(0)->nodeValue;
                    } else {
                        $Address = "";
                    }
                    $Post_Code = $Hotel_Address->item(0)->getElementsByTagName("Post_Code");
                    if ($Post_Code->length > 0) {
                        $Post_Code = $Post_Code->item(0)->nodeValue;
                    } else {
                        $Post_Code = "";
                    }
                    $City = $Hotel_Address->item(0)->getElementsByTagName("City");
                    if ($City->length > 0) {
                        $City = $City->item(0)->nodeValue;
                    } else {
                        $City = "";
                    }
                    $Phone = $Hotel_Address->item(0)->getElementsByTagName("Phone");
                    if ($Phone->length > 0) {
                        $Phone = $Phone->item(0)->nodeValue;
                    } else {
                        $Phone = "";
                    }
                    $Fax = $Hotel_Address->item(0)->getElementsByTagName("Fax");
                    if ($Fax->length > 0) {
                        $Fax = $Fax->item(0)->nodeValue;
                    } else {
                        $Fax = "";
                    }
                }

                $Room = $Hotel->item(0)->getElementsByTagName("Room");
                if ($Room->length > 0) {
                    $ID = $Room->item(0)->getAttribute("ID");

                    $RoomType = $Room->item(0)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        $RoomType = $RoomType->item(0)->nodeValue;
                    } else {
                        $RoomType = "";
                    }
                    $ViewType = $Room->item(0)->getElementsByTagName("ViewType");
                    if ($ViewType->length > 0) {
                        $ViewType = $ViewType->item(0)->nodeValue;
                    } else {
                        $ViewType = "";
                    }
                    $IndexType = $Room->item(0)->getElementsByTagName("IndexType");
                    if ($IndexType->length > 0) {
                        $IndexType = $IndexType->item(0)->nodeValue;
                    } else {
                        $IndexType = "";
                    }
                    $GWGRoomType = $Room->item(0)->getElementsByTagName("GWGRoomType");
                    if ($GWGRoomType->length > 0) {
                        $GWGRoomType = $GWGRoomType->item(0)->nodeValue;
                    } else {
                        $GWGRoomType = "";
                    }
                    $board = $Room->item(0)->getElementsByTagName("board");
                    if ($board->length > 0) {
                        $board = $board->item(0)->nodeValue;
                    } else {
                        $board = "";
                    }

                    $GuestCounts = $Room->item(0)->getElementsByTagName("GuestCounts");
                    if ($GuestCounts->length > 0) {
                        $GuestCount = $GuestCounts->item(0)->getElementsByTagName("GuestCount");
                        if ($GuestCount->length > 0) {
                            $Count = $GuestCount->item(0)->getAttribute("Count");
                            $AgeQualifyingCode = $GuestCount->item(0)->getAttribute("AgeQualifyingCode");
                        }
                    }
                }

                $Addons = $Hotel->item(0)->getElementsByTagName("Addons");
                if ($Addons->length > 0) {
                    $Offers = $Addons->item(0)->getElementsByTagName("Offers");
                    if ($Offers->length > 0) {
                        $Special_Offer = $Offers->item(0)->getAttribute("Special_Offer");
                        $Free_Stay = $Offers->item(0)->getAttribute("Free_Stay");
                        $Early_Booking_Discount = $Offers->item(0)->getAttribute("Early_Booking_Discount");
                        $Transfer = $Offers->item(0)->getAttribute("Transfer");
                        $Gala_meal = $Offers->item(0)->getAttribute("Gala_meal");
                    }
                }

                $Notes = $Hotel->item(0)->getElementsByTagName("Notes");
                if ($Notes->length > 0) {
                    $Customer_Note = $Notes->item(0)->getElementsByTagName("Customer_Note");
                    if ($Customer_Note->length > 0) {
                        $Customer_Note = $Customer_Note->item(0)->nodeValue;
                    } else {
                        $Customer_Note = "";
                    }
                }
            }
        }
    }
}

$Country = $HtSearchRq->item(0)->getElementsByTagName("Country");
if ($Country->length > 0) {
    for ($i=0; $i < $Country->length; $i++) { 
        $ID = $Country->item($i)->getAttribute("ID");
        $Name = $Country->item($i)->getAttribute("Name");
        $Code = $Country->item($i)->getAttribute("Code");

        $Destination = $Country->item($i)->getElementsByTagName("Destination");
        if ($Destination->length > 0) {
            for ($j=0; $j < $Destination->length; $j++) { 
                $ID = $Destination->item($j)->getAttribute("ID");
                $name = $Destination->item($j)->getAttribute("name");

                $ISO_Codes = $Destination->item($j)->getElementsByTagName("ISO_Codes");
                if ($ISO_Codes->length > 0) {
                    $Code_1 = $ISO_Codes->item(0)->getAttribute("Code_1");
                    $Code_2 = $ISO_Codes->item(0)->getAttribute("Code_2");
                    $Code_3 = $ISO_Codes->item(0)->getAttribute("Code_3");
                } else {
                    $Code_1 = "";
                    $Code_2 = "";
                    $Code_3 = "";
                }

                $Resort = $Destination->item($j)->getElementsByTagName("Resort");
                if ($Resort->length > 0) {
                    for ($k=0; $k < $Resort->length; $k++) { 
                        $ID = $Resort->item($k)->getAttribute("ID");

                        $Resort_Name = $Resort->item($k)->getElementsByTagName("Resort_Name");
                        if ($Resort_Name->length > 0) {
                            $Resort_Name = $Resort_Name->item(0)->nodeValue;
                        } else {
                            $Resort_Name = "";
                        }

                        $Hotel = $Resort->item($k)->getElementsByTagName("Hotel");
                        if ($Hotel->length > 0) {
                            $Hotel_ID = $Hotel->item(0)->getElementsByTagName("Hotel_ID");
                            if ($Hotel_ID->length > 0) {
                                $Hotel_ID = $Hotel_ID->item(0)->nodeValue;
                            } else {
                                $Hotel_ID = "";
                            }
                            $Hotel_Name = $Hotel->item(0)->getElementsByTagName("Hotel_Name");
                            if ($Hotel_Name->length > 0) {
                                $Hotel_Name = $Hotel_Name->item(0)->nodeValue;
                            } else {
                                $Hotel_Name = "";
                            }

                            $Mapping = $Hotel->item(0)->getElementsByTagName("Mapping");
                            if ($Mapping->length > 0) {
                                $Latitude = $Mapping->item(0)->getElementsByTagName("Latitude");
                                if ($Latitude->length > 0) {
                                    $Latitude = $Latitude->item(0)->nodeValue;
                                } else {
                                    $Latitude = "";
                                }
                                $Longitude = $Mapping->item(0)->getElementsByTagName("Longitude");
                                if ($Longitude->length > 0) {
                                    $Longitude = $Longitude->item(0)->nodeValue;
                                } else {
                                    $Longitude = "";
                                }
                            }
                        }
                    }
                } else {
                    $Code_1 = "";
                }
            }
        }
    }
}

/* try {
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('hoteis');
    $select->where(array(
        'id' => $id
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
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'recomended' => $recomended,
                'stars' => $stars
            );
            $where['id = ?'] = $id;
            $update = $sql->update('hoteis', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hoteis');
            $insert->values(array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'recomended' => $recomended,
                'stars' => $stars
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
        $insert->into('hoteis');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'name' => $name,
            'city' => $city,
            'country' => $country,
            'recomended' => $recomended,
            'stars' => $stars
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
} catch (Exception $ex) {
    echo $return;
    echo "ERRO: " . $ex;
    echo $return;
} */

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>