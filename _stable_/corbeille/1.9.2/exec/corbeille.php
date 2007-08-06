<?php

include(dirname(__FILE__).'/../inc_corbeille.php');
include(dirname(__FILE__).'/../inc_param.php');


//
// Corbeille
/** 
 *exec_corbeille() function principale d'√©x√©cution de l'administration du plugin
 */ 
function exec_corbeille(){
  global $type_doc;
  global $type_act;
  global $connect_statut;
  global $operation;
  global $debut;
  global $effacer; //array des id des objets √† effacer
  include_spip("inc/presentation");
  //charge les param√©tres de conf
  global $corbeille_param;


	$js='
<script type="text/javascript">
<!--
function checkAll() {  // (un)check all checkboxes by erational.org
   var flag = document.corb.checkmaster.checked;
   var ElNum = document.corb.elements.length;
   for (var i=0;i<ElNum;i++){ // scan form elements to get checkbox
       if (document.corb.elements[i].type=="checkbox")
                    document.corb.elements[i].checked = flag;
   }
}

-->
</script>
';

	echo $js;

	debut_page(_T('corbeille:corbeille'));

	if ($connect_statut == "0minirezo") {
		$page = "corbeille";	

		if (empty($debut)) $debut = 0;
		//si type_act non nul tous les éléments effaçable le sont. 
		if (! empty($type_act)) {
			//pour chacun des objets declarés dans inc_param la fonction effacement est appellée
			foreach($corbeille_param as $key => $objet) {
				Corbeille_effacement($key); //indique l'objet √† vider
			}
			$debut=0;$type_act=0;
		}
		//si un type_doc est spécifié alors recherche des éléments
		if (! empty($type_doc)) {
			//charge les paramètres propre à l'objet demandé (breves, articles, auteurs, ...)
			$statut = $corbeille_param[$type_doc]["statut"];
			$titre = $corbeille_param[$type_doc]["titre"];
			$table = $corbeille_param[$type_doc]["table"];
			$id = $corbeille_param[$type_doc]["id"];
			$temps = $corbeille_param[$type_doc]["temps"];
			$page_voir = $corbeille_param[$type_doc]["page_voir"];
			$libelle = $corbeille_param[$type_doc]["libelle"];

			//securite
			if (empty($table) || empty($temps) || empty($id) || empty($statut) || empty($titre)) die(_T("corbeille:souci"));
	    
      $log_efface = "";            
      if ($operation == "effacer") {
	      //suppression des documents demandés
	      $log_efface = "<div style='background:#eee;border:1px solid #999;padding:5px;margin:0 0 5px 0' class='verdana2'>";
        $log_efface .= _T("corbeille:doc_effaces");
				if (count($effacer) == 0) $log_efface .= _T("corbeille:aucun");
				else {
					$log_efface .= "<ul>";
					//rappelle les éléments supprimés
					foreach($effacer as $i => $id_doc) {
						$req2 = "SELECT $titre FROM $table WHERE $id=$id_doc";
						$result2 = spip_query($req2);
						$row2 = spip_fetch_array($result2);
						$log_efface .= "<li>" . $row2[$titre];
						$log_efface .= "</li>\n";
					}
					$log_efface .= "</ul>";
					//supprime les objets selectionn√©s
					Corbeille_effacement($type_doc, $effacer);
				}
				$log_efface .= "</div>\n";
			}

        // HTML output
      	debut_gauche();	
      	debut_boite_info();
      	echo propre(_T('corbeille:readme'));  	
      	fin_boite_info();
      	
      	echo "<br />";
        debut_boite_info();
        $page = "corbeille";
      	Corbeille_affiche($page);
      	fin_boite_info();
      	
      	debut_droite();
      	gros_titre(_T('corbeille:corbeille'));
        
        echo $log_efface;
				echo $libelle;
				
				//affichage des documents mis a la corbeille et de type doc type                        
				$req_corbeille = "select COUNT(*) as nbElt from $table WHERE statut like '$statut'";
				$result_corbeille = spip_query($req_corbeille);
				$total = 0;
				
				if ($row = spip_fetch_array($result_corbeille)) $total = $row['nbElt'];

				echo "<br /><br />";
				
				//on affiche les docs 10 par 10 : creation des liens vers suivants/precedents
				if ($total > 10) {
					echo "<div style='text-align:center'>";
					for ($i = 0; $i < $total; $i = $i + 10){
						$y = $i + 9;
						if ($i == $debut)
						echo "<span style='font-size:large'><strong>[$i-$y]</strong></span> ";
						else
						echo "[<a href='".generer_url_ecrire($page,"type_doc=$type_doc&debut=$i")."'>$i-$y</a>] ";
					}
					echo "</div>";
				}

				$requete = "SELECT $id, $temps, $titre FROM $table WHERE statut like '$statut' ORDER BY $temps DESC LIMIT $debut,10";
				$result=spip_query($requete);
				
				if (spip_num_rows($result) == 0)
					echo _T("corbeille:aucun");
				else {
					echo "<form action='".generer_url_ecrire($page)."&amp;type_doc=$type_doc' method='post' name='corb'>\n";
					echo "<input type='hidden' name='operation' value='effacer' />\n";
					echo "<input type='hidden' name='type_doc' value='$type_doc' />\n";
					echo "<table style='text-align:center;border:0px;width:100%;background:none;' CELLPADDING=3 CELLSPACING=0 WIDTH=100%>";
					echo "<tr>";
					echo "<td style='text-align:left;'><input type='checkbox' value='0' name='checkmaster' onclick='checkAll();' /></td>";
					echo "<td style='text-align:left;'>"._T("corbeille:titre")."</td>";
					echo "<td style='text-align:left;'>"._T("corbeille:parution")."</td>";
					echo "</tr>\n\n";

					//affichage des 10 documents supprimables
					while($row=spip_fetch_array($result)) {
						
						$id_document=$row[$id];
						$date_heure=$row[$temps];
						$titre=$row[$titre];

						if ($compteur%2) $couleur="#FFFFFF";
						            else $couleur="#EEEEEE";
						$compteur++;

						echo "<tr style='background:$couleur;'>";
						echo "<td style='width:5%;'><input type='checkbox' name='effacer[]' value='$id_document'' /></td>";
						echo "<td class='verdana2' style='width:70%;'>";
						if (! empty($page_voir)) {
                echo "<a href='".generer_url_ecrire($page_voir[0],$page_voir[1]."=$id_document")."'>".typo($titre)."</a>";
            } else {
                /* version 1: avec bloc dépliant
                echo typo($titre);
                echo "<br />".bouton_block_invisible("for_".$id_document)._T('corbeille:voir_detail');
                echo debut_block_invisible("for_".$id_document);
                if ($type_doc == "signatures") $detail = recupere_signature_detail($id_document);
                                          else $detail = recupere_forum_detail($id_document);
                echo "<div style='background:#aaa;padding:5px'>$detail</div>";
                echo fin_block();
                */
                // version 2 rollover
                if ($type_doc == "signatures") $detail = recupere_signature_detail($id_document);
                                          else $detail = recupere_forum_detail($id_document);
                echo "<div id='for_$id_document' style='position:absolute;background:".$GLOBALS["couleur_claire"].";padding:5px;margin:0 0 0 350px;width: 350px;' class='invisible_au_chargement'>$detail</div>";
                echo "<a href='#' onmouseover=\"changestyle('for_$id_document', 'visibility', 'visible');\" onmouseout=\"changestyle('for_$id_document', 'visibility', 'hidden');\">".typo($titre)."</a>";                
            }
						echo "</td>";
						echo "<td class='verdana2' style='width:25%;'>".affdate($date_heure)."</td>";
						echo "</tr>\n";
					}
					echo "</table><br /><input type='submit' value='"._T("corbeille:effacer")."' /></form>\n\n";
				}
			} else { // empty doc: affichage simple	
			   // HTML output
			   debut_gauche();	
      	 debut_boite_info();
         echo propre(_T('corbeille:readme'));  	
      	 fin_boite_info();
      	
         echo "<br />";
         debut_boite_info();
         $page = "corbeille";
         Corbeille_affiche($page);
         fin_boite_info();
        	
         debut_droite();
         gros_titre(_T('corbeille:corbeille'));
         echo "<p>"._T('corbeille:choix_doc')."</p>";
        			   
			}
	}
	else 
		echo "<strong>"._T("ecrire:avis_acces_interdit")."</strong>";

}

?>
