<?php

// Mise a jour automatique des stats (CRON)
// - Flag de declenchement
define('_SVP_CRON_ACTUALISATION_STATS', true);
// - Periode d'actualisation en nombre de jours
define('_SVP_PERIODE_ACTUALISATION_STATS', 7);
// - Adresse de la page fournissant les statistiques par json
define('_SVP_SOURCE_STATS', 'http://stats.spip.org/spip.php?page=stats.json');

?>
