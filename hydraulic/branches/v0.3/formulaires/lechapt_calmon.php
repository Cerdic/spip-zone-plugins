<?php
/*
 * formulaire/lechapt_calmon.php
 *
 *
 *
 * Copyright 2012 David Dorchies <dorch@dorch.fr>
 *
 *
 *
 * This program is free software; you can redistribute it and/or modify
 *
 * it under the terms of the GNU General Public License as published by
 *
 * the Free Software Foundation; either version 2 of the License, or
 *
 * (at your option) any later version.
 *
 *
 *
 * This program is distributed in the hope that it will be useful,
 *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *
 * GNU General Public License for more details.
 *
 *
 *
 * You should have received a copy of the GNU General Public License
 *
 * along with this program; if not, write to the Free Software
 *
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *
 * MA 02110-1301, USA.
 *
 */
 
function mes_champs_coeff_materiau() {
	$mes_champs_coeff = array('L','M','N','Q','D','J','Lg');
	return $mes_champs_coeff;
}

function mes_champs_sans_coeff_materiau(){
	$mes_champs_sans_coeff = array(
			'Q' => _T('hydraulic:param_Q'),
			'D' => _T('hydraulic:param_D'),
			'J' => _T('hydraulic:param_J'),
			'Lg' => _T('hydraulic:param_Lg')
		);
	
	return $mes_champs_sans_coeff;
}

function id_decoupe($champs){
	$decoup = explode('_', $champs, 3);
	return $decoup[count($decoup)-1];
}

/* Tableau des données pour chaque type de tuyau. Ces valeurs sont associées
 * aux numéros des options du select (voir page lechapt_calmon.php)
 */
function mes_saisies_materiau() {
	$type_materiaux = array(
	
				'1'          => array(
									   'L' =>1.863,
									   'M' =>2,
									   'N' =>5.33			
				),
				
				'2'          => array(
									   'L' =>1.601,
									   'M' =>1.975,
									   'N' =>5.25			
				),
				
				'3'          => array(
									   'L' =>1.40,
									   'M' =>1.96,
									   'N' =>5.19			
				),
				
				'4'          => array(
									   'L' =>1.16,
									   'M' =>1.93,
									   'N' =>5.11			
				),
				
				'5'          => array(
									   'L' =>1.1,
									   'M' =>1.89,
									   'N' =>5.01			
				),
				
				'6'          => array(
									   'L' =>1.049,
									   'M' =>1.86,
									   'N' =>4.93			
				),
				
				'7'          => array(
									   'L' =>1.01,
									   'M' =>1.84,
									   'N' =>4.88			
				),
				
				'8'          => array(
									   'L' =>0.916,
									   'M' =>1.78,
									   'N' =>4.78			
				),
				
				'9'          => array(
									   'L' =>0.971,
									   'M' =>1.81,
									   'N' =>4.81			
				),
	);
		
  return $type_materiaux;
  
}

function champs_obligatoires_lcalmon(){
	/* 
	 * Ce tableau contient la liste de tous les champs du formulaire.
	 * La suite de cette fonction se chargera de supprimer les valeurs non obligatoires.
	 */
	 
	$tChOblig = mes_champs_coeff_materiau();
	$tChUtil = mes_champs_sans_coeff_materiau();
	
	$choix_champs = array();
	foreach($tChUtil as $cle=>$valeur){
		$choix_champs[$cle] = _request('choix_champs_'.$cle);
	}

	foreach($choix_champs as $cle=>$valeur){
		// Si le choix du select est de calculer une valeur...
		if(substr($valeur, 0,3) != 'val'){
			foreach($tChOblig as $cle1=>$valeur1){
				if($cle == $valeur1){
					// ... alors on peut supprimer de notre tableau le champs calculé (il n'est pas obligatoire car grisé)
					unset($tChOblig[$cle1]);
					// Permet de tasser le tableau
					$tChOblig = array_values($tChOblig);
				}
			}
		}
		// Si le choix du select est de faire varier une valeur alors on ajoute les 3 champs nécessaires
		if(substr($valeur, 0, 3) == 'var'){
			$tChOblig[] = 'val_min_'.$cle;
			$tChOblig[] = 'val_max_'.$cle;
			$tChOblig[] = 'pas_var_'.$cle;
		}	
	}
	$tChOblig[] = 'prec_lc';
	
	return $tChOblig;
}

function formulaires_lechapt_calmon_charger_dist() { 
	$valeurs = array(
		'mes_saisies_materiaux' => mes_saisies_materiau(),
		'tableau_caract' => mes_champs_sans_coeff_materiau(),
		'typeMateriau' => 1,
		'prec_lc' => 0.001,
		'L' => 1.863,
		'M' => 2,
		'N' => 5.33,
		'Q' => 3,
		'D' => 1.2,
		'J' => 0.634482025,
		'Lg'=> 100
	);
  
	$mes_champs = mes_champs_sans_coeff_materiau();
	foreach($mes_champs as $cle=>$valeur){
		if($cle == 'Q'){
			$valeurs['choix_champs_'.$cle] = 'calcul_val_'.$cle;
		}
		else{
			$valeurs['choix_champs_'.$cle] = 'val_fixe_'.$cle;
		}
		
		$valeurs['val_min_'.$cle] = 1;
		$valeurs['val_max_'.$cle] = 2;
		$valeurs['pas_var_'.$cle] = 0.1;
	}
	
    return $valeurs;
}

function formulaires_lechapt_calmon_verifier_dist(){
    $erreurs = array();
    $datas = array();
    $tChOblig= champs_obligatoires_lcalmon();
    // Vérifier que les champs obligatoires sont bien là :
    foreach($tChOblig as $obligatoire) {
        if (!_request($obligatoire)) {
			$erreurs[$obligatoire] = _T('hydraulic:champ_obligatoire');
        }
        else {
            $datas[$obligatoire] = _request($obligatoire);
        }
    }

	// Gestion des valeurs négatives
    foreach($datas as $champ=>$data) {
        if ($data < 0) $erreurs[$champ] = _T('hydraulic:valeur_positive');
    }
    
    if (count($erreurs)) {
        $erreurs['message_erreur'] = _T('hydraulic:saisie_erreur');
    }

    return $erreurs;
}

function formulaires_lechapt_calmon_traiter_dist(){

	include_spip('hyd_inc/cache');
    include_spip('hyd_inc/log.class');
    include_spip('hyd_inc/graph.class');
	
	 /***************************************************************************
    *                        Calcul de Lechapt et calmon
    ****************************************************************************/
    $echo = '';
	$ValCal = '';
	$result = array();
	$choix_radio = array();
	$tabLibelle = array();
	$champs_materiau_coeff = mes_champs_coeff_materiau();
	$champs_materiau_sans_coeff = mes_champs_sans_coeff_materiau();
    $iPrec=(int)-log10(_request('prec_lc'));


	foreach($champs_materiau_coeff as $champs){
		${$champs} = _request($champs);
	}
	
	foreach($champs_materiau_sans_coeff as $cle=>$valeur){
		$choix_radio[$cle] = _request('choix_champs_'.$cle);
		$tabLibelle[$cle] = _T('hydraulic:param_'.$cle);
	}

	$min = 0;
	$max = 0;
	$pas = 1;
	$i = 0;
	
		
	foreach($choix_radio as $ind){
		if(substr($ind, 0, 3) == 'cal'){
			$ValCal = id_decoupe($ind);
		}
		
		else if(substr($ind, 0, 3) == 'var'){
			$min = _request('val_min_'.id_decoupe($ind));
			$max = _request('val_max_'.id_decoupe($ind));
			$pas = _request('pas_var_'.id_decoupe($ind));
			${id_decoupe($ind)} = &$i;
		}
	}
	
	switch($ValCal){
		case 'Q':
			if($L != 0 && $Lg != 0 && $M != 0){
				for($i = $min; $i <= $max; $i+= $pas){
					$result[] = pow(((($J*pow($D, $N))/$L)*(1000/$Lg)), 1/$M);
				}
			}
			else{
				$result[] = 0;
			}
			
		break;
		
		case 'D': 
			if($J != 0 && $N != 0){
				for($i = $min; $i <= $max; $i+= $pas){
					$result[] = pow(((($L*pow($Q, $M))/$J)*($Lg/1000)), 1/$N);
				}
			}
			else{
				$result[] = 0;
			}
			
		break;
		
		case 'J':
			if($D != 0){
				for($i = $min; $i <= $max; $i+= $pas){
					$result[] = (($L*pow($Q, $M))/pow($D, $N))*($Lg/1000) ;
				}
			}
			else{
				$result[] = 0;
			}
			
		break;
		
		case 'Lg':
			if($L*pow($Q,$M) != 0){
				for($i = $min; $i <= $max; $i+= $pas){
					$result[] = (($J*pow($D, $N))/($L*pow($Q,$M)))*1000 ;
				}
			}
			else{
				$result[] = 0;
			}
			
		break;
	}

	/***************************************************************************
    *                   Affichage du tableau de données
    ****************************************************************************/
	$cptValVar = 1;
	$i = 0;
	$tabClass = array();
	
	foreach($tabLibelle as $cle=>$valeur){
		if(substr(_request('choix_champs_'.$cle), 0, 3) == 'var'){
			$cptValVar++;
		}
	}
	
	foreach($tabLibelle as $cle=>$valeur){
		if(substr(_request('choix_champs_'.$cle), 0, 3) == 'cal'){
			$tabClass['cal'] = $tabLibelle[$cle];
		}
		else if(substr(_request('choix_champs_'.$cle), 0, 3) == 'var'){
			$tabClass['var'] = $tabLibelle[$cle];
		}
		else if(substr(_request('choix_champs_'.$cle), 0, 3) == 'var' || $cptValVar == 1){
			$tabClass['var'] = $tabLibelle[$cle];
			$cptValVar--;
		}
		else if(substr(_request('choix_champs_'.$cle), 0, 3) == 'val'){
			$tabClass['val'.$i] = $tabLibelle[$cle];
			$i++;
		}
	}
	
	$echo.='<table class="spip">
			<thead>
				<tr class="row_first">';
				
				foreach($tabClass as $cle=>$valeur){
					if(substr($cle, 0, 3) == 'val'){
						$echo.= '<th scope="col" rowspan="2">'.$tabClass[$cle].'</th>';
					}
				}

	$echo.= '		<th scope="col" rowspan="2">'.$tabClass['var'].'</th>
					<th scope="col" rowspan="2">'.$tabClass['cal'].'</th>
				</tr>	
			</thead>
			<tbody>';
	
	$i=0;
	$tabAbs = array();
	
	if($cptValVar != 0){
		$ValeurVarie = $min;
	}
	else{
		$ValeurVarie = _request(substr($tabClass['var'],0,1));
	}
	
	foreach($result as $valCal){
		$i++;
		$echo.= '<tr class="';
		$echo.=($i%2==0)?'row_even':'row_odd';
		$echo.='">';
		
				foreach($tabClass as $cle=>$valeur){
					if(substr($cle, 0, 3) == 'val'){
						$echo.= '<td>';
						$decoup = explode(':', $tabClass[$cle], 2);
						$echo.= _request($decoup[0]).'</td>';
					}
				}	
				
		$echo.= '<td>'.$ValeurVarie.'</td><td>'.format_nombre($valCal, $iPrec).'</td>';		
		$echo.= '</tr>';		
		$tabAbs[] = $ValeurVarie;
		$ValeurVarie+= $pas;
	}	
	
    $echo.=	'</tbody>
        </table>';



    /***************************************************************************
    *                        Affichage du graphique
    ****************************************************************************/
  
	// Si notre tableau de résultats contient plus d'une ligne alors on l'affiche.
	if(count($result) > 1){
		$oGraph = new cGraph();
		// Ligne de Lechapt et calmon
		if(isset($result)) {
			$oGraph->AddSerie(
				'ligne_lechapt_calmon',
				$tabAbs,
				$result,
				'#00a3cd',
				'lineWidth:3, showMarker:true, markerOptions:{style:\'filledCircle\', size:8}');
		}
		// Récupération du graphique
		$echo .= $oGraph->GetGraph('ligne_lechapt_calmon',400,600);
	}
	$res['message_ok'] = $echo;
    return $res;
}
?>
