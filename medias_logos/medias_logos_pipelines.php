<?php
/**
 * Utilisations de pipelines par le plugin Logos Médias
 *
 * @plugin     Logos Médias
 * @copyright  2014
 * @author     Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Logos Médias\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajout de contenu dans le bloc «actions» des documents
 * 
 * - Bouton pour définir un document comme logo d'un objet
 *
 * @pipeline document_desc_actions
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function medias_logos_document_desc_actions($flux) {

	$texte       = "";
	$id_document = $flux['args']['id_document'];
	$e           = trouver_objet_exec(_request('exec'));
	include_spip('inc/autoriser');

	if (
		$e !== false // page d'un objet éditorial
		AND $e['edition'] === false // pas en mode édition
		AND $type = $e['type']
		AND $id_table_objet = $e['id_table_objet']
		AND $id = intval(_request($id_table_objet))
		AND autoriser('autoiconifier','document',$id_document,'',array('objet'=>$type,'id_objet'=>$id))
	) {
		$callback = '(function(){ajaxReload("navigation",{args:{var_mode:"calcul"}});return true;})()';
		$url_action = generer_action_auteur('iconifier_document',$id_document.'/'.$type.'/'.$id, self());
		// $libelle, $url, $class, $confirm, $title, $callback
		$texte = bouton_action(_T('paquet-medias_logos:bouton_iconifier'),$url_action,'ajax','','',$callback);
	}

	if ($texte)
			$flux['data'] .= $texte;

	return $flux;
}

?>
