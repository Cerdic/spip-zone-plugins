<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/gravatar/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// G
	'gravatar_description' => 'Permet d’utiliser un cache pour stocker les gravatars.
_ À utiliser dans une boucle de cette manière : <code>#GRAVATAR{email, taille, url image par
défaut}</code>
_ Exemple : <code>#GRAVATAR{#EMAIL,80,#URL_SITE_SPIP/defaut-gravatar.gif}</code>

Étend la balise #LOGO_AUTEUR de manière à prendre en compte le gravatar d’un auteur s’il existe, y compris dans les forums et pétitions.
_ Permet de configurer une image par defaut, et la taille des images.

Fournit le filtre <code>|gravatar</code>, à utiliser par exemple comme 
<code>[(#EMAIL|gravatar|image_reduire{80})]</code>.',
	'gravatar_slogan' => 'Afficher le Gravatar d’un auteur ou d’un contributeur de forum'
);

?>
