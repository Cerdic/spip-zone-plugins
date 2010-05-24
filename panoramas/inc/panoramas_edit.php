<?php

// compatibilite trans 1.9.1-1.9.2
// Cadre formulaires
// http://doc.spip.org/@debut_cadre_formulaire
function Panoramas_debut_cadre_formulaire($style='', $return=false){
	$x = "\n<div class='cadre-formulaire'" .
	  (!$style ? "" : " style='$style'") .
	   ">";
	if ($return) return  $x; else echo $x;
}

// http://doc.spip.org/@fin_cadre_formulaire
function Panoramas_fin_cadre_formulaire($return=false){
	if ($return) return "</div>\n"; else echo "</div>\n";
}







// http://doc.spip.org/@naviguer_doc
function naviguer_doc ($id, $type = "article", $script, $flag_editable) {
	global $spip_lang_left;

	if ($GLOBALS['meta']["documents_$type"]!='non' AND $flag_editable) {

	  $joindre = charger_fonction('joindre', 'inc');
	  $res = debut_cadre_relief("image-24.gif", true, "", _T('titre_joindre_document'))
	  . $joindre($script, "id_$type=$id", $id, _T('info_telecharger_ordinateur'), 'document', $type,'',0,generer_url_ecrire("documenter","id_rubrique=$id&type=$type",true))
	  . fin_cadre_relief(true);

	// eviter le formulaire upload qui se promene sur la page
	// a cause des position:relative incompris de MSIE

	  if (!($align = $GLOBALS['browser_name']=="MSIE")) {
		$res = "\n<table width='50%' cellpadding='0' cellspacing='0' border='0'>\n<tr><td style='text-align: $spip_lang_left;'>\n$res</td></tr></table>";
		$align = " align='right'";
	  }
	  $res = "<div$align>$res</div>";
	      $res .= "<script src='"._DIR_JAVASCRIPT."async_upload.js' type='text/javascript'></script>\n";
    $res .= <<<EOF
    <script type='text/javascript'>
    $(".form_upload").async_upload(async_upload_portfolio_documents);
    </script>
EOF;
	} else $res ='';

	$documenter = charger_fonction('documenter', 'inc');

	return "<div id='portfolio'>".$documenter($id, $type, 'portfolio', $flag_editable)."</div>"
	."<div id='documents'>". $documenter($id, $type, 'documents', $flag_editable)."</div>"
	. $res;
}
function panorama_afficher_bloc_document($intitule_document, $element_panorama="lieu", $id_document=0) {
	if (!sql_fetch(sql_select('*', 'spip_documents', "id_document=".$id_document))) $id_document = 0;
 	$out = '';
	$out .= "<strong><label for='".$intitule_document."_lieu'>"._T("panoramas:".$intitule_document)."</label></strong> ";
	
	if ($id_document) $out .= "[<a href='#' id='".$intitule_document."_lieu_changer'>"._T("panoramas:associer_autre_document")."</a>]
	<script type='text/javascript'>
	      $(document).ready(function(){
		  $('#".$intitule_document."_lieu_changer').bind('click', function(){
			  $('#".$intitule_document."_lieu').removeClass('invisible');
			  $('#".$intitule_document."_lieu').val('0');
			  $('#".$intitule_document."_lieu_galerie').remove();
			  return false;
		  });
	      });
	</script>

	";
	$out .= "<br class='nettoyeur' />";
	
	$out .= "<input ";
	
	if ($id_document) $out .= "class='invisible'";
      
	$out .= "type='text' name='".$intitule_document."' id='".$intitule_document."_lieu' class='formo $focus' ".
		"value=\"".$id_document."\" size='5' /><br />\n";
	
	
	if ($id_document) {
	    $out .= "<div id='".$intitule_document."_lieu_galerie'>";
	    $out.= "<div class='invisible'>".formulaire_recherche('portfolio')."</div>";
	    $contexte = array_merge(array('id_document'=>$id_document),$_GET);
	    $out .= recuperer_fond('prive/inc-panorama-galerie',$contexte);
	    $out .= "</div>";
	}
	
	$out .= "<br class='nettoyeur' />";
	return $out;
}


?>