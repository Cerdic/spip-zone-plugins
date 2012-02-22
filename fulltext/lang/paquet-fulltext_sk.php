<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/paquet-fulltext?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'fulltext_description' => 'Tento poskytuje pomoc pri  FULLTEXTOVOM vyhľadávaní  MySQL a veľmi vylepšuje vyhľadávanie vo vzťahu k fungovaniu SPIPu ako takého a po druhé pri indexovaní obsahu niektorých súborov. 
  -* využíva režim FULLTEXTOVÉ VYHĽADÁVANIE V BOOLEANOVSKOM REŽIME  MySQL, prečítajte si [->http://dev.mysql.com/doc/refman/5.0/fr/fulltext-boolean.html]
Kvôli optimálnemu výkonu si nainštalujte doplnkové programy a ich parametre nastavte v súbore  <code>mes_options.php</code> alebo cez ovládací panel:
-* využíva režim FULLTEXTOVÉ VYHĽADÁVANIE V BOOLEANOVSKOM REŽIME MySQL, prečítajte si [->http://dev.mysql.com/doc/refman/5.0/fr/fulltext-boolean.html]
Kvôli optimálnemu výkonu si nainštalujte doplnkové programy a ich parametre nastavte v súbore  <code>mes_options.php</code> alebo cez ovládací panel:
-** Všetky dokumenty musia byť rovnakého typu <code>_FULLTEXT_EXT_EXE</code> tak ako je to definované (alebo EXT je prípona súboru) alebo jeho ekvivalent v ovládacom paneli.
-** Indexovanie súborov je v predvolených nastaveniach deaktivované (žiadna hodnota nie je definovaná).
-* Pre {{PDF:}}
-** nainštalujte [Xpdf,->http://www.foolabs.com/xpdf/]
-*** na [Ubuntu,->http://packages.ubuntu.com/fr/hardy/xpdf-utils]
-*** na Mac OS X cez [MacPorts->http://xpdf.darwinports.com/] alebo s touto [skompilovanou verziou,->http://users.phg-online.de/tk/MOSXS/xpdf-tools-3.dmg]
-*** na [ostatné OS,->http://www.foolabs.com/xpdf/download.html]
-** definujte tieto hodnoty (alebo použite ovládací panel):
-*** <code>_FULLTEXT_PDF_EXE</code> (napríklad <code>/usr/bin/pdftotext:</code>) umiestnenie spúšťacieho súboru <code>pdftotext</code> z [Xdpf->http://www.foolabs.com/xpdf/] na konvertovanie súborov PDF na neformátovaný text
-*** <code>_FULLTEXT_PDF_CMD_OPTIONS</code> (napríklad <code>-enc UTF-8:</code>) Možnosti na volanie spúšťacieho súboru:
-*<code>_FULLTEXT_TAILLE:</code> maximálna veľkosť uloženej textovej verzie súborov (predvolená 50 000)
-* Pre {{DOC, PPT, XLS:}}
-** nainštalujte [Catdoc->http://www.wagner.pp.ru/~vitus/software/catdoc/]
-*** na [Ubuntu/Linux,->http://www.wagner.pp.ru/~vitus/software/catdoc/]
-*** na [Windows,->http://blog.brush.co.nz/2009/09/catdoc-windows/]
-** definujte príslušné hodnoty (alebo použite ovládací panel) rovnako ako pri PDF,
-* Pre {{ODT, DOCX, PPTX, XLSX:}}
-** Využíva funkcie a triedy PHP (vyžaduje si minimálne PHP 5.2 a možnosť -enable-zip).',
	'fulltext_slogan' => 'Indexovanie CELÉHO TEXTU na zrýchlenie vyhľadávaní'
);

?>
