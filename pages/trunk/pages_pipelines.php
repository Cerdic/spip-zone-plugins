<?php
#---------------------------------------------------#
#  Plugin  : Pages                                  #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-Pages       #
#-----------------------------------------------------------------#

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


// Vérifier que la page n'est pas vide
function pages_formulaire_verifier($flux){

	// Si on est dans l'édition d'un article
	if (is_array($flux) and $flux['args']['form'] == 'editer_article'){
	
		// Si on est dans un article de type page mais que le champ "page" est vide
		if (_request('modele') == 'page' and !_request('champ_page'))
			$flux['data']['champ_page'] .= _T('info_obligatoire');
	
	}

	return $flux;

}


// Ajouter le champ page dans le formulaire d'édition d'article
function pages_editer_contenu_objet($flux){

	$args = $flux['args'];
	$erreurs = $args['contexte']['erreurs'];
	
	if ($args['type'] == 'article' and $args['contexte']['modele'] == 'page'){
	
		// On cherche et remplace l'édition de la rubrique
		$cherche = "/<li[^>]*class=('|\")editer editer_parent.*?<\/li>/is";
		$remplace = '<li class="editer_page obligatoire'.($erreurs['champ_page'] ? ' erreur' : '').'">';
		$remplace .= '<input type="hidden" name="id_parent" value="-1" />';
		$remplace .= '<input type="hidden" name="id_rubrique" value="-1" />';
		$remplace .= '<input type="hidden" name="modele" value="page" />';
    	$remplace .= '<label for="id_page">'._T('pages:titre_page').'</label>';
    	if ($erreurs['champ_page'])
    		$remplace .= '<span class="erreur_message">'.$erreurs['champ_page'].'</span>';
    	$value = $args['contexte']['champ_page'] ? $args['contexte']['champ_page'] : $args['contexte']['page'];
    	$remplace .= '<input type="text" class="text" name="champ_page" id="id_page" value="'.$value.'" />';
    	$remplace .= '</li>';
		$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
	
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
 * Insertion dans le pipeline boite_infos
 * 
 * Ajouter un lien pour transformer une article normal en page inversement
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux Le contexte modifié
 */
function pages_boite_infos($flux){
	if ($flux['args']['type'] == 'article' and autoriser('modifier', 'article', $flux['args']['id'])){
		if (sql_getfetsel('page', 'spip_articles', 'id_article='. $flux['args']['id']) == ''){
			$flux['data'] .= '<div>
				<a href="'.parametre_url(parametre_url(generer_url_ecrire('article_edit'), 'id_article', $flux['args']['id']), 'modele', 'page').'" class="cellule-h">
					<img src="'.find_in_path('images/page-24.png').'" style="vertical-align:middle;" alt="" />
					<span style="vertical-align:middle;">'._T('pages:convertir_page').'</span>
				</a>
			</div>';
		}
		else{
			$flux['data'] .= '<div>
				<a href="'.parametre_url(parametre_url(generer_url_ecrire('article_edit'), 'id_article', $flux['args']['id']), 'modele', 'article').'" class="cellule-h">
					<img src="'.find_in_path('images/article-24.gif').'" style="vertical-align:middle;" alt="" />
					<span style="vertical-align:middle;">'._T('pages:convertir_article').'</span>
				</a>
			</div>';
		}
	}
	return $flux;
}

?>
