<?php
		 $p = explode(basename(_DIR_PLUGINS)."/", str_replace('\\','/',realpath(dirname(__FILE__))));
		 define('_DIR_PLUGIN_ODT2SPIP',(_DIR_PLUGINS.end($p)));

function odt2spip_affiche_droite($flux){
	global $spip_version_code;
  $args = $flux['args'];
	$out = "";
  $id_rubrique = $args['id_rubrique'];
	if ($args['exec']=='naviguer' AND $args['id_rubrique'] AND $args['id_rubrique'] != 0) {
    $retour_final = ($spip_version_code > 2 ? './' : _DIR_RESTREINT_ABS).'?exec=articles&id_article=';
//    $retour = generer_action_auteur('snippet_importe',"articles:articles:id_rubrique=$id_rubrique:$fichier_odt2spip",$retour_final);
    $action = generer_action_auteur('odt2spip_importe',"id_rubrique=$id_rubrique",$retour_final);
    $out .= icone_horizontale(_T("odtspip:importer_fichier"), "#", "", _DIR_PLUGIN_ODT2SPIP."images/odt-24.png", false, "onclick='$(\"#boite_odt2spip\").slideToggle(\"fast\");'");
    $out .= "<div id='boite_odt2spip' style='display:none;' >\n";
    $out .= debut_cadre_relief('',true);
    $out .= "<form action='$action' method='POST' enctype='multipart/form-data'>";
    $out .= form_hidden($action);
    $out .= "<strong><label for='id_article'>"._T("odtspip:choix_fichier")."</label></strong> ";
    $out .= "<br />";
    $out .= "<input type='file' name='fichier_odt' id='fichier_odt' class='formo' style='font-size: 11px;' />";
    $out .= "<br /><small>";
    $out .= "<strong>"._T("odtspip:attacher_fichier_odt")."</strong> ";
    $out .= "<label for='attacher_oui'>"._T("odtspip:oui")."</label>";
    $out .= "<input type='radio' name='attacher_odt' value='1' id='attacher_oui' checked='checked'/>";
    $out .= "<input type='radio' name='attacher_odt' value='0' id='attacher_non'/>";
    $out .= "<label for='attacher_non'>"._T("odtspip:non")."</label>";
    $out .= "<br /><br /><strong>"._T("odtspip:images_mode_document")."</strong><br />";
    $out .= "<label for='mode_image'>"._T("odtspip:mode_image")."</label>";
    $out .= "<input type='radio' name='mode_image' value='image' id='mode_image' checked='checked'/>";
    $out .= "<input type='radio' name='mode_image' value='document' id='mode_document'/>";
    $out .= "<label for='mode_document'>"._T("odtspip:mode_document")."</label>";    
    $out .= "<br /><br /><strong>"._T("odtspip:langue_publication").": </strong>";
    $out .= "<select name='lang_publi' id='lang_publi' style='font-size:1em;'>";
    $Tlangs = explode(',',$GLOBALS['meta']['langues_proposees']);
    foreach($Tlangs as $lang) $out .= "<option value='".$lang."'".($lang==$GLOBALS['meta']['langue_site']?" selected='selected'":"").">".$lang."</option>";   
    $out .= "</select>";
    $out .= "</small><br />";
    $out .= "<div style='text-align:$spip_lang_right'>";
    $out .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
    $out .= "</div>";
    $out .= "</form>\n";
	  $out .= fin_cadre_relief(true)."</div>";
	}
	$flux['data'].= $out;
	return $flux;
}

?>
