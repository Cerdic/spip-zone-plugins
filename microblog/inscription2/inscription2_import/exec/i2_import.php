<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/i2_import");
include_spip('public/assembler');
include_spip('inc/presentation');

/**
 * 
 * @return 
 * @param int $step Le numéro de l'étape
 * @param object $erreur
 * @param object $import_link
 * @param object $import_form_link
 */
function i2_import_step3(&$step, &$erreur, $import_link, $import_form_link){
	$table = array('spip_auteurs','spip_auteurs_elargis');
	$retour = urldecode(_request('retour'));
	$file_name = _request('file_name');
	$tmp_name = _request('tmp_name');
	$size = _request('size');
	$type = _request('type');
	$delim = _request('delim');
	$head = _request('head');
	$ajouter = _request('ajouter');
	$assoc_field = _request('assoc_field');
	$apercu = _request('apercu');
	
	if ($step==3){
		if (_request('annule_action'))
			$step--;
		else if ($apercu!=NULL)
		  	$step--;
	}

	if ($step==3){
		if ( (!$file_name)||(!$tmp_name)||(!$size)||(!$type) )
			 $erreur[$step][] = _L("Fichier absent");
		if (!$delim)
			 $erreur[$step][] = _L("Delimiteur non d&eacute;fini");
		if (!count($assoc_field))
			 $erreur[$step][] = _L("Correspondances CSV-Table non d&eacute;finies");
		if (isset($erreur[$step])){
			$step--;
		} 
	}
	
	if ($step==3){
		if (!$head) $head = false;
		$charger_csv = charger_fonction('importer_csv','inc');
		$data = $charger_csv($tmp_name, $head, $delim);
		if ($data==false) {
		  $erreur[$step][] = _L("Fichier vide");
		}
		$table_fields = i2_import_table_fields($table);
		$new_assoc=i2_import_field_associate($data, $table_fields, $assoc_field);
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
			$hidden["assoc_field[".i2_import_nettoie_key($key)."]"] = $value;
		$hidden["delim"] = $delim;
		$hidden["head"] = $head;

		if (($ajouter)&&(!_request('confirme_ajoute'))){
			$hidden['ajouter'] = 'oui';
			$titre = _T('i2_import:derniers_utilisateurs',array('nb' => 5));
			echo "<div class='entete-formulaire'>";
			echo gros_titre($titre,'',false);
			echo "</div>";
			debut_cadre_relief($icone);
			echo '<div style="width=100%;overflow:auto">';
			// Extrait de la table en commençant par les dernieres maj
			i2_import_table_visu_extrait($table,5);
			echo '</div>';
			fin_cadre_relief();
			
			echo "<div class='formulaire_spip' id='step'>";			
			echo $import_form_link;
			echo "<p class='formulaire_erreur'>";
			echo i2_import_show_erreurs($erreur);
			echo "</p>";
			echo "<p class='explication'>"._L("Les donn&eacute;es du fichier CSV vont &ecirc;tre ajout&eacute;es &agrave; la table comme illustr&eacute; ci-dessus.")."</p>";
			echo '<div style="width=100%;overflow:auto">';
			echo i2_import_array_visu_assoc($data, $table_fields, $assoc_field, 5);
			echo '</div>';
			
			foreach($hidden as  $key=>$value)
				echo "<input type='hidden' name='$key' value='$value' />";
			echo "<p class='boutons'>";
			echo '<input type="submit" name="annule_action" value="'._T('i2_import:revenir_etape',array('step'=>($step-1))).'" class="submit" />';
			echo "<input type='submit' name='confirme_ajoute' value='"._L('Ajouter les donn&eacute;es')."' class='submit' />";
			echo "</p>";
			echo "</div></form>";
 		}
		elseif ($ajouter){
			list($erreurs,$auteurs_ajoutes) = i2_import_ajoute_table_csv($data, $table, $assoc_field,$erreurs);
			$titre = _T('i2_import:derniers_utilisateurs',array('nb' => 10));
			echo "<div class='entete-formulaire'>";
			echo gros_titre($titre,'',false);
			echo "</div>";
			debut_cadre_relief($icone);
			// Extrait de la table en commençant par les dernieres maj
			echo '<div style="width=100%;overflow:auto">';
			i2_import_table_visu_extrait($table,10);
			echo '</div>';
			fin_cadre_relief();

			echo "<div class='formulaire_spip' id='step'>";
			echo $import_form_link;
			echo "<ul>";
			
			if (count($erreurs)){
				echo "<li>";
				echo "<p>"._T('i2_import:total_erreurs',array('nb'=>count($erreurs)))."</p>";
				echo "<div>";
				echo i2_import_show_erreurs($erreurs);
				echo "</div>";
				echo "</li>";
			}
			
			if(count($auteurs_ajoutes)){
				echo "<li>";
				echo "<p>"._T('i2_import:total_ajouts',array('nb'=>count($auteurs_ajoutes)))."</p>";
				echo "<div>";
				echo i2_import_show_imports($auteurs_ajoutes);
				echo "</div>";
				echo "</li>";
			}else{
				foreach($hidden as  $key=>$value)
					echo "<input type='hidden' name='$key' value='$value' />";
				echo "<p class='boutons'>";
				echo '<input type="submit" name="annule_action" value="'._T('i2_import:revenir_etape',array('step'=>($step-1))).'" class="submit" />';
				echo "</p>";
			}
			echo "</ul>";
			echo "</div></form>";
		}
	}
}
function i2_import_step2(&$step, &$erreur, $import_link, $import_form_link){
	$table = array('spip_auteurs','spip_auteurs_elargis');
	$retour = urldecode(_request('retour'));
	$file_name = _request('file_name');
	$tmp_name = _request('tmp_name');
	$size = _request('size');
	$type = _request('type');
	$delim = _request('delim');
	$head = _request('head');
	$ajouter = _request('ajouter');
	$assoc_field = _request('assoc_field');
	$apercu = _request('apercu');
	
	if ($step==2){
		if (!isset($_FILES))
			$erreur[$step][] = _L("Probl&egrave;me inextricable...");
		if ((!isset($_FILES['csvfile']))&&( (!$file_name)||(!$tmp_name)||(!$size)||(!$type)))
			 $erreur[$step][] = _L("Probl&egrave;me lors du chargement du fichier");
		if ((isset($_FILES['csvfile']))&&($_FILES['csvfile']['error']!=0))
			$erreur[$step][]=_T("i2_import:probleme_upload",array('erreur'=>$_FILES['csvfile']['error']));
		if (_request('annule_action'))
		  	$erreur[$step][]=_T("i2_import:annulation_action");
		if (isset($erreur[$step])) $step--;
		
	}
	if ($step==2){
		if (!$head) $head = false;

		// Pre traitement du CSV :
		// - on le déplace dans tmp/session
		// - On garde en mémoire certaines informations
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
		$charger_csv = charger_fonction('importer_csv','inc');
		$data = $charger_csv($tmp_name, $head, $delim);
		if ($data==false) {
			$erreur[$step][] = _T("i2_import:fichier_vide");
			$step--;
		}
	}
	
	$table_fields = i2_import_table_fields($table);
	
	if ($data && ($step==2))
		$assoc_field=i2_import_field_associate($data, $table_fields, $assoc_field);
	if ($step==2){
		$hidden['file_name'] = $file_name;
		$hidden['tmp_name'] = $tmp_name;
		$hidden['size'] = $size;
		$hidden['type'] = $type;
		$hidden['step'] = 3;

		echo "<div class='entete-formulaire' id='step'>";
		echo gros_titre(_T('i2_import:previsualisation'),'',false);
		echo "</div>";
		
		echo "<div class='formulaire_spip'>";
		echo $import_form_link;
		foreach($hidden as  $key=>$value)
			echo "<input type='hidden' name='$key' value='$value' />";

		echo "<p class='formulaire_erreur'>";
		echo i2_import_show_erreurs($erreur);
		echo "</p>";
		
		echo "<ul>";
		echo "<li><label for='separateur'>"._T("i2_import:separateur")."</label> ";
		echo "<input type='text' name='delim' id='separateur' class='text' style='width:2em;' maxlength='1' value='$delim' /></li>";
		echo "<li><label for='entete'>"._T("i2_import:ligne_entete")."</label>";
		echo "<input type='checkbox' name='head' id='entete' class='fondl' style='width:2em;' value='true'";
		if ($head==true)
		  echo " checked='checked'";
		echo " /></li>";

		echo i2_import_field_configure($data, $table_fields, $assoc_field);
		echo "<li class='boutons'><input type='submit' name='apercu' value='"._T('i2_import:previsualiser')."' class='submit' /></li>";
		
		echo "<li class='fieldset'><fieldset>";
		echo "<h3 class='legend'>"._T('i2_import:previsualisation')."</h3>";
		echo "<div style='width:100%;margin-top:15px;overflow:auto'>";
		echo i2_import_array_visu_assoc($data, $table_fields, $assoc_field, 5);
		echo "</div>\n";
		echo "</fieldset></li>";
		
		echo "</ul>";
		
		echo "<p class='boutons'>";
		echo '<input type="submit" name="annule_action" value="'._T('i2_import:revenir_etape',array('step'=>($step-1))).'" class="submit" />';
		echo "<input type='submit' name='ajouter' value='"._T('i2_import:ajouter_auteurs')."' class='submit' />";
		echo "</p>\n";

		echo "</div></form>";
		echo "</div>";
	}
}
function i2_import_step1(&$step, &$erreur, $import_link, $import_form_link){
	$table = array('spip_auteurs','spip_auteurs_elargis');
	$retour = urldecode(_request('retour'));
	$file_name = _request('file_name');
	$tmp_name = _request('tmp_name');
	$size = _request('size');
	$type = _request('type');
	$delim = _request('delim');
	$head = _request('head');
	$ajouter = _request('ajouter');
	$assoc_field = _request('assoc_field');
	$apercu = _request('apercu');

	if ($step==1){
		
		$hidden['head'] = 'true';
		$hidden['step'] = 2;
		
		echo "<div class='entete-formulaire' id='step'>";
		echo gros_titre(_T('i2_import:import_fichier'),'',false);
		echo "</div>";
		
		echo "<div class='formulaire_spip'>";
		echo "\n\n<form action='$import_link' method='post' enctype='multipart/form-data'><div>";
		foreach($hidden as  $key=>$value)
			echo "<input type='hidden' name='$key' value='$value' />";
		echo "<p class='formulaire_erreur'>";
		echo i2_import_show_erreurs($erreur);
		echo "</p>";
		echo "\n\n<ul><li>";
		echo "\n<label for='file_name'>"._T("i2_import:fichier_a_importer")."</label>";
		echo "\n<input type='file' name='csvfile' id='file_name' class='file' />";
		echo "\n</li></ul>";
		echo "\n\n<p class='boutons'>";
		echo "\n<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='submit' />";
		echo "</p>";
		echo "</div></form></div>\n";
	}
}

function exec_i2_import(){
	// On doit etre Webmestre pour acceder a cette page
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	global $spip_lang_right;
	$assoc_field=array();
	$retour = urldecode(_request('retour'));
	$step = _request('step');
	$file_name = _request('file_name');
	$tmp_name = _request('tmp_name');
	$size = _request('size');
	$type = _request('type');
	$delim = _request('delim');
	$head = _request('head');
	$ajouter = _request('ajouter');
	$assoc_field = _request('assoc_field');
	$apercu = _request('apercu');
	$table = array('spip_auteurs','spip_auteurs_elargis');
	
	if (!$step)
		$step = 1;

	$operations = array();

	$import_link = generer_url_ecrire("i2_import",$retour ? "retour=".urlencode($retour)."#step" : "#step");
		
	include_spip('inc/filtres');
	$action = generer_url_ecrire('i2_import', '#step');
	$import_form_link = "\n<form action='".$action."#step' method='post'><div>"
	.form_hidden($action);
	
	$clean_link = $import_link;
	
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('i2_import:i2_import_titre'));
	
	pipeline('exec_init',array('args'=>$_GET,'data'=>''));
	
	echo gros_titre(_T('i2_import:i2_import_gros_titre'),'',false);
	echo debut_gauche('',true);
	
	echo debut_boite_info(true);
	echo _T('i2_import:exec_texte_boite_info');
	echo fin_boite_info(true);
	
	echo pipeline('affiche_gauche',array('args'=> array('exec' => 'i2_import'),'data'=>''));	
	
	echo creer_colonne_droite(true);
	
	echo pipeline('affiche_droite',array('args'=> array('exec' => 'i2_import'),'data'=>''));
	
	echo debut_droite('',true);
		
		$hidden = array();
		// --- STEP 3 => Incorporation a la base de donnee
		i2_import_step3($step, $erreur, $import_link, $import_form_link);
		if ($step<3) {
			$titre = _T('i2_import:derniers_utilisateurs',array('nb' => 5));
			echo "<div class='entete-formulaire'>";
			echo gros_titre($titre,'',false);
			echo "</div>";
			echo debut_cadre_relief($icone, true);
			echo '<div style="width=100%;overflow:auto">';
			// Extrait de la table en commençant par les dernieres maj
			echo i2_import_table_visu_extrait($table,5);
			echo '</div>';
			echo fin_cadre_relief();
	 	}
		//
		// Icones retour
		//
		if ($retour) {
			echo "<div>";
			icone(_T('icone_retour'), $retour, $icone, "rien.gif");
			echo "</div>\n";
		}
	
		// --- STEP 2
		echo i2_import_step2($step, $erreur, $import_link, $import_form_link);
	
		// --- STEP 1
		echo i2_import_step1($step, $erreur, $import_link, $import_form_link);

	echo fin_gauche(),fin_page();
}
?>