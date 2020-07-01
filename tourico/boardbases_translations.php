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
            $id = $data[0];
            $name_en_us = $data[1];
            $name_he_il = $data[2];
            $name_es_es = $data[3];
            $name_pt_pt = $data[4];
            $name_ru_ru = $data[5];
            $name_fr_fr = $data[6];
            $name_de_de = $data[7];
            $name_ja_jp = $data[8];
            $name_it_it = $data[9];
            $name_zh_cn = $data[10];
            $name_ko_kr = $data[11];
            $name_pl_pl = $data[12];
            $name_zh_tw = $data[13];
            $name_nl_nl = $data[14];
            $name_da_dk = $data[15];
            $name_sv_se = $data[16];
            $name_ar_sa = $data[17];
            $name_el_gr = $data[18];
            $name_cs_cz = $data[19];
            $name_bg_bg = $data[20];
            $name_ro_ro = $data[21];
            $name_tr_tr = $data[22];
            $name_fi_fi = $data[23];
            $name_nb_no = $data[24];
            $name_th_th = $data[25];
            $name_ms_my = $data[26];
            $name_id_id = $data[27];

            $name_en_us = str_replace('"', '', $name_en_us);
            $name_en_us = mb_convert_encoding($name_en_us, "UTF-8");
            $name_he_il = str_replace('"', '', $name_he_il);
            $name_he_il = mb_convert_encoding($name_he_il, "UTF-8");
            $name_es_es = str_replace('"', '', $name_es_es);
            $name_es_es = mb_convert_encoding($name_es_es, "UTF-8");
            $name_pt_pt = str_replace('"', '', $name_pt_pt);
            $name_pt_pt = mb_convert_encoding($name_pt_pt, "UTF-8");
            $name_ru_ru = str_replace('"', '', $name_ru_ru);
            $name_ru_ru = mb_convert_encoding($name_ru_ru, "UTF-8");
            $name_fr_fr = str_replace('"', '', $name_fr_fr);
            $name_fr_fr = mb_convert_encoding($name_fr_fr, "UTF-8");
            $name_de_de = str_replace('"', '', $name_de_de);
            $name_de_de = mb_convert_encoding($name_de_de, "UTF-8");
            $name_ja_jp = str_replace('"', '', $name_ja_jp);
            $name_ja_jp = mb_convert_encoding($name_ja_jp, "UTF-8");
            $name_it_it = str_replace('"', '', $name_it_it);
            $name_it_it = mb_convert_encoding($name_it_it, "UTF-8");
            $name_zh_cn = str_replace('"', '', $name_zh_cn);
            $name_zh_cn = mb_convert_encoding($name_zh_cn, "UTF-8");
            $name_ko_kr = str_replace('"', '', $name_ko_kr);
            $name_ko_kr = mb_convert_encoding($name_ko_kr, "UTF-8");
            $name_pl_pl = str_replace('"', '', $name_pl_pl);
            $name_pl_pl = mb_convert_encoding($name_pl_pl, "UTF-8");
            $name_zh_tw = str_replace('"', '', $name_zh_tw);
            $description = mb_convert_encoding($description, "UTF-8");
            $name_nl_nl = str_replace('"', '', $name_nl_nl);
            $name_nl_nl = mb_convert_encoding($name_nl_nl, "UTF-8");
            $name_da_dk = str_replace('"', '', $name_da_dk);
            $name_da_dk = mb_convert_encoding($name_da_dk, "UTF-8");
            $name_sv_se = str_replace('"', '', $name_sv_se);
            $name_sv_se = mb_convert_encoding($name_sv_se, "UTF-8");
            $name_ar_sa = str_replace('"', '', $name_ar_sa);
            $name_ar_sa = mb_convert_encoding($name_ar_sa, "UTF-8");
            $name_el_gr = str_replace('"', '', $name_el_gr);
            $name_el_gr = mb_convert_encoding($name_el_gr, "UTF-8");
            $name_cs_cz = str_replace('"', '', $name_cs_cz);
            $name_cs_cz = mb_convert_encoding($name_cs_cz, "UTF-8");
            $name_bg_bg = str_replace('"', '', $name_bg_bg);
            $name_bg_bg = mb_convert_encoding($name_bg_bg, "UTF-8");
            $name_ro_ro = str_replace('"', '', $name_ro_ro);
            $name_ro_ro = mb_convert_encoding($name_ro_ro, "UTF-8");
            $name_tr_tr = str_replace('"', '', $name_tr_tr);
            $name_tr_tr = mb_convert_encoding($name_tr_tr, "UTF-8");
            $name_fi_fi = str_replace('"', '', $name_fi_fi);
            $name_fi_fi = mb_convert_encoding($name_fi_fi, "UTF-8");
            $name_nb_no = str_replace('"', '', $name_nb_no);
            $name_nb_no = mb_convert_encoding($name_nb_no, "UTF-8");
            $name_th_th = str_replace('"', '', $name_th_th);
            $name_th_th = mb_convert_encoding($name_th_th, "UTF-8");
            $name_ms_my = str_replace('"', '', $name_ms_my);
            $name_ms_my = mb_convert_encoding($name_ms_my, "UTF-8");
            $name_id_id = str_replace('"', '', $name_id_id);
            $name_id_id = mb_convert_encoding($name_id_id, "UTF-8");

            try {               
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('boardbases');
                $insert->values(array(
                    'id' => $id,
                    'name_en_us' => $name_en_us,
                    'name_he_il' => $name_he_il,
                    'name_es_es' => $name_es_es,
                    'name_pt_pt' => $name_pt_pt,
                    'name_ru_ru' => $name_ru_ru,
                    'name_fr_fr' => $name_fr_fr,
                    'name_de_de' => $name_de_de,
                    'name_ja_jp' => $name_ja_jp,
                    'name_it_it' => $name_it_it,
                    'name_zh_cn' => $name_zh_cn,
                    'name_ko_kr' => $name_ko_kr,
                    'name_pl_pl' => $name_pl_pl,
                    'name_zh_tw' => $name_zh_tw,
                    'name_nl_nl' => $name_nl_nl,
                    'name_da_dk' => $name_da_dk,
                    'name_sv_se' => $name_sv_se,
                    'name_ar_sa' => $name_ar_sa,
                    'name_el_gr' => $name_el_gr,
                    'name_cs_cz' => $name_cs_cz,
                    'name_bg_bg' => $name_bg_bg,
                    'name_ro_ro' => $name_ro_ro,
                    'name_tr_tr' => $name_tr_tr,
                    'name_fi_fi' => $name_fi_fi,
                    'name_nb_no' => $name_nb_no,
                    'name_th_th' => $name_th_th,
                    'name_ms_my' => $name_ms_my,
                    'name_id_id' => $name_id_id
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

readCSV("THF_Supplements.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>