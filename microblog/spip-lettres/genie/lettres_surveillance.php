<?php
/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *
 **/

include_spip('inc/delivrer');
function genie_lettres_surveillance_dist($t) {

	$res = sql_select("id_lettre", "spip_lettres", "statut='envoi_en_cours'");
	while ($row = sql_fetch($res)){
		if (!lettres_envois_restants($row['id_lettre'])){
			include_spip('lettres_fonctions');
			$lettre = new lettre($row['id_lettre']);
			$lettre->enregistrer_statut('envoyee');
		}
	}

	return 0;
}

?>