<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function chatin_upgrade($nom_meta_base_version,$version_cible){
   
   echo "hola"!;
    
    $current_version = 0.0;
   include_spip('base/abstract_sql');

    
   spip_query("CREATE TABLE IF NOT EXITS `spip_chat` (
              `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
              `from` VARCHAR(255) NOT NULL DEFAULT '',
              `to` VARCHAR(255) NOT NULL DEFAULT '',
              `message` TEXT NOT NULL,
              `sent` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
              `recd` INTEGER UNSIGNED NOT NULL DEFAULT 0,
               PRIMARY KEY (`id`)
              )
              ENGINE = InnoDB;"
              );

    spip_log("chaTin installed : table created", "maj");
    echo "ChaTiN Installed<br/>";
	}
?>
