<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/paquet-gravatar?lang_cible=it
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// G
	'gravatar_description' => 'Ti permette di usare una cache per memorizzare i gravatars.
_ Per utilizzare in un ciclo in questo modo:: <code>#GRAVATAR{{email, dimensione, immagine url di
default}</code>
_ Esempio: <code>#GRAVATAR{#EMAIL,80,#URL_SITE_SPIP/defaut-gravatar.gif}</code>

Estendi il tag #LOGO_AUTEUR per prendere in considerazione il gravatar di un autore, se esistente, anche nei forum e nelle petizioni.
_ Consente di configurare un’immagine predefinita e la dimensione delle immagini.

Fornisce il filtro <code>|gravatar</code>, da utilizzare ad esempio come <code>[(#EMAIL|gravatar|image_reduire{80})]</code>.',
	'gravatar_slogan' => 'Mostra il Gravatar di un autore o un collaboratore del forum'
);
