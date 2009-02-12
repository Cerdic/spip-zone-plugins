<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

function formulaires_forum_prive_charger_dist($id_rubrique, $id_forum, $id_article, $id_breve, $id_syndic, $id_message, $afficher_texte, $statut, $titre, $url_param_retour = NULL) {

	// Tableau des valeurs servant au calcul d'une signature de securite.
	// Elles seront placees en Input Hidden pour que inc/forum_insert
	// recalcule la meme chose et verifie l'identite des resultats.
	// Donc ne pas changer la valeur de ce tableau entre le calcul de
	// la signature et la fabrication des Hidden
	// Faire attention aussi a 0 != ''

	// id_rubrique est parfois passee pour les articles, on n'en veut pas
	$ids = array();
	if ($id_rubrique > 0 AND ($id_article OR $id_breve OR $id_syndic))
		$id_rubrique = 0;
	foreach (array('id_article', 'id_breve', 'id_forum', 'id_rubrique', 'id_syndic') as $o) {
		$ids[$o] = ($x = intval($$o)) ? $x : '';
	}


	// ne pas mettre '', sinon le squelette n'affichera rien.
	$previsu = ' ';

	// pour les hidden
	$script_hidden = "";
	foreach ($ids as $id => $v)
		$script_hidden .= "<input type='hidden' name='$id' value='$v' />";
		
	$config = array();
	foreach(array('afficher_barre','forum_titre','forums_texte','forums_urlref') as $k)
		$config[$k] = ' ';

	return array(
		'nom_site' => '',
		'table' => $table,
		'texte' => '',
		'config' => $config,
		'titre' => str_replace('~', ' ', extraire_multi($titre)),
		'action' => $url_param_retour?$url_param_retour:self(), # ce sur quoi on fait le action='...'
		'_hidden' => $script_hidden, # pour les variables hidden
		'url_site' => "http://",
		'id_forum' => $id_forum, // passer id_forum au formulaire pour lui permettre d'afficher a quoi l'internaute repond
		'_sign'=>implode('_',$ids)
	);
}


function formulaires_forum_prive_verifier_dist($id_rubrique, $id_forum, $id_article, $id_breve, $id_syndic, $id_message, $afficher_texte, $statut, $titre, $url_param_retour = NULL) {
	include_spip('inc/acces');
	include_spip('inc/texte');
	include_spip('inc/forum');
	include_spip('inc/session');
	include_spip('base/abstract_sql');

	$erreurs = array();

	// desactiver id_rubrique si un id_article ou autre existe dans le contexte
	if ($id_article OR $id_breve OR $id_forum OR $id_syndic)
		$id_rubrique = 0;


	if (strlen($texte = _request('texte')) < 10 AND $GLOBALS['meta']['forums_texte'] == 'oui')
		$erreurs['texte'] = _T('forum_attention_dix_caracteres');
	else if (defined('_FORUM_LONGUEUR_MAXI')
	AND _FORUM_LONGUEUR_MAXI > 0
	AND strlen($texte) > _FORUM_LONGUEUR_MAXI)
		$erreurs['texte'] = _T('forum_attention_trop_caracteres',
			array(
				'compte' => strlen($texte),
				'max' => _FORUM_LONGUEUR_MAXI
			));

	if (strlen($titre=_request('titre')) < 3
	AND $GLOBALS['meta']['forums_titre'] == 'oui')
		$erreurs['titre'] = _T('forum_attention_trois_caracteres');

	if (!count($erreurs) AND !_request('confirmer_previsu_forum')){
		if ($afficher_texte != 'non') {
			$previsu = inclure_forum_prive_previsu($texte, $titre, _request('url_site'), _request('nom_site'), _request('ajouter_mot'), $doc);
			$erreurs['previsu'] = $previsu;
		}
	}

	return $erreurs;
}


function inclure_forum_prive_previsu($texte,$titre, $url_site, $nom_site, $ajouter_mot, $doc){
	$bouton = _T('forum_message_definitif');
	include_spip('public/assembler');
	include_spip('public/composer');
	// supprimer les <form> de la previsualisation
	// (sinon on ne peut pas faire <cadre>...</cadre> dans les forums)
	return preg_replace("@<(/?)form\b@ism",
			    '<\1div',
		inclure_balise_dynamique(array('formulaires/inc-forum_prive_previsu',
		      0,
		      array(
			'titre' => safehtml(typo($titre)),
			'texte' => safehtml(propre($texte)),
			'notes' => safehtml(calculer_notes()),
			'url_site' => vider_url($url_site),
			'nom_site' => safehtml(typo($nom_site)),
			'ajouter_mot' => (is_array($ajouter_mot) ? $ajouter_mot : array($ajouter_mot)),
			'ajouter_document' => $doc,
			'erreur' => $erreur,
			'bouton' => $bouton
			)
					       ),
					 false));
}


function formulaires_forum_prive_traiter_dist($id_rubrique, $id_forum, $id_article, $id_breve, $id_syndic, $id_message, $afficher_texte, $statut, $titre, $url_param_retour = NULL) {

	$forum_insert = charger_fonction('forum_insert', 'inc');
	set_request('retour_forum',$url_param_retour);
	
	list($redirect,$id_forum) = $forum_insert($statut);
	return array('redirect'=>$redirect,'id_forum'=>$id_forum);
}


?>
