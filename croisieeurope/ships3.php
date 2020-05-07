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
echo "COMECOU SHIPS";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.croisieeurope.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = 'en/CE/Ships.xml';
$response = file_get_contents($filename);

// echo "<xmp>";
// var_dump($response);
// echo "</xmp>";

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Catalogue = $inputDoc->getElementsByTagName("Catalogue");
if ($Catalogue->length > 0) {
    $Vendor = $Catalogue->item(0)->getElementsByTagName("Vendor");
    if ($Vendor->length > 0) {
        $VendorLicence = $Vendor->item(0)->getAttribute("Licence");
        $VendorCode = $Vendor->item(0)->getAttribute("Code");
    }
    // Style
    $Style = $Catalogue->item(0)->getElementsByTagName("Style");
    if ($Style->length > 0) {
        $Language = $Style->item(0)->getElementsByTagName("Language");
        if ($Language->length > 0) {
            $LanguageCode = $Language->item(0)->getAttribute("Code");
        }
    }
    // Brand
    $Brand = $Catalogue->item(0)->getElementsByTagName("Brand");
    if ($Brand->length > 0) {
        $Code = $Brand->item(0)->getElementsByTagName("Code");
        if ($Code->length > 0) {
            $BrandRole = $Code->item(0)->getAttribute("Role");
            $BrandValue = $Code->item(0)->getAttribute("Value");
        }
    }
    
    /*
     * try {
     * $sql = new Sql($db);
     * $insert = $sql->insert();
     * $insert->into('ships_catalogue');
     * $insert->values(array(
     * 'datetime_created' => time(),
     * 'datetime_updated' => 0,
     * 'vendorlicence' => $VendorLicence,
     * 'vendorcode' => $VendorCode,
     * 'languagecode' => $LanguageCode,
     * 'brandrole' => $BrandRole,
     * 'brandvalue' => $BrandValue
     * ), $insert::VALUES_MERGE);
     * $statement = $sql->prepareStatementForSqlObject($insert);
     * $results = $statement->execute();
     * $db->getDriver()
     * ->getConnection()
     * ->disconnect();
     * } catch (\Exception $e) {
     * echo $return;
     * echo "Error 1: " . $e;
     * echo $return;
     * }
     */
    
    // Entities
    $Entities = $Catalogue->item(0)->getElementsByTagName("Entities");
    if ($Entities->length > 0) {
        $Entity = $Entities->item(0)->getElementsByTagName("Entity");
        if ($Entity->length > 0) {
            for ($i = 0; $i < $Entity->length; $i ++) {
                $Ratings = $Entity->item($i)->getElementsByTagName("Ratings");
                if ($Ratings->length > 0) {
                    $Rating = $Ratings->item(0)->getElementsByTagName("Rating");
                    if ($Rating->length > 0) {
                        for ($iAux = 0; $iAux < $Rating->length; $iAux ++) {
                            $ID = $Rating->item($iAux)->getAttribute("ID");
                            $Value = $Rating->item($iAux)->getAttribute("Value");
                            $DeliveredBy = $Rating->item($iAux)->getAttribute("DeliveredBy");
                            $Unit = $Rating->item($iAux)->getAttribute("Unit");
                            
                            /*
                             * try {
                             * $sql = new Sql($db);
                             * $insert = $sql->insert();
                             * $insert->into('ships_catalogue_entities_ratings');
                             * $insert->values(array(
                             * 'datetime_created' => time(),
                             * 'datetime_updated' => 0,
                             * 'ratingid' => $ID,
                             * 'value' => $Value,
                             * 'deliveredby' => $DeliveredBy,
                             * 'unit' => $Unit
                             * ), $insert::VALUES_MERGE);
                             * $statement = $sql->prepareStatementForSqlObject($insert);
                             * $results = $statement->execute();
                             * $db->getDriver()
                             * ->getConnection()
                             * ->disconnect();
                             * } catch (\Exception $e) {
                             * echo $return;
                             * echo "Error 2: " . $e;
                             * echo $return;
                             * }
                             */
                        }
                    }
                }
                $Keywords = $Entity->item($i)->getElementsByTagName("Keywords");
                if ($Keywords->length > 0) {
                    $Keyword = $Keywords->item(0)->getElementsByTagName("Keyword");
                    if ($Keyword->length > 0) {
                        $word = "";
                        for ($iAux2 = 0; $iAux2 < $Keyword->length; $iAux2 ++) {
                            $ID = $Keyword->item($iAux2)->getAttribute("ID");
                            $Code = $Keyword->item($iAux2)->getAttribute("Code");
                            $Name = $Keyword->item($iAux2)->getAttribute("Name");
                            $Category = $Keyword->item($iAux2)->getAttribute("Category");
                            $Family = $Keyword->item($iAux2)->getAttribute("Family");
                            $word = $Keyword->item($iAux2)->nodeValue;
                            
                            /*
                             * try {
                             * $sql = new Sql($db);
                             * $insert = $sql->insert();
                             * $insert->into('ships_catalogue_entities_keywords');
                             * $insert->values(array(
                             * 'datetime_created' => time(),
                             * 'datetime_updated' => 0,
                             * 'keywordid' => $ID,
                             * 'code' => $Code,
                             * 'name' => $Name,
                             * 'category' => $Category,
                             * 'family' => $Family,
                             * 'keyword' => $word
                             * ), $insert::VALUES_MERGE);
                             * $statement = $sql->prepareStatementForSqlObject($insert);
                             * $results = $statement->execute();
                             * $db->getDriver()
                             * ->getConnection()
                             * ->disconnect();
                             * } catch (\Exception $e) {
                             * echo $return;
                             * echo "Error 3: " . $e;
                             * echo $return;
                             * }
                             */
                        }
                    }
                }
                $MealPlans = $Entity->item($i)->getElementsByTagName("MealPlans");
                if ($MealPlans->length > 0) {
                    $MealPlan = $MealPlans->item(0)->getElementsByTagName("MealPlan");
                    if ($MealPlan->length > 0) {
                        for ($iAux3 = 0; $iAux3 < $MealPlan->length; $iAux3 ++) {
                            $Code = $MealPlan->item($iAux3)->getAttribute("Code");
                            $ID = $MealPlan->item($iAux3)->getAttribute("ID");
                            $Code2 = $MealPlan->item($iAux3)->getElementsByTagName("Code");
                            if ($Code2->length > 0) {
                                $CodeValue = $Code2->item(0)->getAttribute("Value");
                                $CodeRole = $Code2->item(0)->getAttribute("Role");
                            }
                            
                            /*
                             * try {
                             * $sql = new Sql($db);
                             * $insert = $sql->insert();
                             * $insert->into('ships_catalogue_entities_mealplans');
                             * $insert->values(array(
                             * 'datetime_created' => time(),
                             * 'datetime_updated' => 0,
                             * 'mealplanid' => $ID,
                             * 'code' => $Code,
                             * 'codevalue' => $CodeValue,
                             * 'coderole' => $CodeRole
                             * ), $insert::VALUES_MERGE);
                             * $statement = $sql->prepareStatementForSqlObject($insert);
                             * $results = $statement->execute();
                             * $db->getDriver()
                             * ->getConnection()
                             * ->disconnect();
                             * } catch (\Exception $e) {
                             * echo $return;
                             * echo "Error 4: " . $e;
                             * echo $return;
                             * }
                             */
                        }
                    }
                }
                $Types = $Entity->item($i)->getElementsByTagName("Types");
                if ($Types->length > 0) {
                    $Type = $Types->item(0)->getElementsByTagName("Type");
                    if ($Type->length > 0) {
                        for ($iAux4 = 0; $iAux4 < $Type->length; $iAux4 ++) {
                            $Code = $Type->item($iAux4)->getAttribute("Code");
                            $ID = $Type->item($iAux4)->getAttribute("ID");
                            $Description = $Type->item($iAux4)->getElementsByTagName("Description");
                            if ($Description->length > 0) {
                                $Role = $Description->item(0)->getAttribute("Role");
                                $Text = $Description->item(0)->getElementsByTagName("Text");
                                if ($Text->length > 0) {
                                    $Text = $Text->item(0)->nodeValue;
                                } else {
                                    $Text = "";
                                }
                            }
                            
                            /*
                             * try {
                             * $sql = new Sql($db);
                             * $insert = $sql->insert();
                             * $insert->into('ships_catalogue_entities_types');
                             * $insert->values(array(
                             * 'datetime_created' => time(),
                             * 'datetime_updated' => 0,
                             * 'typeid' => $ID,
                             * 'code' => $Code,
                             * 'role' => $Role,
                             * 'description' => $Text
                             * ), $insert::VALUES_MERGE);
                             * $statement = $sql->prepareStatementForSqlObject($insert);
                             * $results = $statement->execute();
                             * $db->getDriver()
                             * ->getConnection()
                             * ->disconnect();
                             * } catch (\Exception $e) {
                             * echo $return;
                             * echo "Error 5: " . $e;
                             * echo $return;
                             * }
                             */
                        }
                    }
                }
                $Locations = $Entity->item($i)->getElementsByTagName("Locations");
                if ($Locations->length > 0) {
                    $City = $Locations->item(0)->getElementsByTagName("City");
                    if ($City->length > 0) {
                        $cities = "";
                        for ($iAux5 = 0; $iAux5 < $City->length; $iAux5 ++) {
                            $ID = $City->item($iAux5)->getAttribute("ID");
                            $cities = $City->item($iAux5)->nodeValue;
                            $Point = $City->item($iAux5)->getElementsByTagName("Point");
                            if ($Point->length > 0) {
                                $Latitude = $Point->item(0)->getAttribute("Latitude");
                                $Longitude = $Point->item(0)->getAttribute("Longitude");
                            }
                            $Country = $City->item($iAux5)->getElementsByTagName("Country");
                            if ($Country->length > 0) {
                                $CountryRef = $Country->item(0)->getAttribute("Ref");
                            }
                            $Code = $City->item($iAux5)->getElementsByTagName("Code");
                            if ($Code->length > 0) {
                                $Owner = $Code->item(0)->getAttribute("Owner");
                                $Value = $Code->item(0)->getAttribute("Value");
                            }
                            
                            /*
                             * try {
                             * $sql = new Sql($db);
                             * $insert = $sql->insert();
                             * $insert->into('ships_catalogue_entities_locations');
                             * $insert->values(array(
                             * 'datetime_created' => time(),
                             * 'datetime_updated' => 0,
                             * 'cityid' => $ID,
                             * 'city' => $cities,
                             * 'latitude' => $Latitude,
                             * 'longitude' => $Longitude,
                             * 'countryref' => $CountryRef,
                             * 'owner' => $Owner,
                             * 'value' => $Value
                             * ), $insert::VALUES_MERGE);
                             * $statement = $sql->prepareStatementForSqlObject($insert);
                             * $results = $statement->execute();
                             * $db->getDriver()
                             * ->getConnection()
                             * ->disconnect();
                             * } catch (\Exception $e) {
                             * echo $return;
                             * echo "Error 6: " . $e;
                             * echo $return;
                             * }
                             */
                        }
                    }
                }
                $Countries = $Entity->item($i)->getElementsByTagName("Countries");
                if ($Countries->length > 0) {
                    $Country = $Countries->item(0)->getElementsByTagName("Country");
                    if ($Country->length > 0) {
                        for ($iAux6 = 0; $iAux6 < $Country->length; $iAux6 ++) {
                            $ID = $Country->item($iAux6)->getAttribute("ID");
                            $Code = $Country->item($iAux6)->getElementsByTagName("Code");
                            if ($Code->length > 0) {
                                $Owner = $Code->item(0)->getAttribute("Owner");
                                $Value = $Code->item(0)->getAttribute("Value");
                            }
                            $Description = $Country->item($iAux6)->getElementsByTagName("Description");
                            if ($Description->length > 0) {
                                $Role = $Description->item(0)->getAttribute("Role");
                                $Description = $Description->item(0)->nodeValue;
                            } else {
                                $Description = "";
                            }
                            
                            /*
                             * try {
                             * $sql = new Sql($db);
                             * $insert = $sql->insert();
                             * $insert->into('ships_catalogue_entities_countries');
                             * $insert->values(array(
                             * 'datetime_created' => time(),
                             * 'datetime_updated' => 0,
                             * 'countryid' => $ID,
                             * 'owner' => $Owner,
                             * 'value' => $Value,
                             * 'role' => $Role,
                             * 'description' => $Description
                             * ), $insert::VALUES_MERGE);
                             * $statement = $sql->prepareStatementForSqlObject($insert);
                             * $results = $statement->execute();
                             * $db->getDriver()
                             * ->getConnection()
                             * ->disconnect();
                             * } catch (\Exception $e) {
                             * echo $return;
                             * echo "Error 7: " . $e;
                             * echo $return;
                             * }
                             */
                        }
                    }
                }
            }
        }
    }
    // Ships
    $Ships = $Catalogue->item(0)->getElementsByTagName("Ships");
    if ($Ships->length > 0) {
        $Ship = $Ships->item(0)->getElementsByTagName("Ship");
        if ($Ship->length > 0) {
            for ($j = 0; $j < $Ship->length; $j ++) {
                $Brands = $Ship->item($j)->getElementsByTagName("Brands");
                if ($Brands->length > 0) {
                    $Brand = $Brands->item(0)->getElementsByTagName("Brand");
                    if ($Brand->length > 0) {
                        $BrandRole = $Brand->item(0)->getAttribute("Role");
                        $BrandValue = $Brand->item(0)->getAttribute("Value");
                    }
                }
                $Begin = $Ship->item($j)->getElementsByTagName("Begin");
                if ($Begin->length > 0) {
                    $BeginValue = $Begin->item(0)->getAttribute("Value");
                }
                $End = $Ship->item($j)->getElementsByTagName("End");
                if ($End->length > 0) {
                    $EndValue = $End->item(0)->getAttribute("Value");
                }
                $Rating = $Ship->item($j)->getElementsByTagName("Rating");
                if ($Rating->length > 0) {
                    $RatingValue = $Rating->item(0)->getAttribute("Value");
                    $RatingCategory = $Rating->item(0)->getAttribute("Category");
                }
                $Histories = $Ship->item($j)->getElementsByTagName("Histories");
                if ($Histories->length > 0) {
                    $History = $Histories->item(0)->getElementsByTagName("History");
                    if ($History->length > 0) {
                        $Purpose = $History->item(0)->getAttribute("Purpose");
                        $Rank = $History->item(0)->getAttribute("Rank");
                        $When = $History->item(0)->getAttribute("When");
                    }
                }
                
                /*
                 * try {
                 * $sql = new Sql($db);
                 * $insert = $sql->insert();
                 * $insert->into('ships_catalogue_ships');
                 * $insert->values(array(
                 * 'datetime_created' => time(),
                 * 'datetime_updated' => 0,
                 * 'brandrole' => $BrandRole,
                 * 'brandvalue' => $BrandValue,
                 * 'beginvalue' => $BeginValue,
                 * 'endvalue' => $EndValue,
                 * 'ratingvalue' => $RatingValue,
                 * 'ratingcategory' => $RatingCategory,
                 * 'purpose' => $Purpose,
                 * 'rank' => $Rank,
                 * 'when' => $When
                 * ), $insert::VALUES_MERGE);
                 * $statement = $sql->prepareStatementForSqlObject($insert);
                 * $results = $statement->execute();
                 * $db->getDriver()
                 * ->getConnection()
                 * ->disconnect();
                 * } catch (\Exception $e) {
                 * echo $return;
                 * echo "Error 8: " . $e;
                 * echo $return;
                 * }
                 */
                
                $Codes = $Ship->item($j)->getElementsByTagName("Codes");
                if ($Codes->length > 0) {
                    $Code = $Codes->item(0)->getElementsByTagName("Code");
                    if ($Code->length > 0) {
                        for ($jAux = 0; $jAux < $Code->length; $jAux ++) {
                            $Role = $Code->item($jAux)->getAttribute("Role");
                            $Owner = $Code->item($jAux)->getAttribute("Owner");
                            $Value = $Code->item($jAux)->getAttribute("Value");
                            
                            /*
                             * try {
                             * $sql = new Sql($db);
                             * $insert = $sql->insert();
                             * $insert->into('ships_catalogue_ships_codes');
                             * $insert->values(array(
                             * 'datetime_created' => time(),
                             * 'datetime_updated' => 0,
                             * 'role' => $Role,
                             * 'owner' => $Owner,
                             * 'value' => $Value
                             * ), $insert::VALUES_MERGE);
                             * $statement = $sql->prepareStatementForSqlObject($insert);
                             * $results = $statement->execute();
                             * $db->getDriver()
                             * ->getConnection()
                             * ->disconnect();
                             * } catch (\Exception $e) {
                             * echo $return;
                             * echo "Error 9: " . $e;
                             * echo $return;
                             * }
                             */
                        }
                    }
                }
                $Decks = $Ship->item($j)->getElementsByTagName("Decks");
                if ($Decks->length > 0) {
                    $Quantity = $Decks->item(0)->getAttribute("Quantity");
                    $Deck = $Decks->item(0)->getElementsByTagName("Deck");
                    if ($Deck->length > 0) {
                        for ($jAux2 = 0; $jAux2 < $Deck->length; $jAux2 ++) {
                            $Codes = $Deck->item($jAux2)->getElementsByTagName("Codes");
                            if ($Codes->length > 0) {
                                $Code = $Codes->item(0)->getElementsByTagName("Code");
                                if ($Code->length > 0) {
                                    $Role = $Code->item(0)->getAttribute("Role");
                                    $Owner = $Code->item(0)->getAttribute("Owner");
                                    $Value = $Code->item(0)->getAttribute("Value");
                                    
                                    /*
                                     * try {
                                     * $sql = new Sql($db);
                                     * $insert = $sql->insert();
                                     * $insert->into('ships_catalogue_ships_decks_codes');
                                     * $insert->values(array(
                                     * 'datetime_created' => time(),
                                     * 'datetime_updated' => 0,
                                     * 'role' => $Role,
                                     * 'owner' => $Owner,
                                     * 'value' => $Value
                                     * ), $insert::VALUES_MERGE);
                                     * $statement = $sql->prepareStatementForSqlObject($insert);
                                     * $results = $statement->execute();
                                     * $db->getDriver()
                                     * ->getConnection()
                                     * ->disconnect();
                                     * } catch (\Exception $e) {
                                     * echo $return;
                                     * echo "Error 10: " . $e;
                                     * echo $return;
                                     * }
                                     */
                                }
                            }
                            $Descriptions = $Deck->item($jAux2)->getElementsByTagName("Descriptions");
                            if ($Descriptions->length > 0) {
                                $Description = $Descriptions->item(0)->getElementsByTagName("Description");
                                if ($Description->length > 0) {
                                    for ($jAux3 = 0; $jAux3 < $Description->length; $jAux3 ++) {
                                        $Role = $Description->item($jAux3)->getAttribute("Role");
                                        $Title = $Description->item($jAux3)->getElementsByTagName("Title");
                                        if ($Title->length > 0) {
                                            $Title = $Title->item(0)->nodeValue;
                                        } else {
                                            $Title = "";
                                        }
                                        $Text = $Description->item($jAux3)->getElementsByTagName("Text");
                                        if ($Text->length > 0) {
                                            $Text = $Text->item(0)->nodeValue;
                                        } else {
                                            $Text = "";
                                        }
                                        $Types = $Description->item($jAux3)->getElementsByTagName("Types");
                                        if ($Types->length > 0) {
                                            $Type = $Types->item(0)->getElementsByTagName("Type");
                                            if ($Type->length > 0) {
                                                $Ref = $Type->item(0)->getAttribute("Ref");
                                            }
                                        }
                                        $Codes = $Description->item($jAux3)->getElementsByTagName("Codes");
                                        if ($Codes->length > 0) {
                                            $Code = $Codes->item(0)->getElementsByTagName("Code");
                                            if ($Code->length > 0) {
                                                $CodeRole = $Code->item(0)->getAttribute("Role");
                                                $CodeKey = $Code->item(0)->getAttribute("Key");
                                            }
                                        }
                                        $Media = $Description->item($jAux3)->getElementsByTagName("Media");
                                        if ($Media->length > 0) {
                                            $MediaRole = $Media->item(0)->getAttribute("Role");
                                            $URL = $Media->item(0)->getElementsByTagName("URL");
                                            if ($URL->length > 0) {
                                                $URL = $URL->item(0)->nodeValue;
                                            } else {
                                                $URL = "";
                                            }
                                        }
                                        $Keywords = $Description->item($jAux3)->getElementsByTagName("Keywords");
                                        if ($Keywords->length > 0) {
                                            $Keyword = $Keywords->item(0)->getElementsByTagName("Keyword");
                                            if ($Keyword->length > 0) {
                                                $KeywordRef = $Keyword->item(0)->getAttribute("Ref");
                                            }
                                        }
                                        
                                        /*
                                         * try {
                                         * $sql = new Sql($db);
                                         * $insert = $sql->insert();
                                         * $insert->into('ships_catalogue_ships_decks_descriptions');
                                         * $insert->values(array(
                                         * 'datetime_created' => time(),
                                         * 'datetime_updated' => 0,
                                         * 'role' => $Role,
                                         * 'title' => $Title,
                                         * 'description' => $Text,
                                         * 'typeref' => $Ref,
                                         * 'keywordref' => $KeywordRef,
                                         * 'coderole' => $CodeRole,
                                         * 'codekey' => $CodeKey,
                                         * 'mediarole' => $MediaRole,
                                         * 'url' => $URL
                                         * ), $insert::VALUES_MERGE);
                                         * $statement = $sql->prepareStatementForSqlObject($insert);
                                         * $results = $statement->execute();
                                         * $db->getDriver()
                                         * ->getConnection()
                                         * ->disconnect();
                                         * } catch (\Exception $e) {
                                         * echo $return;
                                         * echo "Error 11: " . $e;
                                         * echo $return;
                                         * }
                                         */
                                    }
                                }
                            }
                            $DeckCabins = $Deck->item($jAux2)->getElementsByTagName("Cabins");
                            if ($DeckCabins->length > 0) {
                                $DeckCabin = $DeckCabins->item(0)->getElementsByTagName("Cabin");
                                if ($DeckCabin->length > 0) {
                                    for ($jAux4 = 0; $jAux4 < $DeckCabin->length; $jAux4 ++) {
                                        $Ref = $DeckCabin->item($jAux4)->getAttribute("Ref");
                                        echo $return;
                                        echo "Cabin: " . $Ref;
                                        echo $return;
                                        /*
                                         * try {
                                         * $sql = new Sql($db);
                                         * $insert = $sql->insert();
                                         * $insert->into('ships_catalogue_ships_decks_cabins');
                                         * $insert->values(array(
                                         * 'datetime_created' => time(),
                                         * 'datetime_updated' => 0,
                                         * 'ref' => $Ref
                                         * ), $insert::VALUES_MERGE);
                                         * $statement = $sql->prepareStatementForSqlObject($insert);
                                         * $results = $statement->execute();
                                         * $db->getDriver()
                                         * ->getConnection()
                                         * ->disconnect();
                                         * } catch (\Exception $e) {
                                         * echo $return;
                                         * echo "Error 12: " . $e;
                                         * echo $return;
                                         * }
                                         */
                                    }
                                }
                            }
                        }
                    }
                }
                $Cabins = $Ship->item($j)->getElementsByTagName("Cabins");
                if ($Cabins->length > 0) {
                    for ($wC = 0; $wC < $Cabins->length; $wC ++) {
                        if ($Cabins->item($wC)->parentNode->nodeName == "Ship") {
                            $Cabin = $Cabins->item($wC)->getElementsByTagName("Cabin");
                            if ($Cabin->length > 0) {
                                echo "PASSOU 3 " . $Cabin->length;
                                for ($jAux5 = 0; $jAux5 < $Cabin->length; $jAux5 ++) {
                                    $ID = $Cabin->item($jAux5)->getAttribute("ID");
                                    echo $return;
                                    echo "ID 13: " . $ID;
                                    echo $return;
                                    $Codes = $Cabin->item($jAux5)->getElementsByTagName("Codes");
                                    if ($Codes->length > 0) {
                                        $Code = $Codes->item(0)->getElementsByTagName("Code");
                                        if ($Code->length > 0) {
                                            $CodeRole = $Code->item(0)->getAttribute("Role");
                                            $CodeKey = $Code->item(0)->getAttribute("Key");
                                        }
                                    }
                                    $Occupancy = $Cabin->item($jAux5)->getElementsByTagName("Occupancy");
                                    if ($Occupancy->length > 0) {
                                        $OccupancyUnit = $Occupancy->item(0)->getAttribute("Unit");
                                        $OccupancyMin = $Occupancy->item(0)->getAttribute("Min");
                                        $OccupancyMax = $Occupancy->item(0)->getAttribute("Max");
                                    }
                                    $Dimension = $Cabin->item($jAux5)->getElementsByTagName("Dimension");
                                    if ($Dimension->length > 0) {
                                        $DimensionTopic = $Dimension->item(0)->getAttribute("Topic");
                                        $DimensionValue = $Dimension->item(0)->getAttribute("Value");
                                    }
                                    
                                    /*
                                     * try {
                                     * $sql = new Sql($db);
                                     * $insert = $sql->insert();
                                     * $insert->into('ships_catalogue_ships_cabins');
                                     * $insert->values(array(
                                     * 'datetime_created' => time(),
                                     * 'datetime_updated' => 0,
                                     * 'cabinid' => $ID,
                                     * 'coderole' => $CodeRole,
                                     * 'codekey' => $CodeKey,
                                     * 'occupancyunit' => $OccupancyUnit,
                                     * 'occupancymin' => $OccupancyMin,
                                     * 'occupancymax' => $OccupancyMax,
                                     * 'dimensiontopic' => $DimensionTopic,
                                     * 'dimensionvalue' => $DimensionValue
                                     * ), $insert::VALUES_MERGE);
                                     * $statement = $sql->prepareStatementForSqlObject($insert);
                                     * $results = $statement->execute();
                                     * $db->getDriver()
                                     * ->getConnection()
                                     * ->disconnect();
                                     * } catch (\Exception $e) {
                                     * echo $return;
                                     * echo "Error 13: " . $e;
                                     * echo $return;
                                     * }
                                     */
                                    
                                    $Descriptions = $Cabin->item($jAux5)->getElementsByTagName("Descriptions");
                                    if ($Descriptions->length > 0) {
                                        $Description = $Descriptions->item(0)->getElementsByTagName("Description");
                                        if ($Description->length > 0) {
                                            for ($jAux3 = 0; $jAux3 < $Description->length; $jAux3 ++) {
                                                $Role = $Description->item($jAux3)->getAttribute("Role");
                                                $Title = $Description->item($jAux3)->getElementsByTagName("Title");
                                                if ($Title->length > 0) {
                                                    $Title = $Title->item(0)->nodeValue;
                                                } else {
                                                    $Title = "";
                                                }
                                                
                                                $Text = $Description->item($jAux3)->getElementsByTagName("Text");
                                                if ($Text->length > 0) {
                                                    $Text = $Text->item(0)->nodeValue;
                                                } else {
                                                    $Text = "";
                                                }
                                                $Types = $Description->item($jAux3)->getElementsByTagName("Types");
                                                if ($Types->length > 0) {
                                                    $Type = $Types->item(0)->getElementsByTagName("Type");
                                                    if ($Type->length > 0) {
                                                        $Ref = $Type->item(0)->getAttribute("Ref");
                                                    }
                                                }
                                                $Codes = $Description->item($jAux3)->getElementsByTagName("Codes");
                                                if ($Codes->length > 0) {
                                                    $Code = $Codes->item(0)->getElementsByTagName("Code");
                                                    if ($Code->length > 0) {
                                                        $CodeRole = $Code->item(0)->getAttribute("Role");
                                                        $CodeKey = $Code->item(0)->getAttribute("Key");
                                                    }
                                                }
                                                $Media = $Description->item($jAux3)->getElementsByTagName("Media");
                                                if ($Media->length > 0) {
                                                    $MediaRole = $Media->item(0)->getAttribute("Role");
                                                    $URL = $Media->item(0)->getElementsByTagName("URL");
                                                    if ($URL->length > 0) {
                                                        $URL = $URL->item(0)->nodeValue;
                                                    } else {
                                                        $URL = "";
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
                $Descriptions = $Ship->item($j)->getElementsByTagName("Descriptions");
                if ($Descriptions->length > 0) {
                    for ($Wd=0; $Wd < $Descriptions->length ; $Wd++) { 
                        if ($Descriptions->item($Wd)->parentNode->nodeName == "Ship") {
                            $Description = $Descriptions->item($Wd)->getElementsByTagName("Description");
                            echo $return;
                            echo "TAM DESCR: " . $Description->length;
                            echo $return;
                            if ($Description->length > 0) {
                                for ($jAux3 = 0; $jAux3 < $Description->length; $jAux3 ++) {
                                    $Role = $Description->item($jAux3)->getAttribute("Role");
                                    $Title = $Description->item($jAux3)->getElementsByTagName("Title");
                                    if ($Title->length > 0) {
                                        $Title = $Title->item(0)->nodeValue;
                                    } else {
                                        $Title = "";
                                    }
                                    echo $return;
                                                        echo "TITLE DESCR: " . $Title;
                                                        echo $return;
                                    $Text = $Description->item($jAux3)->getElementsByTagName("Text");
                                    if ($Text->length > 0) {
                                        $Text = $Text->item(0)->nodeValue;
                                    } else {
                                        $Text = "";
                                    }
                                    $Types = $Description->item($jAux3)->getElementsByTagName("Types");
                                    if ($Types->length > 0) {
                                        $Type = $Types->item(0)->getElementsByTagName("Type");
                                        if ($Type->length > 0) {
                                            $Ref = $Type->item(0)->getAttribute("Ref");
                                        }
                                    }
                                    $Codes = $Description->item($jAux3)->getElementsByTagName("Codes");
                                    if ($Codes->length > 0) {
                                        $Code = $Codes->item(0)->getElementsByTagName("Code");
                                        if ($Code->length > 0) {
                                            $CodeRole = $Code->item(0)->getAttribute("Role");
                                            $CodeKey = $Code->item(0)->getAttribute("Key");
                                        }
                                    }
                                    $Media = $Description->item($jAux3)->getElementsByTagName("Media");
                                    if ($Media->length > 0) {
                                        $MediaRole = $Media->item(0)->getAttribute("Role");
                                        $URL = $Media->item(0)->getElementsByTagName("URL");
                                        if ($URL->length > 0) {
                                            $URL = $URL->item(0)->nodeValue;
                                        } else {
                                            $URL = "";
                                        }
                                    }
                                    
                                    /*
                                    * try {
                                    * $sql = new Sql($db);
                                    * $insert = $sql->insert();
                                    * $insert->into('ships_catalogue_ships_descriptions');
                                    * $insert->values(array(
                                    * 'datetime_created' => time(),
                                    * 'datetime_updated' => 0,
                                    * 'role' => $Role,
                                    * 'title' => $Title,
                                    * 'description' => $Text,
                                    * 'ref' => $Ref,
                                    * 'coderole' => $CodeRole,
                                    * 'codekey' => $CodeKey,
                                    * 'mediarole' => $MediaRole,
                                    * 'url' => $URL
                                    * ), $insert::VALUES_MERGE);
                                    * $statement = $sql->prepareStatementForSqlObject($insert);
                                    * $results = $statement->execute();
                                    * $db->getDriver()
                                    * ->getConnection()
                                    * ->disconnect();
                                    * } catch (\Exception $e) {
                                    * echo $return;
                                    * echo "Error 15: " . $e;
                                    * echo $return;
                                    * }
                                    */
                                }
                            }
                        }
                    }
                }
                $Keywords = $Ship->item($j)->getElementsByTagName("Keywords");
                if ($Keywords->length > 0) {
                    for ($wK=0; $wK < $Keywords->length; $wK++) { 
                        if ($Keywords->item($wK)->parentNode->nodeName == "Ship") {
                            $Keyword = $Keywords->item($wK)->getElementsByTagName("Keyword");
                            if ($Keyword->length > 0) {
                                echo $return;
                            echo "TAM KEYWORD: " . $Keyword->length;
                            echo $return;
                                for ($jAux2 = 0; $jAux2 < $Keyword->length; $jAux2 ++) {
                                    $Ref = $Keyword->item($jAux2)->getAttribute("Ref");
                                    $Code = $Keyword->item($jAux2)->getElementsByTagName("Code");
                                    if ($Code->length > 0) {
                                        $CodeValue = $Code->item(0)->getAttribute("Value");
                                    }
                                    echo $return;
                                    echo " CodeValue: " . $CodeValue;
                                    echo $return;
                                    
                                    /*
                                    * try {
                                    * $sql = new Sql($db);
                                    * $insert = $sql->insert();
                                    * $insert->into('ships_catalogue_ships_keywords');
                                    * $insert->values(array(
                                    * 'datetime_created' => time(),
                                    * 'datetime_updated' => 0,
                                    * 'ref' => $Ref,
                                    * 'codevalue' => $CodeValue
                                    * ), $insert::VALUES_MERGE);
                                    * $statement = $sql->prepareStatementForSqlObject($insert);
                                    * $results = $statement->execute();
                                    * $db->getDriver()
                                    * ->getConnection()
                                    * ->disconnect();
                                    * } catch (\Exception $e) {
                                    * echo $return;
                                    * echo "Error 16: " . $e;
                                    * echo $return;
                                    * }
                                    */
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
echo 'Done';
?>