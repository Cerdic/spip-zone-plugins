<?php
	if (!defined("_ECRIRE_INC_VERSION")) return;

		function compare_date($date_debut,$date_fin,$idressource,$idresa){
		include_spip('base/abstract_sql');
		if($idresa>"0"){
			if ($result = sql_select(
									array(
									"reservation.orr_date_debut",
									"reservation.orr_date_fin"),
									array(
									"spip_orr_reservations AS reservation",
									"spip_orr_reservations_liens AS lien",
									"spip_orr_ressources AS ressource"),
									array(
									"reservation.id_orr_reservation=lien.id_orr_reservation",
									"ressource.id_orr_ressource=lien.id_objet",
									"lien.objet='orr_ressource'",
									"ressource.id_orr_ressource=$idressource",
									"reservation.id_orr_reservation<>$idresa")
									)){
				while ($r = sql_fetch($result)){
					$retour=0;
					// date_debut et date_fin à l'interieur sachant que date_debut peut etre egale orr_date_fin et que date_fin peut etre égale à orr_date_debut
					if (($r[orr_date_debut]<=$date_debut) and ($r[orr_date_fin]>=$date_fin)){
						$retour=1;
						break;
					}
					//~ date_debut < à orr_date_debut et date_fin > orr_date_debut
					if (($r[orr_date_debut]>=$date_debut) and ($r[orr_date_debut]<$date_fin)){
						$retour=1;
						break;
					}
					// date_fin > date de fin et que ma date debut < date de fin
					if (($r[orr_date_fin]>$date_debut) and ($r[orr_date_fin]<$date_fin)) {
						$retour=1;
						break;
					}
				}
			}
		}else{
			if ($result = sql_select(
									array(
									"reservation.orr_date_debut",
									"reservation.orr_date_fin"),
									array(
									"spip_orr_reservations AS reservation",
									"spip_orr_reservations_liens AS lien",
									"spip_orr_ressources AS ressource"),
									array(
									"reservation.id_orr_reservation=lien.id_orr_reservation",
									"ressource.id_orr_ressource=lien.id_objet",
									"lien.objet='orr_ressource'",
									"ressource.id_orr_ressource=$idressource")
									)){
				while ($r = sql_fetch($result)){
					$retour=0;
					// date_debut et date_fin à l'interieur sachant que date_debut peut etre egale orr_date_fin et que date_fin peut etre égale à orr_date_debut
					if (($r[orr_date_debut]<=$date_debut) and ($r[orr_date_fin]>=$date_fin)){
						$retour=1;
						break;
					}
					//~ date_debut < à orr_date_debut et date_fin > orr_date_debut
					if (($r[orr_date_debut]>=$date_debut) and ($r[orr_date_debut]<$date_fin)){
						$retour=1;
						break;
					}
					// date_fin > date de fin et que ma date debut < date de fin
					if (($r[orr_date_fin]>$date_debut) and ($r[orr_date_fin]<$date_fin)) {
						$retour=1;
						break;
					}
				}
			}
		}
	return $retour;
	}
?>
