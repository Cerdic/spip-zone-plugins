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
	include_spip('inc/rubriques');


	/**
	 * sondage
	 *
	 * @copyright 2006-2009 Artégo
	 */

	class sondage {

	    var $id_sondage;
		var $id_rubrique;
		var $titre;
		var $texte;
		var $date;
		var $maj;
		var $lang;
		var $statut = 'prepa';

		var $existe = false;


		function sondage($id_sondage=-1) {
			$this->id_sondage = intval($id_sondage);
			$verif = sql_select('*', 'spip_sondages', 'id_sondage='.intval($this->id_sondage));
			if (sql_count($verif) == 1) {
				$sondage = sql_fetch($verif);
				$this->id_rubrique	= $sondage['id_rubrique'];
				$this->titre		= $sondage['titre'];
				$this->texte		= $sondage['texte'];
				$this->ps			= $sondage['ps'];
				$this->date			= $sondage['date'];
				$this->maj			= $sondage['maj'];
				$this->lang			= $sondage['lang'];
				$this->statut		= $sondage['statut'];
				$this->existe		= true;
			}
		}
		

		function enregistrer() {
			if ($this->id_sondage == -1) {
				$champs = array(
								'id_rubrique' => intval($this->id_rubrique),
								'titre' => $this->titre,
								'texte' => $this->texte,
								'date' => 'NOW()',
								'maj' => 'NOW()'
								);
				$this->id_sondage = sql_insertq('spip_sondages', $champs);
				$this->existe = true;
				sql_updateq("spip_documents_liens", array("id_objet" => $this->id_sondage), 'id_objet='.(0 - $GLOBALS['visiteur_session']['id_auteur']).' AND objet="sondage"');
				sondages_trig_propager_les_secteurs($dummy);
				sondages_calculer_langues_rubriques($dummy);
			} else {
				$champs = array(
								'id_rubrique' => intval($this->id_rubrique),
								'titre' => $this->titre,
								'texte' => $this->texte,
								'date' => 'NOW()',
								'maj' => 'NOW()'
								);
				sql_updateq('spip_sondages', $champs, 'id_sondage='.intval($this->id_sondage));
				calculer_rubriques();
				sondages_trig_propager_les_secteurs($dummy);
				sondages_calculer_langues_rubriques($dummy);
			}
		}


		function enregistrer_statut($statut) {
			switch ($statut) {
				case 'prepa':
					sql_updateq('spip_sondages', array('statut' => 'prepa', 'maj' => 'NOW()'), 'id_sondage='.intval($this->id_sondage));
					calculer_rubriques();
					$redirection = generer_url_ecrire('sondages', 'id_sondage='.$this->id_sondage, true);
					break;
				case 'publie':
					sql_updateq('spip_sondages', array('statut' => 'publie', 'maj' => 'NOW()'), 'id_sondage='.intval($this->id_sondage));
					calculer_rubriques();
					$redirection = generer_url_ecrire('sondages', 'id_sondage='.$this->id_sondage, true);
					break;
				case 'purge':
					$this->purger();
					$redirection = generer_url_ecrire('sondages', 'id_sondage='.$this->id_sondage, true);
					break;
				case 'termine':
					sql_updateq('spip_sondages', array('statut' => 'termine', 'maj' => 'NOW()'), 'id_sondage='.intval($this->id_sondage));
					calculer_rubriques();
					$redirection = generer_url_ecrire('sondages', 'id_sondage='.$this->id_sondage, true);
					break;
				case 'poubelle':
					$id_rubrique = $this->id_rubrique;
					$this->supprimer();
					$redirection = generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique, true);
					break;
			}
			return $redirection;
		}
		
		
		function calculer_nb_choix() {
			$nb_choix = sql_countsel('spip_choix', 'id_sondage='.intval($this->id_sondage));
			return $nb_choix;
		}
		
		
		function purger() {
			sql_delete('spip_sondes', 'id_sondage='.intval($this->id_sondage));
			sql_delete('spip_avis', 'id_sondage='.intval($this->id_sondage));
		}
		
		
		function supprimer() {
			sql_delete('spip_sondages', 'id_sondage='.intval($this->id_sondage));
			sql_delete('spip_choix', 'id_sondage='.intval($this->id_sondage));
			sql_delete('spip_sondes', 'id_sondage='.intval($this->id_sondage));
			sql_delete('spip_avis', 'id_sondage='.intval($this->id_sondage));
			sql_delete('spip_mots_sondages', 'id_sondage='.intval($this->id_sondage));
			sql_delete('spip_auteurs_sondage', 'id_sondage='.intval($this->id_sondage));
			// suppression logos
			$logo_f = charger_fonction('chercher_logo', 'inc');
			if ($logo_on = $logo_f($this->id_sondage, 'id_sondage', 'on'))
				unlink($logo_on[0]);
			if ($logo_off = $logo_f($this->id_sondage, 'id_sondage', 'off'))
				unlink($logo_off[0]);
			// suppression documents
			$res = sql_select('D.*', 'spip_documents_liens AS DL INNER JOIN spip_documents AS D ON D.id_document=DL.id_document', 'DL.id_objet='.intval($this->id_sondage).' AND DL.objet="sondage"');
			$supprimer_document = charger_fonction('supprimer_document','action');
			while ($arr = sql_fetch($res))
				$supprimer_document($arr['id_document']);
			sql_delete('spip_documents_liens', 'id_objet='.intval($this->id_sondage).' AND objet="sondage"');
			calculer_rubriques();
			sondages_trig_propager_les_secteurs($dummy);
			sondages_calculer_langues_rubriques($dummy);
		}


	}


	class choix {	
		
		var $id_choix;
	    var $id_sondage;
		var $titre;
		var $ordre;

		var $existe = false;

		function choix($id_choix=-1, $id_sondage=0) {
			$this->id_choix = intval($id_choix);
			$verif = sql_select('*', 'spip_choix', 'id_choix='.intval($this->id_choix));
			if (sql_count($verif) == 1) {
				$choix = sql_fetch($verif);
				$this->id_sondage	= $choix['id_sondage'];
				$this->titre		= $choix['titre'];
				$this->ordre		= $choix['ordre'];
				$this->existe		= true;
			} else {
				$this->titre 		= _T('sondagesprive:nouveau_choix');
				$this->id_sondage	= $id_sondage;
				$sondage = new sondage($id_sondage);
				$this->ordre 		= $sondage->calculer_nb_choix();
			}
		}
		

		function enregistrer() {
			if ($this->id_choix == -1) {
				$this->ordre = sql_countsel('spip_choix', 'id_sondage='.intval($this->id_sondage));
				$champs = array(
								'id_sondage' => intval($this->id_sondage),
								'titre' => $this->titre,
								'ordre' => intval($this->ordre)
								);
				$this->id_choix = sql_insertq('spip_choix', $champs);
			} else {
				$champs = array(
								'id_sondage' => intval($this->id_sondage),
								'titre' => $this->titre
								);
				sql_updateq('spip_choix', $champs, 'id_choix='.intval($this->id_choix));
			}
		}

		
		function enregistrer_position($position) {
			$resultat_tous_les_choix = sql_select('id_choix', 'spip_choix', 'id_sondage='.intval($this->id_sondage).' AND id_choix!='.intval($this->id_choix), '', 'ordre');
			if ($position === 'dernier') {
				$tableau_choix = array();
				while ($arr = sql_fetch($resultat_tous_les_choix)) {
					$tableau_choix[] = $arr['id_choix'];
				}
				$tableau_final = array_merge($tableau_choix, array($this->id_choix));
			} else if ($position == 0) {
				$tableau_choix = array();
				while ($arr = sql_fetch($resultat_tous_les_choix)) {
					$tableau_choix[] = $arr['id_choix'];
				}
				$tableau_final = array_merge(array($this->id_choix), $tableau_choix);
			} else {
				$i = 0;
				$tableau_choix_avant = array();
				$tableau_choix_apres = array();
				$deuxieme_tableau = false;
				while ($arr = sql_fetch($resultat_tous_les_choix)) {
					if ($position == $i)
						$deuxieme_tableau = true;
					if ($deuxieme_tableau)
						$tableau_choix_apres[] = $arr['id_choix'];
					else
						$tableau_choix_avant[] = $arr['id_choix'];
					$tableau_choix[] = $arr['id_choix'];
					$i++;
				}
				$tableau_final = array_merge($tableau_choix_avant, array($this->id_choix), $tableau_choix_apres);
			}
			foreach ($tableau_final as $cle => $valeur) {
				sql_update('spip_choix', array('ordre' => $cle), 'id_choix='.intval($valeur));
			}
		}


		function calculer_nb_votes() {
			$nb_votes = sql_countsel('spip_avis', 'id_choix='.intval($this->id_choix));
			return $nb_votes;
		}
		
		
		function supprimer() {
			$this->enregistrer_position('dernier');
			sql_delete('spip_avis', 'id_choix='.intval($this->id_choix));
			sql_delete('spip_choix', 'id_choix='.intval($this->id_choix));
		}
		
		
	}
	
	
?>