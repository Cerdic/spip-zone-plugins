<?php

/*
 * Recherche entendue
 * plug-in d'outils pour la recherche et l'indexation
 * Panneaux de controle admin_index et index_tous
 * Boucle INDEX
 * filtre google_like
 *
 *
 * Auteur :
 * cedric.morin@yterium.com
 * pdepaepe et Nicolas Steinmetz pour google_like
 * fil pour le panneau admin_index d'origine
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/indexation');
include_spip("inc/logos");
include_spip("inc/presentation");
include_spip('inc/indexation_etendue');

function exec_index_tous_dist()
{
	global $connect_statut;
	if (version_compare($GLOBALS['spip_version_code'],'1.92','<'))
		RechercheEtendue_verifier_base();
	$INDEX_elements_objet;

	$INDEX_elements_objet = array();
	if (isset($GLOBALS['meta']['INDEX_elements_objet']))
		$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);

	$liste_tables = array();
	$icone_table=array('spip_articles'=>'article-24.gif','spip_auteurs'=>'auteur-24.gif','spip_breves'=>'breve-24.gif','spip_documents'=>'document-24.gif','spip_rubriques'=>'rubrique-24.gif','spip_forum'=>'forum-public-24.gif','spip_syndic'=>'site-24.gif','spip_documents'=>'doc-24.gif','spip_mots'=>'mot-cle-24.gif','spip_signatures'=>'suivi-petition-24.gif');

	$liste_tables = liste_index_tables();
	asort($liste_tables);

	foreach($liste_tables as $table){
		if (!isset($icone_table[$table]))
			$icone_table[$table] = "tout-site-24.gif";
 	}

	if (isset($_REQUEST['index_table'])) $index_table = $_REQUEST['index_table'];
	if (!isset($index_table)||(in_array($index_table,$liste_tables)==FALSE))
		$index_table='';

	if (isset($_REQUEST['filtre'])) $filtre = $_REQUEST['filtre'];
	if ( (!isset($filtre))||($filtre!=intval($filtre)) )
		$filtre = 10; // nombre d'occurences mini pour l'affichage des mots


	//
	// Recupere les donnees
	//

	debut_page(_T('rechercheetendue:moteur_recherche'), "administration", "cache");

	debut_gauche();


	//////////////////////////////////////////////////////
	// Boite "voir en ligne"
	//

	debut_boite_info();

	echo propre(_T('rechercheetendue:info_index_tous'));

	fin_boite_info();

	debut_raccourcis();
	echo "<p>";
	icone_horizontale (_T('rechercheetendue:indexation_statut'), generer_url_ecrire("admin_index"), "../"._DIR_PLUGIN_INDEXATION."/img_pack/stock_index.gif");
	echo "</p>";

	icone_horizontale (_T('rechercheetendue:tout'), generer_url_ecrire("index_tous"), "tout-site-24.gif");

	foreach($liste_tables as $t){
		if (isset($INDEX_elements_objet[$t])){
			icone_horizontale (_T('rechercheetendue:index',array('table' => $t)), generer_url_ecrire("index_tous","index_table=$t&filtre=$filtre"), $icone_table[$t]);
		}
	}

	echo generer_url_post_ecrire("index_tous",$index_table?"index_table=$index_table":"");

	echo _T('rechercheetendue:filtrer') . "<br /><select name='filtre'";
	echo "onchange=\"document.location.href='";
	echo generer_url_ecrire('index_tous',($index_table?"index_table=$index_table&":"").'filtre=')."'+this.options[this.selectedIndex].value\"";
	echo ">" . "\n";
	$filtres=array('1'=>_T('rechercheetendue:filtrer_plus_1'),'10'=>_T('rechercheetendue:filtrer_plus_10'),'100'=>_T('rechercheetendue:filtrer_plus_100'));
	foreach($filtres as $val=>$string){
		echo "<option value='$val'";
		if ($val == $filtre)
		  echo " selected='selected'";
		echo ">" . $string ."</option>\n";
	}
	echo "</select>";
	echo "<noscript><div>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
	echo "</div></noscript></div>\n";
	echo "</form></div>\n";

	fin_raccourcis();

	debut_droite();

	gros_titre(_T('rechercheetendue:moteur_recherche'));

	if ($connect_statut != '0minirezo') {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}

	if ($index_table==''){
		$titre_table=_T("rechercheetendue:tous_mots");
		$icone = "doc-24.gif";
	}
	else {
		$titre_table=_T("rechercheetendue:tous_mots_table",array('table'=>$index_table));
		$icone = $icone_table[$index_table];
	}


		// recupere les types
		$liste_tables = array_flip($liste_tables);
		$tableau = array();
		$classement = array();

		
		$vers = spip_fetch_array(spip_query("SELECT VERSION()"));
		if (substr($vers[0], 0, 1) >= 4
		AND substr($vers[0], 2, 1) >= 1 ) {
			$hex_fmt = '';
			$select_hash = 'dic.hash AS h';
		} else {
			$hex_fmt = '0x';
			$select_hash = 'HEX(dic.hash) AS h';
		}
		
		$requete = array(
			'SELECT' => "dic.dico,$select_hash,COUNT(objet.points) AS occurences,SUM(objet.points) AS total",
			'FROM' => 'spip_index_dico AS dic, spip_index AS objet',
			'JOIN' => "",
			'WHERE' => "dic.hash=objet.hash",
			'ORDER BY' => "total DESC",
			'GROUP BY' => "dic.hash",
			'HAVING' => "total>=$filtre");

		if ($index_table!=''){
			$id_table = $liste_tables[$index_table];
			$requete['WHERE'] .= " AND objet.id_table=$id_table";
	 	}
		$select = $requete['SELECT'] ? $requete['SELECT'] : '*';
		$from = $requete['FROM'];
		$join = $requete['JOIN'] ? (' LEFT JOIN ' . $requete['JOIN']) : '';
		$where = $requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '';
		$order = $requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : '';
		$group = $requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '';
		$limit = $requete['LIMIT'] ? (' LIMIT ' . $requete['LIMIT']) : '';
		$having = $requete['HAVING'] ? (' HAVING ' . $requete['HAVING']) : '';

		$tmp_var = "debut";
		$cpt = spip_num_rows(spip_query("SELECT SUM(objet.points) AS total FROM $from$join$where$group$having"));
		if ($requete['LIMIT']) $cpt = min($requete['LIMIT'], $cpt);
	
		$deb_aff = intval(_request('t_' .$tmp_var));
		$nb_aff = 60;
		if ($cpt > 1.5*$nb_aff) {
			$tranches = afficher_tranches_requete($cpt, 3, $tmp_var, '', $nb_aff);
		}
	 	
		if ($cpt) {
			if ($titre_table) echo "<div style='height: 12px;'></div>";
			echo "<div class='liste'>";
			bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
			echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";
			echo $tranches;

		 	$result = spip_query("SELECT $select FROM $from$join$where$group$order LIMIT $deb_aff,$nb_aff");
			$num_rows = spip_num_rows($result);

			$ifond = 0;
			$premier = true;

			$compteur_liste = 0;
			$vals = '';
			while ($row = spip_fetch_array($result)) {
				$compteur_liste ++;

				$dico = $row['dico'];
				$hash = $hex_fmt.$row['h'];
				$points = $row['total'];
				$occurences = $row['occurences'];

				// le tableau

				// puce et titre
				$s = "";
				if ($occurences) {
					$puce = 'puce-verte-breve.gif';
				}
				else {
					$puce = 'puce-orange-breve.gif';
				}
				$vals[] = "<img src='img_pack/"
				  . $puce 
				  . "' width='7' height='7' style='border:0px;' />["
				  . $points
				  . "] <a href='" 
				  . generer_url_ecrire("recherche", "recherche=" . urlencode($dico))
				  .  "' title='"
				  . $occurences
				  . " "._T("rechercheetendue:occurences")."'>"
				  .  $dico
				  . "</a>&nbsp;";

				if (fmod($compteur_liste,3)==0){
					$tableau[] = $vals;
					$vals = '';
				}
			}
			spip_free_result($result);
			$largeurs = array('','','');
			$styles = array('arial11', 'arial11', 'arial11');
			echo afficher_liste($largeurs, $tableau, $styles);
			echo "</table>";
			echo "</div>\n";
		}
		else echo _T("rechercheetendue:filtrer_aucun",array('points'=>$filtre));

	fin_page();

}
?>
