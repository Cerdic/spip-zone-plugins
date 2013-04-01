<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012-2013 kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Licence GNU/GPL
 * 
 * Gestion de l'action editer_collection et de l'API d'édition d'une collection
 * 
 * @package SPIP\Collections\Collections\Edition
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Action d'association d'un auteur à une collection dans la base de données dont
 * les informations de lien sont données soit en paramètre de l'action, soit
 * en argument de l'action sécurisée
 *
 * L'argument doit être de la forme $action/$id_collection/$id_auteur :
 * - $action est obligatoire et doit être soit 'lier' (ajoute le lien) ou 'delier' (enlève le lien)
 * - $id_collection est obligatoire, c'est l'identifiant numérique de la collection
 * - $id_auteur est facultatif, s'il n'est pas donné, on utilise celui de 
 *   l'auteur actuellement connecté
 * 
 * On utilise l'API d'édition des objets de action/editer_objet pour les modifications
 * de collections
 *  
 * @param null|string $arg
 *     Argument de la fonction de la forme $action/$id_collection/$id_auteur ou null
 * @return string|bool
 * 		Retourne une chaine en cas d'erreur ou true en cas de réussite
 */
function action_associer_auteur_collection_dist($arg=null) {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	list($action,$id_collection,$id_auteur) = explode('/',$arg);
	
	if(!$id_auteur)
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	
	include_spip('inc/autoriser');
	
	if ($action != 'lier' AND $action != 'delier')
		$err = _L('Aucune action possible fournie');
	else if (!autoriser('associerauteurs',"collection",$id_collection) || ($id_auteur != $GLOBALS['visiteur_session']['id_auteur']))
		$err = _L('Pas autorisé à ajouter un auteur');
	include_spip('action/editer_auteur');
	
	if($err)
		return $err;

	if ($action == 'lier')
		auteur_associer($id_auteur, array("collection"=>$id_collection));
	elseif ($action == 'delier')
		auteur_dissocier($id_auteur, array("collection"=>$id_collection));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_collection/$id_collection'");
	
	return true;
}

?>