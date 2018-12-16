<?php
/**
 * Utilisations de pipelines par Coupons de réduction
 *
 * @plugin     Coupons de réduction
 * @copyright  2017
 * @author     Nicolas Dorigny
 * @licence    GNU/GPL
 * @package    SPIP\Coupons\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Générer un code autoamtique pour le coupon s'il n'en a pas
 *
 * @param array $flux
 *
 * @return array
 */
function coupons_pre_insertion($flux) {
	if ($flux['args']['table'] == 'spip_coupons' && !trim($flux['data']['code'])) {
		$flux['data']['code'] = coupon_generer_code();
	}
	return $flux;
}

function coupons_post_edition($flux) {

	if (
		$flux['args']['table'] == 'spip_coupons'
	) {
		// désactiver un coupon s'il n'est plus utilisable
		$id_coupon = intval($flux['args']['id_objet']);
		if (!coupon_utilisable($id_coupon)) {
			include_spip('action/editer_objet');
			sql_updateq('spip_coupons', array('actif' => ''), 'id_coupon = ' . $id_coupon);
		}
	}

	if (
		$flux['args']['table'] == 'spip_commandes'
		&& $flux['args']['action'] == 'remplir_commande'
	) {
		include_spip('inc/session');

		// si on a un coupon utilisable en session, le remettre dans la commande
		if ($id_coupon = session_get('id_coupon')) {
			if (coupon_utilisable($id_coupon)) {
				$id_commande = intval($flux['args']['id_objet']);
				coupon_ajouter_commande($id_coupon, $id_commande);
			}
		}
	}

	if (
		$flux['args']['table'] == 'spip_commandes'
		&& $flux['args']['action'] == 'instituer'
		&& $flux['data']['statut'] == 'paye'
	) {
		$id_commande = intval($flux['args']['id_objet']);

		include_spip('inc/session');
		include_spip('action/editer_objet');

		// 1 - au paiement de la commande, traiter les coupons utilisés
		// (normalement il ne peut y en avoir qu'un seul)

		$infos_coupons = sql_allfetsel(
			'id_objet, prix_unitaire_ht',
			'spip_commandes_details',
			'id_commande = ' . $id_commande . ' AND objet = "coupon"');

		foreach ($infos_coupons as $coupon) {
			sql_insertq(
				'spip_coupons_commandes',
				array(
					'id_coupon'   => $coupon['id_objet'],
					'id_commande' => $id_commande,
					'id_auteur'   => $GLOBALS['visiteur_session']['id_auteur'],
					'montant'     => abs($coupon['prix_unitaire_ht']),
				));
			spip_log('coupon ' . $coupon['id_objet'] . ' utilisé par commande ' . $id_commande . ' - montant : ' . $coupon['prix_unitaire_ht'], 'coupons');

			// si le coupon en entièrement utilisé, on le désactive
			if (!coupon_montant_utilisable($coupon['id_objet'])) {
				objet_modifier('coupon', $coupon['id_objet'], array('actif' => ''));
			}
		}

		// supprimer le coupon de la session
		session_set('id_coupon', '');

		// 2 - générer un coupon pour chaque produit "bon d'achat" dans la commande

		$infos_bon_cadeau = sql_allfetsel(
			'cd.*, p.titre, p.taxe',
			'spip_commandes_details cd JOIN spip_produits p ON (cd.id_objet = p.id_produit AND cd.objet="produit")',
			'cd.id_commande = ' . $id_commande . ' AND p.bon_cadeau = "on"');

		foreach ($infos_bon_cadeau as $bon) {

			for ($i = 0; $i < $bon['quantite']; $i++) {
				// montant TTC du bon de réduction
				$montant_coupon = $bon['prix_unitaire_ht'] * (1 + $bon['taxe']);
				if ($montant_coupon) {

					$id_coupon      = objet_inserer('coupon');
					$valeurs_coupon = array(
						'montant'             => $montant_coupon,
						'restriction_taxe'    => $bon['taxe'],
						'id_commandes_detail' => $bon['id_commandes_detail'],
						'titre'               => $bon['titre'],
						'code'                => coupon_generer_code(),
						'actif'               => 'on',
						'date_validite'       => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' + ' . lire_config('coupons/duree_validite') . ' days')),
					);
					objet_modifier('coupon', $id_coupon, $valeurs_coupon);

					// envoi d'une notification
					if ($destinataires = lire_config('coupons/emails_notifications')) {
						$expediteur   = lire_config('email_webmaster');
						$texte        = recuperer_fond('notifications/notification_coupon', $valeurs_coupon);
						$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
						$envoyer_mail($destinataires, _T('coupons:creation_coupon'), $texte, $expediteur);
					}

					spip_log('création du coupon ' . $id_coupon, 'coupons');
					spip_log($valeurs_coupon, 'coupons');
				}
			}

		}
	}

	return $flux;
}

function coupons_affiche_milieu($flux) {
	
	if ($flux['args']['exec'] == 'commande' && $flux['args']['id_commande']) {
		$details              = sql_allfetsel(
			'id_commandes_detail',
			'spip_commandes_details',
			'id_commande=' . $flux['args']['id_commande']
		);
		$id_commandes_details = array();
		foreach ($details as $detail) {
			$id_commandes_details[] = $detail['id_commandes_detail'];
		}
		if (count($id_commandes_details)) {
			$texte = recuperer_fond(
				'prive/objets/liste/coupons',
				array(
					'where' => 'id_commandes_detail IN (' . join(',', $id_commandes_details) . ')',
				)
			);
			if (($p = strpos($flux['data'], '<!--afficher_fiche_objet-->')) !== false) {
				$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
			} else {
				$flux['data'] .= $texte;
			}
		}
	}

	if ($flux['args']['exec'] == 'produit' && $flux['args']['id_produit']) {
		$texte = recuperer_fond(
			'prive/objets/liste/coupons',
			array(
				'id_produit' => $flux['args']['id_produit'],
			)
		);
		if (($p = strpos($flux['data'], '<!--afficher_fiche_objet-->')) !== false) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}