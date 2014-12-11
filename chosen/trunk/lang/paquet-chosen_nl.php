<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-chosen?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'chosen_description' => '[Chosen->http://harvesthq.github.com/chosen/] is een JavaScript bibliotheek die de gebruikerservaring verbetert van keuze-opties in HTML-formulieren.

De CSS class <code>chosen</code> op een <code><select></code> zal automatisch Chosen laden.
Deze tak van de plugin Chosen is gebaseerd op koenpunt - version 1.0.0 - voir https://github.com/koenpunt/chosen/releases/.

Hiermee opent Chosen de mogelijkheid om <a href=\'https://github.com/harvesthq/chosen/pull/166\'>nieuwe opties te maken</a> in een bestaande &lt;select&gt;, op voorwaarde dat deze de class ’chosen-create-option’ heeft.
Wanneer Chosen een nieuwe &lt;option&gt; maakt (bijvoorbeeld het woord ’nieuw’) in een &lt;select&gt;, neemt deze de volgende vorm aan: <code>&lt;option selected=\'selected\' value=\'chosen_nieuw\'&gt;nieuw&lt;/option&gt;</code>.
Let vooral op het voorvoegsel ’chosen_’ dat wordt toegevoegd in parameter ’value’, waardoor je een door Chosen aangemaakte  &lt;option&gt; kunt onderscheiden.',
	'chosen_nom' => 'Chosen (fork van koenpunt)',
	'chosen_slogan' => 'Bibliotheek Chosen in SPIP integreren (fork van koenpunt)'
);

?>
