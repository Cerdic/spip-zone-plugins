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
	echo gros_titre(_T("smslist:spip_sms_liste"),"",false);
	
	smslist_liberer_messages();
	
	// messages en preparation
	foreach(Forms_liste_tables('smslist_message') as $id_form){
		$contexte = array('id_form'=>$id_form,
		'titre_liste'=>_T("smslist:messages_en_redaction"),
		'aucune_reponse'=>" ",
		'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee'],
		'statuts' => array('prepa'),
		'affiche_rang'=>0,
		'affiche_de'=>1,
		'colonne_extra_titre'=>"<img src='"._DIR_PLUGIN_SMSLIST. "img_pack/envoyer-message-16.png' width='16' height='16' alt='"._T('icone_envoyer_message')."' />",
		'colonne_extra_url'=>generer_url_action('smslist_envoyer_message')
		);
		echo recuperer_fond("exec/template/donnees_tous",$contexte);	
	}

	// messages en attende d'envoi
	foreach(Forms_liste_tables('smslist_boiteenvoi') as $id_form){
		$contexte = array('id_form'=>$id_form,
		'titre_liste'=>_T("smslist:envois_programmes"),
		'aucune_reponse'=>" ",
		'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee'],
		'statuts' => array('prepa'),
		'affiche_rang'=>0,
		'affiche_de'=>1,
		'colonne_extra_titre'=>"<img src='"._DIR_IMG_PACK. "supprimer.gif' width='24' height='24' alt='"._T('bouton_annuler')."' />",
		'colonne_extra_url'=>generer_url_action('smslist_instituer_envoi','statut=poubelle')
		);
		echo $s1=recuperer_fond("exec/template/donnees_tous",$contexte);	
	}
	// messages interrompus
	foreach(Forms_liste_tables('smslist_boiteenvoi') as $id_form){
		$contexte = array('id_form'=>$id_form,
		'titre_liste'=>_T("smslist:envois_interrompus"),
		'aucune_reponse'=>" ",
		'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee'],
		'statuts' => array('refuse'),
		'affiche_rang'=>0,
		'affiche_de'=>1,
		'colonne_extra_titre'=>"<img src='"._DIR_PLUGIN_SMSLIST. "img_pack/envoyer-message-16.png' width='16' height='16' alt='"._T('smslist:reprendre_envoi')."' />",
		'colonne_extra_url'=>generer_url_action('smslist_instituer_envoi','statut=prop')
		);
		echo $s2=recuperer_fond("exec/template/donnees_tous",$contexte);	
	}	
	// messages en cours d'envoi
	foreach(Forms_liste_tables('smslist_boiteenvoi') as $id_form){
		$contexte = array('id_form'=>$id_form,
		'titre_liste'=>_T("smslist:envois_en_cours"),
		'aucune_reponse'=>" ",
		'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee'],
		'statuts' => array('prop'),
		'affiche_rang'=>0,
		'affiche_de'=>1,
		'colonne_extra_titre'=>"<img src='"._DIR_IMG_PACK. "warning-24.gif' width='24' height='24' alt='"._T('smslist:stopper_envoi')."' />",
		'colonne_extra_url'=>generer_url_action('smslist_instituer_envoi','statut=refuse')
		);
		echo $s3=recuperer_fond("exec/template/donnees_tous",$contexte);
		if (_request('var_mode')!='test')
			echo smslist_boite_autocron();
	}
	
	if (_request('var_mode')=='test'){
		$smslist_envoyer = charger_fonction('smslist_envoyer','inc');
		$smslist_envoyer();
	}
	
	if (strlen(trim(supprimer_tags($s1)))==0 
	AND strlen(trim(supprimer_tags($s2)))==0 
	AND strlen(trim(supprimer_tags($s3)))==0){
		echo "<p />";
		echo recuperer_fond("exec/fond/envois",array());
	}
	
	echo fin_gauche(), fin_page();
}


function smslist_liberer_messages(){
	$res = spip_query("SELECT id_donnee_liee FROM spip_forms_donnees_donnees WHERE id_donnee=".(0-$GLOBALS['auteur_session']['id_auteur']));
	while ($row = spip_fetch_array($res)){
		$id_donnee = $row['id_donnee_liee'];
		$res2 = spip_query("SELECT d.id_form,d.statut FROM spip_forms_donnees AS d JOIN spip_forms AS f ON f.id_form=d.id_form WHERE f.type_form='smslist_message' AND id_donnee="._q($id_donnee));
		if ($row2 = spip_fetch_array($res2)){
			spip_query("DELETE FROM spip_forms_donnees_donnees WHERE id_donnee=".(0-$GLOBALS['auteur_session']['id_auteur'])." AND id_donnee_liee="._q($id_donnee));
			if ($row2['statut']=='prop'){
				// regarder si pas d'autre lien sur ce message
				$res3 = spip_query("SELECT * FROM spip_forms_donnees_donnees WHERE id_donnee_liee="._q($id_donnee));
				if (!spip_fetch_array($res3))
					spip_query("UPDATE spip_forms_donnees SET statut='prepa' WHERE statut='prop' AND id_donnee="._q($id_donnee));
			}
		}
	}
	// declencher d'eventuels envois pour que le panneau soit a jour
	$smslist_envoyer = charger_fonction('smslist_envoyer','inc');
	smslist_demon_boite_envoi();
}

function smslist_boite_autocron(){
	global $spip_lang_right;
	if (!defined('_SMSLIST_AUTOCRON_PROCESSUS'))
		define('_SMSLIST_AUTOCRON_PROCESSUS',1);
	$out = "";
	$smslist_envoyer = charger_fonction('smslist_envoyer','inc');
	# si rien a faire, pas de boite autocron
	if (!$smslist_envoyer(true)) return $out;
	list($total,$restant) = smslist_compter_spool();
	if (!($total>0)) return $out;

	$out .= "<div style='text-align:$spip_lang_right'>";
	$href = generer_action_auteur('smslist_envoi_lot','envoyer');
	for ($i=0;$i<_SMSLIST_AUTOCRON_PROCESSUS;$i++)
	$out .= "<span id='proc$i' class='processus' name='$href'></span>";
		
	$out .= "<span id='envoi_statut'>"._T('smslist:envois_restants')." "
		  . "<strong id='envois_restants'>$restant</strong>/<span id='envois_total'>$total</span> (<span id='envois_restant_pourcent'>"
		  . round($restant/$total*100)."</span>%)</span>";
		 
	
	if (_request('exec')=='spip_sms_listes')
		$out .= "<a href='".generer_url_ecrire('spip_sms_listes')."' id='redirect_after'></a>";
	$out .= "<img src='"._DIR_PLUGIN_SMSLIST."img_pack/smslist-appeler-24.png' width='24' height='24' />";
	$out .= "</div>";
	$out .= "<script type='text/javascript' src='"._DIR_PLUGIN_SMSLIST."javascript/autocron.js'></script>";
	return $out;
}

?>