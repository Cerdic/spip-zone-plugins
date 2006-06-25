<?php

function action_editer() {
	if (!isset($_REQUEST['widgets']) OR !is_array($_REQUEST['widgets'])) {
		die("rien a faire la !");
	}

	// recuperer les autres morceaux et verifier qu'ils sont nets
	$actions = urldecode($_REQUEST['actions']);
	$callbacks = urldecode($_REQUEST['callbacks']);
	$secu = $_REQUEST['actions_secu'];
	if(!verif_secu($actions, $callbacks, $secu)) {
		die("Actions truandees !");
	}
	$xml= doSpipInclude($actions);
	echo "XML : $xml\n";
	include_spip('inc/actionParser');
	$parser = new actionParser();

	$res= $parser->parse($xml);
	//echo "RES : '$res'\n";
	echo "ACTIONS : ".var_export($parser->actions, 1)."\n";
	$cmds= $parser->evaluate($parser->actions);
	//echo "PHP : ".var_export($cmds, 1)."\n";
	foreach($cmds as $cmd) {
		echo "=> $cmd\n";
		if(eval("return (\$r= ($cmd))===false;")) {
			die("erreur : $r");
		}
	}
	if($parser->retour) {
		$GLOBALS['redirect']= eval("return ".$parser->retour.";");
	} elseif($retour = urldecode($_REQUEST['retour'])) {
		$GLOBALS['redirect']= str_replace('&amp;', '&', $retour);
	}
	echo "RETOUR : ".$parser->retour."/".$GLOBALS['redirect']."\n";
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

@define ('_INC_PUBLIC', 1);

function doSpipInclude($fond) {
	$GLOBALS['contexte_inclus']= $GLOBALS['contexte'];
	$GLOBALS['contexte_inclus']['fond']= $fond;

	$f = charger_fonction('assembler', 'public');
	$page = inclure_page($fond, $contexte_inclus);
	return $page['texte'];  // A REVOIR : faire un eval ??
}


function balise_E_VALEUR($p) {
	if (!$p->param || $p->param[0][0] || !$p->param[0][1]) {
		erreur_squelette('VALEUR necessite 1 parametre');
		return $p;
	}
	$nom=  calculer_liste($p->param[0][1],
						  $p->descr, $p->boucles, $p->id_boucle);
	$p->code= "(\$_REQUEST['content_'.$nom])";
	return $p;
}

function balise_E_MODIFIE($p) {
	if (!$p->param || $p->param[0][0] || !$p->param[0][1]) {
		erreur_squelette('MODIFIE necessite 1 parametre');
		return $p;
	}
	$nom=  calculer_liste($p->param[0][1],
						  $p->descr, $p->boucles, $p->id_boucle);

	$p->code= "(array_key_exists('md5_'.$nom, \$_REQUEST) && (!(\$md5 = \$_REQUEST['md5_'.$nom]) || \$md5!=md5(\$_REQUEST['content_'.$nom])))";
	$p->interdire_script= false;
	return $p;
}

?>
