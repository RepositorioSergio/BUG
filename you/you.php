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
echo "COMECOU YOU";
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
$config = new \Zend\Config\Config(include '../config/autoload/global.you.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$json = file_get_contents('you.json');

$response2 = json_decode($json, true);

echo "<xmp>";
var_dump($response2);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.you.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$response = $response2['response'];
$reviews_count = $response['reviews_count'];
$gender_talks_about = $response['gender_talks_about'];
$version = $response['version'];
$ty_id = $response['ty_id'];
$lang = $response['lang'];
echo $return;
echo "ty_id: " . $ty_id;
echo $return;

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('you');
    $insert->values(array(
        'ty_id' => $ty_id,
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'reviews_count' => $reviews_count,
        'gender_talks_about' => $gender_talks_about,
        'version' => $version,
        'lang' => $lang
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "Error 64: " . $e;
    echo $return;
}

$language_meta_review_list = $response['language_meta_review_list'];
if (count($language_meta_review_list) > 0) {
    for ($i=0; $i < count($language_meta_review_list); $i++) { 
        $reviews_percent = $language_meta_review_list[$i]['reviews_percent'];
        $filter = $language_meta_review_list[$i]['filter'];
        $trip_type = $filter['trip_type'];
        $language = $filter['language'];
        $summary = $language_meta_review_list[$i]['summary'];
        $score_description = $summary['score_description'];
        $global_popularity = $summary['global_popularity'];
        $score = $summary['score'];
        $popularity = $summary['popularity'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('language_meta');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'reviews_percent' => $reviews_percent,
                'trip_type' => $trip_type,
                'language' => $language,
                'score_description' => $score_description,
                'global_popularity' => $global_popularity,
                'popularity' => $popularity,
                'score' => $score,
                'ty_id' => $ty_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 63: " . $e;
            echo $return;
        }

        $trip_type_meta_review_list = $language_meta_review_list[$i]['trip_type_meta_review_list'];
        if (count($trip_type_meta_review_list) > 0) {
            for ($iAux=0; $iAux < count($trip_type_meta_review_list); $iAux++) { 
                $reviews_percent = $trip_type_meta_review_list[$iAux]['reviews_percent'];
                $filter = $trip_type_meta_review_list[$iAux]['filter'];
                $trip_type = $filter['trip_type'];
                $language = $filter['language'];
                $summary = $trip_type_meta_review_list[$iAux]['summary'];
                $score_description = $summary['score_description'];
                $global_popularity = $summary['global_popularity'];
                $score = $summary['score'];
                $popularity = $summary['popularity'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('trip_language');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'reviews_percent' => $reviews_percent,
                        'trip_type' => $trip_type,
                        'language' => $language,
                        'score_description' => $score_description,
                        'global_popularity' => $global_popularity,
                        'popularity' => $popularity,
                        'score' => $score,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 62: " . $e;
                    echo $return;
                }

                $good_to_know_list = $trip_type_meta_review_list[$iAux]['good_to_know_list'];
                if (count($good_to_know_list) > 0) {
                    for ($iAux2=0; $iAux2 < count($good_to_know_list); $iAux2++) { 
                        $category_id = $good_to_know_list[$iAux2]['category_id'];
                        $category_name = $good_to_know_list[$iAux2]['category_name'];
                        $count = $good_to_know_list[$iAux2]['count'];
                        $sentiment = $good_to_know_list[$iAux2]['sentiment'];
                        $text = $good_to_know_list[$iAux2]['text'];
                        $score = $good_to_know_list[$iAux2]['score'];
                        $relevance = $good_to_know_list[$iAux2]['relevance'];
                        $short_text = $good_to_know_list[$iAux2]['short_text'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('good_trip_language');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'category_id' => $category_id,
                                'category_name' => $category_name,
                                'text' => $text,
                                'sentiment' => $sentiment,
                                'count' => $count,
                                'relevance' => $relevance,
                                'short_text' => $short_text,
                                'score' => $score,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 61: " . $e;
                            echo $return;
                        }

                        $highlight_list = $good_to_know_list[$iAux2]['highlight_list'];
                        if (count($highlight_list) > 0) {
                            for ($iAux3=0; $iAux3 < count($highlight_list); $iAux3++) { 
                                $text = $highlight_list[$iAux3]['text'];
                                $confidence = $highlight_list[$iAux3]['confidence'];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('highlight_highgood_language');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'text' => $text,
                                        'confidence' => $confidence,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 60: " . $e;
                                    echo $return;
                                }

                                $category_id_list = $highlight_list[$iAux3]['category_id_list'];
                                if (count($category_id_list) > 0) {
                                    $category = "";
                                    for ($iaUX4=0; $iaUX4 < count($category_id_list); $iaUX4++) { 
                                        $category = $category_id_list[$iaUX4];

                                        try {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('category_highgood_language');
                                            $insert->values(array(
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'category_id' => $category,
                                                'ty_id' => $ty_id
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 59: " . $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $category_list = $trip_type_meta_review_list[$iAux]['category_list'];
                if (count($category_list) > 0) {
                    for ($iAux5=0; $iAux5 < count($category_list); $iAux5++) { 
                        $category_id = $category_list[$iAux5]['category_id'];
                        $category_name = $category_list[$iAux5]['category_name'];
                        $count = $category_list[$iAux5]['count'];
                        $sentiment = $category_list[$iAux5]['sentiment'];
                        $text = $category_list[$iAux5]['text'];
                        $score = $category_list[$iAux5]['score'];
                        $relevance = $category_list[$iAux5]['relevance'];
                        $short_text = $category_list[$iAux5]['short_text'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('category_language');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'category_id' => $category_id,
                                'category_name' => $category_name,
                                'text' => $text,
                                'sentiment' => $sentiment,
                                'count' => $count,
                                'relevance' => $relevance,
                                'short_text' => $short_text,
                                'score' => $score,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 58: " . $e;
                            echo $return;
                        }

                        $highlight_list = $category_list[$iAux5]['highlight_list'];
                        if (count($highlight_list) > 0) {
                            for ($iAux3=0; $iAux3 < count($highlight_list); $iAux3++) { 
                                $text = $highlight_list[$iAux3]['text'];
                                $confidence = $highlight_list[$iAux3]['confidence'];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('highlight_high_language');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'text' => $text,
                                        'confidence' => $confidence,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 57: " . $e;
                                    echo $return;
                                }

                                $category_id_list = $highlight_list[$iAux3]['category_id_list'];
                                if (count($category_id_list) > 0) {
                                    $category = "";
                                    for ($iaUX4=0; $iaUX4 < count($category_id_list); $iaUX4++) { 
                                        $category = $category_id_list[$iaUX4];

                                        try {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('category_highlight_language');
                                            $insert->values(array(
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'category_id' => $category,
                                                'ty_id' => $ty_id
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 56: " . $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                        $sub_category_list = $category_list[$iAux5]['sub_category_list'];
                        if (count($sub_category_list) > 0) {
                            for ($iAux6=0; $iAux6 < count($sub_category_list); $iAux6++) { 
                                $category_id = $sub_category_list[$iAux6]['category_id'];
                                $category_name = $sub_category_list[$iAux6]['category_name'];
                                $count = $sub_category_list[$iAux6]['count'];
                                $sentiment = $sub_category_list[$iAux6]['sentiment'];
                                $text = $sub_category_list[$iAux6]['text'];
                                $score = $sub_category_list[$iAux6]['score'];
                                $relevance = $sub_category_list[$iAux6]['relevance'];
                                $short_text = $sub_category_list[$iAux6]['short_text'];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('subcategory_trip_language');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'category_id' => $category_id,
                                        'category_name' => $category_name,
                                        'text' => $text,
                                        'sentiment' => $sentiment,
                                        'count' => $count,
                                        'relevance' => $relevance,
                                        'short_text' => $short_text,
                                        'score' => $score,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 55: " . $e;
                                    echo $return;
                                }

                                $highlight_list = $sub_category_list[$iAux6]['highlight_list'];
                                if (count($highlight_list) > 0) {
                                    for ($iAux3=0; $iAux3 < count($highlight_list); $iAux3++) { 
                                        $text = $highlight_list[$iAux3]['text'];
                                        $confidence = $highlight_list[$iAux3]['confidence'];

                                        try {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('highlight_sc_trip_language');
                                            $insert->values(array(
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'text' => $text,
                                                'confidence' => $confidence,
                                                'ty_id' => $ty_id
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 54: " . $e;
                                            echo $return;
                                        }

                                        $category_id_list = $highlight_list[$iAux3]['category_id_list'];
                                        if (count($category_id_list) > 0) {
                                            $category = "";
                                            for ($iaUX4=0; $iaUX4 < count($category_id_list); $iaUX4++) { 
                                                $category = $category_id_list[$iaUX4];

                                                try {
                                                    $sql = new Sql($db);
                                                    $insert = $sql->insert();
                                                    $insert->into('category_category_language');
                                                    $insert->values(array(
                                                        'datetime_created' => time(),
                                                        'datetime_updated' => 0,
                                                        'category_id' => $category,
                                                        'ty_id' => $ty_id
                                                    ), $insert::VALUES_MERGE);
                                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                                    $results = $statement->execute();
                                                    $db->getDriver()
                                                        ->getConnection()
                                                        ->disconnect();
                                                } catch (\Exception $e) {
                                                    echo $return;
                                                    echo "Error 53: " . $e;
                                                    echo $return;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $summary_sentence_list = $category_list[$iAux5]['summary_sentence_list'];
                        if (count($summary_sentence_list) > 0) {
                            for ($iAux7=0; $iAux7 < count($summary_sentence_list); $iAux7++) { 
                                $text = $summary_sentence_list[$iAux7]['text'];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('summary_sentence_language');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'text' => $text,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 52: " . $e;
                                    echo $return;
                                }

                                $category_id_list = $summary_sentence_list[$iAux7]['category_id_list'];
                                if (count($category_id_list) > 0) {
                                    $category = "";
                                    for ($iaUX4=0; $iaUX4 < count($category_id_list); $iaUX4++) { 
                                        $category = $category_id_list[$iaUX4];

                                        try {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('category_ss_language');
                                            $insert->values(array(
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'category_id' => $category,
                                                'ty_id' => $ty_id
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 51: " . $e;
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

        $category_list = $language_meta_review_list[$i]['category_list'];
        if (count($category_list) > 0) {
            for ($iAux8=0; $iAux8 < count($category_list); $iAux8++) { 
                $category_id = $category_list[$iAux8]['category_id'];
                $category_name = $category_list[$iAux8]['category_name'];
                $count = $category_list[$iAux8]['count'];
                $sentiment = $category_list[$iAux8]['sentiment'];
                $text = $category_list[$iAux8]['text'];
                $score = $category_list[$iAux8]['score'];
                $relevance = $category_list[$iAux8]['relevance'];
                $short_text = $category_list[$iAux8]['short_text'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('categories_language');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'category_id' => $category_id,
                        'category_name' => $category_name,
                        'text' => $text,
                        'sentiment' => $sentiment,
                        'count' => $count,
                        'relevance' => $relevance,
                        'short_text' => $short_text,
                        'score' => $score,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 50: " . $e;
                    echo $return;
                }

                $highlight_list = $category_list[$iAux8]['highlight_list'];
                if (count($highlight_list) > 0) {
                    for ($iAux3=0; $iAux3 < count($highlight_list); $iAux3++) { 
                        $text = $highlight_list[$iAux3]['text'];
                        $confidence = $highlight_list[$iAux3]['confidence'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('highlight_category_language');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'text' => $text,
                                'confidence' => $confidence,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 49: " . $e;
                            echo $return;
                        }

                        $category_id_list = $highlight_list[$iAux3]['category_id_list'];
                        if (count($category_id_list) > 0) {
                            $category = "";
                            for ($iaUX4=0; $iaUX4 < count($category_id_list); $iaUX4++) { 
                                $category = $category_id_list[$iaUX4];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('category_categoryhl_language');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'category_id' => $category,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 48: " . $e;
                                    echo $return;
                                }
                            }
                        }
                    }
                }
                $sub_category_list = $category_list[$iAux8]['sub_category_list'];
                if (count($sub_category_list) > 0) {
                    for ($iAux6=0; $iAux6 < count($sub_category_list); $iAux6++) { 
                        $category_id = $sub_category_list[$iAux6]['category_id'];
                        $category_name = $sub_category_list[$iAux6]['category_name'];
                        $count = $sub_category_list[$iAux6]['count'];
                        $sentiment = $sub_category_list[$iAux6]['sentiment'];
                        $text = $sub_category_list[$iAux6]['text'];
                        $score = $sub_category_list[$iAux6]['score'];
                        $relevance = $sub_category_list[$iAux6]['relevance'];
                        $short_text = $sub_category_list[$iAux6]['short_text'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('subcategory_language');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'category_id' => $category_id,
                                'category_name' => $category_name,
                                'text' => $text,
                                'sentiment' => $sentiment,
                                'count' => $count,
                                'relevance' => $relevance,
                                'short_text' => $short_text,
                                'score' => $score,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 47: " . $e;
                            echo $return;
                        }

                        $highlight_list = $sub_category_list[$iAux6]['highlight_list'];
                        if (count($highlight_list) > 0) {
                            for ($iAux3=0; $iAux3 < count($highlight_list); $iAux3++) { 
                                $text = $highlight_list[$iAux3]['text'];
                                $confidence = $highlight_list[$iAux3]['confidence'];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('highlight_sc_language');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'text' => $text,
                                        'confidence' => $confidence,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 46: " . $e;
                                    echo $return;
                                }

                                $category_id_list = $highlight_list[$iAux3]['category_id_list'];
                                if (count($category_id_list) > 0) {
                                    $category = "";
                                    for ($iaUX4=0; $iaUX4 < count($category_id_list); $iaUX4++) { 
                                        $category = $category_id_list[$iaUX4];

                                        try {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('category_sc_language');
                                            $insert->values(array(
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'category_id' => $category,
                                                'ty_id' => $ty_id
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 45: " . $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $summary_sentence_list = $category_list[$iAux8]['summary_sentence_list'];
                if (count($summary_sentence_list) > 0) {
                    for ($iAux7=0; $iAux7 < count($summary_sentence_list); $iAux7++) { 
                        $text = $summary_sentence_list[$iAux7]['text'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('ss_language');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'text' => $text,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 44: " . $e;
                            echo $return;
                        }

                        $category_id_list = $summary_sentence_list[$iAux7]['category_id_list'];
                        if (count($category_id_list) > 0) {
                            $category = "";
                            for ($iaUX4=0; $iaUX4 < count($category_id_list); $iaUX4++) { 
                                $category = $category_id_list[$iaUX4];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('category_summarysentence_language');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'category_id' => $category,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 43: " . $e;
                                    echo $return;
                                }
                            }
                        }
                    }
                }
            }
        }
        $good_to_know_list = $language_meta_review_list[$i]['good_to_know_list'];
        if (count($good_to_know_list) > 0) {
            for ($iAux9=0; $iAux9 < count($good_to_know_list); $iAux9++) { 
                $category_id = $good_to_know_list[$iAux9]['category_id'];
                $category_name = $good_to_know_list[$iAux9]['category_name'];
                $count = $good_to_know_list[$iAux9]['count'];
                $sentiment = $good_to_know_list[$iAux9]['sentiment'];
                $text = $good_to_know_list[$iAux9]['text'];
                $score = $good_to_know_list[$iAux9]['score'];
                $relevance = $good_to_know_list[$iAux9]['relevance'];
                $short_text = $good_to_know_list[$iAux9]['short_text'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('good_language');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'category_id' => $category_id,
                        'category_name' => $category_name,
                        'text' => $text,
                        'sentiment' => $sentiment,
                        'count' => $count,
                        'relevance' => $relevance,
                        'short_text' => $short_text,
                        'score' => $score,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 42: " . $e;
                    echo $return;
                }

                $highlight_list = $good_to_know_list[$iAux9]['highlight_list'];
                if (count($highlight_list) > 0) {
                    for ($iAux3=0; $iAux3 < count($highlight_list); $iAux3++) { 
                        $text = $highlight_list[$iAux3]['text'];
                        $confidence = $highlight_list[$iAux3]['confidence'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('highlight_good_language');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'text' => $text,
                                'confidence' => $confidence,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 41: " . $e;
                            echo $return;
                        }

                        $category_id_list = $highlight_list[$iAux3]['category_id_list'];
                        if (count($category_id_list) > 0) {
                            $category = "";
                            for ($iaUX4=0; $iaUX4 < count($category_id_list); $iaUX4++) { 
                                $category = $category_id_list[$iaUX4];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('category_good_language');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'category_id' => $category,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 40: " . $e;
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
//trip_type_meta_review_list
$trip_type_meta_review_list = $response['trip_type_meta_review_list'];
if (count($trip_type_meta_review_list)) {
    for ($j=0; $j < count($trip_type_meta_review_list); $j++) { 
        $reviews_percent = $trip_type_meta_review_list[$j]['reviews_percent'];
        $filter = $trip_type_meta_review_list[$j]['filter'];
        $trip_type = $filter['trip_type'];
        $language = $filter['language'];
        $summary = $trip_type_meta_review_list[$j]['summary'];
        $score_description = $summary['score_description'];
        $global_popularity = $summary['global_popularity'];
        $score = $summary['score'];
        $popularity = $summary['popularity'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('triptype');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'reviews_percent' => $reviews_percent,
                'trip_type' => $trip_type,
                'language' => $language,
                'score_description' => $score_description,
                'global_popularity' => $global_popularity,
                'popularity' => $popularity,
                'score' => $score,
                'ty_id' => $ty_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 39: " . $e;
            echo $return;
        }

        $good_to_know_list = $trip_type_meta_review_list[$j]['good_to_know_list'];
        if (count($good_to_know_list) > 0) {
            for ($jAux=0; $jAux < count($good_to_know_list); $jAux++) { 
                $category_id = $good_to_know_list[$jAux]['category_id'];
                $category_name = $good_to_know_list[$jAux]['category_name'];
                $count = $good_to_know_list[$jAux]['count'];
                $sentiment = $good_to_know_list[$jAux]['sentiment'];
                $text = $good_to_know_list[$jAux]['text'];
                $score = $good_to_know_list[$jAux]['score'];
                $relevance = $good_to_know_list[$jAux]['relevance'];
                $short_text = $good_to_know_list[$jAux]['short_text'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('good_trip');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'category_id' => $category_id,
                        'category_name' => $category_name,
                        'text' => $text,
                        'sentiment' => $sentiment,
                        'count' => $count,
                        'relevance' => $relevance,
                        'short_text' => $short_text,
                        'score' => $score,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 38: " . $e;
                    echo $return;
                }

                $highlight_list = $good_to_know_list[$jAux]['highlight_list'];
                if (count($highlight_list) > 0) {
                    for ($jAux2=0; $jAux2 < count($highlight_list); $jAux2++) { 
                        $text = $highlight_list[$jAux2]['text'];
                        $confidence = $highlight_list[$jAux2]['confidence'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('highlight_good_trip');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'text' => $text,
                                'confidence' => $confidence,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 37: " . $e;
                            echo $return;
                        }

                        $category_id_list = $highlight_list[$jAux2]['category_id_list'];
                        if (count($category_id_list) > 0) {
                            $category = "";
                            for ($iaUX4=0; $iaUX4 < count($category_id_list); $iaUX4++) { 
                                $category = $category_id_list[$iaUX4];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('category_good_trip');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'category_id' => $category,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 36: " . $e;
                                    echo $return;
                                }
                            }
                        }
                    }
                }
            }
        }

        $category_list = $trip_type_meta_review_list[$j]['category_list'];
        if (count($category_list) > 0) {
            for ($jAux3=0; $jAux3 < count($category_list); $jAux3++) { 
                $category_id = $category_list[$jAux3]['category_id'];
                $category_name = $category_list[$jAux3]['category_name'];
                $count = $category_list[$jAux3]['count'];
                $sentiment = $category_list[$jAux3]['sentiment'];
                $text = $category_list[$jAux3]['text'];
                $score = $category_list[$jAux3]['score'];
                $relevance = $category_list[$jAux3]['relevance'];
                $short_text = $category_list[$jAux3]['short_text'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('category_trip');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'category_id' => $category_id,
                        'category_name' => $category_name,
                        'text' => $text,
                        'sentiment' => $sentiment,
                        'count' => $count,
                        'relevance' => $relevance,
                        'short_text' => $short_text,
                        'score' => $score,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 35: " . $e;
                    echo $return;
                }

                $highlight_list = $category_list[$jAux3]['highlight_list'];
                if (count($highlight_list) > 0) {
                    for ($jAux4=0; $jAux4 < count($highlight_list); $jAux4++) { 
                        $text = $highlight_list[$jAux4]['text'];
                        $confidence = $highlight_list[$jAux4]['confidence'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('highlight_category_trip');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'text' => $text,
                                'confidence' => $confidence,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 34: " . $e;
                            echo $return;
                        }

                        $category_id_list = $highlight_list[$jAux4]['category_id_list'];
                        if (count($category_id_list) > 0) {
                            $category = "";
                            for ($jAux6=0; $jAux6 < count($category_id_list); $jAux6++) { 
                                $category = $category_id_list[$jAux6];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('category_category_trip');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'category_id' => $category,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 33: " . $e;
                                    echo $return;
                                }
                            }
                        }
                    }
                }
                $sub_category_list = $category_list[$jAux3]['sub_category_list'];
                if (count($sub_category_list) > 0) {
                    for ($jAux5=0; $jAux5 < count($sub_category_list); $jAux5++) { 
                        $category_id = $sub_category_list[$jAux5]['category_id'];
                        $category_name = $sub_category_list[$jAux5]['category_name'];
                        $count = $sub_category_list[$jAux5]['count'];
                        $sentiment = $sub_category_list[$jAux5]['sentiment'];
                        $text = $sub_category_list[$jAux5]['text'];
                        $score = $sub_category_list[$jAux5]['score'];
                        $relevance = $sub_category_list[$ijAux5]['relevance'];
                        $short_text = $sub_category_list[$jAux5]['short_text'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('subcategory_trip');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'category_id' => $category_id,
                                'category_name' => $category_name,
                                'text' => $text,
                                'sentiment' => $sentiment,
                                'count' => $count,
                                'relevance' => $relevance,
                                'short_text' => $short_text,
                                'score' => $score,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 32: " . $e;
                            echo $return;
                        }

                        $highlight_list = $sub_category_list[$jAux5]['highlight_list'];
                        if (count($highlight_list) > 0) {
                            for ($jAux4=0; $jAux4 < count($highlight_list); $jAux4++) { 
                                $text = $highlight_list[$jAux4]['text'];
                                $confidence = $highlight_list[$jAux4]['confidence'];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('highlight_sc_trip');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'text' => $text,
                                        'confidence' => $confidence,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 31: " . $e;
                                    echo $return;
                                }

                                $category_id_list = $highlight_list[$jAux4]['category_id_list'];
                                if (count($category_id_list) > 0) {
                                    $category = "";
                                    for ($jAux6=0; $jAux6 < count($category_id_list); $jAux6++) { 
                                        $category = $category_id_list[$jAux6];

                                        try {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('category_sc_trip');
                                            $insert->values(array(
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'category_id' => $category,
                                                'ty_id' => $ty_id
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 30: " . $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $summary_sentence_list = $category_list[$jAux3]['summary_sentence_list'];
                if (count($summary_sentence_list) > 0) {
                    for ($jAux7=0; $jAux7 < count($summary_sentence_list); $jAux7++) { 
                        $text = $summary_sentence_list[$jAux7]['text'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('sumary_sentence_trip');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'text' => $text,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 29: " . $e;
                            echo $return;
                        }

                        $category_id_list = $summary_sentence_list[$jAux7]['category_id_list'];
                        if (count($category_id_list) > 0) {
                            $category = "";
                            for ($jAux6=0; $jAux6 < count($category_id_list); $jAux6++) { 
                                $category = $category_id_list[$jAux6];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('category_ss_trip');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'category_id' => $category,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 28: " . $e;
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
$category_list = $response['category_list'];
if (count($category_list) > 0) {
    for ($jAux3=0; $jAux3 < count($category_list); $jAux3++) { 
        $category_id = $category_list[$jAux3]['category_id'];
        $category_name = $category_list[$jAux3]['category_name'];
        $count = $category_list[$jAux3]['count'];
        $sentiment = $category_list[$jAux3]['sentiment'];
        $text = $category_list[$jAux3]['text'];
        $score = $category_list[$jAux3]['score'];
        $relevance = $category_list[$jAux3]['relevance'];
        $short_text = $category_list[$jAux3]['short_text'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('category');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'category_id' => $category_id,
                'category_name' => $category_name,
                'text' => $text,
                'sentiment' => $sentiment,
                'count' => $count,
                'relevance' => $relevance,
                'short_text' => $short_text,
                'score' => $score,
                'ty_id' => $ty_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 27: " . $e;
            echo $return;
        }

        $highlight_list = $category_list[$jAux3]['highlight_list'];
        if (count($highlight_list) > 0) {
            for ($jAux4=0; $jAux4 < count($highlight_list); $jAux4++) { 
                $text = $highlight_list[$jAux4]['text'];
                $confidence = $highlight_list[$jAux4]['confidence'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('highlight_category');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'text' => $text,
                        'confidence' => $confidence,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 26: " . $e;
                    echo $return;
                }

                $category_id_list = $highlight_list[$jAux4]['category_id_list'];
                if (count($category_id_list) > 0) {
                    $category = "";
                    for ($jAux6=0; $jAux6 < count($category_id_list); $jAux6++) { 
                        $category = $category_id_list[$jAux6];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('category_category');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'category_id' => $category,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 25: " . $e;
                            echo $return;
                        }
                    }
                }
            }
        }
        $sub_category_list = $category_list[$jAux3]['sub_category_list'];
        if (count($sub_category_list) > 0) {
            for ($jAux5=0; $jAux5 < count($sub_category_list); $jAux5++) { 
                $category_id = $sub_category_list[$jAux5]['category_id'];
                $category_name = $sub_category_list[$jAux5]['category_name'];
                $count = $sub_category_list[$jAux5]['count'];
                $sentiment = $sub_category_list[$jAux5]['sentiment'];
                $text = $sub_category_list[$jAux5]['text'];
                $score = $sub_category_list[$jAux5]['score'];
                $relevance = $sub_category_list[$ijAux5]['relevance'];
                $short_text = $sub_category_list[$jAux5]['short_text'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('subcategory_category');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'category_id' => $category_id,
                        'category_name' => $category_name,
                        'text' => $text,
                        'sentiment' => $sentiment,
                        'count' => $count,
                        'relevance' => $relevance,
                        'short_text' => $short_text,
                        'score' => $score,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 24: " . $e;
                    echo $return;
                }

                $highlight_list = $sub_category_list[$jAux5]['highlight_list'];
                if (count($highlight_list) > 0) {
                    for ($jAux4=0; $jAux4 < count($highlight_list); $jAux4++) { 
                        $text = $highlight_list[$jAux4]['text'];
                        $confidence = $highlight_list[$jAux4]['confidence'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('highlight_sc_category');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'text' => $text,
                                'confidence' => $confidence,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 23: " . $e;
                            echo $return;
                        }

                        $category_id_list = $highlight_list[$jAux4]['category_id_list'];
                        if (count($category_id_list) > 0) {
                            $category = "";
                            for ($jAux6=0; $jAux6 < count($category_id_list); $jAux6++) { 
                                $category = $category_id_list[$jAux6];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('category_sc_category');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'category_id' => $category,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 22: " . $e;
                                    echo $return;
                                }
                            }
                        }
                    }
                }
            }
        }
        $summary_sentence_list = $category_list[$jAux3]['summary_sentence_list'];
        if (count($summary_sentence_list) > 0) {
            for ($jAux7=0; $jAux7 < count($summary_sentence_list); $jAux7++) { 
                $text = $summary_sentence_list[$jAux7]['text'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('ss_category');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'text' => $text,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 21: " . $e;
                    echo $return;
                }

                $category_id_list = $summary_sentence_list[$jAux7]['category_id_list'];
                if (count($category_id_list) > 0) {
                    $category = "";
                    for ($jAux6=0; $jAux6 < count($category_id_list); $jAux6++) { 
                        $category = $category_id_list[$jAux6];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('category_ss_category');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'category_id' => $category,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 20: " . $e;
                            echo $return;
                        }
                    }
                }
            }
        }
    }
}
$good_to_know_list = $response['good_to_know_list'];
if (count($good_to_know_list) > 0) {
    for ($jAux=0; $jAux < count($good_to_know_list); $jAux++) { 
        $category_id = $good_to_know_list[$jAux]['category_id'];
        $category_name = $good_to_know_list[$jAux]['category_name'];
        $count = $good_to_know_list[$jAux]['count'];
        $sentiment = $good_to_know_list[$jAux]['sentiment'];
        $text = $good_to_know_list[$jAux]['text'];
        $score = $good_to_know_list[$jAux]['score'];
        $relevance = $good_to_know_list[$jAux]['relevance'];
        $short_text = $good_to_know_list[$jAux]['short_text'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('goodlist');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'category_id' => $category_id,
                'category_name' => $category_name,
                'text' => $text,
                'sentiment' => $sentiment,
                'count' => $count,
                'relevance' => $relevance,
                'short_text' => $short_text,
                'score' => $score,
                'ty_id' => $ty_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 19: " . $e;
            echo $return;
        }

        $highlight_list = $good_to_know_list[$jAux]['highlight_list'];
        if (count($highlight_list) > 0) {
            for ($jAux2=0; $jAux2 < count($highlight_list); $jAux2++) { 
                $text = $highlight_list[$jAux2]['text'];
                $confidence = $highlight_list[$jAux2]['confidence'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('highlight_goodlist');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'text' => $text,
                        'confidence' => $confidence,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 18: " . $e;
                    echo $return;
                }

                $category_id_list = $highlight_list[$jAux2]['category_id_list'];
                if (count($category_id_list) > 0) {
                    $category = "";
                    for ($iaUX4=0; $iaUX4 < count($category_id_list); $iaUX4++) { 
                        $category = $category_id_list[$iaUX4];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('category_goodlist');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'category_id' => $category,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 17: " . $e;
                            echo $return;
                        }
                    }
                }
            }
        }
    }
}
$hotel_type_list = $response['hotel_type_list'];
if (count($hotel_type_list) > 0) {
    for ($k=0; $k < count($hotel_type_list); $k++) { 
        $category_id = $hotel_type_list[$kAux]['category_id'];
        $category_name = $hotel_type_list[$kAux]['category_name'];
        $count = $hotel_type_list[$kAux]['count'];
        $sentiment = $hotel_type_list[$kAux]['sentiment'];
        $text = $hotel_type_list[$kAux]['text'];
        $score = $hotel_type_list[$kAux]['score'];
        $relevance = $hotel_type_list[$kAux]['relevance'];
        $short_text = $hotel_type_list[$kAux]['short_text'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hotel_type');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'category_id' => $category_id,
                'category_name' => $category_name,
                'text' => $text,
                'sentiment' => $sentiment,
                'count' => $count,
                'relevance' => $relevance,
                'short_text' => $short_text,
                'score' => $score,
                'ty_id' => $ty_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 16: " . $e;
            echo $return;
        }

        $highlight_list = $hotel_type_list[$kAux]['highlight_list'];
        if (count($highlight_list) > 0) {
            for ($jAux4=0; $jAux4 < count($highlight_list); $jAux4++) { 
                $text = $highlight_list[$jAux4]['text'];
                $confidence = $highlight_list[$jAux4]['confidence'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('highlight_hotel_type');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'text' => $text,
                        'confidence' => $confidence,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 15: " . $e;
                    echo $return;
                }

                $category_id_list = $highlight_list[$jAux4]['category_id_list'];
                if (count($category_id_list) > 0) {
                    $category = "";
                    for ($jAux6=0; $jAux6 < count($category_id_list); $jAux6++) { 
                        $category = $category_id_list[$jAux6];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('category_hotel_type');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'category_id' => $category,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 14: " . $e;
                            echo $return;
                        }
                    }
                }
            }
        }
        $sub_category_list = $hotel_type_list[$kAux]['sub_category_list'];
        if (count($sub_category_list) > 0) {
            for ($jAux5=0; $jAux5 < count($sub_category_list); $jAux5++) { 
                $category_id = $sub_category_list[$jAux5]['category_id'];
                $category_name = $sub_category_list[$jAux5]['category_name'];
                $count = $sub_category_list[$jAux5]['count'];
                $sentiment = $sub_category_list[$jAux5]['sentiment'];
                $text = $sub_category_list[$jAux5]['text'];
                $score = $sub_category_list[$jAux5]['score'];
                $relevance = $sub_category_list[$ijAux5]['relevance'];
                $short_text = $sub_category_list[$jAux5]['short_text'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('subcategory');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'category_id' => $category_id,
                        'category_name' => $category_name,
                        'text' => $text,
                        'sentiment' => $sentiment,
                        'count' => $count,
                        'relevance' => $relevance,
                        'short_text' => $short_text,
                        'score' => $score,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 13: " . $e;
                    echo $return;
                }

                $highlight_list = $sub_category_list[$jAux5]['highlight_list'];
                if (count($highlight_list) > 0) {
                    for ($jAux4=0; $jAux4 < count($highlight_list); $jAux4++) { 
                        $text = $highlight_list[$jAux4]['text'];
                        $confidence = $highlight_list[$jAux4]['confidence'];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('highlight_subcategory');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'text' => $text,
                                'confidence' => $confidence,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 12: " . $e;
                            echo $return;
                        }

                        $category_id_list = $highlight_list[$jAux4]['category_id_list'];
                        if (count($category_id_list) > 0) {
                            $category = "";
                            for ($jAux6=0; $jAux6 < count($category_id_list); $jAux6++) { 
                                $category = $category_id_list[$jAux6];

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('category_subcategory');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'category_id' => $category,
                                        'ty_id' => $ty_id
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 11: " . $e;
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
$badge_list = $response['badge_list'];
if (count($badge_list) > 0) {
    for ($r=0; $r < count($badge_list); $r++) { 
        $text = $badge_list[$r]['text'];
        $subtext = $badge_list[$r]['subtext'];
        $badge_type = $badge_list[$r]['badge_type'];
        $badge_data = $badge_list[$r]['badge_data'];
        $category_id = $badge_data['category_id'];
        $category_name = $badge_data['category_name'];
        $global_popularity = $badge_data['global_popularity'];
        $popularity = $badge_data['popularity'];
        $score = $badge_data['score'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('badge');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'text' => $text,
                'subtext' => $subtext,
                'badge_type' => $badge_type,
                'badge_data' => $badge_data,
                'category_id' => $category_id,
                'category_name' => $category_name,
                'global_popularity' => $global_popularity,
                'popularity' => $popularity,
                'score' => $score,
                'ty_id' => $ty_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 10: " . $e;
            echo $return;
        }

        $highlight_list = $badge_list[$r]['highlight_list'];
        if (count($highlight_list) > 0) {
            for ($rAux=0; $rAux < count($highlight_list); $rAux++) { 
                $text = $highlight_list[$rAux]['text'];
                $confidence = $highlight_list[$rAux]['confidence'];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('highlight_badge');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'text' => $text,
                        'confidence' => $confidence,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 9: " . $e;
                    echo $return;
                }

                $category_id_list = $highlight_list[$rAux]['category_id_list'];
                if (count($category_id_list) > 0) {
                    $category = "";
                    for ($rAux2=0; $rAux2 < count($category_id_list); $rAux2++) { 
                        $category = $category_id_list[$rAux2];

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('category_badge');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'category_id' => $category,
                                'ty_id' => $ty_id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 8: " . $e;
                            echo $return;
                        }
                    }
                }
            }
        }
    }
}
$old_ty_ids = $response['old_ty_ids'];
if (count($old_ty_ids) > 0) {
    $old = "";
    for ($x=0; $x < count($old_ty_ids); $x++) { 
        $old = $old_ty_ids[$x];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('old_ty_ids');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'old' => $old,
                'ty_id' => $ty_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 7: " . $e;
            echo $return;
        }
    }
}
$summary = $response['summary'];
$score_description = $summary['score_description'];
$text = $summary['text'];
$global_popularity = $summary['global_popularity'];
$score = $summary['score'];
$hotel_type = $summary['hotel_type'];
$hotel_type_text = $hotel_type['text'];
$popular_with = $summary['popular_with'];
$popular_with_text = $popular_with['text'];
$popular_with_trip_type = $popular_with['trip_type'];
$location_nearby = $summary['location_nearby'];
$location_nearby_text = $location_nearby['text'];
$location = $summary['location'];
$location_text = $location['text'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('summary');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'score_description' => $score_description,
        'text' => $text,
        'global_popularity' => $global_popularity,
        'score' => $score,
        'hotel_type_text' => $hotel_type_text,
        'popular_with_text' => $popular_with_text,
        'popular_with_trip_type' => $popular_with_trip_type,
        'location_nearby_text' => $location_nearby_text,
        'location_text' => $location_text,
        'ty_id' => $ty_id
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "Error 6: " . $e;
    echo $return;
}

$reviews_distribution = $summary['reviews_distribution'];
if (count($reviews_distribution) > 0) {
    for ($y=0; $y < count($reviews_distribution); $y++) { 
        $reviews_count = $reviews_distribution[$y]['reviews_count'];
        $stars = $reviews_distribution[$y]['stars'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('reviews_distribution');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'reviews_count' => $reviews_count,
                'stars' => $stars,
                'ty_id' => $ty_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 5: " . $e;
            echo $return;
        }
    }
}
$summary_sentence_list = $summary['summary_sentence_list'];
if (count($summary_sentence_list) > 0) {
    for ($w=0; $w < count($summary_sentence_list); $w++) { 
        $text = $summary_sentence_list[$w]['text'];
        $sentiment = $summary_sentence_list[$w]['sentiment'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('summary_sentence');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'text' => $text,
                'sentiment' => $sentiment,
                'ty_id' => $ty_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 4: " . $e;
            echo $return;
        }

        $category_id_list = $summary_sentence_list[$w]['category_id_list'];
        if (count($category_id_list) > 0) {
            $category = "";
            for ($wAux=0; $wAux < count($category_id_list); $wAux++) { 
                $category = $category_id_list[$wAux];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('category_summary_sentence');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'category_id' => $category,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 3: " . $e;
                    echo $return;
                }
            }
        }
    }
}
$highlight_list = $summary['highlight_list'];
if (count($highlight_list) > 0) {
    for ($z=0; $z < count($highlight_list) ; $z++) { 
        $text = $highlight_list[$z]['text'];
        $confidence = $highlight_list[$z]['confidence'];
        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('highlight_list');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'text' => $text,
                'confidence' => $confidence,
                'ty_id' => $ty_id
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 2: " . $e;
            echo $return;
        }

        $category_id_list = $highlight_list[$z]['category_id_list'];
        if (count($category_id_list) > 0) {
            $category = "";
            for ($zAux=0; $zAux < count($category_id_list); $zAux++) { 
                $category = $category_id_list[$zAux];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('category_highlight_list');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'category_id' => $category,
                        'ty_id' => $ty_id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 1: " . $e;
                    echo $return;
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