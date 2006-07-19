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
	 * balise_FORMULAIRE_SONDAGE
	 *
	 * @param p est un objet SPIP
	 * @return string url de validation de l'inscription
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_SONDAGE ($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_SONDAGE', array('id_sondage'));
	}


	/**
	 * balise_FORMULAIRE_SONDAGE_stat
	 *
	 * @param array args
	 * @param array filtres
	 * @return array args si le sondage est "votable"
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_SONDAGE_stat($args, $filtres) {
		// Pas d'id_sondage ? Erreur de squelette
		if (!$args[0])
			return erreur_squelette(_T('zbug_champ_hors_motif', array ('champ' => '#FORMULAIRE_SONDAGE', 'motif' => 'SONDAGES')), '');

		// On ne peut pas "voir les résultats"/"donner son avis" si le sondage n'est pas en ligne et publié/terminé
		$requete_statut = 'SELECT statut, en_ligne FROM spip_sondages WHERE id_sondage="'.$args[0].'"';
		list($statut, $en_ligne) = spip_fetch_array(spip_query($requete_statut));
		if ($statut == 'en_attente' OR $en_ligne == 'non')
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
		$resultat_statut = spip_query($requete_statut);
		list($statut) = spip_fetch_array($resultat_statut);
		
		if ($statut == 'termine') { // le sondage est terminé ou l'internaute a déjà voté
			return	array(
						'resultat_sondage', 
						0,
						array(
							'sondage_termine'	=> ' ',
							'sondage_deja_vote'	=> '',
							'sondage_merci'		=> '',
							'id_sondage'		=> $id_sondage
						)
					);
		
		} else { // le sondage est en cours
			$choix		= _request('choix_'.$id_sondage);
			$valider	= _request('valider');
			$ip			= $_SERVER['REMOTE_ADDR'];

			$requete_deja_vote = 'SELECT id_sonde FROM spip_sondes WHERE id_sondage="'.$id_sondage.'" AND ip="'.$ip.'"';
			$resultat_deja_vote = spip_query($requete_deja_vote);
			$deja_vote = spip_num_rows($resultat_deja_vote);
			if ($deja_vote == 1) {
				return	array(
							'resultat_sondage', 
							0,
							array(
								'sondage_termine'	=> '',
								'sondage_deja_vote'	=> ' ',
								'sondage_merci'		=> '',
								'id_sondage'		=> $id_sondage
							)
						);
			} else {
				if (!empty($choix) AND !empty($valider)) {
					spip_query('INSERT INTO spip_sondes (id_sondage, ip, date) VALUES ("'.$id_sondage.'", "'.$ip.'", NOW())');
					$id_sonde = spip_insert_id();
					foreach ($choix as $id_choix) {
						spip_query('INSERT INTO spip_avis (id_sonde, id_choix) VALUES ("'.$id_sonde.'", "'.intval($id_choix).'")');
					}
				
					return	array(
								'resultat_sondage', 
								0,
								array(
									'sondage_termine'	=> '',
									'sondage_deja_vote'	=> '',
									'sondage_merci'		=> ' ',
									'id_sondage'		=> $id_sondage
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
	}


?>