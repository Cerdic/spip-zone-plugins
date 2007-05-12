<?php

// ICI : Commenter l'une des deux lignes pour fixer l'éditeur par défaut
    define('EDITEUR_PAR_DEFAUT', 'wysiwyg');
    //define('EDITEUR_PAR_DEFAUT', 'text');

// détermination du chemin de base par rapport à la racine du serveur
  $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\',/*'*/'/',realpath(dirname(__FILE__))));
  define('_DIR_RELATIF_PLUGIN_FCKEDITOR',str_replace('../','',(_DIR_PLUGINS.end($p))));
    
  $dir_relatif_array = split('/', $_SERVER["PHP_SELF"]);
  $i = 0;
  while($dir_relatif_array[$i] != 'ecrire') 
    {
  	 $dir_relatif .= $dir_relatif_array[$i]."/";
  	 $i++;
    }
//if($dir_relatif != '') $dir_relatif = "/".$dir_relatif;

define('_DIR_PLUGIN_FCKEDITOR',$dir_relatif._DIR_RELATIF_PLUGIN_FCKEDITOR);

function fckeditor2_header_prive($flux) {
	global $exec;

	$code='';

	if($exec=='articles' || $exec=='breves_voir') {

		if(EDITEUR_PAR_DEFAUT=='text') {
			$code='
	$("img[@src*=\'edit.gif\']").parent().each(function(i){
		$(this).before(this.cloneNode(true));
		$(this).attr("href", $(this).attr("href")+"&wysiwyg=oui");
		$(this).find("span").html(\''.addslashes(_T('fckeditor:icone_modifier_article_fck')).'\');
	});';
		} else {
			$code='
	$("img[@src*=\'edit.gif\']").parent().each(function(i){
		$(this).before(this.cloneNode(true));
		$(this).attr("href", $(this).attr("href")+"&wysiwyg=non");
		$(this).find("span").html(\''.addslashes(_T('fckeditor:icone_modifier_article_spip')).'\');
	});';
		}

	} elseif($exec=='articles_edit' && ( $GLOBALS['wysiwyg']=='oui'
		 || (EDITEUR_PAR_DEFAUT=='wysiwyg' && $GLOBALS['wysiwyg']!='non') ) ) {

		$code='
			var oFCKeditor = new FCKeditor( "text_area" ) ;
			oFCKeditor.BasePath = "'._DIR_PLUGIN_FCKEDITOR.'/FCKeditor/" ;
			oFCKeditor.Config["CustomConfigurationsPath"] = "'._DIR_PLUGIN_FCKEDITOR.'/spip_fck/fckconfig.php?path='._DIR_PLUGIN_FCKEDITOR.'&" + ( new Date() * 1 ) ;
			oFCKeditor.Config[ "AutoDetectLanguage" ] = false ;
			oFCKeditor.Config[ "DefaultLanguage" ] = "'.$GLOBALS['_COOKIE']['spip_lang_ecrire'].'" ;
			oFCKeditor.Height = "600";
    	oFCKeditor.ToolbarSet = "Spip";
			oFCKeditor.ReplaceTextarea();
			$(".spip_barre").remove();
		';

	} elseif($exec=='breves_edit' && ( $GLOBALS['wysiwyg']=='oui'
		 || (EDITEUR_PAR_DEFAUT=='wysiwyg' && $GLOBALS['wysiwyg']!='non') ) ) {

		$code='
    	var oFCKeditor = new FCKeditor( \'texte\' ) ;
    	oFCKeditor.BasePath = "'._DIR_PLUGIN_FCKEDITOR.'/FCKeditor/" ;
			oFCKeditor.Config["CustomConfigurationsPath"] = "'._DIR_PLUGIN_FCKEDITOR.'/spip_fck/fckconfig.php?path='._DIR_PLUGIN_FCKEDITOR.'&" + ( new Date() * 1 ) ;
			oFCKeditor.Config[ "AutoDetectLanguage" ] = false ;
			oFCKeditor.Config[ "DefaultLanguage" ] = "'.$GLOBALS['_COOKIE']['spip_lang_ecrire'].'" ;    	
    	oFCKeditor.Height = "600";
    	oFCKeditor.ToolbarSet = "BarreBreve";   
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
