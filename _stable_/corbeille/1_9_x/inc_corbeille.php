<?php

/**
 * definition du plugin "corbeille" version "classe statique"
 * utilisee comme espace de nommage
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_CORBEILLE',(_DIR_PLUGINS.end($p)));
 
/* static public */

/* public static */
/**
 * Corbeille_ajouterBoutons() ajoute un lien au panneau d'administration
 * @param $boutons_admin flux html de la barre de menu dans la partie privée
 * @ return flux html édité     
 */
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

/**
 *Corbeille_effacement() supprime les elements selectionnés par l'utilisateur
 * @param $type_doc nom de l'objet spip défini dans inc_param.php @see inc_param.php
 * @param $tabid tableau des id à supprimer, optionnel si tout les éléments sont supprimés
 *      
 * @return neant  
 */  
function Corbeille_effacement($type_doc, $tabid=NULL) {
	global $corbeille_param;
	$table = $corbeille_param[$type_doc]["table"];
	$index = $corbeille_param[$type_doc]["id"];
	$statut = $corbeille_param[$type_doc]["statut"];
	$titre = $corbeille_param[$type_doc]["titre"];
	$table_liee = $corbeille_param[$type_doc]["tableliee"];
	 
	//compte le nb total d'objet supprimable 
	$total=Corbeille_compte_elements_vider($type_doc);
	
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
 
/**
 *Corbeille_icone_poubelle() affiche l'icone poubelle (vide ou pleine)
 * @param $total_table nb d'eléments supprimable pour un objet donné
 */
function Corbeille_icone_poubelle($total_table) {
	if (empty($total_table)) {
		return "<img src='"._DIR_PLUGIN_CORBEILLE."/img_pack/trash-empty-24.png' alt='trash empty'/>";
	} else {
		return "<img src='"._DIR_PLUGIN_CORBEILLE."/img_pack/trash-full-24.png'  alt='trash full'/>";
	}
}

/**
 *Corbeille_compte_elements_vider() compte le nombre d'element supprimable pour un objet donné
 * @param $type_doc 
 * @return $total , nb d'élements supprimable  
*/
function Corbeille_compte_elements_vider($type_doc) {
	global $corbeille_param;
	$table = $corbeille_param[$type_doc]["table"];
	$statut = $corbeille_param[$type_doc]["statut"];
	
    $req_corbeille = "select COUNT(*) as nbElt from $table WHERE statut like '$statut'";
    $result_corbeille = spip_query($req_corbeille);
    
    $total1 = 0;
    if ($row = spip_fetch_array($result_corbeille)) $total = $row['nbElt'];
	return ($total);
}
  
/**
 *Corbeille_affiche_ligne() affiche une ligne par objet dans le menu de gauche
 *@param $titre libelle à afficher dans le menu
 *@param $url url de la page de gestion de l'objet
 *@param $total_table nb d'element supprimable
 *
 *@return neant    
 */ 
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
/**Corbeille_affiche() affiche la page d'administration 
 *@param $page flux html de la page d'administration
 *@return neant
 */  
function Corbeille_affiche($page){
  		//charge les paramétres
		global $corbeille_param;
		//initialise les variables
		$totaux = array();
			
		//Calcul du nombre d'objets effa√ßable
		foreach($corbeille_param as $type_doc => $objet) {
			$totaux[$type_doc] = Corbeille_compte_elements_vider($type_doc);
			$totaux["tout"] += $totaux[$type_doc];
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


/**  semble non utilisé jusqu'à present */
// recupere les details du forum
function recupere_forum_detail($id_document){
  $str = "";	
	$req="SELECT id_forum, date_heure, titre, texte, auteur, email_auteur FROM spip_forum WHERE id_forum=$id_document";
	$result = spip_query($req);
	$row=spip_fetch_array($result);
	
	$str = _T("date_fmt_nomjour_date",array("date"=>affdate($row['date_heure']),"nomjour"=>'')) . ", ";
	if (! empty($row['email_auteur'])) $str .= "<a href=\"mailto:" . $row['email_auteur'] . "\">";
	$str .= $row['auteur'];
	if (! empty($row['email_auteur'])) 	$str .= "</a>";
	$str .= _T("corbeille:ecrit")."<br /><br /><strong>";
  $str .= $row['titre'] . "</strong><br /><br /><p align=justify>" . $row['texte'] . "</p>";
	
  return $str;	
}

//
// recupe les details d'une petition
function recupere_signature_detail($id_document){
  $str = "";
	$req="SELECT id_article, date_time, ad_email, nom_site, nom_email  FROM spip_signatures WHERE id_signature=$id_document";
	$result = spip_query($req);
	$row=spip_fetch_array($result);
	
	$str = "<strong>" . _T("date_fmt_nomjour_date",array("date"=>affdate($row['date_time']),"nomjour"=>'')). "</strong>,<br />";
	if (! empty($row['nom_email'])) $str .= "<a href=\"mailto:" . $row['nom_email'] . "\">";
	$str .= $row['nom_email'];
	if (! empty($row['nom_email'])) $str .= "</a>";
	$str .= _T("corbeille:signe")." <strong>";
	$str .= $row['ad_email'] . "</strong><br />";
	$str .= _T("corbeille:petition")." <strong>";
	$row2=spip_fetch_array(spip_query("SELECT * FROM spip_articles WHERE id_article=$row[0]"));
	$str .= $row2['ad_email'] . "<strong> : " . $row2['nom_email'] . "</strong><br />";

  return $str;
}


?>