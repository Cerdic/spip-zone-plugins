<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 */

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
function autoriser_albumcreer_menu_dist($faire, $type='', $id=0, $qui=NULL, $opt=NULL){
	return autoriser('creer','album',$id,$qui,$opt);
}

/**
 * Autorisation a afficher les albums dans le menu
 * = autorisation administrer
 */ 
function autoriser_albums_menu_dist($faire, $type='', $id=0, $qui=NULL, $opt=NULL){
	return autoriser('administrer','album',$id,$qui,$opt);
}

/**
 * Autorisation a administrer les albums
 * Admins
 */ 
function autoriser_albums_administrer_dist($faire,$quoi,$id,$qui,$options) {
	return $qui['statut'] == '0minirezo';
}

/**
 * Autorisation a ajouter (creer ou associer) un album a un objet
 * Il faut avoir le droit de modifier l'objet
 */
function autoriser_ajouteralbumdans_dist($faire, $objet, $id_objet, $qui, $options) {
	if (!$id_objet) return false; // pas d'album sur un article vide !
	return autoriser('modifier', $objet, $id_objet, $qui);
}

/**
 * Autorisation a creer un album
 * Admins et redacteurs
 */ 
function autoriser_album_creer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation a voir les fiches completes
 * Tout le monde
 */ 
function autoriser_album_voir_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Autorisation a modifier un album
 * il faut etre admin ou auteur de l'album
 */ 
function autoriser_album_modifier_dist($faire, $type, $id, $qui, $opt) {

	if (!function_exists('auteurs_album'))
		include_spip('inc/auth'); // pour auteurs_album si espace public

	return
		auteurs_album($id, "id_auteur=".$qui['id_auteur']) 
		OR in_array($qui['statut'], array('0minirezo'));

}

/**
 * Autorisation a mettre un album a la poubelle
 * Il faut etre admin ou auteur de l'album 
 */ 
function autoriser_album_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}

/**
 * Autorisation a associer/dissocier un album a un objet
 * Il faut etre admin ou pouvoir modifier l'objet
 */
function autoriser_album_associer_dist($faire, $type, $associer_objet, $qui, $options) {
	list($objet, $id_objet) = explode('|',$associer_objet); 
	if (!$id_objet) return false;
	return autoriser('modifier', $objet, $id_objet, $qui);
	#return in_array($qui['statut'], array('0minirezo', '1comite'));
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
function auteurs_album($id_album, $cond='')
{
	return sql_allfetsel("id_auteur", "spip_auteurs_liens", "objet='album' AND id_objet=$id_album". ($cond ? " AND $cond" : ''));
}

?>
