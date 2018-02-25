<?php

function messages_personnalises_champs_reservation_email_dist($nom, $data_objet) {
	if ($data_objet['reservation_id_auteur'] > 0) {
		$nom = mp_chercher_valeur_champ('auteur_email', $data_objet['auteur_email'], $data_objet);
	}

	return $nom;
}
