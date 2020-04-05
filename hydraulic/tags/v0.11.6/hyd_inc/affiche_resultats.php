<?php

/**
 * Renvoie un tableau formaté à partir d'un array à deux dimensions
 * @param $tContent Tableau à 2 dimensions contenant les cellules [ligne][colonne]
 * @param $tEntetes Tableau contenant les entêtes de colonne
 */
function GetResultTable($tContent,$tEntetes=false) {
	// On génère les entêtes du tableau de résulats
	$echo='<table class="spip">';

		if($tEntetes) {
			$echo.='<thead>
				<tr class="row_first">';
					foreach($tEntetes as $s){
						$echo.= '<th scope="col" rowspan="2">'.$s.'</th>';
					}
				$echo.= '</tr>
			</thead>';
		}
		$echo.='<tbody>';
			$i=0;
			foreach($tContent as $Ligne){
				$i++;
				$echo.= '<tr class="align_right ';
					$echo.=($i%2==0)?'row_even':'row_odd';
					$echo.='">';
					foreach($Ligne as $Cellule){
						$echo.= '<td>'.$Cellule.'</td>';
					}
				$echo.= '</tr>';
			}
		$echo.= '</tbody>
	</table>';
	return $echo;
}


/**
 * Renvoie Le résultat sous la forme de tableaux et graphiques
 * @param $datas Tableau contenant :
 *      - la valeur de tous les paramètres répertoriés par leur clé
 *      - les clés de la variable à calculer et de la variable qui varie (ValCal et ValVar)
 *      - Un tableau des libellés de tous les paramètres (clé tLib)
 *      - Le nombre de décimales pour la précision d'affichage (clé iPrec)
 * @param $tAbs Tableau contenant les abscisses du résultat (paramètre qui varie)
 * @param $tRes Tableau contenant les résultats du calcul
 * @param $tFlag Tableau contenant les flags du résultats du calcul
 */
function AfficheResultats($datas, $tAbs, $tRes, $tFlag=false) {
	$echo = '';
	$tLib = $datas['tLib'];
	if(!isset($datas['ValVar'])) {
		$datas['ValVar']='';
	}
	// Affichage des paramètres fixes
	$tCnt = array();
	foreach($tLib as $k=>$s) {
		if(!in_array($k,array($datas['ValCal'],$datas['ValVar']))) {
			$tCnt[]=array($s,format_nombre($datas[$k], $datas['iPrec']));
		}
		$tEnt = array(_T('hydraulic:param_fixes'),_T('hydraulic:valeurs'));
	}
	// Si il n'y a pas de valeur à varier on ajoute le résultat et le flag de calcul s'il existe
	if(!$datas['ValVar']) {
		$tCnt[]=array('<b>'.$tLib[$datas['ValCal']].'</b>','<b>'.format_nombre($tRes[0], $datas['iPrec']).'</b>');
		if($tFlag) {
			spip_log($tFlag,'hydraulic.'._LOG_DEBUG);
			$tCnt[]= array(_T('hydraulic:type_ecoulement'),_T('hydraulic:flag_'.$tFlag[0]));
		}
	}
	$tableau_fixe = GetResultTable($tCnt,$tEnt);

	// Affichage d'un tableau pour un paramètre qui varie
	if($datas['ValVar']) {
		$tCnt=array();
		foreach($tAbs as $k=>$Abs){
			$tCnt[] = array(format_nombre($Abs, $datas['iPrec']),format_nombre($tRes[$k], $datas['iPrec']));
		}
		$tEnt = array($tLib[$datas['ValVar']],$tLib[$datas['ValCal']]);
		$tableau_variable = GetResultTable($tCnt,$tEnt);

		// Si la première valeur est infinie alors ...
		if(is_infinite($tRes[0])){
			// ... on supprime cette valeur
			unset($tRes[0]);
			// ... on tasse le tableau des résultats
			$tRes = array_values($tRes);
			// ... on supprime l'abscisse correspond
			unset($tAbs[0]);
			// ... on tasse le tableau des abscisses
			$tAbs = array_values($tAbs);
		}

		/***************************************************************************
		*                        Affichage du graphique
		****************************************************************************/
		include_spip('hyd_inc/graph.class');
		$oGraph = new cGraph('',$tLib[$datas['ValVar']],'');
		if(isset($tRes)) {
			$oGraph->AddSerie(
				_T('hydraulic:param_'.$datas['ValCal']),
				$tAbs,
				$tRes,
				'#00a3cd',
				'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}');
		}
		// Récupération du graphique
		$graph = $oGraph->GetGraph('graphique',400,600);
		$echo = $graph."\n";
	}
	$echo .= '<table class="hyd_graph"><tr><td>'.$tableau_fixe.'</td>';
	if(isset($tableau_variable)) {
		$echo .= '<td width="5%">&nbsp;</td><td>'.$tableau_variable.'</td>';
	}
	$echo .= '</tr></table>';
	return $echo;
}

?>