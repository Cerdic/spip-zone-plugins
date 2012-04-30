<?php
/**
 *      @file formulaires/calcul_ouvrages.php
 */

/*      Copyright 2012 Médéric Dulondel
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

function numLoi(){

	$numLoi = array(
		1 => 'Déversoir/Orifice Cemagref 88',
		2 => 'Vanne de fond/Seuil Cemagref 88',
		3 => 'Seuil dénoyé',	
		4 => 'Seuil noyé',
		5 => 'Vanne dénoyé',
		6 => 'Vanne noyé',
		7 => 'Cunge 1980',
		8 => 'Déversoir/Orifice Cemagref 02',
		9 => 'Vanne de fond/Seuil Cemagref 02'
	);
	
	return $numLoi;
}

function champsLib(){
	
	$libVar = array(
		'L'  => array('largeur', 2.5),
		'W'  => array('ouverture', 0.56),
		'C'  => array('coeffDebit', 0.25),
		'LF' => array('largeur_fond', 2.5),
		'CR' => array('cDebitRect', 0.5),
		'CT' => array('cDebitTria', 0.5),
		'F'  => array('fruit', 0.56)
	);
	
	return $libVar;
}

function mes_saisies_surverse(){
	$num_equation = array(1, 2, 3, 4, 7);
	$numLoi = numLoi();
	$surverse = array(
		'hpelle'    => array('haut_pelle', 5),
		'cdebit'    => array('coeffDebit', 0.25)	
	);
	
	foreach($num_equation as $valeur){
		$surverse['loi_debit'][] = array($valeur, $numLoi[$valeur]);
	}

	return $surverse;
}

function mes_saisies_caract_fixe(){
	$caract_fixe = array(
		array(
			'caract_globale',
             array(
					'rQ'	=>array('debit', 1.2),
                    'cAm'   =>array('cote_amont', 2.1),
                    'cAv'   =>array('cote_aval', 2.1),                         
                  )
        ),
        
        array(
			'param_calcul',
             array(
					'rPrec' =>array('precision', 0.001)                                         
                  )
        )
	);
	
	return $caract_fixe;	
}

function mes_saisies_ouvrages(){
	$numLoi = numLoi();
	$champsLib = champsLib();
	$mes_saisies = array(
		'vRect' => array(
			'vanne_rect',
            array( 'L', 'W', 'C'),
            array(1, 2, 5, 6, 7),
            true
		),
  
		'vCirc' => array(
			'vanne_circ',
            array( 'L', 'W', 'C'),
            array(1, 2, 5, 6, 7),
            true
		),
		
		'sRect' => array(
			'seuil_rect',
            array( 'L', 'C'),
            array(1, 2, 3, 4, 7),
            false
		),
		
		'sTrap' => array(
			'seuil_trap',
            array( 'LF', 'F', 'CR', 'CT'),
            array(8,9),
            false
		),
		
		'vTrap' => array(
			'vanne_trap',
            array( 'LF', 'F', 'CR', 'CT'),
            array(8,9),
            true
		),
	);
		
	$mes_saisies_ouv = array();
	
	foreach($mes_saisies as $cleF=>$valeurTab){
		$mes_saisies_ouv[$cleF][0] = $valeurTab[0];
		foreach($valeurTab[1] as $valeur){
			$mes_saisies_ouv[$cleF][1][$valeur]= $champsLib[$valeur];	
		}
		foreach($valeurTab[2] as $valeur){
			$mes_saisies_ouv[$cleF][2][]= array($valeur, $numLoi[$valeur]);	
		}
		$mes_saisies_ouv[$cleF][3] = $valeurTab[3];
	}
	return $mes_saisies_ouv;
	
}

function formulaires_calcul_ouvrages_charger_dist() {
    // On charge les saisies et les champs qui nécessitent un accès par les fonctions
    $choixOuv = mes_saisies_ouvrages();
    $champsLib = champsLib();
    $numLoi = numLoi();
    $champs_surverse = mes_saisies_surverse();
    $caract_fixes = mes_saisies_caract_fixe();
    $valeurs = array(
        'choixOuvrage' => 'vRect',
        'choixEquation'=> 1,
        'equatSurverse' => 1,
        'afficher_surverse' => _request('afficher_surverse'),
        'mes_saisies'  => $choixOuv,
        'saisie_equat' => $champsLib,
        'numLoi'       => $numLoi,
        'surverse'     => $champs_surverse,     
        'caract_fixes' => $caract_fixes
    );
	
	foreach($choixOuv as $cleF=>$valeurF){
		$cpt = 0;
		foreach($valeurF[1] as $cle=>$valeur){
			if($cpt == 0){
				$valeurs['choix_champs_'.$cle] = 'calcul_val_'.$cle;
				$cpt++;
			}
			else{
				$valeurs['choix_champs_'.$cle] = 'val_fixe_'.$cle;
			}	
			$valeurs[$cle] = $valeur[1];
			$valeurs['val_min_'.$cle] = 1;
			$valeurs['val_max_'.$cle] = 2;
			$valeurs['pas_var_'.$cle] = 0.1;
		}	
	}
	
	foreach($champs_surverse as $cle=>$valeur){
		if($cle !== 'loi_debit'){
			$valeurs[$cle] = $valeur[1];
			$valeurs['choix_champs_'.$cle] = 'val_fixe_'.$cle;
			$valeurs['val_min_'.$cle] = 1;
			$valeurs['val_max_'.$cle] = 2;
			$valeurs['pas_var_'.$cle] = 0.1;
		}
	}
	
	foreach($caract_fixes as $cleF=>$valeurF){
		foreach($valeurF[1] as $cle=>$valeur){
			$valeurs[$cle] = $valeur[1];
			$valeurs['choix_champs_'.$cle] = 'val_fixe_'.$cle;
			$valeurs['val_min_'.$cle] = 1;
			$valeurs['val_max_'.$cle] = 2;
			$valeurs['pas_var_'.$cle] = 0.1;
		}		
	}
    return $valeurs;
}

?>

