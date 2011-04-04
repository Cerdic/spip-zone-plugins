<?php

// Mise a jour automatique des depots (CRON)
// - Flag de declenchement
define('_SVP_CRON_ACTUALISATION_DEPOTS', true);
// - Periode d'actualisation en nombre d'heures (de 1 a 24)
define('_SVP_PERIODE_ACTUALISATION_DEPOTS', 6);

// Flag de log :
// (les erreurs non presentees a l'utilisateur sont elles toujours logees)
// - des actions sur les depots (ajouter, editer, supprimer, actualiser) 
define('_SVP_LOG_ACTIONS', true);
// - des paquets non inseres dans la base
define('_SVP_LOG_PAQUETS', true);

// Définition de la boite du logo de depot pour utilisation de iconifier()
$GLOBALS['logo_libelles']['id_depot'] = _T('svp:titre_boite_logo_depot');

// Infos *temporaires* du plugin de prefixe 'theme'
define('_SVP_PREFIXE_PLUGIN_THEME', 'THEME');
define('_SVP_NOM_PLUGIN_THEME', 'Thèmes SPIP');
define('_SVP_SLOGAN_PLUGIN_THEME', 'Des thèmes pour habiller les squelettes SPIP');

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
