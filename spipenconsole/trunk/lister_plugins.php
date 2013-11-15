#!/usr/bin/php

<?php
chdir($argv[1]);
chdir('../../ecrire/');
if (!defined('_DIR_RESTREINT_ABS')) define('_DIR_RESTREINT_ABS', '');
include_once _DIR_RESTREINT_ABS.'inc_version.php';

$nom_prefix_actifs = array();
$plugins_actifs = unserialize($GLOBALS['meta']['plugin']);

//recuperation d'un tableau de tous les prefix des plugins actifs
foreach ($plugins_actifs as $prefix => $tab) {
    $nom_prefix_actifs[]=strtolower($prefix);
}
//Récuperation des noms et prefix de tous les plugins
include_spip('inc/plugin');
chdir('../plugins');
$plugins = liste_plugin_files();
$i=1;
echo sprintf ("%'_3s %3s %'_-30s %'_-10s %'_-15s"," n°","A","Nom","Version","Prefixe");
echo "\n";
//Récupération du nom, préfixe et numéro de version des plugins disponibles
//dans le répertoire plugins
//Affichage formaté
$racine=getcwd();
foreach ($plugins as $key=>$repertoire) {
    chdir("$racine/$repertoire");
    if (file_exists('paquet.xml')){
        $xml = simplexml_load_file('paquet.xml');
        $prefix=$xml->xpath("//@prefix");
        $version=$xml->xpath("//@version");
        $nom=$xml->xpath("//nom");
        unset($xml);
    }
    else{
        if (file_exists('plugin.xml')){
            $xml = simplexml_load_file('plugin.xml');
            $prefix=$xml->xpath("//prefix");
            $version=$xml->xpath("//version");
            $nom=$xml->xpath("//nom");
            unset($xml);
        }
    }
    chdir('../');
    // Si le plugin est actif, initialisation de la variable symbole et couleur
    $symbole=array_search(strtolower($prefix[0]),$nom_prefix_actifs) ? "*" : " ";
    $couleur=array_search(strtolower($prefix[0]),$nom_prefix_actifs) ? "\033[32m" : "\033[0m";
    //Affichage formaté
    if($i%2 == 1){
        echo $couleur;    
        echo sprintf ("%3s %3s %-30s %-10s %-20s",$i,$symbole,$nom[0],$version[0],$prefix[0]);
        echo "\033[0m";
    }
    else{
        echo $couleur;    
        echo sprintf ("%3s %3s %'--30s %'--10s %-20s",$i,$symbole,$nom[0],$version[0],$prefix[0]);
        echo "\033[0m";
    }
    echo "\n";
    $i++;
}
echo "\n";
