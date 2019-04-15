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
echo "COMECOU TOURS<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.graylineecuador.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));

$url = "http://demo.gl-tours.com/packages_to_xml.php?init=10&final=20&Destination=4&User=TEST&Pass=1234";

$client->setUri($url);
$client->setMethod('POST');
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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.graylineecuador.php');
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
$response2 = $inputDoc->getElementsByTagName("response");
$complete = $response2->item(0)->getElementsByTagName("complete");

$node = $complete->item(0)->getElementsByTagName("package");
for ($i=0; $i < $node->length; $i++) { 
    $id = $node->item($i)->getAttribute("id");
    $typepackage = $node->item($i)->getAttribute("type");
    $order = $node->item($i)->getAttribute("order");
    $code = $node->item($i)->getAttribute("code");
    $modalities = $node->item($i)->getElementsByTagName("modalities");
    if ($modalities->length > 0) {
        $modalities = $modalities->item(0)->nodeValue;
    } else {
        $modalities = "";
    }
    $toptour = $node->item($i)->getElementsByTagName("toptour");
    if ($toptour->length > 0) {
        $toptour = $toptour->item(0)->nodeValue;
    } else {
        $toptour = "";
    }
    $PickUp = $node->item($i)->getElementsByTagName("PickUp");
    if ($PickUp->length > 0) {
        $PickUp = $PickUp->item(0)->nodeValue;
    } else {
        $PickUp = "";
    }
    $zone = $node->item($i)->getElementsByTagName("zone");
    if ($zone->length > 0) {
        $zone = $zone->item(0)->nodeValue;
    } else {
        $zone = "";
    }
    $zone_description = $node->item($i)->getElementsByTagName("zone_description");
    if ($zone_description->length > 0) {
        $zone_description = $zone_description->item(0)->nodeValue;
    } else {
        $zone_description = "";
    }
    $airport = $node->item($i)->getElementsByTagName("airport");
    if ($airport->length > 0) {
        $airport = $airport->item(0)->nodeValue;
    } else {
        $airport = "";
    }
    $title = $node->item($i)->getElementsByTagName("title");
    if ($title->length > 0) {
        $title = $title->item(0)->nodeValue;
    } else {
        $title = "";
    }
    $sub_title = $node->item($i)->getElementsByTagName("sub_title");
    if ($sub_title->length > 0) {
        $sub_title = $sub_title->item(0)->nodeValue;
    } else {
        $sub_title = "";
    }
    $country = $node->item($i)->getElementsByTagName("country");
    if ($country->length > 0) {
        $country = $country->item(0)->nodeValue;
    } else {
        $country = "";
    }
    $destination2 = "";
    $destination = $node->item($i)->getElementsByTagName("destination");
    if ($destination->length > 0) {
        $destinationcode = $destination->item(0)->getAttribute("code");
        $destination2 = $destination->item(0)->nodeValue;
    } else {
        $destination2 = "";
    }
    $type = $node->item($i)->getElementsByTagName("type");
    if ($type->length > 0) {
        $type = $type->item(0)->nodeValue;
    } else {
        $type = "";
    }
    $VoucherType = $node->item($i)->getElementsByTagName("VoucherType");
    if ($VoucherType->length > 0) {
        $VoucherType = $VoucherType->item(0)->nodeValue;
    } else {
        $VoucherType = "";
    }
    $TransferType = $node->item($i)->getElementsByTagName("TransferType");
    if ($TransferType->length > 0) {
        $TransferType = $TransferType->item(0)->nodeValue;
    } else {
        $TransferType = "";
    }
    $VehiculeType = $node->item($i)->getElementsByTagName("VehiculeType");
    if ($VehiculeType->length > 0) {
        $VehiculeType = $VehiculeType->item(0)->nodeValue;
    } else {
        $VehiculeType = "";
    }
    $ServiceType = $node->item($i)->getElementsByTagName("ServiceType");
    if ($ServiceType->length > 0) {
        $ServiceType = $ServiceType->item(0)->nodeValue;
    } else {
        $ServiceType = "";
    }
    $luggage = $node->item($i)->getElementsByTagName("luggage");
    if ($luggage->length > 0) {
        $luggage = $luggage->item(0)->nodeValue;
    } else {
        $luggage = "";
    }
    $hand_luggage = $node->item($i)->getElementsByTagName("hand_luggage");
    if ($hand_luggage->length > 0) {
        $hand_luggage = $hand_luggage->item(0)->nodeValue;
    } else {
        $hand_luggage = "";
    }
    $meet_greet = $node->item($i)->getElementsByTagName("meet_greet");
    if ($meet_greet->length > 0) {
        $meet_greet = $meet_greet->item(0)->nodeValue;
    } else {
        $meet_greet = "";
    }
    $MaxWaitHours = $node->item($i)->getElementsByTagName("MaxWaitHours");
    if ($MaxWaitHours->length > 0) {
        $MaxWaitHours = $MaxWaitHours->item(0)->nodeValue;
    } else {
        $MaxWaitHours = "";
    }
    $tour_redemptions = $node->item($i)->getElementsByTagName("tour_redemptions");
    if ($tour_redemptions->length > 0) {
        $tour_redemptions = $tour_redemptions->item(0)->nodeValue;
    } else {
        $tour_redemptions = "";
    }
    $tour_redemptions_out = $node->item($i)->getElementsByTagName("tour_redemptions_out");
    if ($tour_redemptions_out->length > 0) {
        $tour_redemptions_out = $tour_redemptions_out->item(0)->nodeValue;
    } else {
        $tour_redemptions_out = "";
    }
    $MinPax = $node->item($i)->getElementsByTagName("MinPax");
    if ($MinPax->length > 0) {
        $MinPax = $MinPax->item(0)->nodeValue;
    } else {
        $MinPax = "";
    }
    $MaxPax = $node->item($i)->getElementsByTagName("MaxPax");
    if ($MaxPax->length > 0) {
        $MaxPax = $MaxPax->item(0)->nodeValue;
    } else {
        $MaxPax = "";
    }
    $GEO2 = "";
    $GEO = $node->item($i)->getElementsByTagName("GEO");
    if ($GEO->length > 0) {
        $GEOtype = $GEO->item(0)->getAttribute("type");
        $GEO2 = $GEO->item(0)->nodeValue;
    } else {
        $GEO = "";
    }
    $thumb = $node->item($i)->getElementsByTagName("thumb");
    if ($thumb->length > 0) {
        $thumb = $thumb->item(0)->nodeValue;
    } else {
        $thumb = "";
    }
    $duration = $node->item($i)->getElementsByTagName("duration");
    if ($duration->length > 0) {
        $duration = $duration->item(0)->nodeValue;
    } else {
        $duration = "";
    }
    $duration_h = $node->item($i)->getElementsByTagName("duration_h");
    if ($duration_h->length > 0) {
        $duration_h = $duration_h->item(0)->nodeValue;
    } else {
        $duration_h = "";
    }
    $duration_m = $node->item($i)->getElementsByTagName("duration_m");
    if ($duration_m->length > 0) {
        $duration_m = $duration_m->item(0)->nodeValue;
    } else {
        $duration_m = "";
    }
    $tour_description = $node->item($i)->getElementsByTagName("tour_description");
    if ($tour_description->length > 0) {
        $tour_description = $tour_description->item(0)->nodeValue;
    } else {
        $tour_description = "";
    }

    //age
    $age = $node->item($i)->getElementsByTagName("age");
    if ($age->length > 0) {
        $adult2 = "";
        $adult = $age->item(0)->getElementsByTagName("adult");
        if ($adult->length > 0) {
            $adultavailable = $adult->item(0)->getAttribute("available");
            $adult2 = $adult->item(0)->nodeValue;
        } else {
            $adult2 = "";
        }
        $child2 = "";
        $child = $age->item(0)->getElementsByTagName("child");
        if ($child->length > 0) {
            $childavailable = $child->item(0)->getAttribute("available");
            $child2 = $child->item(0)->nodeValue;
        } else {
            $child2 = "";
        }
        $infant2 = "";
        $infant = $age->item(0)->getElementsByTagName("infant");
        if ($infant->length > 0) {
            $infantavailable = $infant->item(0)->getAttribute("available");
            $infant2 = $adult->item(0)->nodeValue;
        } else {
            $infant2 = "";
        }
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('tours');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'typepackage' => $typepackage,
            'order' => $order,
            'code' => $code,
            'modalities' => $modalities,
            'toptour' => $toptour,
            'PickUp' => $PickUp,
            'zone' => $zone,
            'zone_description' => $zone_description,
            'airport' => $airport,
            'title' => $title,
            'sub_title' => $sub_title,
            'country' => $country,
            'destinationcode' => $destinationcode,
            'destination' => $destination2,
            'type' => $type,
            'VoucherType' => $VoucherType,
            'TransferType' => $TransferType,
            'VehiculeType' => $VehiculeType,
            'ServiceType' => $ServiceType,
            'luggage' => $luggage,
            'hand_luggage' => $hand_luggage,
            'meet_greet' => $meet_greet,
            'MaxWaitHours' => $MaxWaitHours,
            'tour_redemptions' => $tour_redemptions,
            'tour_redemptions_out' => $tour_redemptions_out,
            'MinPax' => $MinPax,
            'MaxPax' => $MaxPax,
            'GEOtype' => $GEOtype,
            'GEO' => $GEO2,
            'thumb' => $thumb,
            'duration' => $duration,
            'duration_h' => $duration_h,
            'duration_m' => $duration_m,
            'tour_description' => $tour_description,
            'adultavailable' => $adultavailable,
            'adultage' => $adult2,
            'childavailable' => $childavailable,
            'childage' => $child2,
            'infantavailable' => $infantavailable,
            'infantage' => $infant2
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO: " . $e;
        echo $return;
    }

    //cancelaciones
    $cancelaciones = $node->item($i)->getElementsByTagName("cancelaciones");
    if ($cancelaciones->length > 0) {
        $cancelacion = $cancelaciones->item(0)->getElementsByTagName("cancelacion");
        if ($cancelacion->length > 0) {
            $reembolsable = $cancelacion->item(0)->getElementsByTagName("reembolsable");
            if ($reembolsable->length > 0) {
                $reembolsable = $reembolsable->item(0)->nodeValue;
            } else {
                $reembolsable = "";
            }
            $from = $cancelacion->item(0)->getElementsByTagName("from");
            if ($from->length > 0) {
                $from = $from->item(0)->nodeValue;
            } else {
                $from = "";
            }
            $to = $cancelacion->item(0)->getElementsByTagName("to");
            if ($to->length > 0) {
                $to = $to->item(0)->nodeValue;
            } else {
                $to = "";
            }
            $unit = $cancelacion->item(0)->getElementsByTagName("unit");
            if ($unit->length > 0) {
                $unit = $unit->item(0)->nodeValue;
            } else {
                $unit = "";
            }
            $percent = $cancelacion->item(0)->getElementsByTagName("percent");
            if ($percent->length > 0) {
                $percent = $percent->item(0)->nodeValue;
            } else {
                $percent = "";
            }
            $gmt = $cancelacion->item(0)->getElementsByTagName("gmt");
            if ($gmt->length > 0) {
                $gmt = $gmt->item(0)->nodeValue;
            } else {
                $gmt = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('tourscancellation');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'reembolsable' => $reembolsable,
                    'from' => $from,
                    'to' => $to,
                    'unit' => $unit,
                    'percent' => $percent,
                    'gmt' => $gmt
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO CA: " . $e;
                echo $return;
            }
        }
    }

    //season
    $season = $node->item($i)->getElementsByTagName("season");
    if ($season->length > 0) {
        for ($j=0; $j < $season->length; $j++) { 
            $to = $season->item($j)->getAttribute("to");
            $from = $season->item($j)->getAttribute("from");
            $advancePurchase = $season->item($j)->getAttribute("advancePurchase");
            $modalities = $season->item($j)->getElementsByTagName("modalities");
            if ($modalities->length > 0) {
                $modalities = $modalities->item(0)->nodeValue;
            } else {
                $modalities = "";
            }
            $operation_days = $season->item($j)->getElementsByTagName("operation_days");
            if ($operation_days->length > 0) {
                $operation_days = $operation_days->item(0)->nodeValue;
            } else {
                $operation_days = "";
            }
            $price_type = $season->item($j)->getElementsByTagName("price_type");
            if ($price_type->length > 0) {
                $price_type = $price_type->item(0)->nodeValue;
            } else {
                $price_type = "";
            }
            $price_adult = $season->item($j)->getElementsByTagName("price_adult");
            if ($price_adult->length > 0) {
                $price_adult = $price_adult->item(0)->nodeValue;
            } else {
                $price_adult = "";
            }
            $price_child = $season->item($j)->getElementsByTagName("price_child");
            if ($price_child->length > 0) {
                $price_child = $price_child->item(0)->nodeValue;
            } else {
                $price_child = "";
            }
            $price_infant = $season->item($j)->getElementsByTagName("price_infant");
            if ($price_infant->length > 0) {
                $price_infant = $price_infant->item(0)->nodeValue;
            } else {
                $price_infant = "";
            }
            $cost_adult = $season->item($j)->getElementsByTagName("cost_adult");
            if ($cost_adult->length > 0) {
                $cost_adult = $cost_adult->item(0)->nodeValue;
            } else {
                $cost_adult = "";
            }
            $cost_child = $season->item($j)->getElementsByTagName("cost_child");
            if ($cost_child->length > 0) {
                $cost_child = $cost_child->item(0)->nodeValue;
            } else {
                $cost_child = "";
            }
            $cost_infant = $season->item($j)->getElementsByTagName("cost_infant");
            if ($cost_infant->length > 0) {
                $cost_infant = $cost_infant->item(0)->nodeValue;
            } else {
                $cost_infant = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('toursseason');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'to' => $to,
                    'from' => $from,
                    'advancePurchase' => $advancePurchase,
                    'modalities' => $modalities,
                    'operation_days' => $operation_days,
                    'price_type' => $price_type,
                    'price_adult' => $price_adult,
                    'price_child' => $price_child,
                    'price_infant' => $price_infant,
                    'cost_adult' => $cost_adult,
                    'cost_child' => $cost_child,
                    'cost_infant' => $cost_infant
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO SEA: " . $e;
                echo $return;
            }

            //modality
            $modality = $season->item($j)->getElementsByTagName("modality");
            if ($modality->length > 0) {
                $modalityid = $modality->item(0)->getAttribute("id");
                $modality_title = $modality->item(0)->getElementsByTagName("modality_title");
                if ($modality_title->length > 0) {
                    $modality_title = $modality_title->item(0)->nodeValue;
                } else {
                    $modality_title = "";
                }
                $modality_title_eng = $modality->item(0)->getElementsByTagName("modality_title_eng");
                if ($modality_title_eng->length > 0) {
                    $modality_title_eng = $modality_title_eng->item(0)->nodeValue;
                } else {
                    $modality_title_eng = "";
                }
                $modality_description = $modality->item(0)->getElementsByTagName("modality_description");
                if ($modality_description->length > 0) {
                    $modality_description = $modality_description->item(0)->nodeValue;
                } else {
                    $modality_description = "";
                }
                $duration = $modality->item(0)->getElementsByTagName("duration");
                if ($duration->length > 0) {
                    $duration = $duration->item(0)->nodeValue;
                } else {
                    $duration = "";
                }
                $duration_h = $modality->item(0)->getElementsByTagName("duration_h");
                if ($duration_h->length > 0) {
                    $duration_h = $duration_h->item(0)->nodeValue;
                } else {
                    $duration_h = "";
                }
                $duration_m = $modality->item(0)->getElementsByTagName("duration_m");
                if ($duration_m->length > 0) {
                    $duration_m = $duration_m->item(0)->nodeValue;
                } else {
                    $duration_m = "";
                }
                $price_adult = $modality->item(0)->getElementsByTagName("price_adult");
                if ($price_adult->length > 0) {
                    $price_adult = $price_adult->item(0)->nodeValue;
                } else {
                    $price_adult = "";
                }
                $price_child = $modality->item(0)->getElementsByTagName("price_child");
                if ($price_child->length > 0) {
                    $price_child = $price_child->item(0)->nodeValue;
                } else {
                    $price_child = "";
                }
                $price_infant = $modality->item(0)->getElementsByTagName("price_infant");
                if ($price_infant->length > 0) {
                    $price_infant = $price_infant->item(0)->nodeValue;
                } else {
                    $price_infant = "";
                }
                $cost_adult = $modality->item(0)->getElementsByTagName("cost_adult");
                if ($cost_adult->length > 0) {
                    $cost_adult = $cost_adult->item(0)->nodeValue;
                } else {
                    $cost_adult = "";
                }
                $cost_child = $modality->item(0)->getElementsByTagName("cost_child");
                if ($cost_child->length > 0) {
                    $cost_child = $cost_child->item(0)->nodeValue;
                } else {
                    $cost_child = "";
                }
                $cost_infant = $modality->item(0)->getElementsByTagName("cost_infant");
                if ($cost_infant->length > 0) {
                    $cost_infant = $cost_infant->item(0)->nodeValue;
                } else {
                    $cost_infant = "";
                }
                $modality_operation_days = $modality->item(0)->getElementsByTagName("modality_operation_days");
                if ($modality_operation_days->length > 0) {
                    $modality_operation_days = $modality_operation_days->item(0)->nodeValue;
                } else {
                    $modality_operation_days = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('toursmodality');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'modalityid' => $modalityid,
                        'modality_title' => $modality_title,
                        'modality_title_eng' => $modality_title_eng,
                        'modality_description' => $modality_description,
                        'duration' => $duration,
                        'duration_h' => $duration_h,
                        'duration_m' => $duration_m,
                        'price_adult' => $price_adult,
                        'price_child' => $price_child,
                        'price_infant' => $price_infant,
                        'cost_adult' => $cost_adult,
                        'cost_child' => $cost_child,
                        'cost_infant' => $cost_infant,
                        'modality_operation_days' => $modality_operation_days
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO MOB: " . $e;
                }
               
            }

            //days
            $days = $season->item($j)->getElementsByTagName("days");
            if ($days->length > 0) {
                $day2 = "";
                $day = $days->item(0)->getElementsByTagName("day");
                if ($day->length > 0) {
                    for ($jAux=0; $jAux < $day->length; $jAux++) { 
                        $day2 = $day->item($jAux)->nodeValue;

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('toursdays');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'day' => $day2
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO: " . $e;
                            echo $return;
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