<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\',/*'*/'/',realpath(dirname(dirname(__FILE__)))));//**********
define('_DIR_PLUGIN_FCKEDITOR',(_DIR_PLUGINS.end($p)));//**********

function fckeditor2_header_prive($flux) {
	global $exec;
	if($exec=='articles') {
		$code='
  <script src="http://www.jquery.info/scripts/jquery-1.0.js" type="text/javascript"></script>
<script type="text/javascript"><!--
$(document).ready(function () {
	$("img[@src=\'img_pack/edit.gif\']").parent().each(function(i){
		$(this).before(this.cloneNode(true));
		$(this).attr("href", $(this).attr("href")+"&wysiwyg=oui");
		$(this).find("span").html(\''.addslashes(_T('fckeditor:icone_modifier_article_fck')).'\');
	});
});
// --></script>
';
		return $flux.$code;
	}

	if($exec=='articles_edit' && $GLOBALS['wysiwyg']=='oui') {
		$code='
<script type="text/javascript" src="'._DIR_PLUGIN_FCKEDITOR.'/FCKeditor/fckeditor.js"></script>
<script type="text/javascript"><!--
$(document).ready(function () {

	var oFCKeditor = new FCKeditor( \'text_area\' ) ;
	oFCKeditor.BasePath = "'._DIR_PLUGIN_FCKEDITOR.'/FCKeditor/" ;
	oFCKeditor.Height = "400";
	oFCKeditor.ReplaceTextarea();

	$(".spip_barre").remove();
});
//-->

</script>
';
		return $flux.$code;
	}

	return $flux;
}

//	$texte = html_entity_decode(entites_html($row['texte']));

?>
