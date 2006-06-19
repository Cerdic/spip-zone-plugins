<?php

include(dirname(__FILE__).'/../inc_corbeille.php');

//
// Corbeille
function exec_corbeille(){
  global $type_doc;
  global $type_act;
  global $connect_statut;
  global $operation;
  global $debut;
  global $effacer;
  include_ecrire("inc_presentation");

	$js=<<<lescript
<script type="text/javascript">

function checkAll() {  // (un)check all checkboxes by erational.org 
   var flag = document.corb.checkmaster.checked;       
   var ElNum = document.corb.elements.length;   
   for (var i=0;i<ElNum;i++){ // scan form elements to get checkbox
       if (document.corb.elements[i].type=="checkbox")  
                    document.corb.elements[i].checked = flag;
   }                                         
}  

<!-- The JavaScript Source!! http://javascript.internet.com -->

<!-- Begin
function NewWindow(mypage, myname, w, h, scroll) {
         var winl = (screen.width - w) / 2;
         var wint = 0;
         winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable'
         win = window.open(mypage, myname, winprops)
         if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}
//  End -->
</script>
lescript;

	echo $js;
  
	debut_page(_T('corbeille:corbeille'));	  
 
	if ($connect_statut == "0minirezo") {
		$page = "corbeille";
		$page3 = "Corbeille_forum";
		$page4 = "Corbeille_signature";	
		
				
		if (empty($debut)) $debut = 0;
		$titre = "titre"; 
		if (! empty($type_act)) {
			$statut = "poubelle"; $table = "spip_signatures"; $id = "id_signature";
			Corbeille_effacement( $table, $statut, $titre, $id);
			$statut = "refuse"; $table = "spip_breves"; $id = "id_breve";
			Corbeille_effacement( $table, $statut, $titre, $id);
			$statut = "poubelle"; $table = "spip_articles"; $id = "id_article";
			Corbeille_effacement( $table, $statut, $titre, $id);
			$statut = "off"; $table = "spip_forum"; $id = "id_forum";
			Corbeille_effacement( $table, $statut, $titre, $id);
			$statut = "privoff"; $table = "spip_forum"; $id = "id_forum";
			Corbeille_effacement( $table, $statut, $titre, $id);
			$statut = "5poubelle"; $titre = "nom"; $table="spip_auteurs"; $id="id_auteur";
			Corbeille_effacement( $table, $statut, $titre, $id);			 
			$debut=0;$type_act=0;
		}

		if (! empty($type_doc)) {

			switch($type_doc) 
			{
				case "signatures" : 
					$statut = "poubelle"; 
					$titre = "nom_email"; 
					$table = "spip_signatures";
					$id = "id_signature"; 
					$temps = "date_time"; 
					$page_voir = array($page4,'id_document');
					$libelle = _L("Toutes les p&eacute;titions dans la corbeille :");
					break;
				case "breves" : 
					$statut = "refuse"; 
					$table = "spip_breves"; 
					$id = "id_breve"; 
					$temps = "date_heure"; 
					$page_voir = array("breves_voir",'id_breve');
					$libelle = _L("Toutes les brèves dans la corbeille :");
					break;
				case "articles" : 
					$statut = "poubelle"; 
					$table = "spip_articles"; 
					$id = "id_article"; 
					$temps = "date";  
					$page_voir = array("articles",'id_article');
					$libelle = _L("Tous les articles dans la corbeille :");
					break;
				case "forums_publics" :
					$statut = "off";
					$table = "spip_forum";
					$id = "id_forum";
					$temps = "date_heure";
					$page_voir = array($page3,'id_document');
					$page_voir_fin=" onclick=\"NewWindow(this.href,'name','500','500','yes');return false;\""; 
					$libelle = _L("Tous les messages du forum dans la corbeille :");
					break;
				case "forums_prives" :
					$statut = "privoff"; 
					$table = "spip_forum"; 
					$id = "id_forum"; 
					$temps = "date_heure"; 
					$page_voir = array($page3,'id_document');
					$page_voir_fin=" onclick=\"NewWindow(this.href,'name','500','500','yes');return false;\""; 
					$libelle = _L("Tous les messages du forum dans la corbeille :");
					break;
				case "auteurs" :  
					$statut = "5poubelle"; 
					$titre = "nom"; 
					$table="spip_auteurs"; 
					$id="id_auteur"; 
					$temps = "maj"; 
					$page_voir = array("auteurs_edit",'id_auteur');
					$libelle = _L("Tous les auteurs dans la corbeille :");
					break;
			}

			//securite
			if (empty($table) || empty($temps) || empty($id) || empty($statut) || empty($titre)) die("souci grave !");
	    
      $log_efface = "";            
      if ($operation == "effacer") {
	      //suppression des documents demand&eacute;s
	      $log_efface = "<div style='background:#eee;border:1px solid #999;padding:5px;margin:0 0 5px 0' class='verdana2'>";
        $log_efface .= _T("corbeille:doc_effaces");
				if (count($effacer) == 0) $log_efface .= "aucun";
				else {
					$log_efface .= "<ul>";
					for ($i = 0; $i < count($effacer); $i++) {
						$id_doc = $effacer[$i];
						$req2 = "SELECT $titre FROM $table WHERE $id=$id_doc";
						$result2 = spip_query($req2);
						$row2 = spip_fetch_array($result2);
						$log_efface .= "<li>" . $row2[0];
						$req = "DELETE FROM $table WHERE statut='$statut' and $id=$id_doc";
						$result = spip_query($req);
						if (! $result) $log_efface .= " : erreur !";
						$log_efface .= "</li>\n";
					}					 
					$log_efface .= "</ul>";
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
				$req_corbeille = "select COUNT(*) from $table WHERE statut like '$statut'";
				$result_corbeille = spip_query($req_corbeille);
				$total = 0;
				
				if ($row = spip_fetch_array($result_corbeille)) $total = $row[0];
			
				echo "<br /><br />";
				
				//on affiche les docs 10 par 10 : creation des liens vers suivants/precedents
				if ($total > 10) {
					echo "<div style='text-align:center'>";
					for ($i = 0; $i < $total; $i = $i + 10){
						$y = $i + 9;
						if ($i == $debut)
						echo "<span stylz='font-size:large'><strong>[$i-$y]</strong></span> ";
						else
						echo "[<a href='".generer_url_ecrire($page,"type_doc=$type_doc&debut=$i")."'>$i-$y</a>] ";
					}
					echo "</div>";
				}

				$requete = "SELECT $id, $temps, $titre FROM $table WHERE statut like '$statut' ORDER BY $temps DESC LIMIT $debut,10";
			
				$result=spip_query($requete);
				
				if (spip_num_rows($result) == 0) 
					echo "aucun";
				else {
					echo "<form action='".generer_url_ecrire($page)."&amp;type_doc=$type_doc' method='post' name='corb'>\n";
					echo "<input type='hidden' name='operation' value='effacer' />\n";
					echo "<input type='hidden' name='type_doc' value='$type_doc' />\n";
					echo "<table style='text-align:center;border:0px;width:100%;background:none;' CELLPADDING=3 CELLSPACING=0 WIDTH=100%>";
					echo "<tr>";
					echo "<td style='text-align:left;'><input type='checkbox' value='0' name='checkmaster' onclick='checkAll();' /></td>";
					echo "<td style='text-align:left;'>"._L("Titre")."</td>";
					echo "<td style='text-align:left;'>"._L("Parution")."</td>";					
					echo "</tr>\n\n";

					//affichage des 10 documents supprimables
					while($row=spip_fetch_array($result))
					{
						$id_document=$row[0];
						$date_heure=$row[1];
						$titre=$row[2];
						
						if ($compteur%2) $couleur="#FFFFFF";
						            else $couleur="#EEEEEE";
						$compteur++;
						
						echo "<tr style='background:$couleur;'>";
						echo "<td style='width:5%;'><input type='checkbox' name='effacer[]' value='$id_document'' /></td>";					
						echo "<td class='verdana2' style='width:70%;'>";
						// FIXME bug: lien sur forums et petition pas bon,
						if (! empty($page_voir))echo "<a href='".generer_url_ecrire($page_voir[0],$page_voir[1]."=$id_document")."'$page_voir_fin>";
						echo typo($titre);
						if (! empty($page_voir)) echo "</a>";						
						echo "</td>";
						echo "<td class='verdana2' style='width:25%;'>".affdate($date_heure)."</td>";						
						echo "</tr>\n";
					}
					echo "</table><br /><input type='submit' value='"._L("Effacer")."' /></form>\n\n";
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
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>";

}

?>
