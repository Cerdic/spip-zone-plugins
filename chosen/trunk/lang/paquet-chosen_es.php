<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-chosen?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'chosen_description' => '[Chosen->http://harvesthq.github.com/chosen/] es una biblioteca JavaScript que mejora la experiencia usuario de los selectores en los formularios HTML.

La clase CSS <code>chosen</code> en un <code><select></code> cambiará automáticamente Chosen arriba.
Esta rama del plugin Chosen se basa en el fork de koenpunt - versión 1.0.0 - ver https://github.com/koenpunt/chosen/releases/.

Con este fork, Chosen permite <a href=\'https://github.com/harvesthq/chosen/pull/166\'>crear nuevas opciones</a> en un &lt;select&gt; existente, a condición de que tenga la clase ’chosen-create-option’.
Cuando chosen crea una nueva &lt;option&gt; (la palabra ’nuevo’ por ejemplo) en un &lt;select&gt;, ésta toma la siguiente forma: <code>&lt;option selected=\'selected\' value=\'chosen_nuevo\'&gt;nuevo&lt;/option&gt;</code>.
Apreciar el prefijo ’chosen_’ añadido en el parámetro ’value’ para permitir diferenciar las &lt;option&gt; creadas por Chosen.',
	'chosen_nom' => 'Chosen (fork de koenpunt)',
	'chosen_slogan' => 'Integrar la biblioteca Chosen en SPIP (fork de koenpunt)'
);

?>
