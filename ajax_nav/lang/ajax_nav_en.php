<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
'nom' => 'AJAX Navigation',
'trouver_doc' => 'For more infos about this plugin\'s configuration, please go to ',
'conf_plugin' => 'Plugin configuration :',
'url_prefix_titre' => 'The prefix of your site\'s urls. \'spip.php?\' for a default install :',
'pages_titre' => 'The types of pages that will be loaded asyncronously :',
'pages_expli' => 'a list of space-separated page types, like: "sommaire article rubrique"',
'ajax_divs_titre' => 'The ids of the elements to be loaded asynchronously :',
'ajax_divs_expli' => 'a list of space-separated ids, like : "contenu extra"',
'loc_divs_titre' => 'The ids of the elements to be reloaded only if the language changes :',
'loc_divs_expli' => 'a list of space-separated ids, like : "menu navigation"',
'html4' => 'Use hash urls with old browsers :',
'use_modern_lib' => 'Use the provided Modernizr library : ',
'use_history_lib' => 'Use the provided History.js library : ',
);

?>