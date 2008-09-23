<?php
#---------------------------------------------------#
#  Plugin  : Pages                                  #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-Pages       #
#-----------------------------------------------------------------#

if (!defined("_ECRIRE_INC_VERSION")) return;

// Modifier le HTML du formulaire pour enlever la sélection du rubrique
// et ajouter le champ page à la place
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
		if (_request('type') == 'page' or $flux['data']['page']){
		
			$flux['data']['type'] = 'page';
			//echo "<pre>"; var_dump($flux); echo "</pre>"; exit;
		
		}
	
	}
	
	return $flux;

}


// Vérifier que la page n'est pas vide
function pages_formulaire_verifier($flux){

	if (is_array($flux) and $flux['args']['form'] == 'editer_article'){
	
		if (_request('type') == 'page' and !_request('page'))
			$flux['data']['page'] .= _T('info_obligatoire');
	
	}
	
	return $flux;

}


// Ajouter le champ page dans le formulaire d'édition d'article
function pages_editer_contenu_objet($flux){

	$args = $flux['args'];
	$erreurs = $args['contexte']['erreurs'];
	
	if ($args['type'] == 'article' and $args['contexte']['type'] == 'page'){
	
		//echo "<pre>"; var_dump($flux); echo "</pre>"; exit;
		// On cherche et remplace l'édition de la rubrique
		$cherche = "/<li[^>]*class=('|\")editer_parent.*?<\/li>/is";
		$remplace = '<li class="editer_page obligatoire'.($erreurs['page'] ? ' erreur' : '').'">';
		$remplace .= '<input type="hidden" name="id_parent" value="'.$args['contexte']['id_rubrique'].'" />';
		$remplace .= '<input type="hidden" name="id_rubrique" value="'.$args['contexte']['id_rubrique'].'" />';
		$remplace .= '<input type="hidden" name="type" value="page" />';
    	$remplace .= '<label for="id_page">'._T('pages:titre_page').'</label>';
    	if ($erreurs['page'])
    		$remplace .= '<span class="erreur_message">'.$erreurs['page'].'</span>';
    	$remplace .= '<input type="text" class="text" name="page" id="id_page" value="'.$args['contexte']['page'].'" />';
    	$remplace .= '</li>';
		$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
	
	}
	
	return $flux;

}


// Ajouter le champ page reçu par POST dans les champs à mettre à jour
function pages_pre_edition_ajouter_page($flux){

	if (is_array($flux) and $flux['args']['type'] == 'article'){
	
		// Si elle existe on récupère la page dans ce qui a été posté
		if (($page = _request('page')) != ''){
		
			// Et on l'ajoute à ce qu'il faut mettre à jour
			$flux['data']['page'] = $page;
		
		}
	
	}
	
	return $flux;

}


// Modifier les liens pourris obtenus à cause de id_rubrique=-1
function pages_hierarchie_page($flux){

	$exec = _request('exec');
	
	if ($exec and ($exec == 'articles' or $exec == 'articles_edit')){
	
		return str_replace("exec=naviguer&id_rubrique=-1", "exec=naviguer", $flux);
	
	}

}

?>
