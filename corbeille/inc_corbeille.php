<?php

/**
 * definition du plugin "corbeille" version "classe statique"
 * utilisee comme espace de nommage
 */
class Corbeille {
	/* static public */

	/* public static */
	function ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['configuration']->sousmenu['corbeille']= new Bouton(
			"../"._DIR_PLUGIN_CORBEILLE."/trash-full-24.png",  // icone
			_L('Corbeille')	// titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function ajouterOnglets($onglets, $rubrique) {
		return $onglets;
	}

	function effacement($table, $statut, $titre, $id) {
		$total=compte_elements_vider($table, $statut, $titre); 
		if ($total == 0) {
			echo "$table vide <br />";
		} else {
			for ($i = 0; $i < count($total); $i++) {
				$req_corbeille = "select COUNT(*) AS total from $table WHERE statut like '$statut'";
				$result_corbeille = spip_query($req_corbeille);
				$row = spip_fetch_array($result_corbeille);
				$total = $row['total'];
				$req = "DELETE FROM $table WHERE statut='$statut'";
				$result = spip_query($req);
				if (! $result) { echo " : erreur !"; }
				echo "$table : $total <br/>\n";
			}
		}
		return $total;
	}

	function icone_poubelle($total_table) {
		if (empty($total_table)) {
			echo"<img src='"._DIR_PLUGIN_CORBEILLE."/trash-empty.png' height='30' width='30' style='border:0px' />"; 
		} else { 
			echo"<img src='"._DIR_PLUGIN_CORBEILLE."/trash-full.png' height='30' width='30' style='border:0px' />"; 
		} 
	}

	function compte_elements_vider($table, $statut, $titre) {
	                        $req_corbeille = "select COUNT(*) from $table WHERE statut like '$statut'";
	                        $result_corbeille = spip_query($req_corbeille);
	                        $total1 = 0;
	                        if ($row = spip_fetch_array($result_corbeille)) $total = $row[0];
		return ($total);
	}


	function affiche_ligne($titre,$url,$total_table,$ifond){
	  global $couleur_claire;
		if ($ifond==0){
			$ifond=1;
			$couleur="$couleur_claire";
		}else{
			$ifond=0;
			$couleur="#FFFFFF";
		}
		echo "<tr style='background-color:$couleur;'>";
		echo "<td >";
		echo "<span style='font:arial,helvetica,sans-serif;font-size:small;'>";
		echo $titre;
		echo "</span><td style='width:50;'>";
		if ($total_table>0) echo "<a href='$url'>";
		Corbeille::icone_poubelle($total_table);
		if ($total_table>0) echo "</a>";
		echo "</td><td>";
		echo "<span style='font:arial,helvetica,sans-serif;font-size:small;'>";
		echo $total_table;
		echo "</span>";
		echo "</td></tr>\n";
		return $ifond;
	}

	function affiche($page){
		//case "signatures" :
		$statut = "poubelle"; $titre = "nom_email"; $table = "spip_signatures"; $id = "id_signature"; $temps = "date_time";
		$total_signatures = Corbeille::compte_elements_vider($table, $statut, $titre);
		//case "breves" :
		$statut = "refuse"; $table = "spip_breves"; $id = "id_breve"; $temps = "date_heure";
		$total_breves = Corbeille::compte_elements_vider($table, $statut, $titre);
		//case "articles" :
		$statut = "poubelle"; $table = "spip_articles"; $id = "id_article"; $temps = "date";
		$total_articles = Corbeille::compte_elements_vider($table, $statut, $titre);
		//case "forums_publics" :
		$statut = "off"; $table = "spip_forum"; $id = "id_forum"; $temps = "date_heure";
		$total_forums_publics = Corbeille::compte_elements_vider($table, $statut, $titre);
		//case "forums_prives" :
		$statut = "privoff"; $table = "spip_forum"; $id = "id_forum"; $temps = "date_heure";
		$total_forums_prives = Corbeille::compte_elements_vider($table, $statut, $titre);
		//case "auteurs" :
		$statut = "5poubelle"; $titre = "nom"; $table="spip_auteurs"; $id="id_auteur"; $temps = "maj";
		$total_auteur = Corbeille::compte_elements_vider($table, $statut, $titre);
		$totaux = ($total_auteur + $total_forums_prives + $total_forums_publics + $total_articles + $total_breves + $total_signatures); 
	
		//types de documents geres par la corbeille
		echo _L("Choisissez le type de documents à afficher :<br/>");
		echo "<table style='width:100%'>";
		$ifond=0;
	
		$ifond = Corbeille::affiche_ligne(_L('P&eacute;titions'),generer_url_ecrire($page,"type_doc=signatures"),$total_signatures,$ifond);
		$ifond = Corbeille::affiche_ligne(_L('Br&egrave;ves'),generer_url_ecrire($page,"type_doc=breves"),$total_breves,$ifond);
		$ifond = Corbeille::affiche_ligne(_L('Articles'),generer_url_ecrire($page,"type_doc=articles"),$total_articles,$ifond);
		$ifond = Corbeille::affiche_ligne(_L('Forums Publics'),generer_url_ecrire($page,"type_doc=forums_publics"),$total_forums_publics,$ifond);
		$ifond = Corbeille::affiche_ligne(_L('Forums Priv&eacute;s'),generer_url_ecrire($page,"type_doc=forums_prives"),$total_forums_prives,$ifond);
		$ifond = Corbeille::affiche_ligne(_L('Auteurs'),generer_url_ecrire($page,"type_doc=auteurs"),$total_auteur,$ifond);
		$ifond = Corbeille::affiche_ligne(_L('Tout'),generer_url_ecrire($page,"type_act=tout"),$totaux,$ifond);
		echo "</table><br/>";
	}
}

?>