<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


//
if (!defined("_ECRIRE_INC_VERSION")) return;


$GLOBALS['version_archive'] = '1.3';
// par defaut tout est exporte
// possibiliter de limiter les tables via mes_options

// Conversion timestamp MySQL (format ascii) en Unix (format integer)
function mysql_timestamp_to_time($maj)
{
	$t_an = substr($maj, 0, 4);
	$t_mois = substr($maj, 4, 2);
	$t_jour = substr($maj, 6, 2);
	$t_h = substr($maj, 8, 2);
	$t_min = substr($maj, 10, 2);
	$t_sec = substr($maj, 12, 2);
	return mktime ($t_h, $t_min, $t_sec, $t_mois, $t_jour, $t_an, 0);
}

// Conversion texte -> xml (ajout d'entites)
function text_to_xml($string) {
	return str_replace('<', '&lt;', str_replace('&', '&amp;', $string));
}


function build_begin_tag($tag) {
	return "<$tag>";
}

function build_end_tag($tag) {
	return "</$tag>";
}

//
// Exportation generique d'objets (fichier ou retour de fonction)
//
function export_objets($table, $primary, $liens, $file = 0, $gz = false, $etape_actuelle="", $nom_etape="",$limit=0) {
	static $etape_affichee=array();
	static $table_fields=array();
	$string='';

	#lire_metas(); // un lire meta pour commencer car les ecrire_meta se font uniquement dans la table pour optimiser
	$status_dump = explode("::",$GLOBALS['meta']["status_dump"]);
	$etape_en_cours = $status_dump[2];
	$pos_in_table = $status_dump[3];
	
	if ($etape_en_cours < 1 OR $etape_en_cours == $etape_actuelle){

		$result = spip_query("SELECT COUNT(*) FROM $table");
		$row = spip_fetch_array($result,SPIP_NUM);
		$total = $row[0];
		$debut = $pos_in_table;
	  if (!isset($etape_affichee[$etape_actuelle])){
			echo "<li><strong>$etape_actuelle-$nom_etape</strong>";
			echo " : $total";
			$etape_affichee[$etape_actuelle] = 1;
			if ($limit<$total) echo "<br/>";
		}
		if ($pos_in_table!=0)
			echo "| $pos_in_table ";
		ob_flush();flush();

		if ($limit == 0) $limit=$total;
		$result = spip_query("SELECT * FROM $table LIMIT $debut,$limit");
		
		if (!isset($table_fields[$table])){
			$nfields = mysql_num_fields($result);
			// Recuperer les noms des champs
			for ($i = 0; $i < $nfields; ++$i) $table_fields[$table][$i] = mysql_field_name($result, $i);
		}
		else
			$nfields = count($table_fields[$table]);

		if (!$file) {
			while ($row = spip_fetch_array($result,SPIP_ASSOC)) {
				$string .= build_begin_tag($table) . "\n";
				// Exporter les champs de la table
				for ($i = 0; $i < $nfields; ++$i) {
					$string .= '<'.$table_fields[$table][$i].'>' . text_to_xml($row[$table_fields[$table][$i]]) . '</'.$table_fields[$table][$i].'>' . "\n";
				}
					
				$string .= build_end_tag($table) . "\n\n";
				$status_dump[3] = $pos_in_table = $pos_in_table +1;
			}
		}
		else {
			$_fputs = ($gz) ? gzputs : fputs;
			while ($row = spip_fetch_array($result,SPIP_ASSOC)) {
				$string .= build_begin_tag($table) . "\n";
				// Exporter les champs de la table
				for ($i = 0; $i < $nfields; ++$i) {
					$string .= '<'.$fields[$i].'>' . text_to_xml($row[$fields[$i]]) . '</'.$fields[$i].'>' . "\n";
				}
					
				$string .= build_end_tag($table) . "\n\n";
				$status_dump[3] = $pos_in_table = $pos_in_table +1;
	
				$_fputs($file, $string);
				fflush($file);
				// on se contente d'une ecriture en base pour aller plus vite
				// a la relecture on en profitera pour mettre le cache a jour
				ecrire_meta("status_dump", implode("::",$status_dump));
				#lire_metas();
				#ecrire_metas(); 
				$string = '';
			}
		}
		if ($pos_in_table>=$total){
			// etape suivante : 
			echo " ok";
			$status_dump[2] = $status_dump[2]+1;
			$status_dump[3] = 0;
		}
		if ($file) {
			// on se contente d'une ecriture en base pour aller plus vite
			// a la relecture on en profitera pour mettre le cache a jour
			ecrire_meta("status_dump", implode("::",$status_dump));
			#lire_metas();
			#ecrire_metas();
		}
		spip_free_result($result);
		return array($string,$status_dump);
	}
	else if ($etape_actuelle < $etape_en_cours) {
	  if (!isset($etape_affichee[$etape_actuelle]))
			echo "<li> $etape_actuelle-$nom_etape";
		ob_flush();flush();
	} else {
	  if (!isset($etape_affichee[$etape_actuelle]))
			echo "<li> <font color='#999999'>$etape_actuelle-$nom_etape</font>";
		ob_flush();flush();
	}
	return array($string,$status_dump);
}


// Liste un sommaire d'objets de n'importe quel type
// a la condition d'etre publics et plus recents que $maj
function liste_objets($result, $type, $maj) {

	$res = array();
	if ($result)
	  while ($row = spip_fetch_array($result)) {
		$t_id = $row["id_$type"];
		$t_statut = $row["statut"];
		$t_maj = mysql_timestamp_to_time($row["maj"]);
		if (!$maj ||
			($t_maj > $maj && 
			 (!$t_statut || $t_statut == "publie"))) {
		  echo "$type $t_id ", ($maj ? $t_maj : ""), "\n";
			if ($type == "article") $res[]=$t_id;
		}
	}
	spip_free_result($result);
	return $res;
}

// Liste un sommaire recursif de rubriques
// a condition que la mise a jour soit plus recente que $maj
function liste_rubriques($id_rubrique) {
	global $maj;
	static $rubriques = array();
	if ($id_rubrique)
		$result = spip_query("SELECT * FROM spip_rubriques WHERE id_rubrique='$id_rubrique'");
	else
		$result = spip_query("SELECT * FROM spip_rubriques WHERE id_parent=0");

	if ($result) while ($row=spip_fetch_array($result)) {
		$id_rubrique = $row['id_rubrique'];
		$id_parent = $row['id_parent'];
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$texte = $row['texte'];
		$rubrique_maj = mysql_timestamp_to_time($row["maj"]);
		if ($rubrique_maj > $maj) {
			echo "rubrique $id_rubrique $rubrique_maj\n";
		}
		$t_rubriques[] = $id_rubrique;
		$rubriques[] = $id_rubrique;
	}
	spip_free_result($result);
 	if ($t_rubriques) {
 		$t_rubriques = join(",", $t_rubriques);
		liste_rubriques(spip_query("SELECT * FROM spip_rubriques WHERE id_parent IN ($t_rubriques)"));

	}
	return $rubriques;
}

function exec_export_dist()
{

	global $id_rubrique, $maj;
	$id_rubrique = intval($id_rubrique);

	header("Content-Type: text/plain");

	$rubriques = liste_rubriques($id_rubriques);

	if ($rubriques) {
		$rubriques = join(",", $rubriques);

		$query = spip_query("SELECT id_article, statut, maj FROM spip_articles WHERE id_rubrique IN ($rubriques)");
		$articles = liste_objets($query, "article", $maj);

		$query = spip_query("SELECT id_breve, statut, maj FROM spip_breves WHERE id_rubrique IN ($rubriques)");
		liste_objets($query, "breve", $maj);

		if ($articles) {
			$articles = join(",", $articles);

			$query = spip_query("SELECT DISTINCT id_auteur FROM spip_auteurs_articles  WHERE id_article IN ($articles)");
			liste_objets($query, "auteur", 0);
		}
	}

}
?>
