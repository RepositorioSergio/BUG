<?php
error_log("\r\nRIU - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
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
$db = new \Zend\Db\Adapter\Adapter($config);
unset($tmp);
$sfilter = array();
$failed = false;
$hotellist = "";
$sql = "select sid from xmlhotels_mriu where hid=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $result = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $hotellist .= '<ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">' . $row->sid . '</ns2:int>';
    }
}
if ($hotellist != "") {
    if (file_exists("src/App/language/" . $lang . ".mo")) {
        $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
    }
    $affiliate_id = 0;
    $branch_filter = "";
    $sql = "select value from settings where name='enableriu' and affiliate_id=$affiliate_id" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $affiliate_id_riu = $affiliate_id;
    } else {
        $affiliate_id_riu = 0;
    }
    if ((int) $nationality > 0) {
        $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $row_settings = $statement2->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings["iso_code_2"];
        }
    }
    if ($sourceMarket == "") {
        $sql = "select value from settings where name='riuPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_riu";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='riuMarkup' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $riuMarkup = (double) $row_settings['value'];
    } else {
        $riuMarkup = 0;
    }
    $sql = "select value from settings where name='riuCommission' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $riuCommission = (float) $row_settings['value'];
    } else {
        $riuCommission = 0;
    }
    $sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $riuLoginEmail = $row_settings['value'];
    }
    $sql = "select value from settings where name='riuTimeout' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $riuTimeout = (int) $row_settings['value'];
    } else {
        $riuTimeout = 0;
    }
    if ($riuTimeout == 0) {
        $riuTimeout = 120;
    }
    $sql = "select value from settings where name='riuPassword' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $riuPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='riuServiceURL' and affiliate_id=$affiliate_id_riu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $riuServiceURL = $row['value'];
    }
    $userpass = $riuLoginEmail . ':' . $riuPassword;
    $login = base64_encode($userpass);
    if ($failed == false) {
        if ($riuServiceURL != "" and $riuLoginEmail != "" and $riuPassword != "") {
            $nC = 0;
            $multiParallelSession = array();
            $raw2 = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><soap:Body><loginXML xmlns="http://services.enginexml.rumbonet.riu.com"><in0 xmlns="http://services.enginexml.rumbonet.riu.com"><acceso xmlns="http://dtos.common.rumbonet.riu.com">XML</acceso><codigoIdioma xmlns="http://dtos.common.rumbonet.riu.com">US</codigoIdioma><codigoPais xmlns="http://dtos.common.rumbonet.riu.com">E</codigoPais><ipCustomer xmlns="http://dtos.common.rumbonet.riu.com" xsi:nil="true" /><usuarioOpera xmlns="http://dtos.common.rumbonet.riu.com" xsi:nil="true" /><usuarioOperaId xmlns="http://dtos.common.rumbonet.riu.com">0</usuarioOperaId></in0></loginXML></soap:Body></soap:Envelope>';
            $client1 = new Client();
            $client1->setOptions(array(
                'timeout' => 100,
                'sslverifypeer' => false,
                'sslverifyhost' => false
            ));
            $client1->setHeaders(array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "Authorization: Basic " . $login,
                "Content-length: " . strlen($raw2)
            ));
            $client1->setUri($riuServiceURL);
            $client1->setMethod('POST');
            $client1->setRawBody($raw2);
            $response2 = $client1->send();
            if ($response2->isSuccess()) {
                $headers = $response2->getHeaders();
                $response2 = $response2->getBody();
            } else {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($client1->getUri());
                $logger->info($response2->getStatusCode() . " - " . $response2->getReasonPhrase());
                $failed = true;
            }
            
            $JSESSIONID = "";
            $x = $headers->toArray();
            $x = $x["Set-Cookie"];
            if (is_array($x)) {
                for ($z = 0; $z < count($x); $z ++) {
                    $xTmp = explode(";", $x[$z]);
                    $xTmp = $xTmp[0];
                    $xTmp = explode("=", $xTmp);
                    // error_log("\r\n0=" . $xTmp[0] . "\r\n", 3, "/srv/www/htdocs/error_log");
                    // error_log("\r\n1=" . $xTmp[1] . "\r\n", 3, "/srv/www/htdocs/error_log");
                    if ($xTmp[0] == 'JSESSIONID') {
                        $JSESSIONID = $xTmp[1];
                        break;
                    }
                }
            }
            if ($JSESSIONID == "") {
                error_log("\r\nUnable to retreive JSESSIONID for RIU\r\n", 3, "/srv/www/htdocs/error_log");
                error_log("\r\nHeaders:\r\n" . print_r($x, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
            } else {
                $childrens = 0;
                $infants = 0;
                for ($z = 0; $z < (int) $children; $z ++) {
                    if ($children_ages[$z] < 2) {
                        $infants ++;
                    } else {
                        $childrens ++;
                    }
                }
                $raw = '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Header/><soapenv:Body><ns6:HotelAvail xmlns:ns6="http://services.enginexml.rumbonet.riu.com"><ns6:in0><ns1:AdultsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . $adults . '</ns1:AdultsCount><ns1:ChildCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . $children . '</ns1:ChildCount><ns1:CountryCode xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . $sourceMarket . '</ns1:CountryCode><HotelList xmlns="http://dtos.enginexml.rumbonet.riu.com"><HotelsList>' . $hotellist . '</HotelsList></HotelList><ns1:InfantsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . $infants . '</ns1:InfantsCount><ns1:Language xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">US</ns1:Language><ns1:MealPlan xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/><ns1:promocode xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com"/><ns1:rateReference xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/><RoomList xmlns="http://dtos.enginexml.rumbonet.riu.com"><RoomConfig><RoomStayCandidate><AdultsCount>' . $adults . '</AdultsCount><ChildCount>' . $children . '</ChildCount>';
                if ($children > 0) {
                    $raw .= '<Ages>';
                    for ($z = 0; $z < $children; $z ++) {
                        $raw .= '<ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">' . $children_ages[$z] . '</ns2:int>';
                    }
                    $raw .= '</Ages>';
                }
                $raw .= '<InfantsCount>' . $infants . '</InfantsCount></RoomStayCandidate></RoomConfig></RoomList><ns1:RoomsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">1</ns1:RoomsCount><ns1:StayDateStart xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . strftime("%Y%m%d", $from) . '</ns1:StayDateStart><ns1:StayDateEnd xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">' . strftime("%Y%m%d", $to) . '</ns1:StayDateEnd></ns6:in0></ns6:HotelAvail></soapenv:Body></soapenv:Envelope>';
                error_log("\r\nRiu RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
                if ($riuTimeout == 0) {
                    $riuTimeout = 120;
                }
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Content-type: text/xml;charset=\"utf-8\"",
                    "Accept: text/xml",
                    "Cache-Control: no-cache",
                    "Pragma: no-cache",
                    "Content-length: " . strlen($raw),
                    "Cookie: JSESSIONID=" . $JSESSIONID
                ));
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                curl_setopt($ch, CURLOPT_URL, $riuServiceURL);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_VERBOSE, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $riuTimeout);
                curl_setopt($ch, CURLOPT_TIMEOUT, $riuTimeout);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($multiParallel, $ch);
                $requestsParallel[$nC] = 'riu';
                $channelsParallel[$nC] = $ch;
                $channelsParallelRequest[$nC] = $raw;
                $multiParallelSession[$nC] = $JSESSIONID;
                $nC ++;
            }
        }
    }
}
?>