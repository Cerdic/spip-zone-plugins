<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip('skeleditor_fonctions');
include_spip('inc/autoriser');
include_spip('inc/skeleditor');

function exec_skeleditor_dist(){
  
  global $spip_lang_right;
  $img_extension = explode('|',_SE_EXTENSIONS_IMG);
  $listed_extension = explode('|',_SE_EXTENSIONS);

  // check rights
  if (!autoriser('skeleditor')) {
		$commencer_page = charger_fonction('commencer_page', 'inc');
    echo $commencer_page(_T("skeleditor:editer_skel"),_T("skeleditor:editer_skel"),_T("skeleditor:editer_skel"));
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	
	$files_editable = skeleditor_files_editables();
	$path_list = array_keys(array_flip(array_map('dirname',$files_editable)));

  
  // ---------------------------------------------------------------------------
  // HTML output 
  // ---------------------------------------------------------------------------
	$commencer_page = charger_fonction('commencer_page', 'inc');
  $out = $commencer_page(_T("skeleditor:editer_skel"),_T("skeleditor:editer_skel"),_T("skeleditor:editer_skel"));
   
  $out .= gros_titre(_T('skeleditor:editer_skel'),'',false);
  $out .= debut_gauche('', true);
  $out .= debut_boite_info(true)._T('skeleditor:skeleditor_description')."<p>"._T("skeleditor:skeleditor_dossier")." <strong>$dossier_squelettes</strong></p>".skeleditor_afficher_dir_skel($files_editable,$file_name,$img_extension).skeleditor_addfile($path_list).skeleditor_uploadfile($path_list);
  $out .= fin_boite_info(true);
  
	$out .=  debut_droite('', true);

	echo $out;

	// something to do ?	
	/*
	 if ($file_name!="") {

       if ($safe_flag) {         
         $out .= "<div>"._T("skeleditor:fichier")."<strong>$file_name</strong> $log</div>\n"; // add extra infos on file:  size ? date ? ...
				 // tools bar
				 $out .= "<div id='skel_toolbar' style='width:100%;text-align:right;'>\n";

				 $out .= bouton_action("<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/action_dl.png' alt='download' />"._T("skeleditor:telecharger"), generer_action_auteur('skeleditor_dl', $file_name));
				 $out .= bouton_action("<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/action_del.png' alt='delete' />"._T("skeleditor:effacer"), generer_action_auteur('skeleditor_delete', $file_name, generer_url_ecrire('skeleditor')), '',_T("skeleditor:effacer_confirme"));
				 $out .= "</div>\n";
				 // img or text ?
				 $extension =  strtolower(substr($file_name, strrpos($file_name,".")+1));
				 if (in_array($extension,$img_extension)) {     // display file as img
						$out .= "<div style='border:1px solid #333;padding:20px;background:#eee'><img src='$file_name' alt='picture' /></div>\n";
						 list($width, $height) = @getimagesize($file_name);
						 $out .= "<small>$width x $height pixels</small>\n";
				 } else {  // edit file as text
						if ($file_tmp = @file("$file_name")) {
								$file_str = implode ('',$file_tmp);
								// FIXME pour l'instant on n'affiche plus le debug de boucle
								// if (($extension=='html') && (_request(debug)!='true')) $out .=  skel_parser($file_str); // experimental
								$file_str = str_replace("&","&amp;",$file_str); //  preserve html entities
								$file_str = str_replace("</textarea","&lt;/textarea",$file_str); // exception: textarea closing tag
								//$out .= generer_url_post_ecrire('skeleditor',"retour=skeleditor&f=".urlencode($file_name));
								$out .= "<form method='post' operation='?exec=skeleditor&f=".urlencode($file_name)."'>"; //FIX temporaire --> tout integrer ds CVT
								$out .= "<textarea name='editor' cols='80' rows='50'>$file_str</textarea>\n";
								$out .= "<div style='text-align:$spip_lang_right'><input type='submit' name='operation' value='"._T("skeleditor:sauver")."' class='fondo'></div>";
								$out .= "</form>\n";

						} else {
								$out .= "<p style='color:red'>"._T("skeleditor:erreur_ouvert_ecrit")."</p>\n";
						}
				 }
      } else { // security failure
        $out .= "<div style='color:red'>"._T('skeleditor:erreur_sansgene')."</div>\n";      
      }
  } else {
      $out .= "<p>"._T("skeleditor:fichier_choix")."</p>\n";
  }
  */
	
  // pied
	echo recuperer_fond('prive/editer/squelette',array('fichier'=>$_GET['f']));
  echo fin_gauche(), fin_page();
}
?>