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
			_L('Corbeille')	// titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function Corbeille_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}

	function Corbeille_effacement($table, $statut, $titre, $id) {
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
	                        if ($row = spip_fetch_array($result_corbeille)) $total = $row[0];
		return ($total);
	}
  
  // affiche ligne
  function Corbeille_affiche_ligne($titre,$url,$total_table){
		echo "<div class='verdana2' style='width:100%;padding:5px;'>\n";		
		if ($total_table>0)  echo "<a href='$url' class='corbeille'>";
    echo Corbeille_icone_poubelle($total_table);    
		echo " $total_table $titre";
		if ($total_table>0)  echo "</a>";
    echo "</div>\n";
	}

	function Corbeille_affiche($page){
		//case "signatures" :
		$statut = "poubelle"; $titre = "nom_email"; $table = "spip_signatures"; $id = "id_signature"; $temps = "date_time";
		$total_signatures = Corbeille_compte_elements_vider($table, $statut, $titre);
		//case "breves" :
		$statut = "refuse"; $table = "spip_breves"; $id = "id_breve"; $temps = "date_heure";
		$total_breves = Corbeille_compte_elements_vider($table, $statut, $titre);
		//case "articles" :
		$statut = "poubelle"; $table = "spip_articles"; $id = "id_article"; $temps = "date";
		$total_articles = Corbeille_compte_elements_vider($table, $statut, $titre);
		//case "forums_publics" :
		$statut = "off"; $table = "spip_forum"; $id = "id_forum"; $temps = "date_heure";
		$total_forums_publics = Corbeille_compte_elements_vider($table, $statut, $titre);
		//case "forums_prives" :
		$statut = "privoff"; $table = "spip_forum"; $id = "id_forum"; $temps = "date_heure";
		$total_forums_prives = Corbeille_compte_elements_vider($table, $statut, $titre);
		//case "auteurs" :
		$statut = "5poubelle"; $titre = "nom"; $table="spip_auteurs"; $id="id_auteur"; $temps = "maj";
		$total_auteur = Corbeille_compte_elements_vider($table, $statut, $titre);
		$totaux = ($total_auteur + $total_forums_prives + $total_forums_publics + $total_articles + $total_breves + $total_signatures); 
	
		//types de documents geres par la corbeille
		echo "<strong>"._T('corbeille:choix_doc')."</strong><br/>";
		echo "<style type='text/css'>a.corbeille {border:3px solid #f00;padding: 5px;} a.corbeille:hover {background: #fcc;border:3px solid #c00;} </style>";
	
  	Corbeille_affiche_ligne(_L('P&eacute;titions'),generer_url_ecrire($page,"type_doc=signatures"),$total_signatures);
		Corbeille_affiche_ligne(_L('Br&egrave;ves'),generer_url_ecrire($page,"type_doc=breves"),$total_breves);
		Corbeille_affiche_ligne(_L('Articles'),generer_url_ecrire($page,"type_doc=articles"),$total_articles);
		Corbeille_affiche_ligne(_L('Forums Publics'),generer_url_ecrire($page,"type_doc=forums_publics"),$total_forums_publics);
		Corbeille_affiche_ligne(_L('Forums Priv&eacute;s'),generer_url_ecrire($page,"type_doc=forums_prives"),$total_forums_prives); // utile ? puisqu'on ne peut pas les supprimer
		Corbeille_affiche_ligne(_L('Auteurs'),generer_url_ecrire($page,"type_doc=auteurs"),$total_auteur);
		// Corbeille_affiche_ligne(_L('Tout'),generer_url_ecrire($page,"type_act=tout"),$totaux); FIXME: ne pas afficher la ligne "tout" car pas fonctionnel pour l'instant

	}
	
	


?>
