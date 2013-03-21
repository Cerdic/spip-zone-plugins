<?php

####### insert_head n'est pas bon car c'est le meme head pour toutes les pages
# => affichage_final pour inserer le head
# => affichage_entetes_final pour envoyer des messages à varnish

if (!defined("_ECRIRE_INC_VERSION")) return;

// Vider le cache implique une demande de purge varnish
if (isset($_GET['exec']) AND $_GET['exec'] == "admin_vider")
	@header('X-Varnish-Purge: *');


// Donner a varnish d'eventuelles instructions de mise en cache
function varnish_affichage_entetes_final($entetes) {
	# signaler a varnish s'il peut conserver la page, et pour quelle durée
	# sachant qu'on saura l'invalider si besoin

	# Certaines pages contenant du PHP peuvent vouloir rester dynamiques
	# * si elles ont une #SESSION ça ne pose pas de problème (varnish respectant
	#   le cookie du visiteur)
	# * par defaut, on autorise varnish a les cacher pour une duree = #CACHE
	# * si on veut bypasser ce default, utiliser
	#    #HTTP_HEADER{X-Varnish-TTL: xxx}
	if (isset($entetes['X-Spip-Cache']) AND !isset($entetes['X-Varnish-Ttl']))
		$entetes['X-Varnish-Ttl'] = $entetes['X-Spip-Cache'];


	// Apparition d'un nouvel article post-date ?
	// on applique ici la logique de public/cacher:cache_valide()
	if ($GLOBALS['meta']['post_dates'] == 'non'
	AND isset($GLOBALS['meta']['date_prochain_postdate'])
	AND time() > $GLOBALS['meta']['date_prochain_postdate']) {
		spip_log('Un article post-date invalide le cache');
		include_spip('inc/rubriques');
		ecrire_meta('derniere_modif', time());
		calculer_prochain_postdate();
		$entetes['X-Varnish-Purge'] = '*';
	}

	return $entetes;
}

/* verifier le besoin d'installer statjs et le signaler */
function varnish_alertes_auteur($flux) {
	if (autoriser('webmestre', $flux['args']['id_auteur'])
	AND $GLOBALS['meta']['activer_statistiques'] == 'oui') {
		$plugins = unserialize($GLOBALS['meta']['plugin']);

		if (!isset($plugins['STATSJS'])) {
			$flux['data'][] = _T('avis_attention'). ' '
				. _L("Pour utiliser les statistiques de SPIP avec Varnish, il est recommandé d'installer le plugin <a href='http://www.spip-contrib.net/3753'>StatsJS</a>.");
		}
	}
#var_dump($flux);

	return $flux;
}

?>
