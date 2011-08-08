<?php
// This is a SPIP-ACS language file  --  Ceci est un fichier langue de SPIP-ACS

$GLOBALS[$GLOBALS['idx_lang']] = array(

'nom' => 'Diaporama',
'description' => 'Diaporama des documents inclus.',
'info' => 'Affiche, en fonction du contexte, les vignettes des documents d\'un article, d\'une rubrique, du résultat d\'une recherche, ou de tout le site, sous forme de diaporama.',

'help' => '
Désactive l\'affichage du portfolio standard dans les articles : le composant DOIT alors être utilisé dans la page article ou dans l\'article lui-même pour afficher ses documents associés.
<br /><br /> 
Utilise le plugion Mediabox, s\'il est installé, pour afficher les documents cliqués avec un effet "lightbox".
<br /><br />
Utilise (en option) un visualiseur externe tel que par exemple Google Docs<sup>&reg;</sup> pour certains types de documents.
<br /><br />
<b>Largeur</b> et <b>Hauteur</b> sont les dimensions de la vignette (si et seulement si la génération automatique des miniatures est activée ET que la dimension maximale demandée est inférieure ou égale à la <a href="?exec=config_fonctions">dimension maximum des miniatures définie dans SPIP</a>)
<br /><br />
Soft-downgrade: fonctionne aussi sans javascript.', 

'Vu' => 'Documents déjà vus',
'VuHelp' => 'Par défaut, les documents déjà affichés dans un article ou une rubrique ne sont PAS ré-affichés dans le diaporama, ce qui est le comportement souhaité pour un composant diaporama inséré dans une page article ou rubrique. Dans une autre page, on veut voir tous les documents qu\'ils soient ou non affichés dans un article ou une rubrique.',
'TitreOver' => 'Superpos&eacute;',
'TitreUnder' => 'Dessous',
'TitreHelp' => 'Affichage du titre et de la description du document.',
'NbCol' => 'Colonnes',
'NbColHelp' => 'Nombre de vignettes à afficher, et nombre de colonnes avant de changer de ligne. Par exemple, Nombre=12 et Colonnes=4 affichera 3 lignes de 4 vignettes.',

'Viewer' => 'URL du visualiseur externe',
'ExtViewer' => 'Avec un visualiseur externe',
'ExtEmbed' => 'Avec le modèle EMBED',
'ExtPlayer' => 'Avec le <a href="spip.php?exec=acs&onglet=pages&pg=modeles/doc_player">modèle PLAYER</a>',

'ImgG' => 'Fond gauche',
'Img' => 'Fond',
'ImgD' => 'Fond droit',
'ImgGoff' => 'Précédant',
'ImgDoff' => 'Suivant',
'ImgGon' => 'au survol',
'ImgDon' => 'au survol'
);
?>