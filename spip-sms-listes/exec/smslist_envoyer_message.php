<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/presentation');
include_spip('inc/smslist_affichage');
include_spip('base/forms_base_api');
include_spip('public/assembler');

function exec_smslist_envoyer_message(){
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	if (!autoriser('configurer','smslist')) {
		echo $commencer_page(_L('Spip-sms-listes'),"", "redacteurs", "smslist");
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		echo fin_page();
		exit;
	}
	
	// Admin Spip-sms-listes
	echo $commencer_page(_L('Spip-sms-listes'),"", "redacteurs", "envoyer");
	
	echo debut_gauche("smslist",true);
	
	echo smslist_barre_nav_gauche('envoyer');

	echo creer_colonne_droite();

	echo debut_droite("smslist",true);
	
	$GLOBALS['forms_actif_exec'][] = 'smslist_envoyer_message';
	$liste = Forms_liste_tables('smslist_boiteenvoi');
	$id_form = reset($liste);
	$contexte = array('id_form'=>$id_form,'id_donnee'=>0,'type_form'=>'smslist_boiteenvoi','titre_liste'=>_L("Envoyer un message"),'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
	$formulaire = recuperer_fond("modeles/form",$contexte);
	echo "<div class='verdana2'>$formulaire</div>";

	echo fin_gauche(), fin_page();
}

?>