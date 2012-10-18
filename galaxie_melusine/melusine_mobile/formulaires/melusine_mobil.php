<?php 
include_spip('inc/config');

function formulaires_melusine_mobil_charger(){
	$valeurs = array('d1'=>'menu','d2'=>'boutonsgauche','d3'=>'miseajour','d4'=>'mentionslegales','d5'=>'diaporama');
	
	return $valeurs;
}

function formulaires_melusine_mobil_verifier(){
	$erreurs = array();
	// verifier que les champs obligatoires sont bien la :
	//foreach(array('pos1','pos2','pos3','pos4','pos5') as $obligatoire)
	//	if (_request($style)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
	
	// verifier que si un email a été saisi, il est bien valide :
	//include_spip('inc/filtres');
	//if (_request('email') AND !email_valide(_request('email')))
	//	$erreurs['email'] = 'Cet email n\'est pas valide';

	
}


function formulaires_mobil_traiter(){
	$position=_request('position');
	if($position=='skels'){melusine_liste_noisettes_dispo("mobil");return false;}
	$action=substr($position,0,1);
	$var=substr($position,1);
	
	if($action=="d" && $var<11 ){
		$varplus=$var+1;
		$chemin='melusine_mobil/effectifs/'.$var;
		$chemin_bas='melusine_mobil/effectifs/'.$varplus;
		$pos=lire_config($chemin);
		$pos_bas=lire_config($chemin_bas);
		ecrire_config($chemin_bas, $pos);
		ecrire_config($chemin,$pos_bas);			
	}
	if($action=="m" && $var>1 ){
		$varmoins=$var-1;
		$chemin='melusine_mobil/effectifs/'.$var;
		$chemin_haut='melusine_mobil/effectifs/'.$varmoins;
		$pos=lire_config($chemin);
		$pos_haut=lire_config($chemin_haut);
		ecrire_config($chemin_haut, $pos);
		ecrire_config($chemin,$pos_haut);			
	}
	if($action=="s"){
		$chemin='melusine_mobil/effectifs/'.$var;
		ecrire_config($chemin,'aucun');	
		melusine_rassembler($var,"mobil");
	}
	if($action=="a"){
		$j=1;
		while($valeur!='aucun'){
		$chemin='melusine_mobil/effectifs/'.$j;
		$valeur=lire_config($chemin);
		$j++;}
		if($j<11){
		ecrire_config($chemin,$var);
		}
			
	}
	if($action=="i"){
		$skelg=array('aucun','bip');
		
		for ($i=1;$i<3;$i++){
			$j=$i-1;
			$chemin='melusine_mobil/effectifs/'.$i;					
			ecrire_config($chemin,$skelg[$j]);
		}			
	}
	
	return array('message_ok'=>'enregistré');
}



?>