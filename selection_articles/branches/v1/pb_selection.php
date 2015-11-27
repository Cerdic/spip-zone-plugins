<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

	function pb_selection_interface ( $vars="" ) {
		$exec = $vars["args"]["exec"];
		$id_rubrique = $vars["args"]["id_rubrique"];
		$id_article = $vars["args"]["id_article"];
		$data =	$vars["data"];
		
		if ($exec == "naviguer") {


			include_spip("inc/utils");


		
			
			$contexte = array('id_rubrique'=>$id_rubrique);
	
			$ret .= "<div id='pave_selection'>";
		
			$page = evaluer_fond("selection_interface", $contexte);
			$ret .= $page["texte"];

			$ret .= "</div>";
/*


			$bouton =  bouton_block_depliable(_L("SÉLECTION D’ARTICLES"),false,"block_selection");
//			$bouton = "SÉLECTION D’ARTICLES";
		
			$ret .= debut_cadre_enfonce("../plugins/pb_selection/imgs/emblem-favorite.png", true, "",$bouton);
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
						$('#articles_proposes').load('?exec=pb_selection_chercher_articles&chercher='+escape(texte), 
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
			*/
		}


		$data .= $ret;
	
		$vars["data"] = $data;
		return $vars;
	}


?>