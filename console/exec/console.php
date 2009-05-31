<?php

function exec_console(){
	global $connect_statut;
	global $connect_id_auteur;
	global $connect_toutes_rubriques;
	global $spip_lang_right;
	include_spip("inc/headers");

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
	
	include_spip("inc/presentation");

	$vieilledef = ($GLOBALS['spip_version']<10000) ? true:false;
	if ($vielledef){
		debut_page("Suivi des logs", "", "");
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page("Suivi des logs", "","");
	}

	
	echo "<br /><br /><br />";
	
	if ($vielledef) gros_titre("Suivi des logs");
	else echo gros_titre("Suivi des logs",'',false);
	
	if ($vielledef) debut_gauche();
	else echo debut_gauche('',true);
	
	if ($vielledef) debut_droite();
	else echo debut_droite('',true);
	
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

	$action = generer_url_ecrire('console');
	echo "\n<form action='$action' method='post'>"
		.form_hidden($action);		
	
	
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
	
	
	if ($vielledef) fin_page();
	else echo fin_page('',false);
}

?>
