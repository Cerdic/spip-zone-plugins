<?php

  //include_spip('inc/callbacks');

include_spip('base/abstract_sql');
include_spip('public/interfaces');
include_spip('base/serial');
include_spip('public/references');
include_spip('inc/sax');

function exec_editer() {
	if (!isset($_POST['widgets']) OR !is_array($_POST['widgets'])) {
		die("rien a faire la !");
	}

	$actions = urldecode($_POST['actions']);
	$secu = $_POST['actions_secu'];
	if(!verif_secu($actions, $secu)) {
		die("Actions truandées !");
	}

	$valeurs= array();

	foreach ($_POST['widgets'] as $widget) {
		$content = $_POST['content_'.$widget];
		$md5 = $_POST['md5_'.$widget];

		// Si les donnees POSTees ne correspondent pas a leur md5,
		// il faut les traiter
		if (md5($content) <> $md5) {
			$callbacks = $_POST['callbacks_'.$widget];
			$r= doCallBacks($callbacks, $content);
			if($r!=null) {
				// faut il ignorer ou tout laisser tomber ?
				echo "<br/>$widget ignore car callback retourne : $r\n";
			} else {
				$valeurs[$widget]= $content;
			}
		}
	}

	doActions($actions, $valeurs);
	//rediriger($_POST['retour']);
}

function verif_secu($v, $secu) {
	error_log("verif_secu($v, $secu) / ".$GLOBALS['meta']['alea_ephemere'].' => '.md5($GLOBALS['meta']['alea_ephemere'].' - '.$v));
	return ( $secu == md5($GLOBALS['meta']['alea_ephemere'].' - '.$v)
		  OR $secu == md5($GLOBALS['meta']['alea_ephemere_ancien'].' - '.$v) );
}

// appelle les callbacks d'une valeur pour valider qu'on peut la prendre
// en compte
function doCallBacks($callbacks, &$content) {
	foreach(explode(';', $callbacks) as $callback) {
		if($callback=='') continue;
		if(($i= strpos($callback, ':'))!==false) {
			error_log("include ".'inc/'.substr($callback, 0, $i));
			include_spip('inc/'.substr($callback, 0, $i));
			$callback= substr($callback, $i+1);
		}
		$callback= 'callback_'.$callback;
		if(!function_exists($callback)) {
			return "callback $callback introuvable";
		}
		if(($res= $callback($content))!==null) {
			echo "<br/>callback $callback repond $res";
			return $res;
		}
	}
}

function doActions($actions, $valeurs) {
	echo "<xmp>doActions $actions\nsur ".var_export($valeurs, 1).'</xmp>';
	include_spip('inc/actionParser');
	$parser = new actionParser($valeurs);
	$parser->parse($actions);
	echo $parser->getSql();
}

?>
