<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_DIR_LIB_GIS',_DIR_RACINE.'lib/mxn-gis-cluster-2.0.10/');

$table_des_traitements['VILLE'][]= 'typo(extraire_multi(%s))';
$table_des_traitements['PAYS'][]= 'typo(extraire_multi(%s))';

$GLOBALS['logo_libelles']['id_gis'] = _T('gis:libelle_logo_gis');
?>