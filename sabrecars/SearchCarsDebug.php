<?php 
$response = '{
  "OTA_VehAvailRateRS" : {
    "Version" : "2.4.1",
    "ApplicationResults" : {
      "status" : "Complete",
      "Success" : [ {
        "timeStamp" : "2016-05-24T10:12:01-05:00"
      } ]
    },
    "VehAvailRSCore" : {
      "HeaderInfo" : {
        "Text" : [ "*RATES RETURNED VIA DIRECT CONNECT", "R C USD RATE/PLAN MI CHG APPROX", "ALL TOTAL PRICES ARE RETURNED DIRECT CONNECT FROM CAR ASSOCIATE", "* BEST PUBLICLY AVAILABLE RATE", "C COMMISSION FX FIXED 05 PERCENTAGE BLANK-COMM UNKNOWN", "R RATE AND/OR VEHICLE ON REQUEST ‡ CURRENCY CONVERTED", "- AMOUNT TOO LARGE P DEPOSIT REQUIRED", "G GUARANTEE REQUIRED C CONTRACT RATE", "L INCLUSIVE RATE", "PLAN D-DAILY E-WEEKEND W-WEEKLY M-MONTHLY B-BUNDLED", "CQ*R LINE RATE DETAILS AND RULES", "CQ*P LOCATION INFO IE.MAKES,EQUIPMENT,PAYMENT AND MORE", "CQ*X EXTRA DAY AND HOUR RATES", "0C LINE SELL CAR", "MIN AGE 21 - MOST CAR CLASSES. 21-24 RATE DIFFERENTIAL", "FOR RENTERS UNDER 25 YRS OF AGE SEE KEYWORD AGE" ]
      },
      "VehRentalCore" : {
        "NumDays" : "9",
        "NumHours" : "0",
        "PickUpDateTime" : "12-21T09:00",
        "ReturnDateTime" : "12-29T11:00",
        "DropOffLocationDetails" : {
          "LocationCode" : "DFW"
        },
        "LocationDetails" : {
          "CounterLocation" : "A",
          "LocationCode" : "DFW",
          "LocationName" : "DALLAS FT WORTH",
          "LocationOwner" : "C",
          "OperationSchedule" : {
            "OperationTimes" : {
              "OperationTime" : [ {
                "Start" : "00:00",
                "End" : "23:59"
              } ]
            }
          }
        }
      },
      "VehVendorAvails" : {
        "VehVendorAvail" : [ {
          "RPH" : "1",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "ECAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "311.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "44.49"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "22.25"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "590.88",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "2",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "CCAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "319.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "45.64"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "22.82"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "604.06",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "3",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "ICAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "334.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "47.78"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "23.89"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "628.69",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "4",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "ICAH" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "299.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "42.78"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "21.39"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "571.19",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "5",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "SCAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "345.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "49.35"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "24.68"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "646.75",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "6",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "FCAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "345.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "49.35"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "24.68"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "646.75",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "7",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "PBAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "333.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "47.64"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "23.82"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "627.06",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "8",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "PCAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "621.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "88.78"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "44.39"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "1100.18",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "9",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "LCAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "610.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "87.21"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "43.61"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "1082.11",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "10",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "LDAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "573.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "81.93"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "40.97"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "1021.34",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "11",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "IFAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "355.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "50.78"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "25.39"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "663.19",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "12",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "SFAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "365.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "52.22"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "26.11"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "679.64",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "13",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "FFAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "598.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "85.50"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "42.75"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "1062.40",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "14",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "PFAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "643.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "91.93"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "45.97"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "1136.34",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        }, {
          "RPH" : "15",
          "VehAvailCore" : {
            "RentalRate" : {
              "AvailabilityStatus" : "S",
              "RateCode" : "MCLW",
              "STM_RatePlan" : "W",
              "Vehicle" : {
                "VehType" : [ "LFAR" ]
              }
            },
            "VehicleCharges" : {
              "VehicleCharge" : {
                "Amount" : "773.49",
                "CurrencyCode" : "USD",
                "GuaranteeInd" : "G",
                "AdditionalDayHour" : {
                  "Day" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "110.50"
                  },
                  "Hour" : {
                    "CurrencyCode" : "USD",
                    "MileageAllowance" : "UNL",
                    "Rate" : "55.25"
                  }
                },
                "Mileage" : {
                  "Allowance" : "UNL",
                  "CurrencyCode" : "USD",
                  "ExtraMileageCharge" : ".00",
                  "UnitOfMeasure" : "MI"
                },
                "SpecialEquipTotalCharge" : {
                  "CurrencyCode" : "USD"
                },
                "TotalCharge" : {
                  "Amount" : "1349.90",
                  "CurrencyCode" : "USD"
                }
              }
            }
          },
          "Vendor" : {
            "Code" : "ZE",
            "CompanyShortName" : "HERTZ",
            "ParticipationLevel" : "B"
          }
        } ]
      }
    }
  },
  "Links" : [ {
    "rel" : "self",
    "href" : "https://api.sabre.com/v2.4.1/shop/cars"
  }, {
    "rel" : "linkTemplate",
    "href" : "https://api.sabre.com/<version>/shop/cars"
  } ]
}';
?>