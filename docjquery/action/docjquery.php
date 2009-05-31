<?php

function action_docjquery() {
	global $tablejq, $langRef;

	$id= _request('id');
	$text= _request('value');

	if(!$GLOBALS['auteur_session']['id_auteur']) {
		echo "PB : acces non autorise";
		return;
	}

	// recup detail id
	if(!preg_match('/(desc|etat):(\d+)(?::(long|short|ex|arg)(?::(\d+))?)?/', $id, $re)) {
		echo "PB : format id";
		return;
	}

	$quoi= $re[1];
	$id= $re[2];
	$detail= $re[3];
	$complement= $re[4];

	include_spip('base/abstract_sql');

	if($quoi=='etat') {
		$etats=array('new', 'ok', 'trv', 'sup', 'mod');
		if(!in_array($text, $etats)) {
			echo "PB : etat '$text' invalide";
			return;
		}
		$r= spip_query("UPDATE $tablejq SET etat='$text' WHERE id='$id'");
		if(!$r) {
			echo "PB : svg etat ...";
			return;
		}
		include_spip('docjq_fonctions');
		echo barre_etat($text, $id);
		return;
	}
 
	// recup xml
	$fetsel= spip_abstract_fetsel(
		array('xml'),
		array($tablejq),
		array(array('=', 'id', $id))
	);
	if(!$fetsel) {
		echo "PB : recup xml ...";
		return;
	}
	$xml= simplexml_load_string($fetsel['xml']);

	// modif dedans
	switch($detail) {
	case 'short':
		$xml['short']= $text;
		break;
	case 'long':
		$xml->desc= $text;
		break;
	case 'ex':
		$xml->examples[$complement-1]->desc= $text;
		break;
	case 'arg':
		$xml->params[$complement-1]->desc= $text;
		break;
	}
	$xml= $xml->asXML();
	// dégager la ligne d'entete
	$xml= substr(strstr($xml, "\n"), 1);
	$xml= mysql_real_escape_string($xml);

	// svg
	$r= spip_query("UPDATE $tablejq SET xml='$xml' WHERE id='$id'");
	if(!$r) {
		echo "PB : svg xml ...";
		return;
	}

	include_spip('inc/invalideur');
	// tout invalider
	suivre_invalideur(1);
	// violent, mais efficace

	echo $text;
}
?>
