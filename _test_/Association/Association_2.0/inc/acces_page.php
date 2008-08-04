<?php
	global $connect_statut, $connect_toutes_rubriques;
	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page();
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
?>