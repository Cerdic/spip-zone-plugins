<?php
//
// Gerer les variables de personnalisation, qui peuvent provenir
// des fichiers d'appel, en verifiant qu'elles n'ont pas ete passees
// par le visiteur (sinon, pas de cache)
//
include_spip('inc/texte');
tester_variable('debut_intertitre', '<h2>');
tester_variable('fin_intertitre', '</h2>');
?>