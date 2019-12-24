<?php
/**
 * Définit les autorisations du plugin Logos Médias
 *
 * @plugin     Logos Médias
 * @copyright  2014
 * @author     Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Logos Médias\Autorisations
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function medias_logos_autoriser(){}


/**
 * Autorisation à auto-iconifier un document.
 *
 * Il faut que le document soit une image, qu'il soit lié à l'objet,
 * et avoir le droit de modifier le logo de ce dernier.
 *
 * @note
 *
 * - Les infos sur l'objet sont dans les options (5ème paramètre).
 * - on utilise le terme «auto-iconifier» car on ne veut pas éditer le logo
 *   du document, mais utiliser le document comme logo d'un objet.
 *
 * @example
 *     ```
 *     #AUTORISER{autoiconifier,document,#ID_DOCUMENT,'',#ARRAY{objet,#OBJET,id_objet,#ID_OBJET}}
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opts  Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_document_autoiconifier_dist($faire, $type, $id, $qui, $opts){

	$objet = $opts['objet'];
	$id_objet = $opts['id_objet'];

	// on est autorisé à changer le logo de l'objet
	$autoriser_iconifier = autoriser('iconifier',$objet,$id_objet,$qui,$opts);

	// le document est lié à l'objet
	include_spip('action/editer_liens');
	$lie = (count(objet_trouver_liens(array('document'=>$id),array($objet=>$id_objet)))>0);

	// le document est une image
	$image = (sql_getfetsel('media','spip_documents','id_document='.intval($id))=='image');

	$autoriser = (
		$autoriser_iconifier
		AND $lie
		AND $image
	) ? true : false;

	return $autoriser;
}

?>
