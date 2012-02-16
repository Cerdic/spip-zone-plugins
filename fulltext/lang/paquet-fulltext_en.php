<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'fulltext_description' => 'Ce plugin permet d\'une part d\'exploiter le mode de recherche FULLTEXT de MySQL et d\'améliorer ainsi énormément les recherches par rapport au fonctionnement natif de SPIP, et d\'autre part d\'indexer le contenu de certains documents. 
  -* exploite le mode FULLTEXT SEARCH IN BOOLEAN MODE de MySQL, cf. [->http://dev.mysql.com/doc/refman/5.0/fr/fulltext-boolean.html]
Pour un fonctionnement optimal, il faut installer des programmes complémentaires et paramétrer leur utilisation dans <code>mes_options.php</code> ou via le panneau de configuration :
-* exploite le mode FULLTEXT SEARCH IN BOOLEAN MODE de MySQL, cf. [->http://dev.mysql.com/doc/refman/5.0/fr/fulltext-boolean.html]
Pour un fonctionnement optimal, il faut installer des programmes complémentaires et paramétrer leur utilisation dans <code>mes_options.php</code> ou via le panneau de configuration :
-** Tous les types de documents doivent avoir une constante de type <code>_FULLTEXT_EXT_EXE</code> de définie (ou EXT est l\'extension du document) ou l\'équivalent dans le panneau de configuration.
-** L\'indexation de document est inactive par défaut (aucune constantes de définies).
-* Pour les {{PDF}} :
-** Installer [Xpdf->http://www.foolabs.com/xpdf/]
-*** Sur [Ubuntu->http://packages.ubuntu.com/fr/hardy/xpdf-utils],
-*** Sur Mac OS X via [MacPorts->http://xpdf.darwinports.com/] ou avec cette [version compilée->http://users.phg-online.de/tk/MOSXS/xpdf-tools-3.dmg],
-*** Sur d\'[autres OS->http://www.foolabs.com/xpdf/download.html]
-** Définir ces constantes (ou utiliser le panneau de configuration):
-*** <code>_FULLTEXT_PDF_EXE</code> (par exemple <code>/usr/bin/pdftotext</code>) : Chemin vers l\'exécutable <code>pdftotext</code> de [Xdpf->http://www.foolabs.com/xpdf/] afin de transformer les fichiers PDF en texte brut
-*** <code>_FULLTEXT_PDF_CMD_OPTIONS</code> (par exemple <code>-enc UTF-8</code>) : Options d\'appel de l\'exécutable
-*<code>_FULLTEXT_TAILLE</code> : Taille maximum conservée pour la version texte des fichiers (50000 par défaut)
-* Pour les {{DOC, PPT, XLS}} :
-** Installer [Catdoc->http://www.wagner.pp.ru/~vitus/software/catdoc/]
-*** Sur [Ubuntu/Linux->http://www.wagner.pp.ru/~vitus/software/catdoc/],
-*** Sur [Windows->http://blog.brush.co.nz/2009/09/catdoc-windows/]
-** Définir les constantes correspondantes (ou utiliser le panneau de configuration) de la meme maniêre que pour les PDF.
-* Pour les {{ODT, DOCX, PPTX, XLSX}} :
-** Utilise des fonctions et des classes PHP (nécessite PHP 5.2 au minimum, ainsi que l\'option -enable-zip).
  ', # NEW
	'fulltext_slogan' => 'FULLTEXT indexation to speed up searches'
);

?>
