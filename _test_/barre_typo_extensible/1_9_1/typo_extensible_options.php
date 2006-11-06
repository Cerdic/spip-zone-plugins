<?php
//
// Gerer les variables de personnalisation, qui peuvent provenir
// des fichiers d'appel, en verifiant qu'elles n'ont pas ete passees
// par le visiteur (sinon, pas de cache)
//
$GLOBALS['spip_pipeline']['BarreTypoEnrichie_toolbox'] = ''; 
$GLOBALS['spip_pipeline']['BarreTypoEnrichie_tous'] = ''; 
$GLOBALS['spip_pipeline']['BarreTypoEnrichie_avancees'] = ''; 
$GLOBALS['spip_pipeline']['BarreTypoEnrichie_forum'] = ''; 
include_spip('inc/texte');
tester_variable('debut_intertitre_1', "\n<h1 class=\"spip\">");
tester_variable('fin_intertitre_1', "</h1>");
tester_variable('debut_intertitre_2', "\n<h2 class=\"spip\">");
tester_variable('fin_intertitre_2', "</h2>");
tester_variable('debut_intertitre_3', "\n<h3 class=\"spip\">");
tester_variable('fin_intertitre_3', "</h3>");
tester_variable('debut_intertitre_4', "\n<h4 class=\"spip\">");
tester_variable('fin_intertitre_4', "</h4>");
tester_variable('debut_intertitre_5', "\n<h5 class=\"spip\">");
tester_variable('fin_intertitre_5', "</h5>");
tester_variable('debut_intertitre_6', "\n<h6 class=\"spip\">");
tester_variable('fin_intertitre_6', "</h6>");
?>