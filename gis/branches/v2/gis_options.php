<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_DIR_LIB_GIS','lib/mxn-gis-2.2.2/');

$table_des_traitements['VILLE'][]= 'typo(extraire_multi(%s))';
$table_des_traitements['PAYS'][]= 'typo(extraire_multi(%s))';

$GLOBALS['logo_libelles']['id_gis'] = _T('gis:libelle_logo_gis');

?>