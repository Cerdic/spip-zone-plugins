<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


include_spip('base/abstract_sql');
include_spip('inc/texte');
include_spip('inc/messages');

/**
 * Traitement de la saisie de #FORMULAIRE_RECOMMANDER
 * recommande, donc ...
 *
 * @return string
 */
function formulaires_recommander_traiter_dist(){
	$dests = _request('destinataires');
	$texte = propre(_request('texte'));
	$out = _T("ecrire_message:message_envoye_erreur");
	$exp = $GLOBALS['visiteur_session']['id_auteur'];
	$profil_decrire = charger_fonction('profil_decrire','inc');
	$qui = $profil_decrire($exp,true);
	
	charger_generer_url();
	$vin = sql_fetsel('id_article,millesime,domaines.nom as domaine','spip_vins as vins inner join spip_domaines as domaines ON vins.id_domaine=domaines.id_domaine','id_vin='.intval($id_vin));
	$vin['titre_vin'] = "<a href='".generer_url_article($vin['id_article'])."'>".$vin['domaine'].($vin['millesime']?" - ".$vin['millesime']:'')."</a>";

	$objet = _T('recommander:x_vous_recommande_le_vin',array_merge($qui,$vin));

	$redirect = "";
	
	
	list($auteurs_dest,$email_dests) = messagerie_destiner($dests);
	
	$id_message = 0;
	$general = false;
	if (in_array(_EMAIL_GENERAL,$dests)&&($GLOBALS['visiteur_session']['statut']=='0minirezo'))
		$general = true;

	if ($id_message = messagerie_messager($objet, $texte, $auteurs_dest,$general, 'recommandervin')){
		$notification = charger_fonction('notifications','inc');
		$notification('recommandervin',$id_vin,array('destinataires'=>$auteurs_dests,'id_message'=>$id_message,'abstract'=>couper($texte,30)));
		include_spip('inc/invalideur');
		suivre_invalideur("recommandervin/$id_vin");
		
		$out = _T("ecrire_message:message_envoye");
	}
	$texte = textebrut($texte);
	$texte = $texte . _T('ecrire_message:texte_email_rejoignez',array('nom_site'=>$GLOBALS['meta']['nom_site'],'url_site'=>$GLOBALS['meta']['adresse_site']));
	$objet = textebrut($objet);
	if (messagerie_mailer($objet,$texte,$email_dests)){
		$out = _T("ecrire_message:message_envoye");
	}

	return $out;
}

?>
