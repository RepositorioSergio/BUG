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

$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$ip = $_SERVER['HTTP_CLIENT_IP'] ? $_SERVER['HTTP_CLIENT_IP'] : ($_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);

$token = "eyJhbGciOiJIUzI1NiIsInppcCI6IkdaSVAifQ.H4sIAAAAAAAAAKtWyiwuVrJSSs4vLknMTSxS0lHKTCxRsjI0tTA2NjE0NDbRUUqtKIAImBiZm4IESotTi_ISc1NB-nIyk7MT81KS8vOzlWoBUTmexU4AAAA.gGlXQgxflJc1ddz7dBAoEutAWogyQwwS0VfjQx-I_fE";
$url = 'http://test-api-zneith.zdev.tech/booking/book';

$raw = '{
    "pos": {
      "source": [
        {
          "requestorID": {
            "id": "2113855",
            "messagePassword": "' . $token . '"
          },
          "pseudoCityCode": "FLL6C2102",
          "erspuserID": "100",
          "terminalID": "' . $ip . '"
        }
      ]
    },
    "airItinerary": {
      "originDestinationOptions": {
        "originDestinationOption": [
          {
            "flightSegment": [
              {
                "departureAirport": {
                  "locationCode": "LIM",
                  "codeContext": "Lima, Lima, Perú (LIM)"
                },
                "arrivalAirport": {
                  "locationCode": "CUZ",
                  "codeContext": "Aeropuerto Velazco Astete, Cusco, Perú (CUZ)"
                },
                "departureDateTime": "2020-06-10T04:30:00.000-0500",
                "arrivalDateTime": "2020-06-10T05:51:00.000-0500",
                "operatingAirline": {
                  "code": "",
                  "companyShortName": "LATAM AIRLINES PERU"
                },
                "marketingAirline": {
                  "code": "LA",
                  "companyShortName": "LATAM Airlines"
                },
                "connectionType": "S",
                "flightNumber": "2025",
                "equipment": [
                  {
                    "airEquipType": "320"
                  }
                ],
                "stopLocation": [],
                "bookingClassAvails": [
                  {
                    "bookingClassAvail": [
                      {
                        "resBookDesigCode": "V"
                      }
                    ],
                    "cabinType": "M"
                  }
                ],
                "tpaextensions": {
                  "any": [
                    "<?xml version=\"1.0\" encoding=\"UTF-16\"?>\n<segmentDetails><elapsedTime>0121</elapsedTime><fareProductDetail fareBasis=\"V00SL5ZV\" fareType=\"RP\" passengerType=\"ADT\"/></segmentDetails>"
                  ]
                },
                "resBookDesigCode": "",
                "marriageGrp": "",
                "fareBasisCode": "V00SL5ZV",
                "rph": ""
              },
              {
                "connectionType": "F",
                "tpaextensions": {
                  "any": [
                    "<?xml version=\"1.0\" encoding=\"UTF-16\"?>\n<flightDetails><elapsedTime>0121</elapsedTime><brandedFare brandID=\"SL\"/><baggageInformationList><baggageInformation pieces=\"0\"/></baggageInformationList><handBaggage description=\"\" pieces=\"1\"/><connectionLocationList><connectionLocation codeContext=\"Lima, Lima, Perú (LIM)\" locationCode=\"LIM\" minChangeTime=\"81\"/></connectionLocationList></flightDetails>"
                    ]
                  }
              }
            ],
            "refNumber": 0,
            "rph": "01"
          },
          {
            "flightSegment": [
            {
                "departureAirport": {
                  "locationCode": "CUZ",
                  "codeContext": "Aeropuerto Velazco Astete, Cusco, Perú (CUZ)"
                },
                "arrivalAirport": {
                  "locationCode": "LIM",
                  "codeContext": "Lima, Lima, Perú (LIM)"
                },
                "departureDateTime": "2020-06-20T06:26:00.000-0500",
                "arrivalDateTime": "2020-06-20T07:50:00.000-0500",
                "operatingAirline": {
                  "code": "",
                  "companyShortName": "LATAM AIRLINES PERU"
                },
                "marketingAirline": {
                  "code": "LA",
                  "companyShortName": "LATAM Airlines"
                },
                "connectionType": "S",
                "flightNumber": "2024",
                "equipment": [
                  {
                    "airEquipType": "319"
                  }
                ],
                "stopLocation": [],
                "bookingClassAvails": [
                  {
                    "bookingClassAvail": [
                      {
                        "resBookDesigCode": "V"
                      }
                    ],
                    "cabinType": "M"
                  }
                ],
                "tpaextensions": {
                  "any": [
                    "<?xml version=\"1.0\" encoding=\"UTF-16\"?>\n<segmentDetails><elapsedTime>0124</elapsedTime><fareProductDetail fareBasis=\"V00SL5ZV\" fareType=\"RP\" passengerType=\"ADT\"/></segmentDetails>"
                  ]
                },
                "resBookDesigCode": "",
                "marriageGrp": "",
                "fareBasisCode": "V00SL5ZV",
                "rph": ""
              }, 
              {
                "connectionType": "F",
                "tpaextensions": {
                  "any": [
                    "<?xml version=\"1.0\" encoding=\"UTF-16\"?>\n<flightDetails><elapsedTime>0124</elapsedTime><brandedFare brandID=\"SL\"/><baggageInformationList><baggageInformation pieces=\"0\"/></baggageInformationList><handBaggage description=\"\" pieces=\"1\"/><connectionLocationList><connectionLocation codeContext=\"Aeropuerto Velazco Astete, Cusco, Perú (CUZ)\" locationCode=\"CUZ\" minChangeTime=\"84\"/></connectionLocationList></flightDetails>"
                  ]
                }
              }
            ],
            "refNumber": 1,
            "rph": "11"
          }
        ]
      }
    },
    "priceInfo": {
      "itinTotalFare": [
        {
          "baseFare": {
            "amount": "202.00"
          },
          "taxes": {
            "amount": "52.14"
          },
          "totalFare": {
            "amount": "254.14"
          },
          "fees": {
            "fee": []
          },
          "discounts": {
            "discount": []
          },
          "fareBaggageAllowance": [
            {
              "unitOfMeasure": "1"
            },
            {
              "unitOfMeasure": "1"
            }
          ],
          "remark": []
        }
      ],
      "ptcfareBreakdowns": {
        "ptcfareBreakdown": [
          {
            "passengerTypeQuantity": {
              "code": "ADT"
            },
            "passengerFare": [
              {
                "baseFare": {
                  "amount": "202.00"
                },
                "taxes": {
                  "amount": "52.14"
                },
                "totalFare": {
                  "amount": "254.14"
                }
              }
            ]
          }
        ]
      },
      "pricingSource": "PUBLISHED",
      "priceRequestInformation": {
        "fareQualifier": "DOMESTIC"
      },
      "validatingAirlineCode": "LA",
      "fareInfos": {
        "fareInfo": [
          {
            "fareInfo": [
              {
                "fareType": "SL"
              }
            ]
          }
        ]
      }
    },
    "travelerInfo": {
      "airTraveler": [
        {
          "personName": {
            "givenName": ["TEST"],
            "surname": "TEST"
          },
          "gender": "M",
          "document": [
            {
              "docID": "662435433",
              "docType": "NI",
              "birthCountry": "PE",
              "birthDate": 874126800000
            }
          ],
          "passengerTypeCode": "ADT"
        }
      ]
    },
    "fulfillment": {
      "paymentDetails": {
        "paymentDetail": [
          {
            "paymentType": 103,
            "paymentAmount": [
              {
                "amount": "254.14"
              }
            ]
          }
        ]
      },
      "name": {
        "givenName": ["TEST"],
        "surname": "TEST"
      }
    },
    "target": "http://localhost:5000/payment/result?id=' . $token . '",
    "primaryLangID": "es",
    "tpaextensions": {
      "any": [
        "<?xml version=\"1.0\" encoding=\"UTF-16\"?>\n<additionalInfo><contactInformation phoneNumber=\"13213132\" locationCode=\"PE\" countryAccessCode=\"51\" email=\"zdev@costamar.com\" /><splitPayment totalPaid=\"400.00\" milesUsed=\"2000\" /></additionalInfo>"
      ]
    }
  }';

$headers = array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
    'Content-Length: ' . strlen($raw)
); 
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo '<xmp>';
var_dump($response);
echo '</xmp>';
$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$transactionIdentifier = $response['transactionIdentifier'];
$transactionStatusCode = $response['transactionStatusCode'];
$successAndWarningsAndAirReservation = $response['successAndWarningsAndAirReservation'];
if (count($successAndWarningsAndAirReservation) > 0) {
  for ($i=0; $i < count($successAndWarningsAndAirReservation); $i++) { 
    $fulfillment = $successAndWarningsAndAirReservation[$i]['fulfillment'];
    $paymentDetails = $fulfillment['paymentDetails'];
    $paymentDetail = $paymentDetails['paymentDetail'];
    if (count($paymentDetail) > 0) {
      for ($iAux=0; $iAux < count($paymentDetail); $iAux++) { 
        $rph = $paymentDetail[$iAux]['rph'];
        $paymentTransactionTypeCode = $paymentDetail[$iAux]['paymentTransactionTypeCode'];
        $paymentType = $paymentDetail[$iAux]['paymentType'];
        $paymentAmount = $paymentDetail[$iAux]['paymentAmount'];
      }
    }
    $paymentText = $fulfillment['paymentText'];
    $ticketing = $successAndWarningsAndAirReservation[$i]['ticketing'];
    if (count($ticketing) > 0) {
      for ($iAux2=0; $iAux2 < count($ticketing); $iAux2++) { 
        $ticketTimeLimit = $ticketing[$iAux2]['ticketTimeLimit'];
        $ticketAdvisory = $ticketing[$iAux2]['ticketAdvisory'];
        $flightSegmentRefNumber = $ticketing[$iAux2]['flightSegmentRefNumber'];
        $travelerRefNumber = $ticketing[$iAux2]['travelerRefNumber'];
        $miscTicketingCode = $ticketing[$iAux2]['miscTicketingCode'];
      }
    }
    $bookingReferenceID = $successAndWarningsAndAirReservation[$i]['bookingReferenceID'];
    if (count($bookingReferenceID) > 0) {
      for ($iAux3=0; $iAux3 < count($bookingReferenceID); $iAux3++) { 
        $id = $bookingReferenceID[$iAux3]['id'];
        $flightRefNumberRPHList = $bookingReferenceID[$iAux3]['flightRefNumberRPHList'];
      }
    }
    $emdinfo = $successAndWarningsAndAirReservation[$i]['emdinfo'];
  }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>