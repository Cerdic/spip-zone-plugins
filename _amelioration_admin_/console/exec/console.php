<?php

function exec_console(){
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $spip_lang_right;

	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {
		if (_request("activer")){
			include_spip('inc/metas');
			ecrire_meta('console','active');
			ecrire_metas();
			redirige_par_entete(generer_url_ecrire('console'));
		}
		if (_request("desactiver")){
			include_spip('inc/metas');
			effacer_meta('console');
			ecrire_metas();
			redirige_par_entete(generer_url_ecrire('console'));
		}
	}
	
	include_ecrire("inc_presentation");

	debut_page("Suivi des logs", "", "");
	
	echo "<br><br><br>";
	gros_titre("Suivi des logs");
	
	debut_gauche();
	
	debut_droite();
	
	if ($connect_statut != "0minirezo" OR !$connect_toutes_rubriques) {
		echo "<B>Vous n'avez pas acc&egrave;s &agrave; cette page.</B>";
		exit;
	}

	echo generer_url_post_ecrire('console');
	if (isset($GLOBALS['meta']['console'])){
		echo "<div style='text-align:$spip_lang_right'>
		<input type='submit' name='desactiver' value='"._L('Desactiver la console')."' class='fondo'></div>";
	}
	else {
		echo "<div style='text-align:$spip_lang_right'>
		<input type='submit' name='activer' value='"._L('Activer la console')."' class='fondo'></div>";
	}
	echo "</form>";

	
	fin_page();

}

?>