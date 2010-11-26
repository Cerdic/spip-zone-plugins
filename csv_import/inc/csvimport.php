<?php
/*
 * CSVimport
 * Plug-in d'import csv dans les tables spip et d'export CSV des tables
 *
 * Auteur :
 * Cedric MORIN
 * notre-ville.net
 * © 2005,2009 - Distribue sous licence GNU/GPL
 *
 */

include_spip("base/abstract_sql");
include_spip("inc/charsets");
include_spip("inc/importer_csv");

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
        $vals = $table = array();

	if (!$icone) $icone = _DIR_PLUGIN_CSVIMPORT."img_pack/csvimport-24.png";

	if (count($csvimport_tables_auth)) {
		echo debut_cadre_enfonce($icone, true, '', $titre_table);
		
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
		 		$result = sql_select("maj",$latable,"","","maj DESC");
		 		if (!$result) {
			 		$result = sql_select("*",$latable);
			 		$maj_exist = false;
		 		}

		 		$nb_data=sql_count($result);
		 		$last_mod='';
		 		if ($maj_exist){
					$row = sql_fetch($result);
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
					$s .= _T("csvimport:nb_enregistrements",array('nb'=>$nb_data));
					if ($last_mod)
					  $s .= " (" . $last_mod . ")";
				}
				$vals[] = $s;

				$s = "";
				if ($exportable && $nb_data){
					$link = generer_url_ecrire("csvimport_telecharger","table=$latable&retour=".urlencode(self()));
					$s .= "<a href='$link'>";
					$s .= _T("bouton_download");
					$s .= "</a>";
				}
				$vals[] = $s;

				$table[] = $vals;
			}
		}

		$largeurs = array('','','');
		$styles = array('arial11', 'arial1', 'arial1');
		
		$liste = ''; 
		foreach ($table as $t) {
			reset($largeurs);
			if ($styles) reset($styles);
			$res ='';
			while (list(, $texte) = each($t)) {
				$style = $largeur = "";
				list(, $largeur) = each($largeurs);
				if ($styles) list(, $style) = each($styles);
				if (!trim($texte)) $texte .= "&nbsp;";
				$res .= "\n<td" .
					($largeur ? (" style=\'width: $largeur" ."px;\'") : '') .
					($style ? " class=\"$style\"" : '') .
					">" . lignes_longues($texte) . "\n</td>";
			}
		
			$liste .=  "\n<tr class='tr_liste'>$res</tr>"; 
		}
		
		echo $liste;
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
		echo _T("csvimport:aucune_table_declaree");
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

function csvimport_importcharset($texte){
	return importer_csv_importcharset($texte);
}

function csvimport_importcsv($file, $head = 0, $delim = ",", $enclos = '"', $len = 10000) {
	$importer_csv = charger_fonction('importer_csv','inc');
	return $importer_csv($file, $head, $delim, $enclos, $len);
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
		$limit = $nombre_lignes+1;
	$result = sql_select("*",$nom_table,"","","maj DESC",$limit);
	if (!$result) {
 		$result = sql_select("*",$nom_table,"","","",$limit);
 		$maj_exist = false;
	}
	$nb_data=sql_count($result);
	if ($nombre_lignes==0)
		$nombre_lignes = $nb_data;
	$data_count = 0;
	$head_set = false;
	$nb_col = 0;
	if($nb_data>0){
		$ret .= "<table width='100%'>";
		while (($row = sql_fetch($result))&&($data_count++<$nombre_lignes)){
			if (!$head_set){
				$ret .= "<tr>";
				foreach($row as $key=>$value){
					$ret .= "<th>" . $key . "</th>";
					$nb_col++;
				}
				$ret .= "</tr>\n";
				$head_set = true;
			}
			$ret .= "<tr>";
			foreach($row as $key=>$value)
				$ret .= "<td>" . $value . "</td>";
			$ret .= "</tr>\n";
		}
		if ($nb_data>$nombre_lignes){
			$num_rows = sql_count(sql_select("*",$nom_table));
			$ret .= "<tr><td colspan='$nb_col' style='border-top:1px dotted;'>"._T("csvimport:lignes_table",array('table'=>$nom_table,'nb_resultats'=>$num_rows))."</td></tr>\n";
		}
		$ret .= "</table>\n";
	}
	else
		$ret = "<p>"._T("csvimport:table_vide", array('table'=>$nom_table))."</p>";
	  
	return $ret;
}

function csvimport_array_visu_extrait($data, $head, $nombre_lignes = 0){
	$output = "";
	$data_count = 0;
	$head_set = false;
	$nb_col = 0;
	if ($data!=false){
		$output .= "<table width='100%'>";
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
		if ($data_count>0)
			$output .= '<tr><td style="border-top:1px dotted" colspan="'.$nb_col.'">'._T("csvimport:lignes_totales_csv",array("nb"=>count($data))).'</td></tr>';
		$output .= "</table>\n";
	}
	if ($data_count==0)
		$output .= _T("csvimport:aucune_donnee");
	return $output;
}

function csvimport_array_visu_assoc($data, $table_fields, $assoc_field, $nombre_lignes = 0){
	$assoc=array_flip($assoc_field);

	$output = "";
	$data_count = 0;
	$output .= "<table width='100%'>";
	$output .= "<tr>";
	foreach($table_fields as $key=>$value){
		$output .= "<th>" . $value . "</th>";
	}
	$output .= "</tr>\n";
	
	$nb_col = 0;
	if ($data!=false){
		foreach($data as $key=>$ligne) {
			$nb_col = 0;
			$output .= "<tr>";
			foreach($table_fields as $key=>$value){
				$nb_col++;
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
	if ($data_count>0)
		$output .= '<tr><td style="border-top:1px dotted" colspan="'.$nb_col.'">'._T("csvimport:lignes_totales_csv",array("nb"=>count($data))).'</td></tr>';
	$output .= "</table>";
	return $output;
}

function csvimport_nettoie_key($key){
	return importer_csv_nettoie_key($key);
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

	$output .= "<table><tr><td>"._T("csvimport:champs_csv")."</td><td>"._T("csvimport:champs_table")."</td></tr>";
	foreach($csvfield as $csvkey){
		$csvkey = csvimport_nettoie_key($csvkey);
		$output .=  "<tr>";
		$output .=  "<td>$csvkey</td>";
		$output .= "<td><select name='assoc_field[$csvkey]'>\n";
		$output .= "<option value='-1'>"._T("csvimport:pas_importer")."</option>\n";
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

/**
 * Fonction de vidange de la table lors du remplacement des données
 *  
 * @return
 * @param String $table Nom de la table
 */
function csvimport_vidange_table($table){
	sql_delete($table);
}

function csvimport_ajoute_table_csv($data, $table, $assoc_field, &$erreur){
	global $tables_principales;
	$csvimport_tables_auth = csvimport_tables_auth();
	$assoc = array_flip($assoc_field);
	$desc = sql_showtable($table);
	if (!isset($desc['field']) || count($desc['field'])==0){
		$erreur[0][] = _T("csvimport:description_table_introuvable");
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
			$check = array_flip($tablefield);
			foreach($check as $key=>$value){
				$kc = csvimport_nettoie_key($key);
				if ((isset($assoc[$kc]))&&(isset($ligne[$assoc[$kc]]))){
					$what[$key] = addslashes($ligne[$assoc[$kc]]);
					unset($check[$key]);
				}
	 		}
			if ((isset($stamp))&&isset($check[$stamp])){
				$what[$stamp] = date('Y-m-d H:i:s');
			}
			if (is_array($what)) {
				$id_primary = sql_insertq($table, $what);
				if ($id_primary==0)
					$erreur[$count_lignes][] = "ajout impossible ::$what::$with::<br />";
			}
			else
				$erreur[$count_lignes][] = "rien &agrave; ajouter<br />";
		}
	}
}

?>