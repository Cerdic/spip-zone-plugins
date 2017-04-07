<?php
if (!defined('_ECRIRE_INC_VERSION'))
	return;

	function inc_donnees_reservations_details_dist($id_reservations_detail, $set) {
		if (count($set) > 0) {
			include_spip('inc/filtres');
			$reservations_details = sql_fetsel('*', 'spip_reservations_details', 'id_reservations_detail=' . $id_reservations_detail);

			$id_evenement = isset($set['id_evenement']) ? $set['id_evenement'] : $reservations_details['id_evenement'];
			// Les données de l'évènenement

			$evenement = sql_fetsel('*', 'spip_evenements', 'id_evenement=' . $id_evenement);

			$date_debut = $evenement['date_debut'];
			if (!$date_fin = sql_getfetsel('date_fin', 'spip_evenements', 'id_evenement_source=' . $id_evenement, '', 'date_debut DESC'))
				$date_fin = $evenement['date_fin'];

				// On établit les dates
				if ($date_debut != $date_fin) {
					if (affdate($date_debut, 'd-m-Y') == affdate($date_fin, 'd-m-Y')) {
						$date = affdate($date_debut, 'd/m/Y') . ',' . affdate($date_debut, 'G:i') . '-' . affdate($date_fin, 'G:i');
					}
					else {
						$date = affdate($date_debut, 'd/m/Y') . '-' . affdate($date_fin, 'd/m/Y') . ', ' . affdate($date_debut, 'nom_jour') . ' ' . affdate($date_debut, 'G:i') . '-' . affdate($date_fin, 'G:i');
					}
				}
				else {
					if (affdate($date_debut, 'G:i') == '0:00')
						$date = affdate($date_debut, 'd/m/Y');
						else
							$date = affdate($date_debut, 'd/m/Y G:i');
				}

				// Les descriptif
				$set['descriptif'] = supprimer_numero($evenement['titre']) . '  (' . $date . ')';
				if (intval($evenement['places']))
					$set['places'] = $evenement['places'];

					$set['quantite'] = _request('quantite') ? _request('quantite') : 1;
					if (is_array($set['quantite']) and isset($set['quantite'][$id_evenement]))
						$set['quantite'] = ($set['quantite'][$id_evenement] > 0) ? $set['quantite'][$id_evenement] : 1;

						$quantite = $set['quantite'];

						// Si le prix n'est pas fournit, on essaye de le trouver
						if (!isset($set['prix']) and !isset($set['prix_ht'])) {

							/* Existence d'un prix via le plugin Prix Objets http://plugins.spip.net/prix_objets.html */
							if ($prix_objets = test_plugin_actif('prix_objets')) {
								$fonction_prix = charger_fonction('prix', 'inc/');
								$fonction_prix_ht = charger_fonction('ht', 'inc/prix');

								// si le plugin déclinaison produit (http://plugins.spip.net/declinaisons.html)
								// est active il peut y avoir plusieurs prix par évenement
								if (test_plugin_actif('declinaisons')) {
									$id_prix = isset($set['id_prix_objet']) ? $set['id_prix_objet'] : $reservations_details['id_prix_objet'];
									$p = sql_fetsel('prix_ht,id_prix_objet,id_declinaison,code_devise,taxe', 'spip_prix_objets', 'id_prix_objet=' . $id_prix);
									if ($p['id_declinaison'] > 0)
										$set['descriptif'] .= ' - ' . supprimer_numero(sql_getfetsel('titre', 'spip_declinaisons', 'id_declinaison=' . $p['id_declinaison']));
								}
								// Sinon on cherche d'abord le prix attaché
								else {
									$p = prix_attache($id_evenement, $evenement['id_article'], $evenement['id_evenement_source']);
								}

								if (isset($p)) {
									$prix_ht = $quantite * $fonction_prix_ht('prix_objet', $p['id_prix_objet']);
									$prix = $quantite * $fonction_prix('prix_objet', $p['id_prix_objet']);
									if ($prix_ht)
										$taxe = $p['taxe'];
										$set['prix_ht'] = $prix_ht;
										$set['prix'] = $prix;
										$set['taxe'] = $taxe;
										$set['id_prix_objet'] = $p['id_prix_objet'];
										// Si pas de devise fournit par le contexte, on prend celle de prix_objets
										if (!isset($set['devise']))
											$set['devise'] = $p['code_devise'];
								}
							}
							// Sinon voir s'il y a un champ prix dans la table evenement
							elseif (intval($evenement['prix']) or intval($evenement['prix_ht'])) {
								$set = etablir_prix($id_evenement, 'evenement', $evenement, $set, $quantite);
							}
							// Sinon voir s'il y a un champ prix dans la table articles
							elseif ($article = sql_fetsel('*', 'spip_articles', 'id_article=' . $evenement['id_article'])) {
								$set = etablir_prix($evenement['id_article'], 'article', $article, $set, $quantite);
							}
						}
		}
		return $set;
	}

	function etablir_prix($id, $objet, $datas, $set, $quantite) {
		if ($fonction_prix = charger_fonction('prix', 'inc/', true)) {
			$prix = $fonction_prix($objet, $id);
		}
		else
			$prix = $datas['prix'];

			if ($fonction_prix_ht = charger_fonction('ht', 'inc/prix', true)) {
				$prix_ht = $fonction_prix_ht($objet, $id);
			}
			elseif (intval($datas['prix_ht']))
			$prix_ht = $datas['prix_ht'];

			if ($prix_ht) {
				$prix = $quantite * $prix;
				$prix_ht = $quantite * $prix_ht;
				if ($prix_ht > 0) {
					$taxe = round(($prix - $prix_ht) / $prix_ht, 3);
				}
				$set['prix_ht'] = $prix_ht;
				$set['prix'] = $prix;
				$set['taxe'] = $taxe;
			}
			else
				$set['prix'] = $quantite * $prix;

				return $set;
	}

	/**
	 * Etablir le prix de l'objet
	 *
	 * @param int $id_evenement
	 *         Identifiant de l'événement.
	 * @param int $id_article
	 *         Identifiant de l'article.
	 * @param int $id_evenement_source
	 *         Identifiant source de l'événement
	 * @return array Les données du prix.
	 */
	function prix_attache($id_evenement, $id_article, $id_evenement_source) {
		// Etablir le prix de l'événement sinon de l'article.
		if (!$p = sql_fetsel(
				'prix_ht,prix,id_prix_objet,code_devise,taxe',
				'spip_prix_objets',
				'objet="evenement" AND id_objet=' . $id_evenement)) {
				if (!$p = sql_fetsel(
						'prix_ht,prix,id_prix_objet,code_devise,taxe',
						'spip_prix_objets',
						'objet="evenement" AND id_objet=' . $id_evenement_source)) {
						$p = sql_fetsel(
								'prix_ht,prix,id_prix_objet,code_devise,taxe',
								'spip_prix_objets',
								'objet="article" AND id_objet=' . $id_article);
				}
		}
		return $p;
	}
