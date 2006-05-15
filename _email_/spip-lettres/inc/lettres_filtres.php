<?php

	/**
	 * lettres_filtrer_parmi_lettres
	 *
	 * Ecrit checked="checked" si id_lettre fait partie de $_GET['lettres']
	 *
	 * @param int id_lettre
	 * @param array lettres pour lesquelles il faut vérifier que l'email est associé
	 * @return array
	 *				boolean résultat
	 *				string message d'erreur
	 * @author Pierre Basson
	 **/
	function lettres_filtrer_parmi_lettres($id_lettre) {
		$lettres = _request('lettres');
		if (empty($lettres)) {
			return false;
		} else {
			if (in_array($id_lettre, $lettres))
				return true;
			else
				return false;
		}
	}

	/**
	 * lettres_filtrer_lettres_virgule
	 *
	 * @author Pierre Basson
	 **/
	function lettres_filtrer_lettres_virgule($lettres_virgule, $avant, $apres) {
		$lettres = explode(',', $lettres_virgule);
		foreach ($lettres as $id_lettre) {
			$requete_titre = 'SELECT titre FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
			list($titre) = @spip_fetch_array(spip_query($requete_titre));
			$affichage.= $avant.$titre.$apres."\n";
		}
		return $affichage;
	}



	function lettres_filtrer_url_pour_redirection($url) {
		$url_encodee = rawurlencode($url);
		$url_pour_stats = lettres_calculer_URL_VALIDATION('redirection').'&url='.$url_encodee;
		return $url_pour_stats;
	}

?>