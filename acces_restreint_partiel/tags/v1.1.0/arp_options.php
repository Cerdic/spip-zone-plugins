<?php
/**
 * Options du plugin Accès Restreint Partielau chargement
 *
 * @plugin     Accès Restreint Partiel
 * @copyright  2014
 * @author     Bruno Caillard
 * @licence    GNU/GPL
 * @package    SPIP\Arp\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier d'options permet de définir des éléments
 * systématiquement chargés à chaque hit sur SPIP.
 *
 * Il vaut donc mieux limiter au maximum son usage
 * tout comme son volume !
 * 
 */

// Surcharge de la fonction de protection des url de Accès Restreint
// présente dans /urls/generer_url_document.php
// on fait en sorte de lairsser la protection active sauf pour les images
function urls_generer_url_document($id, $args='', $ancre='', $public=null, $connect='') {
	include_spip('inc/autoriser');
	include_spip('inc/documents');

	// Ajout par rapport à la fonction d'origine dans Accès Restreint
	//---------------------------------------------------------------
//	if (!autoriser('voir', 'document', $id)) return '';
	$motif = "`^(?:jpg|bmp|png|gif)\/(.*)$`isU";
	$f = $r['fichier'];
	preg_match($motif, $f,$resultat);
	if (sizeof($resultat) != 0)
		return get_spip_doc($f);
	// FIN ajout

	$r = sql_fetsel("fichier,distant", "spip_documents", "id_document=".intval($id));

	if (!$r) return '';

	$f = $r['fichier'];

	if ($r['distant'] == 'oui') return $f;

	// Si droit de voir tous les docs, pas seulement celui-ci
	// il est inutilement couteux de rajouter une protection
	$r = (autoriser('voir', 'document'));
	if (($r AND $r !== 'htaccess'))
		return get_spip_doc($f);

	include_spip('inc/securiser_action');

	// cette url doit etre publique !
	$cle = calculer_cle_action($id.','.$f);

	// renvoyer une url plus ou moins jolie
	if ($GLOBALS['meta']['creer_htaccess'])
		return _DIR_RACINE."docrestreint.api/$id/$cle/$f";
	else
		return get_spip_doc($f)."?$id/$cle";
}

?>