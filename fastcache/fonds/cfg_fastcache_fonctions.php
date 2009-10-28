<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

# code en double, cf spip.php
$site = str_replace('www.', '', $_SERVER['HTTP_HOST']);
list($site) = explode(':', $site); // supprimer le :80 (flash)
define('_FC_LANCEUR', _DIR_RACINE.'tmp/fcconfig_' . $site . '.inc');
unlink(_FC_LANCEUR); # on ne peut pas le creer depuis l'espace prive, les chemins sont faux !
