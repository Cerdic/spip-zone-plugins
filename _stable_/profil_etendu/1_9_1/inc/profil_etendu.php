<?php

function etendu_champs($type, $ensemble='') {

	$champs=array();
	// quels sont les extras de ce type d'objet
	if (!$champs = $GLOBALS['champs_etendus'][$type])
		$champs = array();
	
	// quels sont les extras proposes...
	// ... si l'ensemble est connu
	if ($ensemble && isset($GLOBALS['champs_etendus_proposes'][$type][$ensemble]))
		$champs_proposes = explode('|', $GLOBALS['champs_etendus_proposes'][$type][$ensemble]);
	// ... sinon, les champs proposes par defaut
	else if (isset($GLOBALS['champs_etendus_proposes'][$type]['tous'])) {
		$champs_proposes = explode('|', $GLOBALS['champs_etendus_proposes'][$type]['tous']);
	}

	// sinon tous les champs extra du type
	else {
		$champs_proposes =  array();
		reset($champs);
		while (list($ch, ) = each($champs)) $champs_proposes[] = $ch;
	}

	// bug explode
	if($champs_proposes == explode('|', '')) $champs_proposes = array();

	// maintenant, on affiche les formulaires pour les champs renseignes dans $extra
	// et pour les champs proposes
	reset($champs_proposes);
	while (list(, $champ) = each($champs_proposes)) {
		$desc = $champs[$champ];
		list($form, $filtre, $prettyname, $choix, $valeurs) = explode("|", $desc);

		switch($form) {

			case "multiple":
				$choix = explode(",",$choix);
				if (is_array($choix)) {
					for ($i=0; $i < count($choix); $i++)
						$champs[$champ."_multi_".$i]=$form;
				}
				break;

			default:
				$champs[$champ]=$form;
				break;
		}
	}
//	spip_log(serialize($champs));
	return $champs;

}

?>