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
if (!defined("_ECRIRE_INC_VERSION")) return;

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
**/
function action_commandes_paniers_dist($arg=null){

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$arg = explode("-",$arg);
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

	// création d'une commande "en cours"
	// Ses détails sont ensuite remplis d'après le panier en session
	// via la pipeline post_insertion
	// TODO : c'est ici qu'il faudrait remplir cette commande avec le panier
	// toute commande n'est pas bonne a remplir avec le panier automatiquement
	// cas du bouton "Achat immediat de ce produit" qui va direct au paiement
	// ne doit pas remplir la commande avec le panier en cours
	include_spip('inc/commandes');

	if ($id_commande = creer_commande_encours()){
		panier2commande_remplir_commande($id_commande,$id_panier);
	}


	// Supprimer le panier ?
	if (!$keep){
		$supprimer_panier = charger_fonction('supprimer_panier_encours', 'action/');
		$supprimer_panier();
	}

	// Sans redirection donnée, proposer une redirection par defaut vers la page de la commande créée
	if (is_null(_request('redirect'))) {
		$GLOBALS['redirect'] = generer_url_public('commande','id_commande='.$id_commande,true);
	}
}



/**
 * Remplir une commande d'apres un panier
 *
 * @param  int $id_commande
 * @param  int $id_panier
 */
function panier2commande_remplir_commande($id_commande,$id_panier){

	include_spip('action/editer_objet');
	include_spip('inc/filtres');
	include_spip('inc/paniers');

	// noter le panier source dans le champ source de la commande
	objet_modifier('commande',$id_commande,array('source'=>"panier#$id_panier"));

	// recopier le contenu du panier dans la commande
	// On récupère le contenu du panier
	$panier = sql_allfetsel(
		'*',
		'spip_paniers_liens',
		'id_panier = '.intval($id_panier)
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
			// création du détail de la commande
			if ($id_commandes_detail = objet_inserer('commandes_detail')) {
				$set = array(
					'id_commande' => $id_commande,
					'objet' => $emplette['objet'],
					'id_objet' => $emplette['id_objet'],
					'descriptif' => generer_info_entite($emplette['id_objet'], $emplette['objet'], 'titre', '*'),
					'quantite' => $emplette['quantite'],
					'prix_unitaire_ht' => $prix_ht,
					'taxe' => $taxe,
					'statut' => 'attente'
				);
				objet_modifier('commandes_detail', $id_commandes_detail, $set);
			}
		}
	}

}