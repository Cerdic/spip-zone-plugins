<?php

// S�curit�
if (!defined("_ECRIRE_INC_VERSION")) return;

// Determine le comportement du plugin selon la page appel�e
function archive_execution($flux) {
	//determine la page demand�e 
	switch ($flux['args']['exec']) {
		//la page articles est demand�e
		case "articles" :
			//charge les fonctions necessaire
			include_once('inc/archive_articles.php');
			$id_article = $flux['args']['id_article'];
			//recupere le complement d'affichage
			$flux['data'] .= archive_ajout_option($id_article);
			break;
		default : 
	}
	//retourne l'affichage complet
	return $flux;
}

// Modification de la requ�te des objets pour ne pas afficher les archives par d�faut
function archive_pre_boucle($boucle){
	if ($boucle->type_requete == 'articles') {
		$id_table = $boucle->id_table;
		$champ_archive = $id_table.'.archive';
		
		// Si le critere {archive} ou {tout} est absent on affiche uniquement les elements non archiv�
		if (!isset($boucle->modificateur['criteres']['archive']) &&
			!isset($boucle->modificateur['tout']))
		{
			$boucle->where[]= array("'is'", "'$champ_archive'", "'null'");
		}      
	}
	return $boucle;
}

// Lancement des taches cron pour l'archivage
function archive_taches_generales_cron($taches_generales){ 
	$taches_generales['archive_cron'] = 1*24*3600;
	return $taches_generales;
}

?>


