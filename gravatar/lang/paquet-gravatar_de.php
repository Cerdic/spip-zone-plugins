<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/paquet-gravatar?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// G
	'gravatar_description' => 'Ermöglicht Gravatare in einem Cache zu speichern.
_ Wird mit diesem Code in Schleifen eingebunden : <code>#GRAVATAR{Email, Grösse, Default-Grafik}</code>
_ Beispiel : <code>#GRAVATAR{#EMAIL,80,#URL_SITE_SPIP/defaut-gravatar.gif}</code>

Erweitert den SPIP-Tag #LOGO_AUTEUR um seinen Gravtar, so vorhanden; auch in Foren und Petitionen.
_ Ermöglicht eine Default-Grafik und die Größe der Grafiken einszustellen.

Der mitgelieferte Filter <code>|gravatar</code> kann so verwendet werden <code>[(#EMAIL|gravatar|image_reduire{80})]</code>.',
	'gravatar_slogan' => 'Gravatar eines Autors oder Forenteilnehmers anzeigen'
);
