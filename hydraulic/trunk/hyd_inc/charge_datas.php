<?php

/**
 * Charge les données d'un formulaire avec choix des variables fixées, qui varient et à calculer
 * @param $bLibelles Remplit la clé tlib avec les libellés traduits des variables
 * @return un tableau avec les clés suivantes:
 *      - Couples clés/valeur des champs du formulaire
 *      - iPrec : nombre de décimales pour la précision des calculs
 *      - tLib: tableau avec couples clés/valeurs des libellés traduits des champs du formulaire
 *      - sLang : la langue en cours
 *      - CacheFileName : Le nom du fichier de cache
 *      - min, max, pas : resp. le min, le max et le pas de variation de la variable qui varie
 *      - i : pointeur vers la variable qui varie
 *      - ValCal : Nom de la variable à calculer
 *      - ValVar : Nom de la variable qui varie
 * @author David Dorchies
 * @date Juillet 2012
 */
function charge_datas($bLibelles = true) {
	global $spip_lang;

	$tChOblig = champs_obligatoires();
	$tChCalc = champs_obligatoires(true);
	spip_log($tChOblig,'hydraulic',_LOG_DEBUG);
	$choix_radio = array();
	$tLib = array();
	$datas=array();
	$datas['iPrec']=(int)-log10(_request('rPrec'));

	//On récupère les données
	foreach($tChOblig as $champ) {
		if (_request($champ)){
			$datas[$champ] = _request($champ);
		} else {
			$datas[$champ] = 0.;
		}
		$datas[$champ] = str_replace(',','.',$datas[$champ]); // Bug #574
	}
	//spip_log($datas,'hydraulic');
	// On ajoute la langue en cours pour différencier le fichier de cache par langue
	$datas['sLang'] = $spip_lang;

	// Nom du fichier en cache pour calcul déjà fait
	$datas['CacheFileName']=md5(serialize($datas));

	// On récupère les différents choix effectué sur les boutons radios ainsi que les libelles de tous les paramètres
	foreach($tChCalc as $cle){
		$choix_radio[$cle] = _request('choix_champs_'.$cle);
		if($bLibelles) {$datas['tLib'][$cle] = _T('hydraulic:param_'.$cle);}
	}

	$datas['min'] = 0;
	$datas['max'] = 0;
	$datas['pas'] = 1;
	$datas['i'] = 0;

	foreach($choix_radio as $ind){
		$decoup = explode('_', $ind, 3);
		$sVar = $decoup[count($decoup)-1];
		// Si il y a une valeur a calculer
		if(substr($ind, 0, 3) == 'cal'){
			$datas['ValCal'] = $sVar; // Stockage du nom de la variable à calculer
		}
		// Sinon si une valeur varie
		else if(substr($ind, 0, 3) == 'var'){
			// alors on récupère sa valeur maximum, minimum et son pas de variation
			$datas['min'] = _request('val_min_'.$sVar);
			$datas['max'] = _request('val_max_'.$sVar);
			$datas['pas'] = _request('pas_var_'.$sVar);
			// On fait pointer la variable qui varie sur l'indice de parcours du tableau i
			$datas['ValVar'] = $sVar; // Stockage du nom de la variable qui varie
			$datas[$sVar] = &$datas['i']; // Pointeur pour relier le compteur de boucle à la variable
		}
	}
	// Pour afficher correctement la valeur maximum avec les pb d'arrondi des réels
	$datas['max'] += $datas['pas']/2;

	return $datas;
}
?>