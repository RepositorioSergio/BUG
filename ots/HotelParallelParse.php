<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nOTS - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $OTA_HotelAvailRS = $inputDoc->getElementsByTagName("OTA_HotelAvailRS");
    // HotelStays
    $HotelStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName("HotelStays");
    if ($HotelStays->length > 0) {
        $HotelStay = $HotelStays->item(0)->getElementsByTagName("HotelStay");
        if ($HotelStay->length > 0) {
            for ($i = 0; $i < $HotelStay->length; $i ++) {
                $BasicPropertyInfo = $HotelStay->item($i)->getElementsByTagName("BasicPropertyInfo");
                if ($BasicPropertyInfo->length > 0) {
                    $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                    $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
                    $AreaID = $BasicPropertyInfo->item(0)->getAttribute("AreaID");
                    $HotelCodeContext = $BasicPropertyInfo->item(0)->getAttribute("HotelCodeContext");
                    $Award = $BasicPropertyInfo->item(0)->getElementsByTagName("Award");
                    if ($Award->length > 0) {
                        $Rating = $Award->item(0)->getAttribute("Rating");
                    } else {
                        $Rating = "";
                    }
                    $img = "";
                    $VendorMessages = $BasicPropertyInfo->item(0)->getElementsByTagName("VendorMessages");
                    if ($VendorMessages->length > 0) {
                        $VendorMessage = $VendorMessages->item(0)->getElementsByTagName("VendorMessage");
                        if ($VendorMessage->length > 0) {
                            $Title = $VendorMessage->item(0)->getAttribute("Title");
                            $SubSection = $VendorMessage->item(0)->getElementsByTagName("SubSection");
                            if ($SubSection->length > 0) {
                                $Paragraph = $SubSection->item(0)->getElementsByTagName("Paragraph");
                                if ($Paragraph->length > 0) {
                                    $Image = $Paragraph->item(0)->getElementsByTagName("Image");
                                    if ($Image->length > 0) {
                                        for ($iAux = 0; $iAux < $Image->length; $iAux ++) {
                                            $img = $Image->item($iAux)->nodeValue;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $HotelCode = "";
                    $HotelName = "";
                    $AreaID = "";
                    $HotelCodeContext = "";
                }
            }
        }
    }
    //
    // Areas
    //
    $txt = "";
    $Areas = $OTA_HotelAvailRS->item(0)->getElementsByTagName("Areas");
    if ($Areas->length > 0) {
        $Area = $Areas->item(0)->getElementsByTagName("Area");
        if ($Area->length > 0) {
            for ($j = 0; $j < $Area->length; $j ++) {
                $AreaID = $Area->item($j)->getAttribute("AreaID");
                $AreaDescription = $Area->item($j)->getElementsByTagName("AreaDescription");
                if ($AreaDescription->length > 0) {
                    $Name = $AreaDescription->item($jAux)->getAttribute("Name");
                    $Text = $AreaDescription->item($jAux)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($jAux2 = 0; $jAux2 < $Text->length; $jAux2 ++) {
                            $txt = $Text->item($jAux2)->nodeValue;
                        }
                    }
                }
            }
        }
    }
    // RoomStays
    $RoomStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName("RoomStays");
    if ($RoomStays->length > 0) {
        $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
        if ($RoomStay->length > 0) {
            for ($k = 0; $k < $RoomStay->length; $k ++) {
                $ResponseType = $RoomStay->item($k)->getAttribute("ResponseType");
                $RPH = $RoomStay->item($k)->getAttribute("RPH");
                $RoomStayCandidateRPH = $RoomStay->item($k)->getAttribute("RoomStayCandidateRPH");
                $RoomTypes = $RoomStay->item($k)->getElementsByTagName("RoomTypes");
                if ($RoomTypes->length > 0) {
                    $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        $RoomTypeCode = $RoomType->item(0)->getAttribute("RoomTypeCode");
                    }
                }
                $RoomRates = $RoomStay->item($k)->getElementsByTagName("RoomRates");
                if ($RoomRates->length > 0) {
                    $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
                    if ($RoomRate->length > 0) {
                        $RoomTypeCode2 = $RoomRate->item(0)->getAttribute("RoomTypeCode");
                        $NumberOfUnits = $RoomRate->item(0)->getAttribute("NumberOfUnits");
                        // Rates
                        $Rates = $RoomRate->item(0)->getElementsByTagName("Rates");
                        if ($Rates->length > 0) {
                            $Rate = $Rates->item(0)->getElementsByTagName("Rate");
                            if ($Rate->length > 0) {
                                $Base = $Rate->item(0)->getElementsByTagName("Base");
                                if ($Base->length > 0) {
                                    $BaseCurrencyCode = $Base->item(0)->getAttribute("CurrencyCode");
                                    $BaseAmountAfterTax = $Base->item(0)->getAttribute("AmountAfterTax");
                                }
                                $Total = $Rate->item(0)->getElementsByTagName("Total");
                                if ($Total->length > 0) {
                                    $TotalCurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                                    $TotalAmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                                }
                            }
                        }
                        // Features
                        $Features = $RoomRate->item(0)->getElementsByTagName("Features");
                        if ($Features->length > 0) {
                            $Feature = $Features->item(0)->getElementsByTagName("Feature");
                            if ($Feature->length > 0) {
                                $Description = $Feature->item(0)->getElementsByTagName("Description");
                                if ($Description->length > 0) {
                                    $Text = $Description->item(0)->getElementsByTagName('Text');
                                    if ($Text->length > 0) {
                                        $Text = $Text->item(0)->nodeValue;
                                    } else {
                                        $Text = "";
                                    }
                                }
                            }
                        }
                    }
                }
                // GuestCounts
                $GuestCounts = $RoomStay->item($k)->getElementsByTagName("GuestCounts");
                if ($GuestCounts->length > 0) {
                    $GuestCount = $GuestCounts->item(0)->getElementsByTagName("GuestCount");
                    if ($GuestCount->length > 0) {
                        $Count = $GuestCount->item(0)->getAttribute("Count");
                        $AgeQualifyingCode = $GuestCount->item(0)->getAttribute("AgeQualifyingCode");
                    }
                }
                // TimeSpan
                $TimeSpan = $RoomStay->item($k)->getElementsByTagName("TimeSpan");
                if ($TimeSpan->length > 0) {
                    $End = $TimeSpan->item(0)->getAttribute("End");
                    $Start = $TimeSpan->item(0)->getAttribute("Start");
                }
                // Reference
                $Reference = $RoomStay->item($k)->getElementsByTagName("Reference");
                if ($Reference->length > 0) {
                    $ID_Context = $Reference->item(0)->getAttribute("ID_Context");
                    $ReferenceID = $Reference->item(0)->getAttribute("ID");
                    $Type = $Reference->item(0)->getAttribute("Type");
                }
                // BasicPropertyInfo
                $BasicPropertyInfo = $RoomStay->item($k)->getElementsByTagName("BasicPropertyInfo");
                if ($BasicPropertyInfo->length > 0) {
                    $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                    $shid = $HotelCode;
                    $sfilter[] = " sid='$HotelCode' ";
                    $VendorMessages = $BasicPropertyInfo->item(0)->getElementsByTagName('VendorMessages');
                    if ($VendorMessages->length > 0) {
                        $VendorMessage = $VendorMessages->item(0)->getElementsByTagName('VendorMessage');
                        if ($VendorMessage->length > 0) {
                            $VendorMessageTitle = $VendorMessage->item(0)->getAttribute("Title");
                            $VendorMessageInfoType = $VendorMessage->item(0)->getAttribute("InfoType");
                            
                            $SubSection = $VendorMessage->item(0)->getElementsByTagName('SubSection');
                            if ($SubSection->length > 0) {
                                for ($x = 0; $x < $SubSection->length; $x ++) {
                                    $SubCode = $SubSection->item($x)->getAttribute("SubCode");
                                    $SubTitle = $SubSection->item($x)->getAttribute("SubTitle");
                                    
                                    $Paragraph = $SubSection->item($x)->getElementsByTagName('Paragraph');
                                    if ($Paragraph->length > 0) {
                                        $ParagraphText = $Paragraph->item(0)->getElementsByTagName('Text');
                                        if ($ParagraphText->length > 0) {
                                            $ParagraphText = $ParagraphText->item(0)->nodeValue;
                                        } else {
                                            $ParagraphText = "";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $rooms[$baseCounterDetails]['name'] = $HotelName;
                $rooms[$baseCounterDetails]['hotelid'] = $HotelCode;
                $rooms[$baseCounterDetails]['roomid'] = $RoomTypeCode;
                $rooms[$baseCounterDetails]['code'] = $HotelCode;
                $rooms[$baseCounterDetails]['scode'] = $HotelCode;
                $rooms[$baseCounterDetails]['shid'] = $HotelCode;
                $rooms[$baseCounterDetails]['status'] = 1;
                $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-1";
                $rooms[$baseCounterDetails]['room'] = $RoomTypeCode;
                $rooms[$baseCounterDetails]['roomtypecode'] = $RoomTypeCode;
                $rooms[$baseCounterDetails]['room_description'] = $Roomname;
                $rooms[$baseCounterDetails]['RPH'] = $RPH;
                $rooms[$baseCounterDetails]['RoomStayCandidateRPH'] = $RoomStayCandidateRPH;
                $rooms[$baseCounterDetails]['adults'] = $adults;
                $rooms[$baseCounterDetails]['children'] = $children;
                $rooms[$baseCounterDetails]['mealinformation'] = $Text;
                $rooms[$baseCounterDetails]['nettotal'] = (double) $TotalAmountAfterTax;
                if ($OTSMarkup != 0) {
                    $TotalAmountAfterTax = $TotalAmountAfterTax + (($TotalAmountAfterTax * $OTSMarkup) / 100);
                }
                // Geo target markup
                if ($internalmarkup != 0) {
                    $TotalAmountAfterTax = $TotalAmountAfterTax + (($TotalAmountAfterTax * $internalmarkup) / 100);
                }
                // Agent markup
                if ($agent_markup != 0) {
                    $TotalAmountAfterTax = $TotalAmountAfterTax + (($TotalAmountAfterTax * $agent_markup) / 100);
                }
                // Fallback Markup
                if ($OTSMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                    $TotalAmountAfterTax = $TotalAmountAfterTax + (($TotalAmountAfterTax * $HotelsMarkupFallback) / 100);
                }
                // Agent discount
                if ($agent_discount != 0) {
                    $TotalAmountAfterTax = $TotalAmountAfterTax - (($TotalAmountAfterTax * $agent_discount) / 100);
                }
                if ($scurrency != "" and $currency != $scurrency) {
                    $TotalAmountAfterTax = $CurrencyConverter->convert($TotalAmountAfterTax, $currency, $scurrency);
                }
                $rooms[$baseCounterDetails]['total'] = (double) $TotalAmountAfterTax;
                $rooms[$baseCounterDetails]['totalplain'] = (double) $TotalAmountAfterTax;
                try {
                    $sql = "select mapped from board_mapping where description='" . addslashes($Text) . "'";
                    $statement = $db->createStatement($sql);
                    $statement->prepare();
                    $row_board_mapping = $statement->execute();
                    $row_board_mapping->buffer();
                    if ($row_board_mapping->valid()) {
                        $row_board_mapping = $row_board_mapping->current();
                        $Text = $row_board_mapping["mapped"];
                    }
                } catch (\Exception $e) {
                    $logger = new Logger();
                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                    $logger->addWriter($writer);
                    $logger->info($e->getMessage());
                }
                $rooms[$baseCounterDetails]['meal'] = $translator->translate($Text);
                $pricebreakdown = array();
                $pricebreakdownCount = 0;
                $amount = $TotalAmountAfterTax / $noOfNights;
                if ($OTSMarkup != 0) {
                    $amount = $amount + (($amount * $OTSMarkup) / 100);
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
                if ($OTSMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                    $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
                }
                // Agent discount
                if ($agent_discount != 0) {
                    $amount = $amount - (($amount * $agent_discount) / 100);
                }
                if ($scurrency != "" and $currency != $scurrency) {
                    $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
                }
                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                    $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
                    $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                    $pricebreakdownCount = $pricebreakdownCount + 1;
                }
                $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                $rooms[$baseCounterDetails]['scurrency'] = $TotalCurrencyCode;
                //
                // TODO - Specials
                //
                /*
                 * if ($PromotionName != "") {
                 * $rooms[$baseCounterDetails]['special'] = true;
                 * $rooms[$baseCounterDetails]['specialdescription'] = $PromotionName;
                 * } else {
                 */
                $rooms[$baseCounterDetails]['special'] = false;
                $rooms[$baseCounterDetails]['specialdescription'] = "";
                // }
                //
                // TODO - Cancellation policies
                //
                $rooms[$baseCounterDetails]['cancelpolicy'] = "";
                $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
                $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                $rooms[$baseCounterDetails]['currency'] = strtoupper($TotalCurrencyCode);
                $baseCounterDetails ++;
                // $agoda = true;
            }
        }
    }
    //
    // Store Session
    //
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_ots');
    $delete->where(array(
        'session_id' => $session_id_tmp
    ));
    $statement = $sql->prepareStatementForSqlObject($delete);
    try {
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('quote_session_ots');
    $insert->values(array(
        'session_id' => $session_id_tmp,
        'xmlrequest' => (string) $request,
        'xmlresult' => (string) $response,
        'data' => base64_encode(serialize($srooms)),
        'searchsettings' => base64_encode(serialize($requestdata))
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    try {
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
}
?>