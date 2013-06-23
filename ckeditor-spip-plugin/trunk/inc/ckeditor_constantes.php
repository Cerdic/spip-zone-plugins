<?php

include_spip('inc/config') ;
include_spip('inc/filtres_mini') ;
include_spip('inc/ckeditor_lire_config') ;
include_spip('inc/ckeditor_constantes_inc') ;

define('_CKE_DEVTOOLS_DEF', false) ;
define('SPIP_SITE', isset($GLOBALS['table_prefix'])?$GLOBALS['table_prefix']:'spip' );
define('_CKE_JS', find_in_path('lib/ckeditor/ckeditor.js')) ;
$cke_adapter = find_in_path('lib/ckeditor/adapters/jquery.js') ;
if (file_exists($cke_adapter)) {
	define('_CKE_JQUERY', $cke_adapter) ;
} else {
	// on prépare le terrain pour ckeditor 4.x (les premiers paquetage ne contiennent pas l'adapter jquery
	define('_CKE_JQUERY', find_in_path('adapters/jquery.js')) ;
}
define('_CKE_HTML2SPIP_VERSION', 'html2spip-0.6') ;
define('_CKE_PATH', dirname(_CKE_JS)) ;
define('_CKE_LARGE_DEF', 490 ) ;
define('_CKE_ETROIT_DEF', 460 ) ;
define('_CKE_LARGE', ckeditor_lire_config("cktoolslenlarge",_CKE_LARGE_DEF) ) ;
define('_CKE_ETROIT', ckeditor_lire_config("cktoolslenetroit",_CKE_ETROIT_DEF) ) ;
define('_CKE_MAXSIZETOOLS', ($_COOKIE['spip_ecran']=='large'?_CKE_LARGE:_CKE_ETROIT)) ;
define('_CKE_PREFERED_VERSION', '3.6.5') ;
define('_CKE_RACINE_REGEX', '#^'.preg_quote(_DIR_RACINE, '#').'#') ;
define('_CKE_FONTKIT', _DIR_IMG."FontKits") ;
define('_CKE_DIR_SMILEYS', url_absolue(find_in_path("img/smileys/"))) ;
define('_CKE_GOOGLE_WEBFONT', 'http://fonts.googleapis.com/css?family=' ) ;
define('_CKE_DOUBLE_PONCTUATION', ';:!?' ) ;
define('_CKE_DIR_UPLOAD_DEF', _DIR_IMG.'UserFiles' ) ;
define('_CKE_IMAGES_UPLOAD_DEF', 'Images' ) ;
define('_CKE_FLASH_UPLOAD_DEF', 'Flash' ) ;
define('_CKE_FILES_UPLOAD_DEF', 'Files' ) ;
define('_CKE_IMAGES_EXT_DEF','jpeg; jpg; gif; png' ) ; 
define('_CKE_FLASH_EXT_DEF','swf; flv' ) ;
define('_CKE_FILES_EXT_DEF','txt; csv' ) ;
define('_CKE_ENTERMODE_DEF', 'ENTER_P' ) ;
define('_CKE_SHIFTENTERMODE_DEF', 'ENTER_BR' ) ;
define('_CKE_LANGAGE_DEF', 'auto' ) ;
define('_CKE_HAUTEUR_DEF', 500 ) ;
define('_CKE_VIGNETTE_DEF', 200 ) ;
define('_CKE_SKIN_DEF', 'kama' ) ;
define('_CKE_EDITMODE_DEF', 'ckeditor') ;
define('_CKE_SCAYT_START_DEF', true ) ;
define('_CKE_SCAYT_LANG_DEF', 'fr_FR' ) ;
define('_CKE_BARREOUTILS_DEF', serialize(array(array('About'))) );
define('_CKE_TAGS_DEF', serialize(array( // le serialize est obligatoire puisque qu'on ne peut définir de constante que scalaire
	'doc'=>array('intitule'=>_T('ckeditor:modele_document'),'info'=>_T('ckeditor:modele_document'),'type'=>'num-obligatoire','nombre'=>'documents','noms'=>array('alignement'),'valeurs'=>array('left|'._T('ckeditor:modele_a_gauche').';;right|'._T('ckeditor:modele_a_droite').';;center|'._T('ckeditor:modele_centre'))), 
	'emb'=>array('intitule'=>_T('ckeditor:modele_incorpore'),'info'=>_T('ckeditor:modele_incorpore'),'type'=>'num-obligatoire','nombre'=>'documents','noms'=>array('alignement'),'valeurs'=>array('left|'._T('ckeditor:modele_a_gauche').';;right|'._T('ckeditor:modele_a_droite').';;center|'._T('ckeditor:modele_centre'))), 
	'img'=>array('intitule'=>_T('ckeditor:modele_image'),'info'=>_T('ckeditor:modele_image'),'type'=>'num-obligatoire','nombre'=>'documents','noms'=>array('alignement'),'valeurs'=>array('left|'._T('ckeditor:modele_a_gauche').';;right|'._T('ckeditor:modele_a_droite').';;center|'._T('ckeditor:modele_centre')))
	))
) ;
//define('_CKE_HTML2SPIP_DEF', false ) ;
define('_CKE_SIZE',0);
define('_CKE_DEFAULT',1);
define('_CKE_COMPAT',2);
define('_CKE_ICON',3);
define('_CKE_PLUGIN',4);
define('_CKE_CONVERSION_DEF', 'partielle') ;
define('_CKE_HTML2SPIP_LIMITE_DEF', false ) ;
define('_CKE_HTML2SPIP_IDENTITE', 'script;embed;param;object') ;
define('_CKE_CACHE_REDIM_DEF', false ) ;
define('_CKE_SPIPLINKS_DEF', true ) ;
define('_CKE_INSERTALL_DEF', false ) ;
define('_CKE_PASTETEXT_DEF', true ) ;
define('_CKE_USE_DIRECTUPLOAD_DEF', true ) ;
define('_CKE_PARCOURS_DEF', true ) ;
define('_CKE_UPLOAD_DEF', true ) ;
define('_CKE_UPLOAD_REDAC_DEF', false ) ;
define('_CKE_MKDIR_DEF', true ) ;
define('_CKE_MKDIR_REDAC_DEF', false ) ;
define('_CKE_FORMATS_DEF', 'h3;pre' ) ;
define('_CKE_FORMATS_CLASS_DEF', 'spip' ) ;
define('_CKE_WEBFONTS_DEF', '' ) ;
define('_CKE_FONTKIT_DEF', true ) ;
define('_CKE_INSERT_CSSPUBLIC_DEF', true ) ;
define('_CKE_INSERT_CSSPRIVEE_DEF', true ) ;

define('_CKE_PRIVE_DEF', ".inserer_barre_edition\ntextarea[name=texte]"  ) ;
define('_CKE_PUBLIC_DEF', "#formulaire_forum textarea[name=texte]|basique\ntextarea.crayon-active" ) ;

define('_CKE_STYLES_DEF', 'Gras: strong.spip
Italique: i.spip
Intertitre: h3.spip
Noir: span { color: black; }
Rouge: span { color: red; }
Fond Noir: span { background-color: black; }
Fond Rouge: span { background-color: red; }' ) ;
define('_CKE_FONTSIZES_DEF', '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px' ) ;
define('_CKE_PLUGINSBARREPOSITION_DEF', _T('ckeditor:fin')) ;
define('_CKE_PLUGINSPOS_REF_DEF', 'About') ;
define('_CKE_IGNOREVERSION_DEF', false) ;
define('_CKE_MAX_ONGLETS', 4) ;

?>
