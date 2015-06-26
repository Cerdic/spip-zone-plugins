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
	$id_commande = creer_commande_encours();

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
