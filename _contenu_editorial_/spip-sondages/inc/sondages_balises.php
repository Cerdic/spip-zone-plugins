<?php


	/**
	 * SPIP-Sondages : plugin de gestion de sondages
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	/**
	 * balise_URL_SONDAGE
	 *
	 * @param p est un objet SPIP
	 * @return string url d'un sondage
	 * @author Pierre Basson
	 **/
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
		$p->code = "generer_url_public(sondage, 'id_sondage='.$_id_sondage)";
	
		if ($p->boucles[$p->nom_boucle ? $p->nom_boucle : $p->id_boucle]->hash)
		$p->code = "url_var_recherche(" . $p->code . ")";

		$p->interdire_scripts = false;
		return $p;
	}


	/**
	 * balise_POURCENTAGE
	 *
	 * @param p est un objet SPIP
	 * @return string url de validation de l'inscription
	 * @author Pierre Basson
	 **/
	function balise_POURCENTAGE($p) {
		$_id_choix = champ_sql('id_choix',$p);
		$_id_sondage = champ_sql('id_sondage',$p);
		$p->code = "sondages_calculer_pourcentage($_id_sondage, $_id_choix)";
		$p->statut = 'php';
		return $p;
	}


?>