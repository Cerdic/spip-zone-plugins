<?php

	// formulaires/spip_listes_inscriptions.php
	
	// $LastChangedRevision: 26478 $
	// $LastChangedBy: paladin@quesaco.org $
	// $LastChangedDate: 2009-02-09 11:25:06 +0100 (Lun 09 fév 2009) $
	
	// formulaire d'inscription
	// necessite SPIP >= 2
	
	include_spip('inc/acces');
	include_spip('inc/spiplistes_api');

function formulaires_spip_listes_inscription_charger_dist ($id_liste='')
{
	$valeurs = array(
		'email' => '',
		'id_liste' => $id_liste
	);
	
	return $valeurs;
}

function formulaires_spip_listes_inscription_verifier_dist ($id_liste='')
{
	$erreurs = array();
	
	// verifier que les champs obligatoires sont bien la :
	foreach(array('email') as $obligatoire) {
		if (!_request($obligatoire))
		{
			$erreurs[$obligatoire] = _T('spiplistes:champ_obligatoire');
		}
	}
	
	if (!in_array(_request('format_abo'), array('html','texte')))
	{
		$erreurs['format'] = 'format inconnu';
	}
	
	$listes = _request('listes');
	
	if (is_array($listes))
	{
		foreach($listes as $liste)
		{
			$id_liste = intval($liste);
			if(!$id_liste) 
			{
				$erreurs['liste'] = _T('spiplistes:liste_inconnue');
			}
		}
	}

	// verifier que si un email a ete saisi, il est bien valide :
	include_spip('inc/filtres');
	$email = _request('email');
	if ($email && !email_valide($email)) 
	{
		$erreurs['email'] = _T('spiplistes:cet_email_pas_valide');
	}
	
	//// Verifier si le mail est deja connu
	//if (email_valide(_request('email'))) {
	//	if (sql_getfetsel('id_auteur','spip_auteurs',"id_auteur !='".intval($id_auteur)."' AND email = '$email'")) {
	//		$erreurs['email'] = _T('spiplistes:cet_email_deja_enregistre');
	//	}
	//}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('spiplistes:saisie_erreurs');
	}
	
    return ($erreurs); // si c'est vide, traiter sera appele, sinon le formulaire sera re-soumis
}


function formulaires_spip_listes_inscription_traiter_dist ($id_liste='') {
	
	// enregistre dans spip_auteurs, spip_auteurs_elargis, spip_auteurs_listes			
			
	$val['email'] = _request('email');
	$val['nom'] = _request('email');
	$alea_actuel = creer_uniqid();
	$alea_futur = creer_uniqid();
	$val['alea_actuel'] = $alea_actuel;
	$val['alea_futur'] = $alea_futur;
	$val['low_sec'] = '';
	$val['statut'] = 'aconfirmer';
	
	$format = _request('format_abo');
	$listes = _request('listes');

	// si l'auteur existe deja, 
	$auteur = spiplistes_auteurs_auteur_select('id_auteur,statut,lang'
											   , 'email='.sql_quote($val['email']));
	if ($auteur)
	{
		$id_auteur = $auteur['id_auteur'];
		// reactiver le compte si necessaire
		if ($auteur['statut'] == '5poubelle')
		{
			spiplistes_auteurs_auteur_statut_modifier ($id_auteur, 'aconfirmer');
		}
		spiplistes_debug_log ('inscription auteur #'.$id_auteur.' email:'.$val['email']);
	}
	else
	{
		// creer le compte abonne'
		if ($id_auteur = spiplistes_auteurs_auteur_insertq ($val))
		{
			sql_insertq(
					'spip_auteurs_elargis'
				  , array('id_auteur'=>$id_auteur
						 ,'spip_listes_format'=>$format
						 )
				  );
		}
		spiplistes_debug_log ('NEW inscription email:'.$val['email']);
		$lang = $GLOBALS['meta']['langue_site'];
	}
	
	if ($listes) {
		foreach($listes as $liste) {
			sql_insertq ('spip_auteurs_listes'
					, array('id_auteur'=>$id_auteur
							,'id_liste'=>$liste
							)
					);
		}
	}
	
	// envoyer mail de confirmation
	if (
		spiplistes_envoyer_mail (
			$val['email']
			, _T('spiplistes:confirmation_inscription')
			, _T('spiplistes:inscription_reponses_s'
				 , array('s' => spiplistes_nom_site_texte ($lang))
				 )
	   )
	) {
		$contexte = array('message_ok'=>_T('spiplistes:demande_ok'),'editable' => false,);
	}
	else {
		$contexte = array('message_ok'=>_T('spiplistes:demande_ko'),'editable' => false,);
	}
	
	return ($contexte);
}

