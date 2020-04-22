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
echo "COMECOU RESERVE CONFIRM<br/>";
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


$config = new \Zend\Config\Config(include '../config/autoload/global.destinationservices.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$signature = "";
$word = "";
date_default_timezone_set('UTC');
$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");
$accessKey = "709cc0c1189a46cca41796193c4f19af";
$secretKey = "7a846c68ec6b4a7ba964d3856307a54f";
$method = "POST";
$path = "/booking.json/activity-booking/reserve-and-confirm";

$word = $date . "" . $accessKey . "" . $method . "" . $path;

$signature = hash_hmac("sha1", $word, $secretKey,true);
$signature = base64_encode($signature);


$url = "https://api.bokun.io";
$raw = '{
    "activityRequest": {
        "activityId": 38092,
        "date": "2020-06-06",
        "rateId": 71634,
        "pricingCategoryBookings": [
            { 
                "pricingCategoryId": 16347,
                "leadPassenger": true,
                "passengerInfo": {
                    "firstName": "John",
                    "lastName": "Doe"
                }
            }
        ],
        "startTimeId": 93262
    },
    "chargeRequest": {
        "amount": 1084.88,
        "approvePageURL": "",
        "cancelPageURL": "",
        "cardNumber": "",
        "contractId": 0,
        "currency": "USD",
        "cvc": "CVV2424",
        "errorPageURL": "",
        "expMonth": "11",
        "expYear": "22",
        "formLanguage": "en",
        "name": "TestTest"
      },
    "customer": {
        "email": "Test.Test@example.com",
        "firstName": "TEST",
        "lastName": "TEST",
        "nationality": "IS",
        "sex": "m",
        "dateOfBirth": "2000-01-01",
        "phoneNumber": "+1 555 555555",
        "phoneNumberCountryCode": "US",
        "address": "123 Some st",
        "postCode": "234234",
        "state": "WA",
        "place": "Seattle",
        "country": "USA",
        "organization": "Some Inc.",
        "passportId": "AB123",
        "passportExpDay": "30",
        "passportExpMonth": "12",
        "passportExpYear": "2030"
    },
    "paymentOption": "NOT_PAID"
}';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . '/booking.json/activity-booking/reserve-and-confirm');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Bokun-Date: ' . $date,
    'X-Bokun-AccessKey: ' . $accessKey,
    'X-Bokun-Signature: ' . $signature,
    'Accept: application/json',
    'Content-Type: application/json;charset=UTF-8',
    'Content-Length: ' . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo $return;
echo $response;
echo $return;
$response = json_decode($response, true);

$config = new \Zend\Config\Config(include '../config/autoload/global.destinationservices.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$id = $response['id'];
$confirmationCode = $response['confirmationCode'];
$creationDate = $response['creationDate'];
$currency = $response['currency'];
$status = $response['status'];
$voucherHasPrices = $response['voucherHasPrices'];
$viewerRole = $response['viewerRole'];
$salesSegment = $response['salesSegment'];
$paidAmountAsText = $response['paidAmountAsText'];
$paidPercentage = $response['paidPercentage'];
$paymentStatus = $response['paymentStatus'];
$agent = $response['agent'];
$agentid = $agent['id'];
$agentcontractId = $agent['contractId'];
$agentcontractType = $agent['contractType'];
$agenttitle = $agent['title'];
//customer
$customer = $response['customer'];
$customerid = $customer['id'];
$customeruuid = $customer['uuid'];
$customerfirstName = $customer['firstName'];
$customerlastName = $customer['lastName'];
$customeremail = $customer['email'];
$customeraddress = $customer['address'];
$customercontactDetailsHidden = $customer['contactDetailsHidden'];
$customercontactDetailsHiddenUntil = $customer['contactDetailsHiddenUntil'];
$customercountry = $customer['country'];
$customercreated = $customer['created'];
$customercredentials = $customer['credentials'];
$customerdateOfBirth = $customer['dateOfBirth'];
$customerlanguage = $customer['language'];
$customernationality = $customer['nationality'];
$customerorganization = $customer['organization'];
$customerpassportExpMonth = $customer['passportExpMonth'];
$customerpassportExpYear = $customer['passportExpYear'];
$customerpassportId = $customer['passportId'];
$customerphoneNumber = $customer['phoneNumber'];
$customerphoneNumberCountryCode = $customer['phoneNumberCountryCode'];
$customerplace = $customer['place'];
$customerpostCode = $customer['postCode'];
$customersex = $customer['sex'];
$customerstate = $customer['state'];
$customertitle = $customer['title'];
$customerpersonalIdNumber = $customer['personalIdNumber'];
$customerclcEmail = $customer['clcEmail'];
$customerpassportExpDay = $customer['passportExpDay'];
//seller
$seller = $response['seller'];
$sellerid = $seller['id'];
$sellertitle = $seller['title'];
$sellerexternalId = $seller['externalId'];
$flags = $seller['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
//channel
$channel = $response['channel'];
$channelid = $channel['id'];
$channeltitle = $channel['title'];
$channelchannelType = $channel['channelType'];
$channelshowToSuppliers = $channel['showToSuppliers'];
//customerInvoice
$customerInvoice = $response['customerInvoice'];
$customerInvoiceid = $customerInvoice['id'];
$customerInvoiceissueDate = $customerInvoice['issueDate'];
$customerInvoicecurrency = $customerInvoice['currency'];
$customerInvoicedates = $customerInvoice['dates'];
$customerInvoiceexcludedTaxes = $customerInvoice['excludedTaxes'];
$customerInvoicefree = $customerInvoice['free'];
$customerInvoiceincludedTaxes = $customerInvoice['includedTaxes'];
$customerInvoiceissueDate = $customerInvoice['issueDate'];
$customerInvoiceproductBookingId = $customerInvoice['productBookingId'];
$customerInvoiceproductCategory = $customerInvoice['productCategory'];
$customerInvoiceproductConfirmationCode = $customerInvoice['productConfirmationCode'];
$customerInvoicetotalAsText = $customerInvoice['totalAsText'];
$customerInvoicetotalDiscountedAsText = $customerInvoice['totalDiscountedAsText'];
$customerInvoicetotalDueAsText = $customerInvoice['totalDueAsText'];
$customerInvoicetotalExcludedTaxAsText = $customerInvoice['totalExcludedTaxAsText'];
$customerInvoicetotalIncludedTaxAsText = $customerInvoice['totalIncludedTaxAsText'];
$customerInvoicetotalTaxAsText = $customerInvoice['totalTaxAsText'];
$includedAppliedTaxes = $customerInvoice['includedAppliedTaxes'];
if (count($includedAppliedTaxes) > 0) {
    for ($e=0; $e < count($includedAppliedTaxes); $e++) { 
        $currency = $includedAppliedTaxes[$e]['currency'];
        $tax = $includedAppliedTaxes[$e]['tax'];
        $taxAsText = $includedAppliedTaxes[$e]['taxAsText'];
        $title = $includedAppliedTaxes[$e]['title'];
        $taxAsMoney = $includedAppliedTaxes[$e]['taxAsMoney'];
        $amount = $taxAsMoney['amount'];
        $amountMajor = $taxAsMoney['amountMajor'];
        $amountMajorInt = $taxAsMoney['amountMajorInt'];
        $amountMajorLong = $taxAsMoney['amountMajorLong'];
        $amountMinor = $taxAsMoney['amountMinor'];
        $amountMinorInt = $taxAsMoney['amountMinorInt'];
        $amountMinorLong = $taxAsMoney['amountMinorLong'];
        $minorPart = $taxAsMoney['minorPart'];
        $negative = $taxAsMoney['negative'];
        $negativeOrZero = $taxAsMoney['negativeOrZero'];
        $positive = $taxAsMoney['positive'];
        $positiveOrZero = $taxAsMoney['positiveOrZero'];
        $scale = $taxAsMoney['scale'];
        $zero = $taxAsMoney['zero'];
        $currencyUnit = $taxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                $country = $countryCodes[$eAux];
            }
        }
    }
}
$excludedAppliedTaxes = $customerInvoice['excludedAppliedTaxes'];
if (count($excludedAppliedTaxes) > 0) {
    for ($e=0; $e < count($excludedAppliedTaxes); $e++) { 
        $currency = $excludedAppliedTaxes[$e]['currency'];
        $tax = $excludedAppliedTaxes[$e]['tax'];
        $taxAsText = $excludedAppliedTaxes[$e]['taxAsText'];
        $title = $excludedAppliedTaxes[$e]['title'];
        $taxAsMoney = $excludedAppliedTaxes[$e]['taxAsMoney'];
        $amount = $taxAsMoney['amount'];
        $amountMajor = $taxAsMoney['amountMajor'];
        $amountMajorInt = $taxAsMoney['amountMajorInt'];
        $amountMajorLong = $taxAsMoney['amountMajorLong'];
        $amountMinor = $taxAsMoney['amountMinor'];
        $amountMinorInt = $taxAsMoney['amountMinorInt'];
        $amountMinorLong = $taxAsMoney['amountMinorLong'];
        $minorPart = $taxAsMoney['minorPart'];
        $negative = $taxAsMoney['negative'];
        $negativeOrZero = $taxAsMoney['negativeOrZero'];
        $positive = $taxAsMoney['positive'];
        $positiveOrZero = $taxAsMoney['positiveOrZero'];
        $scale = $taxAsMoney['scale'];
        $zero = $taxAsMoney['zero'];
        $currencyUnit = $taxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                $country = $countryCodes[$eAux];
            }
        }
    }
}
$issuer = $customerInvoice['issuer'];
$issuerid = $issuer['id'];
$issuerexternalId = $issuer['externalId'];
$issuertitle = $issuer['title'];
$flags = $issuer['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($lAux=0; $lAux < count($flags); $lAux++) { 
        $flag = $flags[$lAux];
    }
}
$issuerCompany = $customerInvoice['issuerCompany'];
$registrationNumber = $issuerCompany['registrationNumber'];
$vatRegistrationNumber = $issuerCompany['vatRegistrationNumber'];
$recipient = $customerInvoice['recipient'];
$recipientid = $recipient['id'];
$recipientuuid = $recipient['uuid'];
$recipientfirstName = $recipient['firstName'];
$recipientlastName = $recipient['lastName'];
$recipientemail = $recipient['email'];
$recipientaddress = $recipient['address'];
$recipientcontactDetailsHidden = $recipient['contactDetailsHidden'];
$recipientcontactDetailsHiddenUntil = $recipient['contactDetailsHiddenUntil'];
$recipientcountry = $recipient['country'];
$recipientcreated = $recipient['created'];
$recipientcredentials = $recipient['credentials'];
$recipientdateOfBirth = $recipient['dateOfBirth'];
$recipientlanguage = $recipient['language'];
$recipientnationality = $recipient['nationality'];
$recipientorganization = $recipient['organization'];
$recipientpassportExpMonth = $recipient['passportExpMonth'];
$recipientpassportExpYear = $recipient['passportExpYear'];
$recipientpassportId = $recipient['passportId'];
$recipientphoneNumber = $recipient['phoneNumber'];
$recipientphoneNumberCountryCode = $recipient['phoneNumberCountryCode'];
$recipientplace = $recipient['place'];
$recipientpostCode = $recipient['postCode'];
$recipientsex = $recipient['sex'];
$recipientstate = $recipient['state'];
$recipienttitle = $recipient['title'];
$recipientpersonalIdNumber = $recipient['personalIdNumber'];
$recipientclcEmail = $recipient['clcEmail'];
$recipientpassportExpDay = $recipient['passportExpDay'];
$customLineItems = $customerInvoice['customLineItems'];
if (count($customLineItems) > 0) {
    for ($c=0; $c < count($customLineItems); $c++) { 
        $id = $customLineItems[$c]['id'];
        $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
        $currency = $customLineItems[$c]['currency'];
        $customDiscount = $customLineItems[$c]['customDiscount'];
        $discount = $customLineItems[$c]['discount'];
        $lineItemType = $customLineItems[$c]['lineItemType'];
        $quantity = $customLineItems[$c]['quantity'];
        $taxAmount = $customLineItems[$c]['taxAmount'];
        $taxAsText = $customLineItems[$c]['taxAsText'];
        $title = $customLineItems[$c]['title'];
        $total = $customLineItems[$c]['total'];
        $totalAsText = $customLineItems[$c]['totalAsText'];
        $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
        $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
        $totalDue = $customLineItems[$c]['totalDue'];
        $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
        $unitPrice = $customLineItems[$c]['unitPrice'];
        $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
        $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
        $tax = $customLineItems[$c]['tax'];
        $taxid = $tax['id'];
        $taxincluded = $tax['included'];
        $taxpercentage = $tax['percentage'];
        $taxtitle = $tax['title'];
        $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
        $amount = $taxAsMoney['amount'];
        $amountMajor = $taxAsMoney['amountMajor'];
        $amountMajorInt = $taxAsMoney['amountMajorInt'];
        $amountMajorLong = $taxAsMoney['amountMajorLong'];
        $amountMinor = $taxAsMoney['amountMinor'];
        $amountMinorInt = $taxAsMoney['amountMinorInt'];
        $amountMinorLong = $taxAsMoney['amountMinorLong'];
        $minorPart = $taxAsMoney['minorPart'];
        $negative = $taxAsMoney['negative'];
        $negativeOrZero = $taxAsMoney['negativeOrZero'];
        $positive = $taxAsMoney['positive'];
        $positiveOrZero = $taxAsMoney['positiveOrZero'];
        $scale = $taxAsMoney['scale'];
        $zero = $taxAsMoney['zero'];
        $currencyUnit = $taxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
        $totalAsMoneyamount = $totalAsMoney['amount'];
        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
        $totalAsMoneynegative = $totalAsMoney['negative'];
        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
        $totalAsMoneypositive = $totalAsMoney['positive'];
        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
        $totalAsMoneyscale = $totalAsMoney['scale'];
        $totalAsMoneyzero = $totalAsMoney['zero'];
        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
        $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
        $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
        $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
        $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
        $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
        $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
        $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
        $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
        $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
        $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
        $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
        $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
        $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
        $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
        $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
    }
}
$productInvoices = $customerInvoice['productInvoices'];
if (count($productInvoices) > 0) {
    for ($p=0; $p < count($productInvoices); $p++) { 
        $id = $productInvoices['id'];
        $currency = $productInvoices['currency'];
        $dates = $productInvoices['dates'];
        $excludedTaxes = $productInvoices['excludedTaxes'];
        $free = $productInvoices['free'];
        $includedTaxes = $productInvoices['includedTaxes'];
        $issueDate = $productInvoices['issueDate'];
        $productBookingId = $productInvoices['productBookingId'];
        $productCategory = $productInvoices['productCategory'];
        $productConfirmationCode = $productInvoices['productConfirmationCode'];
        $totalAsText = $productInvoices['totalAsText'];
        $totalDiscountedAsText = $productInvoices['totalDiscountedAsText'];
        $totalDueAsText = $productInvoices['totalDueAsText'];
        $totalExcludedTaxAsText = $productInvoices['totalExcludedTaxAsText'];
        $totalIncludedTaxAsText = $productInvoices['totalIncludedTaxAsText'];
        $totalTaxAsText = $productInvoices['totalTaxAsText'];
        $customLineItems = $productInvoices['customLineItems'];
        if (count($customLineItems) > 0) {
            for ($c=0; $c < count($customLineItems); $c++) { 
                $id = $customLineItems[$c]['id'];
                $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                $currency = $customLineItems[$c]['currency'];
                $customDiscount = $customLineItems[$c]['customDiscount'];
                $discount = $customLineItems[$c]['discount'];
                $lineItemType = $customLineItems[$c]['lineItemType'];
                $quantity = $customLineItems[$c]['quantity'];
                $taxAmount = $customLineItems[$c]['taxAmount'];
                $taxAsText = $customLineItems[$c]['taxAsText'];
                $title = $customLineItems[$c]['title'];
                $total = $customLineItems[$c]['total'];
                $totalAsText = $customLineItems[$c]['totalAsText'];
                $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                $totalDue = $customLineItems[$c]['totalDue'];
                $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                $unitPrice = $customLineItems[$c]['unitPrice'];
                $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                $tax = $customLineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
    }
}
$totalDueAsMoney = $customerInvoice['totalDueAsMoney'];
$totalDueAsMoneyamount = $totalDueAsMoney['amount'];
$totalDueAsMoneycurrency = $totalDueAsMoney['currency'];
$totalDueWithoutExcludedTaxAsMoney = $customerInvoice['totalDueWithoutExcludedTaxAsMoney'];
$totalDueWithoutExcludedTaxAsMoneyamount = $totalDueWithoutExcludedTaxAsMoney['amount'];
$totalDueWithoutExcludedTaxAsMoneycurrency = $totalDueWithoutExcludedTaxAsMoney['currency'];
$lodgingTaxes = $customerInvoice['lodgingTaxes'];
if (count($lodgingTaxes) > 0) {
    for ($l=0; $l < count($lodgingTaxes); $l++) { 
        $currency = $lodgingTaxes[$l]['currency'];
        $tax = $lodgingTaxes[$l]['tax'];
        $taxAsText = $lodgingTaxes[$l]['taxAsText'];
        $title = $lodgingTaxes[$l]['title'];
        $taxAsMoney = $lodgingTaxes[$l]['taxAsMoney'];
        $amount = $taxAsMoney['amount'];
        $amountMajor = $taxAsMoney['amountMajor'];
        $amountMajorInt = $taxAsMoney['amountMajorInt'];
        $amountMajorLong = $taxAsMoney['amountMajorLong'];
        $amountMinor = $taxAsMoney['amountMinor'];
        $amountMinorInt = $taxAsMoney['amountMinorInt'];
        $amountMinorLong = $taxAsMoney['amountMinorLong'];
        $minorPart = $taxAsMoney['minorPart'];
        $negative = $taxAsMoney['negative'];
        $negativeOrZero = $taxAsMoney['negativeOrZero'];
        $positive = $taxAsMoney['positive'];
        $positiveOrZero = $taxAsMoney['positiveOrZero'];
        $scale = $taxAsMoney['scale'];
        $zero = $taxAsMoney['zero'];
        $currencyUnit = $taxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
    }
}
$totalFeesAsMoney = $customerInvoice['totalFeesAsMoney'];
$totalDueAsMoneyamount = $totalDueAsMoney['amount'];
$totalDueAsMoneycurrency = $totalDueAsMoney['currency'];
$totalBookingFeeAsMoney = $customerInvoice['totalBookingFeeAsMoney'];
$totalBookingFeeAsMoneyamount = $totalBookingFeeAsMoney['amount'];
$totalBookingFeeAsMoneycurrency = $totalBookingFeeAsMoney['currency'];
$totalDiscountedAsMoney = $customerInvoice['totalDiscountedAsMoney'];
$totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
$totalDiscountedAsMoneycurrency = $totalDiscountedAsMoney['currency'];
$totalAsMoney = $customerInvoice['totalAsMoney'];
$totalAsMoneyamount = $totalAsMoney['amount'];
$totalAsMoneycurrency = $totalAsMoney['currency'];
$totalExcludedTaxAsMoney = $customerInvoice['totalExcludedTaxAsMoney'];
$totalExcludedTaxAsMoneyamount = $totalExcludedTaxAsMoney['amount'];
$totalExcludedTaxAsMoneycurrency = $totalExcludedTaxAsMoney['currency'];
$totalIncludedTaxAsMoney = $customerInvoice['totalIncludedTaxAsMoney'];
$totalIncludedTaxAsMoneyamount = $totalIncludedTaxAsMoney['amount'];
$totalIncludedTaxAsMoneycurrency = $totalIncludedTaxAsMoney['currency'];
$totalTaxAsMoney = $customerInvoice['totalTaxAsMoney'];
$totalTaxAsMoneyamount = $totalTaxAsMoney['amount'];
$totalTaxAsMoneycurrency = $totalTaxAsMoney['currency'];
$totalDiscountAsMoney = $customerInvoice['totalDiscountAsMoney'];
$totalDiscountAsMoneyamount = $totalDiscountAsMoney['amount'];
$totalDiscountAsMoneycurrency = $totalDiscountAsMoney['currency'];
//paidAmountAsMoney
$paidAmountAsMoney = $response['paidAmountAsMoney'];
$paidAmountAsMoneyamount = $paidAmountAsMoney['amount'];
$paidAmountAsMoneycurrency = $paidAmountAsMoney['currency'];
//productBookings
$productBookings = $response['productBookings'];
if (count($productBookings) > 0) {
    for ($i=0; $i < count($productBookings); $i++) { 
        $bookingId = $productBookings[$i]['bookingId'];
        $confirmationCode = $productBookings[$i]['confirmationCode'];
        $productConfirmationCode = $productBookings[$i]['productConfirmationCode'];
        $parentBookingId = $productBookings[$i]['parentBookingId'];
        $boxBooking = $productBookings[$i]['boxBooking'];
        $startDateTime = $productBookings[$i]['startDateTime'];
        $endDateTime = $productBookings[$i]['endDateTime'];
        $status = $productBookings[$i]['status'];
        $includedOnCustomerInvoice = $productBookings[$i]['includedOnCustomerInvoice'];
        $title = $productBookings[$i]['title'];
        $totalPrice = $productBookings[$i]['totalPrice'];
        $priceWithDiscount = $productBookings[$i]['priceWithDiscount'];
        $discountPercentage = $productBookings[$i]['discountPercentage'];
        $discountAmount = $productBookings[$i]['discountAmount'];
        $productCategory = $productBookings[$i]['productCategory'];
        $paidType = $productBookings[$i]['paidType'];
        $date = $productBookings[$i]['date'];
        $startTime = $productBookings[$i]['startTime'];
        $startTimeId = $productBookings[$i]['startTimeId'];
        $rateId = $productBookings[$i]['rateId'];
        $rateTitle = $productBookings[$i]['rateTitle'];
        $flexible = $productBookings[$i]['flexible'];
        $customized = $productBookings[$i]['customized'];
        $customizedDurationMinutes = $productBookings[$i]['customizedDurationMinutes'];
        $customizedDurationHours = $productBookings[$i]['customizedDurationHours'];
        $customizedDurationDays = $productBookings[$i]['customizedDurationDays'];
        $customizedDurationWeeks = $productBookings[$i]['customizedDurationWeeks'];
        $ticketPerPerson = $productBookings[$i]['ticketPerPerson'];
        $pickup = $productBookings[$i]['pickup'];
        $dropoff = $productBookings[$i]['dropoff'];
        $inventoryConfirmFailed = $productBookings[$i]['inventoryConfirmFailed'];
        $totalParticipants = $productBookings[$i]['totalParticipants'];
        $savedAmount = $productBookings[$i]['savedAmount'];
        $barcode = $productBookings[$i]['barcode'];
        $barcodevalue = $barcode['value'];
        $barcodeType = $barcode['barcodeType'];
        //product
        $product = $productBookings[$i]['product'];
        $productid = $product['id'];
        $productexternalId = $product['externalId'];
        $productCategory = $product['productCategory'];
        $producttitle = $product['title'];
        $vendor = $product['vendor'];
        $vendorid = $vendor['id'];
        $vendorcurrencyCode = $vendor['currencyCode'];
        $vendoremailAddress = $vendor['emailAddress'];
        $vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
        $vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
        $vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
        $vendortitle = $vendor['title'];
        $vendorcompanyEmailIsDefault = $vendor['companyEmailIsDefault'];
        $flags = $product['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
        $cancellationPolicy = $product['cancellationPolicy'];
        $cancellationPolicyid = $cancellationPolicy['id'];
        $cancellationPolicytitle = $cancellationPolicy['title'];
        $defaultPolicy = $cancellationPolicy['defaultPolicy'];
        $tax = $cancellationPolicy['tax'];
        $taxid = $tax['id'];
        $taxincluded = $tax['included'];
        $taxpercentage = $tax['percentage'];
        $taxtitle = $tax['title'];
        $penaltyRules = $cancellationPolicy['penaltyRules'];
        if (count($penaltyRules) > 0) {
            for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                $id = $penaltyRules[$iAux]['id'];
                $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                $charge = $penaltyRules[$iAux]['charge'];
                $chargeType = $penaltyRules[$iAux]['chargeType'];
            }
        }
        //supplier
        $supplier = $productBookings[$i]['supplier'];
        $supplierid = $supplier['id'];
        $suppliercurrencyCode = $supplier['currencyCode'];
        $supplieremailAddress = $supplier['emailAddress'];
        $supplierinvoiceIdNumber = $supplier['invoiceIdNumber'];
        $supplierlogoStyle = $supplier['logoStyle'];
        $supplierphoneNumber = $supplier['phoneNumber'];
        $suppliershowAgentDetailsOnTicket = $supplier['showAgentDetailsOnTicket'];
        $suppliershowInvoiceIdOnTicket = $supplier['showInvoiceIdOnTicket'];
        $suppliershowPaymentsOnInvoice = $supplier['showPaymentsOnInvoice'];
        $suppliertitle = $supplier['title'];
        $supplierwebsite = $supplier['website'];
        $supplierdescription = $supplier['description'];
        $suppliercountryCode = $supplier['countryCode'];
        $suppliertimeZone = $supplier['timeZone'];
        $linkedExternalCustomers = $supplier['linkedExternalCustomers'];
        if (count($linkedExternalCustomers) > 0) {
            for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
                $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
                $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
                $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
                $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
                $systemType = $linkedExternalCustomers[$j]['systemType'];
                $flags = $linkedExternalCustomers[$j]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
            }
        }
        //seller
        $seller = $productBookings[$i]['seller'];
        $sellerid = $seller['id'];
        $sellercurrencyCode = $seller['currencyCode'];
        $selleremailAddress = $seller['emailAddress'];
        $sellerinvoiceIdNumber = $seller['invoiceIdNumber'];
        $sellerlogoStyle = $seller['logoStyle'];
        $sellerphoneNumber = $seller['phoneNumber'];
        $sellershowAgentDetailsOnTicket = $seller['showAgentDetailsOnTicket'];
        $sellershowInvoiceIdOnTicket = $seller['showInvoiceIdOnTicket'];
        $sellershowPaymentsOnInvoice = $seller['showPaymentsOnInvoice'];
        $sellertitle = $seller['title'];
        $sellerwebsite = $seller['website'];
        $logo = $seller['logo'];
        $logoid = $logo['id'];
        $logoalternateText = $logo['alternateText'];
        $logodescription = $logo['description'];
        $logooriginalUrl = $logo['originalUrl'];
        $derived = $logo['derived'];
        if (count($derived) > 0) {
            for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
                $cleanUrl = $derived[$iAux4]['cleanUrl'];
                $name = $derived[$iAux4]['name'];
                $url = $derived[$iAux4]['url'];
            }
        }
        $flags = $logo['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
        $linkedExternalCustomers = $seller['linkedExternalCustomers'];
        if (count($linkedExternalCustomers) > 0) {
            for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
                $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
                $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
                $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
                $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
                $systemType = $linkedExternalCustomers[$j]['systemType'];
                $flags = $linkedExternalCustomers[$j]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
            }
        }
        //agent
        $agent = $productBookings[$i]['agent'];
        $agentid = $agent['id'];
        $agenttitle = $agent['title'];
        $linkedExternalCustomers = $agent['linkedExternalCustomers'];
        if (count($linkedExternalCustomers) > 0) {
            for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
                $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
                $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
                $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
                $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
                $systemType = $linkedExternalCustomers[$j]['systemType'];
                $flags = $linkedExternalCustomers[$j]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
            }
        }
        //linksToExternalProducts
        $linksToExternalProducts = $productBookings[$i]['linksToExternalProducts'];
        if (count($linksToExternalProducts) > 0) {
            for ($l=0; $l < count($linksToExternalProducts); $l++) { 
                $externalProductId = $linksToExternalProducts[$l]['externalProductId'];
                $externalProductTitle = $linksToExternalProducts[$l]['externalProductTitle'];
                $systemConfigId = $linksToExternalProducts[$l]['systemConfigId'];
                $flags = $linksToExternalProducts[$l]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($lAux=0; $lAux < count($flags); $lAux++) { 
                        $flag = $flags[$lAux];
                    }
                }
            }
        }
        //answers
        $answers = $productBookings[$i]['answers'];
        if (count($answers) > 0) {
            for ($k=0; $k < count($answers) ; $k++) { 
                $id = $answers[$k]['id'];
                $answer = $answers[$k]['answer'];
                $group = $answers[$k]['group'];
                $question = $answers[$k]['question'];
                $type = $answers[$k]['type'];
            }
        }
        //invoice
        $invoice = $productBookings[$i]['invoice'];
        $invoiceid = $invoice['id'];
        $invoicecurrency = $invoice['currency'];
        $invoicedates = $invoice['dates'];
        $invoiceexcludedTaxes = $invoice['excludedTaxes'];
        $invoicefree = $invoice['free'];
        $invoiceincludedTaxes = $invoice['includedTaxes'];
        $invoiceissueDate = $invoice['issueDate'];
        $invoiceproductBookingId = $invoice['productBookingId'];
        $invoiceproductCategory = $invoice['productCategory'];
        $invoiceproductConfirmationCode = $invoice['productConfirmationCode'];
        $invoicetotalAsText = $invoice['totalAsText'];
        $invoicetotalDiscountedAsText = $invoice['totalDiscountedAsText'];
        $invoicetotalDueAsText = $invoice['totalDueAsText'];
        $invoicetotalExcludedTaxAsText = $invoice['totalExcludedTaxAsText'];
        $invoicetotalIncludedTaxAsText = $invoice['totalIncludedTaxAsText'];
        $invoicetotalTaxAsText = $invoice['totalTaxAsText'];

        $issuer = $invoice['issuer'];
        $issuerid = $issuer['id'];
        $issuerexternalId = $issuer['externalId'];
        $issuertitle = $issuer['title'];
        $flags = $issuer['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($lAux=0; $lAux < count($flags); $lAux++) { 
                $flag = $flags[$lAux];
            }
        }

        $customLineItems = $invoice['customLineItems'];
        if (count($customLineItems) > 0) {
            for ($c=0; $c < count($customLineItems); $c++) { 
                $id = $customLineItems[$c]['id'];
                $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                $currency = $customLineItems[$c]['currency'];
                $customDiscount = $customLineItems[$c]['customDiscount'];
                $discount = $customLineItems[$c]['discount'];
                $lineItemType = $customLineItems[$c]['lineItemType'];
                $quantity = $customLineItems[$c]['quantity'];
                $taxAmount = $customLineItems[$c]['taxAmount'];
                $taxAsText = $customLineItems[$c]['taxAsText'];
                $title = $customLineItems[$c]['title'];
                $total = $customLineItems[$c]['total'];
                $totalAsText = $customLineItems[$c]['totalAsText'];
                $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                $totalDue = $customLineItems[$c]['totalDue'];
                $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                $unitPrice = $customLineItems[$c]['unitPrice'];
                $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                $tax = $customLineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $excludedAppliedTaxes = $invoice['excludedAppliedTaxes'];
        if (count($excludedAppliedTaxes) > 0) {
            for ($e=0; $e < count($excludedAppliedTaxes); $e++) { 
                $currency = $excludedAppliedTaxes[$e]['currency'];
                $tax = $excludedAppliedTaxes[$e]['tax'];
                $taxAsText = $excludedAppliedTaxes[$e]['taxAsText'];
                $title = $excludedAppliedTaxes[$e]['title'];
                $taxAsMoney = $excludedAppliedTaxes[$e]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                        $country = $countryCodes[$eAux];
                    }
                }
            }
        }
        $includedAppliedTaxes = $invoice['includedAppliedTaxes'];
        if (count($includedAppliedTaxes) > 0) {
            for ($e=0; $e < count($includedAppliedTaxes); $e++) { 
                $currency = $includedAppliedTaxes[$e]['currency'];
                $tax = $includedAppliedTaxes[$e]['tax'];
                $taxAsText = $includedAppliedTaxes[$e]['taxAsText'];
                $title = $includedAppliedTaxes[$e]['title'];
                $taxAsMoney = $includedAppliedTaxes[$e]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                        $country = $countryCodes[$eAux];
                    }
                }
            }
        }
        $lineItems = $invoice['lineItems'];
        if (count($lineItems) > 0) {
            for ($c=0; $c < count($lineItems); $c++) { 
                $id = $lineItems[$c]['id'];
                $calculatedDiscount = $lineItems[$c]['calculatedDiscount'];
                $currency = $lineItems[$c]['currency'];
                $customDiscount = $lineItems[$c]['customDiscount'];
                $discount = $lineItems[$c]['discount'];
                $lineItemType = $lineItems[$c]['lineItemType'];
                $quantity = $lineItems[$c]['quantity'];
                $taxAmount = $lineItems[$c]['taxAmount'];
                $taxAsText = $lineItems[$c]['taxAsText'];
                $title = $lineItems[$c]['title'];
                $total = $lineItems[$c]['total'];
                $totalAsText = $lineItems[$c]['totalAsText'];
                $totalDiscounted = $lineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $lineItems[$c]['totalDiscountedAsText'];
                $totalDue = $lineItems[$c]['totalDue'];
                $totalDueAsText = $lineItems[$c]['totalDueAsText'];
                $unitPrice = $lineItems[$c]['unitPrice'];
                $unitPriceAsText = $lineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $lineItems[$c]['unitPriceDate'];
                $tax = $lineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $lineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $lineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $lineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $lineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $lineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $lodgingTaxes = $invoice['lodgingTaxes'];
        if (count($lodgingTaxes)) {
            for ($l=0; $l < count($lodgingTaxes); $l++) { 
                $currency = $lodgingTaxes[$l]['currency'];
                $tax = $lodgingTaxes[$l]['tax'];
                $taxAsText = $lodgingTaxes[$l]['taxAsText'];
                $title = $lodgingTaxes[$l]['title'];
                $taxAsMoney = $lodgingTaxes[$l]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $paidAmountAsMoney = $invoice['paidAmountAsMoney'];
        $amount = $paidAmountAsMoney['amount'];
        $amountMajor = $paidAmountAsMoney['amountMajor'];
        $amountMajorInt = $paidAmountAsMoney['amountMajorInt'];
        $amountMajorLong = $paidAmountAsMoney['amountMajorLong'];
        $amountMinor = $paidAmountAsMoney['amountMinor'];
        $amountMinorInt = $paidAmountAsMoney['amountMinorInt'];
        $amountMinorLong = $paidAmountAsMoney['amountMinorLong'];
        $minorPart = $paidAmountAsMoney['minorPart'];
        $negative = $paidAmountAsMoney['negative'];
        $negativeOrZero = $paidAmountAsMoney['negativeOrZero'];
        $positive = $paidAmountAsMoney['positive'];
        $positiveOrZero = $paidAmountAsMoney['positiveOrZero'];
        $scale = $paidAmountAsMoney['scale'];
        $zero = $paidAmountAsMoney['zero'];
        $currencyUnit = $paidAmountAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $productInvoices = $invoice['productInvoices'];
        if (count($productInvoices) > 0) {
            for ($p=0; $p < count($productInvoices); $p++) { 
                $id = $productInvoices['id'];
                $currency = $productInvoices['currency'];
                $dates = $productInvoices['dates'];
                $excludedTaxes = $productInvoices['excludedTaxes'];
                $free = $productInvoices['free'];
                $includedTaxes = $productInvoices['includedTaxes'];
                $issueDate = $productInvoices['issueDate'];
                $productBookingId = $productInvoices['productBookingId'];
                $productCategory = $productInvoices['productCategory'];
                $productConfirmationCode = $productInvoices['productConfirmationCode'];
                $totalAsText = $productInvoices['totalAsText'];
                $totalDiscountedAsText = $productInvoices['totalDiscountedAsText'];
                $totalDueAsText = $productInvoices['totalDueAsText'];
                $totalExcludedTaxAsText = $productInvoices['totalExcludedTaxAsText'];
                $totalIncludedTaxAsText = $productInvoices['totalIncludedTaxAsText'];
                $totalTaxAsText = $productInvoices['totalTaxAsText'];
                $customLineItems = $productInvoices['customLineItems'];
                if (count($customLineItems) > 0) {
                    for ($c=0; $c < count($customLineItems); $c++) { 
                        $id = $customLineItems[$c]['id'];
                        $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                        $currency = $customLineItems[$c]['currency'];
                        $customDiscount = $customLineItems[$c]['customDiscount'];
                        $discount = $customLineItems[$c]['discount'];
                        $lineItemType = $customLineItems[$c]['lineItemType'];
                        $quantity = $customLineItems[$c]['quantity'];
                        $taxAmount = $customLineItems[$c]['taxAmount'];
                        $taxAsText = $customLineItems[$c]['taxAsText'];
                        $title = $customLineItems[$c]['title'];
                        $total = $customLineItems[$c]['total'];
                        $totalAsText = $customLineItems[$c]['totalAsText'];
                        $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                        $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                        $totalDue = $customLineItems[$c]['totalDue'];
                        $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                        $unitPrice = $customLineItems[$c]['unitPrice'];
                        $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                        $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                        $tax = $customLineItems[$c]['tax'];
                        $taxid = $tax['id'];
                        $taxincluded = $tax['included'];
                        $taxpercentage = $tax['percentage'];
                        $taxtitle = $tax['title'];
                        $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                        $amount = $taxAsMoney['amount'];
                        $amountMajor = $taxAsMoney['amountMajor'];
                        $amountMajorInt = $taxAsMoney['amountMajorInt'];
                        $amountMajorLong = $taxAsMoney['amountMajorLong'];
                        $amountMinor = $taxAsMoney['amountMinor'];
                        $amountMinorInt = $taxAsMoney['amountMinorInt'];
                        $amountMinorLong = $taxAsMoney['amountMinorLong'];
                        $minorPart = $taxAsMoney['minorPart'];
                        $negative = $taxAsMoney['negative'];
                        $negativeOrZero = $taxAsMoney['negativeOrZero'];
                        $positive = $taxAsMoney['positive'];
                        $positiveOrZero = $taxAsMoney['positiveOrZero'];
                        $scale = $taxAsMoney['scale'];
                        $zero = $taxAsMoney['zero'];
                        $currencyUnit = $taxAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                        $totalAsMoneyamount = $totalAsMoney['amount'];
                        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                        $totalAsMoneynegative = $totalAsMoney['negative'];
                        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                        $totalAsMoneypositive = $totalAsMoney['positive'];
                        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                        $totalAsMoneyscale = $totalAsMoney['scale'];
                        $totalAsMoneyzero = $totalAsMoney['zero'];
                        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                        $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                        $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                        $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                        $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                        $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                        $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                        $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                        $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                        $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                        $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                        $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                        $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                        $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                        $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                        $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                    }
                }
            }
        }
        $totalAsMoney = $invoice['totalAsMoney'];
        $totalAsMoneyamount = $totalAsMoney['amount'];
        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
        $totalAsMoneynegative = $totalAsMoney['negative'];
        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
        $totalAsMoneypositive = $totalAsMoney['positive'];
        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
        $totalAsMoneyscale = $totalAsMoney['scale'];
        $totalAsMoneyzero = $totalAsMoney['zero'];
        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountAsMoney = $invoice['totalDiscountAsMoney'];
        $totalDiscountAsMoneyamount = $totalDiscountAsMoney['amount'];
        $totalDiscountAsMoneyamountMajor = $totalDiscountAsMoney['amountMajor'];
        $totalDiscountAsMoneyamountMajorInt = $totalDiscountAsMoney['amountMajorInt'];
        $totalDiscountAsMoneyamountMajorLong = $totalDiscountAsMoney['amountMajorLong'];
        $totalDiscountAsMoneyamountMinor = $totalDiscountAsMoney['amountMinor'];
        $totalDiscountAsMoneyamountMinorInt = $totalDiscountAsMoney['amountMinorInt'];
        $totalDiscountAsMoneyamountMinorLong = $totalDiscountAsMoney['amountMinorLong'];
        $totalDiscountAsMoneyminorPart = $totalDiscountAsMoney['minorPart'];
        $totalDiscountAsMoneynegative = $totalDiscountAsMoney['negative'];
        $totalDiscountAsMoneynegativeOrZero = $totalDiscountAsMoney['negativeOrZero'];
        $totalDiscountAsMoneypositive = $totalDiscountAsMoney['positive'];
        $totalDiscountAsMoneypositiveOrZero = $totalDiscountAsMoney['positiveOrZero'];
        $totalDiscountAsMoneyscale = $totalDiscountAsMoney['scale'];
        $totalDiscountAsMoneyzero = $totalDiscountAsMoney['zero'];
        $totalDiscountAsMoneycurrencyUnit = $totalDiscountAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountedAsMoney = $invoice['totalDiscountedAsMoney'];
        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDueAsMoney = $invoice['totalDueAsMoney'];
        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalExcludedTaxAsMoney = $invoice['totalExcludedTaxAsMoney'];
        $totalExcludedTaxAsMoneyamount = $totalExcludedTaxAsMoney['amount'];
        $totalExcludedTaxAsMoneyamountMajor = $totalExcludedTaxAsMoney['amountMajor'];
        $totalExcludedTaxAsMoneyamountMajorInt = $totalExcludedTaxAsMoney['amountMajorInt'];
        $totalExcludedTaxAsMoneyamountMajorLong = $totalExcludedTaxAsMoney['amountMajorLong'];
        $totalExcludedTaxAsMoneyamountMinor = $totalExcludedTaxAsMoney['amountMinor'];
        $totalExcludedTaxAsMoneyamountMinorInt = $totalExcludedTaxAsMoney['amountMinorInt'];
        $totalExcludedTaxAsMoneyamountMinorLong = $totalExcludedTaxAsMoney['amountMinorLong'];
        $totalExcludedTaxAsMoneyminorPart = $totalExcludedTaxAsMoney['minorPart'];
        $totalExcludedTaxAsMoneynegative = $totalExcludedTaxAsMoney['negative'];
        $totalExcludedTaxAsMoneynegativeOrZero = $totalExcludedTaxAsMoney['negativeOrZero'];
        $totalExcludedTaxAsMoneypositive = $totalExcludedTaxAsMoney['positive'];
        $totalExcludedTaxAsMoneypositiveOrZero = $totalExcludedTaxAsMoney['positiveOrZero'];
        $totalExcludedTaxAsMoneyscale = $totalExcludedTaxAsMoney['scale'];
        $totalExcludedTaxAsMoneyzero = $totalExcludedTaxAsMoney['zero'];
        $totalExcludedTaxAsMoneycurrencyUnit = $totalExcludedTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalIncludedTaxAsMoney = $invoice['totalIncludedTaxAsMoney'];
        $totalIncludedTaxAsMoneyamount = $totalIncludedTaxAsMoney['amount'];
        $totalIncludedTaxAsMoneyamountMajor = $totalIncludedTaxAsMoney['amountMajor'];
        $totalIncludedTaxAsMoneyamountMajorInt = $totalIncludedTaxAsMoney['amountMajorInt'];
        $totalIncludedTaxAsMoneyamountMajorLong = $totalIncludedTaxAsMoney['amountMajorLong'];
        $totalIncludedTaxAsMoneyamountMinor = $totalIncludedTaxAsMoney['amountMinor'];
        $totalIncludedTaxAsMoneyamountMinorInt = $totalIncludedTaxAsMoney['amountMinorInt'];
        $totalIncludedTaxAsMoneyamountMinorLong = $totalIncludedTaxAsMoney['amountMinorLong'];
        $totalIncludedTaxAsMoneyminorPart = $totalIncludedTaxAsMoney['minorPart'];
        $totalIncludedTaxAsMoneynegative = $totalIncludedTaxAsMoney['negative'];
        $totalIncludedTaxAsMoneynegativeOrZero = $totalIncludedTaxAsMoney['negativeOrZero'];
        $totalIncludedTaxAsMoneypositive = $totalIncludedTaxAsMoney['positive'];
        $totalIncludedTaxAsMoneypositiveOrZero = $totalIncludedTaxAsMoney['positiveOrZero'];
        $totalIncludedTaxAsMoneyscale = $totalIncludedTaxAsMoney['scale'];
        $totalIncludedTaxAsMoneyzero = $totalIncludedTaxAsMoney['zero'];
        $totalIncludedTaxAsMoneycurrencyUnit = $totalIncludedTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalTaxAsMoney = $invoice['totalTaxAsMoney'];
        $totalTaxAsMoneyamount = $totalTaxAsMoney['amount'];
        $totalTaxAsMoneyamountMajor = $totalTaxAsMoney['amountMajor'];
        $totalTaxAsMoneyamountMajorInt = $totalTaxAsMoney['amountMajorInt'];
        $totalTaxAsMoneyamountMajorLong = $totalTaxAsMoney['amountMajorLong'];
        $totalTaxAsMoneyamountMinor = $totalTaxAsMoney['amountMinor'];
        $totalTaxAsMoneyamountMinorInt = $totalTaxAsMoney['amountMinorInt'];
        $totalTaxAsMoneyamountMinorLong = $totalTaxAsMoney['amountMinorLong'];
        $totalTaxAsMoneyminorPart = $totalTaxAsMoney['minorPart'];
        $totalTaxAsMoneynegative = $totalTaxAsMoney['negative'];
        $totalTaxAsMoneynegativeOrZero = $totalTaxAsMoney['negativeOrZero'];
        $totalTaxAsMoneypositive = $totalTaxAsMoney['positive'];
        $totalTaxAsMoneypositiveOrZero = $totalTaxAsMoney['positiveOrZero'];
        $totalTaxAsMoneyscale = $totalTaxAsMoney['scale'];
        $totalTaxAsMoneyzero = $totalTaxAsMoney['zero'];
        $totalTaxAsMoneycurrencyUnit = $totalTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        //sellerInvoice
        $sellerInvoice = $productBookings[$i]['sellerInvoice'];
        $sellerInvoiceid = $sellerInvoice['id'];
        $sellerInvoicecurrency = $sellerInvoice['currency'];
        $sellerInvoicedates = $sellerInvoice['dates'];
        $sellerInvoiceexcludedTaxes = $sellerInvoice['excludedTaxes'];
        $sellerInvoicefree = $sellerInvoice['free'];
        $sellerInvoiceincludedTaxes = $sellerInvoice['includedTaxes'];
        $sellerInvoiceissueDate = $sellerInvoice['issueDate'];
        $sellerInvoiceproductBookingId = $sellerInvoice['productBookingId'];
        $sellerInvoiceproductCategory = $sellerInvoice['productCategory'];
        $sellerInvoiceproductConfirmationCode = $sellerInvoice['productConfirmationCode'];
        $sellerInvoicetotalAsText = $sellerInvoice['totalAsText'];
        $sellerInvoicetotalDiscountedAsText = $sellerInvoice['totalDiscountedAsText'];
        $sellerInvoicetotalDueAsText = $sellerInvoice['totalDueAsText'];
        $sellerInvoicetotalExcludedTaxAsText = $sellerInvoice['totalExcludedTaxAsText'];
        $sellerInvoicetotalIncludedTaxAsText = $sellerInvoice['totalIncludedTaxAsText'];
        $sellerInvoicetotalTaxAsText = $sellerInvoice['totalTaxAsText'];

        $issuer = $sellerInvoice['issuer'];
        $issuerid = $issuer['id'];
        $issuerexternalId = $issuer['externalId'];
        $issuertitle = $issuer['title'];
        $flags = $issuer['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($lAux=0; $lAux < count($flags); $lAux++) { 
                $flag = $flags[$lAux];
            }
        }

        $customLineItems = $sellerInvoice['customLineItems'];
        if (count($customLineItems) > 0) {
            for ($c=0; $c < count($customLineItems); $c++) { 
                $id = $customLineItems[$c]['id'];
                $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                $currency = $customLineItems[$c]['currency'];
                $customDiscount = $customLineItems[$c]['customDiscount'];
                $discount = $customLineItems[$c]['discount'];
                $lineItemType = $customLineItems[$c]['lineItemType'];
                $quantity = $customLineItems[$c]['quantity'];
                $taxAmount = $customLineItems[$c]['taxAmount'];
                $taxAsText = $customLineItems[$c]['taxAsText'];
                $title = $customLineItems[$c]['title'];
                $total = $customLineItems[$c]['total'];
                $totalAsText = $customLineItems[$c]['totalAsText'];
                $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                $totalDue = $customLineItems[$c]['totalDue'];
                $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                $unitPrice = $customLineItems[$c]['unitPrice'];
                $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                $tax = $customLineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $excludedAppliedTaxes = $sellerInvoice['excludedAppliedTaxes'];
        if (count($excludedAppliedTaxes) > 0) {
            for ($e=0; $e < count($excludedAppliedTaxes); $e++) { 
                $currency = $excludedAppliedTaxes[$e]['currency'];
                $tax = $excludedAppliedTaxes[$e]['tax'];
                $taxAsText = $excludedAppliedTaxes[$e]['taxAsText'];
                $title = $excludedAppliedTaxes[$e]['title'];
                $taxAsMoney = $excludedAppliedTaxes[$e]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                        $country = $countryCodes[$eAux];
                    }
                }
            }
        }
        $includedAppliedTaxes = $sellerInvoice['includedAppliedTaxes'];
        if (count($includedAppliedTaxes) > 0) {
            for ($e=0; $e < count($includedAppliedTaxes); $e++) { 
                $currency = $includedAppliedTaxes[$e]['currency'];
                $tax = $includedAppliedTaxes[$e]['tax'];
                $taxAsText = $includedAppliedTaxes[$e]['taxAsText'];
                $title = $includedAppliedTaxes[$e]['title'];
                $taxAsMoney = $includedAppliedTaxes[$e]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                        $country = $countryCodes[$eAux];
                    }
                }
            }
        }
        $lineItems = $sellerInvoice['lineItems'];
        if (count($lineItems) > 0) {
            for ($c=0; $c < count($lineItems); $c++) { 
                $id = $lineItems[$c]['id'];
                $calculatedDiscount = $lineItems[$c]['calculatedDiscount'];
                $currency = $lineItems[$c]['currency'];
                $customDiscount = $lineItems[$c]['customDiscount'];
                $discount = $lineItems[$c]['discount'];
                $lineItemType = $lineItems[$c]['lineItemType'];
                $quantity = $lineItems[$c]['quantity'];
                $taxAmount = $lineItems[$c]['taxAmount'];
                $taxAsText = $lineItems[$c]['taxAsText'];
                $title = $lineItems[$c]['title'];
                $total = $lineItems[$c]['total'];
                $totalAsText = $lineItems[$c]['totalAsText'];
                $totalDiscounted = $lineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $lineItems[$c]['totalDiscountedAsText'];
                $totalDue = $lineItems[$c]['totalDue'];
                $totalDueAsText = $lineItems[$c]['totalDueAsText'];
                $unitPrice = $lineItems[$c]['unitPrice'];
                $unitPriceAsText = $lineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $lineItems[$c]['unitPriceDate'];
                $tax = $lineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $lineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $lineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $lineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $lineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $lineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $lodgingTaxes = $sellerInvoice['lodgingTaxes'];
        if (count($lodgingTaxes)) {
            for ($l=0; $l < count($lodgingTaxes); $l++) { 
                $currency = $lodgingTaxes[$l]['currency'];
                $tax = $lodgingTaxes[$l]['tax'];
                $taxAsText = $lodgingTaxes[$l]['taxAsText'];
                $title = $lodgingTaxes[$l]['title'];
                $taxAsMoney = $lodgingTaxes[$l]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $paidAmountAsMoney = $sellerInvoice['paidAmountAsMoney'];
        $amount = $paidAmountAsMoney['amount'];
        $amountMajor = $paidAmountAsMoney['amountMajor'];
        $amountMajorInt = $paidAmountAsMoney['amountMajorInt'];
        $amountMajorLong = $paidAmountAsMoney['amountMajorLong'];
        $amountMinor = $paidAmountAsMoney['amountMinor'];
        $amountMinorInt = $paidAmountAsMoney['amountMinorInt'];
        $amountMinorLong = $paidAmountAsMoney['amountMinorLong'];
        $minorPart = $paidAmountAsMoney['minorPart'];
        $negative = $paidAmountAsMoney['negative'];
        $negativeOrZero = $paidAmountAsMoney['negativeOrZero'];
        $positive = $paidAmountAsMoney['positive'];
        $positiveOrZero = $paidAmountAsMoney['positiveOrZero'];
        $scale = $paidAmountAsMoney['scale'];
        $zero = $paidAmountAsMoney['zero'];
        $currencyUnit = $paidAmountAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $productsellerInvoices = $sellerInvoice['productsellerInvoices'];
        if (count($productsellerInvoices) > 0) {
            for ($p=0; $p < count($productsellerInvoices); $p++) { 
                $id = $productsellerInvoices['id'];
                $currency = $productsellerInvoices['currency'];
                $dates = $productsellerInvoices['dates'];
                $excludedTaxes = $productsellerInvoices['excludedTaxes'];
                $free = $productsellerInvoices['free'];
                $includedTaxes = $productsellerInvoices['includedTaxes'];
                $issueDate = $productsellerInvoices['issueDate'];
                $productBookingId = $productsellerInvoices['productBookingId'];
                $productCategory = $productsellerInvoices['productCategory'];
                $productConfirmationCode = $productsellerInvoices['productConfirmationCode'];
                $totalAsText = $productsellerInvoices['totalAsText'];
                $totalDiscountedAsText = $productsellerInvoices['totalDiscountedAsText'];
                $totalDueAsText = $productsellerInvoices['totalDueAsText'];
                $totalExcludedTaxAsText = $productsellerInvoices['totalExcludedTaxAsText'];
                $totalIncludedTaxAsText = $productsellerInvoices['totalIncludedTaxAsText'];
                $totalTaxAsText = $productsellerInvoices['totalTaxAsText'];
                $customLineItems = $productsellerInvoices['customLineItems'];
                if (count($customLineItems) > 0) {
                    for ($c=0; $c < count($customLineItems); $c++) { 
                        $id = $customLineItems[$c]['id'];
                        $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                        $currency = $customLineItems[$c]['currency'];
                        $customDiscount = $customLineItems[$c]['customDiscount'];
                        $discount = $customLineItems[$c]['discount'];
                        $lineItemType = $customLineItems[$c]['lineItemType'];
                        $quantity = $customLineItems[$c]['quantity'];
                        $taxAmount = $customLineItems[$c]['taxAmount'];
                        $taxAsText = $customLineItems[$c]['taxAsText'];
                        $title = $customLineItems[$c]['title'];
                        $total = $customLineItems[$c]['total'];
                        $totalAsText = $customLineItems[$c]['totalAsText'];
                        $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                        $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                        $totalDue = $customLineItems[$c]['totalDue'];
                        $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                        $unitPrice = $customLineItems[$c]['unitPrice'];
                        $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                        $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                        $tax = $customLineItems[$c]['tax'];
                        $taxid = $tax['id'];
                        $taxincluded = $tax['included'];
                        $taxpercentage = $tax['percentage'];
                        $taxtitle = $tax['title'];
                        $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                        $amount = $taxAsMoney['amount'];
                        $amountMajor = $taxAsMoney['amountMajor'];
                        $amountMajorInt = $taxAsMoney['amountMajorInt'];
                        $amountMajorLong = $taxAsMoney['amountMajorLong'];
                        $amountMinor = $taxAsMoney['amountMinor'];
                        $amountMinorInt = $taxAsMoney['amountMinorInt'];
                        $amountMinorLong = $taxAsMoney['amountMinorLong'];
                        $minorPart = $taxAsMoney['minorPart'];
                        $negative = $taxAsMoney['negative'];
                        $negativeOrZero = $taxAsMoney['negativeOrZero'];
                        $positive = $taxAsMoney['positive'];
                        $positiveOrZero = $taxAsMoney['positiveOrZero'];
                        $scale = $taxAsMoney['scale'];
                        $zero = $taxAsMoney['zero'];
                        $currencyUnit = $taxAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                        $totalAsMoneyamount = $totalAsMoney['amount'];
                        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                        $totalAsMoneynegative = $totalAsMoney['negative'];
                        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                        $totalAsMoneypositive = $totalAsMoney['positive'];
                        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                        $totalAsMoneyscale = $totalAsMoney['scale'];
                        $totalAsMoneyzero = $totalAsMoney['zero'];
                        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                        $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                        $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                        $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                        $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                        $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                        $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                        $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                        $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                        $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                        $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                        $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                        $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                        $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                        $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                        $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                    }
                }
            }
        }
        $totalAsMoney = $sellerInvoice['totalAsMoney'];
        $totalAsMoneyamount = $totalAsMoney['amount'];
        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
        $totalAsMoneynegative = $totalAsMoney['negative'];
        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
        $totalAsMoneypositive = $totalAsMoney['positive'];
        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
        $totalAsMoneyscale = $totalAsMoney['scale'];
        $totalAsMoneyzero = $totalAsMoney['zero'];
        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountAsMoney = $sellerInvoice['totalDiscountAsMoney'];
        $totalDiscountAsMoneyamount = $totalDiscountAsMoney['amount'];
        $totalDiscountAsMoneyamountMajor = $totalDiscountAsMoney['amountMajor'];
        $totalDiscountAsMoneyamountMajorInt = $totalDiscountAsMoney['amountMajorInt'];
        $totalDiscountAsMoneyamountMajorLong = $totalDiscountAsMoney['amountMajorLong'];
        $totalDiscountAsMoneyamountMinor = $totalDiscountAsMoney['amountMinor'];
        $totalDiscountAsMoneyamountMinorInt = $totalDiscountAsMoney['amountMinorInt'];
        $totalDiscountAsMoneyamountMinorLong = $totalDiscountAsMoney['amountMinorLong'];
        $totalDiscountAsMoneyminorPart = $totalDiscountAsMoney['minorPart'];
        $totalDiscountAsMoneynegative = $totalDiscountAsMoney['negative'];
        $totalDiscountAsMoneynegativeOrZero = $totalDiscountAsMoney['negativeOrZero'];
        $totalDiscountAsMoneypositive = $totalDiscountAsMoney['positive'];
        $totalDiscountAsMoneypositiveOrZero = $totalDiscountAsMoney['positiveOrZero'];
        $totalDiscountAsMoneyscale = $totalDiscountAsMoney['scale'];
        $totalDiscountAsMoneyzero = $totalDiscountAsMoney['zero'];
        $totalDiscountAsMoneycurrencyUnit = $totalDiscountAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountedAsMoney = $sellerInvoice['totalDiscountedAsMoney'];
        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDueAsMoney = $sellerInvoice['totalDueAsMoney'];
        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalExcludedTaxAsMoney = $sellerInvoice['totalExcludedTaxAsMoney'];
        $totalExcludedTaxAsMoneyamount = $totalExcludedTaxAsMoney['amount'];
        $totalExcludedTaxAsMoneyamountMajor = $totalExcludedTaxAsMoney['amountMajor'];
        $totalExcludedTaxAsMoneyamountMajorInt = $totalExcludedTaxAsMoney['amountMajorInt'];
        $totalExcludedTaxAsMoneyamountMajorLong = $totalExcludedTaxAsMoney['amountMajorLong'];
        $totalExcludedTaxAsMoneyamountMinor = $totalExcludedTaxAsMoney['amountMinor'];
        $totalExcludedTaxAsMoneyamountMinorInt = $totalExcludedTaxAsMoney['amountMinorInt'];
        $totalExcludedTaxAsMoneyamountMinorLong = $totalExcludedTaxAsMoney['amountMinorLong'];
        $totalExcludedTaxAsMoneyminorPart = $totalExcludedTaxAsMoney['minorPart'];
        $totalExcludedTaxAsMoneynegative = $totalExcludedTaxAsMoney['negative'];
        $totalExcludedTaxAsMoneynegativeOrZero = $totalExcludedTaxAsMoney['negativeOrZero'];
        $totalExcludedTaxAsMoneypositive = $totalExcludedTaxAsMoney['positive'];
        $totalExcludedTaxAsMoneypositiveOrZero = $totalExcludedTaxAsMoney['positiveOrZero'];
        $totalExcludedTaxAsMoneyscale = $totalExcludedTaxAsMoney['scale'];
        $totalExcludedTaxAsMoneyzero = $totalExcludedTaxAsMoney['zero'];
        $totalExcludedTaxAsMoneycurrencyUnit = $totalExcludedTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalIncludedTaxAsMoney = $sellerInvoice['totalIncludedTaxAsMoney'];
        $totalIncludedTaxAsMoneyamount = $totalIncludedTaxAsMoney['amount'];
        $totalIncludedTaxAsMoneyamountMajor = $totalIncludedTaxAsMoney['amountMajor'];
        $totalIncludedTaxAsMoneyamountMajorInt = $totalIncludedTaxAsMoney['amountMajorInt'];
        $totalIncludedTaxAsMoneyamountMajorLong = $totalIncludedTaxAsMoney['amountMajorLong'];
        $totalIncludedTaxAsMoneyamountMinor = $totalIncludedTaxAsMoney['amountMinor'];
        $totalIncludedTaxAsMoneyamountMinorInt = $totalIncludedTaxAsMoney['amountMinorInt'];
        $totalIncludedTaxAsMoneyamountMinorLong = $totalIncludedTaxAsMoney['amountMinorLong'];
        $totalIncludedTaxAsMoneyminorPart = $totalIncludedTaxAsMoney['minorPart'];
        $totalIncludedTaxAsMoneynegative = $totalIncludedTaxAsMoney['negative'];
        $totalIncludedTaxAsMoneynegativeOrZero = $totalIncludedTaxAsMoney['negativeOrZero'];
        $totalIncludedTaxAsMoneypositive = $totalIncludedTaxAsMoney['positive'];
        $totalIncludedTaxAsMoneypositiveOrZero = $totalIncludedTaxAsMoney['positiveOrZero'];
        $totalIncludedTaxAsMoneyscale = $totalIncludedTaxAsMoney['scale'];
        $totalIncludedTaxAsMoneyzero = $totalIncludedTaxAsMoney['zero'];
        $totalIncludedTaxAsMoneycurrencyUnit = $totalIncludedTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalTaxAsMoney = $sellerInvoice['totalTaxAsMoney'];
        $totalTaxAsMoneyamount = $totalTaxAsMoney['amount'];
        $totalTaxAsMoneyamountMajor = $totalTaxAsMoney['amountMajor'];
        $totalTaxAsMoneyamountMajorInt = $totalTaxAsMoney['amountMajorInt'];
        $totalTaxAsMoneyamountMajorLong = $totalTaxAsMoney['amountMajorLong'];
        $totalTaxAsMoneyamountMinor = $totalTaxAsMoney['amountMinor'];
        $totalTaxAsMoneyamountMinorInt = $totalTaxAsMoney['amountMinorInt'];
        $totalTaxAsMoneyamountMinorLong = $totalTaxAsMoney['amountMinorLong'];
        $totalTaxAsMoneyminorPart = $totalTaxAsMoney['minorPart'];
        $totalTaxAsMoneynegative = $totalTaxAsMoney['negative'];
        $totalTaxAsMoneynegativeOrZero = $totalTaxAsMoney['negativeOrZero'];
        $totalTaxAsMoneypositive = $totalTaxAsMoney['positive'];
        $totalTaxAsMoneypositiveOrZero = $totalTaxAsMoney['positiveOrZero'];
        $totalTaxAsMoneyscale = $totalTaxAsMoney['scale'];
        $totalTaxAsMoneyzero = $totalTaxAsMoney['zero'];
        $totalTaxAsMoneycurrencyUnit = $totalTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        //notes
        $notes = $productBookings[$i]['notes'];
        if (count($notes) > 0) {
            for ($n=0; $n < count($notes); $n++) { 
                $author = $notes[$n]['author'];
                $body = $notes[$n]['body'];
                $created = $notes[$n]['created'];
                $ownerId = $notes[$n]['ownerId'];
                $recipient = $notes[$n]['recipient'];
                $sentAsEmail = $notes[$n]['sentAsEmail'];
                $subject = $notes[$n]['subject'];
                $type = $notes[$n]['type'];
                $voucherAttached = $notes[$n]['voucherAttached'];
                $voucherPricesShown = $notes[$n]['voucherPricesShown'];
            }
        }
        //supplierContractFlags
        $supplierContractFlags = $productBookings[$i]['supplierContractFlags'];
        if (count($supplierContractFlags) > 0) {
            $contract = ""; 
            for ($m=0; $m < count($supplierContractFlags); $m++) { 
                $contract = $supplierContractFlags[$m];
            }
        }
        //sellerContractFlags
        $sellerContractFlags = $productBookings[$i]['sellerContractFlags'];
        if (count($sellerContractFlags) > 0) {
            $contract = ""; 
            for ($m=0; $m < count($sellerContractFlags); $m++) { 
                $contract = $sellerContractFlags[$m];
            }
        }
        //cancellationPolicy
        $cancellationPolicy = $productBookings[$i]['cancellationPolicy'];
        $cancellationPolicyid = $cancellationPolicy['id'];
        $cancellationPolicytitle = $cancellationPolicy['title'];
        $defaultPolicy = $cancellationPolicy['defaultPolicy'];
        $tax = $cancellationPolicy['tax'];
        $taxid = $tax['id'];
        $taxincluded = $tax['included'];
        $taxpercentage = $tax['percentage'];
        $taxtitle = $tax['title'];
        $penaltyRules = $cancellationPolicy['penaltyRules'];
        if (count($penaltyRules) > 0) {
            for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                $id = $penaltyRules[$iAux]['id'];
                $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                $charge = $penaltyRules[$iAux]['charge'];
                $chargeType = $penaltyRules[$iAux]['chargeType'];
            }
        }
        $bookingRoles = $productBookings[$i]['bookingRoles'];
        if (count($bookingRoles) > 0) {
            $roles = "";
            for ($iAux2=0; $iAux2 < count($bookingRoles); $iAux2++) { 
                $roles = $bookingRoles[$iAux2];
            }
        }
        //pricingCategoryBookings
        $pricingCategoryBookings = $productBookings[$i]['pricingCategoryBookings'];
        if (count($pricingCategoryBookings) > 0) {
            for ($iAux3=0; $iAux3 < count($pricingCategoryBookings); $iAux3++) { 
                $id = $pricingCategoryBookings[$iAux3]['id'];
                $pricingCategoryId = $pricingCategoryBookings[$iAux3]['pricingCategoryId'];
                $leadPassenger = $pricingCategoryBookings[$iAux3]['leadPassenger'];
                $age = $pricingCategoryBookings[$iAux3]['age'];
                $bookedTitle = $pricingCategoryBookings[$iAux3]['bookedTitle'];
                $quantity = $pricingCategoryBookings[$iAux3]['quantity'];
                $pricingCategory = $pricingCategoryBookings[$iAux3]['pricingCategory'];
                $pricingCategoryid = $pricingCategory['id'];
                $pricingCategorytitle = $pricingCategory['title'];
                $pricingCategoryticketCategory = $pricingCategory['ticketCategory'];
                $pricingCategoryoccupancy = $pricingCategory['occupancy'];
                $pricingCategorygroupSize = $pricingCategory['groupSize'];
                $pricingCategoryageQualified = $pricingCategory['ageQualified'];
                $pricingCategoryminAge = $pricingCategory['minAge'];
                $pricingCategorymaxAge = $pricingCategory['maxAge'];
                $pricingCategorydependent = $pricingCategory['dependent'];
                $pricingCategorymasterCategoryId = $pricingCategory['masterCategoryId'];
                $pricingCategorymaxPerMaster = $pricingCategory['maxPerMaster'];
                $pricingCategorysumDependentCategories = $pricingCategory['sumDependentCategories'];
                $pricingCategorymaxDependentSum = $pricingCategory['maxDependentSum'];
                $pricingCategoryinternalUseOnly = $pricingCategory['internalUseOnly'];
                $pricingCategorydefaultCategory = $pricingCategory['defaultCategory'];
                $pricingCategoryfullTitle = $pricingCategory['fullTitle'];
                $flags = $pricingCategory['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($lAux=0; $lAux < count($flags); $lAux++) { 
                        $flag = $flags[$lAux];
                    }
                }
                $extras = $pricingCategoryBookings[$iAux3]['extras'];
                if (count($extras) > 0) {
                    for ($iAux14=0; $iAux14 < count($extras); $iAux14++) { 
                        $id = $extras[$iAux14]['id'];
                        $externalId = $extras[$iAux14]['externalId'];
                        $free = $extras[$iAux14]['free'];
                        $included = $extras[$iAux14]['included'];
                        $increasesCapacity = $extras[$iAux14]['increasesCapacity'];
                        $information = $extras[$iAux14]['information'];
                        $maxPerBooking = $extras[$iAux14]['maxPerBooking'];
                        $price = $extras[$iAux14]['price'];
                        $pricingType = $extras[$iAux14]['pricingType'];
                        $pricingTypeLabel = $extras[$iAux14]['pricingTypeLabel'];
                        $title = $extras[$iAux14]['title'];
                        $flags = $extras[$iAux14]['flags'];
                        $questions = $extras[$iAux14]['questions'];
                        if (count($questions) > 0) {
                            for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                                $id = $questions[$iAux15]['id'];
                                $active = $questions[$iAux15]['active'];
                                $answerRequired = $questions[$iAux15]['answerRequired'];
                                $label = $questions[$iAux15]['label'];
                                $options = $questions[$iAux15]['options'];
                                $type = $questions[$iAux15]['type'];
                                $flags = $questions[$iAux15]['flags'];
                                if (count($flags) > 0) {
                                    $flag = "";
                                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                        $flag = $flags[$iAux9];
                                    }
                                }
                            }
                        }
                    }
                }
                $answers = $pricingCategoryBookings[$iAux3]['answers'];
                if (count($answers) > 0) {
                    for ($k=0; $k < count($answers) ; $k++) { 
                        $id = $answers[$k]['id'];
                        $answer = $answers[$k]['answer'];
                        $group = $answers[$k]['group'];
                        $question = $answers[$k]['question'];
                        $type = $answers[$k]['type'];
                    }
                }
                $flags = $pricingCategoryBookings[$iAux3]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($lAux=0; $lAux < count($flags); $lAux++) { 
                        $flag = $flags[$lAux];
                    }
                }
            }
        }
        //extras
        $extras = $productBookings[$i]['extras'];
        if (count($extras) > 0) {
            for ($iAux14=0; $iAux14 < count($extras); $iAux14++) { 
                $id = $extras[$iAux14]['id'];
                $externalId = $extras[$iAux14]['externalId'];
                $free = $extras[$iAux14]['free'];
                $included = $extras[$iAux14]['included'];
                $increasesCapacity = $extras[$iAux14]['increasesCapacity'];
                $information = $extras[$iAux14]['information'];
                $maxPerBooking = $extras[$iAux14]['maxPerBooking'];
                $price = $extras[$iAux14]['price'];
                $pricingType = $extras[$iAux14]['pricingType'];
                $pricingTypeLabel = $extras[$iAux14]['pricingTypeLabel'];
                $title = $extras[$iAux14]['title'];
                $flags = $extras[$iAux14]['flags'];
                $questions = $extras[$iAux14]['questions'];
                if (count($questions) > 0) {
                    for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                        $id = $questions[$iAux15]['id'];
                        $active = $questions[$iAux15]['active'];
                        $answerRequired = $questions[$iAux15]['answerRequired'];
                        $label = $questions[$iAux15]['label'];
                        $options = $questions[$iAux15]['options'];
                        $type = $questions[$iAux15]['type'];
                        $flags = $questions[$iAux15]['flags'];
                        if (count($flags) > 0) {
                            $flag = "";
                            for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                $flag = $flags[$iAux9];
                            }
                        }
                    }
                }
            }
        }
        //bookingFields
        $bookingFields = $productBookings[$i]['bookingFields'];
        if (count($bookingFields) > 0) {
            for ($rAux=0; $rAux < count($bookingFields); $rAux++) { 
                $name = $bookingFields[$rAux]['name'];
                $value = $bookingFields[$rAux]['value'];
            }
        }
        //bookedPricingCategories
        $bookedPricingCategories = $productBookings[$i]['bookedPricingCategories'];
        if (count($bookedPricingCategories) > 0) {
            for ($iAux5=0; $iAux5 < count($bookedPricingCategories); $iAux5++) { 
                $id = $bookedPricingCategories[$iAux5]['id'];
                $title = $bookedPricingCategories[$iAux5]['title'];
                $ticketCategory = $bookedPricingCategories[$iAux5]['ticketCategory'];
                $occupancy = $bookedPricingCategories[$iAux5]['occupancy'];
                $groupSize = $bookedPricingCategories[$iAux5]['groupSize'];
                $ageQualified = $bookedPricingCategories[$iAux5]['ageQualified'];
                $minAge = $bookedPricingCategories[$iAux5]['minAge'];
                $maxAge = $bookedPricingCategories[$iAux5]['maxAge'];
                $dependent = $bookedPricingCategories[$iAux5]['dependent'];
                $masterCategoryId = $bookedPricingCategories[$iAux5]['masterCategoryId'];
                $maxPerMaster = $bookedPricingCategories[$iAux5]['maxPerMaster'];
                $sumDependentCategories = $bookedPricingCategories[$iAux5]['sumDependentCategories'];
                $maxDependentSum = $bookedPricingCategories[$iAux5]['maxDependentSum'];
                $internalUseOnly = $bookedPricingCategories[$iAux5]['internalUseOnly'];
                $defaultCategory = $bookedPricingCategories[$iAux5]['defaultCategory'];
                $fullTitle = $bookedPricingCategories[$iAux5]['fullTitle'];
                $flags = $bookedPricingCategories[$iAux5]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
            }
        }
        //activity
        $activity = $productBookings[$i]['activity'];
        $activityid = $activity['id'];
        $activityexternalId = $activity['externalId'];
        $activityproductGroupId = $activity['productGroupId'];
        $activityproductCategory = $activity['productCategory'];
        $activitybox = $activity['box'];
        $activityinventoryLocal = $activity['inventoryLocal'];
        $activityinventorySupportsPricing = $activity['inventorySupportsPricing'];
        $activityinventorySupportsAvailability = $activity['inventorySupportsAvailability'];
        $activitycreationDate = $activity['creationDate'];
        $activitylastModified = $activity['lastModified'];
        $activitylastPublished = $activity['lastPublished'];
        $activitypublished = $activity['published'];
        $activitytitle = $activity['title'];
        $activitydescription = $activity['description'];
        $activityexcerpt = $activity['excerpt'];
        $cancellationPolicy = $activity['cancellationPolicy'];
        if ($cancellationPolicy != null) {
            $cancellationPolicyid = $cancellationPolicy['id'];
            $cancellationPolicytitle = $cancellationPolicy['title'];
            $defaultPolicy = $cancellationPolicy['defaultPolicy'];
            $tax = $cancellationPolicy['tax'];
            $taxid = $tax['id'];
            $taxincluded = $tax['included'];
            $taxpercentage = $tax['percentage'];
            $taxtitle = $tax['title'];
            $penaltyRules = $cancellationPolicy['penaltyRules'];
            if (count($penaltyRules) > 0) {
                for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                    $id = $penaltyRules[$iAux]['id'];
                    $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                    $charge = $penaltyRules[$iAux]['charge'];
                    $chargeType = $penaltyRules[$iAux]['chargeType'];
                }
            }
        }
        $activityoverrideBarcodeFormat = $activity['overrideBarcodeFormat'];
        $activitybarcodeType = $activity['barcodeType'];
        $activitytimeZone = $activity['timeZone'];
        $activityslug = $activity['slug'];
        $activitybaseLanguage = $activity['baseLanguage'];
        $activityboxedVendor = $activity['boxedVendor'];
        $activitystoredExternally = $activity['storedExternally'];
        $activitypluginId = $activity['pluginId'];
        $activityreviewRating = $activity['reviewRating'];
        $activityreviewCount = $activity['reviewCount'];
        $activityactivityType = $activity['activityType'];
        $activitybookingType = $activity['bookingType'];
        $activityscheduleType = $activity['scheduleType'];
        $activitycapacityType = $activity['capacityType'];
        $activitypassExpiryType = $activity['passExpiryType'];
        $activityfixedPassExpiryDate = $activity['fixedPassExpiryDate'];
        $activitymeetingType = $activity['meetingType'];
        $activityprivateActivity = $activity['privateActivity'];
        $activitypassCapacity = $activity['passCapacity'];
        $activitypassValidForDays = $activity['passValidForDays'];
        $activitypassesAvailable = $activity['passesAvailable'];
        $activitydressCode = $activity['dressCode'];
        $activitypassportRequired = $activity['passportRequired'];
        $activityincluded = $activity['included'];
        $activityexcluded = $activity['excluded'];
        $activityrequirements = $activity['requirements'];
        $activityattention = $activity['attention'];
        $activitylocationCode = $activity['locationCode'];
        $activitybookingCutoffMinutes = $activity['bookingCutoffMinutes'];
        $activitybookingCutoffHours = $activity['bookingCutoffHours'];
        $activitybookingCutoffDays = $activity['bookingCutoffDays'];
        $activitybookingCutoffWeeks = $activity['bookingCutoffWeeks'];
        $activityrequestDeadlineMinutes = $activity['requestDeadlineMinutes'];
        $activityrequestDeadlineHours = $activity['requestDeadlineHours'];
        $activityrequestDeadlineDays = $activity['requestDeadlineDays'];
        $activityrequestDeadlineWeeks = $activity['requestDeadlineWeeks'];
        $activityboxedActivityId = $activity['boxedActivityId'];
        $activitycomboActivity = $activity['comboActivity'];
        $activityticketPerComboComponent = $activity['ticketPerComboComponent'];
        $activitypickupActivityId = $activity['pickupActivityId'];
        $activityallowCustomizedBookings = $activity['allowCustomizedBookings'];
        $activitydayBasedAvailability = $activity['dayBasedAvailability'];
        $activityselectFromDayOptions = $activity['selectFromDayOptions'];
        $activitydefaultRateId = $activity['defaultRateId'];
        $activityticketPerPerson = $activity['ticketPerPerson'];
        $activitydurationType = $activity['durationType'];
        $activityduration = $activity['duration'];
        $activitydurationMinutes = $activity['durationMinutes'];
        $activitydurationHours = $activity['durationHours'];
        $activitydurationDays = $activity['durationDays'];
        $activitydurationWeeks = $activity['durationWeeks'];
        $activitydurationText = $activity['durationText'];
        $activityminAge = $activity['minAge'];
        $activitynextDefaultPrice = $activity['nextDefaultPrice'];
        $activitynextDefaultPriceMoney = $activity['nextDefaultPriceMoney'];
        $activitypickupService = $activity['pickupService'];
        $activitypickupAllotment = $activity['pickupAllotment'];
        $activitypickupAllotmentType = $activity['pickupAllotmentType'];
        $activityuseComponentPickupAllotments = $activity['useComponentPickupAllotments'];
        $activitycustomPickupAllowed = $activity['customPickupAllowed'];
        $activitypickupMinutesBefore = $activity['pickupMinutesBefore'];
        $activitynoPickupMsg = $activity['noPickupMsg'];
        $activityticketMsg = $activity['ticketMsg'];
        $activityshowGlobalPickupMsg = $activity['showGlobalPickupMsg'];
        $activityshowNoPickupMsg = $activity['showNoPickupMsg'];
        $activitydropoffService = $activity['dropoffService'];
        $activitycustomDropoffAllowed = $activity['customDropoffAllowed'];
        $activityuseSameAsPickUpPlaces = $activity['useSameAsPickUpPlaces'];
        $activitydifficultyLevel = $activity['difficultyLevel'];
        $activityhasOpeningHours = $activity['hasOpeningHours'];
        $activitydefaultOpeningHours = $activity['defaultOpeningHours'];
        $activityhasBoxes = $activity['hasBoxes'];
        $activityrequestDeadline = $activity['requestDeadline'];
        $activitybookingCutoff = $activity['bookingCutoff'];
        $activityactualId = $activity['actualId'];
        $activitynextDefaultPriceAsText = $activity['nextDefaultPriceAsText'];
        //
        $mainContactFields = $activity['mainContactFields'];
        if (count($mainContactFields) > 0) {
            for ($k=0; $k < count($mainContactFields); $k++) { 
                $field = $mainContactFields[$k]['field'];
                $required = $mainContactFields[$k]['required'];
            }
        }
        $requiredCustomerFields = $activity['requiredCustomerFields'];
        if (count($requiredCustomerFields) > 0) {
            $customerfields = "";
            for ($l=0; $l < count($requiredCustomerFields); $l++) { 
                $customfields = $requiredCustomerFields[$l];
            }
        }
        $keywords = $activity['keywords'];
        $flags = $activity['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                $flag = $flags[$iAux9];
            }
        }
        $languages = $activity['languages'];
        if (count($languages) > 0) {
            $language = "";
            for ($l=0; $l < count($languages); $l++) { 
                $language = $languages[$l];
            }
        }
        $paymentCurrencies = $activity['paymentCurrencies'];
        if (count($paymentCurrencies) > 0) {
            $payment = "";
            for ($z=0; $z < count($paymentCurrencies); $z++) { 
                $payment = $paymentCurrencies[$z];
            }
        }
        $customFields = $activity['customFields'];
        if (count($customFields) > 0) {
            for ($j=0; $j < count($customFields); $j++) { 
                $type = $customFields[$j]['title'];
                $inputFieldId = $customFields[$j]['inputFieldId'];
                $value = $customFields[$j]['value'];
            }
        }
        $tagGroups = $activity['tagGroups'];
        $categories = $activity['categories'];
        $keyPhoto = $activity['keyPhoto'];
        $keyPhotoid = $keyPhoto['id'];
        $keyPhotooriginalUrl = $keyPhoto['originalUrl'];
        $keyPhotodescription = $keyPhoto['description'];
        $keyPhotoalternateText = $keyPhoto['alternateText'];
        $keyPhotoheight = $keyPhoto['height'];
        $keyPhotowidth = $keyPhoto['width'];
        $keyPhotofileName = $keyPhoto['fileName'];
        $derived = $keyPhoto['derived'];
        if (count($derived) > 0) {
            for ($d=0; $d < count($derived); $d++) { 
                $name = $derived[$d]['name'];
                $url = $derived[$d]['url'];
                $cleanUrl = $derived[$d]['cleanUrl'];
            }
        }
        $photos = $activity['photos'];
        if (count($photos) > 0) {
            for ($f=0; $f < count($photos); $f++) { 
                $photosid = $photos[$f]['id'];
                $photosoriginalUrl = $photos[$f]['originalUrl'];
                $photosdescription = $photos[$f]['description'];
                $photosalternateText = $photos[$f]['alternateText'];
                $photosheight = $photos[$f]['height'];
                $photoswidth = $photos[$f]['width'];
                $photosfileName = $photos[$f]['fileName'];
                $derived = $photos[$f]['derived'];
                if (count($derived) > 0) {
                    for ($d=0; $d < count($derived); $d++) { 
                        $name = $derived[$d]['name'];
                        $url = $derived[$d]['url'];
                        $cleanUrl = $derived[$d]['cleanUrl'];
                    }
                }
            }
        }
        $videos = $activity['videos'];
        $vendor = $activity['vendor'];
        $vendorid = $vendor['id'];
        $vendortitle = $vendor['title'];
        $vendorcurrencyCode = $vendor['currencyCode'];
        $vendortimeZone = $vendor['timeZone'];
        $vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
        $vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
        $vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
        $vendorcompanyEmailIsDefault = $vendor['companyEmailIsDefault'];
        $startPoints = $activity['startPoints'];
        if (count($startPoints) > 0) {
            for ($c=0; $c < count($startPoints); $c++) { 
                $startpointsid = $startPoints[$c]['id'];
                $type = $startPoints[$c]['type'];
                $title = $startPoints[$c]['title'];
                $code = $startPoints[$c]['code'];
                $pickupTicketDescription = $startPoints[$c]['pickupTicketDescription'];
                $dropoffTicketDescription = $startPoints[$c]['dropoffTicketDescription'];
                $address = $startPoints[$c]['address'];
                $addressid = $address['id'];
                $addressLine1 = $address['addressLine1'];
                $addressLine2 = $address['addressLine2'];
                $addressLine3 = $address['addressLine3'];
                $city = $address['city'];
                $state = $address['state'];
                $postalCode = $address['postalCode'];
                $countryCode = $address['countryCode'];
                $mapZoomLevel = $address['mapZoomLevel'];
                $origin = $address['origin'];
                $originId = $address['originId'];
                $geoPoint = $address['geoPoint'];
                $latitude = $geoPoint['latitude'];
                $longitude = $geoPoint['longitude'];
                $unLocode = $address['unLocode'];
                $unlocodecountry = $unLocode['country'];
                $unlocodecity = $unLocode['city'];
                $created = $address['created'];
                if (count($created) > 0) {
                    for ($cAux=0; $cAux < count($created); $cAux++) { 
                        $created = $created[$cAux];
                    }
                }
        
                $created = $startPoints[$c]['created'];
                if (count($created) > 0) {
                    for ($cAux=0; $cAux < count($created); $cAux++) { 
                        $created = $created[$cAux];
                    }
                }
            }
        }
        $bookingQuestions = $activity['bookingQuestions'];
        if (count($bookingQuestions) > 0) {
            for ($x=0; $x < count($bookingQuestions); $x++) { 
                $bookingquestionsid = $bookingQuestions[$x]['id'];
                $personalData = $bookingQuestions[$x]['personalData'];
                $questionCode = $bookingQuestions[$x]['questionCode'];
                $label = $bookingQuestions[$x]['label'];
                $help = $bookingQuestions[$x]['help'];
                $placeholder = $bookingQuestions[$x]['placeholder'];
                $required = $bookingQuestions[$x]['required'];
                $defaultValue = $bookingQuestions[$x]['defaultValue'];
                $dataType = $bookingQuestions[$x]['dataType'];
                $selectFromOptions = $bookingQuestions[$x]['selectFromOptions'];
                $selectMultiple = $bookingQuestions[$x]['selectMultiple'];
                $context = $bookingQuestions[$x]['context'];
                $pricingCategoryTriggerSelection = $bookingQuestions[$x]['pricingCategoryTriggerSelection'];
                $rateTriggerSelection = $bookingQuestions[$x]['rateTriggerSelection'];
                $extraTriggerSelection = $bookingQuestions[$x]['extraTriggerSelection'];  
                $options = $bookingQuestions[$x]['options'];
                if (count($options) > 0) {
                    for ($xAux=0; $xAux < count($options); $xAux++) { 
                        $name = $options[$xAux]['name'];
                        $value = $options[$xAux]['value'];
                    }
                }
            }
        }
        $passengerFields = $activity['passengerFields'];
        if (count($passengerFields) > 0) {
            for ($y=0; $y < count($passengerFields); $y++) { 
                $field = $passengerFields[$y]['field'];
                $required = $passengerFields[$y]['required'];
            }
        }
        $inclusions = $activity['inclusions'];
        $exclusions = $activity['exclusions'];
        $googlePlace = $activity['googlePlace'];
        $googlePlacecountry = $googlePlace['country'];
        $googlePlacecountryCode = $googlePlace['countryCode'];
        $googlePlacecity = $googlePlace['city'];
        $googlePlacecityCode = $googlePlace['cityCode'];
        $geoLocationCenter = $googlePlace['geoLocationCenter'];
        $lat = $geoLocationCenter['lat'];
        $lng = $geoLocationCenter['lng'];
        $resourceSlots = $activity['resourceSlots'];
        $comboParts = $activity['comboParts'];
        $ticketComboComponents = $activity['ticketComboComponents'];
        $dayOptions = $activity['dayOptions'];
        $activityCategories = $activity['activityCategories'];
        if (count($activityCategories) > 0) {
            $activity = "";
            for ($w=0; $w < count($activityCategories); $w++) { 
                $activity = $activityCategories[$w];
            }
        }
        $activityAttributes = $activity['activityAttributes'];
        $guidanceTypes = $activity['guidanceTypes'];
        if (count($guidanceTypes) > 0) {
            for ($s=0; $s < count($guidanceTypes); $s++) { 
                $guidancetypesid = $guidanceTypes[$s]['id'];
                $guidanceType = $guidanceTypes[$s]['guidanceType'];
                $created = $guidanceTypes[$s]['created'];
                if (count($created) > 0) {
                    for ($cAux=0; $cAux < count($created); $cAux++) { 
                        $created = $created[$cAux];
                    }
                }
                $languages = $guidanceTypes[$s]['languages'];
                if (count($languages) > 0) {
                    $language = "";
                    for ($cAux=0; $cAux < count($languages); $cAux++) { 
                        $language = $languages[$cAux];
                    }
                }
            }
        }
        $rates = $activity['rates'];
        if (count($rates) > 0) {
            for ($r=0; $r < count($rates); $r++) { 
                $ratesid = $rates[$r]['id'];
                $title = $rates[$r]['title'];
                $description = $rates[$r]['description'];
                $index = $rates[$r]['index'];
                $rateCode = $rates[$r]['rateCode'];
                $pricedPerPerson = $rates[$r]['pricedPerPerson'];
                $minPerBooking = $rates[$r]['minPerBooking'];
                $maxPerBooking = $rates[$r]['maxPerBooking'];
                $fixedPassExpiryDate = $rates[$r]['fixedPassExpiryDate'];
                $passValidForDays = $rates[$r]['passValidForDays'];
                $pickupSelectionType = $rates[$r]['pickupSelectionType'];
                $pickupPricingType = $rates[$r]['pickupPricingType'];
                $pickupPricedPerPerson = $rates[$r]['pickupPricedPerPerson'];
                $dropoffSelectionType = $rates[$r]['dropoffSelectionType'];
                $dropoffPricingType = $rates[$r]['dropoffPricingType'];
                $dropoffPricedPerPerson = $rates[$r]['dropoffPricedPerPerson'];
                $allStartTimes = $rates[$r]['allStartTimes'];
                $tieredPricingEnabled = $rates[$r]['tieredPricingEnabled'];
                $allPricingCategories = $rates[$r]['allPricingCategories'];
                $cancellationPolicy = $rates[$r]['cancellationPolicy'];
                $cancellationPolicyid = $cancellationPolicy['id'];
                $cancellationPolicytitle = $cancellationPolicy['title'];
                $cancellationPolicytax = $cancellationPolicy['tax'];
                $defaultPolicy = $cancellationPolicy['defaultPolicy'];
                $penaltyRules = $cancellationPolicy['penaltyRules'];
                if (count($penaltyRules) > 0) {
                    for ($i=0; $i < count($penaltyRules); $i++) { 
                        $penaltyrulesid = $penaltyRules[$i]['id'];
                        $chargeType = $penaltyRules[$i]['chargeType'];
                        $charge = $penaltyRules[$i]['charge'];
                        $cutoffHours = $penaltyRules[$i]['cutoffHours'];
                    }
                }
                $extraConfigs = $rates[$r]['extraConfigs'];
                if (count($extraConfigs) > 0) {
                    for ($z=0; $z < count($extraConfigs); $z++) { 
                        $extraconfigsid = $extraConfigs[$z]['id'];
                        $activityExtraId = $extraConfigs[$z]['activityExtraId'];
                        $selectionType = $extraConfigs[$z]['selectionType'];
                        $pricingType = $extraConfigs[$z]['pricingType'];
                        $pricedPerPerson = $extraConfigs[$z]['pricedPerPerson'];       
                        $created = $extraConfigs[$z]['created'];
                        if (count($created) > 0) {
                            for ($cAux=0; $cAux < count($created); $cAux++) { 
                                $created = $created[$cAux];
                            }
                        }
                    }
                }
                $startTimeIds = $rates[$r]['startTimeIds'];
                if (count($startTimeIds) > 0) {
                    $start = "";
                    for ($st=0; $st < count($startTimeIds); $st++) { 
                        $start = $startTimeIds[$st];
                    }
                }
                $pricingCategoryIds = $rates[$r]['pricingCategoryIds'];
                if (count($pricingCategoryIds) > 0) {
                    $pricing = "";
                    for ($p=0; $p < count($pricingCategoryIds) ; $p++) { 
                        $pricing = $pricingCategoryIds[$p];
                    }
                }
            }
        }
        $pickupFlags = $activity['pickupFlags'];
        $pickupPlaceGroups = $activity['pickupPlaceGroups'];
        $dropoffFlags = $activity['dropoffFlags'];
        $dropoffPlaceGroups = $activity['dropoffPlaceGroups'];
        $pricingCategories = $activity['pricingCategories'];
        if (count($pricingCategories) > 0) {
            for ($p=0; $p < count($pricingCategories); $p++) { 
                $pricingcategoriesid = $pricingCategories[$p]['id'];
                $title = $pricingCategories[$p]['title'];
                $ticketCategory = $pricingCategories[$p]['ticketCategory'];
                $occupancy = $pricingCategories[$p]['occupancy'];
                $groupSize = $pricingCategories[$p]['groupSize'];
                $ageQualified = $pricingCategories[$p]['ageQualified'];
                $minAge = $pricingCategories[$p]['minAge'];
                $maxAge = $pricingCategories[$p]['maxAge'];
                $dependent = $pricingCategories[$p]['dependent'];
                $masterCategoryId = $pricingCategories[$p]['masterCategoryId'];
                $maxPerMaster = $pricingCategories[$p]['maxPerMaster'];
                $sumDependentCategories = $pricingCategories[$p]['sumDependentCategories'];
                $maxDependentSum = $pricingCategories[$p]['maxDependentSum'];
                $internalUseOnly = $pricingCategories[$p]['internalUseOnly'];
                $defaultCategory = $pricingCategories[$p]['defaultCategory'];
                $fullTitle = $pricingCategories[$p]['fullTitle'];
            }
        }
        $agendaItems = $activity['agendaItems'];
        if (count($agendaItems)  > 0) {
            for ($a=0; $a < count($agendaItems); $a++) { 
                $agendaitemsid = $agendaItems[$a]['id'];
                $index = $agendaItems[$a]['index'];
                $title = $agendaItems[$a]['title'];
                $excerpt = $agendaItems[$a]['excerpt'];
                $body = $agendaItems[$a]['body'];
                $day = $agendaItems[$a]['day'];
                $address = $agendaItems[$a]['address'];
                $keyPhoto = $agendaItems[$a]['keyPhoto'];
                $location = $agendaItems[$a]['location'];
                $locationaddress = $location['address'];
                $city = $location['city'];
                $countryCode = $location['countryCode'];
                $postCode = $location['postCode'];
                $latitude = $location['latitude'];
                $longitude = $location['longitude'];
                $zoomLevel = $location['zoomLevel'];
                $origin = $location['origin'];
                $originId = $location['originId'];
                $wholeAddress = $location['wholeAddress'];
            }
        }
        $startTimes = $activity['startTimes'];
        if (count($startTimes) > 0) {
            for ($s=0; $s < count($startTimes); $s++) { 
                $starttimesid = $startTimes[$s]['id'];
                $label = $startTimes[$s]['label'];
                $hour = $startTimes[$s]['hour'];
                $minute = $startTimes[$s]['minute'];
                $overrideTimeWhenPickup = $startTimes[$s]['overrideTimeWhenPickup'];
                $pickupHour = $startTimes[$s]['pickupHour'];
                $pickupMinute = $startTimes[$s]['pickupMinute'];
                $durationType = $startTimes[$s]['durationType'];
                $voucherPickupMsg = $startTimes[$s]['voucherPickupMsg'];
                $externalId = $startTimes[$s]['externalId'];
                $duration = $startTimes[$s]['duration'];
                $durationMinutes = $startTimes[$s]['durationMinutes'];
                $durationHours = $startTimes[$s]['durationHours'];
                $durationDays = $startTimes[$s]['durationDays'];
                $durationWeeks = $startTimes[$s]['durationWeeks'];
                $flags = $startTimes[$s]['flags'];
                if (count($flags) > 0) {
                    for ($sAux=0; $sAux < count($flags); $sAux++) { 
                        $flags = $flags[$sAux];
                    }
                }
            }
        }
        $bookableExtras = $activity['bookableExtras'];
        if (count($bookableExtras) > 0) {
            for ($b=0; $b < count($bookableExtras); $b++) { 
                $bookableextrasid = $bookableExtras[$b]['id'];
                $externalId = $bookableExtras[$b]['externalId'];
                $title = $bookableExtras[$b]['title'];
                $information = $bookableExtras[$b]['information'];
                $included = $bookableExtras[$b]['included'];
                $free = $bookableExtras[$b]['free'];
                $productGroupId = $bookableExtras[$b]['productGroupId'];
                $pricingType = $bookableExtras[$b]['pricingType'];
                $pricingTypeLabel = $bookableExtras[$b]['pricingTypeLabel'];
                $price = $bookableExtras[$b]['price'];
                $increasesCapacity = $bookableExtras[$b]['increasesCapacity'];
                $maxPerBooking = $bookableExtras[$b]['maxPerBooking'];
                $limitByPax = $bookableExtras[$b]['limitByPax'];
            }
        }
        $route = $activity['route'];
        $mapZoomLevel = $route['mapZoomLevel'];
        $center = $route['center'];
        $centerlat = $center['lat'];
        $centerlng = $center['lng'];
        $start = $route['start'];
        $startlat = $start['lat'];
        $startlng = $start['lng'];
        $end = $route['end'];
        $endlat = $end['lat'];
        $endlng = $end['lng'];
        $seasonalOpeningHours = $activity['seasonalOpeningHours'];
        $displaySettings = $activity['displaySettings'];
        $showPickupPlaces = $displaySettings['showPickupPlaces'];
        $showRouteMap = $displaySettings['showRouteMap'];
        $selectRateBasedOnStartTime = $displaySettings['selectRateBasedOnStartTime'];
        $customFields = $displaySettings['customFields'];
        if (count($customFields) > 0) {
            for ($j=0; $j < count($customFields); $j++) { 
                $type = $customFields[$j]['title'];
                $inputFieldId = $customFields[$j]['inputFieldId'];
                $value = $customFields[$j]['value'];
            }
        }
        $actualVendor = $activity['actualVendor'];
        $actualVendorid = $actualVendor['id'];
        $actualVendortitle = $actualVendor['title'];
        $actualVendorcurrencyCode = $actualVendor['currencyCode'];
        $actualVendortimeZone = $actualVendor['timeZone'];
        $showInvoiceIdOnTicket = $actualVendor['showInvoiceIdOnTicket'];
        $showAgentDetailsOnTicket = $actualVendor['showAgentDetailsOnTicket'];
        $showPaymentsOnInvoice = $actualVendor['showPaymentsOnInvoice'];
        $companyEmailIsDefault = $actualVendor['companyEmailIsDefault'];
        //quantityByPricingCategory
        $quantityByPricingCategory = $productBookings[$i]['quantityByPricingCategory'];
        $quantity = $quantityByPricingCategory[$pricingCategoryId];
    }
}
echo $return;
echo "ID: " . $id;
echo $return;

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>