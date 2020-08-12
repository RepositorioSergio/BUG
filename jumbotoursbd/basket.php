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
echo "COMECOU BASKET<br/>";
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

$url = 'https://test.xtravelsystem.com/public/v1_0rc1/basketHandler';

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tns="http://xtravelsystem.com/v1_0rc1/basket/types" xmlns:xs="http://www.w3.org/2001/XMLSchema">
<soapenv:Header/>
<soapenv:Body>
   <typ:getBasketExtended>
      <GetBasketRQ_1>
         <agencyCode>266333</agencyCode>
         <brandCode>1</brandCode>
         <pointOfSaleId>1</pointOfSaleId>
         <basketId>8675272212</basketId>
      </GetBasketRQ_1>
   </typ:getBasketExtended>
</soapenv:Body>
</soapenv:Envelope>';

$headers = array(
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
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
$getBasketExtendedResponse = $Body->item(0)->getElementsByTagName("getBasketExtendedResponse");
if ($getBasketExtendedResponse->length > 0) {
    $result = $getBasketExtendedResponse->item(0)->getElementsByTagName("result");
    if ($result->length > 0) {
        $agencyCode = $result->item(0)->getElementsByTagName("agencyCode");
        if ($agencyCode->length > 0) {
            $agencyCode = $agencyCode->item(0)->nodeValue;
        } else {
            $agencyCode = "";
        }
        $agencyReference = $result->item(0)->getElementsByTagName("agencyReference");
        if ($agencyReference->length > 0) {
            $agencyReference = $agencyReference->item(0)->nodeValue;
        } else {
            $agencyReference = "";
        }
        $basketId = $result->item(0)->getElementsByTagName("basketId");
        if ($basketId->length > 0) {
            $basketId = $basketId->item(0)->nodeValue;
        } else {
            $basketId = "";
        }
        $cancelled = $result->item(0)->getElementsByTagName("cancelled");
        if ($cancelled->length > 0) {
            $cancelled = $cancelled->item(0)->nodeValue;
        } else {
            $cancelled = "";
        }
        $confirmed = $result->item(0)->getElementsByTagName("confirmed");
        if ($confirmed->length > 0) {
            $confirmed = $confirmed->item(0)->nodeValue;
        } else {
            $confirmed = "";
        }
        $opened = $result->item(0)->getElementsByTagName("opened");
        if ($opened->length > 0) {
            $opened = $opened->item(0)->nodeValue;
        } else {
            $opened = "";
        }
        $proFormaUrl = $result->item(0)->getElementsByTagName("proFormaUrl");
        if ($proFormaUrl->length > 0) {
            $proFormaUrl = $proFormaUrl->item(0)->nodeValue;
        } else {
            $proFormaUrl = "";
        }
        $status = $result->item(0)->getElementsByTagName("status");
        if ($status->length > 0) {
            $status = $status->item(0)->nodeValue;
        } else {
            $status = "";
        }
        $titular = $result->item(0)->getElementsByTagName("titular");
        if ($titular->length > 0) {
            $titular = $titular->item(0)->nodeValue;
        } else {
            $titular = "";
        }
        $from = $result->item(0)->getElementsByTagName("from");
        if ($from->length > 0) {
            $from = $from->item(0)->nodeValue;
        } else {
            $from = "";
        }
        $to = $result->item(0)->getElementsByTagName("to");
        if ($to->length > 0) {
            $to = $to->item(0)->nodeValue;
        } else {
            $to = "";
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
        $hotels = $result->item(0)->getElementsByTagName("hotels");
        if ($hotels->length > 0) {
            $cancellationDate = $hotels->item(0)->getElementsByTagName("cancellationDate");
            if ($cancellationDate->length > 0) {
                $cancellationDate = $cancellationDate->item(0)->nodeValue;
            } else {
                $cancellationDate = "";
            }
            $commision = $hotels->item(0)->getElementsByTagName("commision");
            if ($commision->length > 0) {
                $commision = $commision->item(0)->nodeValue;
            } else {
                $commision = "";
            }
            $contractId = $hotels->item(0)->getElementsByTagName("contractId");
            if ($contractId->length > 0) {
                $contractId = $contractId->item(0)->nodeValue;
            } else {
                $contractId = "";
            }
            $description = $hotels->item(0)->getElementsByTagName("description");
            if ($description->length > 0) {
                $description = $description->item(0)->nodeValue;
            } else {
                $description = "";
            }
            $okDate = $hotels->item(0)->getElementsByTagName("okDate");
            if ($okDate->length > 0) {
                $okDate = $okDate->item(0)->nodeValue;
            } else {
                $okDate = "";
            }
            $pointOfSaleId = $hotels->item(0)->getElementsByTagName("pointOfSaleId");
            if ($pointOfSaleId->length > 0) {
                $pointOfSaleId = $pointOfSaleId->item(0)->nodeValue;
            } else {
                $pointOfSaleId = "";
            }
            $positionInBasket = $hotels->item(0)->getElementsByTagName("positionInBasket");
            if ($positionInBasket->length > 0) {
                $positionInBasket = $positionInBasket->item(0)->nodeValue;
            } else {
                $positionInBasket = "";
            }
            $requestDate = $hotels->item(0)->getElementsByTagName("requestDate");
            if ($requestDate->length > 0) {
                $requestDate = $requestDate->item(0)->nodeValue;
            } else {
                $requestDate = "";
            }
            $serviceId = $hotels->item(0)->getElementsByTagName("serviceId");
            if ($serviceId->length > 0) {
                $serviceId = $serviceId->item(0)->nodeValue;
            } else {
                $serviceId = "";
            }
            $status = $hotels->item(0)->getElementsByTagName("status");
            if ($status->length > 0) {
                $status = $status->item(0)->nodeValue;
            } else {
                $status = "";
            }
            $userId = $hotels->item(0)->getElementsByTagName("userId");
            if ($userId->length > 0) {
                $userId = $userId->item(0)->nodeValue;
            } else {
                $userId = "";
            }
            $voucherUrl = $hotels->item(0)->getElementsByTagName("voucherUrl");
            if ($voucherUrl->length > 0) {
                $voucherUrl = $voucherUrl->item(0)->nodeValue;
            } else {
                $voucherUrl = "";
            }
            $checkin = $hotels->item(0)->getElementsByTagName("checkin");
            if ($checkin->length > 0) {
                $checkin = $checkin->item(0)->nodeValue;
            } else {
                $checkin = "";
            }
            $checkout = $hotels->item(0)->getElementsByTagName("checkout");
            if ($checkout->length > 0) {
                $checkout = $checkout->item(0)->nodeValue;
            } else {
                $checkout = "";
            }
            $establishmentId = $hotels->item(0)->getElementsByTagName("establishmentId");
            if ($establishmentId->length > 0) {
                $establishmentId = $establishmentId->item(0)->nodeValue;
            } else {
                $establishmentId = "";
            }
            $basket = $hotels->item(0)->getElementsByTagName("basket");
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
            $total = $hotels->item(0)->getElementsByTagName("total");
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
            $valuationLines = $hotels->item(0)->getElementsByTagName("valuationLines");
            if ($valuationLines->length > 0) {
                $valuationLines_code = $valuationLines->item(0)->getElementsByTagName("code");
                if ($valuationLines_code->length > 0) {
                    $valuationLines_code = $valuationLines_code->item(0)->nodeValue;
                } else {
                    $valuationLines_code = "";
                }
                $valuationLines_description = $valuationLines->item(0)->getElementsByTagName("description");
                if ($valuationLines_description->length > 0) {
                    $valuationLines_description = $valuationLines_description->item(0)->nodeValue;
                } else {
                    $valuationLines_description = "";
                }
                $valuationLines_priceType = $valuationLines->item(0)->getElementsByTagName("priceType");
                if ($valuationLines_priceType->length > 0) {
                    $valuationLines_priceType = $valuationLines_priceType->item(0)->nodeValue;
                } else {
                    $valuationLines_priceType = "";
                }
                $valuationLines_quantity = $valuationLines->item(0)->getElementsByTagName("quantity");
                if ($valuationLines_quantity->length > 0) {
                    $valuationLines_quantity = $valuationLines_quantity->item(0)->nodeValue;
                } else {
                    $valuationLines_quantity = "";
                }
                $valuationLines_status = $valuationLines->item(0)->getElementsByTagName("status");
                if ($valuationLines_status->length > 0) {
                    $valuationLines_status = $valuationLines_status->item(0)->nodeValue;
                } else {
                    $valuationLines_status = "";
                }
                $valuationLines_from = $valuationLines->item(0)->getElementsByTagName("from");
                if ($valuationLines_from->length > 0) {
                    $valuationLines_from = $valuationLines_from->item(0)->nodeValue;
                } else {
                    $valuationLines_from = "";
                }
                $valuationLines_to = $valuationLines->item(0)->getElementsByTagName("to");
                if ($valuationLines_to->length > 0) {
                    $valuationLines_to = $valuationLines_to->item(0)->nodeValue;
                } else {
                    $valuationLines_to = "";
                }
                $price = $valuationLines->item(0)->getElementsByTagName("price");
                if ($price->length > 0) {
                    $price_currencyCode = $price->item(0)->getElementsByTagName("currencyCode");
                    if ($price_currencyCode->length > 0) {
                        $price_currencyCode = $price_currencyCode->item(0)->nodeValue;
                    } else {
                        $price_currencyCode = "";
                    }
                    $price_value = $price->item(0)->getElementsByTagName("value");
                    if ($price_value->length > 0) {
                        $price_value = $price_value->item(0)->nodeValue;
                    } else {
                        $price_value = "";
                    }
                }
                $total = $valuationLines->item(0)->getElementsByTagName("total");
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
            }
            $occupations = $hotels->item(0)->getElementsByTagName("occupations");
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