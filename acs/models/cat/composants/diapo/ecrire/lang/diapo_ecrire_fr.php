<?php
// This is a SPIP-ACS language file  --  Ceci est un fichier langue de SPIP-ACS

$GLOBALS[$GLOBALS['idx_lang']] = array(

'nom' => 'Diaporama',
'description' => 'Diaporama des documents inclus.',
'info' => 'Affiche les documents d\'un article (dans une page article), d\'une rubrique (dans
une page rubrique), du résultat d\'une recherche, ou de tout le site, sous forme de diapos pour les documents affichables dans la page web, suivies de la liste des documents non gérés par les outils de visualisation intégrés.
<br />
Les documents déjà insérés dans le texte par un raccourci SPIP ne sont pas ré-affichés.',

'help' => '<b>Nombre</b> détermine le nombre de vignettes à afficher. <b>Colonnes</b> change de ligne lorsqu\'autant de vignettes sont déjà affichées.
<br /><br />
<b>Largeur</b> et <b>Hauteur</b> sont les dimensions de la vignette (si et seulement si la génération automatique des miniatures est activée ET que la dimension maximale demandée est inférieure ou égale à la <a href="?exec=config_fonctions">dimension maximum des miniatures définie dans SPIP</a>).
<br /><br />
Désactive l\'affichage du portfolio standard dans les articles : le composant DOIT alors être utilisé dans la page article ou dans l\'article lui-même pour afficher ses documents associés. 
<br /><br /> 
Utilise le plugion Mediabox, s\'il est installé, pour afficher les documents cliqués avec un effet "lightbox".
<br /><br />
Utilise (en option) le service web Google Docs<sup>&reg;</sup>: pour les documents de types pdf, odt, ods, doc, xls, wmf.
<br /><br />
Soft-downgrade: fonctionne aussi sans javascript.', 

'NbCol' => 'Colonnes',
'UseG' => 'Utiliser Google Docs<sup>&reg;</sup>',
'ExtG' => 'Associer à Google Doc API',
'ExtEmbed' => 'Associer au modèle EMBED',
'ExtPlayer' => 'Associer au modèle PLAYER',

'ImgG' => 'Fond gauche',
'Img' => 'Fond',
'ImgD' => 'Fond droit',
'ImgGoff' => 'Fl&egrave;che gauche',
'ImgDoff' => 'Fl&egrave;che droite',
'ImgGon' => 'au survol',
'ImgDon' => 'au survol'
);
?>