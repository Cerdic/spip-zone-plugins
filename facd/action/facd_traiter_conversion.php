<?php
/**
 * Action qui lance une conversion
 *
 * @plugin FACD pour SPIP
 * @author b_b
 * @author kent1 (http://www.kent1.info - kent1@arscenic.info)
 * @license GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction de lancement de conversion 
 * Il est préférable de l'appeler via le Cron pour éviter de bloquer le processus de l'utilisateur
 * 
 * Consulte la file d'attente pour savoir si des documents sont à convertir.
 *
 * S'il existe au moins un document à convertir on lance le premier
 * Si ce document original n'existe plus, on supprime ses occurences dans la file d'attente
 * et on relance la même fonction
 * 
 */
function action_facd_traiter_conversion_dist(){
	$nb_conversions = sql_countsel('spip_facd_conversions', "statut='non'");
	spip_log("Appel de la fonction de conversion : il y a $nb_conversions document(s) à convertir","facd");
	$en_cours = sql_fetsel('id_facd_conversion,maj','spip_facd_conversions',"statut='en_cours'");
	
	/**
	 * On essaie de voir s'il y a d'autres conversions en cours
	 */
	if(($nb_conversions > 0) && !intval($en_cours['id_facd_conversion'])){
		$doc_attente = sql_fetsel("*","spip_facd_conversions","statut='non'","","id_facd_conversion ASC","1");
		/**
		 * Vérification de l'exisence du document
		 */
		$id_document = sql_getfetsel('id_document','spip_documents','id_document='.intval($doc_attente['id_document']));
		if(intval($id_document) > 0){
			spip_log("on convertit le doc $id_document","facd");
			$convertir = charger_fonction('facd_convertir','inc');
			$retour = $convertir($id_document,$doc_attente['id_facd_conversion'],$doc_attente['extension']);
		}else{
			sql_delete('spip_facd_conversions','id_document='.sql_quote($doc_attente['id_document']));
		}
	}else if(intval($en_cours['id_facd_conversion']) && ($en_cours['maj'] < date('Y-m-d H:i:s',mktime(date('H')-5)))){
		spip_log("L'id". $en_cours['id_facd_conversion']." de la file d'attente est en cours de conversion depuis plus de 5 h (".$en_cours['maj']."), on le réinitialise, cela doit être une erreur","facd");
		sql_updateq('spip_facd_conversions',array('statut' => 'non'),'id_facd_conversion ='.intval($en_cours['id_facd_conversion']));
	}else if(intval($en_cours['id_facd_conversion'])){
		spip_log("L'id ". $en_cours['id_facd_conversion']." de la file d'attente est en cours de conversion","facd");
		spip_log("On attend sa fin avant d'en commencer un nouveau","facd");
	}
	return;
}