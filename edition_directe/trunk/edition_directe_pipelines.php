<?php

// styles
function edition_directe_header_prive($flux){

	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/edition_directe_styles.css').'" type="text/css" media="all" />';

	return $flux;	
 }

// Ajouter le formulaire upload

function edition_directe_affiche_gauche($flux){
	include_spip('edition_directe_fonctions');

	$objets_edition_directe=objets_edition_directe();
	if ($en_cours = trouver_objet_exec($flux['args']['exec'])
		AND $type = $en_cours['type']
		AND in_array($type,$objets_edition_directe)
		AND $id_table_objet = $en_cours['id_table_objet']
		AND ($id = intval($flux['args'][$id_table_objet]) OR $id = 0-$GLOBALS['visiteur_session']['id_auteur'])
		AND autoriser('joindredocument',$type,$id)){

		if($id>0)$flux['data'] .= recuperer_fond('prive/objets/editer/colonne_document',array('objet'=>$type,'id_objet'=>$id));
	}

	return $flux;
}

function edition_directe_recuperer_fond($flux){	
	include_spip('edition_directe_fonctions');
	$fond=$flux['args']['fond'] ;
	$contexte=$flux['args']['contexte'] ;
	$objet=_request('exec');
	$texte=$flux['data']['texte'];
	$contexte['objet']=$objet;
	$id='id_'.$objet;
	$exec=_request('exec');
	if($contexte['exec']=='site')$id='id_syndic';
	$contexte['id_objet']=$contexte[$id];
	
	// Seulement dans l'espace priv&eacute;
	if($exec){
		// On cherche les objets actifs pour l'édition directe
		$objets=objets_edition_directe();
		
		// Les objets éditables déclarés
		$objets_dispos=lister_objets(array());
	
		// Insertion du formulaire d'édition	
		if(in_array($objet,$objets)){		
			  if ($fond == 'prive/squelettes/contenu/'.$objet){
					
					$edition=recuperer_fond('prive/echafaudage/contenu/objet_edit_directe',$contexte,array('ajax'=>true));
					$icone='
					<span class="icone_edition_directe icone active">
						<a href="'.generer_action_auteur('edition_directe_auteur','inactive-'.$objet,generer_url_ecrire($objet,$id.'='.$contexte['id_objet'],false)).'" title="'._T('edir:desactiver_edition_directe_objet').$objet.'">
							<img src="'.find_in_path('prive/themes/spip/images/edir-24.png').'"/>
							<b>'._T('edir:titre_plugin').'</b>
						</a>
					</span>';
					$patterns = array('/class=\'icone/','/<!--\/hd-->/','/<h1>/');
					$replacements = array('class="icone invisible',$edition.'<!--/hd-->',$icone.'<h1>');						
					$flux['data']['texte'] = preg_replace($patterns,$replacements,$texte,1);
			    }
			//Suppression de la prévisualisation	
			 if ($fond == 'prive/objets/contenu/'.$objet){	
					$flux['data']['texte'] = '';
			    }	
			    	    
			}
		elseif ($fond == 'prive/squelettes/contenu/'.$objet AND in_array($objet,$objets_dispos)){
			$icone='
					<span class="icone_edition_directe icone inactive">
						<a href="'.generer_action_auteur('edition_directe_auteur','active-'.$objet,generer_url_ecrire($objet,$id.'='.$contexte['id_objet'],false)).'" title="'._T('edir:activer_edition_directe_objet').$objet.'">
							<img src="'.find_in_path('prive/themes/spip/images/edir-24.png').'"/>
							<b>'._T('edir:titre_plugin').'</b>
						</a>
					</span>';		
			$patterns = array('/<h1>/');
			$replacements = array($icone.'<h1>');
			$flux['data']['texte'] = preg_replace($patterns,$replacements,$texte,1);					
			}
		}

 return $flux;   
}

?>
