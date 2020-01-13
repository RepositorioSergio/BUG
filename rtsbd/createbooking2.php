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
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "SELECT PriceInfoItemNo, PriceInfoItemCode, RoomTypeCode, BreakfastTypeName FROM hoteis_PriceInfo";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $PriceInfoItemNo = $row->PriceInfoItemNo;
        $PriceInfoItemCode = $row->PriceInfoItemCode;
        $RoomTypeCode = $row->RoomTypeCode;
        $BreakfastTypeName = $row->BreakfastTypeName;

        echo $return;
        echo "ItemCode: " . $PriceInfoItemCode;
        echo $return;

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
                    <rts:CustomerInfo>
                        <rts:No>2</rts:No>
                        <!--Optional:-->
                        <rts:Name>Lisete Vinagre</rts:Name>
                        <!--Optional:-->
                        <rts:LastName>Vinagre</rts:LastName>
                        <!--Optional:-->
                        <rts:FirstName>Lisete</rts:FirstName>
                        <!--Optional:-->
                        <rts:Gender>F</rts:Gender>
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
                    <rts:CustomerInfo>
                        <rts:No>3</rts:No>
                        <!--Optional:-->
                        <rts:Name>Helen Vinagre</rts:Name>
                        <!--Optional:-->
                        <rts:LastName>Vinagre</rts:LastName>
                        <!--Optional:-->
                        <rts:FirstName>Helen</rts:FirstName>
                        <!--Optional:-->
                        <rts:Gender>F</rts:Gender>
                        <!--Optional:-->
                        <rts:Age>26</rts:Age>
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
                    <rts:CustomerInfo>
                        <rts:No>4</rts:No>
                        <!--Optional:-->
                        <rts:Name>Pablo Vinagre</rts:Name>
                        <!--Optional:-->
                        <rts:LastName>Vinagre</rts:LastName>
                        <!--Optional:-->
                        <rts:FirstName>Pablo</rts:FirstName>
                        <!--Optional:-->
                        <rts:Gender>M</rts:Gender>
                        <!--Optional:-->
                        <rts:Age>36</rts:Age>
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
                    <rts:CustomerInfo>
                        <rts:No>5</rts:No>
                        <!--Optional:-->
                        <rts:Name>Vania Vinagre</rts:Name>
                        <!--Optional:-->
                        <rts:LastName>Vinagre</rts:LastName>
                        <!--Optional:-->
                        <rts:FirstName>Vania</rts:FirstName>
                        <!--Optional:-->
                        <rts:Gender>F</rts:Gender>
                        <!--Optional:-->
                        <rts:Age>26</rts:Age>
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
                    <rts:ItemCode>' . $PriceInfoItemCode . '</rts:ItemCode>
                    <rts:ItemNo>' . $PriceInfoItemNo . '</rts:ItemNo>
                    <!--Optional:-->
                    <rts:AgentBookingReference></rts:AgentBookingReference>
                    <!--Optional:-->
                    <rts:BookerTypeCode>Partner</rts:BookerTypeCode>
                    <!--Optional:-->
                    <rts:BookingPathCode>PATH01</rts:BookingPathCode>
                    <!--Optional:-->
                    <rts:AppliedFromDate>2020-05-14</rts:AppliedFromDate>
                    <!--Optional:-->
                    <rts:AppliedToDate>2020-05-16</rts:AppliedToDate>
                    <!--Optional:-->
                    <rts:RoomTypeCode>' . $RoomTypeCode . '</rts:RoomTypeCode>
                    <!--Optional:-->
                    <rts:FreeBreakfastTypeName>' . $BreakfastTypeName . '</rts:FreeBreakfastTypeName>
                    <!--Optional:-->
                    <rts:AddBreakfastTypeName></rts:AddBreakfastTypeName>
                    <rts:VatSheetYn>false</rts:VatSheetYn>
                    <!--Optional:-->
                    <rts:GuestCountAndGuestList>
                        <!--Zero or more repetitions:-->
                        <rts:GuestCountAndGuestInfo>
                            <rts:RoomNo>1</rts:RoomNo>
                            <rts:AdultCount>2</rts:AdultCount>
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
                                <rts:GuestInfo>
                                    <rts:GuestNo>2</rts:GuestNo>
                                    <!--Optional:-->
                                    <rts:AgeTypeCode></rts:AgeTypeCode>
                                    <!--Optional:-->
                                    <rts:ProductId></rts:ProductId>
                                </rts:GuestInfo>                       
                            </rts:GuestList>
                        </rts:GuestCountAndGuestInfo>
                        <rts:GuestCountAndGuestInfo>
                            <rts:RoomNo>2</rts:RoomNo>
                            <rts:AdultCount>3</rts:AdultCount>
                            <rts:ChildCount>0</rts:ChildCount>
                            <!--Optional:-->
                            <rts:GuestList>
                                <!--Zero or more repetitions:-->
                                <rts:GuestInfo>
                                    <rts:GuestNo>3</rts:GuestNo>
                                    <!--Optional:-->
                                    <rts:AgeTypeCode></rts:AgeTypeCode>
                                    <!--Optional:-->
                                    <rts:ProductId></rts:ProductId>
                                </rts:GuestInfo> 
                                <rts:GuestInfo>
                                    <rts:GuestNo>4</rts:GuestNo>
                                    <!--Optional:-->
                                    <rts:AgeTypeCode></rts:AgeTypeCode>
                                    <!--Optional:-->
                                    <rts:ProductId></rts:ProductId>
                                </rts:GuestInfo>
                                <rts:GuestInfo>
                                    <rts:GuestNo>5</rts:GuestNo>
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
        if ($GetBookingDetailResponse->length > 0) {
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
                echo $return;
                echo "FIM";
                echo $return;
                die();
            }
        }
    }
}
echo "FIM2<br/>";
?>
