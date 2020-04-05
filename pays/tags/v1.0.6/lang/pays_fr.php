<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'code' => 'Code ISO3166-1',

	// I
	'id_pays' => 'ID',

	// N
	'nom' => 'Nom du pays',

	// P
	'pays' => 'Pays',

	// T
	'titre_page_test' => 'La liste des pays du monde',
);

include_spip('base/abstract');
$pays_nom = sql_multi('nom', 'fr');
$select = sql_select(array("code",$pays_nom), 'spip_pays');


while ($r = sql_fetch($select)) {
    $GLOBALS[$GLOBALS['idx_lang']][strtolower($r['code'])] = $r['multi'];
}
?>
