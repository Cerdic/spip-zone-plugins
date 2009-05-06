<?php


	/**
	 * SPIP-Plans
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('plans_fonctions');


	/**
	 * plan
	 *
	 * @copyright 2006-2009 Artégo
	 */

	class plan {

	    var $id_plan;
	    var $titre;
		var $descriptif;
		var $maj;
		var $statut = 'hors_ligne';

		var $existe = false;


		/**
		 * plan : constructeur
		 *
		 * @param int id_plan
		 * @return void
		 **/
		function plan($id_plan=-1) {
			$this->id_plan = $id_plan;
			if ($this->id_plan == -1) {
				$this->titre = _T('plans:nouveau_plan');
			} else {
				$res = sql_select('*', 'spip_plans', 'id_plan='.intval($this->id_plan));
				if (sql_count($res)) {
					$arr = sql_fetch($res);
					$this->titre		= $arr['titre'];
					$this->descriptif	= $arr['descriptif'];
					$this->maj			= $arr['maj'];
					$this->statut		= $arr['statut'];
					$this->existe		= true;
				}
			}
		}


		function enregistrer() {
			if ($this->id_plan == -1) {
				$champs = array(
								'titre' => $this->titre,
								'descriptif' => $this->descriptif,
								'maj' => 'NOW()',
								'statut' => 'hors_ligne'
								);
				$this->id_plan = sql_insertq('spip_plans', $champs);
				$this->existe = true;
			} else {
				$champs = array(
								'titre' => $this->titre,
								'descriptif' => $this->descriptif,
								'date' => 'NOW()'
								);
				sql_updateq('spip_plans', $champs, 'id_plan='.intval($this->id_plan));
			}
		}


		function enregistrer_statut($statut) {
			switch ($statut) {
				case 'hors_ligne':
					sql_updateq('spip_plans', array('statut' => 'hors_ligne', 'maj' => 'NOW()'), 'id_plan='.intval($this->id_plan));
					$redirection = generer_url_ecrire('plans', 'id_plan='.$this->id_plan, true);
					break;
				case 'en_ligne':
					sql_updateq('spip_plans', array('statut' => 'en_ligne', 'maj' => 'NOW()'), 'id_plan='.intval($this->id_plan));
					$redirection = generer_url_ecrire('plans', 'id_plan='.$this->id_plan, true);
					break;
				case 'poubelle':
					$this->supprimer();
					$redirection = generer_url_ecrire('plans_tous');
					break;
			}
			return $redirection;
		}


		function ajouter_logo($fichier, $type) {
			$chemin_fichier	= $fichier['tmp_name'];
			$mime			= $fichier['type'];
			$logo = '';
			if ($mime == 'image/jpeg') {
				$logo = _DIR_IMG.'plan'.$type.$this->id_plan.'.jpg';
			}
			if (strcmp($mime, 'image/png') == 0) {
				$logo = _DIR_IMG.'plan'.$type.$this->id_plan.'.png';
			}
			if (strcmp($mime, 'image/gif') == 0) {
				$logo = _DIR_IMG.'plan'.$type.$this->id_plan.'.gif';
			}
			if (!empty($logo)) {
				move_uploaded_file($chemin_fichier, $logo);
			}
		}


		function calculer_nb_points() {
			return sql_countsel('spip_points', 'id_plan='.intval($this->id_plan));
		}
		
		
		function supprimer() {
			$this->supprimer_points();
			sql_delete('spip_points', 'id_plan='.intval($this->id_plan));
			// suppression logos
			$logo_f = charger_fonction('chercher_logo', 'inc');
			if ($logo_on = $logo_f($this->id_plan, 'id_plan', 'on'))
				unlink($logo_on[0]);
			if ($logo_off = $logo_f($this->id_plan, 'id_plan', 'off'))
				unlink($logo_off[0]);
			sql_delete('spip_plans', 'id_plan='.intval($this->id_plan));
			sql_delete('spip_mots_plans', 'id_plan='.intval($this->id_plan));
		}


		function supprimer_points() {
			$res = sql_select('id_plan, id_point', 'spip_points', 'id_plan='.intval($this->id_plan));
			while ($arr = sql_fetch($res)) {
				$point = new point($arr['id_plan'], $arr['id_point']);
				$point->supprimer();
			}
		}
		
		
	}


	/**
	 * point
	 *
	 * @copyright 2006-2009 Artégo
	 */

	class point {

	    var $id_point;
	    var $id_plan;
	    var $titre;
	    var $lien;
		var $descriptif;
		var $abscisse;
		var $ordonnee;
		var $z_index;

		var $existe = false;


		/**
		 * point : constructeur
		 *
		 * @param int id_point
		 * @return void
		 **/
		function point($id_plan, $id_point=-1) {
			$this->id_point = $id_point;
			$this->id_plan = $id_plan;
			if ($this->id_point == -1) {
				$this->titre	= _T('plans:nouveau_point');
				$this->z_index	= sql_countsel('spip_points', 'id_plan='.intval($this->id_plan));
			} else {
				$res = sql_select('*', 'spip_points', 'id_point='.intval($this->id_point).' AND id_plan='.intval($this->id_plan));
				if (sql_count($res)) {
					$arr = sql_fetch($res);
					$this->titre		= $arr['titre'];
					$this->lien			= $arr['lien'];
					$this->descriptif	= $arr['descriptif'];
					$this->abscisse		= $arr['abscisse'];
					$this->ordonnee		= $arr['ordonnee'];
					$this->z_index		= $arr['z_index'];
					$this->existe		= true;
				}
			}
		}


		function enregistrer() {
			if ($this->id_point == -1) {
				$this->z_index = sql_countsel('spip_points', 'id_plan='.intval($this->id_plan));
				$champs = array(
								'id_plan' => intval($this->id_plan),
								'titre' => $this->titre,
								'lien' => $this->lien,
								'descriptif' => $this->descriptif,
								'abscisse' => intval($this->abscisse),
								'ordonnee' => intval($this->ordonnee),
								'z_index' => intval($this->z_index)
								);
				$this->id_point = sql_insertq('spip_points', $champs);
				$this->existe = true;
			} else {
				$champs = array(
								'id_plan' => intval($this->id_plan),
								'titre' => $this->titre,
								'lien' => $this->lien,
								'descriptif' => $this->descriptif,
								'abscisse' => intval($this->abscisse),
								'ordonnee' => intval($this->ordonnee)
								);
				sql_updateq('spip_points', $champs, 'id_point='.intval($this->id_point));
			}
			sql_updateq('spip_plans', array('maj' => 'NOW()'), 'id_plan='.intval($this->id_plan));
		}


		function supprimer() {
			$this->enregistrer_z_index('tout_au_dessus');
			// suppression logos
			$logo_f = charger_fonction('chercher_logo', 'inc');
			if ($logo_on = $logo_f($this->id_point, 'id_point', 'on'))
				unlink($logo_on[0]);
			if ($logo_off = $logo_f($this->id_point, 'id_point', 'off'))
				unlink($logo_off[0]);
			sql_delete('spip_points', 'id_point='.intval($this->id_point));
			sql_updateq('spip_plans', array('maj' => 'NOW()'), 'id_plan='.intval($this->id_plan));
		}


		function ajouter_logo($fichier, $type) {
			$chemin_fichier	= $fichier['tmp_name'];
			$mime			= $fichier['type'];
			$logo = '';
			if ($mime == 'image/jpeg') {
				$logo = _DIR_IMG.'point'.$type.$this->id_point.'.jpg';
			}
			if (strcmp($mime, 'image/png') == 0) {
				$logo = _DIR_IMG.'point'.$type.$this->id_point.'.png';
			}
			if (strcmp($mime, 'image/gif') == 0) {
				$logo = _DIR_IMG.'point'.$type.$this->id_point.'.gif';
			}
			if (!empty($logo)) {
				if (file_exists($logo))
					unlink($logo);
				move_uploaded_file($chemin_fichier, $logo);
			}
		}


		function enregistrer_z_index($position) {
			$resultat_tous_les_points = sql_select('id_point', 'spip_points', 'id_plan='.intval($this->id_plan).' AND id_point!='.intval($this->id_point), '', 'z_index');
			if ($position === 'tout_au_dessus') {
				$tableau_points = array();
				while ($arr = sql_fetch($resultat_tous_les_points)) {
					$tableau_points[] = $arr['id_point'];
				}
				$tableau_final = array_merge($tableau_points, array($this->id_point));
			} else if ($position == 0) {
				$tableau_points = array();
				while ($arr = sql_fetch($resultat_tous_les_points)) {
					$tableau_points[] = $arr['id_point'];
				}
				$tableau_final = array_merge(array($this->id_point), $tableau_points);
			} else {
				$i = 0;
				$tableau_points_avant = array();
				$tableau_points_apres = array();
				$deuxieme_tableau = false;
				while ($arr = sql_fetch($resultat_tous_les_points)) {
					if ($position == $i)
						$deuxieme_tableau = true;
					if ($deuxieme_tableau)
						$tableau_points_apres[] = $arr['id_point'];
					else
						$tableau_points_avant[] = $arr['id_point'];
					$i++;
				}
				$tableau_final = array_merge($tableau_points_avant, array($this->id_point), $tableau_points_apres);
			}
			foreach ($tableau_final as $cle => $valeur) {
				sql_update('spip_points', array('z_index' => $cle), 'id_point='.intval($valeur));
			}
			sql_updateq('spip_plans', array('maj' => 'NOW()'), 'id_plan='.intval($this->id_plan));
		}


	}


?>