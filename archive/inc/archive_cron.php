<?php 

function cron_archive_cron($t){
// recupere les valeurs fixées avec CFG
 $jours = lire_config("archive/jours");
 $arch_rub = lire_config("archive/idrub");
 $arch_auto = lire_config("archive/act_archive");

// teste si l'archivage automatique est activé
if ( $arch_auto == "oui" ) {
 
// s'il est activé, alors il archive les articles
// de charque rubrique selectionnée

foreach ($arch_rub as $num_rub) {

       $q="update spip_articles set archive_date=NOW(), archive=1 where
FROM_UNIXTIME(UNIX_TIMESTAMP(date))<FROM_UNIXTIME(UNIX_TIMESTAMP(NOW())-($jours*24*3600))AND id_rubrique = $num_rub" ;
       spip_query($q);
	  }
return 1;
}
else {

// si l'archivage n'est pas activé, il ne fait rien
		exit;
}
}
?>