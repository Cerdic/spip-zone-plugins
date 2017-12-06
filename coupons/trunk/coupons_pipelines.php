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

function coupons_post_edition($flux) {
	
	if (
		$flux['args']['table'] == 'spip_commandes'
		&& $flux['args']['action'] == 'instituer'
		&& $flux['data']['statut'] == 'paye'
	) {
		$id_commande = intval($flux['args']['id_objet']);
		
		// 1 - au paiement de la commande, marquer les coupons comme utilisés
		
		$infos_coupons = sql_allfetsel(
			'id_objet',
			'spip_commandes_details',
			'id_commande = ' . $id_commande . ' AND objet = "coupon"');

		foreach ($infos_coupons as $coupon) {
			sql_updateq('spip_coupons', array('id_commande' => $id_commande), 'id_coupon = ' . $coupon['id_objet']);
			spip_log('coupon '.$coupon['id_objet'].' utilisé par commande '.$id_commande, 'coupons');
		}

		// 2 - générer un coupon pour chaque bon d'achat dans la commande

		$infos_bon_cadeau = sql_allfetsel(
			'cd.*, p.titre, p.taxe',
			'spip_commandes_details cd JOIN spip_produits p ON (cd.id_objet = p.id_produit AND cd.objet="produit")',
			'cd.id_commande = ' . $id_commande . ' AND p.bon_cadeau = "on"');

		foreach ($infos_bon_cadeau as $bon) {

			// ne pas recréer le coupon s'il existe déjà
			if (!sql_getfetsel(
				'id_coupon',
				'spip_coupons',
				'id_commandes_detail_origine = ' . $bon['id_commandes_detail'])) {

				// montant TTC du bon de réduction
				$montant_coupon = $bon['quantite'] * $bon['prix_unitaire_ht'] * (1 + $bon['taxe']);
				if ($montant_coupon) {
					include_spip('action/editer_objet');
					$id_coupon = objet_inserer('coupon');
					$valeurs_coupon   = array(
						'montant'                    => $montant_coupon,
						'restriction_taxe'           => $bon['taxe'],
						'id_commandes_detail_origine' => $bon['id_commandes_detail'],
						'titre'                      => $bon['titre'] . ' ' . html_entity_decode(prix_formater($montant_coupon)),
						'code'                       => coupon_generer_code(),
					);
					objet_modifier('coupon', $id_coupon, $valeurs_coupon);

					// envoi d'une notification
					if($destinataires = lire_config('coupons/emails_notifications')) {
						$expediteur   = lire_config('email_webmaster');
						$texte        = recuperer_fond('notifications/notification_coupon', $valeurs_coupon);
						$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
						$envoyer_mail($destinataires, _T('coupons:creation_coupon'), $texte, $expediteur);
					}


					spip_log('création du coupon ' . $id_coupon, 'coupons');
					spip_log($valeurs_coupon, 'coupons');

				} else {
					// comment est ce possible ?
					spip_log('montant coupon null ', 'coupons');
					spip_log($bon, 'coupons');
				}

			}

		}
	}
	
	return $flux;
}