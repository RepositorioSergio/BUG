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
$sql = "select value from settings where name='enablerts' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_rts = $affiliate_id;
} else {
    $affiliate_id_rts = 0;
}
$sql = "select value from settings where name='rtsID' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsID = $row_settings['value'];
}
$sql = "select value from settings where name='rtsPassword' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='rtsSiteCode' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsSiteCode = $row_settings['value'];
}
$sql = "select value from settings where name='rtsRequestType' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsRequestType = $row_settings['value'];
}
$sql = "select value from settings where name='rtsServiceURL' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $rtsServiceURL = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.rts.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rts="http://www.rts.co.kr/">
<soapenv:Header>
   <rts:BaseInfo>
      <!--Optional:-->
      <rts:SiteCode>CTM1X-00</rts:SiteCode>
      <!--Optional:-->
      <rts:Password>test1234</rts:Password>
      <!--Optional:-->
      <rts:RequestType>NetPartner</rts:RequestType>
   </rts:BaseInfo>
</soapenv:Header>
<soapenv:Body>
   <rts:CreateSystemBookingForGuestCount>
      <rts:SystemBookingInfoNetForGuestCount>
         <!--Optional:-->
         <rts:LanguageCode>AR</rts:LanguageCode>
         <!--Optional:-->
         <rts:ClientCurrencyCode>USD</rts:ClientCurrencyCode>
         <!--Optional:-->
         <rts:BookingCode></rts:BookingCode>
         <!--Optional:-->
         <rts:AdminCompCode>RTS32</rts:AdminCompCode>
         <!--Optional:-->
         <rts:GroupOrFit>F</rts:GroupOrFit>
         <!--Optional:-->
         <rts:NationalityCode>EU</rts:NationalityCode>
         <!--Optional:-->
         <rts:TravelerNationality>AR</rts:TravelerNationality>
         <!--Optional:-->
         <rts:SalesCompCode>CTM1X</rts:SalesCompCode>
         <!--Optional:-->
         <rts:SalesSiteCode>CTM1X-00</rts:SalesSiteCode>
         <rts:SalesUserNo>813054</rts:SalesUserNo>
         <!--Optional:-->
         <rts:SalesUserId></rts:SalesUserId>
         <!--Optional:-->
         <rts:SalesUserName></rts:SalesUserName>
         <!--Optional:-->
         <rts:SalesUserGender></rts:SalesUserGender>
         <!--Optional:-->
         <rts:SalesUserBirthday></rts:SalesUserBirthday>
         <!--Optional:-->
         <rts:SalesUserHandPhone></rts:SalesUserHandPhone>
         <!--Optional:-->
         <rts:SalesUserCompPhone></rts:SalesUserCompPhone>
         <!--Optional:-->
         <rts:SalesUserHomePhone></rts:SalesUserHomePhone>
         <!--Optional:-->
         <rts:SalesUserEamil></rts:SalesUserEamil>
         <rts:SalesEmpNo>813054</rts:SalesEmpNo>
         <!--Optional:-->
         <rts:SalesEmpName></rts:SalesEmpName>
         <!--Optional:-->
         <rts:SalesPayStatusCode></rts:SalesPayStatusCode>
         <!--Optional:-->
         <rts:NormalRemarks></rts:NormalRemarks>
         <!--Optional:-->
         <rts:SalesRemarks></rts:SalesRemarks>
         <!--Optional:-->
         <rts:AdminRemarks></rts:AdminRemarks>
         <rts:AdminBlockYn>false</rts:AdminBlockYn>
         <!--Optional:-->
         <rts:BookingPathCode>PATH01</rts:BookingPathCode>
         <rts:CardPaymentYn>false</rts:CardPaymentYn>
         <!--Optional:-->
         <rts:CardPaymentAmount></rts:CardPaymentAmount>
         <rts:LastWriterUno>813054</rts:LastWriterUno>
         <!--Optional:-->
         <rts:CustomerList>
            <!--Zero or more repetitions:-->
            <rts:CustomerInfo>
               <rts:No>1</rts:No>
               <!--Optional:-->
               <rts:Name>Daniel Vinagre</rts:Name>
               <!--Optional:-->
               <rts:LastName>Vinagre</rts:LastName>
               <!--Optional:-->
               <rts:FirstName>Daniel</rts:FirstName>
               <!--Optional:-->
               <rts:Gender>M</rts:Gender>
               <!--Optional:-->
               <rts:Age>30</rts:Age>
               <!--Optional:-->
               <rts:Country></rts:Country>
               <!--Optional:-->
               <rts:Birthday></rts:Birthday>
               <!--Optional:-->
               <rts:JuminNo></rts:JuminNo>
               <rts:LeadYn>false</rts:LeadYn>
               <!--Optional:-->
               <rts:PassportNo></rts:PassportNo>
               <!--Optional:-->
               <rts:PassportExpiry></rts:PassportExpiry>
            </rts:CustomerInfo>             
         </rts:CustomerList>
         <!--Optional:-->
         <rts:BookingHotelList>
            <!--Zero or more repetitions:-->
            <rts:BookingHotelInfo>
               <!--Optional:-->
               <rts:ItemCode>MIA0406</rts:ItemCode>
               <rts:ItemNo>8</rts:ItemNo>
               <!--Optional:-->
               <rts:AgentBookingReference></rts:AgentBookingReference>
               <!--Optional:-->
               <rts:BookerTypeCode>Partner</rts:BookerTypeCode>
               <!--Optional:-->
               <rts:BookingPathCode>PATH01</rts:BookingPathCode>
               <!--Optional:-->
               <rts:AppliedFromDate>2019-09-17</rts:AppliedFromDate>
               <!--Optional:-->
               <rts:AppliedToDate>2019-09-24</rts:AppliedToDate>
               <!--Optional:-->
               <rts:RoomTypeCode>20190917|20190924|W|235|128528|DBL.GR|ID_B2B_26|BB|DRBL|1~1~0||N@02~null~1A112AD092274DE155499749478802PAAR0000139011300100823d9b9:CEIJ.GB:double guest room:|ZMMJRBBZG|~BED AND BREAKFAST</rts:RoomTypeCode>
               <!--Optional:-->
               <rts:FreeBreakfastTypeName>BED AND BREAKFAST</rts:FreeBreakfastTypeName>
               <!--Optional:-->
               <rts:AddBreakfastTypeName></rts:AddBreakfastTypeName>
               <rts:VatSheetYn>false</rts:VatSheetYn>
               <!--Optional:-->
               <rts:GuestCountAndGuestList>
                  <!--Zero or more repetitions:-->
                  <rts:GuestCountAndGuestInfo>
                     <rts:RoomNo>1</rts:RoomNo>
                     <rts:AdultCount>1</rts:AdultCount>
                     <rts:ChildCount>0</rts:ChildCount>
                     <!--Optional:-->
                     <rts:GuestList>
                        <!--Zero or more repetitions:-->
                        <rts:GuestInfo>
                           <rts:GuestNo>1</rts:GuestNo>
                           <!--Optional:-->
                           <rts:AgeTypeCode></rts:AgeTypeCode>
                           <!--Optional:-->
                           <rts:ProductId></rts:ProductId>
                        </rts:GuestInfo>                         
                     </rts:GuestList>
                  </rts:GuestCountAndGuestInfo>
               </rts:GuestCountAndGuestList>
            </rts:BookingHotelInfo>
         </rts:BookingHotelList>
         <!--Optional:-->
         <rts:SellerMarkup>*1</rts:SellerMarkup>
      </rts:SystemBookingInfoNetForGuestCount>
   </rts:CreateSystemBookingForGuestCount>
</soapenv:Body>
</soapenv:Envelope>';

$soapUrl = 'http://devwsar.rts.net/WebServiceProjects/NetWebService/WsBookings.asmx';

$headers = array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "SOAPAction: http://www.rts.co.kr/CreateSystemBookingForGuestCount",
    "Content-length: " . strlen($raw)
);

$url = $soapUrl;

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
if ($response === false) {
    echo $return;
    echo "ERRO: " . $error;
    echo $return;
}
curl_close($ch);
/*
 * echo $return;
 * echo $response;
 * echo $return;
 */
$response = str_replace('&lt;', '<', $response);
$response = str_replace('&gt;', '>', $response);

echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.rts.php');
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
$CreateSystemBookingForGuestCountResponse = $Body->item(0)->getElementsByTagName("CreateSystemBookingForGuestCountResponse");
// CreateSystemBookingForGuestCountResult
$CreateSystemBookingForGuestCountResult = $CreateSystemBookingForGuestCountResponse->item(0)->getElementsByTagName("CreateSystemBookingForGuestCountResult");

// GetBookingDetailResponse
$GetBookingDetailResponse = $CreateSystemBookingForGuestCountResult->item(0)->getElementsByTagName("GetBookingDetailResponse");
$BookingResult = $GetBookingDetailResponse->item(0)->getElementsByTagName("BookingResult");
if ($BookingResult->length > 0) {
    $BookingCode = $BookingResult->item(0)->getElementsByTagName("BookingCode");
    if ($BookingCode->length > 0) {
        $BookingCode = $BookingCode->item(0)->nodeValue;
    } else {
        $BookingCode = "";
    }
    $NormalReceivedYn = $BookingResult->item(0)->getElementsByTagName("NormalReceivedYn");
    if ($BookinNormalReceivedYngCode->length > 0) {
        $NormalReceivedYn = $NormalReceivedYn->item(0)->nodeValue;
    } else {
        $NormalReceivedYn = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('booking');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'BookingCode' => $BookingCode,
            'NormalReceivedYn' => $NormalReceivedYn
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO BOOK: " . $e;
        echo $return;
    }
}

$GetBookingDetailResult = $GetBookingDetailResponse->item(0)->getElementsByTagName("GetBookingDetailResult");
if ($GetBookingDetailResult->length > 0) {
    $LanguageCode = $GetBookingDetailResult->item(0)->getElementsByTagName("LanguageCode");
    if ($LanguageCode->length > 0) {
        $LanguageCode = $LanguageCode->item(0)->nodeValue;
    } else {
        $LanguageCode = "";
    }
    $LanguageName = $GetBookingDetailResult->item(0)->getElementsByTagName("LanguageName");
    if ($LanguageName->length > 0) {
        $LanguageName = $LanguageName->item(0)->nodeValue;
    } else {
        $LanguageName = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('bookingdetail');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'LanguageCode' => $LanguageCode,
            'LanguageName' => $LanguageName,
            'BookingCode' => $BookingCode
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO BOOKdetail: " . $e;
        echo $return;
    }

    //BookingMaster
    $BookingMaster = $GetBookingDetailResult->item(0)->getElementsByTagName("BookingMaster");
    if ($BookingMaster->length > 0) {
        $BookingCode = $BookingMaster->item(0)->getElementsByTagName("BookingCode");
        if ($BookingCode->length > 0) {
            $BookingCode = $BookingCode->item(0)->nodeValue;
        } else {
            $BookingCode = "";
        }
        echo "PASSOU 1<br/>";
        $ProductTypeCode = $BookingMaster->item(0)->getElementsByTagName("ProductTypeCode");
        if ($ProductTypeCode->length > 0) {
            $ProductTypeCode = $ProductTypeCode->item(0)->nodeValue;
        } else {
            $ProductTypeCode = "";
        }
        $ProductTypeName = $BookingMaster->item(0)->getElementsByTagName("ProductTypeName");
        if ($ProductTypeName->length > 0) {
            $ProductTypeName = $ProductTypeName->item(0)->nodeValue;
        } else {
            $ProductTypeName = "";
        }
        $GroupOrFit = $BookingMaster->item(0)->getElementsByTagName("GroupOrFit");
        if ($GroupOrFit->length > 0) {
            $GroupOrFit = $GroupOrFit->item(0)->nodeValue;
        } else {
            $GroupOrFit = "";
        }
        $NationalityCode = $BookingMaster->item(0)->getElementsByTagName("NationalityCode");
        if ($NationalityCode->length > 0) {
            $NationalityCode = $NationalityCode->item(0)->nodeValue;
        } else {
            $NationalityCode = "";
        }
        $NationalityName = $BookingMaster->item(0)->getElementsByTagName("NationalityName");
        if ($NationalityName->length > 0) {
            $NationalityName = $NationalityName->item(0)->nodeValue;
        } else {
            $NationalityName = "";
        }
        echo "PASSOU 2<br/>";
        $BookingName = $BookingMaster->item(0)->getElementsByTagName("BookingName");
        if ($BookingName->length > 0) {
            $BookingName = $BookingName->item(0)->nodeValue;
        } else {
            $BookingName = "";
        }
        $SalesCompCode = $BookingMaster->item(0)->getElementsByTagName("SalesCompCode");
        if ($SalesCompCode->length > 0) {
            $SalesCompCode = $SalesCompCode->item(0)->nodeValue;
        } else {
            $SalesCompCode = "";
        }
        echo "PASSOU 3<br/>";
        $SalesCompName = $BookingMaster->item(0)->getElementsByTagName("SalesCompName");
        if ($SalesCompName->length > 0) {
            $SalesCompName = $SalesCompName->item(0)->nodeValue;
        } else {
            $SalesCompName = "";
        }
        $SalesSiteCode = $BookingMaster->item(0)->getElementsByTagName("SalesSiteCode");
        if ($SalesSiteCode->length > 0) {
            $SalesSiteCode = $SalesSiteCode->item(0)->nodeValue;
        } else {
            $SalesSiteCode = "";
        }
        $SalesSiteDomain = $BookingMaster->item(0)->getElementsByTagName("SalesSiteDomain");
        if ($SalesSiteDomain->length > 0) {
            $SalesSiteDomain = $SalesSiteDomain->item(0)->nodeValue;
        } else {
            $SalesSiteDomain = "";
        }
        $SalesUserNo = $BookingMaster->item(0)->getElementsByTagName("SalesUserNo");
        if ($SalesUserNo->length > 0) {
            $SalesUserNo = $SalesUserNo->item(0)->nodeValue;
        } else {
            $SalesUserNo = "";
        }
        echo "PASSOU 4<br/>";
        $SalesUserId = $BookingMaster->item(0)->getElementsByTagName("SalesUserId");
        if ($SalesUserId->length > 0) {
            $SalesUserId = $SalesUserId->item(0)->nodeValue;
        } else {
            $SalesUserId = "";
        }
        $SalesUserName = $BookingMaster->item(0)->getElementsByTagName("SalesUserName");
        if ($SalesUserName->length > 0) {
            $SalesUserName = $SalesUserName->item(0)->nodeValue;
        } else {
            $SalesUserName = "";
        }
        $SalesUserGender = $BookingMaster->item(0)->getElementsByTagName("SalesUserGender");
        if ($SalesUserGender->length > 0) {
            $SalesUserGender = $SalesUserGender->item(0)->nodeValue;
        } else {
            $SalesUserGender = "";
        }
        $SalesUserJuminNo = $BookingMaster->item(0)->getElementsByTagName("SalesUserJuminNo");
        if ($SalesUserJuminNo->length > 0) {
            $SalesUserJuminNo = $SalesUserJuminNo->item(0)->nodeValue;
        } else {
            $SalesUserJuminNo = "";
        }
        $SalesUserBirthday = $BookingMaster->item(0)->getElementsByTagName("SalesUserBirthday");
        if ($SalesUserBirthday->length > 0) {
            $SalesUserBirthday = $SalesUserBirthday->item(0)->nodeValue;
        } else {
            $SalesUserBirthday = "";
        }
        echo "PASSOU 5<br/>";
        $SalesUserLastName = $BookingMaster->item(0)->getElementsByTagName("SalesUserLastName");
        if ($SalesUserLastName->length > 0) {
            $SalesUserLastName = $SalesUserLastName->item(0)->nodeValue;
        } else {
            $SalesUserLastName = "";
        }
        $SalesUserFirstName = $BookingMaster->item(0)->getElementsByTagName("SalesUserFirstName");
        if ($SalesUserFirstName->length > 0) {
            $SalesUserFirstName = $SalesUserFirstName->item(0)->nodeValue;
        } else {
            $SalesUserFirstName = "";
        }
        $SalesUserHandPhone = $BookingMaster->item(0)->getElementsByTagName("SalesUserHandPhone");
        if ($SalesUserHandPhone->length > 0) {
            $SalesUserHandPhone = $SalesUserHandPhone->item(0)->nodeValue;
        } else {
            $SalesUserHandPhone = "";
        }
        $SalesUserCompPhone = $BookingMaster->item(0)->getElementsByTagName("SalesUserCompPhone");
        if ($SalesUserCompPhone->length > 0) {
            $SalesUserCompPhone = $SalesUserCompPhone->item(0)->nodeValue;
        } else {
            $SalesUserCompPhone = "";
        }
        $SalesUserHomePhone = $BookingMaster->item(0)->getElementsByTagName("SalesUserHomePhone");
        if ($SalesUserHomePhone->length > 0) {
            $SalesUserHomePhone = $SalesUserHomePhone->item(0)->nodeValue;
        } else {
            $SalesUserHomePhone = "";
        }
        $SalesUserEmail = $BookingMaster->item(0)->getElementsByTagName("SalesUserEmail");
        if ($SalesUserEmail->length > 0) {
            $SalesUserEmail = $SalesUserEmail->item(0)->nodeValue;
        } else {
            $SalesUserEmail = "";
        }
        $SalesEmpUno = $BookingMaster->item(0)->getElementsByTagName("SalesEmpUno");
        if ($SalesEmpUno->length > 0) {
            $SalesEmpUno = $SalesEmpUno->item(0)->nodeValue;
        } else {
            $SalesEmpUno = "";
        }
        echo "PASSOU 6<br/>";
        $SalesEmpId = $BookingMaster->item(0)->getElementsByTagName("SalesEmpId");
        if ($SalesEmpId->length > 0) {
            $SalesEmpId = $SalesEmpId->item(0)->nodeValue;
        } else {
            $SalesEmpId = "";
        }
        $SalesEmpName = $BookingMaster->item(0)->getElementsByTagName("SalesEmpName");
        if ($SalesEmpName->length > 0) {
            $SalesEmpName = $SalesEmpName->item(0)->nodeValue;
        } else {
            $SalesEmpName = "";
        }
        $SalesEmpPosition = $BookingMaster->item(0)->getElementsByTagName("SalesEmpPosition");
        if ($SalesEmpPosition->length > 0) {
            $SalesEmpPosition = $SalesEmpPosition->item(0)->nodeValue;
        } else {
            $SalesEmpPosition = "";
        }
        $SalesEmpCompPhone = $BookingMaster->item(0)->getElementsByTagName("SalesEmpCompPhone");
        if ($SalesEmpCompPhone->length > 0) {
            $SalesEmpCompPhone = $SalesEmpCompPhone->item(0)->nodeValue;
        } else {
            $SalesEmpCompPhone = "";
        }
        $SalesEmpEmail = $BookingMaster->item(0)->getElementsByTagName("SalesEmpEmail");
        if ($SalesEmpEmail->length > 0) {
            $SalesEmpEmail = $SalesEmpEmail->item(0)->nodeValue;
        } else {
            $SalesEmpEmail = "";
        }
        $SalesmanUno = $BookingMaster->item(0)->getElementsByTagName("SalesmanUno");
        if ($SalesmanUno->length > 0) {
            $SalesmanUno = $SalesmanUno->item(0)->nodeValue;
        } else {
            $SalesmanUno = "";
        }
        $SalesmanId = $BookingMaster->item(0)->getElementsByTagName("SalesmanId");
        if ($SalesmanId->length > 0) {
            $SalesmanId = $SalesmanId->item(0)->nodeValue;
        } else {
            $SalesmanId = "";
        }
        $SalesmanName = $BookingMaster->item(0)->getElementsByTagName("SalesmanName");
        if ($SalesmanName->length > 0) {
            $SalesmanName = $SalesmanName->item(0)->nodeValue;
        } else {
            $SalesmanName = "";
        }
        $SalesmanId = $BookingMaster->item(0)->getElementsByTagName("SalesmanId");
        if ($SalesmanId->length > 0) {
            $SalesmanId = $SalesmanId->item(0)->nodeValue;
        } else {
            $SalesmanId = "";
        }
        $SalesmanName = $BookingMaster->item(0)->getElementsByTagName("SalesmanName");
        if ($SalesmanName->length > 0) {
            $SalesmanName = $SalesmanName->item(0)->nodeValue;
        } else {
            $SalesmanName = "";
        }
        $SalesmanPosition = $BookingMaster->item(0)->getElementsByTagName("SalesmanPosition");
        if ($SalesmanPosition->length > 0) {
            $SalesmanPosition = $SalesmanPosition->item(0)->nodeValue;
        } else {
            $SalesmanPosition = "";
        }
        echo "PASSOU 7<br/>";
        $SalesmanHandPhone = $BookingMaster->item(0)->getElementsByTagName("SalesmanHandPhone");
        if ($SalesmanHandPhone->length > 0) {
            $SalesmanHandPhone = $SalesmanHandPhone->item(0)->nodeValue;
        } else {
            $SalesmanHandPhone = "";
        }
        $SalesmanCompPhone = $BookingMaster->item(0)->getElementsByTagName("SalesmanCompPhone");
        if ($SalesmanCompPhone->length > 0) {
            $SalesmanCompPhone = $SalesmanCompPhone->item(0)->nodeValue;
        } else {
            $SalesmanCompPhone = "";
        }
        $SalesmanEmail = $BookingMaster->item(0)->getElementsByTagName("SalesmanEmail");
        if ($SalesmanEmail->length > 0) {
            $SalesmanEmail = $SalesmanEmail->item(0)->nodeValue;
        } else {
            $SalesmanEmail = "";
        }
        $OperatorUno = $BookingMaster->item(0)->getElementsByTagName("OperatorUno");
        if ($OperatorUno->length > 0) {
            $OperatorUno = $OperatorUno->item(0)->nodeValue;
        } else {
            $OperatorUno = "";
        }
        $OperatorId = $BookingMaster->item(0)->getElementsByTagName("OperatorId");
        if ($OperatorId->length > 0) {
            $OperatorId = $OperatorId->item(0)->nodeValue;
        } else {
            $OperatorId = "";
        }
        $OperatorName = $BookingMaster->item(0)->getElementsByTagName("OperatorName");
        if ($OperatorName->length > 0) {
            $OperatorName = $OperatorName->item(0)->nodeValue;
        } else {
            $OperatorName = "";
        }
        $OperatorPosition = $BookingMaster->item(0)->getElementsByTagName("OperatorPosition");
        if ($OperatorPosition->length > 0) {
            $OperatorPosition = $OperatorPosition->item(0)->nodeValue;
        } else {
            $OperatorPosition = "";
        }
        echo "PASSOU 8<br/>";
        $OperatorCompPhone = $BookingMaster->item(0)->getElementsByTagName("OperatorCompPhone");
        if ($OperatorCompPhone->length > 0) {
            $OperatorCompPhone = $OperatorCompPhone->item(0)->nodeValue;
        } else {
            $OperatorCompPhone = "";
        }
        $OperatorEmail = $BookingMaster->item(0)->getElementsByTagName("OperatorEmail");
        if ($OperatorEmail->length > 0) {
            $OperatorEmail = $OperatorEmail->item(0)->nodeValue;
        } else {
            $OperatorEmail = "";
        }
        $BookingStatusCode = $BookingMaster->item(0)->getElementsByTagName("BookingStatusCode");
        if ($BookingStatusCode->length > 0) {
            $BookingStatusCode = $BookingStatusCode->item(0)->nodeValue;
        } else {
            $BookingStatusCode = "";
        }
        $BookingStatusName = $BookingMaster->item(0)->getElementsByTagName("BookingStatusName");
        if ($BookingStatusName->length > 0) {
            $BookingStatusName = $BookingStatusName->item(0)->nodeValue;
        } else {
            $BookingStatusName = "";
        }
        $TicketStatusCode = $BookingMaster->item(0)->getElementsByTagName("TicketStatusCode");
        if ($TicketStatusCode->length > 0) {
            $TicketStatusCode = $TicketStatusCode->item(0)->nodeValue;
        } else {
            $TicketStatusCode = "";
        }
        $TicketStatusName = $BookingMaster->item(0)->getElementsByTagName("TicketStatusName");
        if ($TicketStatusName->length > 0) {
            $TicketStatusName = $TicketStatusName->item(0)->nodeValue;
        } else {
            $TicketStatusName = "";
        }
        echo "PASSOU 9<br/>";
        $SalesPayStatusCode = $BookingMaster->item(0)->getElementsByTagName("SalesPayStatusCode");
        if ($SalesPayStatusCode->length > 0) {
            $SalesPayStatusCode = $SalesPayStatusCode->item(0)->nodeValue;
        } else {
            $SalesPayStatusCode = "";
        }
        $SalesPayStatusName = $BookingMaster->item(0)->getElementsByTagName("SalesPayStatusName");
        if ($SalesPayStatusName->length > 0) {
            $SalesPayStatusName = $SalesPayStatusName->item(0)->nodeValue;
        } else {
            $SalesPayStatusName = "";
        }
        $InsidePayStatusCode = $BookingMaster->item(0)->getElementsByTagName("InsidePayStatusCode");
        if ($InsidePayStatusCode->length > 0) {
            $InsidePayStatusCode = $InsidePayStatusCode->item(0)->nodeValue;
        } else {
            $InsidePayStatusCode = "";
        }
        $InsidePayStatusName = $BookingMaster->item(0)->getElementsByTagName("InsidePayStatusName");
        if ($InsidePayStatusName->length > 0) {
            $InsidePayStatusName = $InsidePayStatusName->item(0)->nodeValue;
        } else {
            $InsidePayStatusName = "";
        }
        $FullCancelReasonCode = $BookingMaster->item(0)->getElementsByTagName("FullCancelReasonCode");
        if ($FullCancelReasonCode->length > 0) {
            $FullCancelReasonCode = $FullCancelReasonCode->item(0)->nodeValue;
        } else {
            $FullCancelReasonCode = "";
        }
        $FullCancelReasonName = $BookingMaster->item(0)->getElementsByTagName("FullCancelReasonName");
        if ($FullCancelReasonName->length > 0) {
            $FullCancelReasonName = $FullCancelReasonName->item(0)->nodeValue;
        } else {
            $FullCancelReasonName = "";
        }
        $AccountCloseYn = $BookingMaster->item(0)->getElementsByTagName("AccountCloseYn");
        if ($AccountCloseYn->length > 0) {
            $AccountCloseYn = $AccountCloseYn->item(0)->nodeValue;
        } else {
            $AccountCloseYn = "";
        }
        $AccountCloseTime = $BookingMaster->item(0)->getElementsByTagName("AccountCloseTime");
        if ($AccountCloseTime->length > 0) {
            $AccountCloseTime = $AccountCloseTime->item(0)->nodeValue;
        } else {
            $AccountCloseTime = "";
        }
        echo "PASSOU 10<br/>";
        $NormalRemarks = $BookingMaster->item(0)->getElementsByTagName("NormalRemarks");
        if ($NormalRemarks->length > 0) {
            $NormalRemarks = $NormalRemarks->item(0)->nodeValue;
        } else {
            $NormalRemarks = "";
        }
        $SalesRemarks = $BookingMaster->item(0)->getElementsByTagName("SalesRemarks");
        if ($SalesRemarks->length > 0) {
            $SalesRemarks = $SalesRemarks->item(0)->nodeValue;
        } else {
            $SalesRemarks = "";
        }
        $BookingTime = $BookingMaster->item(0)->getElementsByTagName("BookingTime");
        if ($BookingTime->length > 0) {
            $BookingTime = $BookingTime->item(0)->nodeValue;
        } else {
            $BookingTime = "";
        }
        $DepartureDate = $BookingMaster->item(0)->getElementsByTagName("DepartureDate");
        if ($DepartureDate->length > 0) {
            $DepartureDate = $DepartureDate->item(0)->nodeValue;
        } else {
            $DepartureDate = "";
        }
        $DepartureWeekday = $BookingMaster->item(0)->getElementsByTagName("DepartureWeekday");
        if ($DepartureWeekday->length > 0) {
            $DepartureWeekday = $DepartureWeekday->item(0)->nodeValue;
        } else {
            $DepartureWeekday = "";
        }
        $DepartureLeftDays = $BookingMaster->item(0)->getElementsByTagName("DepartureLeftDays");
        if ($DepartureLeftDays->length > 0) {
            $DepartureLeftDays = $DepartureLeftDays->item(0)->nodeValue;
        } else {
            $DepartureLeftDays = "";
        }
        echo "PASSOU 11<br/>";
        $CancelDeadLine = $BookingMaster->item(0)->getElementsByTagName("CancelDeadLine");
        if ($CancelDeadLine->length > 0) {
            $CancelDeadLine = $CancelDeadLine->item(0)->nodeValue;
        } else {
            $CancelDeadLine = "";
        }
        $DeadLineWeekday = $BookingMaster->item(0)->getElementsByTagName("DeadLineWeekday");
        if ($DeadLineWeekday->length > 0) {
            $DeadLineWeekday = $DeadLineWeekday->item(0)->nodeValue;
        } else {
            $DeadLineWeekday = "";
        }
        $DeadLineLeftDays = $BookingMaster->item(0)->getElementsByTagName("DeadLineLeftDays");
        if ($DeadLineLeftDays->length > 0) {
            $DeadLineLeftDays = $DeadLineLeftDays->item(0)->nodeValue;
        } else {
            $DeadLineLeftDays = "";
        }
        $LastWriteTime = $BookingMaster->item(0)->getElementsByTagName("LastWriteTime");
        if ($LastWriteTime->length > 0) {
            $LastWriteTime = $LastWriteTime->item(0)->nodeValue;
        } else {
            $LastWriteTime = "";
        }
        echo "PASSOU 12<br/>";
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('bookingmaster');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'BookingCode' => $BookingCode,
                'ProductTypeCode' => $ProductTypeCode,
                'ProductTypeName' => $ProductTypeName,
                'GroupOrFit' => $GroupOrFit,
                'NationalityCode' => $NationalityCode,
                'NationalityName' => $NationalityName,
                'BookingName' => $BookingName,
                'SalesCompCode' => $SalesCompCode,
                'SalesCompName' => $SalesCompName,
                'SalesSiteCode' => $SalesSiteCode,
                'SalesSiteDomain' => $SalesSiteDomain,
                'SalesUserNo' => $SalesUserNo,
                'SalesUserId' => $SalesUserId,
                'SalesUserName' => $SalesUserName,
                'SalesUserGender' => $SalesUserGender,
                'SalesUserJuminNo' => $SalesUserJuminNo,
                'SalesUserBirthday' => $SalesUserBirthday,
                'SalesUserLastName' => $SalesUserLastName,
                'SalesUserFirstName' => $SalesUserFirstName,
                'SalesUserHandPhone' => $SalesUserHandPhone,
                'SalesUserCompPhone' => $SalesUserCompPhone,
                'SalesUserHomePhone' => $SalesUserHomePhone,
                'SalesUserEmail' => $SalesUserEmail,
                'SalesEmpUno' => $SalesEmpUno,
                'SalesEmpId' => $SalesEmpId,
                'SalesEmpName' => $SalesEmpName,
                'SalesEmpPosition' => $SalesEmpPosition,
                'SalesEmpCompPhone' => $SalesEmpCompPhone,
                'SalesEmpEmail' => $SalesEmpEmail,
                'SalesmanUno' => $SalesmanUno,
                'SalesmanId' => $SalesmanId,
                'SalesmanName' => $SalesmanName,
                'SalesmanPosition' => $SalesmanPosition,
                'SalesmanHandPhone' => $SalesmanHandPhone,
                'SalesmanCompPhone' => $SalesmanCompPhone,
                'SalesmanEmail' => $SalesmanEmail,
                'OperatorUno' => $OperatorUno,
                'OperatorId' => $OperatorId,
                'OperatorName' => $OperatorName,
                'OperatorPosition' => $OperatorPosition,
                'OperatorCompPhone' => $OperatorCompPhone,
                'OperatorEmail' => $OperatorEmail,
                'BookingStatusCode' => $BookingStatusCode,
                'BookingStatusName' => $BookingStatusName,
                'TicketStatusCode' => $TicketStatusCode,
                'TicketStatusName' => $TicketStatusName,
                'SalesPayStatusCode' => $SalesPayStatusCode,
                'SalesPayStatusName' => $SalesPayStatusName,
                'InsidePayStatusCode' => $InsidePayStatusCode,
                'InsidePayStatusName' => $InsidePayStatusName,
                'FullCancelReasonCode' => $FullCancelReasonCode,
                'FullCancelReasonName' => $FullCancelReasonName,
                'AccountCloseYn' => $AccountCloseYn,
                'AccountCloseTime' => $AccountCloseTime,
                'NormalRemarks' => $NormalRemarks,
                'SalesRemarks' => $SalesRemarks,
                'BookingTime' => $BookingTime,
                'DepartureDate' => $DepartureDate,
                'DepartureWeekday' => $DepartureWeekday,
                'DepartureLeftDays' => $DepartureLeftDays,
                'CancelDeadLine' => $CancelDeadLine,
                'DeadLineWeekday' => $DeadLineWeekday,
                'DeadLineLeftDays' => $DeadLineLeftDays,
                'LastWriteTime' => $LastWriteTime,
                'No' => $No,
                'Name' => $Name,
                'LastName' => $LastName,
                'FirstName' => $FirstName,
                'Gender' => $Gender,
                'Age' => $Age,
                'Birthday' => $Birthday,
                'JuminNo' => $JuminNo,
                'PassportNo' => $PassportNo,
                'PassportExpiryDate' => $PassportExpiryDate
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO CC: " . $e;
            echo $return;
        }
            echo "PASSOU 13<br/>";
        //CustomerList
        $CustomerList = $BookingMaster->item(0)->getElementsByTagName("CustomerList");
        if ($CustomerList->length > 0) {
            $CustomerInfo = $CustomerList->item(0)->getElementsByTagName("CustomerInfo");
            if ($CustomerInfo->length > 0) {
                for ($k = 0; $k < $CustomerList->length; $k++) {
                    $No = $CustomerInfo->item($k)->getElementsByTagName("No");
                    if ($No->length > 0) {
                        $No = $No->item(0)->nodeValue;
                    } else {
                        $No = "";
                    }
                    $Name = $CustomerInfo->item($k)->getElementsByTagName("Name");
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    $LastName = $CustomerInfo->item($k)->getElementsByTagName("LastName");
                    if ($LastName->length > 0) {
                        $LastName = $LastName->item(0)->nodeValue;
                    } else {
                        $LastName = "";
                    }
                    $FirstName = $CustomerInfo->item($k)->getElementsByTagName("FirstName");
                    if ($FirstName->length > 0) {
                        $FirstName = $FirstName->item(0)->nodeValue;
                    } else {
                        $FirstName = "";
                    }
                    $Gender = $CustomerInfo->item($k)->getElementsByTagName("Gender");
                    if ($Gender->length > 0) {
                        $Gender = $Gender->item(0)->nodeValue;
                    } else {
                        $Gender = "";
                    }
                    $Age = $CustomerInfo->item($k)->getElementsByTagName("Age");
                    if ($Age->length > 0) {
                        $Age = $Age->item(0)->nodeValue;
                    } else {
                        $Age = "";
                    }
                    $Birthday = $CustomerInfo->item($k)->getElementsByTagName("Birthday");
                    if ($Birthday->length > 0) {
                        $Birthday = $Birthday->item(0)->nodeValue;
                    } else {
                        $Birthday = "";
                    }
                    $JuminNo = $CustomerInfo->item($k)->getElementsByTagName("JuminNo");
                    if ($JuminNo->length > 0) {
                        $JuminNo = $JuminNo->item(0)->nodeValue;
                    } else {
                        $JuminNo = "";
                    }
                    $PassportNo = $CustomerInfo->item($k)->getElementsByTagName("PassportNo");
                    if ($PassportNo->length > 0) {
                        $PassportNo = $PassportNo->item(0)->nodeValue;
                    } else {
                        $PassportNo = "";
                    }
                    $PassportExpiryDate = $CustomerInfo->item($k)->getElementsByTagName("PassportExpiryDate");
                    if ($PassportExpiryDate->length > 0) {
                        $PassportExpiryDate = $PassportExpiryDate->item(0)->nodeValue;
                    } else {
                        $PassportExpiryDate = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('bookingmaster_CustomerInfo');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'BookingCode' => $BookingCode,
                            'No' => $No,
                            'Name' => $Name,
                            'LastName' => $LastName,
                            'FirstName' => $FirstName,
                            'Gender' => $Gender,
                            'Age' => $Age,
                            'Birthday' => $Birthday,
                            'JuminNo' => $JuminNo,
                            'PassportNo' => $PassportNo,
                            'PassportExpiryDate' => $PassportExpiryDate
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO CUST: " . $e;
                        echo $return;
                    }

                }        
            }
        }
    }
    echo "PASSOU 14<br/>";
    //BookingItemList
    $BookingItemList = $GetBookingDetailResult->item(0)->getElementsByTagName("BookingItemList");
    if ($BookingItemList->length > 0) {
        $BookingItemInfo = $BookingItemList->item(0)->getElementsByTagName("BookingItemInfo");
        if ($BookingItemInfo->length > 0) {
            $ItemNo = $BookingItemInfo->item(0)->getElementsByTagName("ItemNo");
            if ($ItemNo->length > 0) {
                $ItemNo = $ItemNo->item(0)->nodeValue;
            } else {
                $ItemNo = "";
            }
            echo "PASSOU 15<br/>";
            $ItemTypeCode = $BookingItemInfo->item(0)->getElementsByTagName("ItemTypeCode");
            if ($ItemTypeCode->length > 0) {
                $ItemTypeCode = $ItemTypeCode->item(0)->nodeValue;
            } else {
                $ItemTypeCode = "";
            }
            $NormalReceiveYn = $BookingItemInfo->item(0)->getElementsByTagName("NormalReceiveYn");
            if ($NormalReceiveYn->length > 0) {
                $NormalReceiveYn = $NormalReceiveYn->item(0)->nodeValue;
            } else {
                $NormalReceiveYn = "";
            }
            $BookingReference = $BookingItemInfo->item(0)->getElementsByTagName("BookingReference");
            if ($BookingReference->length > 0) {
                $BookingReference = $BookingReference->item(0)->nodeValue;
            } else {
                $BookingReference = "";
            }
            $ItemReference = $BookingItemInfo->item(0)->getElementsByTagName("ItemReference");
            if ($ItemReference->length > 0) {
                $ItemReference = $ItemReference->item(0)->nodeValue;
            } else {
                $ItemReference = "";
            }
            $CountryCode = $BookingItemInfo->item(0)->getElementsByTagName("CountryCode");
            if ($CountryCode->length > 0) {
                $CountryCode = $CountryCode->item(0)->nodeValue;
            } else {
                $CountryCode = "";
            }
            $CountryEname = $BookingItemInfo->item(0)->getElementsByTagName("CountryEname");
            if ($CountryEname->length > 0) {
                $CountryEname = $CountryEname->item(0)->nodeValue;
            } else {
                $CountryEname = "";
            }
            $CountryName = $BookingItemInfo->item(0)->getElementsByTagName("CountryName");
            if ($CountryName->length > 0) {
                $CountryName = $CountryName->item(0)->nodeValue;
            } else {
                $CountryName = "";
            }
            $CityCode = $BookingItemInfo->item(0)->getElementsByTagName("CityCode");
            if ($CityCode->length > 0) {
                $CityCode = $CityCode->item(0)->nodeValue;
            } else {
                $CityCode = "";
            }
            $CityEname = $BookingItemInfo->item(0)->getElementsByTagName("CityEname");
            if ($CityEname->length > 0) {
                $CityEname = $CityEname->item(0)->nodeValue;
            } else {
                $CityEname = "";
            }
            $CtiyName = $BookingItemInfo->item(0)->getElementsByTagName("CtiyName");
            if ($CtiyName->length > 0) {
                $CtiyName = $CtiyName->item(0)->nodeValue;
            } else {
                $CtiyName = "";
            }
            $ItemCode = $BookingItemInfo->item(0)->getElementsByTagName("ItemCode");
            if ($ItemCode->length > 0) {
                $ItemCode = $ItemCode->item(0)->nodeValue;
            } else {
                $ItemCode = "";
            }
            $ItemName = $BookingItemInfo->item(0)->getElementsByTagName("ItemName");
            if ($ItemName->length > 0) {
                $ItemName = $ItemName->item(0)->nodeValue;
            } else {
                $ItemName = "";
            }
            $BookerTypeCode = $BookingItemInfo->item(0)->getElementsByTagName("BookerTypeCode");
            if ($BookerTypeCode->length > 0) {
                $BookerTypeCode = $BookerTypeCode->item(0)->nodeValue;
            } else {
                $BookerTypeCode = "";
            }
            $BookerTypeName = $BookingItemInfo->item(0)->getElementsByTagName("BookerTypeName");
            if ($BookerTypeName->length > 0) {
                $BookerTypeName = $BookerTypeName->item(0)->nodeValue;
            } else {
                $BookerTypeName = "";
            }
            $BookingPathCode = $BookingItemInfo->item(0)->getElementsByTagName("BookingPathCode");
            if ($BookingPathCode->length > 0) {
                $BookingPathCode = $BookingPathCode->item(0)->nodeValue;
            } else {
                $BookingPathCode = "";
            }
            $BookingPathName = $BookingItemInfo->item(0)->getElementsByTagName("BookingPathName");
            if ($BookingPathName->length > 0) {
                $BookingPathName = $BookingPathName->item(0)->nodeValue;
            } else {
                $BookingPathName = "";
            }
            $CheckInDate = $BookingItemInfo->item(0)->getElementsByTagName("CheckInDate");
            if ($CheckInDate->length > 0) {
                $CheckInDate = $CheckInDate->item(0)->nodeValue;
            } else {
                $CheckInDate = "";
            }
            $CheckInWeekday = $BookingItemInfo->item(0)->getElementsByTagName("CheckInWeekday");
            if ($CheckInWeekday->length > 0) {
                $CheckInWeekday = $CheckInWeekday->item(0)->nodeValue;
            } else {
                $CheckInWeekday = "";
            }
            $CheckOutDate = $BookingItemInfo->item(0)->getElementsByTagName("CheckOutDate");
            if ($CheckOutDate->length > 0) {
                $CheckOutDate = $CheckOutDate->item(0)->nodeValue;
            } else {
                $CheckOutDate = "";
            }
            $CheckOutWeekday = $BookingItemInfo->item(0)->getElementsByTagName("CheckOutWeekday");
            if ($CheckOutWeekday->length > 0) {
                $CheckOutWeekday = $CheckOutWeekday->item(0)->nodeValue;
            } else {
                $CheckOutWeekday = "";
            }
            $Duration = $BookingItemInfo->item(0)->getElementsByTagName("Duration");
            if ($Duration->length > 0) {
                $Duration = $Duration->item(0)->nodeValue;
            } else {
                $Duration = "";
            }
            $CheckInLeftDays = $BookingItemInfo->item(0)->getElementsByTagName("CheckInLeftDays");
            if ($CheckInLeftDays->length > 0) {
                $CheckInLeftDays = $CheckInLeftDays->item(0)->nodeValue;
            } else {
                $CheckInLeftDays = "";
            }
            $CancelDeadLine = $BookingItemInfo->item(0)->getElementsByTagName("CancelDeadLine");
            if ($CancelDeadLine->length > 0) {
                $CancelDeadLine = $CancelDeadLine->item(0)->nodeValue;
            } else {
                $CancelDeadLine = "";
            }
            $DeadLineWeekday = $BookingItemInfo->item(0)->getElementsByTagName("DeadLineWeekday");
            if ($DeadLineWeekday->length > 0) {
                $DeadLineWeekday = $DeadLineWeekday->item(0)->nodeValue;
            } else {
                $DeadLineWeekday = "";
            }
            $DeadLineLeftDays = $BookingItemInfo->item(0)->getElementsByTagName("DeadLineLeftDays");
            if ($DeadLineLeftDays->length > 0) {
                $DeadLineLeftDays = $DeadLineLeftDays->item(0)->nodeValue;
            } else {
                $DeadLineLeftDays = "";
            }
            $FreeBreakfastCode = $BookingItemInfo->item(0)->getElementsByTagName("FreeBreakfastCode");
            if ($FreeBreakfastCode->length > 0) {
                $FreeBreakfastCode = $FreeBreakfastCode->item(0)->nodeValue;
            } else {
                $FreeBreakfastCode = "";
            }
            $FreeBreakfastName = $BookingItemInfo->item(0)->getElementsByTagName("FreeBreakfastName");
            if ($FreeBreakfastName->length > 0) {
                $FreeBreakfastName = $FreeBreakfastName->item(0)->nodeValue;
            } else {
                $FreeBreakfastName = "";
            }
            echo "PASSOU 16<br/>";
            $AddBreakfastCode = $BookingItemInfo->item(0)->getElementsByTagName("AddBreakfastCode");
            if ($AddBreakfastCode->length > 0) {
                $AddBreakfastCode = $AddBreakfastCode->item(0)->nodeValue;
            } else {
                $AddBreakfastCode = "";
            }
            $AddBreakfastName = $BookingItemInfo->item(0)->getElementsByTagName("AddBreakfastName");
            if ($AddBreakfastName->length > 0) {
                $AddBreakfastName = $AddBreakfastName->item(0)->nodeValue;
            } else {
                $AddBreakfastName = "";
            }
            $ExchangeConvertDate = $BookingItemInfo->item(0)->getElementsByTagName("ExchangeConvertDate");
            if ($ExchangeConvertDate->length > 0) {
                $ExchangeConvertDate = $ExchangeConvertDate->item(0)->nodeValue;
            } else {
                $ExchangeConvertDate = "";
            }
            $ExchangeConvertWeekday = $BookingItemInfo->item(0)->getElementsByTagName("ExchangeConvertWeekday");
            if ($ExchangeConvertWeekday->length > 0) {
                $ExchangeConvertWeekday = $ExchangeConvertWeekday->item(0)->nodeValue;
            } else {
                $ExchangeConvertWeekday = "";
            }
            $SellingCurrencyCode = $BookingItemInfo->item(0)->getElementsByTagName("SellingCurrencyCode");
            if ($SellingCurrencyCode->length > 0) {
                $SellingCurrencyCode = $SellingCurrencyCode->item(0)->nodeValue;
            } else {
                $SellingCurrencyCode = "";
            }
            echo "PASSOU 17<br/>";
            $SellingConvertRate = $BookingItemInfo->item(0)->getElementsByTagName("SellingConvertRate");
            if ($SellingConvertRate->length > 0) {
                $SellingConvertRate = $SellingConvertRate->item(0)->nodeValue;
            } else {
                $SellingConvertRate = "";
            }
            $ClientCurrencyCode = $BookingItemInfo->item(0)->getElementsByTagName("ClientCurrencyCode");
            if ($ClientCurrencyCode->length > 0) {
                $ClientCurrencyCode = $ClientCurrencyCode->item(0)->nodeValue;
            } else {
                $ClientCurrencyCode = "";
            }
            $LocalProductAmount = $BookingItemInfo->item(0)->getElementsByTagName("LocalProductAmount");
            if ($LocalProductAmount->length > 0) {
                $LocalProductAmount = $LocalProductAmount->item(0)->nodeValue;
            } else {
                $LocalProductAmount = "";
            }
            $LocalCommAmount = $BookingItemInfo->item(0)->getElementsByTagName("LocalCommAmount");
            if ($LocalCommAmount->length > 0) {
                $LocalCommAmount = $LocalCommAmount->item(0)->nodeValue;
            } else {
                $LocalCommAmount = "";
            }
            $LocalTaxAmount = $BookingItemInfo->item(0)->getElementsByTagName("LocalTaxAmount");
            if ($LocalTaxAmount->length > 0) {
                $LocalTaxAmount = $LocalTaxAmount->item(0)->nodeValue;
            } else {
                $LocalTaxAmount = "";
            }
            $LocalChangedAmount = $BookingItemInfo->item(0)->getElementsByTagName("LocalChangedAmount");
            if ($LocalChangedAmount->length > 0) {
                $LocalChangedAmount = $LocalChangedAmount->item(0)->nodeValue;
            } else {
                $LocalChangedAmount = "";
            }
            $LocalCancelCharge = $BookingItemInfo->item(0)->getElementsByTagName("LocalCancelCharge");
            if ($LocalCancelCharge->length > 0) {
                $LocalCancelCharge = $LocalCancelCharge->item(0)->nodeValue;
            } else {
                $LocalCancelCharge = "";
            }
            $LocalSellingAmount = $BookingItemInfo->item(0)->getElementsByTagName("LocalSellingAmount");
            if ($LocalSellingAmount->length > 0) {
                $LocalSellingAmount = $LocalSellingAmount->item(0)->nodeValue;
            } else {
                $LocalSellingAmount = "";
            }
            $LocalPartnerAmount = $BookingItemInfo->item(0)->getElementsByTagName("LocalPartnerAmount");
            if ($LocalPartnerAmount->length > 0) {
                $LocalPartnerAmount = $LocalPartnerAmount->item(0)->nodeValue;
            } else {
                $LocalPartnerAmount = "";
            }
            $ClientProductAmount = $BookingItemInfo->item(0)->getElementsByTagName("ClientProductAmount");
            if ($ClientProductAmount->length > 0) {
                $ClientProductAmount = $ClientProductAmount->item(0)->nodeValue;
            } else {
                $ClientProductAmount = "";
            }
            $ClientCommAmount = $BookingItemInfo->item(0)->getElementsByTagName("ClientCommAmount");
            if ($ClientCommAmount->length > 0) {
                $ClientCommAmount = $ClientCommAmount->item(0)->nodeValue;
            } else {
                $ClientCommAmount = "";
            }
            echo "PASSOU 18<br/>";
            $ClientCancelCharge = $BookingItemInfo->item(0)->getElementsByTagName("ClientCancelCharge");
            if ($ClientCancelCharge->length > 0) {
                $ClientCancelCharge = $ClientCancelCharge->item(0)->nodeValue;
            } else {
                $ClientCancelCharge = "";
            }
            $ClientPartnerAmount = $BookingItemInfo->item(0)->getElementsByTagName("ClientPartnerAmount");
            if ($ClientPartnerAmount->length > 0) {
                $ClientPartnerAmount = $ClientPartnerAmount->item(0)->nodeValue;
            } else {
                $ClientPartnerAmount = "";
            }
            $ClientSellingAmount = $BookingItemInfo->item(0)->getElementsByTagName("ClientSellingAmount");
            if ($ClientSellingAmount->length > 0) {
                $ClientSellingAmount = $ClientSellingAmount->item(0)->nodeValue;
            } else {
                $ClientSellingAmount = "";
            }
            $ItemStatusCode = $BookingItemInfo->item(0)->getElementsByTagName("ItemStatusCode");
            if ($ItemStatusCode->length > 0) {
                $ItemStatusCode = $ItemStatusCode->item(0)->nodeValue;
            } else {
                $ItemStatusCode = "";
            }
            $ItemStatusName = $BookingItemInfo->item(0)->getElementsByTagName("ItemStatusName");
            if ($ItemStatusName->length > 0) {
                $ItemStatusName = $ItemStatusName->item(0)->nodeValue;
            } else {
                $ItemStatusName = "";
            }
            $ItemConfirmationNo = $BookingItemInfo->item(0)->getElementsByTagName("ItemConfirmationNo");
            if ($ItemConfirmationNo->length > 0) {
                $ItemConfirmationNo = $ItemConfirmationNo->item(0)->nodeValue;
            } else {
                $ItemConfirmationNo = "";
            }
            $ItemConfrimedTime = $BookingItemInfo->item(0)->getElementsByTagName("ItemConfrimedTime");
            if ($ItemConfrimedTime->length > 0) {
                $ItemConfrimedTime = $ItemConfrimedTime->item(0)->nodeValue;
            } else {
                $ItemConfrimedTime = "";
            }
            $TaxSheetStatusCode = $BookingItemInfo->item(0)->getElementsByTagName("TaxSheetStatusCode");
            if ($TaxSheetStatusCode->length > 0) {
                $TaxSheetStatusCode = $TaxSheetStatusCode->item(0)->nodeValue;
            } else {
                $TaxSheetStatusCode = "";
            }
            $TaxSheetStatusName = $BookingItemInfo->item(0)->getElementsByTagName("TaxSheetStatusName");
            if ($TaxSheetStatusName->length > 0) {
                $TaxSheetStatusName = $TaxSheetStatusName->item(0)->nodeValue;
            } else {
                $TaxSheetStatusName = "";
            }
            $ItemCancelReasonCode = $BookingItemInfo->item(0)->getElementsByTagName("ItemCancelReasonCode");
            if ($ItemCancelReasonCode->length > 0) {
                $ItemCancelReasonCode = $ItemCancelReasonCode->item(0)->nodeValue;
            } else {
                $ItemCancelReasonCode = "";
            }
            $ItemCancelReasonName = $BookingItemInfo->item(0)->getElementsByTagName("ItemCancelReasonName");
            if ($ItemCancelReasonName->length > 0) {
                $ItemCancelReasonName = $ItemCancelReasonName->item(0)->nodeValue;
            } else {
                $ItemCancelReasonName = "";
            }
            $VoucherReferenceName = $BookingItemInfo->item(0)->getElementsByTagName("VoucherReferenceName");
            if ($VoucherReferenceName->length > 0) {
                $VoucherReferenceName = $VoucherReferenceName->item(0)->nodeValue;
            } else {
                $VoucherReferenceName = "";
            }
            $PorcessingComment = $BookingItemInfo->item(0)->getElementsByTagName("PorcessingComment");
            if ($PorcessingComment->length > 0) {
                $PorcessingComment = $PorcessingComment->item(0)->nodeValue;
            } else {
                $PorcessingComment = "";
            }
            $RoomTypeCode = $BookingItemInfo->item(0)->getElementsByTagName("RoomTypeCode");
            if ($RoomTypeCode->length > 0) {
                $RoomTypeCode = $RoomTypeCode->item(0)->nodeValue;
            } else {
                $RoomTypeCode = "";
            }
            $RoomTypeName = $BookingItemInfo->item(0)->getElementsByTagName("RoomTypeName");
            if ($RoomTypeName->length > 0) {
                $RoomTypeName = $RoomTypeName->item(0)->nodeValue;
            } else {
                $RoomTypeName = "";
            }
            $ModifyAble = $BookingItemInfo->item(0)->getElementsByTagName("ModifyAble");
            if ($ModifyAble->length > 0) {
                $ModifyAble = $ModifyAble->item(0)->nodeValue;
            } else {
                $ModifyAble = "";
            }
            $DeleteAble = $BookingItemInfo->item(0)->getElementsByTagName("DeleteAble");
            if ($DeleteAble->length > 0) {
                $DeleteAble = $DeleteAble->item(0)->nodeValue;
            } else {
                $DeleteAble = "";
            }
            $LastWriteTime = $BookingItemInfo->item(0)->getElementsByTagName("LastWriteTime");
            if ($LastWriteTime->length > 0) {
                $LastWriteTime = $LastWriteTime->item(0)->nodeValue;
            } else {
                $LastWriteTime = "";
            }
            $VatSheetYn = $BookingItemInfo->item(0)->getElementsByTagName("VatSheetYn");
            if ($VatSheetYn->length > 0) {
                $VatSheetYn = $VatSheetYn->item(0)->nodeValue;
            } else {
                $VatSheetYn = "";
            }
            $BookingDate = $BookingItemInfo->item(0)->getElementsByTagName("BookingDate");
            if ($BookingDate->length > 0) {
                $BookingDate = $BookingDate->item(0)->nodeValue;
            } else {
                $BookingDate = "";
            }
            echo "PASSOU 19<br/>";
            $BookingWeek = $BookingItemInfo->item(0)->getElementsByTagName("BookingWeek");
            if ($BookingWeek->length > 0) {
                $BookingWeek = $BookingWeek->item(0)->nodeValue;
            } else {
                $BookingWeek = "";
            }
            $UserKey = $BookingItemInfo->item(0)->getElementsByTagName("UserKey");
            if ($UserKey->length > 0) {
                $UserKey = $UserKey->item(0)->nodeValue;
            } else {
                $UserKey = "";
            }
            $AgentBookingReference = $BookingItemInfo->item(0)->getElementsByTagName("AgentBookingReference");
            if ($AgentBookingReference->length > 0) {
                $AgentBookingReference = $AgentBookingReference->item(0)->nodeValue;
            } else {
                $AgentBookingReference = "";
            }
            $OPStatusCode = $BookingItemInfo->item(0)->getElementsByTagName("OPStatusCode");
            if ($OPStatusCode->length > 0) {
                $OPStatusCode = $OPStatusCode->item(0)->nodeValue;
            } else {
                $OPStatusCode = "";
            }
            $OPStatusName = $BookingItemInfo->item(0)->getElementsByTagName("OPStatusName");
            if ($OPStatusName->length > 0) {
                $OPStatusName = $OPStatusName->item(0)->nodeValue;
            } else {
                $OPStatusName = "";
            }
            $PromotionCode = $BookingItemInfo->item(0)->getElementsByTagName("PromotionCode");
            if ($PromotionCode->length > 0) {
                $PromotionCode = $PromotionCode->item(0)->nodeValue;
            } else {
                $PromotionCode = "";
            }
            $AdminRemarks = $BookingItemInfo->item(0)->getElementsByTagName("AdminRemarks");
            if ($AdminRemarks->length > 0) {
                $AdminRemarks = $AdminRemarks->item(0)->nodeValue;
            } else {
                $AdminRemarks = "";
            }
            $NavisionUploadYn = $BookingItemInfo->item(0)->getElementsByTagName("NavisionUploadYn");
            if ($NavisionUploadYn->length > 0) {
                $NavisionUploadYn = $NavisionUploadYn->item(0)->nodeValue;
            } else {
                $NavisionUploadYn = "";
            }
            $NavisionPostingYn = $BookingItemInfo->item(0)->getElementsByTagName("NavisionPostingYn");
            if ($NavisionPostingYn->length > 0) {
                $NavisionPostingYn = $NavisionPostingYn->item(0)->nodeValue;
            } else {
                $NavisionPostingYn = "";
            }
            $BookMailCount = $BookingItemInfo->item(0)->getElementsByTagName("BookMailCount");
            if ($BookMailCount->length > 0) {
                $BookMailCount = $BookMailCount->item(0)->nodeValue;
            } else {
                $BookMailCount = "";
            }
            $CancelMailCount = $BookingItemInfo->item(0)->getElementsByTagName("CancelMailCount");
            if ($CancelMailCount->length > 0) {
                $CancelMailCount = $CancelMailCount->item(0)->nodeValue;
            } else {
                $CancelMailCount = "";
            }
            $ModifyMailCount = $BookingItemInfo->item(0)->getElementsByTagName("ModifyMailCount");
            if ($ModifyMailCount->length > 0) {
                $ModifyMailCount = $ModifyMailCount->item(0)->nodeValue;
            } else {
                $ModifyMailCount = "";
            }
            $VoucherMailCount = $BookingItemInfo->item(0)->getElementsByTagName("VoucherMailCount");
            if ($VoucherMailCount->length > 0) {
                $VoucherMailCount = $VoucherMailCount->item(0)->nodeValue;
            } else {
                $VoucherMailCount = "";
            }
            $VoucherRemarks = $BookingItemInfo->item(0)->getElementsByTagName("VoucherRemarks");
            if ($VoucherRemarks->length > 0) {
                $VoucherRemarks = $VoucherRemarks->item(0)->nodeValue;
            } else {
                $VoucherRemarks = "";
            }
            echo "PASSOU 20<br/>";
            $TravelerNationality = $BookingItemInfo->item(0)->getElementsByTagName("TravelerNationality");
            if ($TravelerNationality->length > 0) {
                $TravelerNationality = $TravelerNationality->item(0)->nodeValue;
            } else {
                $TravelerNationality = "";
            }
            $SpecialRemarks = $BookingItemInfo->item(0)->getElementsByTagName("SpecialRemarks");
            if ($SpecialRemarks->length > 0) {
                $SpecialRemarks = $SpecialRemarks->item(0)->nodeValue;
            } else {
                $SpecialRemarks = "";
            }
            $CancelNotice = $BookingItemInfo->item(0)->getElementsByTagName("CancelNotice");
            if ($CancelNotice->length > 0) {
                $CancelNotice = $CancelNotice->item(0)->nodeValue;
            } else {
                $CancelNotice = "";
            }
            echo "PASSOU 21<br/>";
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('bookingItem');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'ItemNo' => $ItemNo,
                    'ItemTypeCode' => $ItemTypeCode,
                    'NormalReceiveYn' => $NormalReceiveYn,
                    'BookingReference' => $BookingReference,
                    'ItemReference' => $ItemReference,
                    'CountryCode' => $CountryCode,
                    'CountryEname' => $CountryEname,
                    'CountryName' => $CountryName,
                    'CityCode' => $CityCode,
                    'CityEname' => $CityEname,
                    'CtiyName' => $CtiyName,
                    'ItemCode' => $ItemCode,
                    'ItemName' => $ItemName,
                    'BookerTypeCode' => $BookerTypeCode,
                    'BookerTypeName' => $BookerTypeName,
                    'BookingPathCode' => $BookingPathCode,
                    'BookingPathName' => $BookingPathName,
                    'CheckInDate' => $CheckInDate,
                    'CheckInWeekday' => $CheckInWeekday,
                    'CheckOutDate' => $CheckOutDate,
                    'CheckOutWeekday' => $CheckOutWeekday,
                    'Duration' => $Duration,
                    'CheckInLeftDays' => $CheckInLeftDays,
                    'CancelDeadLine' => $CancelDeadLine,
                    'DeadLineWeekday' => $DeadLineWeekday,
                    'DeadLineLeftDays' => $DeadLineLeftDays,
                    'FreeBreakfastCode' => $FreeBreakfastCode,
                    'FreeBreakfastName' => $FreeBreakfastName,
                    'AddBreakfastCode' => $AddBreakfastCode,
                    'AddBreakfastName' => $AddBreakfastName,
                    'ExchangeConvertDate' => $ExchangeConvertDate,
                    'ExchangeConvertWeekday' => $ExchangeConvertWeekday,
                    'SellingCurrencyCode' => $SellingCurrencyCode,
                    'SellingConvertRate' => $SellingConvertRate,
                    'ClientCurrencyCode' => $ClientCurrencyCode,
                    'LocalProductAmount' => $LocalProductAmount,
                    'LocalCommAmount' => $LocalCommAmount,
                    'LocalTaxAmount' => $LocalTaxAmount,
                    'LocalChangedAmount' => $LocalChangedAmount,
                    'LocalCancelCharge' => $LocalCancelCharge,
                    'LocalSellingAmount' => $LocalSellingAmount,
                    'LocalPartnerAmount' => $LocalPartnerAmount,
                    'ClientProductAmount' => $ClientProductAmount,
                    'ClientCommAmount' => $ClientCommAmount,
                    'ClientCancelCharge' => $ClientCancelCharge,
                    'ClientPartnerAmount' => $ClientPartnerAmount,
                    'ClientSellingAmount' => $ClientSellingAmount,
                    'ItemStatusCode' => $ItemStatusCode,
                    'ItemStatusName' => $ItemStatusName,
                    'ItemConfirmationNo' => $ItemConfirmationNo,
                    'ItemConfrimedTime' => $ItemConfrimedTime,
                    'TaxSheetStatusCode' => $TaxSheetStatusCode,
                    'TaxSheetStatusName' => $TaxSheetStatusName,
                    'ItemCancelReasonCode' => $ItemCancelReasonCode,
                    'ItemCancelReasonName' => $ItemCancelReasonName,
                    'VoucherReferenceName' => $VoucherReferenceName,
                    'PorcessingComment' => $PorcessingComment,
                    'RoomTypeCode' => $RoomTypeCode,
                    'RoomTypeName' => $RoomTypeName,
                    'ModifyAble' => $ModifyAble,
                    'DeleteAble' => $DeleteAble,
                    'LastWriteTime' => $LastWriteTime,
                    'VatSheetYn' => $VatSheetYn,
                    'BookingDate' => $BookingDate,
                    'BookingWeek' => $BookingWeek,
                    'UserKey' => $UserKey,
                    'AgentBookingReference' => $AgentBookingReference,
                    'OPStatusCode' => $OPStatusCode,
                    'OPStatusName' => $OPStatusName,
                    'PromotionCode' => $PromotionCode,
                    'AdminRemarks' => $AdminRemarks,
                    'NavisionUploadYn' => $NavisionUploadYn,
                    'NavisionPostingYn' => $NavisionPostingYn,
                    'BookMailCount' => $BookMailCount,
                    'CancelMailCount' => $CancelMailCount,
                    'ModifyMailCount' => $ModifyMailCount,
                    'VoucherMailCount' => $VoucherMailCount,
                    'VoucherRemarks' => $VoucherRemarks,
                    'TravelerNationality' => $TravelerNationality,
                    'SpecialRemarks' => $SpecialRemarks,
                    'CancelNotice' => $CancelNotice,
                    'RoomNo' => $RoomNo,
                    'BedTypeCode' => $BedTypeCode,
                    'BedTypeName' => $BedTypeName,
                    'LocalAddedAmount' => $LocalAddedAmount,
                    'VouchereComment' => $VouchereComment,
                    'GuestNo' => $GuestNo,
                    'GuestName' => $GuestName,
                    'BookingCode' => $BookingCode
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO ITEM: " . $e;
                echo $return;
            }
            echo "PASSOU 22<br/>";
            //RoomsAndGuestList
            $RoomsAndGuestList = $BookingItemInfo->item(0)->getElementsByTagName("RoomsAndGuestList");
            if ($RoomsAndGuestList->length > 0) {
                $RoomsAndGuestInfo = $RoomsAndGuestList->item(0)->getElementsByTagName("RoomsAndGuestInfo");
                if ($RoomsAndGuestInfo->length > 0) {
                    for ($x = 0; $x < $RoomsAndGuestInfo->length; $x++) {
                        $RoomNo = $RoomsAndGuestInfo->item($x)->getElementsByTagName("RoomNo");
                        if ($RoomNo->length > 0) {
                            $RoomNo = $RoomNo->item(0)->nodeValue;
                        } else {
                            $RoomNo = "";
                        }
                        $BedTypeCode = $RoomsAndGuestInfo->item($x)->getElementsByTagName("BedTypeCode");
                        if ($BedTypeCode->length > 0) {
                            $BedTypeCode = $BedTypeCode->item(0)->nodeValue;
                        } else {
                            $BedTypeCode = "";
                        }
                        $BedTypeName = $RoomsAndGuestInfo->item($x)->getElementsByTagName("BedTypeName");
                        if ($BedTypeName->length > 0) {
                            $BedTypeName = $BedTypeName->item(0)->nodeValue;
                        } else {
                            $BedTypeName = "";
                        }
                        $LocalAddedAmount = $RoomsAndGuestInfo->item($x)->getElementsByTagName("LocalAddedAmount");
                        if ($LocalAddedAmount->length > 0) {
                            $LocalAddedAmount = $LocalAddedAmount->item(0)->nodeValue;
                        } else {
                            $LocalAddedAmount = "";
                        }
                        $VouchereComment = $RoomsAndGuestInfo->item($x)->getElementsByTagName("VouchereComment");
                        if ($VouchereComment->length > 0) {
                            $VouchereComment = $VouchereComment->item(0)->nodeValue;
                        } else {
                            $VouchereComment = "";
                        }
                        echo "PASSOU 23<br/>";
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('booking_roomsInfo');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'RoomNo' => $RoomNo,
                                'BedTypeCode' => $BedTypeCode,
                                'BedTypeName' => $BedTypeName,
                                'LocalAddedAmount' => $LocalAddedAmount,
                                'VouchereComment' => $VouchereComment,
                                'ItemNo' => $ItemNo,
                                'BookingCode' => $BookingCode
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO ROOM: " . $e;
                            echo $return;
                        }

                        //GuestList
                        $GuestList = $RoomsAndGuestInfo->item($x)->getElementsByTagName("GuestList");
                        if ($GuestList->length > 0) {
                            $GuestInfo = $GuestList->item(0)->getElementsByTagName("GuestInfo");
                            if ($GuestInfo->length > 0) {
                                for ($x = 0; $x < $GuestInfo->length; $x++) {
                                    $GuestNo = $GuestInfo->item(0)->getElementsByTagName("GuestNo");
                                    if ($GuestNo->length > 0) {
                                        $GuestNo = $GuestNo->item(0)->nodeValue;
                                    } else {
                                        $GuestNo = "";
                                    }
                                    $GuestName = $GuestInfo->item(0)->getElementsByTagName("GuestName");
                                    if ($GuestName->length > 0) {
                                        $GuestName = $GuestName->item(0)->nodeValue;
                                    } else {
                                        $GuestName = "";
                                    }echo "PASSOU 24<br/>";

                                    try {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('booking_GuestInfo');
                                        $insert->values(array(
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'GuestNo' => $GuestNo,
                                            'GuestName' => $GuestName,
                                            'RoomNo' => $RoomNo,
                                            'BookingCode' => $BookingCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "ERRO GUEST: " . $e;
                                        echo $return;
                                    }
                                }
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