<?php
$scurrency = strtoupper($currency);
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$totalstay = false;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select city_xml44, latitude, longitude from cities where id=" . $destination;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml44 = $row_settings["city_xml44"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml44 = 0;
}
if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sql = "select value from settings where name='totalstayDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_totalstay";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='totalstayuser' and affiliate_id=$affiliate_id_totalstay";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $totalstayuser = $row_settings['value'];
}
$sql = "select value from settings where name='totalstaypassword' and affiliate_id=$affiliate_id_totalstay";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $totalstaypassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='totalstayMarkup' and affiliate_id=$affiliate_id_totalstay";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $totalstayMarkup = (double) $row_settings['value'];
} else {
    $totalstayMarkup = 0;
}
$sql = "select value from settings where name='totalstayserviceURL' and affiliate_id=$affiliate_id_totalstay";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $totalstayserviceURL = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$ArrivalDate = strftime("%Y-%m-%d", $from);

$date = new Datetime();
$timestamp = $date->format('U');
$raw = 'Data=<SearchRequest><LoginDetails>
  <Login>' . $totalstayuser . '</Login>
  <Password>' . $totalstaypassword . '</Password>
</LoginDetails>
<SearchDetails>
  <ArrivalDate>' . $ArrivalDate . '</ArrivalDate>
  <Duration>' . $noOfNights . '</Duration>
  <RegionID>72</RegionID>
  <MealBasisID>0</MealBasisID>
  <MinStarRating>0</MinStarRating>
  <ContractSpecialOfferID>0</ContractSpecialOfferID>
  <RoomRequests>';
  for ($a=0; $a < count($selectedAdults); $a++) { 
      $raw .= '<RoomRequest>
      <Adults>' . $selectedAdults[$a] . '</Adults>
      <Children>' . $selectedChildren[$a] . '</Children>
      <Infants>0</Infants>';
        if ($selectedChildren[$a] == 0) {
            $raw .= '<ChildAges/>';
        } else {
            $raw .= '<ChildAges>';
            for ($z = 0; $z < $selectedChildren[$a]; $z++) {
                $raw .= '<ChildAge><Age>' . $selectedChildrenAges[$a][$z] . '</Age></ChildAge>';
            }
            $raw .= '</ChildAges>';
        }
        $raw .= '</RoomRequest>';
  }
  $raw .=  '</RoomRequests></SearchDetails></SearchRequest>';
if ($totalstayserviceURL != "" and $totalstayuser != "" and $totalstaypassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $totalstayserviceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $endTime = microtime();
    try {
        $db2 = new \Zend\Db\Adapter\Adapter($config);
        $sql = new Sql($db2);
        $insert = $sql->insert();
        $insert->into('log_totalstay');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $totalstayserviceURL . $raw,
            'sqlcontext' => $response,
            'errcontext' => ''
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db2->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $node = $inputDoc->getElementsByTagName("PropertyResult");
    for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
        $TotalProperties = $node->item($rAUX)->getElementsByTagName("TotalProperties");
        if ($TotalProperties->length > 0) {
            $TotalProperties = $TotalProperties->item(0)->nodeValue;
        } else {
            $TotalProperties = "";
        }
        $PropertyID = $node->item($rAUX)->getElementsByTagName('PropertyID');
        if ($PropertyID->length > 0) {
            $PropertyID = $PropertyID->item(0)->nodeValue;
        } else {
            $PropertyID = "";
        }
        $shid = $PropertyID;
        $sfilter[] = " sid='$shid' ";
        $PropertyReferenceID = $node->item($rAUX)->getElementsByTagName('PropertyReferenceID');
        if ($PropertyReferenceID->length > 0) {
            $PropertyReferenceID = $PropertyReferenceID->item(0)->nodeValue;
        } else {
            $PropertyReferenceID = "";
        }
        $PropertyName = $node->item($rAUX)->getElementsByTagName('PropertyName');
        if ($PropertyName->length > 0) {
            $PropertyName = $PropertyName->item(0)->nodeValue;
        } else {
            $PropertyName = "";
        }
        $Rating = $node->item($rAUX)->getElementsByTagName('Rating');
        if ($Rating->length > 0) {
            $Rating = $Rating->item(0)->nodeValue;
        } else {
            $Rating = "";
        }
        $OurRating = $node->item($rAUX)->getElementsByTagName('OurRating');
        if ($OurRating->length > 0) {
            $OurRating = $OurRating->item(0)->nodeValue;
        } else {
            $OurRating = "";
        }
        $Country = $node->item($rAUX)->getElementsByTagName('Country');
        if ($Country->length > 0) {
            $Country = $Country->item(0)->nodeValue;
        } else {
            $Country = "";
        }
        $Region = $node->item($rAUX)->getElementsByTagName('Region');
        if ($Region->length > 0) {
            $Region = $Region->item(0)->nodeValue;
        } else {
            $Region = "";
        }
        $Resort = $node->item($rAUX)->getElementsByTagName('Resort');
        if ($Resort->length > 0) {
            $Resort = $Resort->item(0)->nodeValue;
        } else {
            $Resort = "";
        }
        $SearchURL = $node->item($rAUX)->getElementsByTagName('SearchURL');
        if ($SearchURL->length > 0) {
            $SearchURL = $SearchURL->item(0)->nodeValue;
        } else {
            $SearchURL = "";
        }
        // ROOMS
        $RoomTypes = $node->item($rAUX)->getElementsByTagName('RoomTypes');
        if ($RoomTypes->length > 0) {
            $RoomType = $RoomTypes->item(0)->getElementsByTagName('RoomType');
            for ($Auxjj = 0; $Auxjj < $RoomType->length; $Auxjj ++) {
                $Seq = $RoomType->item($Auxjj)->getElementsByTagName('Seq');
                if ($Seq->length > 0) {
                    $Seq = $Seq->item(0)->nodeValue;
                } else {
                    $Seq = 0;
                }
                if ($Seq > 0) {
                    $PropertyRoomTypeID = $RoomType->item($Auxjj)->getElementsByTagName('PropertyRoomTypeID');
                    if ($PropertyRoomTypeID->length > 0) {
                        $PropertyRoomTypeID = $PropertyRoomTypeID->item(0)->nodeValue;
                    } else {
                        $PropertyRoomTypeID = "";
                    }
                    $BookingToken = $RoomType->item($Auxjj)->getElementsByTagName('BookingToken');
                    if ($BookingToken->length > 0) {
                        $BookingToken = $BookingToken->item(0)->nodeValue;
                    } else {
                        $BookingToken = "";
                    }
                    $MealBasisID = $RoomType->item($Auxjj)->getElementsByTagName('MealBasisID');
                    if ($MealBasisID->length > 0) {
                        $MealBasisID = $MealBasisID->item(0)->nodeValue;
                    } else {
                        $MealBasisID = "";
                    }
                    $RoomType2 = $RoomType->item($Auxjj)->getElementsByTagName('RoomType');
                    if ($RoomType2->length > 0) {
                        $RoomType2 = $RoomType2->item(0)->nodeValue;
                    } else {
                        $RoomType2 = "";
                    }
                    $RoomView = $RoomType->item($Auxjj)->getElementsByTagName('RoomView');
                    if ($RoomView->length > 0) {
                        $RoomView = $RoomView->item(0)->nodeValue;
                    } else {
                        $RoomView = "";
                    }
                    $MealBasis = $RoomType->item($Auxjj)->getElementsByTagName('MealBasis');
                    if ($MealBasis->length > 0) {
                        $MealBasis = $MealBasis->item(0)->nodeValue;
                    } else {
                        $MealBasis = "";
                    }
                    $SubTotal = $RoomType->item($Auxjj)->getElementsByTagName('SubTotal');
                    if ($SubTotal->length > 0) {
                        $SubTotal = $SubTotal->item(0)->nodeValue;
                    } else {
                        $SubTotal = "";
                    }
                    $Discount = $RoomType->item($Auxjj)->getElementsByTagName('Discount');
                    if ($Discount->length > 0) {
                        $Discount = $Discount->item(0)->nodeValue;
                    } else {
                        $Discount = "";
                    }
                    $OnRequest = $RoomType->item($Auxjj)->getElementsByTagName('OnRequest');
                    if ($OnRequest->length > 0) {
                        $OnRequest = $OnRequest->item(0)->nodeValue;
                    } else {
                        $OnRequest = "";
                    }
                    $Total = $RoomType->item($Auxjj)->getElementsByTagName('Total');
                    if ($Total->length > 0) {
                        $Total = $Total->item(0)->nodeValue;
                    } else {
                        $Total = "";
                    }
                    $Adults = $RoomType->item($Auxjj)->getElementsByTagName('Adults');
                    if ($Adults->length > 0) {
                        $Adults = $Adults->item(0)->nodeValue;
                    } else {
                        $Adults = "";
                    }
                    $Children = $RoomType->item($Auxjj)->getElementsByTagName('Children');
                    if ($Children->length > 0) {
                        $Children = $Children->item(0)->nodeValue;
                    } else {
                        $Children = "";
                    }
                    $Infants = $RoomType->item($Auxjj)->getElementsByTagName('Infants');
                    if ($Infants->length > 0) {
                        $Infants = $Infants->item(0)->nodeValue;
                    } else {
                        $Infants = "";
                    }
                    $Adjustments = $RoomType->item($Auxjj)->getElementsByTagName('Adjustments');
                    if ($Adjustments->length > 0) {
                        $Adjustments = $Adjustments->item(0)->nodeValue;
                    } else {
                        $Adjustments = "";
                    }
                    $Errata = $RoomType->item($Auxjj)->getElementsByTagName('Errata');
                    if ($Errata->length > 0) {
                        // $Erratum = $Errata->item(0)->getElementsByTagName('Erratum');
                        // if ($Erratum->length > 0) {
                        // for ($i = 0; $i < $Erratum->length; $i ++) {
                        // $Subject = $Erratum->item($i)->getElementsByTagName('Subject');
                        // if ($Subject->length > 0) {
                        // $Subject = $Subject->item(0)->nodeValue;
                        // } else {
                        // $Subject = "";
                        // }
                        // $Description = $Erratum->item($i)->getElementsByTagName('Description');
                        // if ($Description->length > 0) {
                        // $Description = $Description->item(0)->nodeValue;
                        // } else {
                        // $Description = "";
                        // }
                        // }
                        // }
                        $Errata = $Errata->item(0)->nodeValue;
                    } else {
                        $Errata = "";
                    }
                    $OptionalSupplements = $RoomType->item($Auxjj)->getElementsByTagName('OptionalSupplements');
                    if ($SearchURL->length > 0) {
                        $OptionalSupplements = $OptionalSupplements->item(0)->nodeValue;
                    } else {
                        $OptionalSupplements = "";
                    }
                    if (is_array($tmp[$shid])) {
                        $baseCounterDetails = count($tmp[$shid]['details'][$Seq]);
                    } else {
                        $baseCounterDetails = 0;
                    }
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['name'] = $PropertyName;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['shid'] = $PropertyID;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['status'] = 1;
                    // cancellationType nao existe
                    // $tmp[$code]['details'][$Seq][$baseCounterDetails]['cancellationType'] = $c_type;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-44";
                    if ($RoomView != "") {
                        $RoomType2 .= " - " . $RoomView;
                    }
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['room'] = $RoomType2;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['room_type'] = $RoomType2;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['adults'] = $Adults;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['children'] = $Children;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['total'] = (double) $Total;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['nett'] = $Total;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['special'] = false;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['specialdescription'] = "";
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['mealid'] = $MealBasisID;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['meal'] = $translator->translate($MealBasis);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $amount = $noOfNights * $Total;
                        if ($totalstayMarkup != 0) {
                            $amount = $amount + (($amount * $totalstayMarkup) / 100);
                        }
                        // Geo target markup
                        if ($internalmarkup != 0) {
                            $amount = $amount + (($amount * $internalmarkup) / 100);
                        }
                        // Agent markup
                        if ($agent_markup != 0) {
                            $amount = $amount + (($amount * $agent_markup) / 100);
                        }
                        // Fallback Markup
                        if ($totalstayMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                            $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
                        }
                        // Agent discount
                        if ($agent_discount != 0) {
                            $amount = $amount - (($amount * $agent_discount) / 100);
                        }
                        if ($scurrency != "" and $currency != $scurrency) {
                            $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
                        }
                        $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                        $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                        $pricebreakdownCount = $pricebreakdownCount + 1;
                    }
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                    $tmp[$shid]['details'][$Seq][$baseCounterDetails]['scurrency'] = $currency;
                    // policyDescription nao existe
                    // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $policyDescription;
                    // }
                }
            }
        }
    }
    $totalstay = true;
}
// error_log("\r\n " . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($totalstay == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $db2 = new \Zend\Db\Adapter\Adapter($config);
        $sql = "select hid, sid from xmlhotels_mtotalstay where " . $sfilter;
        // error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
        $statement2 = $db2->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            foreach ($resultSet2 as $row2) {
                // $sidfilter[] = "id=" . $row2->hid;
                $sidfilter[] = $row2->hid;
                if (is_array($hotels_array[$row2->hid])) {
                    // Append to original details
                    $tmph = $hotels_array[$row2->hid]['details'];
                    $tmps = $tmp[$row2->sid]['details'];
                    foreach ($tmph as $key => $value) {
                        $last = count($tmph[$key]);
                        foreach ($tmps[$key] as $keyd => $valued) {
                            $tmph[$key][$last] = $valued;
                            $last ++;
                        }
                    }
                    $hotels_array[$row2->hid]['details'] = $tmph;
                } else {
                    $hotels_array[$row2->hid] = $tmp[$row2->sid];
                }
            }
        }
        $db2->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 44;
        try {
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = new Sql($db2);
            $delete = $sql->delete();
            $delete->from('quote_session_totalstay');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db2);
            $insert = $sql->insert();
            $insert->into('quote_session_totalstay');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $raw,
                'xmlresult' => (string) $response,
                'data' => base64_encode(serialize($hotels_array)),
                'searchsettings' => base64_encode(serialize($requestdata))
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db2->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
?>