<?php 
include_spip('inc/config');
function formulaires_melusine_boutons_charger(){
	$valeurs = array('pos1','pos2','pos3','pos4','pos5','pos6');
	
	return $valeurs;
}

function formulaires_melusine_boutons_verifier(){
	$erreurs = array();
	// verifier que les champs obligatoires sont bien la :
	//foreach(array('pos1','pos2','pos3','pos4','pos5') as $obligatoire)
	//	if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
	
	// verifier que si un email a été saisi, il est bien valide :
	//include_spip('inc/filtres');
	//if (_request('email') AND !email_valide(_request('email')))
	//	$erreurs['email'] = 'Cet email n\'est pas valide';

	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
	return $erreurs;
}

function melusine_rassembler_boutons($i){
	$tab=array(intit,add,blanck,emplacement,boutonimage);

	foreach($tab as $value){
		$chemin='melusine_boutons/'.$i.'/'.$value;
		$j=$i+1;
		$chemin_bas='melusine_boutons/'.$j.'/'.$value;		
		$pos_bas=lire_config($chemin_bas);
		ecrire_config($chemin,$pos_bas);
		
	}
	$chemin_old='melusine_boutons/'.$j;
	effacer_config($chemin_old);
	$k=$j+1;
	$test='melusine_boutons/'.$k;
	if(lire_config($test)){melusine_rassembler_boutons($j);};
	
}


function formulaires_melusine_boutons_traiter(){
	$req=_request('ok');
	$cle=substr($req,2);
	$ok=substr($req,0,2);
	$nb=lire_config('melusine_boutons');
	$total_boutons=count($nb);
	$tab=array(intit,add,blanck,emplacement,boutonimage,alt);
	if($ok==ok){	
		$tab_post=array(intit,add,blanck,emplacement,alt);
		foreach($tab_post as $value){
			${$value}=$value.$cle;		
			$r=_request(${$value});
			$chemin='melusine_boutons/'.$cle.'/'.$value;
			ecrire_config($chemin, $r);			
		}
		$nf="boutonimage".$cle;
		if($_FILES[$nf]['tmp_name']){
			$chemin='melusine_boutons/'.$cle.'/boutonimage';
			$nom_fichier= $_FILES[$nf]['tmp_name'];
			if(strpos($_SERVER['REQUEST_URI'],"/ecrire/")){$vers="../";}
			else{$vers="";};
			$chemin_destination_boutons=$vers."IMG/config/boutons";
			$chemin_destination_config=$vers."IMG/config";
			$nom_destination=$vers.'IMG/config/boutons/'.$_FILES[$nf]['name'];
			$nom_destination0='IMG/config/boutons/'.$_FILES[$nf]['name'];
			if(!is_dir("$chemin_destination_boutons")){
				if(!is_dir($chemin_destination_config)){
					mkdir($chemin_destination_config,0777);
				}
				mkdir($chemin_destination_boutons,0777);
			};
			move_uploaded_file($nom_fichier, $nom_destination); 
			ecrire_config($chemin,$nom_destination0);
		}
	
	}

	
	$position=_request('position');
	$action=substr($position,0,1);
	$i=substr($position,1);
	$var="pos".$i;
	if($action=="d" && $i<$total_boutons){
		$j=$i+1;
		foreach($tab as $value){
			$chemin='melusine_boutons/'.$i.'/'.$value;
			$chemin_bas='melusine_boutons/'.$j.'/'.$value;
			$pos=lire_config($chemin);
			$pos_bas=lire_config($chemin_bas);
			ecrire_config($chemin_bas, $pos);
			ecrire_config($chemin,$pos_bas);	
		}		
	}
	if($action=="m" && $i>1 ){
		$j=$i-1;		
		foreach($tab as $value){
			$chemin='melusine_boutons/'.$i.'/'.$value;
			$chemin_haut='melusine_boutons/'.$j.'/'.$value;
			$pos=lire_config($chemin);
			$pos_haut=lire_config($chemin_haut);
			ecrire_config($chemin_haut, $pos);
			ecrire_config($chemin,$pos_haut);	
		}		
	}
	if($action=="s"){
		$chemin='melusine_boutons/'.$i;
		effacer_config($chemin); 
		$j=$i+1;
		$chemin_bas='melusine_boutons/'.$j;
		if(lire_config($chemin_bas)){melusine_rassembler_boutons($i);}
		
	}

	if($action=="a"){
		$cle_nv=$total_boutons+1;
		$chemin='melusine_boutons/'.$cle_nv.'/intit';
		ecrire_config($chemin,'Nouveau');					
	}

	if($action=="i"){
		effacer_config('melusine_boutons');
		$bouton1=array("Education nationale","http://www.education.gouv.fr/","blanck","bandeau","IMG/config/boutons/image4.png","Education nationale");
		$clebouton=array("intit","add","blanck","emplacement","boutonimage","alt");
		for ($i=0;$i<5;$i++){
			
			$chemin='melusine_boutons/1/'.$clebouton[$i];
			
			ecrire_config($chemin,$bouton1[$i]);
		}			
	}
	
	return array('message_ok'=>'enregistré');
}



?>