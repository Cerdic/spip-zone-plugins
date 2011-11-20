<?php
$GLOBALS[$GLOBALS['idx_lang']] = array(

	'fulltext_slogan' => 'FULLTEXT indexation to speed up searches',
	'fulltext_description' => 'Ce plugin permet d&#39;une part d&#39;exploiter le mode de recherche FULLTEXT de MySQL et d&#39;am&eacute;liorer ainsi &eacute;norm&eacute;ment les recherches par rapport au fonctionnement natif de SPIP, et d&#39;autre part d&#39;indexer le contenu de certains documents. 
  -* exploite le mode FULLTEXT SEARCH IN BOOLEAN MODE de MySQL, cf. [->http://dev.mysql.com/doc/refman/5.0/fr/fulltext-boolean.html]
Pour un fonctionnement optimal, il faut installer des programmes compl&#233;mentaires et param&#233;trer leur utilisation dans <code>mes_options.php</code> ou via le panneau de configuration :
-* exploite le mode FULLTEXT SEARCH IN BOOLEAN MODE de MySQL, cf. [->http://dev.mysql.com/doc/refman/5.0/fr/fulltext-boolean.html]
Pour un fonctionnement optimal, il faut installer des programmes compl&#233;mentaires et param&#233;trer leur utilisation dans <code>mes_options.php</code> ou via le panneau de configuration :
-** Tous les types de documents doivent avoir une constante de type <code>_FULLTEXT_EXT_EXE</code> de d&#233;finie (ou EXT est l&#39;extension du document) ou l&#39;&#233;quivalent dans le panneau de configuration.
-** L&#39;indexation de document est inactive par d&#233;faut (aucune constantes de d&#233;finies).
-* Pour les {{PDF}} :
-** Installer [Xpdf->http://www.foolabs.com/xpdf/]
-*** Sur [Ubuntu->http://packages.ubuntu.com/fr/hardy/xpdf-utils],
-*** Sur Mac OS X via [MacPorts->http://xpdf.darwinports.com/] ou avec cette [version compil&#233;e->http://users.phg-online.de/tk/MOSXS/xpdf-tools-3.dmg],
-*** Sur d&#39;[autres OS->http://www.foolabs.com/xpdf/download.html]
-** D&#233;finir ces constantes (ou utiliser le panneau de configuration):
-*** <code>_FULLTEXT_PDF_EXE</code> (par exemple <code>/usr/bin/pdftotext</code>) : Chemin vers l&#39;ex&#233;cutable <code>pdftotext</code> de [Xdpf->http://www.foolabs.com/xpdf/] afin de transformer les fichiers PDF en texte brut
-*** <code>_FULLTEXT_PDF_CMD_OPTIONS</code> (par exemple <code>-enc UTF-8</code>) : Options d&#39;appel de l&#39;ex&#233;cutable
-*<code>_FULLTEXT_TAILLE</code> : Taille maximum conserv&#233;e pour la version texte des fichiers (50000 par d&#233;faut)
-* Pour les {{DOC, PPT, XLS}} :
-** Installer [Catdoc->http://www.wagner.pp.ru/~vitus/software/catdoc/]
-*** Sur [Ubuntu/Linux->http://www.wagner.pp.ru/~vitus/software/catdoc/],
-*** Sur [Windows->http://blog.brush.co.nz/2009/09/catdoc-windows/]
-** D&#233;finir les constantes correspondantes (ou utiliser le panneau de configuration) de la meme mani&#234;re que pour les PDF.
-* Pour les {{ODT, DOCX, PPTX, XLSX}} :
-** Utilise des fonctions et des classes PHP (n&#233;cessite PHP 5.2 au minimum, ainsi que l&#39;option -enable-zip).
  ',

 );

?> 