<?php
/**
 *      @file formulaires/courbe_remous.php
 *      Fonctions du formulaire CVT pour les courbes de remous
 */

/*      Copyright 2009-2012 Dorch <dorch@dorch.fr>, Médéric Dulondel
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */


/* Tableau des champs à afficher dans le formulaire.
 * On travaille avec les libelles non traduits pour pouvoir gérer
 * le multilinguisme.
 */
function mes_saisies() {

	// On récupère les champs communs à tous les formulaires à savoir les champs de section.
	include_spip('hyd_inc/section');
	$fieldset_champs = mes_saisies_section(true);

	$fieldset_champs['Cond_lim']    = array(
		'condition_limite',
		array(
			'rQ'     =>array('debit_amont',2,'op'),
			'rYaval' =>array('h_aval_imposee',0.4,'pn'),
			'rYamont'=>array('h_amont_imposee',0.15,'pn')
		)
	);

	$fieldset_champs['Param_calc']  = array(
		'param_calcul',
		array(
			'rDx'    =>array('pas_discret',5,'op'),
			'rPrec'  =>array('precision_calc',0.001,'op')
		)
	);
	return $fieldset_champs;
}


// Définition des champs à lire dans le formulaire
function getChamps() {

	$tSaisie = mes_saisies();
	$sTypeSection = _request('crTypeSection');
	$tData = array();

	foreach($tSaisie as $IdFS=>$FieldSet) {
		// Si ce n'est pas une section ou la section définie...
		if((substr($IdFS,0,1) != 'F') || ($IdFS == $sTypeSection)){
			// ... alors on parcourt notre deuxième tableau en ajoutant les champs nécessaires.
			foreach($FieldSet[1] as $Cle=>$Champ) {
				$tData[$IdFS.'_'.$Cle] = _request($IdFS.'_'.$Cle); // Valeur dans le formulaire
				$tCtrl[$IdFS.'_'.$Cle] = $Champ[2]; // Codes de vérification
			}
		}
	}
	return array($tData,$tCtrl);
}


function formulaires_courbe_remous_charger_dist() {
	// On charge les saisies et les champs qui nécessitent un accès par les fonctions
	$tSaisie_section = mes_saisies();
	$valeurs = array(
		'crTypeSection' => 'FT',
		'mes_saisies' => $tSaisie_section
	);

	// On charge tous les champs avec leur valeur
	foreach($tSaisie_section as $CleFD=>$FieldSet) {
		foreach($FieldSet[1] as $Cle=>$Champ) {
			$valeurs[$CleFD.'_'.$Cle] = $Champ[1];
		}
	}
    $valeurs['choix_resolution'] = _request('choix_resolution');

	return $valeurs;
}

function formulaires_courbe_remous_verifier_dist() {
	$erreurs = array();
	list($tData,$tCtrl) = getChamps();
	include_spip('hyd_inc/formulaire');
	return hyd_formulaires_verifier($tData,$tCtrl);
}

function formulaires_courbe_remous_traiter_dist() {
	global $spip_lang;
	include_spip('hyd_inc/section.class');
	include_spip('hyd_inc/cache');
	include_spip('hyd_inc/log.class');
	include_spip('hyd_inc/courbe_remous');
	include_spip('hyd_inc/graph.class');

	$datas = array();
	$echo = '';
	$tSaisie = mes_saisies();
	$tChUtil = array();
	$crTypeSection = _request('crTypeSection');

	// On récupère tous les champs utiles, à savoir les champs fixes, et les champs appartenant à la section choisie
	foreach($tSaisie as $IdFS=>$FieldSet) {
		if((substr($IdFS,0,1) != 'F') || ($IdFS == $crTypeSection)){
			foreach($FieldSet[1] as $Cle=>$Champ) {
				$tChUtil[] = $IdFS.'_'.$Cle;
			}
		}
	}

	//On récupère tous les champs utiles dans le tableau datas
	foreach($tChUtil as $champ) {
		if (_request($champ)){
			$datas[$champ] = _request($champ);
		}

		$datas[$champ] = str_replace(',','.',$datas[$champ]); // Bug #574
	}

	// On ajoute la langue en cours pour différencier le fichier de cache par langue
	$datas['sLang'] = $spip_lang;

	// Nom du fichier en cache pour calcul déjà fait
	$CacheFileName=md5(serialize($datas));

	// Initialisation de la classe chargée d'afficher le journal de calcul
	$oLog = new cLog();

	//Transformation des variables contenues dans $datas
	foreach($datas as $champ=>$data) {
		${$champ}=$data;
	}

	// Contrôle du nombre de pas d'espace maximum
	$iPasMax = 1000;
	if($c_bief_rLong / $Param_calc_rDx > $iPasMax) {
		$Param_calc_rDx = $c_bief_rLong / $iPasMax;
		$oLog->Add(_T('hydraulic:pas_nombre').' > '.$iPasMax.' => '._T('hydraulic:pas_ajustement').$Param_calc_rDx.' m');
	}
	//spip_log(array($Cond_lim_rYaval,$c_bief_rKs,$Cond_lim_rQ,$c_bief_rLong,$c_bief_rIf,$Param_calc_rDx,$Param_calc_rPrec),'hydraulic');

	// Enregistrement des paramètres dans les classes qui vont bien
	$oParam= new cParam($c_bief_rKs,$Cond_lim_rQ,$c_bief_rIf,$Param_calc_rPrec,$c_bief_rYB);

	// Création d'un objet de type Section selon la section choisie.
	switch($crTypeSection) {
		case 'FT':
		include_spip('hyd_inc/sectionTrapez.class');
		$oSection=new cSnTrapez($oLog,$oParam,$FT_rLargeurFond,$FT_rFruit);
		break;

		case 'FR':
		include_spip('hyd_inc/sectionRectang.class');
		$oSection=new cSnRectang($oLog,$oParam,$FR_rLargeurFond);
		break;

		case 'FC':
		include_spip('hyd_inc/sectionCirc.class');
		$oSection=new cSnCirc($oLog,$oParam,$FC_rD);
		break;

		case 'FP':
		include_spip('hyd_inc/sectionPuiss.class');
		$oSection=new cSnPuiss($oLog,$oParam,$FP_rCoef,$FP_rLargeurBerge);
		break;

		default:
		include_spip('hyd_inc/sectionTrapez.class');
		$oSection=new cSnTrapeze($oLog,$oParam,$FT_rLargeurFond,$FT_rFruit);
	}

	/***************************************************************************
	*                        Calcul de la ligne d'eau
	****************************************************************************/
	$bNoCache = true; // false pour activer le cache !!!! BUG : Il manque la méthode résolution comme clé de différenciation de $CacheFileName !!!!
	if(!$bNoCache && is_file(HYD_CACHE_DIRECTORY.$CacheFileName)) {
		// On récupère toutes les données dans un cache déjà créé
		list($aC,$sLog,$oSection->rHautCritique,$oSection->rHautNormale) = ReadCacheFile($CacheFileName);
	}
	else {
		// On calcule les données pour créer un cache et afficher le résultat
		$oLog->Add(_T('hydraulic:largeur_berge').' = '.format_nombre($oSection->rLargeurBerge,$oParam->iPrec).' m');
		$oLog->Add(_T('hydraulic:h_critique').' = '.format_nombre($oSection->CalcGeo('Yc'),$oParam->iPrec).' m');
		$oLog->Add(_T('hydraulic:h_normale').' = '.format_nombre($oSection->CalcGeo('Yn'),$oParam->iPrec).' m');

		// Calcul des courbes de remous
		$aC = array(); // deux items (Flu et Tor) composé d'un vecteur avec key=X et value=Y

		// Calcul depuis l'aval
		if($oSection->rHautCritique <= $Cond_lim_rYaval) {
			$oLog->Add(_T('hydraulic:calcul_fluvial'));
			$oCRF = new cCourbeRemous($oLog, $oParam, $oSection, $Param_calc_rDx);
			$aC['Flu'] = $oCRF->calcul($Cond_lim_rYaval, $c_bief_rLong, _request('choix_resolution'));
		}
		else {
			$oLog->Add(_T('hydraulic:pas_calcul_depuis_aval'));
		}

		// Calcul depuis l'amont
		if($oSection->rHautCritique >= $Cond_lim_rYamont) {
			$oLog->Add(_T('hydraulic:calcul_torrentiel'));
			$oCRT = new cCourbeRemous($oLog, $oParam, $oSection, -$Param_calc_rDx);
			$aC['Tor'] = $oCRT->calcul($Cond_lim_rYamont, $c_bief_rLong, _request('choix_resolution'));
		}
		else {
			$oLog->Add(_T('hydraulic:pas_calcul_depuis_amont'));
		}
		spip_log($oParam,'hydraulic',_LOG_DEBUG);
		spip_log($aC,'hydraulic',_LOG_DEBUG);

		// Détection du ressaut hydraulique
		$bDetectRessaut = true;
		if($bDetectRessaut && isset($aC['Flu']) && isset($aC['Tor'])) {
			if(count($aC['Flu']) > count($aC['Tor']) || (count($aC['Flu']) == count($aC['Tor']) && $oSection->Calc('Imp', end($aC['Flu'])) > $oSection->Calc('Imp', end($aC['Tor'])))) {
				// La courbe fluviale va jusqu'au bout
				$sCC = 'Flu';
				$sCN = 'Tor';
				$iSens = 1; // On cherche l'aval du ressaut
				$sSens = _T('hydraulic:amont');
			} else {
				// La courbe torrentielle va jusqu'au bout
				$sCC = 'Tor';
				$sCN = 'Flu';
				$iSens = -1; // On cherche l'amont du ressaut
				$sSens = _T('hydraulic:aval');
			}
			$trX = array_reverse(array_keys($aC[$sCN])); // Parcours des sections de la ligne d'eau la plus courte
			$bRessaut = false;
			foreach($trX as $rX) {
				// Calcul de l'abscisse de la section dans l'autre régime
				$Yco = $oSection->Calc('Yco', $aC[$sCN][$rX]); // Y conjugué
				$rLongRst = 5 * abs($aC[$sCN][$rX] - $Yco); // Longueur du ressaut
				$xRst = $rX + round($iSens * $rLongRst / $Param_calc_rDx) * $Param_calc_rDx; // Abscisse où comparer Yconj et Y
				$xRst = sprintf('%1.'.round($oParam->iPrec).'f',$xRst);
				//spip_log("\nrX=$rX xRst=$xRst Yco=$Yco",'hydraulic',_LOG_DEBUG);
				if(isset($aC[$sCC][$xRst])) {
					// Hauteur décalée de la longueur du ressaut (il faut gérer la pente du fond)
					$Ydec = $aC[$sCC][$xRst] + $rLongRst * $oParam->rIf * $iSens;
					spip_log("\nrX=$rX xRst=$xRst Yco=$Yco Ydec=$Ydec",'hydraulic',_LOG_DEBUG);
					if(($Yco - $Ydec) > 0) {
						$oLog->Add(_T('hydraulic:ressaut_hydrau', array('Xmin'=>min($rX,$xRst), 'Xmax'=>max($rX,$xRst))));
						spip_log("rX=$rX xRst=$xRst",'hydraulic',_LOG_DEBUG);
						// Modification de la ligne d'eau CC
						foreach(array_keys($aC[$sCN]) as $rXCC) {
							if($iSens * ($rXCC - $rX) < 0) {
								unset($aC[$sCC][$rXCC]);
							} elseif($rXCC == $rX) {
								$aC[$sCC][$rXCC] = $aC[$sCN][$rXCC];
								break;
							}
						}
						// Modification de la ligne d'eau CN
						foreach($trX as $rXCN) {
							if($iSens * ($rXCN - $xRst) > 0) {
								unset($aC[$sCN][$rXCN]);
							} elseif($rXCN == $xRst) {
								$aC[$sCN][$rXCN] = $aC[$sCC][$rXCN];
								break;
							}
						}
						$bRessaut = true;
						break;
					}
				}
			}
			if(!$bRessaut) {
				// Le ressaut est en dehors du canal
				$oLog->Add(_T('hydraulic:ressaut_dehors', array('Sens' => $sSens, 'X' => end($trX))));
				$aC[$sCN] = array();
			}
		}

		//Production du journal de calcul
		$sLog = $oLog->Result();
		//Enregistrement des données dans fichier cache
		WriteCacheFile($CacheFileName,array($aC,$sLog,$oSection->rHautCritique,$oSection->rHautNormale));
	}
	//Construction d'un tableau des indices x combinant les abscisses des 2 lignes d'eau
	$trX = array();
	if(isset($aC['Flu'])) $trX = array_merge($trX, array_keys($aC['Flu']));
	if(isset($aC['Tor'])) $trX = array_merge($trX, array_keys($aC['Tor']));
	$trX = array_unique($trX, SORT_NUMERIC);
	sort($trX, SORT_NUMERIC);
	//~ spip_log($tr,'hydraulic'); // Debug


	/***************************************************************************
	*                        Affichage du graphique
	****************************************************************************/
	$oGraph = new cGraph();
	// Cote des berges
	$oGraph->AddSerie(
		'berge',
		$trX,
		$oParam->rYB,  // La cote des berges sera calculée à partir de la pente fournie dans GetGraph
		'#C58f50',
		'lineWidth:1'
	);
	// Cote du fond
	$oGraph->AddSerie(
		'fond',
		$trX,
		0,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
		'#753f00',
		'lineWidth:1, fill:true'
	);
	// Ligne d'eau fluviale
	if(isset($aC['Flu'])) {
		$oGraph->AddSerie(
			'ligne_eau_fluviale',
			array_keys($aC['Flu']),
			array_values($aC['Flu']),
			'#0093bd',
			'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}'
		);
	}
	// Ligne d'eau torrentielle
	if(isset($aC['Tor'])) {
		$oGraph->AddSerie(
			'ligne_eau_torrentielle',
			array_keys($aC['Tor']),
			array_values($aC['Tor']),
			'#77a3cd',
			'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}'
		);
	}
	// Hauteur critique
	if(is_numeric($oSection->rHautCritique)) {
		$oGraph->AddSerie(
			'h_critique',
			$trX,
			$oSection->rHautCritique,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
			'#ff0000',
			'lineWidth:2'
		);
	}
	// Hauteur normale
	if(is_numeric($oSection->rHautNormale)) {
		$oGraph->AddSerie(
			'h_normale',
			$trX,
			$oSection->rHautNormale,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
			'#a4c537',
			'lineWidth:2'
		);
	}

	// Décalage des données par rapport au fond
	$oGraph->Decal(0, $c_bief_rIf, $c_bief_rLong);

	// Récupération du graphique
	$echo .= $oGraph->GetGraph('graphique',400,600);


	$echo .= $sLog;

	/***************************************************************************
	*                   Affichage du tableau de données
	****************************************************************************/
	$echo.='<table class="spip">
		<thead>
			<tr class="row_first">
				<th scope="col" colspan="1" rowspan="2">'._T('hydraulic:abscisse').' (m)</th>
				<th scope="col" colspan="2" rowspan="1">'._T('hydraulic:ligne_eau_fluviale').'</th>
				<th scope="col" colspan="2" rowspan="1">'._T('hydraulic:ligne_eau_torrentielle').'</th>
			</tr>
			<tr class="row_first">
				<th scope="col">'._T('hydraulic:tirant_eau').'</th>
				<th scope="col">Froude</th>
				<th scope="col">'._T('hydraulic:tirant_eau').'</th>
				<th scope="col">Froude</th>
			</tr>
		</thead>
		<tbody>';
			$i=0;
			foreach($trX as $rX) {
				$i+=1;
				$echo.='<tr class="align_right ';
					$echo.=($i%2==0)?'row_even':'row_odd';
					$echo.='"><td>'.format_nombre($rX,$oParam->iPrec).'</td>';
					if(isset($aC['Flu'][$rX])) {
						// On formalise les résultats, avec le nombre de chiffres aprés la virgule adéquat
						$echo .= '<td>'.format_nombre($aC['Flu'][$rX],$oParam->iPrec).'</td>';
						$echo .= '<td>'.format_nombre($oSection->Calc('Fr', $aC['Flu'][$rX]),$oParam->iPrec).'</td>';
					}
					else {
						$echo .= '<td></td><td></td>';
					}
					if(isset($aC['Tor'][$rX])) {
						$echo .= '<td>'.format_nombre($aC['Tor'][$rX],$oParam->iPrec).'</td>';
						$echo .= '<td>'.format_nombre($oSection->Calc('Fr', $aC['Tor'][$rX]),$oParam->iPrec).'</td>';
					}
					else {
						$echo .= '<td></td><td></td>';
					}
				$echo .= '</tr>';
			}
		$echo.='</tbody>
	</table>';

	$res['message_ok'] = $echo;

	return $res;
}
?>
