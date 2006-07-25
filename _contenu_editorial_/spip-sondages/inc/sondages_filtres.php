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
	 * sondages_pourcentage
	 *
	 * calcule le pourcentage associé à un choix
	 *
	 * @param int total_avis
	 * @param int id_sondage
	 * @return string pourcentage
	 * @author Pierre Basson
	 **/
	function sondages_pourcentage($total_avis, $id_sondage) {
		$requete_total_sondage = 'SELECT A.id_avis
								FROM spip_avis AS A, spip_sondes AS S 
								WHERE S.id_sonde=A.id_sonde
									AND S.id_sondage="'.$id_sondage.'"';
		$resultat_total_sondage = spip_query($requete_total_sondage);
		$total_sondage = intval(spip_num_rows($resultat_total_sondage));
		
		$pourcentage = ( ($total_avis / $total_sondage) * 100 );
		$pourcentage = number_format($pourcentage, 1, '.', '');
		return $pourcentage;
	}


	/**
	 * sondages_largeur
	 *
	 * calcule la largeur pour le total passé en argument
	 *
	 * @param int total_avis
	 * @param int id_sondage
	 * @param int largeur_max
	 * @return string pourcentage
	 * @author Pierre Basson
	 **/
	function sondages_largeur($total_avis, $id_sondage, $largeur_max) {
		$requete_total_sondage = 'SELECT A.id_avis
								FROM spip_avis AS A, spip_sondes AS S 
								WHERE S.id_sonde=A.id_sonde
									AND S.id_sondage="'.$id_sondage.'"';
		$resultat_total_sondage = spip_query($requete_total_sondage);
		$total_sondage = intval(spip_num_rows($resultat_total_sondage));
		
		$requete_max = 'SELECT COUNT(id_choix) AS total
						FROM spip_avis
						WHERE id_sondage="'.$id_sondage.'"
						GROUP BY id_choix
						ORDER BY total DESC 
						LIMIT 1';
		$resultat_max = spip_query($requete_max);
		list($max) = spip_fetch_array($resultat_max);
		if ($max == 0)
			return '';
		
		$rapport = $total_avis / $max;
		$largeur = $rapport * $largeur_max;

		return $largeur;
	}


?>