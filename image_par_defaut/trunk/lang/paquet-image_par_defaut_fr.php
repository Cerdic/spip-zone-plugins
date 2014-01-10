<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

    'image_par_defaut_nom'         => "Image par défaut",
    'image_par_defaut_slogan'      => "Crée des images alternatives de dimensions données.",
    'image_par_defaut_description' => "Lorsqu'un squelette doit afficher une image et que celle-ci n'a pas encore été importée dans le site, la balise génére une image temporaire dont on peut préciser les dimensions :
<code>#IMAGE_PAR_DEFAUT&#123;largeur,hauteur&#125;</code>
Un (petit) texte peut être affiché au centre de l'image, dont la couleur, ainsi que la couleur du fond, peuvent être précisées :
<code>#IMAGE_PAR_DEFAUT&#123;largeur,hauteur,texte,couleur fond,couleur texte&#125;</code>",
);

?>