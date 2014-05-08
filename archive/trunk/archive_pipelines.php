<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Modification de la requète des objets pour ne pas afficher les archives par défaut
function archive_pre_boucle($boucle){
	if ($boucle->type_requete == 'articles') {
		$id_table = $boucle->id_table;
		
		// Si le critere {archive} ou {tout} est absent on affiche uniquement les elements non archivés
		if (!isset($boucle->modificateur['criteres']['archive']) && !isset($boucle->modificateur['tout']) && !isset($boucle->modificateur['statut'])){
			$champ_archive = $id_table.'.statut';
			$boucle->where[]= array("'!='", "'$champ_archive'", "'archive'");
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