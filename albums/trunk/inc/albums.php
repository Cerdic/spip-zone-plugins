<?php
/**
 * Fonctions du plugin Albums
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Fonctions
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Supprimer proprement un ou plusieurs albums
 *
 * @param int|string|array $id_albums
 *     Identifiant unique ou tableau d'identifiants des albums
 * @param bool $supprimer_docs_orphelins
 *     true pour supprimer les documents rendus orphelins
 */
function supprimer_albums($ids_albums, $supprimer_docs_orphelins=false) {

	if (!$ids_albums) return false;
	if (!is_array($ids_albums)) $ids_albums = array(intval($ids_albums));

	// Vider les albums de leurs documents
	vider_albums($ids_albums,$supprimer_docs_orphelins);

	// Nettoyer la table de liens
	include_spip('action/editer_liens');
	objet_dissocier(array('album'=>$ids_albums),'*');

	// Supprimer les albums
	$in_albums = sql_in('id_album', $ids_albums);
	sql_delete(table_objet_sql('album'), $in_albums);

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='album/$id_album'");

	return;
}


/**
 * Vider un ou plusieurs albums de leurs documents.
 *
 * @note
 * Impossible de retirer en une fois tous les documents d'un album
 * via le bouton d'action `dissocier_document` du plugin Médias.
 * Il faut lui passer en paramètre un des 3 modes pour les documents :
 *
 * - les images en mode image : `I/image`
 * - les images en mode document : `I/document`
 * - les documents en mode document : 'D/document'
 * 
 * Cf. fonction `dissocier_document` dans `action/dissocier_document.php`.
 * 
 * @param int|string|array $id_albums
 *     Identifiant unique ou tableau d'identifiants des albums
 * @param bool $supprimer_docs_orphelins
 *     true pour supprimer les documents rendus orphelins
 * @return array
 *     tableau des albums vidés et ceux laissés intacts
 *     [succes => [x,y,z]],[erreurs => [x,y,z]]
 */
function vider_albums($ids_albums, $supprimer_docs_orphelins=false) {

	if (!$ids_albums) return false;
	if (!is_array($ids_albums)) $ids_albums = array($ids_albums);
	$supprimer_docs_orphelins = ($supprimer_docs_orphelins==true) ? 'suppr' : '';

	include_spip('inc/autoriser');
	include_spip('action/editer_liens');
	foreach($ids_albums as $id_album) {
		if (
			$id_album = intval($id_album)
			AND autoriser('modifier','album',$id_album)
			AND count(objet_trouver_liens(array('document'=>'*'),array('album'=>$id_album)))
		){
			$dissocier_document = charger_fonction('dissocier_document','action');
			$modes_documents = array('I/image','I/document','D/document'); // cf. @note
			foreach($modes_documents as $mode) {
				$dissocier_document("${id_album}-album-${mode}-${supprimer_docs_orphelins}");
				// dès que l'album est vide, on arrête
				if (!count(objet_trouver_liens(array('document'=>'*'),array('album'=>$id_album))))
					break;
			}
			$succes[] = $id_album;
		} else {
			$erreurs[] = $id_album;
		}
	}

	return array('succes'=>$succes, 'erreurs'=>$erreurs);
}


/**
 * Lister les formulaire yaml des modèles «album» disponibles dans les dossiers modeles/
 *
 * Les premiers modèles retournés sont les modèles du plugin (album.yaml et album_liste.yaml)
 *
 * @staticvar array $liste_modeles_albums
 * @return array
 */
function albums_lister_modeles(){

	static $liste_modeles_albums = false;
	if ($liste_modeles_albums === false) {
		$liste_modeles_albums = array();

		// d'abord, on liste les 2 modèles par défaut afin qu'ils soient en tête de liste
		foreach(array('album.yaml','album_liste.yaml') as $modele)
			$pre_liste[$modele] = find_in_path($modele,'modeles/');
		// ensuite, on liste tous les modèles
		$liste = find_all_in_path('modeles/', "album(_.*)?\.yaml$");
		// puis on mélange
		$liste = array_merge($pre_liste,$liste);

		if (count($liste)){
			include_spip('inc/yaml');
			foreach($liste as $fichier => $chemin)
				$liste_modeles_albums[$fichier] = yaml_charger_inclusions(yaml_decode_file($chemin));
		}
	}

	return $liste_modeles_albums;
}


/**
 * Charger les informations d'un formulaire yaml de modèle «album»
 *
 * @staticvar array $infos_modeles_album
 * @return array
 */
function infos_modele_album($fichier){

	static $infos_modeles_album = array();
	if (!isset($infos_modeles_album[$fichier])) {
		if (substr($fichier,-5) != '.yaml')
			$formulaire .= '.yaml';
		if ($chemin = find_in_path($fichier,'modeles/')) {
			include_spip('inc/yaml');
			$infos_modeles_album[$fichier] = yaml_charger_inclusions(yaml_decode_file($chemin));
		}
	}

	return $infos_modeles_album[$fichier];
}

?>
