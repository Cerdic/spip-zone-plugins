<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt
#
/**
 * A filepicker
 */
if (!autoriser('acs', 'filepicker')) {
  echo _T('avis_non_acces_page');
  exit;
}

function url_filepicker($dir, $file, $args = false) {
  return '?action=filepickerwrapper&dir='.$dir.'&file='.$file.(($args==true) ? $args : '');
}
$action_effacer = url_filepicker($dir, $file, '&del=true'); 

if (isset($_POST['dir']) && isset($_POST['file'])) {
  $file = $_POST['file'];
  $dir = $_POST['dir'];
  if (isset($_POST['sousaction1'])) {
    $tmp = $_FILES["fichier"]["tmp_name"];
    $dest = $dir.'/'.$_FILES["fichier"]["name"];
    include_spip('inc/getdocument');
    deplacer_fichier_upload($tmp, $dest);
    if (is_readable($dest)) $file = $_FILES["fichier"]["name"];
  }
}
else {
  $file = $_GET['file'];
  $dir = $_GET['dir'];
  $del = $_GET['del'];
  $hash = $_GET['hash'];
  // Efface l'image après quelques vérifications ... ;-)
  if (isset($dir) && $dir && isset($file) && $file && isset($del) && $del) {
    $hashdel = md5(serialize($action_effacer.$GLOBALS['auteur_session']['hash_env']));
    if($hash==$hashdel) {
     if (!@unlink($dir.'/'.$file)) echo '<div class="alert">'._T('acs:err_del_file').'</div>';
    }
    else {
    	echo '<div class="alert">'._T('acs:err_del_file').'</div>';
    }
  }
}

echo '<html><head><title>'._T('choix_image').'</title>
<link rel="stylesheet" type="text/css" href="?page=style_prive.css" />
<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_ACS.'inc/picker/filepicker.css" />
</head><body>
<script 	language="JavaScript">
var P = opener.TFP;
P.draw(window, document);

function aconfirmer(msg, href) {
  if(confirm(msg)) {
    this.location = href;
  }
}
</script>';

// boutonImg and add image
include_spip('inc/presentation');
$ret .= debut_cadre_relief("image-24.gif", true, "creer.gif", _T('medias:bouton_ajouter_image').aide("ins_img"));
$ret.= '<form method="post" action="" enctype="multipart/form-data" class="form_upload">';
$ret.= '<input type="hidden" name="file" value="'.$file.'">';
$ret.= '<input type="hidden" name="dir" value="'.$dir.'">';
$ret.= "<input name='fichier' type='file' class='forml spip_xx-small' size='15' />"
. "\n\t\t<div align='$spip_lang_right'><input name='sousaction1' type='submit' value='"
. _T('bouton_telecharger')."' class='fondo' /></div>";
$ret.= '</form>';
$ret .= fin_cadre_relief();
echo '<table style="background: #dfdfdf"><tr><td width="80%"><div align="center"><img id="selection" src="'.$dir.'/'.$file.'" title="Sélection" class="selection" alt="" /></div></td><td><div align="right" style="margin-right: 10px;">'.$ret.'</div></td></tr></table>';

// Show gallery
if ($d = @opendir($dir)) {
	while (false !== ($file = @readdir($d))) {
  	if (is_file($dir.'/'.$file) && ($file != ".") && ($file != "..")) {
      $s = @getimagesize($dir.'/'.$file);
      if ($s)
      	$s = $s[0].'x'.$s[1];
      $hash = md5(serialize($action_effacer.$GLOBALS['auteur_session']['hash_env']));
      $txt_effacer = htmlentities(_T('acs:effacer_image', array("image" => $dir.'/'.$file)));
      $onclick = "aconfirmer('".$txt_effacer."','".url_filepicker($dir, $file, '&del=true')."&hash=$hash"."')";
			echo '<table class="cadre" style="width: auto"><tr><td colspan="2" style="text-align: center"><img src="'.$dir.'/'.$file.'" title="'.$file.'" class="boutonImg" onclick="P.select(\''.$file.'\')" onmouseover="P.preview(\''.$dir.'/'.$file.'\')" onmouseout="P.preview(\'\')" /></td></tr><tr><td class="bandeau">'.$s.'</td><td><a onclick="'.$onclick.'" title="'.$txt_effacer.'"><img src="'._DIR_PLUGIN_ACS.'images/supprimer.gif" alt="x" /></a></td></tr></table>';
		}
	}
	closedir($d);
}
else {
	echo '<br />'._T('acs:impossible_ouvrir_dossier').' "'.$dir.'"';
}
echo '</html>';
?>
