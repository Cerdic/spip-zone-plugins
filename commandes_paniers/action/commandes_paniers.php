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
 *     identifiant du panier
 * @return void
**/
function action_commandes_paniers_dist($arg=null){

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// Sans paramètre, récupérer $id_panier dans la session du visiteur actuel
	if (is_null($id_panier=$arg)) {
		include_spip('inc/session');
		$id_panier = session_get('id_panier');
	}

	// Si aucun panier ne pas agir
	if (is_null($id_panier)) 
		return;        

	// création d'une commande "en cours"
	// Ses détails sont ensuite remplis d'après le panier en session
	// via la pipeline post_insertion
	include_spip('inc/commandes');
	$id_commande = creer_commande_encours();

	// Plus besoin du panier
	$supprimer_panier = charger_fonction('supprimer_panier_encours', 'action/');
	$supprimer_panier();

	// Sans redirection donnée, on redirige vers la page de la commande créée
	if (is_null(_request('redirect'))) {   
		$redirect = generer_url_public('commande','id_commande='.$id_commande,true);
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}
?>
