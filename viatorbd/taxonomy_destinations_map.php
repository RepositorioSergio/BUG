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
echo $return;
echo "START MAPPING...";
echo $return;
$config = new \Zend\Config\Config(include '../config/autoload/global.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select id, destinationname, destinationtype, parentid, destinationid from viator_taxonomydestinations where mapped=0 and destinationtype='COUNTRY'";
echo $return;
echo $sql;
echo $return;
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
try {
    $results = $statement->execute();
    $results->buffer();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
if ($results instanceof ResultInterface && $results->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($results);
    foreach ($resultSet as $row) {
        $id = (int) $row["id"];
        $destinationid = (int) $row['destinationid'];
        $parentid = (int) $row["parentid"];
        $destinationname = $row["destinationname"];
        if ($destinationname == "USA") {
            $destinationname = "United States";
        } elseif ($destinationname == "England") {
            $destinationname = "United Kingdom";
        }
        $destinationtype = $row["destinationtype"];
        $sql = "select id from countries where name='" . $destinationname . "'";
        $statement = $db->createStatement($sql);
        try {
            $statement->prepare();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        try {
            $statement = $statement->execute();
            $statement->buffer();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        if ($statement->valid()) {
            $row = $statement->current();
            $country_id = $row['id'];
            $sql = "update viator_taxonomydestinations set country_id=" . $country_id . ", zone_id=0, city_id=0, mapped=1 where id=" . $id;
            echo $return;
            echo $sql;
            echo $return;
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $row_settings = $statement->execute();
        }
    }
}
echo $return;
echo $sql;
echo $return;
$sql = "select id, destinationname, destinationtype, parentid, destinationid from viator_taxonomydestinations where mapped=0 and destinationtype='REGION'";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
try {
    $results = $statement->execute();
    $results->buffer();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
if ($results instanceof ResultInterface && $results->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($results);
    foreach ($resultSet as $row) {
        $id = (int) $row["id"];
        $destinationid = (int) $row['destinationid'];
        $parentid = (int) $row["parentid"];
        $destinationname = $row["destinationname"];
        $destinationtype = $row["destinationtype"];
        $sql = "select country_id from viator_taxonomydestinations where destinationid=" . $parentid . " and country_id!=0";
        echo $return;
        echo $sql;
        echo $return;
        $statement = $db->createStatement($sql);
        try {
            $statement->prepare();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        try {
            $statement = $statement->execute();
            $statement->buffer();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        if ($statement->valid()) {
            $row = $statement->current();
            $country_id = (int) $row['country_id'];
            $sql = "select id from zones where name='" . $destinationname . "' and country_id=" . $country_id;
            echo $return;
            echo $sql;
            echo $return;
            $statement = $db->createStatement($sql);
            try {
                $statement->prepare();
            } catch (\Exception $e) {
                echo $return;
                echo $e->getMessage();
                echo $return;
                die();
            }
            try {
                $statement = $statement->execute();
                $statement->buffer();
            } catch (\Exception $e) {
                echo $return;
                echo $e->getMessage();
                echo $return;
                die();
            }
            if ($statement->valid()) {
                $row = $statement->current();
                $zone_id = $row['id'];
                $sql = "update viator_taxonomydestinations set country_id=$country_id, zone_id=" . $zone_id . ", city_id=0, mapped=1 where id=" . $id;
                echo $return;
                echo $sql . "<br>";
                echo $return;
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $row_settings = $statement->execute();
            }
        }
    }
}
$sql = "select id, destinationname, destinationtype, parentid, destinationid, iatacode from viator_taxonomydestinations where mapped=0 and destinationtype='CITY' and iatacode!=''";
echo $return;
echo $sql;
echo $return;
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
try {
    $results = $statement->execute();
    $results->buffer();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
if ($results instanceof ResultInterface && $results->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($results);
    foreach ($resultSet as $row) {
        $id = (int) $row["id"];
        $iata = (string) $row["iatacode"];
        $destinationid = (int) $row['destinationid'];
        $parentid = (int) $row["parentid"];
        $destinationname = $row["destinationname"];
        $destinationtype = $row["destinationtype"];
        $sql = "select country_id, zone_id from viator_taxonomydestinations where destinationid=" . $parentid . " and country_id!=0 and zone_id!=0";
        echo $return;
        echo $sql;
        echo $return;
        $statement = $db->createStatement($sql);
        try {
            $statement->prepare();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        try {
            $statement = $statement->execute();
            $statement->buffer();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        if ($statement->valid()) {
            $row = $statement->current();
            $country_id = (int) $row['country_id'];
            $zone_id = (int) $row['zone_id'];
            $sql = "select id, zone_id, country_id from cities where (iata_code='" . $iata . "' or iata_code_2='" . $iata . "' or iata_code_3='" . $iata . "' or iata_code_4='" . $iata . "' or iata_code_5='" . $iata . "' or iata_code_6='" . $iata . "' or iata_code_7='" . $iata . "') or (name='" . $destinationname . "' and country_id=$country_id)";
            echo $return;
            echo $sql;
            echo $return;
            $statement = $db->createStatement($sql);
            try {
                $statement->prepare();
            } catch (\Exception $e) {
                echo $return;
                echo $e->getMessage();
                echo $return;
                die();
            }
            try {
                $statement = $statement->execute();
                $statement->buffer();
            } catch (\Exception $e) {
                echo $return;
                echo $e->getMessage();
                echo $return;
                die();
            }
            if ($statement->valid()) {
                $row = $statement->current();
                $city_id = $row['id'];
                if ($zone_id == 0) {
                    $zone_id = $row['zone_id'];
                }
                $sql = "update viator_taxonomydestinations set country_id=$country_id, zone_id=$zone_id, city_id=" . $city_id . ", mapped=1 where id=" . $id;
                echo $return;
                echo $sql;
                echo $return;
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $row_settings = $statement->execute();
            }
        }
    }
}
//
// Map via IATA Code
//
// Repeat for non regions
//
$sql = "select id, destinationname, destinationtype, parentid, destinationid from viator_taxonomydestinations where mapped=0 and destinationtype='CITY'";
echo $return;
echo $sql;
echo $return;
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
try {
    $results = $statement->execute();
    $results->buffer();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
if ($results instanceof ResultInterface && $results->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($results);
    foreach ($resultSet as $row) {
        $id = (int) $row["id"];
        $destinationid = (int) $row['destinationid'];
        $parentid = (int) $row["parentid"];
        $destinationname = $row["destinationname"];
        $destinationtype = $row["destinationtype"];
        $sql = "select country_id, zone_id from viator_taxonomydestinations where destinationid=" . $parentid . " and country_id!=0";
        echo $return;
        echo $sql;
        echo $return;
        $statement = $db->createStatement($sql);
        try {
            $statement->prepare();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        try {
            $statement = $statement->execute();
            $statement->buffer();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        if ($statement->valid()) {
            $row = $statement->current();
            $country_id = (int) $row['country_id'];
            $zone_id = (int) $row['zone_id'];
            $sql = "select id, zone_id, country_id from cities where name='" . $destinationname . "' and country_id=$country_id";
            echo $return;
            echo $sql;
            echo $return;
            $statement = $db->createStatement($sql);
            try {
                $statement->prepare();
            } catch (\Exception $e) {
                echo $return;
                echo $e->getMessage();
                echo $return;
                die();
            }
            try {
                $statement = $statement->execute();
                $statement->buffer();
            } catch (\Exception $e) {
                echo $return;
                echo $e->getMessage();
                echo $return;
                die();
            }
            if ($statement->valid()) {
                $row = $statement->current();
                $city_id = $row['id'];
                if ($zone_id == 0) {
                    $zone_id = $row['zone_id'];
                }
                $sql = "update viator_taxonomydestinations set country_id=$country_id, zone_id=$zone_id, city_id=" . $city_id . ", mapped=1 where id=" . $id;
                echo $return;
                echo $sql;
                echo $return;
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $row_settings = $statement->execute();
            }
        }
    }
}
//
// Repeat for non regions
//
$sql = "select id, destinationname, destinationtype, parentid, destinationid from viator_taxonomydestinations where mapped=0 and destinationtype='CITY'";
echo $return;
echo $sql;
echo $return;
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
try {
    $results = $statement->execute();
    $results->buffer();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
if ($results instanceof ResultInterface && $results->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($results);
    foreach ($resultSet as $row) {
        $id = (int) $row["id"];
        $destinationid = (int) $row['destinationid'];
        $parentid = (int) $row["parentid"];
        $destinationname = $row["destinationname"];
        $destinationtype = $row["destinationtype"];
        $sql = "select country_id, zone_id from viator_taxonomydestinations where destinationid=" . $parentid . " and country_id!=0";
        echo $return;
        echo $sql;
        echo $return;
        $statement = $db->createStatement($sql);
        try {
            $statement->prepare();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        try {
            $statement = $statement->execute();
            $statement->buffer();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        if ($statement->valid()) {
            $row = $statement->current();
            $country_id = (int) $row['country_id'];
            $zone_id = (int) $row['zone_id'];
            $sql = "select id, zone_id, country_id from cities where name='" . $destinationname . "' and country_id=$country_id";
            echo $return;
            echo $sql;
            echo $return;
            $statement = $db->createStatement($sql);
            try {
                $statement->prepare();
            } catch (\Exception $e) {
                echo $return;
                echo $e->getMessage();
                echo $return;
                die();
            }
            try {
                $statement = $statement->execute();
                $statement->buffer();
            } catch (\Exception $e) {
                echo $return;
                echo $e->getMessage();
                echo $return;
                die();
            }
            if ($statement->valid()) {
                $row = $statement->current();
                $city_id = $row['id'];
                if ($zone_id == 0) {
                    $zone_id = $row['zone_id'];
                }
                $sql = "update viator_taxonomydestinations set country_id=$country_id, zone_id=$zone_id, city_id=" . $city_id . ", mapped=1 where id=" . $id;
                echo $return;
                echo $sql;
                echo $return;
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $row_settings = $statement->execute();
            }
        }
    }
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo $return;
echo "END MAPPING...";
echo $return;
?>