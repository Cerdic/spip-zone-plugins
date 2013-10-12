<?php
/**
 * Plugin Pages
 * 
 * @author Rastapopoulos
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.spip-contrib.net/Plugin-Pages Documentation
 * @package SPIP\Pages\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Change l'entête du formulaire des articles pour montrer que c'est une page
function pages_affiche_milieu_ajouter_page($flux){

	if ($flux['args']['exec'] == 'article_edit'){
		include_spip('base/abstract_sql');
		if (
			_request('modele') == 'page'
			or
			(
				($id_article = $flux['args']['id_article']) > 0
				and
				(sql_getfetsel('page', 'spip_articles', 'id_article='.sql_quote($id_article)))
			)
		)
		{
			
			// On cherche et remplace l'entete de la page : "modifier la page"
			$cherche = "/(<div[^>]*class=('|\")entete-formulaire.*?<\/span>).*?(<h1>.*?<\/h1>.*?<\/div>)/is";
			$surtitre = _T('pages:modifier_page');
			$remplace = "$1$surtitre$3";
			$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
			
			// Si c'est une nouvelle page, on remplace le lien de retour dans l'entete
			if (_request('new') == 'oui'){
				$cherche = "/(<span[^>]*class=(?:'|\")icone[^'\"]*retour[^'\"]*(?:'|\")>"
				         . "<a[^>]*href=(?:'|\"))[^'\"]*('|\")/is";
				$retour = generer_url_ecrire("pages_tous");
				$remplace = "$1$retour$2";
				$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
			
			}
		
		}
	
	}
	
	return $flux;

}


/**
 * Saisie de l'identifiant de la page sur la fiche d'une page
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function pages_affiche_milieu_identifiant($flux){
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);
	$id_article = $flux['args'][$e['id_table_objet']];

	// Si on est sur la fiche d'un article...
	if ( !$e['edition'] and $e['type']=='article' ) {
		include_spip('base/abstract_sql');
		// ... et s'il s'agit d'une page
		if (
			_request('modele') == 'page'
			or
			(
				$id_article > 0
				and
				(sql_getfetsel('page', 'spip_articles', 'id_article='.sql_quote($id_article)))
			)
		) {
			$texte .= recuperer_fond('prive/objets/editer/identifiant_page',
				array('id_article' => $id_article),
				array('ajax'=>true)
			);
		}
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


// Vérifier que la page n'est pas vide
function pages_formulaire_charger($flux){

	// Si on est dans l'édition d'un article
	if (is_array($flux) and $flux['args']['form'] == 'editer_article'){
	
		// Si on est dans un article de modele page
		if (_request('modele') == 'page' or ($flux['data']['page'] and _request('modele') != 'article')){
			$flux['data']['modele'] = 'page';
			$flux['data']['champ_page'] = $flux['data']['page'];
		}
		unset($flux['data']['page']);
	}
	
	return $flux;

}


/**
 * Vérifications de l'identifiant d'une page
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function pages_formulaire_verifier($flux){

	// Si on est dans l'édition d'un article/page ou dans le formulaire d'édition d'un identifiant page
	if ( 
		is_array($flux)
		and (
			( $flux['args']['form'] == 'editer_article' and _request('modele') == 'page' )
			or $flux['args']['form'] == 'editer_identifiant_page'
		)
	){
		$erreur = '';
		$page = _request('champ_page');
		$id_page = $flux['args']['args'][0];

		// champ vide
		if (!$page)
			$erreur .= _T('info_obligatoire');
		// nombre de charactères : 40 max
		elseif (strlen($page) > 255)
			 $erreur = _T('pages:erreur_champ_page_taille');
		// format : charactères alphanumériques en minuscules ou "_"
		elseif (!preg_match('/^[a-z0-9_]+$/', $page))
			 $erreur = _T('pages:erreur_champ_page_format');
		// doublon
		elseif (sql_countsel(table_objet_sql('article'), "page=".sql_quote($page) . " AND id_article!=".intval($id_page)))
			$erreur = _T('pages:erreur_champ_page_doublon');

		if ($erreur)
			$flux['data']['champ_page'] .= $erreur;
	}
	return $flux;

}


/**
 * Insertion dans le pipeline editer_contenu_objet (SPIP)
 * 
 * Sur les articles considérés comme pages uniques, on remplace l'élément de choix de rubriques par :
 * -* un input hidden id_rubrique et id_parent avec pour valeur -1
 * -* un input hidden modele avec comme valeur "page"
 * -* un champ d'édition de l'identifiant de la page unique
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function pages_editer_contenu_objet($flux){
	$args = $flux['args'];
	if ($args['type'] == 'article' && isset($args['contexte']['modele']) && $args['contexte']['modele'] == 'page'){
		$erreurs = $args['contexte']['erreurs'];
		// On cherche et remplace l'édition de la rubrique
		$cherche = "/<li[^>]*class=('|\")editer editer_parent.*?<\/li>/is";
		$remplace = '<li class="editer editer_page obligatoire'.($erreurs['champ_page'] ? ' erreur' : '').'">';
		$remplace .= '<input type="hidden" name="id_parent" value="-1" />';
		$remplace .= '<input type="hidden" name="id_rubrique" value="-1" />';
		$remplace .= '<input type="hidden" name="modele" value="page" />';
    	$remplace .= '<label for="id_page">'._T('pages:titre_page').'</label>';
    	if ($erreurs['champ_page'])
    		$remplace .= '<span class="erreur_message">'.$erreurs['champ_page'].'</span>';
    	$value = $args['contexte']['champ_page'] ? $args['contexte']['champ_page'] : $args['contexte']['page'];
    	$remplace .= '<input type="text" class="text" name="champ_page" id="id_page" value="'.$value.'" />';
    	$remplace .= '</li>';
		$flux['data'] = preg_replace($cherche, $remplace, $flux['data'],1);
		$flux['data'] = preg_replace($cherche, '', $flux['data']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition (SPIP)
 * 
 * Si on édite un article :
 * - Si on récupère un champ "champ_page" dans les _request() et qu'il est différent de "article", 
 * on transforme l'article en page unique, id_rubrique devient -1 
 * - Si on ne récupère pas de champ_page et que id_parent est supérieur à 0, on le passe en article et on vide
 * son champ page pour pouvoir réaliser le processus inverse dans le futur
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux Le contexte modifié
 */
function pages_pre_edition_ajouter_page($flux){
	if (is_array($flux) and $flux['args']['type'] == 'article'){
		if ((($page = _request('champ_page')) != '') AND ($page != 'article')){
			/**
			 * On ajoute le "champ_page" du formulaire qui deviendra "page" dans la table
			 * On force l'id_rubrique à -1
			 */
			$flux['data']['page'] = $page;
			$flux['data']['id_rubrique'] = '-1';
			$flux['data']['id_secteur'] = '0';
		}
		/**
		 * si l'id_parent est supérieur à 0 on que l'on ne récupère pas de champ_page,
		 * on pense à vider le champ "page", pour pouvoir revenir après coup en page
		 */
		if (!_request('champ_page') && (_request('id_parent') > 0)){
			$flux['data']['page'] = '';
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline boite_infos (SPIP)
 * 
 * Ajouter un lien pour transformer une article normal en page inversement
 * 
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte modifié
 */
function pages_boite_infos($flux){
	if ($flux['args']['type'] == 'article' and autoriser('modifier', 'article', $flux['args']['id'])){
		include_spip('inc/presentation');
		if (sql_getfetsel('page', 'spip_articles', 'id_article='. $flux['args']['id']) == '')
			$flux['data'] .= icone_horizontale(_T('pages:convertir_page'), parametre_url(parametre_url(generer_url_ecrire('article_edit'), 'id_article', $flux['args']['id']), 'modele', 'page'), 'page', $fonction="", $dummy="", $javascript="");
		else
			$flux['data'] .= icone_horizontale(_T('pages:convertir_article'), parametre_url(parametre_url(generer_url_ecrire('article_edit'), 'id_article', $flux['args']['id']), 'modele', 'article'), 'article', $fonction="", $dummy="", $javascript="");
	}
	return $flux;
}


/**
 * Insertion dans le pipeline affiche_hierarchie (SPIP)
 * Pour les pages, faire pointer la racine vers la liste des pages au lieux des rubriques
 * Pour savoir si on se trouve sur une page, on vérifie que le champ "page" existe, faute de mieux  
 *
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte modifié
 */
function pages_affiche_hierarchie($flux){

	$objet = $flux['args']['objet'];
	$id_article = $flux['args']['id_objet'];
	if (
		$objet == 'article'
		and sql_getfetsel('page', 'spip_articles', 'id_article='.sql_quote($id_article))
	){
		$cherche = "<a href=\"". generer_url_ecrire('rubriques') . "\">" . _T('info_racine_site') . "</a>";
		$remplace = "<a href=\"". generer_url_ecrire('pages_tous') . "\">" . _T('pages:pages_uniques') . "</a>";
		$flux['data'] = str_replace($cherche,$remplace,$flux['data']);
	}


	return $flux;
}

?>
