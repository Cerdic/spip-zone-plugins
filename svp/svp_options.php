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
define('_SVP_NOM_PLUGIN_THEME', 'Thèmes SPIP');
define('_SVP_SLOGAN_PLUGIN_THEME', 'Des thèmes pour habiller les squelettes SPIP');

?>
