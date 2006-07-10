<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

// On prend l'email dans le contexte de maniere a ne pas avoir a le
// verifier dans la base ni a le devoiler au visiteur


function balise_FORMULAIRE_ECRIRE_MESSAGE ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_ECRIRE_MESSAGE', array('id_auteur', 'id_article', 'email'));
}

function balise_FORMULAIRE_ECRIRE_MESSAGE_stat($args, $filtres) {
	include_spip('inc/filtres');

	// rien a faire : l'id_auteur est celui de l'auteur connecte
	// Pas d'id_auteur ni d'id_article ? Erreur de squelette

	// OK
	return $args;
}

function balise_FORMULAIRE_ECRIRE_MESSAGE_dyn() {
	include_spip('inc/texte');
	$puce = definir_puce();

	global $auteur_session;
	if (isset($auteur_session['id_auteur']))
		$id_auteur=$auteur_session['id_auteur'];
	else
		$id_auteur=0;


	$sujet = _request('sujet_message');
	$texte = _request('texte_message');
	$destinataires = _request('destinataires');
	
	$destko = $texte && !count($destinataires);
	$validable = $texte && $sujet && (!$destko);

	// doit-on envoyer le message ?
	if ($validable
	AND _request('confirmer')) { 
		$texte = propre($texte);
		$date_heure=date('Y-m-d H:i:s');
		$id_message = spip_abstract_insert("spip_messages",
				"(titre,texte,type,date_heure,date_fin,rv,statut,id_auteur,maj)",
				"(".spip_abstract_quote($sujet).",".spip_abstract_quote($texte).",'normal','$date_heure','$date_heure','non','publie',$id_auteur,NOW())");

		if ($id_message!=0){
			foreach($destinataires as $id_dest){
				spip_query("INSERT INTO spip_auteurs_messages (id_message, id_auteur, vu) VALUES ($id_message, ".spip_abstract_quote($id_dest).",'non');");
			}
		}
		$messageenvoye = _T('form_prop_message_envoye');
	}

	return 
		array('formulaires/formulaire_ecrire_message', 0,
			array(
			'self' => str_replace("&amp;","&",self()),
			'id_auteur' => $id_auteur,
			'messageko' => $messageko ? _T('form_prop_indiquer_email') : '',
			'destinataires' => $destinataires,
			'sujetko' => ($texte && !$sujet) ? _T('form_prop_indiquer_sujet') : '',
			'messageenvoye' => $messageenvoye,
			'sujet' => $sujet,
			'texte' => $texte,
			'valide' => $validable ? $id_auteur : '',
			'bouton' => _T('form_prop_envoyer'),
			'boutonconfirmation' => $validable ? _T('form_prop_confirmer_envoi') : '',
			'tri' => (_request('tri')!==NULL)?_request('tri'):'date_heure',
			'senstri' => (_request('senstri')!==NULL)?_request('senstri'):'0',
			)
		);
}
?>
