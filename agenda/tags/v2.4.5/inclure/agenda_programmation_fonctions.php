<?php
if (!defined('_ECRIRE_INC_VERSION')) return; 

function programmation_datedecalejours($date,$decalage) {
	return date('Y-m-d H:i:s',mktime(affdate($date,"H"), affdate($date,"i"), affdate($date,"s"), affdate($date,"m"), affdate($date,"d")+$decalage, affdate($date,"Y")));
}

function programmation_debut($date, $jour_debut = 3) {
	$jour_semaine = affdate($date,'w');
	if ($jour_semaine >= $jour_debut) {
		$decalage = $jour_debut - $jour_semaine;
	} else {
		$decalage = $jour_debut - $jour_semaine - 7;
	}
	$date_debut = programmation_datedecalejours($date,$decalage);
	return $date_debut;
}
?>