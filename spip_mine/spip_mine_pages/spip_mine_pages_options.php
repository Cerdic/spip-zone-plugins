<?php

// chemin pour les fichiers squelettes
// $GLOBALS['dossier_squelettes'] = find_in_path($fond."squelettes");
_chemin(_DIR_PLUGIN_SPIP_MINE_PAGES.'squelettes/');


include_spip('base/spip_mine_pages_fonctions.php');

// Pour faire prendre en compte la fonction "propre" pour tous les champs de type "texte"
// pour le passage par le filtre "propre" sur certains champs texte identifiés
global $table_des_traitements;

define('_TRAITEMENT_TYPO', 'typo(%s, "TYPO", $connect)'); // champs courts
define('_TRAITEMENT_RACCOURCIS', 'propre(%s, $connect)'); // champs longs

$table_des_traitements['OBJECTIFS'][]	= _TRAITEMENT_RACCOURCIS;

global $table_date;
$table_date['spip_mine_pages']='date_creation';


?>