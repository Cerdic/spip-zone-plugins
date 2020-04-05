<?php
include_spip('hyd_inc/form_section.abstract.class');

class form_courbe_remous extends form_section {

	// Définition du nombre de colonnes du formulaire
	protected $nb_col = 2;

	function __construct() {

		$this->bFVC = false; // Ce n'est pas un formulaire avec les boutons fixe, varie, calcul

		$this->saisies['cond_lim']    = array(
			'condition_limite',
			array(
				'rQ'     =>array('debit_amont',2,'op'),
				'rYaval' =>array('h_aval_imposee',0.4,'pn'),
				'rYamont'=>array('h_amont_imposee',0.15,'pn')
			),
			'fix'
		);

		$this->saisies['param_calc']  = array(
			'param_calcul',
			array(
				'rDx'    =>array('pas_discret',5,'op'),
				'rPrec'  =>array('precision_calc',0.001,'op'),
				'Methode' => array('choix_resolution','form_courbe_remous_methode','s')
			),
			'fix'
		);

		$this->saisies['val_a_cal'] = array(
			'donnee_calc',
			array(
				'val_a_cal' => array('choix_donnee_calc','form_calcul_section_valacal','s')
			),
			'fix'
		);
		parent::__construct(true);

		foreach($this->saisies as &$saisie) {
			$saisie[2] = 'fix';
		}
	}


	public function charger() {
		$valeurs = parent::charger(true);
		$valeurs = array_merge($valeurs,
			array(
				'choix_section' => 'FT',
				'val_a_cal'  => 'none',
				'choix_champs_select' => array_merge(
					array('none' => 'aucune'),
					$this->champs_select_calc
				)
			)
		);
		if(self::DBG_CHARGER) spip_log($valeurs,'hydraulic',_LOG_DEBUG);
		return $valeurs;
	}


	protected function calculer() {
		$this->creer_section_param();

		// On transforme les champs du tableau des données du formulaire en variables
		extract($this->data, EXTR_OVERWRITE|EXTR_REFS);

		include_spip('hyd_inc/courbe_remous');

		$oLog = &$this->oLog;

		// On calcule les données pour créer un cache et afficher le résultat
		$this->oLog->Add(_T('hydraulic:largeur_berge').' = '.format_nombre($this->oSn->rLargeurBerge,$this->oP->iPrec).' m');
		$this->oLog->Add(_T('hydraulic:h_critique').' = '.format_nombre($this->oSn->CalcGeo('Yc'),$this->oP->iPrec).' m');
		$this->oLog->Add(_T('hydraulic:h_normale').' = '.format_nombre($this->oSn->CalcGeo('Yn'),$this->oP->iPrec).' m');

		// Calcul des courbes de remous
		$aC = array(); // deux items (Flu et Tor) composé d'un vecteur avec key=X et value=Y

		// Calcul depuis l'aval
		if($this->oSn->rHautCritique <= $rYaval) {
			$this->oLog->Add(_T('hydraulic:calcul_fluvial'));
			$oCRF = new cCourbeRemous($this->oLog, $this->oP, $this->oSn, $rDx);
			$aC['Flu'] = $oCRF->calcul($rYaval, $rLong, $Methode);
		}
		else {
			$this->oLog->Add(_T('hydraulic:pas_calcul_depuis_aval'), true);
		}

		// Calcul depuis l'amont
		if($this->oSn->rHautCritique >= $rYamont) {
			$this->oLog->Add(_T('hydraulic:calcul_torrentiel'));
			$oCRT = new cCourbeRemous($this->oLog, $this->oP, $this->oSn, -$rDx);
			$aC['Tor'] = $oCRT->calcul($rYamont, $rLong, $Methode);
		}
		else {
			$this->oLog->Add(_T('hydraulic:pas_calcul_depuis_amont'), true);
		}
		spip_log($aC,'hydraulic',_LOG_DEBUG);

		// Détection du ressaut hydraulique
		$bDetectRessaut = true;
		if($bDetectRessaut && isset($aC['Flu']) && isset($aC['Tor'])) {
			if(count($aC['Flu']) > count($aC['Tor']) || (count($aC['Flu']) == count($aC['Tor']) && $this->oSn->Calc('Imp', end($aC['Flu'])) > $this->oSn->Calc('Imp', end($aC['Tor'])))) {
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
				$Yco = $this->oSn->Calc('Yco', $aC[$sCN][$rX]); // Y conjugué
				$rLongRst = 5 * abs($aC[$sCN][$rX] - $Yco); // Longueur du ressaut
				$xRst = $rX + round($iSens * $rLongRst / $rDx) * $rDx; // Abscisse où comparer Yconj et Y
				$xRst = sprintf('%1.'.round($this->oP->iPrec).'f',$xRst);
				//spip_log("\nrX=$rX xRst=$xRst Yco=$Yco",'hydraulic',_LOG_DEBUG);
				if(isset($aC[$sCC][$xRst])) {
					// Hauteur décalée de la longueur du ressaut (il faut gérer la pente du fond)
					$Ydec = $aC[$sCC][$xRst] + $rLongRst * $this->oP->rIf * $iSens;
					spip_log("\nrX=$rX xRst=$xRst Yco=$Yco Ydec=$Ydec",'hydraulic',_LOG_DEBUG);
					if(($Yco - $Ydec) > 0) {
						$this->oLog->Add(_T('hydraulic:ressaut_hydrau', array('Xmin'=>min($rX,$xRst), 'Xmax'=>max($rX,$xRst))));
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
				$this->oLog->Add(_T('hydraulic:ressaut_dehors', array('Sens' => $sSens, 'X' => end($trX))));
				$aC[$sCN] = array();
			}
		}

		// Définition des abscisses
		$trX = array();
		if(isset($aC['Flu'])) $trX = array_merge($trX, array_keys($aC['Flu']));
		if(isset($aC['Tor'])) $trX = array_merge($trX, array_keys($aC['Tor']));
		$trX = array_unique($trX, SORT_NUMERIC);
		sort($trX, SORT_NUMERIC);

		// Calcul de la variable à calculer
		$this->data['ValCal'] = $val_a_cal;
		$tRes = array();
		if($val_a_cal != 'none') {
			foreach($trX as $rX) {
				$rY = false;
				if(isset($aC['Flu'][$rX]) && !isset($aC['Tor'][$rX])) {
					$rY = $aC['Flu'][$rX];
				}
				if(isset($aC['Tor'][$rX])) {
					if(!isset($aC['Flu'][$rX]) || (isset($aC['Flu'][$rX]) && $aC['Flu'][$rX]==$aC['Tor'][$rX])) {
						$rY = $aC['Tor'][$rX];
					}
				}
				if($rY !== false) {
					if(!in_array($val_a_cal,array('Yn', 'Yc', 'Hsc'))){
						$tRes[$rX] = $this->oSn->Calc($val_a_cal, $rY);
					}
					else{
						$tRes[$rX] = $this->oSn->CalcGeo($val_a_cal, $rY);
					}
				}
			}
		}

		return array_merge(
			$aC,
			array(
				'trX' => $trX,
				'tRes' => $tRes
			)
		);
	}


	/** ************************************************************************
	* Affichage des tableaux et graphiques des résultats des calculs
	* @return Chaîne de caractère avec le code HTML à afficher
	***************************************************************************/
	protected function afficher_result() {
		// Code de langue du champ de la valeur calculée
		if($this->data['ValCal'] != 'none') {
			$sCodeLangValCal = $this->champs_select_calc[$this->data['ValCal']];
			$sLibValCal = _T("hydraulic:$sCodeLangValCal");
		} else {
			$sLibValCal = '';
		}

		// Gestion du graphique affichant la variable à calculer
		if(in_array($this->data['ValCal'], array('Hs', 'Hsc', 'Yf', 'Yt', 'Yco')))
		{
			// Affichage d'une courbe supplémentaire sur le graphique courbe de remous
			$choix_graph = 'courbe';
		}
		elseif(in_array($this->data['ValCal'], array('B', 'P', 'S', 'R', 'V', 'Fr', 'J', 'I-J', 'Imp', 'Tau0')))
		{
			// Affichage de la donnée sur un nouveau graphique
			$choix_graph = 'graph';
		}
		else
		{
			// Pas de graph pour la hauteur normale et la hauteur critique qui sont déjà affichés par défaut
			$choix_graph = 'none';
		}

		//Construction d'un tableau des indices x combinant les abscisses des 2 lignes d'eau
		$trX = $this->result['trX'];

		$echo = '';

		if(!empty($trX)) {
			/***************************************************************************
			*                        Affichage du graphique
			****************************************************************************/
			include_spip('hyd_inc/graph.class');
			$oGraph = new cGraph('', _T('hydraulic:abscisse'));
			// Ligne d'eau globale
			$LgnEau = array();
			if(isset($this->result['Flu'])) {
				$LgnEau = $this->result['Flu'];
			}
			spip_log($LgnEau,'hydraulic',_LOG_DEBUG);
			if(isset($this->result['Tor'])) {
				$LgnEau = array_merge($this->result['Tor'], $LgnEau);
			}
			spip_log($LgnEau,'hydraulic',_LOG_DEBUG);
			if(!empty($LgnEau)) {
				$oGraph->AddSerie(
					'',
					array_keys($LgnEau),
					array_values($LgnEau),
					'#F0F0FF70',
					'lineWidth:0, fill:true, showLabel:false'
				);
			}
			// Cote des berges
			$oGraph->AddSerie(
				'berge',
				$trX,
				$this->oP->rYB,  // La cote des berges sera calculée à partir de la pente fournie dans GetGraph
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
			if(isset($this->result['Flu'])) {
				$oGraph->AddSerie(
					'ligne_eau_fluviale',
					array_keys($this->result['Flu']),
					array_values($this->result['Flu']),
					'#0093bd',
					'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}'
				);
			}
			// Ligne d'eau torrentielle
			if(isset($this->result['Tor'])) {
				$oGraph->AddSerie(
					'ligne_eau_torrentielle',
					array_keys($this->result['Tor']),
					array_values($this->result['Tor']),
					'#77a3cd',
					'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}'
				);
			}
			// Hauteur critique
			if(is_numeric($this->oSn->rHautCritique)) {
				$oGraph->AddSerie(
					'h_critique',
					$trX,
					$this->oSn->rHautCritique,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
					'#ff0000',
					'lineWidth:2'
				);
			}
			// Hauteur normale
			if(is_numeric($this->oSn->rHautNormale)) {
				$oGraph->AddSerie(
					'h_normale',
					$trX,
					$this->oSn->rHautNormale,  // La cote du fond sera calculée à partir de la pente fournie dans GetGraph
					'#a4c537',
					'lineWidth:2'
				);
			}

			// Valeur calculée
			if($choix_graph == 'courbe') {
				$oGraph->AddSerie(
					$sCodeLangValCal,
					array_keys($this->result['tRes']),
					array_values($this->result['tRes']),
					'#C17AF0',
					'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}'
				);
			}

			// Décalage des données par rapport au fond
			$oGraph->Decal(max(0,-$this->data['rIf']*$this->data['rLong']), $this->data['rIf'], $this->data['rLong']);

			// Récupération du graphique
			$echo .= $oGraph->GetGraph('courbe_remous',400,600);

			// Affichage du graphique
			if($choix_graph == 'graph') {
				$echo .= $this->getGraph(
					_T('hydraulic:abscisse'),
					$sLibValCal,
					array_keys($this->result['tRes']),
					array_values($this->result['tRes'])
				);
			}
		}

		// Journal de calcul
		$echo .= $this->oLog->Result();

		if(!empty($trX)) {
			/***************************************************************************
			*                   Affichage du tableau de données
			****************************************************************************/
			$echo.='<table class="spip">
				<thead>
					<tr class="row_first">
						<th scope="col" colspan="1" rowspan="2">'._T('hydraulic:abscisse').'</th>
						<th scope="col" colspan="2" rowspan="1">'._T('hydraulic:ligne_eau_fluviale').'</th>
						<th scope="col" colspan="2" rowspan="1">'._T('hydraulic:ligne_eau_torrentielle').'</th>
					</tr>
					<tr class="row_first">
						<th scope="col">'._T('hydraulic:tirant_eau').'</th>
						<th scope="col">'.$sLibValCal.'</th>
						<th scope="col">'._T('hydraulic:tirant_eau').'</th>
						<th scope="col">'.$sLibValCal.'</th>
					</tr>
				</thead>
				<tbody>';
					$i=0;
					foreach($trX as $rX) {
						$i+=1;
						$echo.='<tr class="align_right ';
							$echo.=($i%2==0)?'row_even':'row_odd';
							$echo.='"><td>'.format_nombre($rX,$this->oP->iPrec).'</td>';
							if(isset($this->result['tRes'][$rX])) {
								$sValCal = format_nombre($this->result['tRes'][$rX],$this->oP->iPrec);
							} else {
								$sValCal = '-';
							}
							if(isset($this->result['Flu'][$rX])) {
								// On formalise les résultats, avec le nombre de chiffres aprés la virgule adéquat
								$echo .= '<td>'.format_nombre($this->result['Flu'][$rX],$this->oP->iPrec).'</td>';
								$echo .= "<td>$sValCal</td>";
							}
							else {
								$echo .= '<td></td><td></td>';
							}
							if(isset($this->result['Tor'][$rX])) {
								$echo .= '<td>'.format_nombre($this->result['Tor'][$rX],$this->oP->iPrec).'</td>';
								$echo .= "<td>$sValCal</td>";
							}
							else {
								$echo .= '<td></td><td></td>';
							}
						$echo .= '</tr>';
					}
				$echo.='</tbody>
			</table>';
		}
		return $echo;
	}


	protected function get_champs_libelles() {
		$lib = parent::get_champs_libelles();
		foreach($this->champs_select_calc as $cle=>$champ) {
			$lib[$cle] = _T('hydraulic:'.$champ);
		}
		return $lib;
	}
}
?>
