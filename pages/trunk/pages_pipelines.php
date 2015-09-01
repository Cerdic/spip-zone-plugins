<?php
/**
 * Déclaration des pipelines utilisés par le plugin
 *
 * @plugin     Pages
 * @copyright  2013
 * @author     RastaPopoulos 
 * @licence    GNU/GPL
 * @package    SPIP\Pages\Pipelines
 * @link       http://contrib.spip.net/Pages-uniques
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
				(sql_getfetsel('page', 'spip_articles', 'id_article='.intval($id_article)))
			)
		)
		{
			
			//On force l'id parent à -1
			//Par principe une page nouvelle ou existante est dans la rubrique parent -1
			$cherche = "/(<input[^>]*name=('|\")id_parent[^>]*>)/is";
			if (!preg_match($cherche,$flux['data'])) {
				$cherche = "/(<input[^>]*name=('|\")id_rubrique[^>]*>)/is";
				$remplace = "$1<input type=\"hidden\" name=\"id_parent\" value=\"-1\" />\n";
				$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
			}
			
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

	// Si on est sur la fiche d'un article...
	if (!$e['edition'] and $e['type']=='article' ) {
		include_spip('base/abstract_sql');
		$id_article = isset($flux['args'][$e['id_table_objet']]) ? $flux['args'][$e['id_table_objet']] : false;
		// ... et s'il s'agit d'une page
		if (
			_request('modele') == 'page'
			or
			(
				intval($id_article) > 0
				and
				(sql_getfetsel('page', 'spip_articles', 'id_article='.intval($id_article)))
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
		$page = strtolower($page);
		$page = preg_replace(",\\s+,","_",$page);
		set_request('champ_page',$page);
		$id_page = $flux['args']['args'][0];
		// champ vide
		$lang = sql_getfetsel('lang','spip_articles','id_article='.intval($id_page));
		if (!$page)
			$erreur .= _T('info_obligatoire');
		// nombre de charactères : 40 max
		elseif (strlen($page) > 255)
			 $erreur = _T('pages:erreur_champ_page_taille');
		// format : charactères alphanumériques en minuscules ou "_"
		elseif (!preg_match('/^[a-z0-9_]+$/', $page))
			 $erreur = _T('pages:erreur_champ_page_format');
		// doublon
		elseif (sql_countsel(table_objet_sql('article'), "page=".sql_quote($page) . " AND id_article!=".intval($id_page)." AND lang=".sql_quote($lang)))
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
		$cherche = "/(<(li|div)[^>]*class=(?:'|\")editer editer_parent.*?<\/\\2>)\s*(<(li|div)[^>]*class=(?:'|\")editer)/is";
		$remplace = '<\\2 class="editer editer_page obligatoire'.($erreurs['champ_page'] ? ' erreur' : '').'">';
		$remplace .= '<input type="hidden" name="id_parent" value="-1" />';
		$remplace .= '<input type="hidden" name="id_rubrique" value="-1" />';
		$remplace .= '<input type="hidden" name="modele" value="page" />';
		$remplace .= '<label for="id_page">'._T('pages:titre_page').'</label>';
		if ($erreurs['champ_page'])
			$remplace .= '<span class="erreur_message">'.$erreurs['champ_page'].'</span>';
		$value = $args['contexte']['champ_page'] ? $args['contexte']['champ_page'] : $args['contexte']['page'];
		$remplace .= '<input type="text" class="text" name="champ_page" id="id_page" value="'.$value.'" />';
		$remplace .= '</\\2>$3';
		if (preg_match($cherche,$flux['data'],$m)) {
			$flux['data'] = preg_replace($cherche, $remplace, $flux['data'],1);
			$flux['data'] = preg_replace($cherche, '', $flux['data']);
		} else {
			$cherche = "/(<(li|div)[^>]*class=(?:'|\")editer editer_soustitre.*?<\/\\2>)\s*(<(li|div)[^>]*class=(?:'|\")editer)/is";
			if (preg_match($cherche,$flux['data'])) {
				$flux['data'] = preg_replace($cherche,'$1'.$remplace, $flux['data']);
			} else {
				$cherche = "/(<(li|div)[^>]*class=(?:'|\")editer editer_titre.*?<\/\\2>)\s*(<(li|div)[^>]*class=(?:'|\")editer)/is";
				$flux['data'] = preg_replace($cherche,'$1'.$remplace, $flux['data']);
			}
		}
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
	if (is_array($flux) and isset($flux['args']['type']) && $flux['args']['type'] == 'article'){
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
 * Ajouter un lien pour transformer un article éditorial en page ou inversement
 * 
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte modifié
 */
function pages_boite_infos($flux){
	if ($flux['args']['type'] == 'article') {
		include_spip('inc/presentation');
		if (sql_getfetsel('page', 'spip_articles', 'id_article='. intval($flux['args']['id'])) == '') {
			if (autoriser('creer', 'page', $flux['args']['id']))
				$flux['data'] .= icone_horizontale(_T('pages:convertir_page'), parametre_url(parametre_url(generer_url_ecrire('article_edit'), 'id_article', $flux['args']['id']), 'modele', 'page'), 'page', $fonction="", $dummy="", $javascript="");
		}
		else {
			if (autoriser('modifier', 'page', $flux['args']['id']))
				$flux['data'] .= icone_horizontale(_T('pages:convertir_article'), parametre_url(parametre_url(generer_url_ecrire('article_edit'), 'id_article', $flux['args']['id']), 'modele', 'article'), 'article', $fonction="", $dummy="", $javascript="");
		}
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
		and sql_getfetsel('page', 'spip_articles', 'id_article='.intval($id_article))
	){
		$cherche = "<a href=\"". generer_url_ecrire('rubriques') . "\">" . _T('info_racine_site') . "</a>";
		$remplace = "<a href=\"". generer_url_ecrire('pages') . "\">" . _T('pages:pages_uniques') . "</a>";
		$flux['data'] = str_replace($cherche,$remplace,$flux['data']);
	}
	return $flux;
}


/**
 * Insertion dans le pipeline pre_boucle (SPIP)
 * Pour les listes d'articles purement éditoriaux, il faut exclure les pages uniques afin d'éviter la confusion des genres
 * ainsi que les liens vers des pages parfois inaccessibles en fonction de l'autorisation de l'auteur.
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte modifié
 */
function pages_pre_boucle($boucle){

	// On ne s'intéresse qu'à la boucle ARTICLES
	if ($boucle->type_requete == 'articles' and empty($boucle->modificateur['tout'])) {
		// On n'insère le filtre {id_rubriques>0} pour exclure les pages uniques que si aucune des conditions
		// suivantes n'est vérifiée:
		// - pas de critère page autre que {page=''}
		// - pas de critère explicite {id_rubrique=-1} ou {id_rubrique<0}
		// - pas de critère {id_rubrique?} pour lequel l'environnement renvoie -1 pour l'id de la rubrique
		// pas de critère {id_article=XX} ou {id_article} ou {id_article?}
		$boucle_articles = true;
		$critere_page = false;

		// On cherche les critères id_rubrique, id_article ou page
		foreach($boucle->criteres as $_critere){
			if ($_critere->op == 'page' AND !$_critere->not) { // {page} ou {page?} mais pas {!page}
				// On considère qu'on cherche toujours des pages uniques donc on force le filtre id_rubrique=-1
				$boucle_articles = false;
				$critere_page = true;
				break;
			}
			elseif (isset($_critere->param[0][0]->texte) and $_critere->param[0][0]->texte == 'page') { // {page=x}
				if (
					($_critere->op == '=') AND ($_critere->param[1][0]->texte == '')
				  OR $_critere->not
				  ) {
					// On veut exclure explicitement les pages
					break;
				}
				else {
					// on désigne bien des pages par leur champ 'page'
					$boucle_articles = false;
					$critere_page = true;
					break;
				}
			}
			elseif (($_critere->op == 'id_article') // {id_article} ou {id_article?}
				OR (isset($_critere->param[0][0]->texte) and $_critere->param[0][0]->texte == 'id_article')) { // {id_article=x}
				// On pointe sur un article précis, il est donc inutile de rajouter un test sur la rubrique
				// Pour le critère {id_article?} on considère que pour sélectionner des pages uniques
				// ou des articles éditoriaux on doit préciser le critère {id_rubrique}
				$boucle_articles = false;
			}
			elseif (((isset($_critere->param[0][0]->texte) and $_critere->param[0][0]->texte == 'id_rubrique') // {id_rubrique=-1}
					AND ($_critere->op == '=')
					AND ($_critere->param[1][0]->texte == '-1'))
				OR ((isset($_critere->param[0][0]->texte) and $_critere->param[0][0]->texte == 'id_rubrique') // {id_rubrique<0}
					AND ($_critere->op == '<')
					AND ($_critere->param[1][0]->texte == '0'))) {
				// On cherche explicitement des pages uniques
				$boucle_articles = false;
				break;
			}
			elseif (($_critere->op == 'id_rubrique')) {
				// On connait pas à ce stade la valeur de id_rubrique qui est passé dans le env.
				// Aussi, on créer une condition where qui se compile différemment suivant la valeur de l'id_rubrique.
				// En fait, il suffit de tester si l'id_rubrique est null. Dans ce cas il faut bien rajouter id_rubrique>0
				// pour éliminer les pages uniques.
				$boucle_articles = false;
				$env_id = "\$Pile[0]['id_rubrique']";
				$boucle->where[] =
					array("'?'", "(isset($env_id)?(is_array($env_id)?count($env_id):strlen($env_id)):'')", "''", "'articles.id_rubrique>0'");
				break;
			}
		}

		// Si on est en présence d'une boucle article purement éditoriale, on ajoute le filtre id_rubrique>0
		if ($boucle_articles) {
			$boucle->where[] = array("'>'", "'articles.id_rubrique'", "'\"0\"'");
		}

		// Si on est en présence d'un critère {page} quelconque, on force le filtre id_rubrique=-1
		if ($critere_page) {
			$boucle->where[] = array("'='", "'articles.id_rubrique'", "'\"-1\"'");
		}
	}

	return $boucle;
}

?>
