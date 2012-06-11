<?php

function edition_directe_header_prive($flux){
	
	include_spip('inc/config');
	$config=lire_config('edition_directe');
	
	$objet=$_REQUEST['exec'];
	
	if($objet)$config=objet_edition_directe($objet);
	
	if(is_array($config))
		if (in_array($objet,$config))
			$flux .= '<link rel="stylesheet" href="'.generer_url_public('edition_directe_styles','id_'.$objet.'='.$_REQUEST['id_'.$objet]).'" type="text/css" media="all" />';

	return $flux;	
 }

// Ajouter le formulaire upload

function edition_directe_afficher_config_objet($flux){
	$type= $flux['args']['type'];
	$id = $flux['args']['id'];
	if($type=='article' AND autoriser('joindredocument',$type,$id ) AND  objet_edition_directe($type)){
		$flux['data'] .=recuperer_fond('prive/objets/editer/colonne_document',array('objet'=>$type,'id_objet'=>$id));
			}
	if($type=='rubrique' AND autoriser('joindredocument',$type,_request('id_rubrique')) AND  objet_edition_directe($type)){
		$flux['data'] .=recuperer_fond('prive/objets/editer/colonne_document',array('objet'=>$type,'id_objet'=>$id));
			}	
					
	if($type=='breve' AND autoriser('joindredocument',$type,_request('id_breve')) AND  objet_edition_directe($type)){
		$flux['data'] .=recuperer_fond('prive/objets/editer/colonne_document',array('objet'=>$type,'id_objet'=>$id));
		}
	if($type=='site' AND autoriser('joindredocument',$type,_request('id_'.$type)) AND  objet_edition_directe($type)){
		$flux['data'] .=recuperer_fond('prive/objets/editer/colonne_document',array('objet'=>$type,'id_objet'=>$id));
		}									
	/* pas de pipeline disponible pour le moment
	if($type=='auteur' AND autoriser('joindredocument',$type,_request('id_'.$type)) AND objet_edition_directe($type)){
		$flux['data'] .=recuperer_fond('prive/objets/editer/colonne_document',array('objet'=>$type,'id_objet'=>$id));
		}								
	if($type=='mot' AND autoriser('joindredocument',$type,_request('id_'.$type)) AND objet_edition_directe($type)){
		$flux['data'] .=recuperer_fond('prive/objets/editer/colonne_document',array('objet'=>$type,'id_objet'=>$id));
		}	*/					
    return $flux;
}


function edition_directe_recuperer_fond($flux){
	
	include_spip('inc/config');
	include_spip('base/objets');
	$fond=$flux['args']['fond'] ;
	$contexte=$flux['args']['contexte'] ;
	$objets=array();
	$objets=lire_config('edition_directe/objets');
	
	// Sie rien n'est choisit, tout est en édition directe
	if(count($objets)<1){
		$liste_objets=lister_tables_objets_sql();

		foreach($liste_objets AS $o=>$valeur){
			$objets[]=$valeur['page'];
			}
		}
	
	// Insertion du formulaire d'édition	
	foreach($objets as $objet){
		  if ($fond == 'prive/squelettes/contenu/'.$objet){
				$contexte['objet']=$objet;
				$contexte['id_objet']=$contexte['id_'.$objet];
				$texte=$flux['data']['texte'];
				$edition=recuperer_fond('prive/echafaudage/contenu/objet_edit_directe',$contexte,array('ajax'=>true));
				$patterns = array('/class=\'icone/','/<!--\/hd-->/');
				$replacements = array('class="icone invisible',$edition.'<!--/hd-->');						
				$flux['data']['texte'] = preg_replace($patterns,$replacements,$texte);
		    }
		//Suppression de la prévisualisation	
		 if ($fond == 'prive/objets/contenu/'.$objet){			
				$flux['data']['texte'] = '';
		    }		    
		}

 return $flux;   
}

?>
