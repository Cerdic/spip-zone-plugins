<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
$GLOBALS[$GLOBALS['idx_lang']] = array(
	// C
	'code' => 'ISO3166-1 code',
	
	// I
	'id_pays' => 'ID',
	
	// N
	'nom' => 'Country name',

	// P
	'pays' => 'Country',

	// T
	'titre_page_test' => 'List of Planet Earth countries',
);

include_spip('base/abstract');
$pays_nom = sql_multi('nom', 'en');
$select = sql_select(array("code",$pays_nom), 'spip_pays');

while ($r = sql_fetch($select)) {
    $GLOBALS[$GLOBALS['idx_lang']][strtolower($r['code'])] = $r['multi'];
}
?>
