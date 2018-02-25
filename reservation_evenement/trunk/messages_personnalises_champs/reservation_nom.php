<?php

function messages_personnalises_champs_reservation_nom_dist($nom, $data_objet) {
	if ($data_objet['reservation_id_auteur'] > 0) {
		$nom = mp_chercher_valeur_champ('auteur_nom', $data_objet['auteur_nom'], $data_objet);
	}

	return $nom;
}
