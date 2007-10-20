<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

/********************************/
/* GESTION DU FORMULAIRE UPLOAD */
/********************************/

// Contexte du formulaire

function balise_FORMULAIRE_UPLOAD ($p) {
	$p = calculer_balise_dynamique($p,'FORMULAIRE_UPLOAD', array('id_rubrique', 'id_forum', 'id_article', 'id_breve', 'id_syndic'));
	return $p;
}


// http://doc.spip.org/@balise_FORMULAIRE_UPLOAD_stat
function balise_FORMULAIRE_UPLOAD_stat($args, $filtres) {
	if(!$args[5] || !preg_match(",\w+,",$args[5]))
		$args[5] = "upload";
	return $args;
}


// http://doc.spip.org/@balise_FORMULAIRE_UPLOAD_dyn
function balise_FORMULAIRE_UPLOAD_dyn(
	$id_rubrique, $id_forum, $id_article, $id_breve, $id_syndic, $fond
) {

	// Le contexte nous servira peut-etre a identifier
	// le type d'upload (est-ce destine a un article etc)
	// Pour l'instant on uploade tout dans la fiche auteur

	// id_rubrique est parfois passee pour les articles, on n'en veut pas
	$ids = array();
	if ($id_rubrique > 0 AND ($id_article OR $id_breve OR $id_syndic))
		$id_rubrique = 0;
	foreach (array('id_article', 'id_breve', 'id_rubrique', 'id_syndic', 'id_forum') as $o) {
		if ($x = intval($$o)) {
			$ids[$o] = $x;
			$id = $x;
			$type = str_replace('id_', '', $o);
		}
	}

	if (!$proprietaire = intval($GLOBALS['auteur_session']['id_auteur']))
		return false;


	if (!$type) {
		$type = 'auteur';
		$id = $proprietaire;
		$ids['id_auteur'] = $id;
	}

	include_spip('inc/autoriser');
	if (!autoriser('joindredocument', $type, $id))
		return false;

	$invalider = false;

	// supprimer des documents ?
	if (is_array(_request('supprimer')))
	foreach (_request('supprimer') as $supprimer) {
		if ($supprimer = intval($supprimer)
		AND $s = spip_query("SELECT * FROM spip_documents_${type}s WHERE id_${type}="._q($id)." AND id_document="._q($supprimer))
		AND $t = spip_fetch_array($s)) {
			include_spip('inc/documents');
			$s = spip_query("SELECT * FROM spip_documents WHERE id_document="._q($supprimer));
			$t = spip_fetch_array($s);
			unlink(copie_locale(get_spip_doc($t['fichier'])));
			spip_query("DELETE FROM spip_documents_${type}s WHERE id_document="._q($supprimer));
			spip_query("DELETE FROM spip_documents WHERE id_document="._q($supprimer));
			$invalider = true;
			spip_log("supprimer document ($type)".$supprimer, 'upload');
		}
	}

	// Ajouter un document
	if ($files = ($_FILES ? $_FILES : $HTTP_POST_FILES)) {
		spip_log($files, 'upload');
		include_spip('action/joindre');
		$joindre1 = charger_fonction('joindre1', 'inc');
		$joindre1($files, 'document', $type, $id, 0,
		 $hash, $redirect, $documents_actifs, $iframe_redirect);
		$invalider = true;
		spip_log($files, 'upload');
	}

	if ($invalider) {
		include_spip('inc/invalideur');
		suivre_invalideur("0",true);
		spip_log('invalider', 'upload');
	}

	return array('formulaires/'.$fond, 0,

	array_merge($ids,
	array(
		'url' => $script, # ce sur quoi on fait le action='...'
		'url_post' => $script_hidden, # pour les variables hidden
		'arg' => $arg,
		'hash' => $hash,
		'nobot' => _request('nobot'),
		'debug' => $debug /* un truc a afficher si on veut debug */
		))
	);
}

?>
