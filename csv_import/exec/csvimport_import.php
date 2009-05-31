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

include_spip("inc/csvimport");
include_spip("inc/presentation");
include_spip('public/assembler');

function csvimport_visu_extrait($nombre,$import_mode,$table,$id_form){
	// Extrait de la table en commençant par les dernieres maj
	if ($import_mode!='form')
		csvimport_table_visu_extrait($table,$nombre);
	else {
		$contexte = array('id_form'=>$id_form,'total'=>$nombre);
		$out = recuperer_fond("fonds/tables_visu_extrait",$contexte);
		echo $out;
	}
}

function csvimport_table_fields($mode,$table,$id_form){
	$table_fields = array();
	if ($mode=='table'){
		$csvimport_tables_auth = csvimport_tables_auth();
		if (isset($csvimport_tables_auth[$table]['field']))
			$table_fields=$csvimport_tables_auth[$table]['field'];
		else
			$table_fields=array_keys($GLOBALS['tables_principales'][$table]['field']);
		$table_fields=array_flip($table_fields);
		foreach ($table_fields as $key=>$value) {
			$table_fields[$key] = $key;
		}
		return $table_fields;
	}
	if ($mode=='form' && $id_form){
		include_spip('inc/forms');
		$structure = Forms_structure($id_form);
		$table_fields['id_donnee'] = 'id_donnee';
		foreach ($structure as $champ=>$info){
			if ($info['type']!='multiple')
				$table_fields[$champ] = $info['titre'];
			else 
				foreach ($info['choix'] as $choix=>$value) {
					$table_fields[$choix] = $value;
				}
		}
		return $table_fields;
	}
	return $table_fields;
}

function csvimport_import_step3(&$step, &$erreur, $import_link, $import_form_link, $csvimport_replace_actif, $csvimport_add_actif){
	$table = _request('table');	
	$id_form = intval(_request('id_form'));
	$retour = urldecode(_request('retour'));
	$file_name = _request('file_name');
	$tmp_name = _request('tmp_name');
	$size = _request('size');
	$type = _request('type');
	$delim = _request('delim');
	$head = _request('head');
	$ajouter = _request('ajouter');
	$remplacer = _request('remplacer');
	$assoc_field = _request('assoc_field');
	$apercu = _request('apercu');
	if ($table===NULL && $id_form)
		$import_mode='form';
	else	
		$import_mode='table';
	
	if ($step==3){
		if (($remplacer)&&(_request('annule_remplace')))
		  $step--;
		else if (($ajouter)&&(_request('annule_ajoute')))
		  $step--;
		else if ($apercu!=NULL)
		  	$step--;
		else if (($remplacer)&&(!isset($csvimport_replace_actif)))
		  	$step--;
		else if (($ajouter)&&(!isset($csvimport_add_actif)))
		  	$step--;
  }

	if ($step==3){
		if ( (!$file_name)||(!$tmp_name)||(!$size)||(!$type) )
			 $erreur[$step][] = _L("Fichier absent");

		if (!$delim)
			 $erreur[$step][] = _L("Delimiteur non d&eacute;fini");
		/*if (!isset($head))
			 $erreur[$step][] = _L("Header non d&eacute;fini");*/
		if (!count($assoc_field))
			 $erreur[$step][] = _L("Correspondances CSV-Table non d&eacute;finies");
		if (isset($erreur[$step])) $step--;
	}
	
	if ($step==3){
		if (!$head) $head = false;
		$data = csvimport_importcsv($tmp_name, $head, $delim);
		if ($data==false) {
		  $erreur[$step][] = _L("Fichier vide");
		}
		$table_fields = csvimport_table_fields($import_mode,$table,$id_form);
		$new_assoc=csvimport_field_associate($data, $table_fields, $assoc_field);
		$test=array_diff($new_assoc,$assoc_field);
		if (count($test)>0){
			$erreur[$step][] = _L("Correspondances CSV-Table incompl&egrave;tes");
		}
		if (isset($erreur[$step])) $step--;
	}
	if ($step==3){
		$hidden['file_name'] = $file_name;
		$hidden['tmp_name'] = $tmp_name;
		$hidden['size'] = $size;
		$hidden['type'] = $type;
		$hidden['step'] = 3;
		foreach($assoc_field as $key=>$value)
			$hidden["assoc_field[".csvimport_nettoie_key($key)."]"] = $value;
		$hidden["delim"] = $delim;
		$hidden["head"] = $head;

		/*echo "<br />\n";
		if (count($erreur)>0){
			echo "<div class='messages'>";
			foreach($erreur as $steper=>$desc)
				foreach($desc as $val)
					echo "<strong>$steper::$val</strong><br />";
			echo "</div>\n";
	 	}*/

		if (($remplacer)&&(!_request('confirme_remplace'))){
			$hidden['remplacer'] = 'oui';
			debut_cadre_relief($icone);
			gros_titre($titre);
			// Extrait de la table en commençant par les dernieres maj
			csvimport_visu_extrait(5,$import_mode,$table,$id_form);
			fin_cadre_relief();

			debut_cadre_enfonce();
			echo csvimport_array_visu_assoc($data, $table_fields, $assoc_field, 5);
			fin_cadre_enfonce();
			echo "<div style='padding: 2px; color: black;'>&nbsp;";
			echo _L("Cette op&eacute;ration va entra&icirc;ner la suppression de toutes les donn&eacute;es pr&eacute;sentes dans la table.");
			echo $import_form_link;
			foreach($hidden as  $key=>$value)
				echo "<input type='hidden' name='$key' value='$value' />";
			echo "<input type='submit' name='annule_remplace' value='"._L('Annuler')."' class='fondo'>";
			echo "</div>\n";
			echo "<div class='iconedanger' style='margin-top:15px;'>";
			echo "<input type='submit' name='confirme_remplace' value='"._L('Remplacer toute la table')."' class='fondo'>";
			echo "</div>\n";
			echo "</form>";
		}
		else if (($ajouter)&&(!_request('confirme_ajoute'))){
			$hidden['ajouter'] = 'oui';
			debut_cadre_relief($icone);
			gros_titre($titre);
			// Extrait de la table en commençant par les dernieres maj
			csvimport_visu_extrait(5,$import_mode,$table,$id_form);
			fin_cadre_relief();

			debut_cadre_enfonce();
			echo csvimport_array_visu_assoc($data, $table_fields, $assoc_field, 5);
			fin_cadre_enfonce();
			if ($import_mode=='form')
				if (include_spip('inc/forms')){
					Forms_csvimport_ajoute_table_csv($data, $id_form, $assoc_field, $err, true);
					echo csvimport_show_erreurs($err);
				}
			
			echo "<div style='padding: 2px; color: black;'>&nbsp;";
			echo _L("Les donn&eacute;es du fichier CSV vont &ecirc;tre ajout&eacute;es &agrave; la table comme illustr&eacute; ci-dessus.");
			echo $import_form_link;
			foreach($hidden as  $key=>$value)
				echo "<input type='hidden' name='$key' value='$value' />";
			echo "<input type='submit' name='annule_ajoute' value='"._L('Annuler')."' class='fondo'> ";
			echo "<input type='submit' name='confirme_ajoute' value='"._L('Ajouter les donn&eacute;es')."' class='fondo'>";
			echo "</form>";
 		}
		else {
			// vidange de la table
			if (($remplacer)&&(_request('confirme_remplace'))){
				if ($import_mode=='table')
					csvimport_vidange_table($table);
				elseif ($import_mode=='form')
					if (include_spip('inc/forms'))
						Forms_donnees_vide($id_form);
			}
			// le reste est identique que ce soit un ajout ou un remplace
			if (($remplacer)||($ajouter)){
				$err = array();
				if ($import_mode=='table')
					$out = csvimport_ajoute_table_csv($data, $table, $assoc_field,$err);
				elseif ($import_mode=='form')
					if (include_spip('inc/forms'))
						Forms_csvimport_ajoute_table_csv($data, $id_form, $assoc_field, $err);

				debut_cadre_relief($icone);
				gros_titre($titre);
				// Extrait de la table en commençant par les dernieres maj
				csvimport_visu_extrait(10,$import_mode,$table,$id_form);
				fin_cadre_relief();

				if (count($err)){
					echo bouton_block_invisible("erreurs");
					echo count($err) . _L(" erreurs lors de l'ajout dans la base");
					echo debut_block_invisible("erreurs");
					echo csvimport_show_erreurs($err);
					echo fin_block();
				}
				else
					echo csvimport_show_erreurs($err);
  		}
		}
	}
}
function csvimport_import_step2(&$step, &$erreur, $import_link, $import_form_link, $csvimport_replace_actif, $csvimport_add_actif){
	$table = _request('table');	
	$id_form = intval(_request('id_form'));
	$retour = urldecode(_request('retour'));
	$file_name = _request('file_name');
	$tmp_name = _request('tmp_name');
	$size = _request('size');
	$type = _request('type');
	$delim = _request('delim');
	$head = _request('head');
	$ajouter = _request('ajouter');
	$remplacer = _request('remplacer');
	$assoc_field = _request('assoc_field');
	$apercu = _request('apercu');
	if ($table===NULL && $id_form)
		$import_mode='form';
	else	
		$import_mode='table';
	if ($step==2){
		if (!isset($_FILES))
			$erreur[$step][] = _L("Probl&egrave;me inextricable...");
		if (
				(!isset($_FILES['csvfile']))
			&&( (!$file_name)||(!$tmp_name)||(!$size)||(!$type) )
			 )
			 $erreur[$step][] = _L("Probl&egrave;me lors du chargement du fichier");

		if ((isset($_FILES['csvfile']))&&($_FILES['csvfile']['error']!=0))
			$erreur[$step][]=_L("Probl&egrave;me lors du chargement du fichier (erreur ".$_FILES['csvfile']['error'].")");
		if (isset($erreur[$step])) $step--;
	}
	if ($step==2){
		if (!$head) $head = false;

		if (isset($_FILES['csvfile'])){
			$file_name = $_FILES['csvfile']['name'];
			$tmp_name = $_FILES['csvfile']['tmp_name'];
			$size = $_FILES['csvfile']['size'];
			$type = $_FILES['csvfile']['type'];

			$dest = _DIR_SESSIONS.basename($tmp_name);
			move_uploaded_file ( $tmp_name, $dest );
			$tmp_name = $dest;
	 	}


		if (!$delim){
			if ($type=="application/vnd.ms-excel")
				$delim = ";"; // specificite Excel de faire des fichiers csv avec des ; au lieu de ,
			else{
				$handle = fopen($tmp_name, "rt");
  			$contenu = fread($handle, 8192);
				fclose($handle);
				if ($contenu!=FALSE){
					if (substr_count($contenu,",")>=substr_count($contenu,";"))
						$delim = ",";
					else
						$delim = ";";
				}
				else
					$delim = ",";
			}
	 	}
		$data = csvimport_importcsv($tmp_name, $head, $delim);
		if ($data==false) {
		  $erreur[$step][] = _L("Fichier vide");
		  $step--;
		}
	}
	$table_fields = csvimport_table_fields($import_mode,$table,$id_form);
	if ($data && ($step==2))
		$assoc_field=csvimport_field_associate($data, $table_fields, $assoc_field);
	if ($step==2){
		$hidden['file_name'] = $file_name;
		$hidden['tmp_name'] = $tmp_name;
		$hidden['size'] = $size;
		$hidden['type'] = $type;
		$hidden['step'] = 3;

		echo "<br />\n";
		echo csvimport_show_erreurs($erreur);

		debut_cadre_enfonce();
		echo csvimport_array_visu_extrait($data, /*$head*/true, 5);
		fin_cadre_enfonce();


		debut_cadre_relief();
		echo $import_form_link;
		foreach($hidden as  $key=>$value)
			echo "<input type='hidden' name='$key' value='$value' />";
		echo "<div style='margin: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
		echo "Pr&eacute;visualisation ";
		echo "<input type='submit' name='apercu' value='"._L('Appliquer')."' class='fondl'>";
		echo "</div>";

		echo "<strong><label for='separateur'>"._L("Caract&egrave;re de s&eacute;paration")."</label></strong> ";
		echo "<input type='text' name='delim' id='separateur' class='fondl' style='width:2em;' maxlength='1' value='$delim'><br />";
		echo "<strong><label for='entete'>"._L("1<sup>&egrave;re</sup> ligne d'en-t&ecirc;te")."</label></strong> ";
		echo "<input type='checkbox' name='head' id='entete' class='fondl' style='width:2em;' value='true'";
		if ($head==true)
		  echo " checked='checked'";
		echo "><br />";


		echo csvimport_field_configure($data, $table_fields, $assoc_field);

		echo "</div><hr />\n";

		echo "<div align='$spip_lang_left'>";

		echo csvimport_array_visu_assoc($data, $table_fields, $assoc_field, 5);
		echo "</div><hr />\n";

		if ($csvimport_add_actif) {
			echo "<div style='padding: 2px; color: black;'>&nbsp;";
			echo "<input type='submit' name='ajouter' value='"._L('Ajouter &agrave; la table')."' class='fondo'>";
			echo "</div>\n";
		}

		if ($csvimport_replace_actif) {
			echo "<div class='iconedanger' style='margin-top:15px;'>";
			echo "<input type='submit' name='remplacer' value='"._L('Remplacer toute la table')."' class='fondo'>";
			echo "</div>\n";
		}

		echo "</form>";

		fin_cadre_relief();
	}
}
function csvimport_import_step1(&$step, &$erreur, $import_link, $import_form_link, $csvimport_replace_actif, $csvimport_add_actif){
	$table = _request('table');	
	$id_form = intval(_request('id_form'));
	$retour = urldecode(_request('retour'));
	$file_name = _request('file_name');
	$tmp_name = _request('tmp_name');
	$size = _request('size');
	$type = _request('type');
	$delim = _request('delim');
	$head = _request('head');
	$ajouter = _request('ajouter');
	$remplacer = _request('remplacer');
	$assoc_field = _request('assoc_field');
	$apercu = _request('apercu');
	if ($table===NULL && $id_form)
		$import_mode='form';
	else	
		$import_mode='table';

	if ($step==1){
		echo "<br />\n";
		echo "<div align='$spip_lang_left'>";
		echo csvimport_show_erreurs($erreur);

		$hidden['head'] = 'true';
		$hidden['step'] = 2;
		echo "<form action='$import_link' method='POST' enctype='multipart/form-data'>";
		foreach($hidden as  $key=>$value)
			echo "<input type='hidden' name='$key' value='$value' />";
		echo "<strong><label for='file_name'>"._L("Fichier CSV &agrave; importer")."</label></strong> ";
		echo "<br />";
		echo "<input type='file' name='csvfile' id='file_name' class='formo'>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
		echo "</form></div>\n";
	}
}

function exec_csvimport_import(){
	global $spip_lang_right;
	$assoc_field=array();
	$table = _request('table');	
	$id_form = intval(_request('id_form'));
	$retour = urldecode(_request('retour'));
	$step = _request('step');
	$file_name = _request('file_name');
	$tmp_name = _request('tmp_name');
	$size = _request('size');
	$type = _request('type');
	$delim = _request('delim');
	$head = _request('head');
	$ajouter = _request('ajouter');
	$remplacer = _request('remplacer');
	$assoc_field = _request('assoc_field');
	$apercu = _request('apercu');

	if (!$step)
		$step = 1;
	if (!$retour)
		$retour = generer_url_ecrire('csvimport_tous');
	
	$titre = _L("Import CSV : ");
	$icone = "../"._DIR_PLUGIN_CSVIMPORT."img_pack/csvimport-24.png";
	$operations = array();

	if ($table===NULL && $id_form) {
		$import_mode='form';
		$import_link = generer_url_ecrire("csvimport_import","id_form=$id_form&retour=".urlencode($retour));
		$import_form_link = generer_url_post_ecrire("csvimport_import","id_form=$id_form&retour".urlencode($retour));
		if (!include_spip('inc/autoriser'))
			include_spip('inc/autoriser_compat');
		$is_importable = 	autoriser('administrer','form',$id_form);
	  $csvimport_replace_actif = true;
	  $csvimport_add_actif = true;
	}
	else {
		$import_mode='table';
		$import_link = generer_url_ecrire("csvimport_import","table=$table&retour=".urlencode($retour));
		$import_form_link = generer_url_post_ecrire("csvimport_import","table=$table&retour".urlencode($retour));
		
		$is_importable = csvimport_table_importable($table,$titre,$operations);
	
		if (in_array('replaceall',$operations))
		  $csvimport_replace_actif = true;
		if (in_array('add',$operations))
		  $csvimport_add_actif = true;
	}
	$clean_link = $import_link;
	
	//
	// Affichage de la page
	//
	
	debut_page($titre, "documents", "csvimport");
	debut_gauche();
	
	echo "<br /><br />\n";
	
	debut_droite();
	
	$erreur=array();
	
	if ($is_importable) {
	
		$hidden = array();
		// --- STEP 3
		csvimport_import_step3($step, $erreur, $import_link, $import_form_link, $csvimport_replace_actif, $csvimport_add_actif);
		if ($step<3) {
			debut_cadre_relief($icone);
			gros_titre($titre);
			// Extrait de la table en commençant par les dernieres maj
			csvimport_visu_extrait(5,$import_mode,$table,$id_form);
			fin_cadre_relief();
	 	}	
		//
		// Icones retour
		//
		if ($retour) {
			echo "<br />\n";
			echo "<div align='$spip_lang_right'>";
			icone(_T('icone_retour'), $retour, $icone, "rien.gif");
			echo "</div>\n";
		}
	
		// --- STEP 2
		csvimport_import_step2($step, $erreur, $import_link, $import_form_link, $csvimport_replace_actif, $csvimport_add_actif);

	
		// --- STEP 1
		csvimport_import_step1($step, $erreur, $import_link, $import_form_link, $csvimport_replace_actif, $csvimport_add_actif);
	}
	else {
		//
		// Icones retour
		//
		if ($retour) {
			echo "<br />\n";
			echo "<div align='$spip_lang_right'>";
			icone(_T('icone_retour'), $retour, $icone, "rien.gif");
			echo "</div>\n";
		}
	}
	
	fin_page();
}

?>