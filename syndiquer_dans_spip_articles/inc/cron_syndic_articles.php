<?php

// Creer les rubriques correspondant aux sites syndiques
$result = spip_query("SELECT id_syndic, nom_site
	FROM spip_syndic WHERE statut='publie'");

while ($row = spip_fetch_array($result)) {
	$i = spip_fetch_array(spip_query("SELECT
		COUNT(id_rubrique) AS compteur
		FROM spip_rubriques
		WHERE id_rubrique='".$row['id_syndic']."'"));
	if($i['compteur'] == 0) {
		$nom_site = addslashes($row['nom_site']);

		$inserer = spip_query("INSERT
			INTO spip_rubriques(id_rubrique, id_parent, id_secteur, titre)
			VALUES('".$row['id_syndic']."', '1', '1', '".$nom_site."')");
	}
	// On en profite pour synchroniser toutes les rubriques
	else
		$update_rubrique = spip_query("UPDATE spip_rubriques
			SET titre='".addslashes($row['nom_site'])."',
			descriptif='".addslashes($row['descriptif'])."'
			WHERE id_rubrique='".$row['id_syndic']."'");
}

// Mettre a jour le champ id_secteur des articles (si pas defini)
$update_article = spip_query("UPDATE spip_articles
	SET id_secteur='1' WHERE id_secteur='0'");

// Tous les sites syndiques doivent etre dans la rubrique 1
$update_sites = spip_query("UPDATE spip_syndic
	SET id_secteur='1', id_rubrique='1'");

?>
