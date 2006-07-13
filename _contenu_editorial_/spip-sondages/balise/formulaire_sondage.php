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


	include_spip('base/abstract_sql');


	/**
	 * balise_FORMULAIRE_SONDAGE
	 *
	 * @param p est un objet SPIP
	 * @return string url de validation de l'inscription
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_SONDAGE ($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_SONDAGE', array('id_sondage'));
	}

	function balise_FORMULAIRE_SONDAGE_stat($args, $filtres) {
		// Pas d'id_sondage ? Erreur de squelette
		if (!$args[0])
			return erreur_squelette(_T('zbug_champ_hors_motif', array ('champ' => '#FORMULAIRE_SONDAGE', 'motif' => 'SONDAGES')), '');

		// On ne peut pas "voir les résultats"/"donner son avis" si le sondage n'est pas à l'état publie ou termine
		$requete_statut = 'SELECT statut FROM spip_sondages WHERE id_sondage="'.$args[0].'"';
		list($statut) = spip_fetch_array(spip_query($requete_statut));
		if ($statut == 'brouillon')
			return '';

		return $args;
	}

	/**
	 * balise_FORMULAIRE_SONDAGE_dyn
	 *
	 * Calcule la balise #FORMULAIRE_SONDAGE
	 *
	 * @return array
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_SONDAGE_dyn($id_sondage) {

		$requete_statut = 'SELECT statut FROM spip_sondages WHERE id_sondage="'.$id_sondage.'"';
		list($statut) = spip_fetch_array(spip_query($requete_statut));
		
		if ($statut == 'termine') { // le sondage est terminé, l'internaute a déjà voté
			return	array(
						'resultat_sondage', 
						0,
						array(
							'id_sondage'	=> $id_sondage
						)
					);
		
		} else { // le sondage est en cours
			$choix		= _request('choix_'.$id_sondage);
			$valider	= _request('valider');

			if (!empty($choix) AND !empty($valider)) {
				$ip = $_SERVER['REMOTE_ADDR'];
				foreach ($choix as $id_choix) {
					spip_query('INSERT INTO spip_avis (id_sondage, id_choix, ip, date) VALUES ("'.$id_sondage.'", "'.intval($id_choix).'", "'.$ip.'", NOW())');
				}
				
				return	array(
							'resultat_sondage', 
							0,
							array(
								'id_sondage'	=> $id_sondage
							)
						);
			}

			return	array(
						'formulaire_sondage', 
						0,
						array(
							'id_sondage'	=> $id_sondage
						)
					);
		}
	}


?>