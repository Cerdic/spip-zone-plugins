<?php
/*
 * i2_import
 * plug-in d'import csv des utilisateurs dans les tables spip_auteurs et spip_auteurs_elargis
 *
 * Auteur :
 * Quentin Drouet (vilement pompé de Cedric MORIN)
 * © 2009 - Distribue sous licence GNU/GPL
 *
 */

include_spip("base/abstract_sql");
include_spip("inc/charsets");

/**
 * 
 * Liste de l'ensemble des champs possibles
 * 
 * @return si $type='unique' retourne un array des deux tables, sinon retourne un array contenant deux
 * arrays distincts
 * @param array $tables un array des tables (spip_auteurs et spip_auteurs_elargis)
 * @param string $type[optional] détermine la forme du retour
 */
function i2_import_table_fields($tables,$type='unique'){
	$trouver_table = charger_fonction('trouver_table','base');
	$table_fields_final = array();
	if(is_array($tables)){
		foreach($tables as $table){
			if($table == 'spip_auteurs'){
				// Tous les champs de spip_auteurs ne sont pas à prendre en compte
				
				$spip_auteurs['nom'] = 'nom';
				$spip_auteurs['bio'] = 'bio';
				$spip_auteurs['email'] = 'email';
				$spip_auteurs['nom_site'] = 'nom_site';
				$spip_auteurs['url_site'] = 'url_site';
				$spip_auteurs['login'] = 'login';
				$spip_auteurs['statut'] = 'statut';
			}else{
				$table_desc = $trouver_table($table);
				$spip_auteurs_elargis=array_keys(is_array($table_desc['field']) ? $table_desc['field'] : array());
				$spip_auteurs_elargis=array_flip($spip_auteurs_elargis);
				foreach ($spip_auteurs_elargis as $key=>$value) {
					/**
					 * On ne garde que les champs activés
					 */
					if(lire_config('inscription2/'.$key) == 'on'){
						$spip_auteurs_elargis[$key] = $key;
					}else{
						unset($spip_auteurs_elargis[$key]);
					}
				}
				// On ne met pas à disposition le champs id_auteur
				unset($spip_auteurs_elargis['id_auteur']);
			}
		}
		if($type == 'unique'){
			$table_fields_final = array_merge($spip_auteurs,$spip_auteurs_elargis);
			return $table_fields_final;
		}
		else{
			return array($spip_auteurs,$spip_auteurs_elargis);
		}
	}
	return;
}

function i2_import_csv_champ($champ) {
	$champ = preg_replace(',[\s]+,', ' ', $champ);
	$champ = str_replace(',",', '""', $champ);
	return '"'.$champ.'"';
}

function i2_import_csv_ligne($ligne, $delim = ',') {
	return join($delim, array_map('i2_import_csv_champ', $ligne))."\r\n";
}

function i2_import_show_erreurs($erreur){
	$output = "";
	if (count($erreur)>0){
		$bouton = bouton_block_depliable(_T('i2_import:csv_erreurs'), false,"csv_erreurs");
		$output .= debut_cadre_enfonce("mot-cle-24.gif", true, "", $bouton);
		$output .= debut_block_depliable(false,"csv_erreurs");
		foreach($erreur as $steper=>$desc){
			$output .= "<dl>";
			$output .= "<dt>"._T('i2_import:nb_ligne',array('nb'=>$steper));
			foreach($desc as $key=>$val)
				$output .=  "<dd>"._T('inscription2:'.$key)." : $val<dd>";
			$output .= "</dl>";
		}
		$output .= fin_block();
		$output .= fin_cadre_enfonce(true);
	}
	return $output;
}

function i2_import_show_imports($imports){
	$output = "";
	if (count($imports)>0){
		$bouton = bouton_block_depliable(_T('i2_import:csv_ajouts'), false,"csv_ajouts");
		$output .= debut_cadre_enfonce("mot-cle-24.gif", true, "", $bouton);
		$output .= debut_block_depliable(false,"csv_ajouts");
		foreach($imports as $steper=>$desc){
			$output .= "<dl>";
			$output .= "<dt>"._T('i2_import:nb_ligne',array('nb'=>$steper));
			foreach($desc as $key=>$val)
				$output .=  "<dd>"._T('inscription2:'.$key)." : $val<dd>";
			$output .= "</dl>";
		}
		$output .= fin_block();
		$output .= fin_cadre_enfonce(true);
	}
	return $output;
}

function i2_import_table_visu_extrait($tables,$nombre_lignes = 0){
	$maj_exist = true;
	$limit = "";
	
	$champs = i2_import_table_fields($tables);
	
	if ($nombre_lignes > 0)
		$limit = ($nombre_lignes+1);
		$result = sql_select($champs,"spip_auteurs LEFT JOIN spip_auteurs_elargis USING(id_auteur)",'','','maj DESC',$limit);
	if (!$result) {
 		$result = sql_select($champs,"spip_auteurs LEFT JOIN spip_auteurs_elargis USING(id_auteur)",'','','',$limit);
 		$maj_exist = false;
	}

	$nb_data=sql_count($result);
	if ($nombre_lignes==0)
		$nombre_lignes = $nb_data;
	$data_count = 0;
	$head_set = false;
	$nb_col = 0;
	$ligne = 0;
	echo "<table class='spip'>";
	while (($row = sql_fetch($result))&&($data_count++<$nombre_lignes)){
		$ligne++;
		if (!$head_set){
			echo "<tr>";
			foreach($row as $key=>$value){
			  echo "<th>" . _T('inscription2:'.$key) . "</th>";
			  $nb_col++;
			}
			echo "</tr>\n";
			$head_set = true;
		}
		echo "<tr class='".alterner($ligne,'row_odd','row_even')."'>";
		foreach($row as $key=>$value)
		  echo "<td>" . $value . "</td>";
		echo "</tr>\n";
	}
	if ($nb_data>$nombre_lignes){
		$query = sql_select("id_auteur","spip_auteurs");
		$num_rows = sql_count($query);
		echo "<tr><td colspan='$nb_col' style='border-top:1px dotted;'>"._T('i2_import:total_auteur',array('nb'=>$num_rows))."</td></tr>\n";
	}
	echo "</table>\n";
	if ($data_count==0)
	  echo _L("Table vide");
}

function i2_import_array_visu_assoc($data, $table_fields, $assoc_field, $nombre_lignes = 0){
	$assoc=array_flip($assoc_field);

	$output = "";
	$data_count = 0;
	$ligne_nb = 0;
	echo "<table class='spip'>";
	$output .= "<tr>";
	foreach($table_fields as $key=>$value){
		if(isset($assoc[$value])){
	  		$output .= "<th>" . _T('inscription2:'.$value) . "</th>";
		}
	}
	$output .= "</tr>\n";

	if ($data!=false){
		foreach($data as $key=>$ligne) {
			$ligne_nb++;
			$output .= "<tr class='".alterner($ligne_nb,'row_odd','row_even')."'>";
			foreach($table_fields as $key=>$value){
				$kc = i2_import_nettoie_key($key);
				if(isset($assoc[$kc])){
			  		$output .= "<td>";
			  		if(isset($ligne[$assoc[$kc]]))
			    		$output .= $ligne[$assoc[$kc]];
					else
						$output .= "&nbsp;";
					$output .= "</td>";
				}
			}
			$output .= "</tr>\n";
			if (($nombre_lignes>0)&&(++$data_count>=$nombre_lignes))
			  break;
		}
	}
	$output .= "</table>";

	if ($data_count>0)
	  $output .= "<p class='explication'>". _T('i2_import:total_lignes',array('nb'=>count($data)))."</p>";;
	return $output;
}

function i2_import_nettoie_key($key){
	return translitteration($key);
}

function i2_import_field_associate($data, $table_fields, $assoc_field){
	global $tables_principales;
	$assoc=$assoc_field;
	if (!is_array($assoc)) $assoc = array();
	$csvfield=array_keys($data{1});
	foreach($csvfield as $k=>$v){
		$csvfield[$k] = i2_import_nettoie_key($v);
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

function i2_import_field_configure($data, $table_fields, $assoc){
	$output = "";
	$csvfield=array_keys($data{1});
	
	$output .= "<li><label>"._L("Champ CSV")."</label>"._L("Champ Table")."</li>";
	$nb_champs = 0;
	foreach($csvfield as $csvkey){
		$nb_champs++;
		$csvkey = i2_import_nettoie_key($csvkey);
		$output .=  "<li>";
		$output .=  "<label for='champs$nb_champs'>$csvkey</label>";
		$output .= "<select name='assoc_field[$csvkey]' id='champs$nb_champs'>\n";
		$output .= "<option value='-1'>"._T("i2_import:pas_importer")."</option>\n";
		foreach($table_fields as $tablekey => $libelle){
			$output .= "<option value='$tablekey'";
			if ($assoc[$csvkey]==$tablekey)
				$output .= " selected='selected'";
			$output .= ">"._T('inscription2:'.$libelle)."</option>\n";
		}
		$output .= "</select></li>";
	}
	return $output;
}

function i2_import_ajoute_table_csv($data, $table, $assoc_field, &$erreur){
	$erreur = array();
	$assoc = array_flip($assoc_field);

	$table_fields = i2_import_table_fields($table);
	list($auteurs,$auteurs_elargis) = i2_import_table_fields($table,'separe');

	$auteurs_obligatoires = array('nom','login','email','statut');

	if ($data!=false){
		$count_lignes = 0;
		$verif_champs = pipeline('i2_verifications_specifiques');
		foreach($data as $key=>$ligne) {
			$count_lignes ++;
			$auteurs_insert = array();
			$auteurs_elargis_insert = array();
			$check = array_flip($table_fields);
			foreach($check as $key=>$value){
				$kc = i2_import_nettoie_key($key);
				$ligne[$assoc[$kc]] = trim($ligne[$assoc[$kc]]);
				if ((isset($assoc[$kc]))&&(isset($ligne[$assoc[$kc]]))){
					// On vérifie tout d'abord si le champs dispose d'une fonction de vaidation
					if(array_key_exists($key,$verif_champs)){
						$fonction_verif_{$key} = charger_fonction('inscription2_'.$verif_champs[$key],'inc');
						if($val = $fonction_verif_{$key}($ligne[$assoc[$kc]],'')){
							$erreurs[$count_lignes][$key] = $val;
						}
					}
					// Si pas d'erreur sur ce champs on vérifie qu'il soit obligatoire
					if(!isset($erreurs[$count_lignes][$key])){
						if(in_array($key,$auteurs)){
							if(in_array($key,$auteurs_obligatoires) && (strlen($ligne[$assoc[$kc]])==0)){
								$erreurs[$count_lignes][$key] = _T("i2_import:champs_oblig",array('champs'=>$key));
							}
							$auteurs_insert[$key] = $ligne[$assoc[$kc]];
							$auteur[$count_lignes][$key] = $ligne[$assoc[$kc]];
						}
						else{
							$auteurs_elargis_insert[$key] = $ligne[$assoc[$kc]];
							$auteur[$count_lignes][$key] = $ligne[$assoc[$kc]];
						}
					}
					unset($check[$key]);
				}
	 		}
			if(count($erreurs[$count_lignes],COUNT_RECURSIVE) == 0){
				if(count($auteurs_insert)){
					// Vérifier les données
					// On ajoute la date de MAJ qui correspond à maintenant
					$auteurs_insert['maj'] = date('Y-m-d H:i:s');
					// Le statut est obligatoire ... donc on le rajoute s'il n'est pas dans le fichier
					if(!isset($auteurs_insert['statut'])){
						$auteurs_insert['statut'] = lire_config('inscription2/statut_nouveau')?lire_config('inscription2/statut_nouveau'):'6forum';
					}
					// Le login est obligatoire ... S'il n'est pas présent, on le génère à partir du nom et de l'email
					if(!isset($auteurs_insert['login'])){
						$definir_login = charger_fonction('inscription2_definir_login','inc');
						$auteurs_insert['login'] = $definir_login($auteurs_insert['nom'],$auteurs_insert['email']);
					}
					$auteur[$count_lignes]['id_auteur'] = sql_insertq('spip_auteurs',$auteurs_insert);
				}
				if(count($auteurs_elargis_insert) && ($auteur[$count_lignes]['id_auteur']>0)){
					// Vérifier les données
					$auteurs_elargis_insert['id_auteur'] = $auteur[$count_lignes]['id_auteur'];
					if(isset($auteurs_elargis['creation']) && !isset($auteurs_elargis_insert['creation'])){
						$auteurs_elargis_insert['creation'] = date('Y-m-d H:i:s');
					}
					sql_insertq('spip_auteurs_elargis',$auteurs_elargis_insert);
				}
			}else{
				unset($auteur[$count_lignes]);
			}
		}
	}
	return array($erreurs,$auteur);
}

?>