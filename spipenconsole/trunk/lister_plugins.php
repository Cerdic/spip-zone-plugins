#!/usr/bin/php

<?php
chdir('../');
function prefixe_plugins(){
    if ($handle = opendir('.')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                chdir($entry);
                if (file_exists('paquet.xml')){
                    $xml = simplexml_load_file('paquet.xml');
                    $result=$xml->xpath("//@prefix");
                    $prefix=$result[0];
                    unset($xml);
                }
                chdir('../');
            }
        }
        closedir($handle);
    }
    return $prefix;
}


chdir('../ecrire/');
if (!defined('_DIR_RESTREINT_ABS')) define('_DIR_RESTREINT_ABS', '');
include_once _DIR_RESTREINT_ABS.'inc_version.php';

$nom_prefix_actifs = array();
$plugins_actifs = unserialize($GLOBALS['meta']['plugin']);
//recuperation d'un tableau des tous les prefix des plugins actifs
foreach ($plugins_actifs as $prefix => $tab) {
    $nom_prefix_actifs[]=strtolower($prefix);
}
//print_r($nom_prefix_actifs);


//Récuperation des noms et prefix de tous les plugins
include_spip('inc/plugin');
chdir('../plugins');
$plugins = liste_plugin_files();
$i=1;
echo sprintf ("%'_3s %3s %'_-30s %'_-20s %'_-20s"," n°","A","Nom","Version","Prefixe");
echo "\n";
foreach ($plugins as $key=>$repertoire) {
    chdir($repertoire);
    if (file_exists('paquet.xml')){
        $xml = simplexml_load_file('paquet.xml');
        $prefix=$xml->xpath("//@prefix");
        $version=$xml->xpath("//@version");
        $nom=$xml->xpath("//nom");
        //echo "prefix : $prefix[0] --";
        //echo "nom: $nom[0] \n";
        unset($xml);
    }
    else{
        if (file_exists('plugin.xml')){
            $xml = simplexml_load_file('plugin.xml');
            $prefix=$xml->xpath("//prefix");
            $version=$xml->xpath("//version");
            $nom=$xml->xpath("//nom");
            //echo "prefix : $prefix[0] --";
            //echo "nom: $nom[0] \n";
            unset($xml);
        }
    }
    chdir('../');
    $symbole=array_search($prefix[0],$nom_prefix_actifs) ? "*" : " ";
    $couleur=array_search($prefix[0],$nom_prefix_actifs) ? "\033[32m" : "\033[0m";
    //echo "$symbole : $i  $nom[0] - [$prefix[0]] \n";
    if($i%2 == 1){
        echo $couleur;    
        echo sprintf ("%3s %3s %-30s %-20s %-30s",$i,$symbole,$nom[0],$version[0],$prefix[0]);
        echo "\033[0m";
    }
    else{
        echo $couleur;    
        echo sprintf ("%3s %3s %'--30s %'--20s %-30s",$i,$symbole,$nom[0],$version[0],$prefix[0]);
        echo "\033[0m";
    }
    echo "\n";
    $i++;
}
echo "\n";

?>
