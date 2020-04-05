<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// À chaque hit en partie publique, on va chercher le panier du visiteur actuel si il en a un
// on ne fait rien sur les hits visiteurs anonymes, bots, cron, etc...
if (isset($_COOKIE[$GLOBALS['cookie_prefix'].'_panier'])
  OR (isset($GLOBALS['visiteur_session']['id_panier']) AND $GLOBALS['visiteur_session']['id_panier'])
  OR (isset($GLOBALS['visiteur_session']['id_auteur']) AND $GLOBALS['visiteur_session']['id_auteur'])){

	// verifier/mettre a jour l'existence d'un panier en cours
	include_spip('inc/paniers');
	$id_panier = paniers_id_panier_encours();

}

// si on a une transformation panier=>commande en attente et qu'on est connecte,
// creer la commande
if (  isset($_COOKIE['spip_pwl']) AND $_COOKIE['spip_pwl']
	AND isset($GLOBALS['visiteur_session']['id_auteur']) AND $GLOBALS['visiteur_session']['id_auteur']){

	include_spip('inc/filtres');
	$contexte = decoder_contexte_ajax($_COOKIE['spip_pwl'],'spip_pwl');
	$arg = reset($contexte);
	$commandes_paniers = charger_fonction("commandes_paniers","action");
	$commandes_paniers($arg);
	include_spip('inc/cookie');
	spip_setcookie("spip_pwl",$_COOKIE['spip_pwl'] = '',0);
}


/**
 * Calculer rapidement le nombre de produits dans un panier
 * @param int $id_panier
 * @param bool $compte_quantite
 *   si true on compte le nombre de produits en additionnant les quantites, sinon on compte le nombre d'items
 * @return int|number
 */
function paniers_nombre_produits($id_panier, $compte_quantite = true){
	if (!function_exists('sql_getfetsel')) {
		include_spip('base/abstract_sql');
	}
	if ($compte_quantite) {
		$nombre = intval(sql_getfetsel("SUM(quantite)","spip_paniers_liens","id_panier=".intval($id_panier)));
	}
	else {
		$nombre = intval(sql_getfetsel("COUNT(*)","spip_paniers_liens","id_panier=".intval($id_panier)));
	}
	return $nombre;
}

// Eviter une collistion de fonction si le plugin deprecie panier2commande est encore actif
if (!defined('_DIR_PLUGIN_PANIER2COMMANDE')){
	/**
	 * Créer la commande si on est connecté,
	 * sinon noter la demande de création dans un cookie,
	 * et celle-ci sera créée dès qu'on sera connecté.
	 *
	 * Dérogation : renvoie vers la page "qui" si elle existe
	 *
	 * @param null $arg
	 */
	function action_commandes_paniers_if_loged_dist($arg = null) {

		// Si $arg n'est pas donne directement, le recuperer via _POST ou _GET
		if (is_null($arg)){
			$securiser_action = charger_fonction('securiser_action', 'inc');
			$arg = $securiser_action();
		}

		// si on est identifie, on peut passer a la commande directement
		if (isset($GLOBALS['visiteur_session']['id_auteur']) AND $GLOBALS['visiteur_session']['id_auteur']){
			$commandes_paniers = charger_fonction("commandes_paniers", "action");
			$commandes_paniers($arg);
		}
		// sinon on note le arg pour creer la commande des qu'on est identifie
		else {
			include_spip('inc/cookie');
			include_spip('inc/filtres');
			spip_setcookie("spip_pwl", encoder_contexte_ajax(array($arg), 'spip_pwl'));

			// Dérogation : s'il existe une page d'identification "qui",
			// on redirige vers celle-ci en passant le redirect d'origine en paramètre
			if (
				find_in_path('qui.html')
				or (
					test_plugin_actif('zcore')
					and include_spip('public/styliser_par_z')
					and $contenu = reset(z_blocs(false))
					and find_in_path('qui.html', "$contenu/")
				)
			) {
				$GLOBALS['redirect'] = parametre_url(generer_url_public('qui'), 'url', _request('redirect'));
			}
		}
	}
}
