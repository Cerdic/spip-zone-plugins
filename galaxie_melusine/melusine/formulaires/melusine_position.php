<?php 
include_spip('inc/config');

function formulaires_melusine_position_charger(){
	$valeurs = array('d1'=>'menu','d2'=>'boutonsgauche','d3'=>'miseajour','d4'=>'mentionslegales','d5'=>'diaporama');
	melusine_liste_noisettes_dispo();
	return $valeurs;
}

function formulaires_melusine_position_verifier(){
	$erreurs = array();
	// verifier que les champs obligatoires sont bien la :
	//foreach(array('pos1','pos2','pos3','pos4','pos5') as $obligatoire)
	//	if (_request($style)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
	
	// verifier que si un email a été saisi, il est bien valide :
	//include_spip('inc/filtres');
	//if (_request('email') AND !email_valide(_request('email')))
	//	$erreurs['email'] = 'Cet email n\'est pas valide';

	$style=_request('style');
	$gauche=lire_config("melusine_squelettes/g");
	
	$droite=lire_config("melusine_squelettes/d");
	if($style=="layout2.css" and melusine_colonne_pasvide($droite) ){$erreurs['style'] ="<span style='color:red'>La colonne droite  doit &ecirc;tre vide</span>"	;};
	if($style=="layout3.css" and melusine_colonne_pasvide($gauche) ){$erreurs['style'] ="<span style='color:red'>La colonne gauche doit &ecirc;tre vide</span>"	;};
	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
	return $erreurs;
}


function formulaires_melusine_position_traiter(){
	$style=_request('style');
	ecrire_config("melusine_squelettes/style",$style);	
	$position=_request('position');
	if($position=='skels'){melusine_liste_noisettes_dispo();return false;}
	$action=substr($position,0,1);
	$zone=substr($position,1,1);
	$var=substr($position,2);
	
	if($action=="d" && $var<11 ){
		$varplus=$var+1;
		$chemin='melusine_squelettes/'.$zone.'/'.$var;
		$chemin_bas='melusine_squelettes/'.$zone.'/'.$varplus;
		$pos=lire_config($chemin);
		$pos_bas=lire_config($chemin_bas);
		ecrire_config($chemin_bas, $pos);
		ecrire_config($chemin,$pos_bas);			
	}
	if($action=="m" && $var>1 ){
		$varmoins=$var-1;
		$chemin='melusine_squelettes/'.$zone.'/'.$var;
		$chemin_haut='melusine_squelettes/'.$zone.'/'.$varmoins;
		$pos=lire_config($chemin);
		$pos_haut=lire_config($chemin_haut);
		ecrire_config($chemin_haut, $pos);
		ecrire_config($chemin,$pos_haut);			
	}
	if($action=="s"){
		$chemin='melusine_squelettes/'.$zone.'/'.$var;
		ecrire_config($chemin,'aucun');	
		melusine_rassembler($var,"squelettes",$zone);
	}
	if($action=="a"){
		$j=1;
		while($valeur!='aucun'){
		$chemin='melusine_squelettes/'.$zone.'/'.$j;
		$valeur=lire_config($chemin);
		$j++;}
		if($j<11){
		ecrire_config($chemin,$var);
		}
			
	}
	if($action=="i"){
		$skelg=array('menu','boutons_gauche','diaporama','mise_a_jour','mentions_legales','aucun');
		$skeld=array('formulaire_recherche','boutons_droite','logo','breves','sites_favoris','aucun');
		for ($i=1;$i<7;$i++){
			$j=$i-1;
			$chemin='melusine_squelettes/'.$zone.'/'.$i;
			//effacer_config($chemin);
			if($zone=="g"){$skel=$skelg;}
			else{$skel=$skeld;};
			ecrire_config($chemin,$skel[$j]);
		}			
	}
	return array('message_ok'=>'enregistré');
}



?>