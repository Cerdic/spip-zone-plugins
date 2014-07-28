<?php
/**
 * Action : définir un document comme logo d'un objet.
 *
 * @plugin     Logos Médias
 * @copyright  2014
 * @author     Charles Razack
 * @licence    GPL
 * @package    SPIP\Logos Médias\Action
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Définir une image liée à un objet comme logo du dit objet.
 *
 * On se repose sur le formulaire « editer_logo » (vérifications, traitements),
 * auquel on soumet une copie du document comme fichier à traiter.
 * 
 * - On crée une copie temporaire de l'image dans /tmp
 * - On effectue les vérifications et les traitements du formulaire `editer_logo`,
 * - Puis on supprime le fichier temporaire.
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{iconifier_document, #ID_DOCUMENT/#OBJET/#ID_OBJET, #SELF}
 *     #URL_ACTION_AUTEUR{iconifier_document, #ID_DOCUMENT/#OBJET/#ID_OBJET/off, #SELF}
 *     ```
 *
 * @param string $arg
 *     Arguments séparés par un charactère non alphanumérique
 *     sous la forme `$id_document/$objet/$id_objet/$etat`
 *
 *     - id_document : identifiant du document
 *     - objet       : type de l'objet
 *     - id_objet    : identifiant de l'objet
 *     - etat        : on | off
 *                     défaut : « on »
 * @return void|string
 *     message d'erreur éventuel
 */
function action_iconifier_document_dist($arg=null){

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($id_document, $objet, $id_objet, $etat) = preg_split('/\W/', $arg);

	// normaliser les valeurs
	$id_document = intval($id_document);
	$id_objet = intval($id_objet);
	$objet = objet_type($objet);
	if (!isset($etat) OR (isset($etat) AND !in_array($etat,array('on','off'))))
		$etat = 'on';

	// vérifier qu'on a le droit de logotifier le document
	// et de modifier le logo (d'après charger de «editer_logo »)
	include_spip('inc/autorise');
	if (
		autoriser('autoiconifier','document',$id_document,'',array('objet'=>$objet,'id_objet'=>$id_objet))
		AND $charger = charger_fonction('charger','formulaires/editer_logo')
		AND $res = $charger($objet,$id_objet,'',array('titre'=>''))
		AND $res['editable'] === true
	){

		// récupérer quelques infos sur le document
		$document = sql_allfetsel('fichier,extension,taille','spip_documents', 'id_document='.$id_document);
		$document = array_shift($document);
		$type = sql_getfetsel('mime_type','spip_types_documents','extenstion='.sql_quote($document['extension']));

		// créer une copie temporaire du document dans /tmp
		$fichier = find_in_path(_NOM_PERMANENTS_ACCESSIBLES.$document['fichier']);
		$fichier_tmp = _DIR_TMP.pathinfo($document['fichier'], PATHINFO_FILENAME).'_tmp.'.$document['extension'];
		if (!copy($fichier,$fichier_tmp))
			return;
		$name = pathinfo($fichier_tmp, PATHINFO_BASENAME);
		$size = filesize($fichier_tmp);

		// variable de téléversement
		// on fait "comme si" on avait choisit la copie dans le sélecteur de fichier
		$_FILES['logo_'.$etat] = array(
			'name' => $name,
			'tmp_name' => $fichier_tmp,
			'type' => $type,
			'size' => $size,
			'error' => ''
		);

		// vérifications de «editer_logo » (format de l'image)
		$verifier = charger_fonction('verifier','formulaires/editer_logo');
		if ($erreurs = $verifier($objet,$id_objet))
			return $erreurs;

		// traitements de « editer_logo »
		$traiter = charger_fonction('traiter','formulaires/editer_logo');
		$res = $traiter($objet,$id_objet);

		// supprimer le fichier temporaire
		include_spip('inc/flock');
		supprimer_fichier(find_in_path($fichier_tmp));

		if (isset($res['message_erreur']))
			return $res['message_erreur'];
	} else {
		return _T('erreur');
	}

}

?>
