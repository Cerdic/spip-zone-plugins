<?php

function balise_ID_SECTEUR_COURANT_dist($p) {
	$p->code = '$GLOBALS["multidomaine_id_secteur_courant"] ? $GLOBALS["multidomaine_id_secteur_courant"] : multidomaine_trouver_secteur($Pile[0])';

	return $p;
}


