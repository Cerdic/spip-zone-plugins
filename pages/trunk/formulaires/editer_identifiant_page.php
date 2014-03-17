<?php
/**
 * Editer l'identifiant page d'un article
 *
 * @plugin     Pages Uniques
 * @copyright  2013
 * @author     RastaPopoulos
 * @licence    GNU/GPL
 * @package    SPIP\Pages\Formulaires
 * @link       http://contrib.spip.net/Pages-uniques
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_editer_identifiant_page_charger($id_article, $retour=''){
	$valeurs['champ_page'] = generer_info_entite($id_article,'article','page');
	$valeurs['_saisie_en_cours'] = (_request('champ_page')!==null);
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_identifiant_page_identifier_dist($id_article, $retour=''){
	return serialize(array('article', $id_article));
}

/**
 * Verification avant traitement
 *
 * @param integer $id_article
 * @param string $retour
 * @return Array Tableau des erreurs
 */
function formulaires_editer_identifiant_page_verifier_dist($id_article, $retour=''){
	$erreurs = array();
/*
	if ($page = _request('champ_page')) {
		// nombre de charactères : 40 max
		if (strlen($page) > 40)
			 $erreurs['champ_page'] = _T('pages:erreur_champ_page_taille');
		// format : charactères alphanumériques en minuscules ou "_"
		elseif (!preg_match('/^[a-z0-9_]+$/', $page))
			 $erreurs['champ_page'] = _T('pages:erreur_champ_page_format');
		// doublon
		elseif (sql_countsel(table_objet_sql('article'), "page=".sql_quote($page) . " AND id_article!=".intval($id_article)))
			$erreurs['champ_page'] = _T('pages:erreur_champ_page_doublon');
	}
*/
	return $erreurs;
}

/**
 * Traitement 
 *
 * @param integer $id_article
 * @param string $retour
 * @return Array
 */
function formulaires_editer_identifiant_page_traiter_dist($id_article, $retour=''){

	if (
		_request('changer')
		and $page = _request('champ_page')
	) {
		include_spip('action/editer_objet');
		objet_modifier('article',$id_article,array('page'=>$page));
	}

	set_request('champ_page');
	$res['editable'] = true;
	if ($retour)
		$res['redirect'] = $retour;

	return $res;
}

?>
