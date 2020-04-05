<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans https://git.spip.net/spip-contrib-extensions/mathjax.git
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'appel' => 'Mode d’appel du script MathJax',

	// C
	'cnd' => 'Par CDN (plus efficace en termes de performances mais nécessite que le serveur soit connecté à internet)',
	'configuration_globale' => 'Configuration globale de MathJax',
	'configuration_mathjax' => 'Mathjax pour SPIP',

	// D
	'direct' => 'Par chargement direct depuis votre serveur',

	// M
	'mode_info' => '<p>Pour choisir le mode de chargement direct depuis votre serveur, il vous faudra télécharger la libraire à l’adresse suivante <a href="https://github.com/mathjax/MathJax/archive/master.zip" title="Télécharger la librairie">https://github.com/mathjax/MathJax/archive/master.zip</a>, en extraire son contenu, puis le déplacer dans le dossier <em>lib/mathjax/</em> (à créer si besoin) à la racine du site.</p>',

	// T
	'titre_page_configurer_mathjax' => 'MathJax'
);
