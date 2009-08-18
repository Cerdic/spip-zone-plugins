<?php
function exec_savecfg() {
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('savecfg:titre'), "", "");
	include_spip('inc/cfg');
	$arbo = liste_cfg();
	$fonds = array();
	foreach ($arbo as $prem => $deux) {
		$req = sql_countsel('spip_meta', 'nom='.sql_quote($prem));
		if ($req > 0) {
			$fonds[$prem] = $deux;
		}
	}
	echo recuperer_fond('prive/savecfg', array('arbo' => $fonds));
}
?>