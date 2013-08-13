<?php


if (!defined('_ECRIRE_INC_VERSION')) return;

## complètement pompé de plugins/sites/genie/syndic.php
## valeurs modifiables dans mes_options
## attention il est tres mal vu de prendre une periode < 20 minutes
if (!defined('_PERIODE_SYNCHRONISATION'))
	define('_PERIODE_SYNCHRONISATION', 24*60);
if (!defined('_PERIODE_SYNCHRONISATION_SUSPENDUE'))
	define('_PERIODE_SYNCHRONISATION_SUSPENDUE', 48*60);

genie_synchro_dist($t){
	return executer_une_synchronisation();
}


function executer_une_synchronisation() {
	$id_synchro = '';
	// inserer la tache dans la file, avec controle d'unicite
	job_queue_add('synchro_a_jour','synchro_a_jour',array($id_synchro),'genie/syndic',true);
}

function synchro_a_jour(){


}

function inserer_dans_la_base(){}


// récupérer url fichier distant
// parser le fichier distant

// pour chaque événement du fichier distant
// 	récupération uid_distant

// 		pour chaque enregistrement déjà présent dans la base
// 		si uid_distant = uid_local alors
// 			récupérer sequence_distant
// 			si sequence_distant != sequence_local alors 
// 				supprimer l événement local et le remplacer par l événement distant
// 		sinon insérer l événement distant dans la base 



?>