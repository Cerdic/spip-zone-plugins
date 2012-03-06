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
	$tChOblig = array('L', 'M', 'N', 'Q', 'D', 'J');
	$choix_champs = array(
		'Q' => _request('choix_champs_Q'),
		'D' => _request('choix_champs_D'),
		'J' => _request('choix_champs_J')
	);

	foreach($choix_champs as $cle=>$valeur){
		if(substr($valeur, 0,3) != 'val'){
			foreach($tChOblig as $cle1=>$valeur1){
				if($cle == $valeur1){
					unset($tChOblig[$cle1]);
					$tChOblig = array_values($tChOblig);
				}
			}
		}
		
		if(substr($valeur, 0, 3) == 'var'){
			$tChOblig[] = 'val_min_'.$cle;
			$tChOblig[] = 'val_max_'.$cle;
			$tChOblig[] = 'pas_var_'.$cle;
		}	
	}
	return $tChOblig;
}

function formulaires_lechapt_calmon_charger_dist() { 
    // On charge les saisies et les champs qui nécessitent un accès par les fonctions
	$tSaisie_materiau = mes_saisies_materiau();
	$valeurs = array(
		'mes_saisies_materiaux' => $tSaisie_materiau,
		'typeMateriau' => 1,
		'choix_champs_Q' => 'calcul_val_Q',
		'choix_champs_D' => 'val_fixe_D',
		'choix_champs_J' => 'val_fixe_J',
		'L' => 1.863,
		'M' => 2,
		'N' => 5.33,
		'Q' => 1,
		'val_min_Q' => 1,
		'val_max_Q' => 2,
		'pas_var_Q' => 3,
		'val_min_D' => 1,
		'val_max_D' => 2,
		'pas_var_D' => 3,	
		'val_min_J' => 1,
		'val_max_J' => 2,
		'pas_var_J' => 3,
		'D' => 2,
		'J' => 3	
	);
  
    return $valeurs;
}

function formulaires_lechapt_calmon_verifier_dist(){
    $erreurs = array();
    $datas = array();
    $tChOblig= champs_obligatoires_lcalmon();
    // verifier que les champs obligatoires sont bien là :
    foreach($tChOblig as $obligatoire) {
        if (!_request($obligatoire)) {
            $erreurs[$obligatoire] = _T('hydraulic:champ_obligatoire');}
        else {
            $datas[$obligatoire] = _request($obligatoire);
        }
    }

    foreach($datas as $champ=>$data) {
        if ($data < 0) $erreurs[$champ] = _T('hydraulic:valeur_positive');
    }

    if (count($erreurs)) {
        $erreurs['message_erreur'] = _T('hydraulic:saisie_erreur');
    }
    return $erreurs;
}

function formulaires_lechapt_calmon_traiter_dist(){
	
	$datas = champs_obligatoires_lcalmon();
	$datas[] = _request('choix_champs_Q');
	$datas[] = _request('choix_champs_D');
	$datas[] = _request('choix_champs_J');
	$CalVal = '';
	
	foreach($datas as $i){
		if(substr($i, 0, 3) == 'cal'){
			$CalVal = substr($i, -1);
		}
	}
	
	switch($CalVal){
		case 'Q':
			
		break;
		
	}
	
	print_r($datas);
}
?>
