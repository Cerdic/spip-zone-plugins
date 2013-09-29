<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/fbmodeles?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cf_navigation' => 'See [column navigation->@url@]',
	'cfg_comment_appid' => 'Identifier of an application specific to your website; requires that you create the application before.',
	'cfg_comment_border_color' => 'Enter a hexadecimal color code WITH the initial sharp.',
	'cfg_comment_colorscheme' => 'Select here the predetermined profile by modules that will be used for display.',
	'cfg_comment_font' => 'Here you can select the font to be used to display modules.',
	'cfg_comment_identifiants' => '{{Use the fields below to specify different identifiers you want to use.}} They are not mandatory, but can help us to track precisely statistics offered by Facebook.',
	'cfg_comment_pageid' => 'Identifier for a facebook page, it requires you have created the page.',
	'cfg_comment_reglages' => '{{Here you can choose some settings of the Facebook javascript tools.}} By default, the models use the language XFBML ({SDK Facebook JavaScript}) but you can disable this feature, the tools will be loaded into frames.',
	'cfg_comment_url_page' => 'Full URL of your page or Facebook profile; it will be used by default by the models (URL like "<code>http://www.facebook.com/...</ code>").',
	'cfg_comment_userid' => 'User(s) Identifier(s) for the plugins administrators. You can specify multiple ones separated by a comma. ',
	'cfg_comment_xfbml' => 'Use the Facebook SDK javascript library and associated language. If you choose "No", the modules will be displayed in iframe.',
	'cfg_descr' => 'Here you need to define the different identifiers provided by the Facebook system.<br /><br />More: [->http://www.facebook.com/insights/].

To include the tags "Open Graph" in the header of your public pages, you must include the model "insert_head_og" by passing the environment <code>#MODELE{insert_head_og}{env}</code>. 
<br /><br />More: [->http://developers.facebook.com/docs/opengraph/].',
	'cfg_descr_titre' => 'Facebook models',
	'cfg_identifiants' => 'Facebook logins',
	'cfg_label_appid' => 'Identifier "App ID" application ',
	'cfg_label_border_color' => 'Default border color',
	'cfg_label_colorscheme' => 'Color profile',
	'cfg_label_font' => 'Default font',
	'cfg_label_pageid' => 'Login "Page ID" page',
	'cfg_label_titre' => 'Configuration Facebook models',
	'cfg_label_url_page' => 'Page URL or profile',
	'cfg_label_userid' => 'Identifier "User ID" user',
	'cfg_label_xfbml' => 'XFBML use',
	'cfg_reglages' => 'Default settings',

	// D
	'defaut' => 'Default',
	'doc_chapo' => 'the plugin Models Facebook for SPIP 2.0 ({and more}) proposes a set of models, or hazelnuts, allowing quick and easy use of social plugins provided by Facebook.',
	'doc_en_ligne' => 'Documentation',
	'doc_titre_court' => 'Facebook models documentation',
	'doc_titre_page' => 'Documentation page of the Facebook Models plugin',
	'documentation' => '{{{Plugin use}}}

As shown above, the models are directly included from the desired options.

Each model has a list of options, some of which are necessary for its display. For a complete list, refer to the information in the header models files in the directory "<code>modeles/</ code>" in the plugin.

The plugin also provides a {{Open Graph}} model generating information, the meta information used by Facebook, specific to each object SPIP. To use it, you must manually "{{insert_head_og}}" add in the header of your skeleton model.

{{Warning -}} This model needs to receive the current environment, you must include in each of the pages skeletons  ({"article.html", "rubrique.html" ...}) and not in the global header inclusion ({"inc_head.html"}) :
<cadre class=\'spip\'>
{{#MODELE{insert_head_og}{env}}}
</cadre>', # MODIF

	// E
	'exemple' => '{{{Exemple}}}

Different blocks below show you an example of each model with dummy values​​. Refer to the corresponding model for options.',

	// F
	'fb_modeles' => 'Facebook models',

	// I
	'info_doc' => 'If you are having problems viewing this page [click here->@link@].',
	'info_doc_titre' => 'Note on the display of this page',
	'info_skel_contrib' => 'Online complete documentation page on spip-contrib : [->http://www.spip-contrib.fr/?article3567].',
	'info_skel_doc' => 'This manual page is designed as a skeleton SPIP operating with the standard distribution ({files from the "squelettes-dist/"}). If you are unable to view the page, or if your site uses its own skeletons, the links below can manage its display:

-* [Mode "plain text"->@mode_brut@] ({simple html + tag INSERT_HEAD})
-* [Mode "skeleton Zpip"->@mode_zpip@] ({compatible Z skeleton})
-* [Mode "skeleton SPIP"->@mode_spip@] ({compatible distribution})',

	// J
	'javascript_inactif' => 'Javascript is disabled in your browser. Some features of this tool will be inactive ...',

	// L
	'licence' => 'Plugin for SPIP 2.0+ : {{"Facebook Models" - copyright © 2009 [Piero Wbmstr->http://www.spip-contrib.net/PieroWbmstr] under licence [GPL->http://www.opensource.org/licenses/gpl-3.0.html] }}.',

	// N
	'new_window' => 'New window',
	'non' => 'No',

	// O
	'oui' => 'Yes',

	// P
	'page_test' => 'Test page (local)',
	'page_test_in_new_window' => 'Test page in new window',
	'personnalisation' => '{{{Customization}}}

Each model presents its content in a block such as <code>div</ code> with CSS classes <code>fb_modeles fb_XXX</ code> where {{XXX}} is the name of the model. This allows customization of styles for all models and for each of them.


Eg for Facebook "Send" module:
<cadre class="spip">
<div class="fb_modeles fb_send">
     ... content ... 
</div>
</cadre>',

	// S
	'sep' => '----',

	// T
	'titre_original' => 'Facebook Models, plugin for SPIP 2.0+'
);

?>
