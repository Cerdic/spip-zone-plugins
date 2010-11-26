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

include_spip("inc/csvimport");
include_spip("inc/presentation");
include_spip('public/assembler');

function csvimport_visu_extrait($nombre,$import_mode,$table,$id_form){
	// Extrait de la table en commençant par les dernieres maj
        if ($import_mode!='form')
		return csvimport_table_visu_extrait($table,$nombre);
	else {
		$contexte = array('id_form'=>$id_form,'total'=>$nombre);
		$out = recuperer_fond("fonds/tables_visu_extrait",$contexte);
		return $out;
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
	
	$titre = _T("csvimport:import_csv",array('table'=>$table));
	
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
			 $erreur[$step][] = _T("csvimport:fichier_absent");

		if (!$delim)
			 $erreur[$step][] = _T("csvimport:delimiteur_indefini");
		/*if (!isset($head))
			 $erreur[$step][] = _L("Header non d&eacute;fini");*/
		if (!count($assoc_field))
			 $erreur[$step][] = _T("csv_import:correspondance_indefinie");
		if (isset($erreur[$step])) $step--;
	}
	
	if ($step==3){
		if (!$head) $head = false;
		$data = csvimport_importcsv($tmp_name, $head, $delim);
		if ($data==false) {
		  $erreur[$step][] = _T("csvimport:fichier_vide");
		}
		$table_fields = csvimport_table_fields($import_mode,$table,$id_form);
		$new_assoc=csvimport_field_associate($data, $table_fields, $assoc_field);
		$test=array_diff($new_assoc,$assoc_field);
		if (count($test)>0){
			$erreur[$step][] = _T("csvimport:correspondance_incomplete");
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
			$step3 .= $import_form_link;
			foreach($hidden as  $key=>$value)
				$step3 .= "<input type='hidden' name='$key' value='$value' />";
			// Extrait de la table en commencant par les dernieres maj
			$step3 .= "<ul><li class='editer_texte'>";
			$step3 .= csvimport_visu_extrait(5,$import_mode,$table,$id_form);
			$step3 .= "</li><li class='editer_texte'>";
			$step3 .= "<div class='explication'>"._T('csvimport:avertissement_remplacement')."</div>";
			$step3 .= csvimport_array_visu_assoc($data, $table_fields, $assoc_field, 5);
			$step3 .= "</li></ul>";
			$step3 .= "<p class='boutons'>";
			$step3 .= "<input type='submit' name='annule_remplace' value='"._T('annuler')."' class='submit' />";
			$step3 .= "</p>\n";
			$step3 .= "<p class='boutons iconedanger'>";
			$step3 .= "<input type='submit' name='confirme_remplace' value='"._T('csvimport:remplacer_toute_table')."' class='submit' />";
			$step3 .= "</p>\n";
			$step3 .= "</div></form>";
		}
		else if (($ajouter)&&(!_request('confirme_ajoute'))){
			$hidden['ajouter'] = 'oui';
			$step3 .= $import_form_link;
			// Extrait de la table en commencant par les dernieres maj
			$step3 .= csvimport_visu_extrait(5,$import_mode,$table,$id_form);

			$step3 .= csvimport_array_visu_assoc($data, $table_fields, $assoc_field, 5);
			if ($import_mode=='form')
				if (include_spip('inc/forms')){
					Forms_csvimport_ajoute_table_csv($data, $id_form, $assoc_field, $err, true);
					$step3 .= csvimport_show_erreurs($err);
				}
			
			$step3 .= "<div style='padding: 2px; color: black;'>&nbsp;";
			$step3 .= _T("csvimport:avertissement_ajout",array('table'=>$table));
			foreach($hidden as  $key=>$value)
				$step3 .= "<input type='hidden' name='$key' value='$value' />";
			$step3 .= "<p class='boutons'><input type='submit' name='annule_ajoute' value='"._T('annuler')."' class='submit' /> ";
			$step3 .= "<input type='submit' name='confirme_ajoute' value='"._T('csvimport:ajouter_donnees')."' class='submit' /></p>";
			$step3 .= "</div></form>";
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

				// Extrait de la table en commencant par les dernieres maj
				$step3 .= csvimport_visu_extrait(10,$import_mode,$table,$id_form);

				if (count($err)){
					$step3 .= bouton_block_invisible("erreurs");
					$step3 .= _T("csvimport:erreurs_ajout_base",array('nb'=>count($err)));
					$step3 .= debut_block_invisible("erreurs");
					$step3 .= csvimport_show_erreurs($err);
					$step3 .= fin_block();
				}
				else
					$step3 .= csvimport_show_erreurs($err);
  			}
		}
	}
	return $step3;
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
			$erreur[$step][] = _T("csvimport:probleme_inextricable");
		if (
			(!isset($_FILES['csvfile'])) &&( (!$file_name)||(!$tmp_name)||(!$size)||(!$type) )
		)
			 $erreur[$step][] = _T("csvimport:probleme_chargement_fichier");

		if ((isset($_FILES['csvfile']))&&($_FILES['csvfile']['error']!=0))
			$erreur[$step][]=_T("csvimport:probleme_chargement_fichier_erreur",array("erreur" => $_FILES['csvfile']['error']));
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
			move_uploaded_file( $tmp_name, $dest );
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
		  $erreur[$step][] = _T("csvimport:fichier_vide");
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

		$step2 .= csvimport_show_erreurs($erreur);
		$step2 .= $import_form_link;
		foreach($hidden as  $key=>$value)
			$step2 .= "<input type='hidden' name='$key' value='$value' />";
					
		$step2 .= "<ul><li class='editer_texte'>";
		$step2 .= "<div class='explication'>"._T('csvimport:premieres_lignes',array('nb'=>5))."</div>";
		$step2 .= csvimport_array_visu_extrait($data, /*$head*/true, 5);

		$step2 .= "</li><li class='editer_texte'>";
		$step2 .= "<p class='boutons'><input type='submit' name='apercu' value='"._T('previsualisation')."' class='submit' /></p>";
		$step2 .= "</li>";

		$step2 .= "<li><label for='separateur'>"._T("csvimport:caracteres_separation")."</label>";
		$step2 .= "<input type='text' name='delim' id='separateur' class='text' style='width:2em;' maxlength='1' value='$delim' /></li>";
		$step2 .= "<li><label for='entete'>"._T("csvimport:ligne_entete")."</label>";
		$step2 .= "<input type='checkbox' name='head' id='entete' class='fondl' style='width:2em;' value='true'";
		if ($head==true)
		  $step2 .= " checked='checked'";
		$step2 .= " /></li><li class='editer_texte'>";

		$step2 .= csvimport_field_configure($data, $table_fields, $assoc_field);

		$step2 .= "</li>\n";

		$step2 .= "<li class='editer_texte'>";
		$step2 .= csvimport_array_visu_assoc($data, $table_fields, $assoc_field, 5);
		$step2 .= "</li></ul>\n";

		if ($csvimport_add_actif) {
			$step2 .= "<p class='boutons'>";
			$step2 .= "<input type='submit' name='ajouter' value='"._T('csvimport:ajouter_table')."' class='submit' />";
			$step2 .= "</p>\n";
		}

		if ($csvimport_replace_actif) {
			$step2 .= "<p class='boutons iconedanger' style='margin-top:15px;'>";
			$step2 .= "<input type='submit' name='remplacer' value='"._T('csvimport:remplacer_toute_table')."' class='submit' />";
			$step2 .= "</p>\n";
		}

		$step2 .= "</div></form>";

		return $step2;
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
		$step1 = "<br />\n";
		$step1 .= "<div style='float:$spip_lang_left'>";
		$step1 .= csvimport_show_erreurs($erreur);

		$hidden['head'] = 'true';
		$hidden['step'] = 2;
		$step1 .= "<form action='$import_link' method='post' enctype='multipart/form-data' class='formulaire_editer'><div>";
		foreach($hidden as  $key=>$value)
			$step1 .= "<input type='hidden' name='$key' value='$value' />";
		$step1 .= "<ul><li>";
		$step1 .= "<label for='file_name'>"._T("csvimport:fichier_choisir")."</label>";
		$step1 .= "<input type='file' name='csvfile' id='file_name' class='fichier' />";
		$step1 .= "</li></ul>";
		$step1 .= "<p class='boutons'><input type='submit' name='Valider' value='"._T('bouton_valider')."' class='submit' /></p>";
		$step1 .= "</div></form></div>\n";
	}
	return $step1;
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
	
	$titre = _T("csvimport:import_csv",array('table'=>$table));
	$icone = _DIR_PLUGIN_CSVIMPORT."img_pack/csvimport-24.png";
	$operations = array();

	if ($table===NULL && $id_form) {
		$import_mode='form';
		$import_link = generer_url_ecrire("csvimport_import","id_form=$id_form&retour=".urlencode($retour));
		$action = generer_url_ecrire("csvimport_import","id_form=$id_form&retour".urlencode($retour));
		$import_form_link = "\n<form action='$action' method='post' class='formulaire_editer'><div>".form_hidden($action);
		if (!include_spip('inc/autoriser'))
			include_spip('inc/autoriser_compat');
		$is_importable = autoriser('administrer','form',$id_form);
		$csvimport_replace_actif = true;
		$csvimport_add_actif = true;
	}
	else {
		$import_mode='table';
		$import_link = generer_url_ecrire("csvimport_import","table=$table&retour=".urlencode($retour));
		$action = generer_url_ecrire("csvimport_import","table=$table&retour".urlencode($retour));
		$import_form_link = "\n<form action='$action' method='post' class='formulaire_editer'><div>".form_hidden($action);
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
	$commencer_page = charger_fonction('commencer_page', 'inc');
	pipeline('exec_init',array('args'=>$_GET,'data'=>''));
		
	echo $commencer_page($titre,"csvimport");
	
	echo debut_gauche('',true);
	
	$raccourcis = icone_horizontale(_T('csvimport:administrer_tables'), generer_url_ecrire("csvimport_admin"), $icone, "", false);
	$raccourcis .= icone_horizontale(_T('csvimport:import_export_tables'), generer_url_ecrire("csvimport_tous"), $icone, "", false);

	echo bloc_des_raccourcis($raccourcis);
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'csvimport_import','table'=>$table),'data'=>''));
	
	echo creer_colonne_droite("",true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'csvimport_import','table'=>$table),'data'=>''));
	echo debut_droite("",true);
	
	$erreur=array();
	
	if ($is_importable) {
	
		$hidden = array();
		
		$milieu = '';
		
		$milieu .= "<div class='entete-formulaire'>";
		//
		// Icones retour
		//
		if ($retour) {
			$milieu .= icone_inline(_T('icone_retour'), $retour, $icone, "rien.gif",$GLOBALS['spip_lang_left']);
		}
		$milieu .= gros_titre($titre,'', false);
		$milieu .= "</div>";

		$milieu .= "<div class='formulaire_spip'>";
		
		if($step<3){
			$milieu .= csvimport_visu_extrait(5,$import_mode,$table,$id_form);
		}
		// --- STEP 3
		$milieu .= csvimport_import_step3($step, $erreur, $import_link, $import_form_link, $csvimport_replace_actif, $csvimport_add_actif);
		
		// --- STEP 2
		$milieu .= csvimport_import_step2($step, $erreur, $import_link, $import_form_link, $csvimport_replace_actif, $csvimport_add_actif);
	
		// --- STEP 1
		$milieu .= csvimport_import_step1($step, $erreur, $import_link, $import_form_link, $csvimport_replace_actif, $csvimport_add_actif);
		
		$milieu .= "</div>";
		
	}
	else {
		//
		// Icones retour
		//
		if ($retour) {
			$milieu = "<br />\n";
			$milieu .= "<div style='float:$spip_lang_right'>";
			$milieu .= icone_inline(_T('icone_retour'), $retour, $icone, "rien.gif",$GLOBALS['spip_lang_left']);
			$milieu .= "</div>\n";
		}
	}
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'csvimport_import','table'=>$table),'data'=>$milieu));

	echo fin_gauche(), fin_page();
}

?>
