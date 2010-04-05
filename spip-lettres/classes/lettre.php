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
	include_spip('public/assembler');
	include_spip('inc/distant');
	include_spip('inc/rubriques');
	include_spip('base/lettres');
	include_spip('classes/abonne');

	/**
	 * lettre - classe pour la gestion des lettres
	 *
	 * @copyright 2006-2009 Artégo
	 */

	class lettre {

	    var $id_lettre;
		var $id_rubrique;
		var $titre;
		var $descriptif;
		var $chapo;
		var $texte;
		var $date;
		var $lang;
		var $message_html;
		var $message_texte;
		var $date_debut_envoi;
		var $date_fin_envoi;
		var $extra;
		var $statut = 'brouillon';

		var $existe = false;


		/**
		 * lettre : constructeur
		 *
		 * @param int id_lettre
		 * @return void
		 **/
		function lettre($id_lettre=-1) {
			$this->id_lettre = intval($id_lettre);
			$verif = sql_select('*', 'spip_lettres', 'id_lettre='.intval($this->id_lettre));
			if (sql_count($verif) == 1) {
				$lettre = sql_fetch($verif);
				$this->id_rubrique				= $lettre['id_rubrique'];
				$this->titre					= $lettre['titre'];
				$this->descriptif				= $lettre['descriptif'];
				$this->chapo					= $lettre['chapo'];
				$this->texte					= $lettre['texte'];
				$this->ps						= $lettre['ps'];
				$this->date						= $lettre['date'];
				$this->lang						= $lettre['lang'];
				$this->message_html				= $lettre['message_html'];
				$this->message_texte			= $lettre['message_texte'];
				$this->date_debut_envoi			= $lettre['date_debut_envoi'];
				$this->date_fin_envoi			= $lettre['date_fin_envoi'];
				$this->statut					= $lettre['statut'];
				$this->extra					= $lettre['extra'];
				$this->existe					= true;
			}
		}
		

		function enregistrer() {
			if ($this->id_lettre == -1 and $this->statut == 'brouillon') {
				$champs = array(
								'id_rubrique' => intval($this->id_rubrique),
								'titre' => $this->titre,
								'descriptif' => $this->descriptif,
								'chapo' => $this->chapo,
								'texte' => $this->texte,
								'ps' => $this->ps,
								'date' => 'NOW()',
								'maj' => 'NOW()'
								);
				if ($this->extra)
					$champs['extra'] = $this->extra;
				$this->id_lettre = sql_insertq('spip_lettres', $champs);
				$this->existe = true;
				sql_updateq("spip_documents_liens", array("id_objet" => $this->id_lettre), 'id_objet='.(0 - $GLOBALS['visiteur_session']['id_auteur']).' AND objet="lettre"');
				calculer_rubriques();
				propager_les_secteurs();
				calculer_langues_rubriques();
			} else if ($this->statut == 'brouillon') {
				$champs = array(
								'id_rubrique' => intval($this->id_rubrique),
								'titre' => $this->titre,
								'descriptif' => $this->descriptif,
								'chapo' => $this->chapo,
								'texte' => $this->texte,
								'ps' => $this->ps,
								'date' => 'NOW()',
								'maj' => 'NOW()'
								);
				if ($this->extra)
					$champs['extra'] = $this->extra;
				sql_updateq('spip_lettres', $champs, 'id_lettre='.intval($this->id_lettre));
				calculer_rubriques();
				propager_les_secteurs();
				calculer_langues_rubriques();
			}
		}

		function tester(){
			$resultat = true;
			if ($this->statut=='brouillon'){
				$GLOBALS['var_preview'] = true;
				$GLOBALS['var_nocache'] = true;
				$GLOBALS['var_mode'] = 'recalcul';
			}
			$this->enregistrer_squelettes();
			if ($this->statut=='brouillon'){
				unset($GLOBALS['var_preview']);
				unset($GLOBALS['var_nocache']);
				unset($GLOBALS['var_mode']);
			}
			$auteurs = sql_select('A.email', 'spip_auteurs AS A INNER JOIN spip_auteurs_lettres AS AL ON AL.id_auteur=A.id_auteur', 'AL.id_lettre='.intval($this->id_lettre));
			while ($auteur = sql_fetch($auteurs)) {
				$abonne = new abonne(0, $auteur['email']);
				if (!$abonne->envoyer_lettre($this->id_lettre)) {
					$resultat = false;
					break;
				}
			}
			return $resultat;
		}


		function enregistrer_statut($statut, $cron=false, $xml=false) {
			$ancien_statut = $this->statut;
			switch ($statut) {
				case 'brouillon':
					$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre, true);
					break;
				case 'envoi_en_cours':
					if ($ancien_statut == 'brouillon') {
						$this->statut = 'envoi_en_cours';
						$this->date_debut_envoi = date('Y-m-d h:i:s');
						sql_updateq('spip_lettres', array('statut' => $this->statut, 'date_debut_envoi' => 'NOW()', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
						$this->enregistrer_squelettes();
						calculer_rubriques();
						propager_les_secteurs();
						calculer_langues_rubriques();
						$rubriques = lettres_recuperer_toutes_les_rubriques_parentes($this->id_rubrique);
						$rubriques_virgules = implode(',', $rubriques);
						$resultat_abonnes = sql_select('A.id_abonne, A.format', 'spip_abonnes_rubriques AS AR INNER JOIN spip_abonnes AS A ON A.id_abonne=AR.id_abonne', 'AR.id_rubrique IN ('.$rubriques_virgules.') AND AR.statut="valide"', 'A.id_abonne');
						while ($arr = sql_fetch($resultat_abonnes)) {
							sql_insertq('spip_abonnes_lettres', array(
																	'id_abonne' => intval($arr['id_abonne']), 
																	'id_lettre' => intval($this->id_lettre),
																	'statut' => 'a_envoyer',
																	'format' => $arr['format'],
																	'verrou' => 0,
																	'maj' => 'NOW()'
																	));
						}
						if ($cron) {
							$envois = sql_select('*', 'spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre).' AND verrou=0 AND statut="a_envoyer"');
							if (sql_count($envois) > 0) {
								while ($arr = sql_fetch($envois)) {
									$abonne = new abonne($arr['id_abonne']);
									$resultat = $abonne->envoyer_lettre($this->id_lettre);
									$abonne->enregistrer_envoi($this->id_lettre, $resultat);
								}
							}
							$this->statut = 'envoyee';
							$this->date_fin_envoi = date('Y-m-d h:i:s');
							sql_updateq('spip_lettres', array('statut' => $this->statut, 'date_fin_envoi' => 'NOW()', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
							sql_updateq('spip_abonnes_lettres', array('statut' => 'annule'), 'id_lettre='.intval($this->id_lettre).' AND statut="a_envoyer"');
						}
						$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre, true);
					}
					if ($ancien_statut == 'envoyee') {
						$this->statut = 'envoi_en_cours';
						sql_updateq('spip_lettres', array('statut' => $this->statut, 'date_debut_envoi' => 'NOW()', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
						sql_updateq('spip_abonnes_lettres', array('verrou' => 0, 'statut' => 'a_envoyer'), 'id_lettre='.intval($this->id_lettre));
						$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre, true);
					}
					if ($ancien_statut == 'envoi_en_cours') {
						$envois = sql_select('*', 'spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre).' AND verrou=0 AND statut="a_envoyer"', '', '', '10');
						if (sql_count($envois) > 0) {
							while ($arr = sql_fetch($envois)) {
								$abonne = new abonne($arr['id_abonne']);
								$resultat = $abonne->envoyer_lettre($this->id_lettre);
								$abonne->enregistrer_envoi($this->id_lettre, $resultat);
							}
							if ($xml)
								$redirection = 0;
							else
								$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre, true);
						} else {
							$this->statut = 'envoyee';
							$this->date_fin_envoi = date('Y-m-d h:i:s');
							sql_updateq('spip_lettres', array('statut' => $this->statut, 'date_fin_envoi' => 'NOW()', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
							sql_updateq('spip_abonnes_lettres', array('statut' => 'annule'), 'id_lettre='.intval($this->id_lettre).' AND statut="a_envoyer"');
							if ($xml)
								$redirection = 1;
							else
								$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre.'&message=envoi_termine', true);
						}
					}
					break;
				case 'envoyee':
					if ($ancien_statut == 'envoi_en_cours') {
						$this->statut = 'envoyee';
						$this->date_fin_envoi = date('Y-m-d h:i:s');
						sql_updateq('spip_lettres', array('statut' => $this->statut, 'date_fin_envoi' => 'NOW()', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
						sql_updateq('spip_abonnes_lettres', array('statut' => 'annule'), 'id_lettre='.intval($this->id_lettre).' AND statut="a_envoyer"');
					}
					$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre.'&message=envoi_termine', true);
					break;
				case 'poubelle':
				case 'poub':
					sql_updateq('spip_lettres', array('statut' => 'poub'), 'id_lettre='.intval($this->id_lettre));
					#$id_rubrique = $this->id_rubrique;
					#$this->supprimer();
					#$redirection = generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique, true);
					break;
			}
			return $redirection;
		}
		
		
		function callback_clic_html($matches) {
			$url = $matches[2];
			if (strcmp($url, '%%URL_VALIDATION_DESABONNEMENTS%%') != 0) {
				$verification = sql_select('id_clic', 'spip_clics', 'url='.sql_quote($url).' AND id_lettre='.intval($this->id_lettre));
				if (sql_count($verification) == 1) {
					$arr = sql_fetch($verification);
					$id_clic = $arr['id_clic'];
				} else {
					$id_clic = sql_insertq('spip_clics', array('id_lettre' => $this->id_lettre, 'url' => html_entity_decode($url)));
				}
				$url_clic = generer_url_action('clic', 'id_clic='.$id_clic.'&code=%%CODE%%&email=%%EMAIL%%', false);
				return 'href="'.$url_clic.'"';
			} else {
				return 'href="'.$url.'"';
			}
		}
		
		
		function callback_clic_texte($matches) {
			$url = $matches[0];
			if (strcmp($url, '%%URL_VALIDATION_DESABONNEMENTS%%') != 0) {
				$verification = sql_select('id_clic', 'spip_clics', 'url='.sql_quote($url).' AND id_lettre='.intval($this->id_lettre));
				if (sql_count($verification) == 1) {
					$arr = sql_fetch($verification);
					$id_clic = $arr['id_clic'];
				} else {
					$id_clic = sql_insertq('spip_clics', array('id_lettre' => $this->id_lettre, 'url' => html_entity_decode($url)));
				}
				$url_clic = generer_url_action('clic', 'id_clic='.$id_clic.'&code=%%CODE%%&email=%%EMAIL%%', true);
				return $url_clic;
			} else {
				return $url;
			}
		}
		
		
		function callback_images($matches) {
			global $i;
			$image = $matches[2];
			if (file_exists($image)) {
				$tab = explode('.', basename($image));
				$copie = _DIR_LETTRES.'lettre-'.$this->id_lettre.'-'.$i.'.'.$tab[1];
				$i++;
				if (copy($image, $copie))
					return 'src="'.$copie.'"';
			}
			return 'src="'.$image.'"';
		}
		
		
		function enregistrer_squelettes($vidange = true) {
			$this->message_html	= recuperer_fond($GLOBALS['meta']['spip_lettres_fond_lettre_html'], array('id_lettre' => $this->id_lettre, 'lang' => $this->lang));
			$this->message_texte = recuperer_fond($GLOBALS['meta']['spip_lettres_fond_lettre_texte'], array('id_lettre' => $this->id_lettre, 'lang' => $this->lang));
			
			if ($vidange) {
				// petite vidange due à l'envoi de test
				sql_delete('spip_clics', 'id_lettre='.intval($this->id_lettre));
				sql_delete('spip_abonnes_clics', 'id_lettre='.intval($this->id_lettre));
			}

			$this->message_html = preg_replace_callback('/(href=")(.*?)(")/i', array($this, 'callback_clic_html'), $this->message_html);
			$this->message_html = preg_replace_callback("/(href=')(.*?)(')/i", array($this, 'callback_clic_html'), $this->message_html);
			global $i;
			$i = 1;
			$this->message_html = preg_replace_callback('/(src=")(.*?)(")/i', array($this, 'callback_images'), $this->message_html);
			$this->message_html = preg_replace_callback("/(src=')(.*?)(')/i", array($this, 'callback_images'), $this->message_html);
			$this->message_texte = preg_replace_callback('/http:[^\s]*/', array($this, 'callback_clic_texte'), $this->message_texte);
			
			sql_updateq('spip_lettres', array('message_html' => $this->message_html, 'message_texte' => $this->message_texte, 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
		}


		function enregistrer_auteur($id_auteur) {
			$verif_email = sql_countsel('spip_auteurs', 'id_auteur='.intval($id_auteur).' AND email!=""');
			if ($verif_email) {
				sql_replace('spip_auteurs_lettres', array('id_auteur' => intval($id_auteur), 'id_lettre' => intval($this->id_lettre)));
				$email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
				$abonne = new abonne(0, $email);
				if (!$abonne->existe)
					$abonne->enregistrer();
				$abonne->enregistrer_abonnement($this->id_rubrique);
				$abonne->valider_abonnement($this->id_rubrique);
			}
		}


		function enregistrer_article($id_article) {
			$verif = sql_countsel('spip_articles_lettres', 'id_article='.intval($id_article).' AND id_lettre='.intval($this->id_lettre));
			if (!$verif)
				sql_insertq('spip_articles_lettres', array('id_article' => intval($id_article), 'id_lettre' => intval($this->id_lettre)));
		}


		function copier_lettre($copie_lettre) {
			$lettre_a_copier = new lettre($copie_lettre);
			if ($lettre_a_copier->existe) {
				$this->id_rubrique				= $lettre_a_copier->id_rubrique;
				$this->titre					= _T('lettresprive:copie').' - '.$lettre_a_copier->titre;
				$this->descriptif				= $lettre_a_copier->descriptif;
				$this->chapo					= $lettre_a_copier->chapo;
				$this->texte					= $lettre_a_copier->texte;
				$this->ps						= $lettre_a_copier->ps;
				$this->date						= $lettre_a_copier->date;
				$this->extra					= $lettre_a_copier->extra;
				$this->enregistrer();
				// auteurs
				$auteurs = sql_select('id_auteur', 'spip_auteurs_lettres', 'id_lettre='.intval($lettre_a_copier->id_lettre));
				while ($arr = sql_fetch($auteurs))
					$this->enregistrer_auteur($arr['id_auteur']);
				// logos
				$logo_f = charger_fonction('chercher_logo', 'inc');
				if ($logo = $logo_f($lettre_a_copier->id_lettre, 'id_lettre', 'on')) {
					list($fid, $dir, $nom, $format) = $logo;
					copy($fid, $dir.'lettreon'.$this->id_lettre.'.'.$format);
				}
				if ($logo = $logo_f($lettre_a_copier->id_lettre, 'id_lettre', 'off')) {
					list($fid, $dir, $nom, $format) = $logo;
					copy($fid, $dir.'lettreoff'.$this->id_lettre.'.'.$format);
				}
				// mots-clés
				$mots = sql_select('id_mot', 'spip_mots_lettres', 'id_lettre='.intval($lettre_a_copier->id_lettre));
				while ($arr = sql_fetch($mots))
					sql_insertq('spip_mots_lettres', array('id_mot' => intval($arr['id_mot']), 'id_lettre' => intval($this->id_lettre)));
			}
		}
		
		
		function calculer_nb_envois($statut='') {
			if ($statut)
				return sql_countsel('spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre).' AND statut='.sql_quote($statut));
			else
				return sql_countsel('spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre));
		}


		function calculer_pourcentage_format($format) {
			$total = $this->calculer_nb_envois();
			if ($total) {
				$nb = sql_countsel('spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre).' AND format='.sql_quote($format));
				return floor($nb / $total * 100);
			} else {
				return 0;
			}
		}
		

		function calculer_taux_ouverture() {
			$total = $this->calculer_nb_envois();
			if ($total) {
				$nb = sql_count(sql_select('AC.id_abonne', 'spip_abonnes_clics AS AC, spip_clics AS C', 'C.id_clic=AC.id_clic AND C.id_lettre='.intval($this->id_lettre), 'AC.id_abonne'));
				$pourcentage = $nb / $total * 100;
				return round($pourcentage, 2);
			} else {
				return 0;
			}
		}
		
		function supprimer() {
			sql_delete('spip_lettres', 'id_lettre='.intval($this->id_lettre));
			$res = sql_select('id_clic', 'spip_clics', 'id_lettre='.intval($this->id_lettre));
			while ($arr = sql_fetch($res))
				sql_delete('spip_abonnes_clics', 'id_clic='.intval($arr['id_clic']));
			sql_delete('spip_clics', 'id_lettre='.intval($this->id_lettre));
			sql_delete('spip_mots_lettres', 'id_lettre='.intval($this->id_lettre));
			sql_delete('spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre));
			sql_delete('spip_auteurs_lettres', 'id_lettre='.intval($this->id_lettre));
			// suppression logos
			$logo_f = charger_fonction('chercher_logo', 'inc');
			if ($logo_on = $logo_f($this->id_lettre, 'id_lettre', 'on'))
				unlink($logo_on[0]);
			if ($logo_off = $logo_f($this->id_lettre, 'id_lettre', 'off'))
				unlink($logo_off[0]);
			// suppression documents
			$res = sql_select('D.*', 'spip_documents_liens AS DL INNER JOIN spip_documents AS D ON D.id_document=DL.id_document', 'DL.id_objet='.intval($this->id_lettre).' AND DL.objet="lettre"');
			$supprimer_document = charger_fonction('supprimer_document','action');
			while ($arr = sql_fetch($res))
				$supprimer_document($arr['id_document']);
			sql_delete('spip_documents_liens', 'id_objet='.intval($this->id_lettre).' AND objet="lettre"');
			// articles associés
			sql_delete('spip_articles_lettres', 'id_lettre='.intval($this->id_lettre));
			calculer_rubriques();
			propager_les_secteurs();
			calculer_langues_rubriques();
		}


		function supprimer_article($id_article) {
			sql_delete('spip_articles_lettres', 'id_article='.intval($id_article).' AND id_lettre='.intval($this->id_lettre));
		}
		
		
	}

?>
