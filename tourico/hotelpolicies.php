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
echo "COMECOU READ CSV<br/>";
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
    
function readCSV(string $filename){
    $config = new \Zend\Config\Config(include '../config/autoload/global.tourico.php');
    $config = [
        'driver' => $config->db->driver,
        'database' => $config->db->database,
        'username' => $config->db->username,
        'password' => $config->db->password,
        'hostname' => $config->db->hostname
    ];
    $db = new \Zend\Db\Adapter\Adapter($config);

    $object = fopen($filename, 'r');
    $line = 0;

    while ($data = fgetcsv($object, 0, "|")) {
        if ($line > 0) {
            $hotelid = $data[0];
            $hotelpolicies_en_us = $data[1];
            $hotelpolicies_he_il = $data[2];
            $hotelpolicies_es_es = $data[3];
            $hotelpolicies_pt_pt = $data[4];
            $hotelpolicies_ru_ru = $data[5];
            $hotelpolicies_fr_fr = $data[6];
            $hotelpolicies_de_de = $data[7];
            $hotelpolicies_ja_jp = $data[8];
            $hotelpolicies_it_it = $data[9];
            $hotelpolicies_zh_cn = $data[10];
            $hotelpolicies_ko_kr = $data[11];
            $hotelpolicies_pl_pl = $data[12];
            $hotelpolicies_zh_tw = $data[13];
            $hotelpolicies_nl_nl = $data[14];
            $hotelpolicies_da_dk = $data[15];
            $hotelpolicies_sv_se = $data[16];
            $hotelpolicies_el_gr = $data[17];
            $hotelpolicies_cs_cz = $data[18];
            $hotelpolicies_bg_bg = $data[19];
            $hotelpolicies_ro_ro = $data[20];
            $hotelpolicies_tr_tr = $data[21];
            $hotelpolicies_fi_fi = $data[22];
            $hotelpolicies_nb_no = $data[23];
            $hotelpolicies_th_th = $data[24];
            $hotelpolicies_ms_my = $data[25];
            $hotelpolicies_id_id = $data[26];
            echo "ID: ". $hotelid ."<br/>";
            
            $hotelpolicies_en_us = str_replace('"', '', $hotelpolicies_en_us);
            $hotelpolicies_he_il = str_replace('"', '', $hotelpolicies_he_il);
            $hotelpolicies_es_es = str_replace('"', '', $hotelpolicies_es_es);
            $hotelpolicies_pt_pt = str_replace('"', '', $hotelpolicies_pt_pt);
            $hotelpolicies_ru_ru = str_replace('"', '', $hotelpolicies_ru_ru);
            $hotelpolicies_fr_fr = str_replace('"', '', $hotelpolicies_fr_fr);
            $hotelpolicies_de_de = str_replace('"', '', $hotelpolicies_de_de);
            $hotelpolicies_ja_jp = str_replace('"', '', $hotelpolicies_ja_jp);
            $hotelpolicies_it_it = str_replace('"', '', $hotelpolicies_it_it);
            $hotelpolicies_zh_cn = str_replace('"', '', $hotelpolicies_zh_cn);
            $hotelpolicies_ko_kr = str_replace('"', '', $hotelpolicies_ko_kr);
            $hotelpolicies_pl_pl = str_replace('"', '', $hotelpolicies_pl_pl);
            $hotelpolicies_zh_tw = str_replace('"', '', $hotelpolicies_zh_tw);
            $hotelpolicies_nl_nl = str_replace('"', '', $hotelpolicies_nl_nl);
            $hotelpolicies_da_dk = str_replace('"', '', $hotelpolicies_da_dk);
            $hotelpolicies_sv_se = str_replace('"', '', $hotelpolicies_sv_se);
            $hotelpolicies_el_gr = str_replace('"', '', $hotelpolicies_el_gr);
            $hotelpolicies_cs_cz = str_replace('"', '', $hotelpolicies_cs_cz);
            $hotelpolicies_bg_bg = str_replace('"', '', $hotelpolicies_bg_bg);
            $hotelpolicies_ro_ro = str_replace('"', '', $hotelpolicies_ro_ro);
            $hotelpolicies_tr_tr = str_replace('"', '', $hotelpolicies_tr_tr);
            $hotelpolicies_fi_fi = str_replace('"', '', $hotelpolicies_fi_fi);
            $hotelpolicies_nb_no = str_replace('"', '', $hotelpolicies_nb_no);
            $hotelpolicies_th_th = str_replace('"', '', $hotelpolicies_th_th);
            $hotelpolicies_ms_my = str_replace('"', '', $hotelpolicies_ms_my);
            $hotelpolicies_id_id = str_replace('"', '', $hotelpolicies_id_id);

            try {               
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hotelpolicies');
                $insert->values(array(
                    'hotelid' => $hotelid,
                    'hotelpolicies_en_us' => $hotelpolicies_en_us,
                    'hotelpolicies_he_il' => $hotelpolicies_he_il,
                    'hotelpolicies_es_es' => $hotelpolicies_es_es,
                    'hotelpolicies_pt_pt' => $hotelpolicies_pt_pt,
                    'hotelpolicies_ru_ru' => $hotelpolicies_ru_ru,
                    'hotelpolicies_fr_fr' => $hotelpolicies_fr_fr,
                    'hotelpolicies_de_de' => $hotelpolicies_de_de,
                    'hotelpolicies_ja_jp' => $hotelpolicies_ja_jp,
                    'hotelpolicies_it_it' => $hotelpolicies_it_it,
                    'hotelpolicies_zh_cn' => $hotelpolicies_zh_cn,
                    'hotelpolicies_ko_kr' => $hotelpolicies_ko_kr,
                    'hotelpolicies_pl_pl' => $hotelpolicies_pl_pl,
                    'hotelpolicies_zh_tw' => $hotelpolicies_zh_tw,
                    'hotelpolicies_nl_nl' => $hotelpolicies_nl_nl,
                    'hotelpolicies_da_dk' => $hotelpolicies_da_dk,
                    'hotelpolicies_sv_se' => $hotelpolicies_sv_se,
                    'hotelpolicies_el_gr' => $hotelpolicies_el_gr,
                    'hotelpolicies_cs_cz' => $hotelpolicies_cs_cz,
                    'hotelpolicies_bg_bg' => $hotelpolicies_bg_bg,
                    'hotelpolicies_ro_ro' => $hotelpolicies_ro_ro,
                    'hotelpolicies_tr_tr' => $hotelpolicies_tr_tr,
                    'hotelpolicies_fi_fi' => $hotelpolicies_fi_fi,
                    'hotelpolicies_nb_no' => $hotelpolicies_nb_no,
                    'hotelpolicies_th_th' => $hotelpolicies_th_th,
                    'hotelpolicies_ms_my' => $hotelpolicies_ms_my,
                    'hotelpolicies_id_id' => $hotelpolicies_id_id 
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO: ". $e;
                echo $return;
            }
        }
        $line = $line + 1;
    }
    fclose($filename);
}

readCSV("HotelInfo/PDS2_HotelPolicies_THF.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>