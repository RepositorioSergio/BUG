<?php
error_log("\r\nRestel - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$hotellist = "";
$sql = "select sid from xmlhotels_mrestel where hid=" . $hid;
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
        if ($hotellist == "") {
            $hotellist = $row->sid;
        } else {
            $hotellist .= "#" . $row->sid;
        }
    }
}
if ($hotellist != "") {
    $affiliate_id_restel = 0;
    $sql = "select value from settings where name='RestelHotUSAUsername' and affiliate_id=$affiliate_id_restel";
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RestelHotUSAUsername = $row_settings["value"];
    }
    $sql = "select value from settings where name='RestelHotUSApassword' and affiliate_id=$affiliate_id_restel";
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RestelHotUSApassword = base64_decode($row_settings["value"]);
    }
    $sql = "select value from settings where name='RestelHotUSAMarkupd' and affiliate_id=$affiliate_id_restel";
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RestelHotUSAMarkup = $row_settings["value"];
    } else {
        $RestelHotUSAMarkup = 0;
    }
    $sql = "select value from settings where name='RestelHotUSAAccessCode' and affiliate_id=$affiliate_id_restel";
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RestelHotUSAAccessCode = $row_settings["value"];
    }
    $sql = "select value from settings where name='RestelAffiliate' and affiliate_id=$affiliate_id_restel";
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RestelAffiliate = $row_settings["value"];
    }
    $sql = "select value from settings where name='RestelHotUSAServiceURL' and affiliate_id=$affiliate_id_restel";
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RestelHotUSAServiceURL = $row_settings["value"];
    }
    $sql = "select value from settings where name='RestelHotUSATimeout' and affiliate_id=$affiliate_id_restel";
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RestelHotUSATimeout = (int) $row_settings["value"];
    } else {
        $RestelHotUSATimeout = 0;
    }
    $sql = "select value from settings where name='RestelHotUSACustomerID' and affiliate_id=$affiliate_id_restel";
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RestelHotUSACustomerID = $row_settings["value"];
    }
    if ($nationality > 0) {
        $sql = "select iso_code_2 from countries where id=" . $nationality;
        $statement = $db->query($sql);
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings["iso_code_2"];
        } else {
            $sourceMarket = "";
        }
    } else {
        $sourceMarket = "";
    }
    $sql = "select city_xml39 from cities where id=" . $destination;
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $city_xml39 = $row_settings["city_xml39"];
        if ($city_xml39 != "") {
            $city_xml39 = explode(":", $city_xml39);
            $pais = $city_xml39[0];
            $provincia = $city_xml39[1];
        } else {
            $pais = "";
            $provincia = "";
        }
    } else {
        $pais = "";
        $provincia = "";
    }
    $translator = new Translator();
    if (file_exists("src/App/language/" . $lang . ".mo")) {
        $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
    }
    $xmlrequest = 'xml=<?xml version="1.0" encoding="UTF-8"?><peticion><tipo>110</tipo><nombre>Availability</nombre><agencia>BUG</agencia><parametros><hotel>' . $hotellist . '</hotel><pais>' . $pais . '</pais><provincia>' . $provincia . '</provincia><pais_cliente>' . $sourceMarket . '</pais_cliente><radio>9</radio><fechaentrada>' . strftime("%m/%d/%Y", $from) . '</fechaentrada><fechasalida>' . strftime("%m/%d/%Y", $to) . '</fechasalida><afiliacion>' . $RestelAffiliate . '</afiliacion><usuario>' . $RestelHotUSACustomerID . '</usuario><numhab1>1</numhab1><paxes1>' . (int) $adults . '-' . (int) $children . '</paxes1>';
    if ((int) $children > 0) {
        $xmlrequest .= '<edades1>';
        for ($z = 0; $z < (int) $children; $z ++) {
            if ($z > 0) {
                $xmlrequest .= ',';
            }
            $xmlrequest .= $children_ages[$z];
        }
        $xmlrequest .= '</edades1>';
    } else {
        $xmlrequest .= '<edades1></edades1>';
    }
    $xmlrequest .= '<idioma>2</idioma><duplicidad>1</duplicidad><comprimido>2</comprimido><informacion_hotel>0</informacion_hotel></parametros></peticion>';
    error_log("\r\n RAW - $xmlrequest\r\n", 3, "/srv/www/htdocs/error_log");
    if ($RestelHotUSATimeout == 0) {
        $RestelHotUSATimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $RestelHotUSAServiceURL . "listen_xml.jsp?codigousu=" . $RestelHotUSAUsername . "&clausu=" . $RestelHotUSApassword . "&afiliacio=" . $RestelAffiliate . "&secacc=" . $RestelHotUSAAccessCode);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlrequest);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $RestelHotUSATimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $RestelHotUSATimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'restel';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $xmlrequest;
    $nC ++;
}
?>