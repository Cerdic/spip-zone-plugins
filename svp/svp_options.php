<?php

// Mise a jour automatique des depots (CRON)
// - Flag de declenchement
define('_SVP_CRON_ACTUALISATION_DEPOTS', true);
// - Periode d'actualisation en nombre d'heures (de 1 a 24)
define('_SVP_PERIODE_ACTUALISATION_DEPOTS', 6);

// Mise a jour automatique des stats (CRON)
// - Flag de declenchement
define('_SVP_CRON_ACTUALISATION_STATS', false);
// - Periode d'actualisation en nombre de jours
define('_SVP_PERIODE_ACTUALISATION_STATS', 7);
// - Adresse de la page fournissant les statistiques par json
define('_SVP_SOURCE_STATS', 'http://stats.spip.org/spip.php?page=stats.json');


// Définition de la boite du logo de depot pour utilisation de iconifier()
$GLOBALS['logo_libelles']['id_depot'] = _T('svp:titre_boite_logo_depot');

// Type parseur XML a appliquer pour recuperer les infos du plugin 
// - plugin, pour utiliser plugin.xml 
// - paquet, pour paquet.xml 
define('_SVP_DTD_PLUGIN', 'plugin'); 
define('_SVP_DTD_PAQUET', 'paquet'); 

// Regexp de recherche des balises principales de archives.xml
define('_SVP_REGEXP_BALISE_DEPOT', '#<depot[^>]*>(.*)</depot>#Uims');
define('_SVP_REGEXP_BALISE_ARCHIVES', '#<archives[^>]*>(.*)</archives>#Uims');
define('_SVP_REGEXP_BALISE_ARCHIVE', '#<archive[^>]*>(.*)</archive>#Uims');
define('_SVP_REGEXP_BALISE_ZIP', '#<zip[^>]*>(.*)</zip>#Uims');
define('_SVP_REGEXP_BALISE_TRADUCTIONS', '#<traductions[^>]*>(.*)</traductions>#Uims');
define('_SVP_REGEXP_BALISE_PLUGIN', '#<plugin[^>]*>(.*)</plugin>#Uims');
define('_SVP_REGEXP_BALISE_PAQUET', '#<paquet[^>]*>(.*)</paquet>#Uims');

// Version SPIP minimale quand un plugin ne le precise pas
define('_SVP_VERSION_SPIP_MIN', '1.9.0');
// Intervalles de compatibilite SPIP
define('_SVP_COMPATIBILITE_SPIP_TOUT', '[1.9.0;)');
define('_SVP_COMPATIBILITE_SPIP_NA', '(;)');

?>
