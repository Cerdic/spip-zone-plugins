<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 *
 * Formulaire pour deplacer des documents dans le sens album <=> album, ou album <=> portfolio
 * Pour album <=> portfolio, il faut renseigner les parametres $objet et $id_objet
 * pour album <=> album, pas la peine
 * Le champ *deplacements* est invisible et rempli via Jquery, il indique les traitements a effectuer
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_albums_deplacer_documents_charger_dist(){
	$valeurs = array();
	$valeurs['deplacements'] = '';

	return $valeurs;
}

function formulaires_albums_deplacer_documents_verifier_dist($objet='',$id_objet=''){
	$erreurs = array();

	$chaine_deplacements = _request('deplacements');

	if (strlen(trim($chaine_deplacements)) AND is_string($chaine_deplacements) AND $deplacements = json_decode($chaine_deplacements,true)) {

		// verifier que tous les docs dissocies vont bien etre associes ailleurs
		// on pourrait sans doute simplifier avec un array_intersect mais bon...
		$total_associer = array();
		$total_dissocier = array();
		foreach ($deplacements as $alias_objet => $actions) {
			foreach ($actions['associer'] as $docs) array_push($total_associer,$docs);
			foreach ($actions['dissocier'] as $docs) array_push($total_dissocier,$docs);
		}
		if (count(array_diff($total_associer,$total_dissocier)) != 0) {
			$message_erreur = _T('album:erreur_deplacement');
		//
		// si le compte est bon, continuer
		} else {
			include_spip('action/editer_liens');
			foreach ($deplacements as $alias_objet_traitement => $actions) {
				// objet_traitement = objet sur lequel on va associer/dissocier un document
				list($objet_traitement, $id_objet_traitement) = preg_split('/_/', $alias_objet_traitement);

				// verifier que l on a le droit de modifier objet_traitement
				if (!autoriser('modifier', $objet_traitement, $id_objet_traitement)) {
					$message_erreur = _T('album:erreur_deplacement');
					break;
				}

				// objet_traitement doit etre soit album, soit objet du contexte
				if (
					$objets_valides = ($objet) ? array($objet,"album") : array("album")
					AND ( !in_array($objet_traitement,$objets_valides) )
				) {
					$message_erreur = _T('album:erreur_deplacement');
					break;
				}

				// si objet_traitement == objet du contexte, verifier que l id correspond
				if (
					$objet
					AND ($objet_traitement == $objet AND $id_objet_traitement != $id_objet)
				) {
					$message_erreur = _T('album:erreur_deplacement');
					break;
				}

				// si dissocier, verifier que le doc appartient bien a l objet
				foreach ($actions['dissocier'] as $id_document) {
					if (count(lien_find('document','id_document','spip_documents_liens',intval($id_document),array($objet_traitement=>intval($id_objet_traitement)))) === 0) {
						$message_erreur = _T('album:erreur_deplacement');
						break;
					}
				}

			}
		}
	} else {
		$message_erreur = _T('album:erreur_deplacement');
	}

	if ($message_erreur) {
		$erreurs['message_erreur'] = $message_erreur;
	}

	return $erreurs;
}

function formulaires_albums_deplacer_documents_traiter_dist(){

	$deplacements = json_decode(_request('deplacements'),true);
	include_spip('action/editer_liens');
	foreach ($deplacements as $alias_objet_traitement => $actions) {
		list($objet_traitement, $id_objet_traitement) = preg_split('/_/', $alias_objet_traitement);
		foreach ($actions as $action => $documents) {
			if ($action == 'associer') {
				foreach ($documents as $id_document)
					objet_associer(array('document' => intval($id_document)), array($objet_traitement => intval($id_objet_traitement)));
			}
			if ($action == 'dissocier') {
				foreach ($documents as $id_document)
					objet_dissocier(array('document' => intval($id_document)), array($objet_traitement => intval($id_objet_traitement)));
			}
		}
	}

	$res = array();
	$res['editable'] = true;
	$res['message_ok'] = _T('info_modification_enregistree');

	return $res;
}


?>
