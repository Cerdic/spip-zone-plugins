<?php
	global $connect_statut, $connect_toutes_rubriques;
	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		debut_page();
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
?>