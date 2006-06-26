<?php

//include_spip('inc/callbacks');

function exec_editer() {
	if (!isset($_POST['widgets']) OR !is_array($_POST['widgets'])) {
		die("rien a faire la !");
	}

	// recuperer les morceaux et verifier qu'ils sont nets
	$retour = urldecode($_POST['url']);
	$actions = urldecode($_POST['actions']);
	$callbacks = urldecode($_POST['callbacks']);
	$secu = $_POST['actions_secu'];
	if(!verif_secu($actions, $callbacks, $secu)) {
		die("Actions truandees !");
	}

	// desosser les callbacks
	if($callbacks) {
		preg_match_all('/&(\w*)=([^&]*)/', $callbacks, $re, PREG_PATTERN_ORDER);
		$callbacks= array_combine($re[1], $re[2]);
		error_log("callbacks => ".var_export($callbacks, 1));
	} else {
		$callbacks= array();
	}

	// et partir d'une liste de valeurs vide
	$valeurs= array();

	foreach ($_POST['widgets'] as $widget) {
		$content = $_POST['content_'.$widget];
		$md5 = $_POST['md5_'.$widget];

		// Si les donnees POSTees ne correspondent pas a leur md5,
		// il faut les traiter
		if (md5($content) <> $md5) {
			if(array_key_exists($widget, $callbacks)) {
				$r= doCallBacks($callbacks[$widget], $content);
			} else {
				$r= null;
			}
			if($r!=null) {
				// faut il ignorer ou tout laisser tomber ?
				echo "<br/>$widget ignore car callback retourne : $r\n";
			} else {
				$valeurs[$widget]= $content;
			}
		}
	}

	$res= doActions($actions, $valeurs);
	if(!$res) {
		echo mysql_error();
	}
	if($retour)
		rediriger($retour);
}

// A l'alle, on a calcule un md5 sur les elements sensibles, concatene a un peu
// de sel (alea_ephemere). si ce qu'on recoit a le meme md5, c'est que
// l'internaute n'a pas truande les valeurs
// Petit hack en passant, on calcule sur action.alea.callback et pas
// action.callback.alea pour pas risquer qu'un plaisantin mette action.callback
// dans action et zappe donc les callback
function verif_secu($v1, $v2, $secu) {
	$ligne= $v1.' - '.$GLOBALS['meta']['alea_ephemere'].' - '.$v2;
	error_log("verif_secu($v1, $v2, $secu) / ".$GLOBALS['meta']['alea_ephemere'].' => '.md5($ligne));
	return ( $secu == md5($v1.' - '.$GLOBALS['meta']['alea_ephemere'].' - '.$v2)
		  OR $secu == md5($v1.' - '.$GLOBALS['meta']['alea_ephemere_ancien'].' - '.$v2) );
}

// appelle les callbacks d'une valeur pour valider qu'on peut la prendre
// en compte
function doCallBacks($callbacks, &$content) {
	//error_log("CALLBACKS $callbacks sur $content");
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
	//echo "<xmp>doActions $actions\nsur ".var_export($valeurs, 1).'</xmp>';
	include_spip('inc/actionParser');
	$parser = new actionParser($valeurs);
	$parser->parse($actions);
	$code= $parser->getCode();
	//echo "<xmp>$code</xmp>\n";
	return eval($code);
}

?>
