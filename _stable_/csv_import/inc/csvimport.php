<?php
/*
 * csvimport
 * plug-in d'import csv dans les tables spip
 *
 * Auteur :
 * Cedric MORIN
 * notre-ville.net
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

if (!defined('_DIR_PLUGIN_CSVIMPORT')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_CSVIMPORT',(_DIR_PLUGINS.end($p))."/");
}

include_spip("base/db_mysql");
include_spip("base/abstract_sql");
include_spip("inc/charsets");

function acces_interdit() {
	debut_page(_T('avis_acces_interdit'), "documents", "cvsimport");
	debut_gauche();
	debut_droite();
	echo "<strong>"._T('avis_acces_interdit')."</strong>";
	fin_page();
	exit;
}

function csvimport_tables_auth(){
	if (isset($GLOBALS['meta']['csvimport_tables_auth']))
		return unserialize($GLOBALS['meta']['csvimport_tables_auth']);
	else 
		return array();
}

function csvimport_table_importable($nom_table,&$titre,&$operations){
	$csvimport_tables_auth = csvimport_tables_auth();
	global $connect_statut;
	$declared = false;
	foreach($csvimport_tables_auth as $table=>$infos){
	  if (strcmp($table,$nom_table)==0){
	    $declared = true;
	    if (isset($infos['statut'])){
				if (!in_array($connect_statut,$infos['statut']))
	    		$declared = false;
			}
			if ($declared){
				if (isset($infos['titre']))
					$titre .= $infos['titre'];
				if (isset($infos['operations']))
				  foreach($infos['operations'] as $op)
				    $operations[]=$op;
			}
		}
	}
	return $declared;
}

function csvimport_afficher_tables($titre_table, $icone = '') {
	$csvimport_tables_auth = csvimport_tables_auth();
	global $connect_statut;
	global $couleur_claire, $couleur_foncee;
	global $connect_id_auteur;

	if (!$icone) $icone = "../"._DIR_PLUGIN_CSVIMPORT."img_pack/csvimport-24.png";

	if (count($csvimport_tables_auth)) {
		if ($titre_table) echo "<div style='height: 12px;'></div>";
		echo "<div class='liste'>";
		echo bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black",false);
		echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";

		$num_rows = count($csvimport_tables_auth);

		$ifond = 0;
		$premier = true;

		$compteur_liste = 0;
		foreach($csvimport_tables_auth as $latable=>$info) {
	    $declared = true;
	    if (isset($info['statut'])){
				if (!in_array($connect_statut,$info['statut']))
	    		$declared = false;
			}
			if ($declared) {

				$maj_exist = true;
				$query="SELECT maj FROM $latable ORDER BY maj DESC";
		 		$result = spip_query($query);
		 		if (!$result) {
					$query="SELECT * FROM $latable";
			 		$result = spip_query($query);
			 		$maj_exist = false;
		 		}

		 		$nb_data=spip_num_rows($result);
		 		$last_mod='';
		 		if ($maj_exist){
					$row = spip_fetch_array($result);
					$last_mod = $row['maj'];
			 	}

				$vals = '';
				$titre = $latable;
				if (isset($info['titre']))
					$titre = $info['titre'];

				$importable = false;
				$exportable = false;
				if (isset($info['operations'])){
				  if ((in_array('add',$info['operations']))||(in_array('replaceall',$info['operations'])))
						$importable = true;
				  if (in_array('export',$info['operations']))
						$exportable = true;
				}

				$link = generer_url_ecrire("csvimport_import","table=$latable&retour=".urlencode(self()));
				if ($nb_data) {
					$puce = 'puce-verte-breve.gif';
				}
				else {
					$puce = 'puce-orange-breve.gif';
				}

				$s = "";
				if ($importable)
					$s .= "<a href='$link'>";
				$s .= "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' border='0'>&nbsp;&nbsp;";
				$s .= strlen($titre)?typo($titre):$latable;
				if ($importable)
					$s .= "</a>";
				$s .= " &nbsp;&nbsp;";
				$vals[] = $s;

				$s = "";
				if ($nb_data) {
					$s .= $nb_data . " " . _L("enregistrements");
					if ($last_mod)
					  $s .= " (" . $last_mod . ")";
				}
				$vals[] = $s;

				$s = "";
				if ($exportable){
					$link = generer_url_ecrire("csvimport_telecharger","table=$latable&retour=".urlencode(self()));
					$s .= "<a href='$link'>";
					$s .= _L("T&eacute;l&eacute;charger");
					$s .= "</a>";
				}
				$vals[] = $s;

				$table[] = $vals;
			}
		}

		$largeurs = array('','','');
		$styles = array('arial11', 'arial1', 'arial1');
		echo afficher_liste($largeurs, $table, $styles);
		echo "</table>";
		echo "</div>\n";
	}
	$out = "<br />";
	if (defined('_DIR_PLUGIN_FORMS')&&($GLOBALS['meta']['forms_base_version']>0.17)){
		if (include_spip('inc/forms_tables_affichage'))
			$out .= afficher_tables_tous_corps('table');
		else {
			include_spip('public/assembler');
			$contexte = array('type_form'=>'table','titre_liste'=>_T("forms:toutes_tables"),'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
			$out .= recuperer_fond("exec/template/tables_import_tous",$contexte);
		}
		echo $out;
	}
	
	if (!count($csvimport_tables_auth) && !$out) {
		echo _L("Pas de tables d&eacute;clar&eacute;es pour l'import CSV");
 	}
}

function csvimport_csv_champ($champ) {
	$champ = preg_replace(',[\s]+,', ' ', $champ);
	$champ = str_replace(',",', '""', $champ);
	return '"'.$champ.'"';
}

function csvimport_csv_ligne($ligne, $delim = ',') {
	return join($delim, array_map('csvimport_csv_champ', $ligne))."\r\n";
}

/**
 * Based on an example by ramdac at ramdac dot org
 * Returns a multi-dimensional array from a CSV file optionally using the
 * first row as a header to create the underlying data as associative arrays.
 * @param string $file Filepath including filename
 * @param bool $head Use first row as header.
 * @param string $delim Specify a delimiter other than a comma.
 * @param int $len Line length to be passed to fgetcsv
 * @return array or false on failure to retrieve any rows.
 */

function csvimport_importcharset($texte){
	return importer_charset($texte,'iso-8859-1');
}

function csvimport_importcsv($file, $head = 0, $delim = ",", $enclos = '"', $len = 10000) {
	$return = false;
	$handle = fopen($file, "r");
	if ($handle){
		if ($head) {
			$header = fgetcsv($handle, $len, $delim);
			if ($header){
				$header = array_map('csvimport_importcharset',$header);
				$header = array_map('csvimport_nettoie_key',$header);
			}
		}
		while (($data = fgetcsv($handle, $len, $delim)) !== FALSE) {
			$data = array_map('csvimport_importcharset',$data);
			if ($head AND isset($header)) {
				foreach ($header as $key=>$heading) {
					$row[$heading]=(isset($data[$key])) ? $data[$key] : '';
				}
				$return[]=$row;
			} else {
				$return[]=$data;
			}
		}
		fclose($handle);
	}
	return $return;
}

function csvimport_show_erreurs($erreur){
	$output = "";
	if (count($erreur)>0){
		$output .= "<div class='messages'>";
		foreach($erreur as $steper=>$desc)
			foreach($desc as $key=>$val)
				$output .=  "<strong>$steper::$key:$val</strong><br />";
		$output .=  "</div>\n";
	}
	return $output;
}

function csvimport_table_visu_extrait($nom_table,$nombre_lignes = 0){
	$maj_exist = true;
	$limit = "";
	if ($nombre_lignes > 0)
	  $limit = " LIMIT " . ($nombre_lignes+1);
	$query="SELECT * FROM $nom_table ORDER BY maj DESC" . $limit;
	$result = spip_query($query);
	if (!$result) {
		$query="SELECT * FROM $nom_table $limit";
 		$result = spip_query($query);
 		$maj_exist = false;
	}

	$nb_data=spip_num_rows($result);
	if ($nombre_lignes==0)
		$nombre_lignes = $nb_data;
	$data_count = 0;
	$head_set = false;
	$nb_col = 0;
	echo "<table>";
	while (($row = spip_fetch_array($result,SPIP_ASSOC))&&($data_count++<$nombre_lignes)){
		if (!$head_set){
			echo "<tr>";
			foreach($row as $key=>$value){
			  echo "<th>" . $key . "</th>";
			  $nb_col++;
			}
			echo "</tr>\n";
			$head_set = true;
		}
		echo "<tr>";
		foreach($row as $key=>$value)
		  echo "<td>" . $value . "</td>";
		echo "</tr>\n";
	}
	if ($nb_data>$nombre_lignes){
		$query="SELECT COUNT(*) FROM $nom_table";
		list($num_rows) = spip_fetch_array(spip_query($query));
		echo "<tr><td colspan='$nb_col' style='border-top:1px dotted;'>$num_rows "._L("lignes")." ...</td></tr>\n";
	}
	echo "</table>\n";
	if ($data_count==0)
	  echo _L("Table vide");
}

function csvimport_array_visu_extrait($data, $head, $nombre_lignes = 0){
	$output = "";
	$data_count = 0;
	$head_set = false;
	$nb_col = 0;
	if ($data!=false){
		$output .= "<table>";
		foreach($data as $key=>$ligne) {
			if (($head==true)&&($head_set==false)){
				$output .= "<tr>";
				foreach($ligne as $key=>$value){
				  $output .= "<th>" . $key . "</th>";
				  $nb_col++;
				}
				$output .= "</tr>\n";
				$head_set = true;
			}
			else{
				$output .= "<tr>";
				foreach($ligne as $value){
				  $output .= "<td>" . $value . "</td>";
				}
				$output .= "</tr>\n";
			}
			if (($nombre_lignes>0)&&($data_count++>=$nombre_lignes))
			  break;
		}
		$output .= "</table>\n";
	}
	if ($data_count==0)
	  $output .= _L("Pas de donn&eacute;e");
	else
	  $output .= count($data) . _L(" lignes au total");
	return $output;
}

function csvimport_array_visu_assoc($data, $table_fields, $assoc_field, $nombre_lignes = 0){
	$assoc=array_flip($assoc_field);

	$output = "";
	$data_count = 0;
	$output .= "<table>";
	$output .= "<tr>";
	foreach($table_fields as $key=>$value){
	  $output .= "<th>" . $value . "</th>";
	}
	$output .= "</tr>\n";

	if ($data!=false){
		foreach($data as $key=>$ligne) {
			$output .= "<tr>";
			foreach($table_fields as $key=>$value){
				$kc = csvimport_nettoie_key($key);
			  $output .= "<td>";
			  if ((isset($assoc[$kc]))&&(isset($ligne[$assoc[$kc]])))
			    $output .= $ligne[$assoc[$kc]];
				else
					$output .= "&nbsp;";
				$output .= "</td>";
			}
			$output .= "</tr>\n";
			if (($nombre_lignes>0)&&(++$data_count>=$nombre_lignes))
			  break;
		}
	}
	$output .= "</table>";

	if ($data_count>0)
	  $output .= count($data) . _L(" lignes au total");
	return $output;
}

function csvimport_nettoie_key($key){
	$accents=array('é','è','ê','à','ù',"ô","ç","'");
	$accents_rep=array('e','e','e','a','u',"o","c","_");
	return str_replace($accents,$accents_rep,$key);
}

function csvimport_field_associate($data, $table_fields, $assoc_field){
	global $tables_principales;
	$assoc=$assoc_field;
	if (!is_array($assoc)) $assoc = array();
	$csvfield=array_keys($data{1});
	foreach($csvfield as $k=>$v){
		$csvfield[$k] = csvimport_nettoie_key($v);
	}
	$csvfield=array_flip($csvfield);

	// on enleve toutes les associations dont
	// la cle n'est pas un csvfield
	// la valeur n'est pas un tablefield
	// l'un des deux est deja affecte
	foreach ($assoc as $key=>$value){
		$good_key = false;
		$good_value = false;
		if (isset($csvfield[$key])){
		  $good_key = true;
		}
		if ((isset($table_fields[$value]))||($value==-1)){
		  $good_value = true;
		}
		if (($good_key==false)||($good_value==false))
		  unset($assoc[$key]);
		else{
			unset($csvfield[$key]);
			if ($value!=-1) unset($table_fields[$value]);
		}
	}

	//assoc auto des cles qui portent le meme nom
	foreach(array_keys($csvfield) as $csvkey){
		foreach(array_keys($table_fields) as $tablekey)
		  if (strcasecmp($csvkey,$tablekey)==0){
				$assoc[$csvkey]=$tablekey;
				unset($csvfield[$csvkey]);
				unset($table_fields[$tablekey]);
			}
 	}
	//assoc des autres dans l'ordre qui vient
	$table_fields=array_keys($table_fields);
	foreach(array_keys($csvfield) as $csvkey){
		$assoc[$csvkey]=array_shift($table_fields);
		if ($assoc[$csvkey]==NULL) $assoc[$csvkey]="-1";
		unset($csvfield[$csvkey]);
	}
	return $assoc;
}

function csvimport_field_configure($data, $table_fields, $assoc){
	$output = "";
	$csvfield=array_keys($data{1});

	$output .= "<table><tr><td>"._L("Champ CSV")."</td><td>"._L("Champ Table")."</td></tr>";
	foreach($csvfield as $csvkey){
		$csvkey = csvimport_nettoie_key($csvkey);
		$output .=  "<tr>";
		$output .=  "<td>$csvkey</td>";
		$output .= "<td><select name='assoc_field[$csvkey]'>\n";
		$output .= "<option value='-1'>"._L("Ne pas importer")."</option>\n";
		foreach($table_fields as $tablekey => $libelle){
			$output .= "<option value='$tablekey'";
			if ($assoc[$csvkey]==$tablekey)
			  $output .= " selected='selected'";
			$output .= ">$libelle</option>\n";
		}
		$output .= "</select></td></tr>";
	}
	$output .= "</table>";
	return $output;
}

// vidange de la table
function csvimport_vidange_table($table){
	$res = spip_query("DELETE FROM $table"); // et voila ...
}

function csvimport_ajoute_table_csv($data, $table, $assoc_field, &$erreur){
	global $tables_principales;
	$csvimport_tables_auth = csvimport_tables_auth();
	$assoc = array_flip($assoc_field);
	$desc = spip_abstract_showtable($table);
	if (!isset($desc['field']) || count($desc['field'])==0){
		$erreur[0][] = "Description de la table introuvable";
		return;
	}
	if ($GLOBALS['mysql_rappel_nom_base'] AND $db = $GLOBALS['spip_mysql_db'])
		$table = '`'.$db.'`.'.$table;

	
	$tablefield=array_keys($desc['field']);
	$output = "";
	// y a-t-il une cle primaire ?
	if (isset($desc['key']["PRIMARY KEY"])){
		$primaire = $desc['key']["PRIMARY KEY"];
		// la cle primaire est-elle importee ?
		if (in_array($primaire,$assoc_field))
		  unset($primaire);
 	}
	// y a-t-il un champ TIMESTAMP ?
	$test=array_flip($desc['field']);
	if (isset($test['TIMESTAMP']))
	  $stamp = $test['TIMESTAMP'];

	if ($data!=false){
		$count_lignes = 0;
		foreach($data as $key=>$ligne) {
      $count_lignes ++;
			// creation de la cle primaire puis modif de l'enregistrement
			//if (isset($primaire)){
				$what = "(";
				$with = "(";
				$check = array_flip($tablefield);
				foreach($check as $key=>$value){
					$kc = csvimport_nettoie_key($key);
				  if ((isset($assoc[$kc]))&&(isset($ligne[$assoc[$kc]]))){
						$what .= "$key,";
						$with .= "'" . addslashes($ligne[$assoc[$kc]]) . "',";
						unset($check[$key]);
					}
		 		}
				if ((isset($stamp))&&isset($check[$stamp])){
					$what .= "$stamp,";
					$with .= "NOW(),";
				}
				if ((strlen($what)>1)&&(strlen($with)>1)) {
					$what = substr($what,0,strlen($what)-1) . ")";
					$with = substr($with,0,strlen($with)-1) . ")";
					$id_primary = spip_abstract_insert($table, $what, $with);
					if ($id_primary==0)
					  $erreur[$count_lignes][] = "ajout impossible ::$what::$with::<br />";
				}
				else
				  $erreur[$count_lignes][] = "rien &agrave; ajouter<br />";
		 	//}
			// creation de l'enregistrement direct
		 	/*else {

			}*/
		}
	}
}

?>
