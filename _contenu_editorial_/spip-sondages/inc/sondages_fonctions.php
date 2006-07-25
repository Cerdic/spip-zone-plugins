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


	include_spip('base/sondages');
	include_spip('inc/plugin');
	include_spip('inc/sondages_admin');
	include_spip('inc/sondages_balises');
	include_spip('inc/sondages_filtres');


	/**
	 * sondages_ajouter_boutons
	 *
	 * Ajoute les boutons pour les sondages dans l'espace privé
	 *
	 * @param array boutons_admin
	 * @return array boutons_admin le même tableau avec des entrées en plus
	 * @author Pierre Basson
	 **/
	function sondages_ajouter_boutons($boutons_admin) {
		if ($GLOBALS['connect_statut'] == "0minirezo") {
			$boutons_admin['naviguer']->sousmenu['sondages']= new Bouton('../'._DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', _T('sondages:sondages'));
		}
		return $boutons_admin;
	}


	/**
	 * sondages_header_prive
	 *
	 * Vérifie que la base est à jour
	 *
	 * @param string texte
	 * @return string texte avec le chemin modifié
	 * @author Pierre Basson
	 **/
	function sondages_header_prive($texte) { 
		sondages_verifier_base();
		return $texte;
	}


	/**
	 * sondages_taches_generales_cron
	 *
	 * Ajout des tâches planifiées pour le plugin
	 *
	 * @param array taches_generales
	 * @return true
	 * @author Pierre Basson
	 **/
	function sondages_taches_generales_cron($taches_generales) {
		$taches_generales['sondages'] = 60 * 5; // toutes les 5 minutes
		return $taches_generales;
	}


	/**
	 * cron_sondages
	 *
	 * Tâche de fond pour publier/terminer les sondages
	 *
	 * @param array taches_generales
	 * @return true
	 * @author Pierre Basson
	 **/
	function cron_sondages($t) {
		$requete_tous_les_sondages_en_ligne = 'SELECT id_sondage FROM spip_sondages WHERE en_ligne="oui"';
		$resultat_tous_les_sondages_en_ligne = spip_query($requete_tous_les_sondages_en_ligne);
		while (list($id_sondage) = spip_fetch_array($resultat_tous_les_sondages_en_ligne)) {
			sondages_mettre_a_jour_sondage($id_sondage);
		}
		return true;
	}


	/**
	 * sondages_calculer_pourcentage
	 *
	 * calcule le pourcentage associé à un choix
	 *
	 * @param int id_sondage
	 * @param int id_choix
	 * @return string pourcentage
	 * @author Pierre Basson
	 **/
	function sondages_calculer_pourcentage($id_sondage, $id_choix) {
		$requete_total_sondage = 'SELECT A.id_avis
								FROM spip_avis AS A, spip_sondes AS S 
								WHERE S.id_sonde=A.id_sonde
									AND S.id_sondage="'.$id_sondage.'"';
		$resultat_total_sondage = spip_query($requete_total_sondage);
		$total_sondage = intval(spip_num_rows($resultat_total_sondage));
		
		$requete_total_avis = 'SELECT A.id_avis
								FROM spip_avis AS A, spip_sondes AS S 
								WHERE S.id_sonde=A.id_sonde
									AND A.id_choix="'.$id_choix.'"
									AND S.id_sondage="'.$id_sondage.'"';
		$resultat_total_avis = spip_query($requete_total_avis);
		$total_avis = intval(spip_num_rows($resultat_total_avis));

		$pourcentage = ( ($total_avis / $total_sondage) * 100 );
		$pourcentage = number_format($pourcentage, 1, '.', '');
		return $pourcentage;
	}


?>