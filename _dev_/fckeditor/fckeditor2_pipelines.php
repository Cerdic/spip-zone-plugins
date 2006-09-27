<?php

// ICI : Commenter l'une des deux lignes pour fixer l'éditeur par défaut
//define('EDITEUR_PAR_DEFAUT', 'wysiwyg');
define('EDITEUR_PAR_DEFAUT', 'text');

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\',/*'*/'/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_FCKEDITOR',(_DIR_PLUGINS.end($p)));

function fckeditor2_header_prive($flux) {
	global $exec;

	$code='';

	if($exec=='articles' || $exec=='breves_voir') {

		if(EDITEUR_PAR_DEFAUT=='text') {
			$code='
	$("img[@src=\'img_pack/edit.gif\']").parent().each(function(i){
		$(this).before(this.cloneNode(true));
		$(this).attr("href", $(this).attr("href")+"&wysiwyg=oui");
		$(this).find("span").html(\''.addslashes(_T('fckeditor:icone_modifier_article_fck')).'\');
	});';
		} else {
			$code='
	$("img[@src=\'img_pack/edit.gif\']").parent().each(function(i){
		$(this).before(this.cloneNode(true));
		$(this).attr("href", $(this).attr("href")+"&wysiwyg=non");
		$(this).find("span").html(\''.addslashes(_T('fckeditor:icone_modifier_article_spip')).'\');
	});';
		}

	} elseif($exec=='articles_edit' && ( $GLOBALS['wysiwyg']=='oui'
		 || (EDITEUR_PAR_DEFAUT=='wysiwyg' && $GLOBALS['wysiwyg']!='non') ) ) {

		$code='
	var oFCKeditor = new FCKeditor( \'text_area\' ) ;
	oFCKeditor.BasePath = "'._DIR_PLUGIN_FCKEDITOR.'/FCKeditor/" ;
	oFCKeditor.Height = "400";
	oFCKeditor.ReplaceTextarea();

	$(".spip_barre").remove();
';

	} elseif($exec=='breves_edit' && ( $GLOBALS['wysiwyg']=='oui'
		 || (EDITEUR_PAR_DEFAUT=='wysiwyg' && $GLOBALS['wysiwyg']!='non') ) ) {

		$code='
	var oFCKeditor = new FCKeditor( \'texte\' ) ;
	oFCKeditor.BasePath = "'._DIR_PLUGIN_FCKEDITOR.'/FCKeditor/" ;
	oFCKeditor.Height = "400";
	oFCKeditor.ReplaceTextarea();

	$(".spip_barre").remove();
';
	}

	if(!empty($code)) {
		$code='
<script src="http://www.jquery.info/scripts/jquery-1.0.js" type="text/javascript"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_FCKEDITOR.'/FCKeditor/fckeditor.js"></script>
<script type="text/javascript"><!--
$(document).ready(function () {
'.$code.'
});
//-->

</script>';
		return $flux.$code;
	}

	return $flux;
}

//	$texte = html_entity_decode(entites_html($row['texte']));

?>
