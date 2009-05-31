<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_marqueur_stats(){
	global $connect_statut,$connect_toutes_rubriques;
	
	include_spip("inc/presentation");
	
	if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques) {
		if (($nb=_request('cron'))!=NULL){
			Sirtaqui_syndique (intval($nb));
			envoie_image_vide();
			exit();
		}
	}

	debut_page(_L("Marqueur Statistiques"),  "marqueur_stats", "statistiques");

	debut_gauche();
	
	debut_boite_info();
	echo propre(_L('Cette page vous permet de configurer le code du marqueur statistique a inserer sur vos pages publiques'));
	fin_boite_info();
	
	debut_droite();
	gros_titre(_L('Marqueur Statistiques'));
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	// parametres generaux : id-syndication et url web service
	echo generer_url_post_ecrire('marqueur_stats');
	
	$marqueur_stats = _request('marqueur_stats');
	if ($marqueur_stats!==NULL){
		include_spip('inc/meta');
		ecrire_meta('marqueur_stats',$marqueur_stats);
		ecrire_metas();
	}

	$marqueur_stats = isset($GLOBALS['meta']['marqueur_stats'])?$GLOBALS['meta']['marqueur_stats']:"";
	echo "<div>";
	echo "<label for='marqueur_stats'><strong>"._L('Code de votre marqueur')."</strong></label><br/>";
	echo "<textarea label='marqueur_stats' name='marqueur_stats' class='formo' />";
	echo entites_html($marqueur_stats);
	echo "</textarea>";
	echo "</div>";
	echo "<p style='text-align:right;'>";
	echo "<input type='submit' name='submit' value='"._T('Modifier')."' class='fondo' />";
	echo "</p></div></form>";
		
	fin_page();
}


?>