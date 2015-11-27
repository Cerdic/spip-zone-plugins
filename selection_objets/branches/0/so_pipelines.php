<?php

/*function so_affiche_milieu ( $vars="" ) {
	$exec = $vars["args"]["exec"];
	$id_rubrique = $vars["args"]["id_rubrique"];
	
	echo serialize($vars["args"]);
	
	
	
		if (!$id_rubrique)$id_rubrique=0;
		
		$id_article = $vars["args"]["id_article"];
		$data =	$vars["data"];
		
		$active = lire_config('so/id_rubrique');
		
	
		if ($exec == "naviguer" && in_array($id_rubrique,$active) OR ($exec == "accueil" && in_array($id_rubrique,$active))) {


			include_spip("inc/utils");


		
			
			$contexte = array('id_rubrique'=>$id_rubrique);
	
			$ret .= "<div id='pave_selection'>";
		
			$page = evaluer_fond("selection_interface", $contexte);
			$ret .= $page["texte"];

			$ret .= "</div>";



			$bouton =  bouton_block_depliable(_L("SÉLECTION D’ARTICLES"),false,"block_selection");
//			$bouton = "SÉLECTION D’ARTICLES";
		
			$ret .= debut_cadre_enfonce("../plugins/so/imgs/emblem-favorite.png", true, "",$bouton);
			$ret .= pb_install_afficher_articles ($id_rubrique) ;
			
			$ret .= pb_install_dernier_ret($id_rubrique);
			
			$ret .= debut_block_depliable(false, "block_selection");
			
			$ret .= "<form onsubmit='return false;'>";
			$ret .= "<div>Ajouter un article&nbsp;: ";
			$ret .= "<script>";
			$ret .= "var ze_rechercher = 0;\n";
			$ret .= "function selection_chercher() {
						texte = $('#selection_chercher_article').attr('value');
						$('#articles_proposes').hide('fast'); 
						$('#articles_proposes').load('?exec=so_chercher_articles&chercher='+escape(texte), 
							function(){  
								$('#articles_proposes').show('slow'); 
							}
						);	
					}
				";
			
			$ret .= "</script>";
			
			$ret .= "<input type='text' style='width: 200px;' class='fondl' id='selection_chercher_article' onkeypress=\"ze_rechercher = setTimeout('selection_chercher()',100);\" />";
			$ret .= "</div>";

			$ret .= "<div id='articles_proposes'>AAA</div>";

			
			$ret .= "</form>";
			
			$ret .= fin_block();
			
			$ret .= fin_cadre_enfonce(true);
			
//			$ret = ajax_action_greffe("editer_mot", $id_objet, $ret);
			
		}


		$data .= $ret;
	
		$vars["data"] = $data;
		return $vars;
	} */
function so_affiche_gauche($flux) {
   $exec = $flux["args"]["exec"];

   
      $contexte = array();
      
   	switch($exec){
   	case  'articles':
   		$contexte['objet']='articles';
    		$contexte['id_objet']=$flux["args"]["id_article"]; 
    		
    		$sql = sql_fetsel('lang','spip_articles','id_article='.$contexte['id_objet']);

		$contexte['langue'] = $sql['lang'];	
			
    		 				
   		break;
   		
   	case  'naviguer':
    		$contexte['objet']='rubriques';

    		$contexte['id_objet']=$flux["args"]["id_rubrique"]; 
    		  		
   		$sql = sql_fetsel('langue_choisie,lang','spip_rubriques','id_rubrique='.$contexte['id_objet']);
   		
   		$contexte['langue'] = $sql['lang'];
   		
   		/*if($sql['langue_choisie']!='non')$contexte['langue'] = $sql['lang'];
   		
   		if($sql['langue_choisie'] == 'non') $contexte['langue'] = explode(",",lire_config("langues_utilisees"));  */
   		 		

   		
   		break;   
   	}

	if ($contexte['objet']){
			

      $ret .= recuperer_fond("prive/gauche", $contexte);

      $flux["data"] .= $ret;
	};
    return $flux;
}

?>
