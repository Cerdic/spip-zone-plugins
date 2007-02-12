<?

function documents_distants_ajouter_boutons($boutons_admin){
	$boutons_admin['naviguer']->sousmenu['documents_distants']= new Bouton("plugin-24.gif", _T('documentsdistants:importer') );
  
  return $boutons_admin;
 }

function documents_distants_affiche_gauche($flux){
	include_spip('public/assembler');
	
	if (in_array($flux['args']['exec'],array('articles')) and $flux['args']['id_article'])		{
					$flux['data'].=recuperer_fond('lien_documents_distants',Array('type_lien'=>'articles','id'=>$flux['args']['id_article']));
	
				
	}
	//rubriques
	if (in_array($flux['args']['exec'],array('rubriques_edit')) and $flux['args']['id_rubrique'])		{
					$flux['data'].=recuperer_fond('lien_documents_distants',Array('type_lien'=>'rubriques','id'=>$flux['args']['id_rubrique']));
	
				
	}
	//breves
	if (in_array($flux['args']['exec'],array('breves_voir')) and $flux['args']['id_breve'])		{
					$flux['data'].=recuperer_fond('lien_documents_distants',Array('type_lien'=>'breves','id'=>$flux['args']['id_breve']));
	
				
	}
	return $flux;} 
 
?>