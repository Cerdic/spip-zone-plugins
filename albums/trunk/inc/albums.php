<?php
/**
 * Fonctions du plugin Albums
 *
 * @plugin     Albums
 * @copyright  2014
 * @author     Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Fonctions
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Supprimer proprement un ou plusieurs albums
 *
 * - suppression des liens faisant référence à l'album dans `spip_documents_liens`
 * - suppression des liens faisant référence à l'album dans `spip_albums_liens`
 * - suppression de l'album
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
 * Transvaser les documents entre un album et un objet éditorial auquel il est associé
 *
 * @note
 * On ne fait que modifier des liens existants au lieu de dissocier puis réassocier
 * les documents au moyen des fonctions de Médias (`dissocier_document` et `associer_document`)
 *
 * @param int|string $id_album
 *     Identifiant de l'album
 *     0 pour créer un nouvel album vide, dans le cas d'un remplissage
 * @param string $objet
 *     Type d'objet
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param bool $remplir
 *     Définit le sens du transvasement (on remplit l'album ou on le vide)
 *     true  : portfolio -> album
 *     false : album     -> portfolio
 * @param bool $supprimer
 *     true : supprimer l'album dans le cas d'un vidage
 * @return int|bool
 *     nb de liens changés si ok,
 *     false en cas d'erreur
 */
function transvaser_album($id_album, $objet, $id_objet, $remplir=true, $supprimer=false) {

	include_spip('inc/autoriser');
	include_spip('action/editer_liens');
	include_spip('action/editer_objet');

	$echec = null;
	$nb_maj = 0;

	// au besoin, on crée d'abord un album et on l'associe à l'objet
	if (
		!intval($id_album)
		AND $remplir === true
	) {
		$id_album = objet_inserer('album');
		objet_associer(array('album'=>$id_album),array($objet=>$id_objet));
	}

	if (autoriser('transvaser','album',$id_album,'',array('objet'=>$objet,'id_objet'=>$id_objet))){

		$objet_source = ($remplir === true) ? $objet : 'album';
		$id_objet_source = ($remplir === true) ? $id_objet : $id_album;
		$objet_destination = ($remplir === true) ? 'album' : $objet;
		$id_objet_destination = ($remplir === true) ? $id_album : $id_objet;

		// changer les liens existants
		// on ne peut pas changer objet et id_objet avec objet_qualifier_liens, donc on fait ça à la main
		if (
			$liens_docs = objet_trouver_liens(array('document'=>'*'),array($objet_source=>$id_objet_source))
			AND is_array($liens_docs)
		){
			foreach ($liens_docs as $lien){
				$qualif = array('objet'=>$objet_destination,'id_objet'=>$id_objet_destination);
				$where = 'id_document='.intval($lien['id_document']).' AND objet='.sql_quote($objet_source).' AND id_objet='.intval($id_objet_source);
				$res = sql_updateq('spip_documents_liens',$qualif,$where);
				if ($res===false)
					$echec = true;
				else
					$nb_maj++;
			}
		}
		// en cas de vidage, dissocier l'album
		// puis éventuellement le supprimer
		if (
			$remplir === false
			AND $echec !== false
		){
			objet_dissocier(array('album'=>$id_album),array($objet=>$id_objet));
			if (
				$supprimer === true
				AND autoriser('supprimer','album',$id_album)
			) {
				supprimer_albums($id_album);
			}
		}
	} else {
		$echec = true;
	}
	return ($echec?false:$nb_maj);
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
