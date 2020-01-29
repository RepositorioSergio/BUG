<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nRTS - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    $response = str_replace('&lt;', '<', $response);
    $response = str_replace('&gt;', '>', $response);
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $Envelope = $inputDoc->getElementsByTagName("Envelope");
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $GetHotelSearchListForCustomerCountResponse = $Body->item(0)->getElementsByTagName("GetHotelSearchListForCustomerCountResponse");
    // GetHotelSearchListForCustomerCountResult
    $GetHotelSearchListForCustomerCountResult = $GetHotelSearchListForCustomerCountResponse->item(0)->getElementsByTagName("GetHotelSearchListForCustomerCountResult");
    if ($GetHotelSearchListForCustomerCountResult->length > 0) {
        $GetHotelSearchListResponse = $GetHotelSearchListForCustomerCountResult->item(0)->getElementsByTagName("GetHotelSearchListResponse");
        if ($GetHotelSearchListResponse->length > 0) {
            $node = $GetHotelSearchListResponse->item(0)->getElementsByTagName("GetHotelSearchListResult");
            $LanguageCode = $node->item(0)->getElementsByTagName("LanguageCode");
            if ($LanguageCode->length > 0) {
                $LanguageCode = $LanguageCode->item(0)->nodeValue;
            } else {
                $LanguageCode = "";
            }
            $LanguageName = $node->item(0)->getElementsByTagName("LanguageName");
            if ($LanguageName->length > 0) {
                $LanguageName = $LanguageName->item(0)->nodeValue;
            } else {
                $LanguageName = "";
            }
            $NationalityCode = $node->item(0)->getElementsByTagName("NationalityCode");
            if ($NationalityCode->length > 0) {
                $NationalityCode = $NationalityCode->item(0)->nodeValue;
            } else {
                $NationalityCode = "";
            }
            $NationalityName = $node->item(0)->getElementsByTagName("NationalityName");
            if ($NationalityName->length > 0) {
                $NationalityName = $NationalityName->item(0)->nodeValue;
            } else {
                $NationalityName = "";
            }
            $ContinentCode = $node->item(0)->getElementsByTagName("ContinentCode");
            if ($ContinentCode->length > 0) {
                $ContinentCode = $ContinentCode->item(0)->nodeValue;
            } else {
                $ContinentCode = "";
            }
            $CityCode = $node->item(0)->getElementsByTagName("CityCode");
            if ($CityCode->length > 0) {
                $CityCode = $CityCode->item(0)->nodeValue;
            } else {
                $CityCode = "";
            }
            $CityEname = $node->item(0)->getElementsByTagName("CityEname");
            if ($CityEname->length > 0) {
                $CityEname = $CityEname->item(0)->nodeValue;
            } else {
                $CityEname = "";
            }
            $CityName = $node->item(0)->getElementsByTagName("CityName");
            if ($CityName->length > 0) {
                $CityName = $CityName->item(0)->nodeValue;
            } else {
                $CityName = "";
            }
            $CountryCode = $node->item(0)->getElementsByTagName("CountryCode");
            if ($CountryCode->length > 0) {
                $CountryCode = $CountryCode->item(0)->nodeValue;
            } else {
                $CountryCode = "";
            }
            $CountryEname = $node->item(0)->getElementsByTagName("CountryEname");
            if ($CountryEname->length > 0) {
                $CountryEname = $CountryEname->item(0)->nodeValue;
            } else {
                $CountryEname = "";
            }
            $CountryName = $node->item(0)->getElementsByTagName("CountryName");
            if ($CountryName->length > 0) {
                $CountryName = $CountryName->item(0)->nodeValue;
            } else {
                $CountryName = "";
            }
            $StateCode = $node->item(0)->getElementsByTagName("StateCode");
            if ($StateCode->length > 0) {
                $StateCode = $StateCode->item(0)->nodeValue;
            } else {
                $StateCode = "";
            }
            $StateEname = $node->item(0)->getElementsByTagName("StateEname");
            if ($StateEname->length > 0) {
                $StateEname = $StateEname->item(0)->nodeValue;
            } else {
                $StateEname = "";
            }
            $StateName = $node->item(0)->getElementsByTagName("StateName");
            if ($StateName->length > 0) {
                $StateName = $StateName->item(0)->nodeValue;
            } else {
                $StateName = "";
            }
            $CheckInDate = $node->item(0)->getElementsByTagName("CheckInDate");
            if ($CheckInDate->length > 0) {
                $CheckInDate = $CheckInDate->item(0)->nodeValue;
            } else {
                $CheckInDate = "";
            }
            $CheckInWeekday = $node->item(0)->getElementsByTagName("CheckInWeekday");
            if ($CheckInWeekday->length > 0) {
                $CheckInWeekday = $CheckInWeekday->item(0)->nodeValue;
            } else {
                $CheckInWeekday = "";
            }
            $CheckOutDate = $node->item(0)->getElementsByTagName("CheckOutDate");
            if ($CheckOutDate->length > 0) {
                $CheckOutDate = $CheckOutDate->item(0)->nodeValue;
            } else {
                $CheckOutDate = "";
            }
            $CheckOutWeekday = $node->item(0)->getElementsByTagName("CheckOutWeekday");
            if ($CheckOutWeekday->length > 0) {
                $CheckOutWeekday = $CheckOutWeekday->item(0)->nodeValue;
            } else {
                $CheckOutWeekday = "";
            }
            
            $Duration = $node->item(0)->getElementsByTagName("Duration");
            if ($Duration->length > 0) {
                $Duration = $Duration->item(0)->nodeValue;
            } else {
                $Duration = "";
            }
            $CheckInLeftDays = $node->item(0)->getElementsByTagName("CheckInLeftDays");
            if ($CheckInLeftDays->length > 0) {
                $CheckInLeftDays = $CheckInLeftDays->item(0)->nodeValue;
            } else {
                $CheckInLeftDays = "";
            }
            $ItemName = $node->item(0)->getElementsByTagName("ItemName");
            if ($ItemName->length > 0) {
                $ItemName = $ItemName->item(0)->nodeValue;
            } else {
                $ItemName = "";
            }
            $ItemCode = $node->item(0)->getElementsByTagName("ItemCode");
            if ($ItemCode->length > 0) {
                $ItemCode = $ItemCode->item(0)->nodeValue;
            } else {
                $ItemCode = "";
            }
            $ItemNo = $node->item(0)->getElementsByTagName("ItemNo");
            if ($ItemNo->length > 0) {
                $ItemNo = $ItemNo->item(0)->nodeValue;
            } else {
                $ItemNo = "";
            }
            $StarRating = $node->item(0)->getElementsByTagName("StarRating");
            if ($StarRating->length > 0) {
                $StarRating = $StarRating->item(0)->nodeValue;
            } else {
                $StarRating = "";
            }
            $LocationCode = $node->item(0)->getElementsByTagName("LocationCode");
            if ($LocationCode->length > 0) {
                $LocationCode = $LocationCode->item(0)->nodeValue;
            } else {
                $LocationCode = "";
            }
            $AvailableHotelOnly = $node->item(0)->getElementsByTagName("AvailableHotelOnly");
            if ($AvailableHotelOnly->length > 0) {
                $AvailableHotelOnly = $AvailableHotelOnly->item(0)->nodeValue;
            } else {
                $AvailableHotelOnly = "";
            }
            $RecommendHotelOnly = $node->item(0)->getElementsByTagName("RecommendHotelOnly");
            if ($RecommendHotelOnly->length > 0) {
                $RecommendHotelOnly = $RecommendHotelOnly->item(0)->nodeValue;
            } else {
                $RecommendHotelOnly = "";
            }
            $TotalResultCount = $node->item(0)->getElementsByTagName("TotalResultCount");
            if ($TotalResultCount->length > 0) {
                $TotalResultCount = $TotalResultCount->item(0)->nodeValue;
            } else {
                $TotalResultCount = "";
            }
            
            $ExchangeConvertDate = $node->item(0)->getElementsByTagName("ExchangeConvertDate");
            if ($ExchangeConvertDate->length > 0) {
                $ExchangeConvertDate = $ExchangeConvertDate->item(0)->nodeValue;
            } else {
                $ExchangeConvertDate = "";
            }
            $SellingCurrencyCode = $node->item(0)->getElementsByTagName("SellingCurrencyCode");
            if ($SellingCurrencyCode->length > 0) {
                $SellingCurrencyCode = $SellingCurrencyCode->item(0)->nodeValue;
            } else {
                $SellingCurrencyCode = "";
            }
            $ClientCurrencyCode = $node->item(0)->getElementsByTagName("ClientCurrencyCode");
            if ($ClientCurrencyCode->length > 0) {
                $ClientCurrencyCode = $ClientCurrencyCode->item(0)->nodeValue;
            } else {
                $ClientCurrencyCode = "";
            }
            $SellingConvertRate = $node->item(0)->getElementsByTagName("SellingConvertRate");
            if ($SellingConvertRate->length > 0) {
                $SellingConvertRate = $SellingConvertRate->item(0)->nodeValue;
            } else {
                $SellingConvertRate = "";
            }
            $CityEventList = $node->item(0)->getElementsByTagName("CityEventList");
            if ($CityEventList->length > 0) {
                $CityEventList = $CityEventList->item(0)->nodeValue;
            } else {
                $CityEventList = "";
            }
            
            $RoomList = $node->item(0)->getElementsByTagName("RoomList");
            if ($RoomList->length > 0) {
                $RoomInfo = $RoomList->item(0)->getElementsByTagName("RoomInfo");
                if ($RoomInfo->length > 0) {
                    for ($i = 0; $i < $RoomInfo->length; $i ++) {
                        $BedTypeCode = $RoomInfo->item($i)->getElementsByTagName("BedTypeCode");
                        if ($BedTypeCode->length > 0) {
                            $BedTypeCode = $BedTypeCode->item(0)->nodeValue;
                        } else {
                            $BedTypeCode = "";
                        }
                        $RoomCount = $RoomInfo->item($i)->getElementsByTagName("RoomCount");
                        if ($RoomCount->length > 0) {
                            $RoomCount = $RoomCount->item(0)->nodeValue;
                        } else {
                            $RoomCount = "";
                        }
                        $ChlidAge1 = $RoomInfo->item($i)->getElementsByTagName("ChlidAge1");
                        if ($ChlidAge1->length > 0) {
                            $ChlidAge1 = $ChlidAge1->item(0)->nodeValue;
                        } else {
                            $ChlidAge1 = "";
                        }
                        $ChlidAge2 = $RoomInfo->item($i)->getElementsByTagName("ChlidAge2");
                        if ($ChlidAge2->length > 0) {
                            $ChlidAge2 = $ChlidAge2->item(0)->nodeValue;
                        } else {
                            $ChlidAge2 = "";
                        }
                    }
                }
            }
            $HotelSearchList = $node->item(0)->getElementsByTagName("HotelSearchList");
            if ($HotelSearchList->length > 0) {
                $HotelItemInfo = $HotelSearchList->item(0)->getElementsByTagName("HotelItemInfo");
                for ($w = 0; $w < $HotelItemInfo->length; $w ++) {
                    $ItemCode = $HotelItemInfo->item($w)->getElementsByTagName("ItemCode");
                    if ($ItemCode->length > 0) {
                        $ItemCode = $ItemCode->item(0)->nodeValue;
                    } else {
                        $ItemCode = "";
                    }
                    $shid = $ItemCode;
                    $sfilter[] = " sid='$ItemCode' ";
                    $ItemName = $HotelItemInfo->item($w)->getElementsByTagName("ItemName");
                    if ($ItemName->length > 0) {
                        $ItemName = $ItemName->item(0)->nodeValue;
                    } else {
                        $ItemName = "";
                    }
                    $StarRating = $HotelItemInfo->item($w)->getElementsByTagName("StarRating");
                    if ($StarRating->length > 0) {
                        $StarRating = $StarRating->item(0)->nodeValue;
                    } else {
                        $StarRating = "";
                    }
                    $RecommendYn = $HotelItemInfo->item($w)->getElementsByTagName("RecommendYn");
                    if ($RecommendYn->length > 0) {
                        $RecommendYn = $RecommendYn->item(0)->nodeValue;
                    } else {
                        $RecommendYn = "";
                    }
                    $ExpertReportYn = $HotelItemInfo->item($w)->getElementsByTagName("ExpertReportYn");
                    if ($ExpertReportYn->length > 0) {
                        $ExpertReportYn = $ExpertReportYn->item(0)->nodeValue;
                    } else {
                        $ExpertReportYn = "";
                    }
                    $FirstImageFileName = $HotelItemInfo->item($w)->getElementsByTagName("FirstImageFileName");
                    if ($FirstImageFileName->length > 0) {
                        $FirstImageFileName = $FirstImageFileName->item(0)->nodeValue;
                    } else {
                        $FirstImageFileName = "";
                    }
                    $HotelDescription = $HotelItemInfo->item($w)->getElementsByTagName("HotelDescription");
                    if ($HotelDescription->length > 0) {
                        $HotelDescription = $HotelDescription->item(0)->nodeValue;
                    } else {
                        $HotelDescription = "";
                    }
                    $BackpackYn = $HotelItemInfo->item($w)->getElementsByTagName("BackpackYn");
                    if ($BackpackYn->length > 0) {
                        $BackpackYn = $BackpackYn->item(0)->nodeValue;
                    } else {
                        $BackpackYn = "";
                    }
                    $BusinessYn = $HotelItemInfo->item($w)->getElementsByTagName("BusinessYn");
                    if ($BusinessYn->length > 0) {
                        $BusinessYn = $BusinessYn->item(0)->nodeValue;
                    } else {
                        $BusinessYn = "";
                    }
                    $HoneymoonYn = $HotelItemInfo->item($w)->getElementsByTagName("HoneymoonYn");
                    if ($HoneymoonYn->length > 0) {
                        $HoneymoonYn = $HoneymoonYn->item(0)->nodeValue;
                    } else {
                        $HoneymoonYn = "";
                    }
                    $FairYn = $HotelItemInfo->item($w)->getElementsByTagName("FairYn");
                    if ($FairYn->length > 0) {
                        $FairYn = $FairYn->item(0)->nodeValue;
                    } else {
                        $FairYn = "";
                    }
                    $AirPackYn = $HotelItemInfo->item($w)->getElementsByTagName("AirPackYn");
                    if ($AirPackYn->length > 0) {
                        $AirPackYn = $AirPackYn->item(0)->nodeValue;
                    } else {
                        $AirPackYn = "";
                    }
                    $BookingCount = $HotelItemInfo->item($w)->getElementsByTagName("BookingCount");
                    if ($BookingCount->length > 0) {
                        $BookingCount = $BookingCount->item(0)->nodeValue;
                    } else {
                        $BookingCount = "";
                    }
                    $LocationList = $HotelItemInfo->item($w)->getElementsByTagName("LocationList");
                    if ($LocationList->length > 0) {
                        $LocationList = $LocationList->item(0)->nodeValue;
                    } else {
                        $LocationList = "";
                    }
                    $GeoCode = $HotelItemInfo->item($w)->getElementsByTagName("GeoCode");
                    if ($GeoCode->length > 0) {
                        $Latitude = $GeoCode->item(0)->getElementsByTagName("Latitude");
                        if ($Latitude->length > 0) {
                            $Latitude = $Latitude->item(0)->nodeValue;
                        } else {
                            $Latitude = "";
                        }
                        $Longitude = $GeoCode->item(0)->getElementsByTagName("Longitude");
                        if ($Longitude->length > 0) {
                            $Longitude = $Longitude->item(0)->nodeValue;
                        } else {
                            $Longitude = "";
                        }
                    }
                    $PriceList = $HotelItemInfo->item($w)->getElementsByTagName("PriceList");
                    if ($PriceList->length > 0) {
                        $PriceInfo = $PriceList->item(0)->getElementsByTagName("PriceInfo");
                        if ($PriceInfo->length > 0) {
                            for ($k = 0; $k < $PriceInfo->length; $k ++) {
                                $PriceInfoItemNo = $PriceInfo->item($k)->getElementsByTagName("ItemNo");
                                if ($PriceInfoItemNo->length > 0) {
                                    $PriceInfoItemNo = $PriceInfoItemNo->item(0)->nodeValue;
                                } else {
                                    $PriceInfoItemNo = "";
                                }
                                $PriceInfoItemCode = $PriceInfo->item($k)->getElementsByTagName("ItemCode");
                                if ($PriceInfoItemCode->length > 0) {
                                    $PriceInfoItemCode = $PriceInfoItemCode->item(0)->nodeValue;
                                } else {
                                    $PriceInfoItemCode = "";
                                }
                                $SupplierCompCode = $PriceInfo->item($k)->getElementsByTagName("SupplierCompCode");
                                if ($SupplierCompCode->length > 0) {
                                    $SupplierCompCode = $SupplierCompCode->item(0)->nodeValue;
                                } else {
                                    $SupplierCompCode = "";
                                }
                                $RoomTypeCode = $PriceInfo->item($k)->getElementsByTagName("RoomTypeCode");
                                if ($RoomTypeCode->length > 0) {
                                    $RoomTypeCode = $RoomTypeCode->item(0)->nodeValue;
                                } else {
                                    $RoomTypeCode = "";
                                }
                                $RoomTypeName = $PriceInfo->item($k)->getElementsByTagName("RoomTypeName");
                                if ($RoomTypeName->length > 0) {
                                    $RoomTypeName = $RoomTypeName->item(0)->nodeValue;
                                } else {
                                    $RoomTypeName = "";
                                }
                                $BreakfastTypeName = $PriceInfo->item($k)->getElementsByTagName("BreakfastTypeName");
                                if ($BreakfastTypeName->length > 0) {
                                    $BreakfastTypeName = $BreakfastTypeName->item(0)->nodeValue;
                                } else {
                                    $BreakfastTypeName = "";
                                }
                                $AddBreakfastTypeName = $PriceInfo->item($k)->getElementsByTagName("AddBreakfastTypeName");
                                if ($AddBreakfastTypeName->length > 0) {
                                    $AddBreakfastTypeName = $AddBreakfastTypeName->item(0)->nodeValue;
                                } else {
                                    $AddBreakfastTypeName = "";
                                }
                                $PriceComment = $PriceInfo->item($k)->getElementsByTagName("PriceComment");
                                if ($PriceComment->length > 0) {
                                    $PriceComment = $PriceComment->item(0)->nodeValue;
                                } else {
                                    $PriceComment = "";
                                }
                                $FareRateType = $PriceInfo->item($k)->getElementsByTagName("FareRateType");
                                if ($FareRateType->length > 0) {
                                    $FareRateType = $FareRateType->item(0)->nodeValue;
                                } else {
                                    $FareRateType = "";
                                }
                                $PriceStatus = $PriceInfo->item($k)->getElementsByTagName("PriceStatus");
                                if ($PriceStatus->length > 0) {
                                    $PriceStatus = $PriceStatus->item(0)->nodeValue;
                                } else {
                                    $PriceStatus = "";
                                }
                                $NetCurrencyCode = $PriceInfo->item($k)->getElementsByTagName("NetCurrencyCode");
                                if ($NetCurrencyCode->length > 0) {
                                    $NetCurrencyCode = $NetCurrencyCode->item(0)->nodeValue;
                                } else {
                                    $NetCurrencyCode = "";
                                }
                                $NetConvertRate = $PriceInfo->item($k)->getElementsByTagName("NetConvertRate");
                                if ($NetConvertRate->length > 0) {
                                    $NetConvertRate = $NetConvertRate->item(0)->nodeValue;
                                } else {
                                    $NetConvertRate = "";
                                }
                                $SellerNetPrice = $PriceInfo->item($k)->getElementsByTagName("SellerNetPrice");
                                if ($SellerNetPrice->length > 0) {
                                    $SellerNetPrice = $SellerNetPrice->item(0)->nodeValue;
                                } else {
                                    $SellerNetPrice = "";
                                }
                                $LocalNetPrice = $PriceInfo->item($k)->getElementsByTagName("LocalNetPrice");
                                if ($LocalNetPrice->length > 0) {
                                    $LocalNetPrice = $LocalNetPrice->item(0)->nodeValue;
                                } else {
                                    $LocalNetPrice = "";
                                }
                                $SellerMarkupPrice = $PriceInfo->item($k)->getElementsByTagName("SellerMarkupPrice");
                                if ($SellerMarkupPrice->length > 0) {
                                    $SellerMarkupPrice = $SellerMarkupPrice->item(0)->nodeValue;
                                } else {
                                    $SellerMarkupPrice = "";
                                }
                                $RecommendClientPrice = $PriceInfo->item($k)->getElementsByTagName("RecommendClientPrice");
                                if ($RecommendClientPrice->length > 0) {
                                    $RecommendClientPrice = $RecommendClientPrice->item(0)->nodeValue;
                                } else {
                                    $RecommendClientPrice = "";
                                }
                                $SellerClientPrice = $PriceInfo->item($k)->getElementsByTagName("SellerClientPrice");
                                if ($SellerClientPrice->length > 0) {
                                    $SellerClientPrice = $SellerClientPrice->item(0)->nodeValue;
                                } else {
                                    $SellerClientPrice = "";
                                }
                                $DoubleBedYn = $PriceInfo->item($k)->getElementsByTagName("DoubleBedYn");
                                if ($DoubleBedYn->length > 0) {
                                    $DoubleBedYn = $DoubleBedYn->item(0)->nodeValue;
                                } else {
                                    $DoubleBedYn = "";
                                }
                                $SupplierPromotion = $PriceInfo->item($k)->getElementsByTagName("SupplierPromotion");
                                if ($SupplierPromotion->length > 0) {
                                    $PromotionName = $SupplierPromotion->item(0)->getElementsByTagName("PromotionName");
                                    if ($PromotionName->length > 0) {
                                        $PromotionName = $PromotionName->item(0)->nodeValue;
                                    } else {
                                        $PromotionName = "";
                                    }
                                    $PromotionDescription = $SupplierPromotion->item(0)->getElementsByTagName("PromotionDescription");
                                    if ($PromotionDescription->length > 0) {
                                        $PromotionDescription = $PromotionDescription->item(0)->nodeValue;
                                    } else {
                                        $PromotionDescription = "";
                                    }
                                }
                                $PriceBreakdown = $PriceInfo->item($k)->getElementsByTagName("PriceBreakdown");
                                if ($PriceBreakdown->length > 0) {
                                    for ($j = 0; $j < $PriceBreakdown->length; $j ++) {
                                        $Date = $PriceBreakdown->item($j)->getElementsByTagName("Date");
                                        if ($Date->length > 0) {
                                            $Date = $Date->item(0)->nodeValue;
                                        } else {
                                            $Date = "";
                                        }
                                        $Price = $PriceBreakdown->item($j)->getElementsByTagName("Price");
                                        if ($Price->length > 0) {
                                            $Price = $Price->item(0)->nodeValue;
                                        } else {
                                            $Price = "";
                                        }
                                    }
                                }
                                $rooms[$baseCounterDetails]['name'] = $Name;
                                $rooms[$baseCounterDetails]['hotelid'] = $ItemCode;
                                $rooms[$baseCounterDetails]['roomid'] = $IDRoomRate;
                                $rooms[$baseCounterDetails]['code'] = $ItemCode;
                                $rooms[$baseCounterDetails]['scode'] = $ItemCode;
                                $rooms[$baseCounterDetails]['shid'] = $ItemCode;
                                $rooms[$baseCounterDetails]['status'] = 1;
                                $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-19";
                                $rooms[$baseCounterDetails]['room'] = $RoomTypeName;
                                $rooms[$baseCounterDetails]['roomtype'] = $RoomTypeCode;
                                $rooms[$baseCounterDetails]['room_description'] = $RoomTypeName;
                                $rooms[$baseCounterDetails]['CityCode'] = $CityCode;
                                $rooms[$baseCounterDetails]['ItemCode'] = $ItemCode;
                                $rooms[$baseCounterDetails]['ItemNo'] = $ItemNo;
                                $rooms[$baseCounterDetails]['adults'] = $adults;
                                $rooms[$baseCounterDetails]['children'] = $children;
                                $rooms[$baseCounterDetails]['nettotal'] = (double) $SellerNetPrice;
                                if ($rtsMarkup != 0) {
                                    $SellerNetPrice = $SellerNetPrice + (($SellerNetPrice * $rtsMarkup) / 100);
                                }
                                // Geo target markup
                                if ($internalmarkup != 0) {
                                    $SellerNetPrice = $SellerNetPrice + (($SellerNetPrice * $internalmarkup) / 100);
                                }
                                // Agent markup
                                if ($agent_markup != 0) {
                                    $SellerNetPrice = $SellerNetPrice + (($SellerNetPrice * $agent_markup) / 100);
                                }
                                // Fallback Markup
                                if ($rtsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                    $SellerNetPrice = $SellerNetPrice + (($SellerNetPrice * $HotelsMarkupFallback) / 100);
                                }
                                // Agent discount
                                if ($agent_discount != 0) {
                                    $SellerNetPrice = $SellerNetPrice - (($SellerNetPrice * $agent_discount) / 100);
                                }
                                if ($scurrency != "" and $currency != $scurrency) {
                                    $SellerNetPrice = $CurrencyConverter->convert($SellerNetPrice, $currency, $scurrency);
                                }
                                $rooms[$baseCounterDetails]['total'] = (double) $SellerNetPrice;
                                $rooms[$baseCounterDetails]['totalplain'] = (double) $SellerNetPrice;
                                try {
                                    $sql = "select mapped from board_mapping where description='" . addslashes($BreakfastTypeName) . "'";
                                    $statement = $db->createStatement($sql);
                                    $statement->prepare();
                                    $row_board_mapping = $statement->execute();
                                    $row_board_mapping->buffer();
                                    if ($row_board_mapping->valid()) {
                                        $row_board_mapping = $row_board_mapping->current();
                                        $BreakfastTypeName = $row_board_mapping["mapped"];
                                    }
                                } catch (\Exception $e) {
                                    $logger = new Logger();
                                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                    $logger->addWriter($writer);
                                    $logger->info($e->getMessage());
                                }
                                $rooms[$baseCounterDetails]['meal'] = $translator->translate($BreakfastTypeName);
                                $pricebreakdown = array();
                                $pricebreakdownCount = 0;
                                $amount = $SellerNetPrice / $noOfNights;
                                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                    $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
                                    $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                    $pricebreakdownCount = $pricebreakdownCount + 1;
                                }
                                $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                $rooms[$baseCounterDetails]['scurrency'] = $ClientCurrencyCode;
                                //
                                // Special
                                //
                                if ($PromotionName != "") {
                                    $rooms[$baseCounterDetails]['special'] = true;
                                    $rooms[$baseCounterDetails]['specialdescription'] = $PromotionName;
                                } else {
                                    $rooms[$baseCounterDetails]['special'] = false;
                                    $rooms[$baseCounterDetails]['specialdescription'] = "";
                                }
                                $rooms[$baseCounterDetails]['FareRateType'] = $FareRateType;
                                $rooms[$baseCounterDetails]['DailyCostCancel'] = $DailyCostCancel;
                                //
                                // Cancellation policies
                                //
                                $procurar = "Non-Refundable";
                                if (strpos($PromotionName, $procurar) !== false) {
                                    $rooms[$baseCounterDetails]['nonrefundable'] = true;
                                    $rooms[$baseCounterDetails]['cancelpolicy'] = $translator->translate("This is a non refundable booking");
                                    $rooms[$baseCounterDetails]['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
                                    $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                                    $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = time();
                                } else {
                                    $rooms[$baseCounterDetails]['cancelpolicy'] = "";
                                    $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = 0;
                                    $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                                }
                                $rooms[$baseCounterDetails]['currency'] = strtoupper($ClientCurrencyCode);
                                $baseCounterDetails ++;
                            }
                        }
                    }
                }
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
    $delete->from('quote_session_rts');
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
    $insert->into('quote_session_rts');
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