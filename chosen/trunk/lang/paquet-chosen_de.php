<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-chosen?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'chosen_description' => '[Chosen->http://harvesthq.github.com/chosen/] ist eine JavaScript Bibliothe, die HTML-Formulare benutzerfreundlicher macht.
Durch Zuordnen der CSS-Klasse <code>chosen</code> zu einem <code><select></code> wendet Chosen auf das Formular an.
Dieser Zweig von Chosen basiert auf dem Fork von koenpunt - Version 1.0.0 - siehe https://github.com/koenpunt/chosen/releases/.

Avec ce fork, Chosen permet de <a href=\'https://github.com/harvesthq/chosen/pull/166\'>Anlegen neuer Optionen</a> in einem vorhandenen &lt;select&gt; , wenn die Klasse "chosen-create-option" vorhanden ist.
Wenn Chosen eine neue &lt;Option&gt; (z.B. das Wort "neu") in einem &lt;select&gt; erstellt, nimmt sie folgende Form an: <code>&lt;option selected=\'selected\' value=\'chosen_nouveau\'&gt;nouveau&lt;/option&gt;</code>.
Das Präfix "chosen_" , wird dem Wert "value" hunzugefügt, um die von Chosen erstellte &lt;option&gt; zu identifizieren.',
	'chosen_nom' => 'Chosen (Tork von koenpunt)',
	'chosen_slogan' => 'Integriert die Bibliothe Chosen in SPIP (Fork von koenpunt)'
);

?>
