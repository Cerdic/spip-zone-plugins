<?php
/*
 * Plugin Cache Cool
 * (c) 2009 Cedric
 * Distribue sous licence GPL
 *
 */

/**
 * Fonction chargee de produire le cache pour un contexte et un fond donne
 * et de le memoriser si besoin
 * 
 * S'insere a la place de la fonction du core public_produire_page_dist pour
 * decider si le cache existant peut etre servi froid, et lancer dans la queue
 * une tache de mise a jour du cache en cron
 *
 * Dans ce cas, on reentre ici avec $use_cache.
 * On verifie que le contexte produit un $use_cache et un $chemin_cache credibles
 * (si on est dans l'espace prive au moment du calcul differe, aucun cache ne sera demande)
 * Il faudrait idealement verifier qu'on retrouve le meme $chemin_cache
 * mais cela necessiterait d'avoir le $page et le $contexte qui utilises pour cela
 * dans le cas de la page principal dans public/assembler, mais qui ne sont pas fournis ici
 * 
 * Si le contexte est semblable, on lance le calcul et la mise a jour du cache, 
 * sinon on reprogramme avec les memes arguments
 * 
 * @param string $fond
 * @param array $contexte
 * @param int $use_cache
 * @param string $chemin_cache
 * @param array $contexte_cache
 * @param array $page
 * @param int $lastinclude
 * @param string $connect
 * @return array 
 */
function public_produire_page($fond, $contexte, $use_cache, $chemin_cache, $contexte_cache, $page, &$lastinclude, $connect=''){

	// calcul differe du cache ?
	// prend la main si c'est un calcul normal avec mise en cache
	// et qu'un cache existe deja qui peut etre servi
	if ($use_cache==1
		AND $chemin_cache
		AND is_array($page)
		AND count($page)
		AND !$GLOBALS['visiteur_session']['id_auteur']
		) {
		// on differe la maj du cache et on affiche le contenu du cache ce coup ci encore
		$where = is_null($contexte_cache)?"principal":"inclure_page";
		job_queue_add('public_produire_page',$c="Calcul du cache $fond [$where]",array($fond, $contexte, 2, $chemin_cache, $contexte_cache, NULL, $lastinclude, $connect),"",TRUE);
		gunzip_page(&$page); // decomprimer la page si besoin
		#spip_log($c,'cachedelai');
		return $page;
	}
	// si c'est un cacul differe, verifier qu'on est dans le bon contexte
	if ($use_cache==2){
		$cacher = charger_fonction('cacher','public');
		$cacher($contexte_cache, $use_cache, $chemin2, $page, $lastmodified);
		if (intval($use_cache)!==1 OR !$chemin2){
			// on n'est pas dans le bon contexte, il faut se reprogrammer !
			$where = is_null($contexte_cache)?"principal":"inclure_page";
			$args = func_get_args();
			job_queue_add('public_produire_page',$c="[Re] Calcul du cache $fond [$where]",$args,"",TRUE);
			#spip_log($c,'cachedelai');
			return;
		}
	}

	include_spip('public/assembler');
	return public_produire_page_dist($fond, $contexte, $use_cache, $chemin_cache, $contexte_cache, $page, $lastinclude, $connect);
}

?>