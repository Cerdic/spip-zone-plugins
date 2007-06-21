<?php

/********** variables de config à modifier en fonction des versions de TinyMCE souhaitées ********************/

define('_PLUGIN_TINYMCE_ARCHIVE_URL', 'http://heanet.dl.sourceforge.net/sourceforge/tinymce/tinymce_2_1_0.zip'); //à changer pour avoir la nouvelle version
define('_PLUGIN_TINYMCE_LANGUAGES_URL', 'http://tinymce.moxiecode.com/language.php'); //normalement ne devrait pas changer
$_PLUGIN_TINYMCE_LANGUAGES_PACK = array('fr'); //ajouter toutes les langues qu'on souhaite ex. : array('fr', 'pl')




/********** ne pas modifier les variables suivantes ********************/

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_TINYMCE',(_DIR_PLUGINS.end($p)));

define('_DIR_TINYMCE_FILES', _DIR_PLUGIN_TINYMCE.'/tinymce/jscripts/tiny_mce');
define('_PLUGIN_TINYMCE_LANGUAGES_PACK_VARNAME', '_PLUGIN_TINYMCE_LANGUAGES_PACK');





?>