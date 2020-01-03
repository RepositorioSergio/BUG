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


$url = 'http://testxml.youtravel.com/webservicestest/index.asp?Dstn=FAO&LangID=EN&Username=xmltestme&Password=testme&Nights=2&Checkin_Date=12/04/2020&Rooms=1&ADLTS_1=1&ADLTS_2=2&BT=1&SBT=1';

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
$session = $HtSearchRq->item(0)->getElementsByTagName("session");
if ($session->length > 0) {
    $id = $session->item(0)->getAttribute("id");
    $Currency = $session->item(0)->getElementsByTagName("Currency");
    if ($Currency->length > 0) {
        $Currency = $Currency->item(0)->nodeValue;
    } else {
        $Currency = "";
    }

    $Hotel = $session->item(0)->getElementsByTagName("Hotel");
    if ($Hotel->length > 0) {
        for ($i=0; $i < $Hotel->length; $i++) { 
            $ID = $Hotel->item($i)->getAttribute("ID");
            $Hotel_Name = $Hotel->item($i)->getElementsByTagName("Hotel_Name");
            if ($Hotel_Name->length > 0) {
                $Hotel_Name = $Hotel_Name->item(0)->nodeValue;
            } else {
                $Hotel_Name = "";
            }
            $Youtravel_Rating = $Hotel->item($i)->getElementsByTagName("Youtravel_Rating");
            if ($Youtravel_Rating->length > 0) {
                $Youtravel_Rating = $Youtravel_Rating->item(0)->nodeValue;
            } else {
                $Youtravel_Rating = "";
            }
            $Official_Rating = $Hotel->item($i)->getElementsByTagName("Official_Rating");
            if ($Official_Rating->length > 0) {
                $Official_Rating = $Official_Rating->item(0)->nodeValue;
            } else {
                $Official_Rating = "";
            }
            $Board_Type = $Hotel->item($i)->getElementsByTagName("Board_Type");
            if ($Board_Type->length > 0) {
                $Board_Type = $Board_Type->item(0)->nodeValue;
            } else {
                $Board_Type = "";
            }
            $Child_Age = $Hotel->item($i)->getElementsByTagName("Child_Age");
            if ($Child_Age->length > 0) {
                $Child_Age = $Child_Age->item(0)->nodeValue;
            } else {
                $Child_Age = "";
            }
            $Country = $Hotel->item($i)->getElementsByTagName("Country");
            if ($Country->length > 0) {
                $Country = $Country->item(0)->nodeValue;
            } else {
                $Country = "";
            }
            $Destination = $Hotel->item($i)->getElementsByTagName("Destination");
            if ($Hotel_Name->length > 0) {
                $Destination = $Destination->item(0)->nodeValue;
            } else {
                $Destination = "";
            }
            $Resort = $Hotel->item($i)->getElementsByTagName("Resort");
            if ($Resort->length > 0) {
                $Resort = $Resort->item(0)->nodeValue;
            } else {
                $Resort = "";
            }
            $Image = $Hotel->item($i)->getElementsByTagName("Image");
            if ($Image->length > 0) {
                $Image = $Image->item(0)->nodeValue;
            } else {
                $Image = "";
            }
            $Hotel_Desc = $Hotel->item($i)->getElementsByTagName("Hotel_Desc");
            if ($Hotel_Desc->length > 0) {
                $Hotel_Desc = $Hotel_Desc->item(0)->nodeValue;
            } else {
                $Hotel_Desc = "";
            }

            $Room_1 = $Hotel->item($i)->getElementsByTagName("Room_1");
            if ($Room_1->length > 0) {
                $Passengers = $Room_1->item(0)->getElementsByTagName("Passengers");
                if ($Passengers->length > 0) {
                    $Adults = $Passengers->item(0)->getAttribute("Adults");
                    $Children = $Passengers->item(0)->getAttribute("Children");
                    $Infants = $Passengers->item(0)->getAttribute("Infants");
                }

                $Room = $Room_1->item(0)->getElementsByTagName("Room");
                if ($Room->length > 0) {
                    for ($j=0; $j < $Room->length; $j++) { 
                        $Id = $Room->item($j)->getAttribute("Id");
                        $ADV = $Room->item($j)->getAttribute("ADV");
                        $Refundable = $Room->item($j)->getAttribute("Refundable");
                        $Type = $Room->item($j)->getElementsByTagName("Type");
                        if ($Type->length > 0) {
                            $Type = $Type->item(0)->nodeValue;
                        } else {
                            $Type = "";
                        }
                        $Board = $Room->item($j)->getElementsByTagName("Board");
                        if ($Board->length > 0) {
                            $Board = $Board->item(0)->nodeValue;
                        } else {
                            $Board = "";
                        }
                        $Rates = $Room->item($j)->getElementsByTagName("Rates");
                        if ($Rates->length > 0) {
                            $Final_Rate = $Rates->item(0)->getAttribute("Final_Rate");
                            $Original_Rate = $Rates->item(0)->getAttribute("Original_Rate");
                        } else {
                            $Final_Rate = "";
                            $Original_Rate = "";
                        }
                        $Offers = $Room->item($j)->getElementsByTagName("Offers");
                        if ($Offers->length > 0) {
                            $Gala_Meals = $Offers->item(0)->getAttribute("Gala_Meals");
                            $Free_Transfer = $Offers->item(0)->getAttribute("Free_Transfer");
                            $Free_Stay = $Offers->item(0)->getAttribute("Free_Stay");
                            $Early_Booking_Discount = $Offers->item(0)->getAttribute("Early_Booking_Discount");
                            $Lastminute_Offer = $Offers->item(0)->getAttribute("Lastminute_Offer");
                        } else {
                            $Gala_Meals = "";
                            $Free_Transfer = "";
                            $Free_Stay = "";
                            $Early_Booking_Discount = "";
                            $Lastminute_Offer = "";
                        }
                    }
                }
            }

            $Room_2 = $Hotel->item($i)->getElementsByTagName("Room_2");
            if ($Room_2->length > 0) {
                $Passengers = $Room_2->item(0)->getElementsByTagName("Passengers");
                if ($Passengers->length > 0) {
                    $Adults = $Passengers->item(0)->getAttribute("Adults");
                    $Children = $Passengers->item(0)->getAttribute("Children");
                    $Infants = $Passengers->item(0)->getAttribute("Infants");
                }

                $Room = $Room_2->item(0)->getElementsByTagName("Room");
                if ($Room->length > 0) {
                    for ($j=0; $j < $Room->length; $j++) { 
                        $Id = $Room->item($j)->getAttribute("Id");
                        $ADV = $Room->item($j)->getAttribute("ADV");
                        $Refundable = $Room->item($j)->getAttribute("Refundable");
                        $Type = $Room->item($j)->getElementsByTagName("Type");
                        if ($Type->length > 0) {
                            $Type = $Type->item(0)->nodeValue;
                        } else {
                            $Type = "";
                        }
                        $Board = $Room->item($j)->getElementsByTagName("Board");
                        if ($Board->length > 0) {
                            $Board = $Board->item(0)->nodeValue;
                        } else {
                            $Board = "";
                        }
                        $Rates = $Room->item($j)->getElementsByTagName("Rates");
                        if ($Rates->length > 0) {
                            $Final_Rate = $Rates->item(0)->getAttribute("Final_Rate");
                            $Original_Rate = $Rates->item(0)->getAttribute("Original_Rate");
                        } else {
                            $Final_Rate = "";
                            $Original_Rate = "";
                        }
                        $Offers = $Room->item($j)->getElementsByTagName("Offers");
                        if ($Offers->length > 0) {
                            $Gala_Meals = $Offers->item(0)->getAttribute("Gala_Meals");
                            $Free_Transfer = $Offers->item(0)->getAttribute("Free_Transfer");
                            $Free_Stay = $Offers->item(0)->getAttribute("Free_Stay");
                            $Early_Booking_Discount = $Offers->item(0)->getAttribute("Early_Booking_Discount");
                            $Lastminute_Offer = $Offers->item(0)->getAttribute("Lastminute_Offer");
                        } else {
                            $Gala_Meals = "";
                            $Free_Transfer = "";
                            $Free_Stay = "";
                            $Early_Booking_Discount = "";
                            $Lastminute_Offer = "";
                        }
                    }
                }
            }

            $Room_3 = $Hotel->item($i)->getElementsByTagName("Room_3");
            if ($Room_3->length > 0) {
                $Passengers = $Room_2->item(0)->getElementsByTagName("Passengers");
                if ($Passengers->length > 0) {
                    $Adults = $Passengers->item(0)->getAttribute("Adults");
                    $Children = $Passengers->item(0)->getAttribute("Children");
                    $Infants = $Passengers->item(0)->getAttribute("Infants");
                }

                $Room = $Room_3->item(0)->getElementsByTagName("Room");
                if ($Room->length > 0) {
                    for ($j=0; $j < $Room->length; $j++) { 
                        $Id = $Room->item($j)->getAttribute("Id");
                        $ADV = $Room->item($j)->getAttribute("ADV");
                        $Refundable = $Room->item($j)->getAttribute("Refundable");
                        $Type = $Room->item($j)->getElementsByTagName("Type");
                        if ($Type->length > 0) {
                            $Type = $Type->item(0)->nodeValue;
                        } else {
                            $Type = "";
                        }
                        $Board = $Room->item($j)->getElementsByTagName("Board");
                        if ($Board->length > 0) {
                            $Board = $Board->item(0)->nodeValue;
                        } else {
                            $Board = "";
                        }
                        $Rates = $Room->item($j)->getElementsByTagName("Rates");
                        if ($Rates->length > 0) {
                            $Final_Rate = $Rates->item(0)->getAttribute("Final_Rate");
                            $Original_Rate = $Rates->item(0)->getAttribute("Original_Rate");
                        } else {
                            $Final_Rate = "";
                            $Original_Rate = "";
                        }
                        $Offers = $Room->item($j)->getElementsByTagName("Offers");
                        if ($Offers->length > 0) {
                            $Gala_Meals = $Offers->item(0)->getAttribute("Gala_Meals");
                            $Free_Transfer = $Offers->item(0)->getAttribute("Free_Transfer");
                            $Free_Stay = $Offers->item(0)->getAttribute("Free_Stay");
                            $Early_Booking_Discount = $Offers->item(0)->getAttribute("Early_Booking_Discount");
                            $Lastminute_Offer = $Offers->item(0)->getAttribute("Lastminute_Offer");
                        } else {
                            $Gala_Meals = "";
                            $Free_Transfer = "";
                            $Free_Stay = "";
                            $Early_Booking_Discount = "";
                            $Lastminute_Offer = "";
                        }
                    }
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