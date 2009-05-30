<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) {
	include_spip('balise/abcalendrier_20');
} else {
   include_spip('balise/abcalendrier_19');
}
?>