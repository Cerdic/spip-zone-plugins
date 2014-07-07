<?php
/**
 * Gestion du formulaire d'ajout d'albums à un objet
 *
 * Le formulaire permet soit de créer et remplir un nouvel album,
 * soit de choisir un ou plusieurs albums existants.
 * Dans le premier cas, il s'agit en gros d'un fusion des formulaires
 * «editer_album» et «joindre_document» : les traitements sont identiques
 * à ceux de ces 2 formulaires.
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Formulaires
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Chargement du formulaire d'ajout d'album
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_album_charger()
 * @uses formulaires_joindre_document_charger()
 *
 * @param int|string $objet
 *     type de l'objet à associer.
 * @param int|string $id_objet
 *     Identifiant de l'objet à associer.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un album source d'une traduction
 * @return array
 *     Environnement du formulaire
 */
function formulaires_ajouter_album_charger_dist($objet='', $id_objet=0, $retour='', $lier_trad=0){

	$valeurs['ids_albums_associer'] = '';
	$valeurs['objet_associer'] = $objet;
	$valeurs['id_objet_associer'] = $id_objet;

	// valeurs du formulaire d'édition d'un album
	$charger_editer_album = charger_fonction('charger','formulaires/editer_album');
	$valeurs_editer_album = $charger_editer_album('new',$retour,$objet.'|'.$id_objet,$lier_trad);
	if (count($valeurs_editer_album)) $valeurs = array_merge($valeurs,$valeurs_editer_album);

	// valeurs du formulaire d'ajout de documents
	$charger_joindre_document = charger_fonction('charger','formulaires/joindre_document');
	$valeurs_joindre_document = $charger_joindre_document('new',0-$GLOBALS['visiteur_session']['id_auteur'],'album','document');
	if (count($valeurs_joindre_document)) $valeurs = array_merge($valeurs,$valeurs_joindre_document);

	// valeur de l'identifiant négatif pour l'ajout de documents
	// cf. medias_pipelines.php L.161
	$valeurs['_id_temporaire'] = 0-$GLOBALS['visiteur_session']['id_auteur'];

	return $valeurs;

}

/**
 * Vérifications du formulaire d'ajout d'album
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_album_verifier()
 * @uses formulaires_joindre_document_verifier()
 *
 * @param int|string $objet
 *     type de l'objet à associer.
 * @param int|string $id_objet
 *     Identifiant de l'objet à associer.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un album source d'une traduction
 * @return array
 *     Tableau des erreurs
 */
function formulaires_ajouter_album_verifier_dist($objet='', $id_objet=0, $retour='', $lier_trad=0){

	$erreurs = array();

	// créer un album
	if (!_request('choisir_album')) {

		// erreurs du formulaire d'édition d'un album
		$verifier_editer_album = charger_fonction('verifier','formulaires/editer_album');
		$erreurs_editer_album = $verifier_editer_album('new',$retour,$objet.'|'.$id_objet,$lier_trad);
		if (count($erreurs_editer_album)) $erreurs = array_merge($erreurs, $erreurs_editer_album);

		// erreurs du formulaire d'ajout de documents
		$verifier_joindre_document = charger_fonction('verifier','formulaires/joindre_document');
		$erreurs_joindre_document = $verifier_joindre_document('new',0-$GLOBALS['visiteur_session']['id_auteur'],'album','document');
		if (count($erreurs_joindre_document)) $erreurs = array_merge($erreurs, $erreurs_joindre_document);

	// choisir un album
	} else {
		if (!_request('ids_albums_associer'))
			$erreurs['ids_albums_associer'] = _T('info_obligatoire');
	}

	return $erreurs;
}

/**
 * Traitement du formulaire d'ajout d'album
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_album_traiter()
 * @uses formulaires_joindre_document_traiter()
 *
 * @param int|string $objet
 *     type de l'objet à associer.
 * @param int|string $id_objet
 *     Identifiant de l'objet à associer.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un album source d'une traduction
 * @return array
 *     Retours des traitements
 */
function formulaires_ajouter_album_traiter_dist($objet='', $id_objet=0, $retour='', $lier_trad=0){

	$res = array();

	// créer un album
	if (!_request('choisir_album')) {

		// traitement des documents
		$id_temporaire = 0-$GLOBALS['visiteur_session']['id_auteur'];
		$traiter_joindre_document = charger_fonction('traiter','formulaires/joindre_document');
		$res_joindre_document = $traiter_joindre_document('new',$id_temporaire,'album','document');
		$res = array_merge($res, $res_joindre_document);
		// pas besoin du js ajouté dans le message de retour
		if (isset($res['message_ok']) AND $res['message_ok']){
			$res['message_ok'] = preg_replace('/(<script.*<\/script>)/is','',$res['message_ok']);
		}

		// traitement de l'album
		set_request('statut','publie');
		$traiter_editer_album = charger_fonction('traiter','formulaires/editer_album');
		$res_editer_album = $traiter_editer_album('new',$retour,$objet.'|'.$id_objet,$lier_trad);
		$res = array_merge($res, $res_editer_album);
		if (
			isset($res['message_ok']) AND $res['message_ok']
			AND $id_album = $res['id_album']
		){
			$res['message_ok'] = _T('album:message_id_album_ajoute',array('id_album'=>$id_album));
			$res['message_ok'] .= js_ajouter_albums($id_album);
			// r.a.z des champs
			foreach(array('titre','descriptif','refdoc_joindre') as $champ) set_request($champ,'');
		}

	// choisir un album
	} else {
		$ids = _request('ids_albums_associer');
		$ids = explode(',',$ids);
		include_spip('action/editer_liens');
		if ($nb=objet_associer(array('album'=>$ids), array($objet=>$id_objet))) {
			$res['message_ok'] = singulier_ou_pluriel($nb,'album:message_1_album_ajoute','album:message_nb_albums_ajoutes');
			$res['message_ok'] .= js_ajouter_albums($ids);
			// r.a.z du champ
			set_request('ids_albums_associer','');
		} else {
			$res['message_erreur'] = _T('erreur');
		}
	}

	$res['editable'] = true;

	return $res;

}

/**
 * Fonction privée mutualisée retournant le js pour recharger les blocs adéquats
 * 
 * @param string|array $ids identifiants des albums ajoutés
 * @return string message js
 */
function js_ajouter_albums($ids=array()){
	if (!intval($ids)) return;
	if (!is_array($ids)) $ids = array($ids);

	foreach($ids as $id) $divs[] = "#album${id}";
	$divs = implode(',',$divs);
	$callback = "jQuery('${divs}').animateAppend();";
	$js = "if (window.jQuery) jQuery(function(){ajaxReload('liste_albums',{callback:function(){ $callback }});});";;
	$js = "<script type='text/javascript'>${js}</script>";
	return $js;
}

?>
