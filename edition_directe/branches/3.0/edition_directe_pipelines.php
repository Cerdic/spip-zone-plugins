<?php

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

	// On cherche les objets actifs pour l'édition directe
	$objets=objets_edition_directe();
		
	// Insertion du formulaire d'édition	
	foreach($objets as $objet){
		  if ($fond == 'prive/squelettes/contenu/'.$objet){
				$contexte['objet']=$objet;
				$contexte['id_objet']=$contexte['id_'.$objet];
				if($contexte['exec']=='site')$contexte['id_objet']=$contexte['id_syndic'];
				
				$texte=$flux['data']['texte'];
				$edition=recuperer_fond('prive/echafaudage/contenu/objet_edit_directe',$contexte,array('ajax'=>true));
				$patterns = array('/class=\'icone/','/<!--\/hd-->/');
				$replacements = array('class="icone invisible',$edition.'<!--/hd-->');						
				$flux['data']['texte'] = preg_replace($patterns,$replacements,$texte,1);
		    }
		//Suppression de la prévisualisation	
		 if ($fond == 'prive/objets/contenu/'.$objet){			
				$flux['data']['texte'] = '';
		    }		    
		}

 return $flux;   
}

?>
