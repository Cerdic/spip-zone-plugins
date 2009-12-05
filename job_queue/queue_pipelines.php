<?php
/*
 * Plugin Job Queue
 * (c) 2009 Cedric&Fil
 * Distribue sous licence GPL
 *
 */

function queue_affiche_milieu($flux){
	$args = $flux['args'];
	$res = "";
	foreach($args as $key=>$arg){
		if (preg_match(",^id_,",$key)){
			$objet = preg_replace(',^id_,', '', $key);
			$res .= recuperer_fond('modeles/object_jobs_list',array('id_objet'=>$arg,'objet'=>$objet),array('ajax'=>true));
		}
	}
	if ($res)
		$flux['data'] = $res . $flux['data'];

	return $flux;
}

// gerer le lancement du cron
function queue_affichage_final(&$texte){

	// rien a faire si le prochain job est encore dans le futur
	if ($GLOBALS['meta']['queue_next_job_time']>time()){
		if (!defined('_DIRECT_CRON_INHIBE'))
			define('_DIRECT_CRON_INHIBE',true); // ne plus faire d'appel direct au cron en fin de hit
		return $texte;
	}

	// il y a des taches en attentes

	// si c'est un bot
	// inutile de faire un appel par image background, on force un appel direct en fin de hit
	if ((defined('_IS_BOT') AND _IS_BOT) OR defined('_DIRECT_CRON_FORCE')){
		define('_DIRECT_CRON_FORCE',true);
		return $texte;
	}

	// ici lancer le cron par un CURL asynchrone si CURL est présent
	// TBD

	// si on est pas dans une page html, on ne sait rien faire de mieux
	if (!$GLOBALS['html'])
		return $texte;

	// en derniere solution, on insere une image background dans la page
	$code = '<!-- SPIP-CRON --><div style="background-image: url(\'' .
		generer_url_action('cron') .
		'\');"></div>';

	if ($p=strpos($texte,'</body>')!==FALSE)
		$texte = substr($texte,0,$p).$code.substr($texte,$p);
	else
		$texte .= $p;

	return $texte;
}
?>