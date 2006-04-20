<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/mots');
include_spip('base/abstract_sql');

function exec_install_mots_arbo() {

	$requetes=array(
		// ajouter les colonnes
		"ALTER TABLE spip_groupes_mots
		  ADD debut  MEDIUMINT NOT NULL DEFAULT '0',
		  ADD fin    MEDIUMINT NOT NULL DEFAULT '0';",

		"ALTER TABLE spip_mots
		  ADD debut  MEDIUMINT NOT NULL DEFAULT '0',
		  ADD fin    MEDIUMINT NOT NULL DEFAULT '0',
		  ADD niveau TINYINT   NOT NULL DEFAULT '1';",

		// ajouter des index sur debut et fin ?

		// d'abord compter les groupes
		"SET @pos:=-1;",
		"UPDATE spip_groupes_mots SET debut=(@pos:=@pos+2) ORDER BY titre;",

		// puis numéroter les mots en profitant de la num des groupe
		// pour savoir qaund ca se décale
		"SET @pos:=-1;",
		"UPDATE spip_mots
		   SET spip_mots.debut=(@pos:=@pos+2)+(
		            SELECT spip_groupes_mots.debut
		              FROM spip_groupes_mots
		             WHERE spip_mots.id_groupe = spip_groupes_mots.id_groupe),
		       spip_mots.fin= spip_mots.debut+1,
		       spip_mots.niveau=1
		  ORDER BY id_groupe,titre;",

		// reste plus qu'a mettre les vraies valeurs pour les groupes
		"UPDATE spip_groupes_mots
		   SET debut=(
			SELECT min(debut) FROM spip_mots
			 WHERE spip_mots.id_groupe = spip_groupes_mots.id_groupe)-1,
		       fin=(
			SELECT max(fin) FROM spip_mots
			 WHERE spip_mots.id_groupe = spip_groupes_mots.id_groupe)+1;",

		// et les repercuter dans des mots cle
		"REPLACE INTO spip_mots
			(id_mot, id_groupe, titre, descriptif, texte, debut, fin, niveau)
		SELECT id_groupe+100000, id_groupe, titre, descriptif, texte, debut, fin, 0
		  FROM spip_groupes_mots",

	);

	foreach($requetes as $requete) {
		if(!spip_query($requete)) die("Echec sur $requete : ".mysql_error());
	}
	echo "OK";
}

?>
