<?php
function genie_speedsyndication_dist($t){
	   $sites = lire_config('speedsyndic/syndicatedlist');
       foreach($sites as $id_syndic){
		include_spip('inc/speedsyndic_fonctions');
		traiter_site($id_syndic);		
       }
      return true;
}
?>



