<?php

function exec_console(){
	global $connect_statut;
	global $connect_id_auteur;
	global $connect_toutes_rubriques;
	global $spip_lang_right;

	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {
		$liste_auteur_console_active = array();
		if (isset($GLOBALS['meta']['console']))
			$liste_auteur_console_active = unserialize($GLOBALS['meta']['console']);
		$console_active = in_array($connect_id_auteur,$liste_auteur_console_active);
		
		if (_request("activer")){
			include_spip('inc/metas');
			$liste_auteur_console_active = array_merge($liste_auteur_console_active,array($connect_id_auteur));
			ecrire_meta('console',serialize($liste_auteur_console_active));
			ecrire_metas();
			redirige_par_entete(generer_url_ecrire('console'));
		}
		if (_request("desactiver")){
			include_spip('inc/metas');
			$liste_auteur_console_active = array_diff($liste_auteur_console_active,array($connect_id_auteur));
			ecrire_meta('console',serialize($liste_auteur_console_active));
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


echo "<script>
	$(document).ready(function(){
		$('#belle_console').click(function(e){
		e.preventDefault;
		document.location = '../?page=logs'; 
		});
	});
	</script>";
	
	echo "<div style='text-align:center'>
		<input id='belle_console' type='submit' name='afficher_belle_console' value='"._L('Afficher la belle console')."' class='fondo'></div>";

	echo generer_url_post_ecrire('console');
	
	if ($console_active){
		echo "<div style='text-align:$spip_lang_right'>
		<input type='submit' name='desactiver' value='"._L('Desactiver la console')."' class='fondo'></div>";
	}
	else {
		echo "<div style='text-align:$spip_lang_right'>
		<input type='submit' name='activer' value='"._L('Activer la console')."' class='fondo'></div>";
	}
	echo "</form>";
	echo "<p>";
	$urlpopup = generer_url_ecrire('console_popup');
	echo "<a href='$urlpopup' onclick='window.open(this.href,\"console\",\"scrollbars=no, resize=yes, width=300,height=600\");return false;'>";
	echo _L('Ouvrir la console dans une popup');
	echo "</a>";
	
	
	fin_page();
}

?>