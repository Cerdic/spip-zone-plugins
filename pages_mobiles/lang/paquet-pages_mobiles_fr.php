<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// P
	'pages_mobiles_description' => 'Ce plugin est dérivé du plugin [{cimobile}->http://contrib.spip.net/cimobile-plugin-detection-et-aiguillage-des-telephones] de détection et aiguillage des téléphones mobiles.

Il en reprend le but: {{orienter les visiteurs vers les bonnes pages selon leur périphérique de navigation}}.

Le plugin reprend le mécanisme de détection des mobiles de {cimobile}. Cependant, l\'aiguillage se fait avec une philosophie différente sur deux aspects: 

-# Avec {Pages pour mobiles}, vous n\'avez pas besoin d\'avoir développé toutes les pages pour mobile, le plugin aiguillera vers les pages mobiles, si elles sont présentes. Sinon vers les pages du site normal.
-# {Pages pour mobiles} ne nécessite pas  de placer les squelettes mobiles dans un répertoire squelettes différent, juste dans un sous répertoire \"{mobile}\" du dossier squelette courant (notez que comme dans {cimobile}, il est possible d\'avoir des pages déclinées spécifiquement pour certains périphériques, et qui pourront être trouvées dans un répertoire dédié. Par ex. <code>/squelettes/ipad/article.html</code>).

Le logo du plugin est issu de nounproject: http://thenounproject.com/noun/application/#icon-No3038 il est CC-BY Kyle Klitch.',
	'pages_mobiles_nom' => 'Pages pour mobiles',
	'pages_mobiles_slogan' => 'Redirigez vers la meilleure page disponible pour les mobiles',
);

?>