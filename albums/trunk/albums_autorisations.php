<?php
/**
 * Fonctions d'autorisations du plugin Albums
 *
 * @plugin     Albums
 * @copyright  2013
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Autorisations
**/

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * @param string $faire L'action
 * @param string $type Le type d'objet
 * @param int $id L'identifiant numérique de l'objet
 * @param array $qui Les informations de session de l'auteur
 * @param array $opt Des options
 * @return boolean true/false
*/


/**
 * Declaration vide pour ce pipeline.
 */
function albums_autoriser(){}

/**
 * Autorisation a afficher l'icone de creation rapide
 * = autorisation creer
 */ 
function autoriser_albumcreer_menu_dist($faire, $type='', $id=0, $qui=NULL, $opts=NULL){
	return autoriser('creer','album',$id,$qui,$opts);
}

/**
 * Autorisation a afficher les albums dans le menu
 * = autorisation creer
 */ 
function autoriser_albums_menu_dist($faire, $type='', $id=0, $qui=NULL, $opts=NULL){
	return autoriser('creer','album',$id,$qui,$opts);
}

/**
 * Autorisation a administrer les albums
 * Admins et redacteurs
 */ 
function autoriser_album_administrer_dist($faire, $type, $id, $qui, $opts) {
	return autoriser('creer','album',$id,$qui,$opts);
}

/**
 * Autorisation a ajouter (creer ou associer) un album a un objet
 * Il faut avoir le droit de modifier l'objet
 */
function autoriser_ajouteralbum_dist($faire, $type, $id, $qui, $opts) {
	if (!intval($id) OR !$type) return false;
	return autoriser('modifier', $type, $id, $qui);
}

/**
 * Autorisation a creer un album
 * Admins et redacteurs
 */ 
function autoriser_album_creer_dist($faire, $type, $id, $qui, $opts) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation a associer/dissocier un album a un objet
 * Il faut etre admin ou pouvoir modifier l'objet
 */
function autoriser_associeralbum_dist($faire, $type, $id, $qui, $opts) {
	if (!intval($id) OR !$type) return false;
	return autoriser('modifier', $type, $id, $qui);
}

/**
 * Autorisation a voir les fiches completes
 * Tout le monde
 */ 
function autoriser_album_voir_dist($faire, $type, $id, $qui, $opts) {
	return true;
}

/**
 * Autorisation a modifier un album
 * il faut etre admin ou auteur de l'album
 */ 
function autoriser_album_modifier_dist($faire, $type, $id, $qui, $opts) {
	$id = sql_quote($id);
	if (!function_exists('auteurs_objet'))
		include_spip('inc/auth'); // pour auteurs_objet si espace public

	return
		auteurs_objet($type, $id, "id_auteur=".$qui['id_auteur'])
		OR in_array($qui['statut'], array('0minirezo'));

}

/**
 * Autorisation a supprimer definitivement un album
 * Il faut etre admin, que l'album soit vide, a la poubelle et inutilise
 */
function autoriser_album_supprimer_dist($faire, $type, $id, $qui, $opts) {
	$id = sql_quote($id);
	$statut = sql_fetsel("statut", "spip_albums", "id_album='$id'");
	$docs = sql_fetsel("id_objet", "spip_documents_liens", "objet='$type' AND id_objet='$id'");
	$liaison = sql_fetsel("id_album", "spip_albums_liens", "id_album='$id'");
	return
		!in_array($liaison['id_album'], array($id)) # inutilise (sans lien vers d'autres objets)
		AND !in_array($docs['id_objet'], array($id)) # vide (sans document ayant un lien vers l'album)
		AND in_array($statut['statut'], array('poubelle')) # a la poubelle
		AND ( $qui['statut'] == '0minirezo' AND !$qui['restreint'] ); # statut: admin
}

/**
 * Auto-association d'albums a du contenu editorial qui le reference
 * par defaut true pour tous les objets
 */
function autoriser_autoassocieralbum_dist($faire, $type, $id, $qui, $opts) {
	return true;
}

/**
 * Lister les auteurs d'un album
 * 
 * Fonction générique utilisée, retourne une liste des id_auteur trouvés
 *
 * @param int $id_album Identifiant de l'album
 * @param string $cond  Condition en plus dans le where de la requête
 * @return array|bool
 *     - array : liste des id_auteur trouvés
 *     - false : serveur SQL indisponible
 */
function auteurs_objet($objet='album', $id_objet, $cond=''){
	return sql_allfetsel("id_auteur", "spip_auteurs_liens", "objet='$objet' AND id_objet=".sql_quote($id_objet). ($cond ? " AND $cond" : ''));
}

?>
