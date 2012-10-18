<?php 
include_spip('inc/config');
function formulaires_melusine_nuage_charger(){
	$valeurs = array();
	return $valeurs;
}

function formulaires_melusine_nuage_verifier(){
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





function formulaires_melusine_nuage_traiter(){
	effacer_config(melusine_nuage);
	$couleur=_request('couleur');	
	$chemin_couleur="melusine_nuage/couleur";
	ecrire_config($chemin_couleur,$couleur);
	$mots=_request('mot',$tableau);
	$texte="<tags>\n";
	if($mots){
		foreach ($mots as $value){
			$tab_mot=explode(" ",$value[titre]);
			$titre=str_replace($tab_mot[0],"",$value[titre]);
			$pattern="/^( [0-9])\. /";
			$titre=preg_replace($pattern,"",$titre);
			$chemin_id="melusine_nuage/mot/".$tab_mot[0]."/id";
			$chemin_tit="melusine_nuage/mot/".$tab_mot[0]."/titre";
			$chemin_taille="melusine_nuage/mot/".$tab_mot[0]."/taille";
			if ($titre){
				ecrire_config($chemin_id,$tab_mot[0]);
				ecrire_config($chemin_tit,$titre);
				ecrire_config($chemin_taille,$value[taille]);
				$couleur_mot=str_replace("#","0x",$couleur);
				$texte.="<a href='spip.php?page=mot&id_mot=".$tab_mot[0]."'  rel='tag' style='font-size:".$value[taille]."px;' color='".$couleur_mot."' >".$titre."</a>\n";
			}
		
		};
	};
	$articles=_request('article',$tableau2);
	if($articles){
		foreach ($articles as $value){
			$tab_article=explode(" ",$value[titre]);
			$titre=str_replace($tab_article[0],"",$value[titre]);
			$pattern="/^( [0-9])\. /";
			$titre=preg_replace($pattern,"",$titre);
			$chemin_id="melusine_nuage/article/".$tab_article[0]."/id";
			$chemin_tit="melusine_nuage/article/".$tab_article[0]."/titre";
			$chemin_taille="melusine_nuage/article/".$tab_article[0]."/taille";
			if ($titre){
				ecrire_config($chemin_id,$tab_article[0]);
				ecrire_config($chemin_tit,$titre);
				ecrire_config($chemin_taille,$value[taille]);
				$couleur_article=str_replace("#","0x",$couleur);
				$texte.="<a href='spip.php?page=article&id_article=".$tab_article[0]."'  rel='tag' style='font-size:".$value[taille]."px;' color='".$couleur_article."' >".$titre."</a>\n";
			}
		};
	};
	if(strpos($_SERVER['SCRIPT_FILENAME'],"/ecrire/")){$chem="../";}
	else{$chem="";};
	$chemin_nuage=$chem."IMG/nuage.txt";
	$texte.="</tags>";
	unlink($chemin_nuage);
	$file=fopen($chemin_nuage,"a+");
	fwrite($file,$texte);
	fclose($file);
	return false;	
	
}



?>