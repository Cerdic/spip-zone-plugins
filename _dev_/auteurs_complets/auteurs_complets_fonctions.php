<?php
function get_auteur_infos($id='', $nom='') {
	if ($id) $query = "SELECT * FROM spip_auteurs WHERE id_auteur=$id";
	if ($nom) $query = "SELECT * FROM spip_auteurs WHERE nom='$nom'";
	$result = spip_query($query);

	if ($row = spip_fetch_array($result)) {
		$row=serialize($row);
	}

	return $row;
}

//permet de voir les champs ajoutés en 1.9.2+
 
?>