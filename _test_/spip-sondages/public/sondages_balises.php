<?php


	/**
	 * SPIP-Sondages
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('sondages_fonctions');
	

	function balise_URL_SONDAGE($p) {
		$_id_sondage = '';
		if ($p->param && !$p->param[0][0]){
			$_id_sondage =  calculer_liste($p->param[0][1],
								$p->descr,
								$p->boucles,
								$p->id_boucle);
		}
		if (!$_id_sondage)
			$_id_sondage = champ_sql('id_sondage',$p);
		$p->code = "generer_url_public(sondage, 'id_sondage='.$_id_sondage, true)";
	
		if ($p->boucles[$p->nom_boucle ? $p->nom_boucle : $p->id_boucle]->hash)
		$p->code = "url_var_recherche(" . $p->code . ")";

		$p->interdire_scripts = false;
		return $p;
	}


	function balise_POURCENTAGE($p) {
		$_id_choix = champ_sql('id_choix',$p);
		$_id_sondage = champ_sql('id_sondage',$p);
		$p->code = "sondages_calculer_pourcentage($_id_sondage, $_id_choix)";
		$p->statut = 'php';
		return $p;
	}


	function balise_POURCENTAGE_MAX($p) {
		$_id_sondage = champ_sql('id_sondage',$p);
		$p->code = "sondages_calculer_pourcentage_max($_id_sondage)";
		$p->statut = 'php';
		return $p;
	}


	function calculer_url_sondage($id_sondage, $texte, $ancre) {
		$lien = generer_url_sondage($id_sondage) . $ancre;
		if (!$texte) {
			$row = @spip_fetch_array(spip_query("SELECT titre FROM spip_sondages WHERE id_sondage=$id_sondage"));
			$texte = $row['titre'];
		}
		return array($lien, 'spip_in', $texte);
	}


	function generer_url_sondage($id_sondage, $preview=false) {
		if ($preview)
			$var_mode = '&var_mode=preview';
		return generer_url_public('sondage', 'id_sondage='.$id_sondage.$var_mode);
	}


?>