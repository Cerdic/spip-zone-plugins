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
  
  $img_extension = explode('|',_SE_EXTENSIONS_IMG);

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
  $filename = _request('f');

  $out .= gros_titre(_T('skeleditor:editer_skel'),'',false);
  $out .= debut_gauche('', true);
  $out .= debut_boite_info(true)
	  ._T('skeleditor:skeleditor_description')
	  ."<p>"._T("skeleditor:skeleditor_dossier")
	  ." <strong>$dossier_squelettes</strong></p>"
	  .skeleditor_afficher_dir_skel($files_editable,$filename,$img_extension)
	  .skeleditor_addfile($path_list)
	  .skeleditor_uploadfile($path_list);
	
  $out .= fin_boite_info(true);
  
	$out .=  debut_droite('', true);

	echo $out;
	
  // pied
	echo recuperer_fond('prive/editer/squelette',array('fichier'=>$filename));
  echo fin_gauche(), fin_page();
}
?>