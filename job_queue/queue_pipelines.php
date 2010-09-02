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

function queue_affichage_cron(){
	$texte = "";

	// rien a faire si le prochain job est encore dans le futur
	if (queue_sleep_time_to_next_job()){
		if (!defined('_DIRECT_CRON_INHIBE'))
			define('_DIRECT_CRON_INHIBE',true); // ne plus faire d'appel direct au cron en fin de hit
		return $texte;
	}
	// il y a des taches en attentes

	// Si fsockopen est possible, on lance le cron via un socket
	// en asynchrone
	if(function_exists('fsockopen')){
		$url = generer_url_action('cron','',false,true);
		$parts=parse_url($url);

		$fp = @fsockopen($parts['host'],
	        isset($parts['port'])?$parts['port']:80,
	        $errno, $errstr, 30);

		if ($fp) {
			$query = $parts['path'].($parts['query']?"?".$parts['query']:"");
			$out = "GET ".$query." HTTP/1.1\r\n";
			$out.= "Host: ".$parts['host']."\r\n";
			$out.= "Connection: Close\r\n\r\n";
			fwrite($fp, $out);
			fclose($fp);
			return $texte;
		}
	}

	// ici lancer le cron par un CURL asynchrone si CURL est présent
	// TBD

	// si c'est un bot
	// inutile de faire un appel par image background, on force un appel direct en fin de hit
	if ((defined('_IS_BOT') AND _IS_BOT) OR defined('_DIRECT_CRON_FORCE')){
		define('_DIRECT_CRON_FORCE',true);
		return $texte;
	}

	// en derniere solution, on insere une image background dans la page
	$texte = '<!-- SPIP-CRON --><div style="background-image: url(\'' .
		generer_url_action('cron') .
		'\');"></div>';

	return $texte;
}

// gerer le lancement du cron
function queue_affichage_final(&$texte){

	$code = queue_affichage_cron();

	// si rien a afficher
	// ou si on est pas dans une page html, on ne sait rien faire de mieux
	if (!$code OR !$GLOBALS['html'])
		return $texte;

	if (($p=strpos($texte,'</body>'))!==FALSE)
		$texte = substr($texte,0,$p).$code.substr($texte,$p);
	else
		$texte .= $code;

	return $texte;
}

?>
