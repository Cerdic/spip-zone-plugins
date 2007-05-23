<?php

/**
 * definition du plugin "corbeille" version "classe statique"
 * utilisee comme espace de nommage
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_CORBEILLE',(_DIR_PLUGINS.end($p)));
 
/* static public */

/* public static */
function Corbeille_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
	  // on voit le bouton dans la barre "naviguer"
	  $boutons_admin['configuration']->sousmenu['corbeille']= new Bouton(
		"../"._DIR_PLUGIN_CORBEILLE."/img_pack/trash-full-24.png",  // icone
		_T('corbeille:corbeille')	// titre
		);
	}
	return $boutons_admin;
}

/* public static */
function Corbeille_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}

//vide l'ensembles des element effacçable d'un objet
//argument optionnel : tabid liste des id à supprimer
function Corbeille_effacement($type_doc, $tabid=NULL) {
	global $corbeille_param;
	$table = $corbeille_param[$type_doc]["table"];
	$index = $corbeille_param[$type_doc]["id"];
	$statut = $corbeille_param[$type_doc]["statut"];
	$titre = $corbeille_param[$type_doc]["titre"];
	$table_liee = $corbeille_param[$type_doc]["tableliee"];
	 
	//compte le nb total d'objet supprimable 
	$total=Corbeille_compte_elements_vider($table, $statut, $titre);
	
	if ($total == 0) {
		echo "$table vide <br />";
	} else {
		//determine les index des éléments à supprimer
		if (is_null($tabid)) {
			//recupére les identifiants des objets à supprimer
			$req = "SELECT $index FROM $table WHERE statut='$statut'";			
			$result = spip_query($req);
			while ($row = spip_fetch_array($result)) {
				$tabid[] = $row[$index];
			}
		}
		//supprime les élements défini par la liste des index
		foreach($tabid as $id) {
			$req = "DELETE FROM $table WHERE statut='$statut' AND $index = $id";
			$result = spip_query($req);
			//suppresion des elements liés	
			if ($table_liee) {
				foreach($table_liee as $unetable) {
					$req = "DELETE FROM $unetable WHERE $index = $id";
					$result = spip_query($req);							
				}
			}
		}
	}
}

// affiche l'icone poubelle (vide ou pleine)
function Corbeille_icone_poubelle($total_table) {
	if (empty($total_table)) 	return "<img src='"._DIR_PLUGIN_CORBEILLE."/img_pack/trash-empty-24.png' alt='trash empty'/>"; 
	                     else return "<img src='"._DIR_PLUGIN_CORBEILLE."/img_pack/trash-full-24.png'  alt='trash full'/>";
}

// compteur
function Corbeille_compte_elements_vider($table, $statut, $titre) {
                        $req_corbeille = "select COUNT(*) from $table WHERE statut like '$statut'";
                        $result_corbeille = spip_query($req_corbeille);
                        $total1 = 0;
                        if ($row = spip_fetch_array($result_corbeille,SPIP_NUM)) $total = $row[0];
	return ($total);
}
  
// affiche ligne
function Corbeille_affiche_ligne($titre,$url,$total_table){
		echo "<div class='verdana2' style='width:100%;padding:5px;'>\n";		
		if ($total_table>0) $style = "class='corbeille'";
		               else $style = "";
    echo "<a href='$url'$style>";		              
    echo Corbeille_icone_poubelle($total_table);    
		echo " $total_table $titre";
		echo "</a>";
    echo "</div>\n";
}

function Corbeille_affiche($page){
  		//charge les paramétres
		global $corbeille_param;
		//initialise les variables
		$totaux = array();
			
		//Calcul du nombre d'objets effaçable
		foreach($corbeille_param as $key => $objet) {
			$totaux[$key] = Corbeille_compte_elements_vider($objet["table"],$objet["statut"],$objet["titre"]);
			$totaux["tout"] += $totaux[$key];
		}
 	
		//types de documents geres par la corbeille
		echo "<strong>"._T('corbeille:choix_doc')."</strong><br/>";
		echo "<style type='text/css'>div a.corbeille {display:block;border:3px solid #f00;padding: 5px;margin-right:5px} div a.corbeille:hover {background: #fcc;border:3px solid #c00;} </style>";
	
		//parcours les totaux et genere une ligne de résulat par type d'objet
		foreach($totaux as $key => $total) {
			//ignore tout car pas de paramétre déclaré dans inc_param
			if ($key != "tout") {
				Corbeille_affiche_ligne($corbeille_param[$key]["libelle_court"],generer_url_ecrire($page,"type_doc=".$key),$total);
			}
		}
		// Corbeille_affiche_ligne(_L('Tout'),generer_url_ecrire($page,"type_act=tout"),$totaux); FIXME: ne pas afficher la ligne "tout" car pas fonctionnel pour l'instant
}

//
// recupere les details du forum
function recupere_forum_detail($id_document){
  $str = "";	
	$req="SELECT id_forum, date_heure, titre, texte, auteur, email_auteur FROM spip_forum WHERE id_forum=$id_document";
	$result = spip_query($req);
	$row=spip_fetch_array($result,SPIP_NUM);
	
	$str = _T("date_fmt_nomjour_date",array("date"=>affdate($row[1]),"nomjour"=>'')) . ", ";
	if (! empty($row[5])) $str .= "<a href=\"mailto:" . $row[5] . "\">";
	$str .= $row[4];
	if (! empty($row[5])) 	$str .= "</a>";
	$str .= _T("corbeille:ecrit")."<br /><br /><strong>";
  $str .= $row[2] . "</strong><br /><br /><p align=justify>" . $row[3] . "</p>";
	
  return $str;	
}

//
// recupe les details d'une petition
function recupere_signature_detail($id_document){
  $str = "";
	$req="SELECT id_article, date_time, ad_email, nom_site, nom_email  FROM spip_signatures WHERE id_signature=$id_document";
	$result = spip_query($req);
	$row=spip_fetch_array($result,SPIP_NUM);
	
	$str = "<strong>" . _T("date_fmt_nomjour_date",array("date"=>affdate($row[1]),"nomjour"=>'')). "</strong>,<br />";
	if (! empty($row[5])) $str .= "<a href=\"mailto:" . $row[5] . $row[4] . "\">";
	$str .= $row[4];
	if (! empty($row[5])) $str .= "</a>";
	$str .= _T("corbeille:signe")." <strong>";
	$str .= $row[2] . "</strong><br />";
	$str .= _T("corbeille:petition")." <strong>";
	$row2=spip_fetch_array(spip_query("SELECT * FROM spip_articles WHERE id_article=$row[0]"),SPIP_NUM);
	$str .= $row2[2] . "<strong> : " . $row2[5] . "</strong><br />";

  return $str;
}


?>
