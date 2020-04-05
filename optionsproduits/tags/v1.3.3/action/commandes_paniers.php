<?php

/**
 * Fonction du plugin Commandes de paniers
 *
 * @plugin     Commandes de Paniers
 * @copyright  2014
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Panier2commande\Action
 */
// Sécurité
if (!defined("_ECRIRE_INC_VERSION"))
	return;

include_spip('inc/base');

/**
 * Créer une commande et remplir ses détails d'après le panier en cours du visiteur (ou d'après un panier donné).
 *
 * Pour créer une commande d'après le panier présent dans la session:
 *   #URL_ACTION{commandes_paniers,'',#SELF}
 * Pour créer une commande d'après un panier précis :
 *   #URL_ACTION{commandes_paniers,#ID_PANIER,#SELF}
 * Sans redirection explicite, la fonction redirige vers la page de la commande
 *
 * @param string $arg
 *     id_panier pour creer la commande et le detruire
 *     id_panier-1 pour creer la commande et le conserver
 * @return void
 * */
function action_commandes_paniers_dist($arg = null) {

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$arg = explode("-", $arg);
	$id_panier = 0;
	if (count($arg))
		$id_panier = intval(array_shift($arg));
	$keep = false;
	if (count($arg))
		$keep = intval(array_shift($arg));


	// Sans paramètre, récupérer $id_panier dans la session du visiteur actuel
	if (!$id_panier) {
		include_spip('inc/paniers');
		$id_panier = paniers_id_panier_encours();
	}

	// Si aucun panier ne pas agir
	if (!$id_panier)
		return;

	$id_auteur = sql_getfetsel("id_auteur", "spip_paniers", "id_panier=" . intval($id_panier));


	include_spip('inc/commandes');
	include_spip('inc/config');
	// si une commande recente est encours (statut et dans la session de l'utilisateur), on la reutilise
	// plutot que de recreer N commandes pour un meme panier
	// (cas de l'utilisateur qui revient en arriere puis retourne a la commande)
	include_spip('inc/session');
	$id_commande = sql_getfetsel("id_commande", "spip_commandes", $w = "statut=" . sql_quote('encours') . " AND date>" . sql_quote(date('Y-m-d H:i:s', strtotime('-' . lire_config('paniers/limite_ephemere', 24) . ' hour'))) . " AND source=" . sql_quote("panier#$id_panier") . " AND id_commande=" . session_get('id_commande'));

	// sinon on cree une commande "en cours"
	if (!$id_commande) {
		$id_commande = creer_commande_encours();
	}

	// et la remplir les details de la commande d'après le panier en session
	if ($id_commande) {
		panier2commande_remplir_commande($id_commande, $id_panier, false);
	}

	// Supprimer le panier ?
	if (!$keep) {
		$supprimer_panier = charger_fonction('supprimer_panier_encours', 'action/');
		$supprimer_panier();
	}

	// Sans redirection donnée, proposer une redirection par defaut vers la page de la commande créée
	if (is_null(_request('redirect'))) {
		$GLOBALS['redirect'] = generer_url_public('commande', 'id_commande=' . $id_commande, true);
	}
}

/**
 * Remplir une commande d'apres un panier
 *
 * @param  int $id_commande
 * @param  int $id_panier
 * @param  bool $append
 *   true pour ajouter brutalement le panier a la commande, false pour verifier que commande==panier en ajoutant/supprimant uniquement les details necessaires
 */
function panier2commande_remplir_commande($id_commande, $id_panier, $append = true) {

	include_spip('action/editer_objet');
	include_spip('inc/filtres');
	include_spip('inc/paniers');

	// noter le panier source dans le champ source de la commande
	objet_modifier('commande', $id_commande, array('source' => "panier#$id_panier"));

	// recopier le contenu du panier dans la commande
	// On récupère le contenu du panier
	$panier = sql_allfetsel(
			'*', 'spip_paniers_liens', 'id_panier = ' . intval($id_panier)
	);

	// Pour chaque élément du panier, on va remplir la commande
	// (ou verifier que la ligne est deja dans la commande)
	if ($panier and is_array($panier)) {
		$details = array();
		include_spip('spip_bonux_fonctions');
		$fonction_prix    = charger_fonction('prix_option', 'inc/');
		$fonction_prix_ht = charger_fonction('ht', 'inc/prix_option');
		foreach ($panier as $emplette) {
			$prix_ht = $fonction_prix_ht($emplette['objet'], $emplette['id_objet'], $emplette['options'], 6);
			$prix    = $fonction_prix($emplette['objet'], $emplette['id_objet'], $emplette['options'], 6);

			// On déclenche un pipeline pour pouvoir éditer le prix avant la création de la commande
			// Utile par exemple pour appliquer une réduction automatique lorsque la commande est crée
			$prix_pipeline = pipeline(
				'panier2commande_prix',
				array(
					'args' => $emplette,
					'data' => array(
						'prix' => $prix,
						'prix_ht' => $prix_ht
					)
				)
			);

			// On ne récupère que le prix_ht dans le pipeline
			$prix_ht = $prix_pipeline['prix_ht'];
			$prix = $prix_pipeline['prix'];

			if ($prix_ht > 0)
				$taxe = round(($prix - $prix_ht) / $prix_ht, 6);
			else
				$taxe = 0;

			$libelle_option = '';
			if ($emplette['options']) {
				$options = explode('|', trim($emplette['options'],'|'));
				foreach ($options as $option) {
					$titre_option = sql_fetsel(
						'titre, titre_groupe', 
						'spip_options left join spip_optionsgroupes using(id_optionsgroupe)', 
						'id_option = ' . intval($option)
					);
					if ($titre_option) {
						$libelle_option .= "\n\r".'<br><small>'.supprimer_numero($titre_option['titre_groupe']).' : '.$titre_option['titre'].'</small>';
					}
				}

			}
			$set   = array(
				'id_commande'      => $id_commande,
				'objet'            => $emplette['objet'],
				'id_objet'         => $emplette['id_objet'],
				'options'          => $emplette['options'],
				'descriptif'       => generer_info_entite($emplette['id_objet'], $emplette['objet'], 'titre', '*') . $libelle_option,
				'quantite'         => $emplette['quantite'],
				'reduction'        => $emplette['reduction'],
				'prix_unitaire_ht' => $prix_ht,
				'taxe'             => $taxe,
				'statut'           => 'attente',
			);
			$where = array();
			foreach ($set as $k => $w) {
				if (in_array($k, array('id_commande', 'objet', 'id_objet', 'options'))) {
					$where[] = "$k=" . sql_quote($w);
				}
			}
			// est-ce que cette ligne est deja la ?
			if ($append OR ! $id_commandes_detail = sql_getfetsel("id_commandes_detail", "spip_commandes_details", $where)) {
				// sinon création et renseignement du détail de la commande
				$id_commandes_detail = objet_inserer('commandes_detail');
			}
			if ($id_commandes_detail) {
				objet_modifier('commandes_detail', $id_commandes_detail, $set);
				$details[] = $id_commandes_detail;
			}
		}
		if (!$append) {
			// supprimer les details qui n'ont rien a voir avec ce panier
			sql_delete("spip_commandes_details", "id_commande=" . intval($id_commande) . " AND " . sql_in('id_commandes_detail', $details, "NOT"));
		}

		// Envoyer aux plugins après édition pour verification eventuelle du contenu de la commande
		pipeline(
			'post_edition',
			array(
				'args' => array(
					'table' => 'spip_commandes',
					'id_objet' => $id_commande,
					'action' => 'remplir_commande',
				),
				'data' => array()
			)
		);

	}
}
