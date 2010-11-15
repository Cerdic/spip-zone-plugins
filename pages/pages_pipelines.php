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

	if ($flux['args']['exec'] == 'articles_edit'){
	
		find_in_path('abstract_sql', 'base/', true);
		if (
			_request('type') == 'page'
			or
			(
				($id_article = $flux['args']['id_article']) > 0
				and
				(sql_getfetsel('page', 'spip_articles', 'id_article='.sql_quote($id_article)))
			)
		)
		{
			
			// On cherche et remplace l'entete de la page : "modifier la page"
			$cherche = "/(<div[^>]*class=('|\")entete-formulaire.*?<\/a>).*?(<h1>.*?<\/h1>.*?<\/div>)/is";
			$surtitre = _T('pages:modifier_page');
			$remplace = "$1$surtitre$3";
			$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
			
			// Si c'est une nouvelle page, on remplace le lien de retour dans l'entete
			if (_request('new') == 'oui'){
			
				$cherche = "/(<a[^>]*class=('|\")icone36[^>]*?href=('|\"))[^'\"]*(('|\").*?<\/a>)/is";
				$retour = generer_url_ecrire("pages_tous");
				$remplace = "$1$retour$4";
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
	
		// Si on est dans un article de type page
		if (_request('type') == 'page' or ($flux['data']['page'] and _request('type') != 'article')){
			$flux['data']['type'] = 'page';
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
		if (_request('type') == 'page' and !_request('champ_page'))
			$flux['data']['champ_page'] .= _T('info_obligatoire');
	
	}
	
	return $flux;

}


// Ajouter le champ page dans le formulaire d'édition d'article
function pages_editer_contenu_objet($flux){

	$args = $flux['args'];
	$erreurs = $args['contexte']['erreurs'];
	
	if ($args['type'] == 'article' and $args['contexte']['type'] == 'page'){
	
		// On cherche et remplace l'édition de la rubrique
		$cherche = "/<li[^>]*class=('|\")editer_parent.*?<\/li>/is";
		$remplace = '<li class="editer_page obligatoire'.($erreurs['champ_page'] ? ' erreur' : '').'">';
		$remplace .= '<input type="hidden" name="id_parent" value="-1" />';
		$remplace .= '<input type="hidden" name="id_rubrique" value="-1" />';
		$remplace .= '<input type="hidden" name="type" value="page" />';
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


// Ajouter le champ page reçu par POST dans les champs à mettre à jour
function pages_pre_edition_ajouter_page($flux){
	if (is_array($flux) and $flux['args']['type'] == 'article'){
	
		// Si elle existe on récupère la page dans ce qui a été posté
		if ((($page = _request('champ_page')) != '') AND ($page != 'article')){
		
			// Et on l'ajoute à ce qu'il faut mettre à jour
			$flux['data']['page'] = $page;
			// Et on force l'id_rubrique à -1
			$flux['data']['id_rubrique'] = '-1';
			// si l'id_parent est supérieur à 0 on pense à vider le champ "page"
			if (_request('id_parent') > 0){
				$flux['data']['page'] = '';
			}
		}
	}
	
	return $flux;

}

// Ajouter un lien pour transformer une article normal en page ou l'inverse
function pages_boite_infos($flux){
	if ($flux['args']['type'] == 'article' and autoriser('modifier', 'article', $flux['args']['id'])){
		if ($flux['args']['row']['page'] == ''){
			$flux['data'] .= '<div>
				<a href="'.parametre_url(parametre_url(generer_url_ecrire('articles_edit'), 'id_article', $flux['args']['id']), 'type', 'page').'" class="cellule-h">
					<img src="'.find_in_path('images/page-24.png').'" style="vertical-align:middle;" alt="" />
					<span style="vertical-align:middle;">'._T('pages:convertir_page').'</span>
				</a>
			</div>';
		}
		else{
			$flux['data'] .= '<div>
				<a href="'.parametre_url(parametre_url(generer_url_ecrire('articles_edit'), 'id_article', $flux['args']['id']), 'type', 'article').'" class="cellule-h">
					<img src="'.find_in_path('images/article-24.gif').'" style="vertical-align:middle;" alt="" />
					<span style="vertical-align:middle;">'._T('pages:convertir_article').'</span>
				</a>
			</div>';
		}
	}
	return $flux;
}

?>
