<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-chosen?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'chosen_description' => '[Chosen->http://harvesthq.github.com/chosen/] is a JavaScript library that enhances the user experience of selectors in HTML forms.

The CSS class <code>chosen</code> on a <code><select></code> will automatically load Chosen above. This leaf of the plugin is based on the fork of koenpunt - version 1.0.0 - see https://github.com/koenpunt/chosen/releases/.

Using this fork, Chosen allows to <a href=\'https://github.com/harvesthq/chosen/pull/166\'>create ne< options</a> in an existing &lt;select&gt; tag, under the condition that it has a class of ’chosen-create-option’.
When Chosen creates a new  &lt;option&gt; (for example word ’dummy’) in a &lt;select&gt;, it takes the following format: <code>&lt;option selected=\'selected\' value=\'chosen_dummy\'&gt;dummy&lt;/option&gt;</code>.
Take notice of the prefix ’chosen_’ that is added in the ’value’ parameter in order to distinguish the &lt;option&gt; values created by Chosen.',
	'chosen_nom' => 'Chosen (koenpunt’s fork)',
	'chosen_slogan' => 'Integrate the Chosen library in SPIP (koenpunt’s fork)'
);

?>
