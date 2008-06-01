<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/serial'); 
global $tables_principales;

// Extension de la table auteurs
$tables_principales['spip_auteurs']['field']['openid'] = "text DEFAULT '' NOT NULL";
$tables_principales['spip_auteurs']['key']['KEY openid'] = "openid";

?>
