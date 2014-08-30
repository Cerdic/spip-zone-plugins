<?php
/**
 * Pipelines utilisées par le plugin Commandes de paniers
 *
 * @plugin     Commandes de Paniers
 * @copyright  2014
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Panier2commande\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Actions effectuées après l'insertion d'un objet dans la base de donnée
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function panier2commande_post_insertion($flux){
	// Après insertion d'une commande "encours" et s'il y a un panier en cours
	if (
		$flux['args']['table'] == 'spip_commandes'
		and ($id_commande = intval($flux['args']['id_objet'])) > 0
		and $flux['data']['statut'] == 'encours'
		and include_spip('inc/paniers')
		and $id_panier = paniers_id_panier_encours()
		and include_spip('inc/filtres')
	){
		// On récupère le contenu du panier
		$panier = sql_allfetsel(
			'*',
			'spip_paniers_liens',
			'id_panier = '.$id_panier
		);

		// Pour chaque élément du panier, on va remplir la commande
		if ($panier and is_array($panier)){
			include_spip('spip_bonux_fonctions');
			$fonction_prix = charger_fonction('prix', 'inc/');
			$fonction_prix_ht = charger_fonction('ht', 'inc/prix');
			foreach($panier as $emplette){
				$prix_ht = $fonction_prix_ht($emplette['objet'], $emplette['id_objet'],3);
				$prix = $fonction_prix($emplette['objet'], $emplette['id_objet'],3);
				if($prix_ht > 0)
					$taxe = round(($prix - $prix_ht) / $prix_ht, 3);
				else
					$taxe = 0;
				sql_insertq(
					'spip_commandes_details',
					array(
						'id_commande' => $id_commande,
						'objet' => $emplette['objet'],
						'id_objet' => $emplette['id_objet'],
						'descriptif' => generer_info_entite($emplette['id_objet'], $emplette['objet'], 'titre', '*'),
						'quantite' => $emplette['quantite'],
						'prix_unitaire_ht' => $prix_ht,
						'taxe' => $taxe
					)
				);
			}
		}
	}

	return $flux;
}

?>
