<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de lancement d'encodage 
 * Il est préférable de l'appeler via le Cron pour éviter de bloquer le processus de l'utilisateur
 * 
 * Consulte la file d'attente pour savoir si des documents sont à encoder.
 * Vérifie en amont que la meta "spipmotion_casse" ne soit pas à "oui", si elle l'est
 * aucun encodage n'est lancé
 *
 * S'il existe au moins un document à encoder on lance le premier
 * Si ce document original n'existe plus, on supprime ses occurences dans la file d'attente
 * et on relance la même fonction
 * 
 */
function action_spipmotion_encoder(){
	if(($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) && (_request('action') != 'forcer_job')){
		spip_log('Echec : Appel de spipmotion_encoder depuis '.$_SERVER['REMOTE_ADDR'],'spipmotion');
		include_spip('inc/minipres');
	    echo minipres();
	    exit;
	}
	include_spip('inc/genie');
	genie_queue_watch_dist();
	spip_log('genie_queue_watch','spipmotion');
	$nb_encodages = sql_countsel('spip_spipmotion_attentes', "encode='non'");
	spip_log('Appel de la fonction d encodage','spipmotion');
	spip_log("Il y a $nb_encodages vidéo(s) à encoder","spipmotion");
	$en_cours = sql_fetsel('id_spipmotion_attente,maj','spip_spipmotion_attentes',"encode='en_cours'");
	
	/**
	 * On essaie de voir s'il y a d'autres processus ffmpeg en cours sur le serveur (autres sites?)
	 */
	$ps_ffmpeg = exec('ps -e |grep ffmpeg',$retour,$retour_int);
	if(($retour_int == 1) && (count($retour) >= 3)){
		$process = false;
	}else{
		$process = true;
	}
	if(($nb_encodages>0) && $process && ($GLOBALS['meta']['spipmotion_casse'] != 'oui') && !intval($en_cours['id_spipmotion_attente'])){
		$doc_attente = sql_fetsel("*","spip_spipmotion_attentes","encode='non'","","id_spipmotion_attente ASC","1");
		$id_document = $doc_attente['id_document'];
		$id_doc_attente = $doc_attente['id_spipmotion_attente'];
		$format = $doc_attente['extension'];
		$document = sql_fetsel('*','spip_documents','id_document='.sql_quote($id_document));
		if($document['id_document']){
			spip_log('on encode le doc '.$id_document,'spipmotion');
			$encoder = charger_fonction('encodage','inc');
			$encoder($document,$id_doc_attente,$format);
		}else{
			sql_delete('spip_spipmotion_attentes','id_document='.sql_quote($id_document));
			genie_spipmotion_file($time);
		}
	}else if(lire_config('spipmotion_casse') == 'oui'){
		spip_log('Attention, problème dans la configuration','spipmotion');
	}else if(intval($en_cours['id_spipmotion_attente']) && ($en_cours['maj'] < date('Y-m-d H:i:s',mktime(date('H')-5)))){
		/**
		 * Il est peut être nécessaire de vérifier qu'un processus n'est pas en cours?
		 */
		spip_log("L'id". $en_cours['id_spipmotion_attente']." de la file d'attente est en cours d'encodage depuis plus de 5 h (".$en_cours['maj']."), on doit le réinitialiser",'spipmotion');
		sql_updateq('spip_spipmotion_attentes',array('encode' => 'non'),'id_spipmotion_attente ='.intval($en_cours['id_spipmotion_attente']));
	}else if(intval($en_cours['id_spipmotion_attente'])){
		spip_log("L'id". $en_cours['id_spipmotion_attente']." de la file d'attente est en cours d'encodage",'spipmotion');
		spip_log("On attend sa fin avant d'en commencer un nouveau",'spipmotion');
	}else if(!$process){
		spip_log("Trop de processus en cours de ffmpeg sur le serveur","spipmotion");
	}
	
	return;
}