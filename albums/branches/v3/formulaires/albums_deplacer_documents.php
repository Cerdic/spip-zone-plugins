<?php
/**
 * Formulaire permettant de déplacer des documents entre albums par cliquer-glisser.
 *
 * Les paramètres $objet et $id_objet sont optionnels.
 * Les utiliser permet de prendre en compte également les documents des portfolios de l'objet.
 * L'input `_deplacements` est invisible et rempli via jQuery, il indique les traitements à effectuer.
 * Une fois convertie en tableau, la chaîne est de la forme :
 *
 *     ```
 *     objetX => [associer  => [a,b]]
 *            => [dissocier => [y,z]]
 *     objetY => ...
 *     ```
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Formulaires
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement du formulaire de déplacements de documents.
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @param string $objet
 *     Type d'objet auquel sont associés les albums.
 *     A renseigner si on veut prendre en compte les documents des portfolios.
 * @param int|string $id_objet
 *     Identifiant de l'objet.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_albums_deplacer_documents_charger_dist($objet='',$id_objet=''){
	// option pas activée = pas de chocolat
	include_spip('inc/config');
	if (!lire_config('albums/deplacer_documents','')) $valeurs['editable'] = false;
	$valeurs = array();
	// champ contenant la liste des déplacements à effectuer (auto complété par js)
	$valeurs['_deplacements'] = '';
	// on a besoin de «objet» et «id_objet» dans l'environnement pour le script
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;
	// js renvoyé par la fonction traiter
	$valeurs['_js'] = _request('_js');

	return $valeurs;
}

/**
 * Vérifications du formulaire de déplacements de documents.
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs.
 *
 * @param string $objet
 *     Type d'objet auquel sont associés les albums.
 *     A renseigner si on veut prendre en compte les documents des portfolios.
 * @param int|string $id_objet
 *     Identifiant de l'objet.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_albums_deplacer_documents_verifier_dist($objet='',$id_objet=''){
	$erreurs = array();

	if (_request('valider')){

		if (
			is_array($deplacements=json_decode(_request('_deplacements'),true))
			AND count($deplacements=array_filter($deplacements))
		) {

			// vérifier que les tous les documents dissociés sont également associés
			$associers = array();
			$dissociers = array();
			foreach ($deplacements as $alias_objet => $actions) {
				if (isset($actions['associer']) AND count($actions['associer']))
					foreach ($actions['associer'] as $doc)
						$associers[] = $doc;
				if (isset($actions['dissocier']) AND count($actions['dissocier']))
					foreach ($actions['dissocier'] as $doc)
						$dissociers[] = $doc;
			}
			if (count(array_diff($associers,$dissociers)) > 0) {
				$message_erreur = _T('album:erreur_deplacement');
			}
			// si le compte est bon, continuer
			else {
				include_spip('action/editer_liens');
				foreach ($deplacements as $alias=>$actions) {

					// objet_action = objet sur lequel on va associer/dissocier un document
					list($objet_action, $id_objet_action) = preg_split('/_/',$alias);
					$id_objet_action = intval($id_objet_action);

					// si l'objet traité n'est pas un album,
					// ce doit être l'objet du contexte et un objet connu
					if (
						$objet_action != "album"
						AND (
							$objet_action != $objet
							OR $id_objet_action != $id_objet
							OR !in_array($objet_action,array_keys(lister_types_surnoms()))
						)
					) {
						$message_erreur = _T('album:erreur_deplacement');
						break;
					}

					// verifier qu'on a le droit de modifier l'objet
					if (!autoriser('modifier', $objet_action, $id_objet_action)) {
						$message_erreur = _T('album:erreur_deplacement');
						break;
					}

					// si associer, vérifier que les docs ne sont pas déjà liés à l'objet
					// (pour éviter de les dissocier d'un autre objet par erreur)
					if (isset($actions['associer']) AND count($actions['associer'])) {
						foreach ($actions['associer'] as $id_document) {
							if (count(objet_trouver_liens(array('document'=>$id_document),array($objet_action=>$id_objet_action)))) {
								$message_erreur = _T('album:erreur_deplacement');
								break;
							}
						}
					}

					// si dissocier, vérifier que les docs sont bien liés à l'objet
					// FIXME : vérif superflue ?
					if (isset($actions['dissocier']) AND count($actions['dissocier'])) {
						foreach ($actions['dissocier'] as $id_document) {
							if (!count(objet_trouver_liens(array('document'=>$id_document),array($objet_action=>$id_objet_action)))) {
								$message_erreur = _T('album:erreur_deplacement');
								break;
							}
						}
					}

				}
			}
		}
		// la chaîne décrivant les déplacements est vide ou invalide
		else {
			$message_erreur = _T('album:erreur_deplacement');
		}
	}

	if (isset($message_erreur) AND $message_erreur) {
		$erreurs['message_erreur'] = $message_erreur;
	}

	return $erreurs;
}

/**
 * Traitement du formulaire de déplacements de documents.
 *
 * Traiter les champs postés
 *
 * @param string $objet
 *     Type d'objet auquel sont associés les albums.
 *     A renseigner si on veut prendre en compte les documents des portfolios.
 * @param int|string $id_objet
 *     Identifiant de l'objet.
 * @return array
 *     Retours des traitements
 */
function formulaires_albums_deplacer_documents_traiter_dist($objet='',$id_objet=''){

	$res = array();

	if (
		_request('valider')
		AND is_array($deplacements = json_decode(_request('_deplacements'),true))
		AND count($deplacements)
	) {
		include_spip('action/editer_liens');
		foreach ($deplacements as $alias=>$actions) {
			if (list($objet_action, $id_objet_action) = preg_split('/_/',$alias)) {
				if ($objet_action=='album') $albums[]=$id_objet_action;
				foreach ($actions as $action => $documents) {
					if ($action == 'associer') {
						foreach ($documents as $id_document)
							objet_associer(array('document'=>intval($id_document)), array($objet_action=>intval($id_objet_action)));
					}
					if ($action == 'dissocier') {
						foreach ($documents as $id_document)
							objet_dissocier(array('document'=>intval($id_document)), array($objet_action=>intval($id_objet_action)));
					}
				}
			}
			if (isset($albums) AND count($albums)) $albums=array_unique($albums);
		}
		$res['message_ok'] = _T('info_modification_enregistree');
	}

	// code javascript
	// boutons valider et annuler : rechargement ajax des albums et éventuellement des portfolios
	// annuler : cacher le formulaire
	// TODO : ne recharger que les albums impactés au lieu de toute la liste (cf. $albums)
	if (
		(_request('valider') AND isset($res['message_ok']) AND $res['message_ok'])
		OR (_request('annuler'))
	) {
		$contexte_objet = ($objet AND intval($id_objet)>0) ? true : false;
		$ajaxReload_documents =  ($contexte_objet) ? "ajaxReload('documents');" : "";
		$id_form = "#formulaire_albums_deplacer_documents";
		$id_form .= ($contexte_objet) ? "_${objet}${id_objet}" : "";
		//if (_request('valider')) $callback = "jQuery('${id_form}').find('.boutons').hide();";
		if (_request('annuler')) $callback = "jQuery('${id_form}').slideUp(500,function(){jQuery(this).find('.reponse_formulaire').hide();});";

		$js = "if (window.jQuery) jQuery(function(){ajaxReload('liste_albums',{callback:function(){ ${callback} }});${ajaxReload_documents}});";
		$js = "<script type='text/javascript'>${js}</script>";
		set_request('_js',$js);
	}

	$res['editable'] = true;

	return $res;
}


?>
