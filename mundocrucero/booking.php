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
echo "COMECOU BOOKING<br/>";
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
$sql = "select value from settings where name='enablemundocruceros' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_mundocruceros = $affiliate_id;
} else {
    $affiliate_id_mundocruceros = 0;
}
echo $return;
echo "AFFIL: " . $affiliate_id_mundocruceros;
echo $return;
$sql = "select value from settings where name='mundocrucerosusername' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mundocrucerosusername = $row_settings['value'];
}
echo $return;
echo "USER: " . $mundocrucerosusername;
echo $return;
$sql = "select value from settings where name='mundocrucerospassword' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mundocrucerospassword = base64_decode($row_settings['value']);
}
echo $return;
echo $mundocrucerospassword;
echo $return;
$sql = "select value from settings where name='mundocrucerosServiceURL' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosServiceURL = $row['value'];
}
echo $return;
echo $mundocrucerosServiceURL;
echo $return;
$sql = "select value from settings where name='mundocrucerosSID' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosSID = $row['value'];
}
echo $return;
echo $mundocrucerosSID;
echo $return;
$sql = "select value from settings where name='mundocrucerosWebsite' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosWebsite = $row['value'];
}
echo $return;
echo $mundocrucerosWebsite;
echo $return;
$db->getDriver()
    ->getConnection()
    ->disconnect();
$raw = 'xml=<?xml version="1.0"?>
<request>
    <auth password="' . $mundocrucerospassword . '" username="' . $mundocrucerosusername . '" />
    <method action="getbooking" bookingid="851277" sitename="training.traveltek.net" />
</request>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $mundocrucerosServiceURL );
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-type: application/x-www-form-urlencoded",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
echo $response;
echo "</xmp>";
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.mundocruceros.php');
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
$results = $inputDoc->getElementsByTagName("results");
$booking = $results->item(0)->getElementsByTagName("booking");
if ($booking->length > 0) {
    $id = $booking->item(0)->getAttribute("id");
    $title = $booking->item(0)->getAttribute("title");
    $language = $booking->item(0)->getAttribute("language");
    $totaltoms = $booking->item(0)->getAttribute("totaltoms");
    $totalprice = $booking->item(0)->getAttribute("totalprice");
    $topperexchangerate = $booking->item(0)->getAttribute("topperexchangerate");
    $toppercurrency = $booking->item(0)->getAttribute("toppercurrency");
    $telephone = $booking->item(0)->getAttribute("telephone");
    $returndate = $booking->item(0)->getAttribute("returndate");
    $referrer = $booking->item(0)->getAttribute("referrer");
    $referencecode = $booking->item(0)->getAttribute("referencecode");
    $promocode = $booking->item(0)->getAttribute("promocode");
    $postcode = $booking->item(0)->getAttribute("postcode");
    $ownerid = $booking->item(0)->getAttribute("ownerid");
    $mobile = $booking->item(0)->getAttribute("mobile");
    $firstname = $booking->item(0)->getAttribute("firstname");
    $middlename = $booking->item(0)->getAttribute("middlename");
    $lastname = $booking->item(0)->getAttribute("lastname");
    $loyaltyprogramid = $booking->item(0)->getAttribute("loyaltyprogramid");
    $lowdepositduedate = $booking->item(0)->getAttribute("lowdepositduedate");
    $lowdepositamount = $booking->item(0)->getAttribute("lowdepositamount");
    $lowdeposit = $booking->item(0)->getAttribute("lowdeposit");
    $ipaddress = $booking->item(0)->getAttribute("ipaddress");
    $internalref = $booking->item(0)->getAttribute("internalref");
    $gender = $booking->item(0)->getAttribute("gender");
    $externaluser = $booking->item(0)->getAttribute("externaluser");
    $email = $booking->item(0)->getAttribute("email");
    $deposittotal = $booking->item(0)->getAttribute("deposittotal");
    $depositduedate = $booking->item(0)->getAttribute("depositduedate");
    $depositbooking = $booking->item(0)->getAttribute("depositbooking");
    $departdate = $booking->item(0)->getAttribute("departdate");
    $datebooked = $booking->item(0)->getAttribute("datebooked");
    $currency = $booking->item(0)->getAttribute("currency");
    $county = $booking->item(0)->getAttribute("county");
    $country = $booking->item(0)->getAttribute("country");
    $city = $booking->item(0)->getAttribute("city");
    $balancepaymentdate = $booking->item(0)->getAttribute("balancepaymentdate");
    $altphone = $booking->item(0)->getAttribute("altphone");
    $address1 = $booking->item(0)->getAttribute("address1");
    $address2 = $booking->item(0)->getAttribute("address2");
    $address3 = $booking->item(0)->getAttribute("address3");
    $address4 = $booking->item(0)->getAttribute("address4");
    $address5 = $booking->item(0)->getAttribute("address5");

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('booking');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'title' => $title,
            'language' => $language,
            'totaltoms' => $totaltoms,
            'totalprice' => $totalprice,
            'topperexchangerate' => $topperexchangerate,
            'toppercurrency' => $toppercurrency,
            'telephone' => $telephone,
            'returndate' => $returndate,
            'referrer' => $referrer,
            'referencecode' => $referencecode,
            'promocode' => $promocode,
            'postcode' => $postcode,
            'ownerid' => $ownerid,
            'mobile' => $mobile,
            'firstname' => $firstname,
            'middlename' => $middlename,
            'lastname' => $lastname,
            'loyaltyprogramid' => $loyaltyprogramid,
            'lowdepositduedate' => $lowdepositduedate,
            'lowdepositamount' => $lowdepositamount,
            'lowdeposit' => $lowdeposit,
            'ipaddress' => $ipaddress,
            'internalref' => $internalref,
            'gender' => $gender,
            'externaluser' => $externaluser,
            'email' => $email,
            'deposittotal' => $deposittotal,
            'depositduedate' => $depositduedate,
            'depositbooking' => $depositbooking,
            'departdate' => $departdate,
            'datebooked' => $datebooked,
            'currency' => $currency,
            'county' => $county,
            'country' => $country,
            'city' => $city,
            'balancepaymentdate' => $balancepaymentdate,
            'altphone' => $altphone,
            'address1' => $address1,
            'address2' => $address2,
            'address3' => $address3,
            'address4' => $address4,
            'address5' => $address5
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Erro 1: " . $e;
        echo $return;
    }

    $billing = $booking->item(0)->getElementsByTagName("billing");
    if ($billing->length > 0) {
        $title = $billing->item(0)->getAttribute("title");
        $bookingid = $billing->item(0)->getAttribute("bookingid");
        $postcode = $billing->item(0)->getAttribute("postcode");
        $firstname = $billing->item(0)->getAttribute("firstname");
        $lastname = $billing->item(0)->getAttribute("lastname");
        $county = $billing->item(0)->getAttribute("county");
        $country = $billing->item(0)->getAttribute("country");
        $city = $billing->item(0)->getAttribute("city");
        $address1 = $billing->item(0)->getAttribute("address1");
        $address2 = $billing->item(0)->getAttribute("address2");
        $uniquememberid = $billing->item(0)->getAttribute("uniquememberid");
        $startyear = $billing->item(0)->getAttribute("startyear");
        $startmonth = $billing->item(0)->getAttribute("startmonth");
        $nameoncard = $billing->item(0)->getAttribute("nameoncard");
        $issueno = $billing->item(0)->getAttribute("issueno");
        $expiryyear = $billing->item(0)->getAttribute("expiryyear");
        $expirymonth = $billing->item(0)->getAttribute("expirymonth");
        $cardtype = $billing->item(0)->getAttribute("cardtype");
        $cardnumber = $billing->item(0)->getAttribute("cardnumber");

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('billing_booking');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'title' => $title,
                'bookingid' => $bookingid,
                'postcode' => $postcode,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'county' => $county,
                'country' => $country,
                'city' => $city,
                'address1' => $address1,
                'address2' => $address2,
                'uniquememberid' => $uniquememberid,
                'startyear' => $startyear,
                'startmonth' => $startmonth,
                'nameoncard' => $nameoncard,
                'issueno' => $issueno,
                'expiryyear' => $expiryyear,
                'expirymonth' => $expirymonth,
                'cardtype' => $cardtype,
                'cardnumber' => $cardnumber
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Erro 2: " . $e;
            echo $return;
        }
    }

    $items = $booking->item(0)->getElementsByTagName("items");
    if ($items->length > 0) {
        $item = $items->item(0)->getElementsByTagName("item");
        if ($item->length > 0) {
            for ($i=0; $i < $item->length; $i++) { 
                $id = $item->item($i)->getAttribute("id");
                $type = $item->item($i)->getAttribute("type");
                $status = $item->item($i)->getAttribute("status");
                $reservation = $item->item($i)->getAttribute("reservation");
                $request = $item->item($i)->getAttribute("request");
                $price = $item->item($i)->getAttribute("price");
                $outterminal = $item->item($i)->getAttribute("outterminal");
                $outflightno = $item->item($i)->getAttribute("outflightno");
                $outflightclass = $item->item($i)->getAttribute("outflightclass");
                $outdeparttime = $item->item($i)->getAttribute("outdeparttime");
                $outdepartdate = $item->item($i)->getAttribute("outdepartdate");
                $outcarriercode = $item->item($i)->getAttribute("outcarriercode");
                $outcarrier = $item->item($i)->getAttribute("outcarrier");
                $outarrivaltime = $item->item($i)->getAttribute("outarrivaltime");
                $outarrivaldate = $item->item($i)->getAttribute("outarrivaldate");
                $originalprice = $item->item($i)->getAttribute("originalprice");
                $originalcurrency = $item->item($i)->getAttribute("originalcurrency");
                $oneway = $item->item($i)->getAttribute("oneway");
                $interminal = $item->item($i)->getAttribute("interminal");
                $inflightno = $item->item($i)->getAttribute("inflightno");
                $inflightclass = $item->item($i)->getAttribute("inflightclass");
                $indeparttime = $item->item($i)->getAttribute("indeparttime");
                $indepartdate = $item->item($i)->getAttribute("indepartdate");
                $indepartcode = $item->item($i)->getAttribute("indepartcode");
                $incarriercode = $item->item($i)->getAttribute("incarriercode");
                $incarrier = $item->item($i)->getAttribute("incarrier");
                $inarrivecode = $item->item($i)->getAttribute("inarrivecode");
                $inarrivaltime = $item->item($i)->getAttribute("inarrivaltime");
                $inarrivaldate = $item->item($i)->getAttribute("inarrivaldate");
                $immediateticket = $item->item($i)->getAttribute("immediateticket");
                $engine = $item->item($i)->getAttribute("engine");
                $destair = $item->item($i)->getAttribute("destair");
                $depair = $item->item($i)->getAttribute("depair");
                $carrier = $item->item($i)->getAttribute("carrier");

                //deposit
                $deposit = $item->item($i)->getElementsByTagName("deposit");
                if ($deposit->length > 0) {
                    $amount = $deposit->item(0)->getAttribute("amount");
                    $finalpaymentdate = $deposit->item(0)->getAttribute("finalpaymentdate");
                    $paidon = $deposit->item(0)->getAttribute("paidon");
                    $currency = $deposit->item(0)->getAttribute("currency");
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('items_booking');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'type' => $type,
                        'status' => $status,
                        'reservation' => $reservation,
                        'request' => $request,
                        'price' => $price,
                        'outterminal' => $outterminal,
                        'outflightno' => $outflightno,
                        'outflightclass' => $outflightclass,
                        'outdeparttime' => $outdeparttime,
                        'outdepartdate' => $outdepartdate,
                        'outcarriercode' => $outcarriercode,
                        'outcarrier' => $outcarrier,
                        'outarrivaltime' => $outarrivaltime,
                        'outarrivaldate' => $outarrivaldate,
                        'originalprice' => $originalprice,
                        'originalcurrency' => $originalcurrency,
                        'oneway' => $oneway,
                        'interminal' => $interminal,
                        'inflightno' => $inflightno,
                        'inflightclass' => $inflightclass,
                        'indeparttime' => $indeparttime,
                        'indepartdate' => $indepartdate,
                        'indepartcode' => $indepartcode,
                        'incarriercode' => $incarriercode,
                        'incarrier' => $incarrier,
                        'inarrivecode' => $inarrivecode,
                        'inarrivaltime' => $inarrivaltime,
                        'inarrivaldate' => $inarrivaldate,
                        'immediateticket' => $immediateticket,
                        'engine' => $engine,
                        'destair' => $destair,
                        'depair' => $depair,
                        'carrier' => $carrier,
                        'amount' => $amount,
                        'finalpaymentdate' => $finalpaymentdate,
                        'paidon' => $paidon,
                        'currency' => $currency
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Erro 3: " . $e;
                    echo $return;
                }

                //breakdown
                $breakdown = $item->item($i)->getElementsByTagName("breakdown");
                if ($breakdown->length > 0) {
                    $item2 = $breakdown->item(0)->getElementsByTagName("item");
                    if ($item2->length > 0) {
                        for ($iAux=0; $iAux < $item2->length; $iAux++) { 
                            $description = $item2->item($iAux)->getAttribute("description");
                            $quantity = $item2->item($iAux)->getAttribute("quantity");
                            $grossvalue = $item2->item($iAux)->getAttribute("grossvalue");

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('breakdown_booking');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'description' => $description,
                                    'quantity' => $quantity,
                                    'grossvalue' => $grossvalue
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Erro 4: " . $e;
                                echo $return;
                            }
                        }
                    }
                }

                $rooms = $item->item($i)->getElementsByTagName("rooms");
                if ($rooms->length > 0) {
                    $room = $rooms->item(0)->getElementsByTagName("room");
                    if ($room->length > 0) {
                        for ($iAux2=0; $iAux2 < $room->length; $iAux2++) { 
                            $roomno = $room->item($iAux2)->getAttribute("roomno");
                            $roomtype = $room->item($iAux2)->getAttribute("roomtype");
                            $roomstatus = $room->item($iAux2)->getAttribute("roomstatus");
                            $roomview = $room->item($iAux2)->getAttribute("roomview");
                            $basisinfo = $room->item($iAux2)->getAttribute("basisinfo");
                            $basiscode = $room->item($iAux2)->getAttribute("basiscode");
                            $adults = $room->item($iAux2)->getAttribute("adults");
                            $children = $room->item($iAux2)->getAttribute("children");
                            $infants = $room->item($iAux2)->getAttribute("infants");

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('rooms_booking');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'roomno' => $roomno,
                                    'roomtype' => $roomtype,
                                    'roomstatus' => $roomstatus,
                                    'roomview' => $roomview,
                                    'basisinfo' => $basisinfo,
                                    'basiscode' => $basiscode,
                                    'adults' => $adults,
                                    'children' => $children,
                                    'infants' => $infants
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Erro 5: " . $e;
                                echo $return;
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
echo '<br/>Done';
?>
