<?php
function get_auteur_infos($id='', $nom='') {
	$nom_table = "spip_auteurs_ajouts";
	if ($id) $query = "SELECT * FROM ".$nom_table." WHERE id_auteur=$id";
	if ($nom) $query = "SELECT * FROM ".$nom_table." WHERE nom='$nom'";
	$result = spip_query($query);

	if ($row = spip_fetch_array($result)) {
		$row=serialize($row);
	}
	return $row;
}
?>
