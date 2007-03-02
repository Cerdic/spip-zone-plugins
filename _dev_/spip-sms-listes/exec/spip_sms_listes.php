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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/smslist_affichage');
include_spip('base/forms_base_api');
include_spip('public/assembler');

function exec_spip_sms_listes() {
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	if (!autoriser('administrer','smslist')) {
		echo $commencer_page(_L('Spip-sms-listes'),"", "redacteurs", "smslist");
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		echo fin_page();
		exit;
	}
	
	// Admin Spip-sms-listes
	echo $commencer_page(_L('Spip-sms-listes'),"", "redacteurs", "smslist");
	//echo smslist_onglets("smslist", "Spip-SMS-Listes");
	
	echo debut_gauche("smslist",true);
	//echo smslist_raccourcis();
	
	echo smslist_barre_nav_gauche('accueil');

	echo creer_colonne_droite();

	echo debut_droite("smslist",true);
	
	if (_request('message')){
		$GLOBALS['forms_actif_exec'][] = 'spip_sms_listes';
		$liste = Forms_liste_tables('smslist_boiteenvoi');
		$id_form = reset($liste);
		$contexte = array('id_form'=>$id_form,'id_donnee'=>0,'type_form'=>'smslist_boiteenvoi','titre_liste'=>_L("Envoyer un message"),'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
		$formulaire = recuperer_fond("modeles/form",$contexte);
		echo "<div class='verdana2'>$formulaire</div>";
	}
	else {
		
		// messages en preparation
		foreach(Forms_liste_tables('smslist_message') as $id_form){
			$contexte = array('id_form'=>$id_form,
			'titre_liste'=>_T("smslist:messages_en_redaction"),
			'aucune_reponse'=>" ",
			'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee'],
			'statuts' => array('prepa'),
			'affiche_rang'=>0,
			'affiche_de'=>1,
			'colonne_extra_titre'=>"<img src='"._DIR_PLUGIN_SMSLIST. "img_pack/envoyer-message-16.png' width='16' height='16' alt='"._L('Envoyer')."' />",
			'colonne_extra_url'=>generer_url_action('smslist_envoyer_message'));
			echo recuperer_fond("exec/template/donnees_tous",$contexte);	
		}
	
		// messages en cours d'envoi
		foreach(Forms_liste_tables('smslist_boiteenvoi') as $id_form){
			$contexte = array('id_form'=>$id_form,
			'titre_liste'=>_T("smslist:envois_programmes"),
			'aucune_reponse'=>" ",
			'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee'],
			'statuts' => array('prepa'),
			'affiche_rang'=>0,
			'affiche_de'=>1,
			);
			echo recuperer_fond("exec/template/donnees_tous",$contexte);	
		}
	}
	
	if (_request('var_mode')=='test'){
		$smslist_envoyer = charger_fonction('smslist_envoyer','inc');
		$smslist_envoyer();
	}
	
	echo fin_gauche(), fin_page();
}

?>