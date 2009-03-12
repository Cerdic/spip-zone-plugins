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


	include_spip('lettres_fonctions');
	include_spip('surcharges_fonctions');


	function action_export_abonnes() {
		global $champs_extra, $id_parent;

		if (autoriser('exporter', 'lettres')) {

			$abonnes = array();
			$i = 0;
			$res = sql_select('id_abonne', 'spip_abonnes_rubriques', 'statut="valide" AND id_rubrique='.intval($id_parent));
			while ($arr = sql_fetch($res)) {
				$abonne = new abonne($arr['id_abonne']);
				$abonnes[$i][] = $abonne->email;
				$abonnes[$i][] = $abonne->nom;
/*
TODO
				if ($champs_extra['abonnes']) {
					$tableau_extras = array();
					$tableau_extras = unserialize($abonne->extra);
					foreach ($champs_extra['abonnes'] as $cle => $valeur) {
						$abonnes[$i][] = $tableau_extras[$cle];
					}
				}
*/
				$i++;
			}

			surcharges_exporter_csv('abonnes', $abonnes);

		}

	}


?>