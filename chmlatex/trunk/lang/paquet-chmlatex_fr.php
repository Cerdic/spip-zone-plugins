<?php
    if (!defined('_ECRIRE_INC_VERSION')) return;

    $GLOBALS[$GLOBALS['idx_lang']] = array(
    'chmlatex_nom' => "CHM & LaTeX",

    'chmlatex_slogan' => "Exporter un secteur au format CHM ou LaTeX",

    'chmlatex_description' => "Le plugin CHM & LaTex est conçu pour exporter le contenu d'un secteur d'un site SPIP dans les formats :

    - HTML statique pour compilation au format [?CHM] à l'aide de [HTML Help Workshop->https://www.microsoft.com/en-us/download/details.aspx?id=21138]
    - Latex pour compilation au format PDF avec [XeLatex->http://www.xelatex.org]

    Le multilinguisme est supporté de la façon suivante :

    - Utilisation d'une structure unique de rubriques avec traduction des titres et texte des rubriques à l'aide des balises [&lt;multi&gt;->http://programmer.spip.net/Les-Polyglottes-multi];
    - Utilisation des mécanismes SPIP de traduction des articles. Les articles non traduits sont remplacés par l'article de référence des traductions.",
    );
?>
