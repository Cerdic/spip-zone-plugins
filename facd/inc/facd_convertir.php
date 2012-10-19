<?php
/**
 * Fonction de conversion de document
 *
 * @plugin FACD pour SPIP
 * @author b_b
 * @author kent1 (http://www.kent1.info - kent1@arscenic.info)
 * @license GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction qui lance la conversion d'un document
 * 
 * @param int $id_document
 * 	Identifiant numérique du document à convertir
 * @param int $id_facd
 * 	Identifiant numérique dans la liste d'attente
 * @param string/false $format
 * 	Le format attendu
 * @return bool $reussite
 * 	True si la conversion a réussi, false dans le cas contraire
 */
function inc_facd_convertir_dist($id_document,$id_facd,$format=false){
	/**
	 * On change le statut de conversion à en_cours pour
	 * - changer les messages sur le site (ce media est en cours de conversion par exemple)
	 * - indiquer si nécessaire le statut
	 */
	$infos = array('debut_conversion' => time());
	sql_updateq("spip_facd_conversions",array('statut'=>'en_cours','infos'=>serialize($info)),"id_facd_conversion=".intval($id_facd));

	$attente = sql_fetsel("*","spip_facd_conversions","id_facd_conversion=".intval($id_facd));
	$sortie = $attente['extension'];
	
	$options = array('format' => $attente['extension'],'id_facd_conversion'=>$id_facd);
	// chercher la fonction de conversion pour le format démandé
	if(is_array(@unserialize($attente['options']))){
		$options_table = unserialize($attente['options']);
		$options = array_merge($options,$options_table);
	}else if(strlen($attente['options'])){
		$options['options'] = $attente['options'];
	}
		
	if($f = charger_fonction($attente['fonction'],'convertir',true))
		$res = $f($id_document,$options);
	else if($f = charger_fonction($attente['fonction'],'inc',true))
		$res = $f($id_document,$options);
	elseif ($f=charger_fonction("{$entree}_{$sortie}","convertir",true))
		$res = $f($id_document,$options);
	elseif ($f=charger_fonction("{$entree}","convertir",true))
		$res = $f($id_document,$options);
	else {
		$res = false;
		$res['erreur'] = 'fonction_conversion_inexistante';
		$reussite = false;
	}
	
	$infos['fin_conversion'] = time();
	if(is_array($res)){
		if(is_array($res['infos']))
			$infos = array_merge($infos,$res['infos']);
		if($res['success']){
			/**
			 * Modification de la file d'attente : 
			 * on marque le document comme correctement converti
			 */
			sql_updateq("spip_facd_conversions",array('statut'=>'oui','infos'=>serialize($infos)),"id_facd_conversion=".intval($id_facd));
			$reussite = true;
		}else if(isset($res['erreur'])){
			/**
			 * Modification de la file d'attente : 
			 * on marque le document comme étant en erreur
			 */
			$infos['erreur'] = $res['erreur'];
			sql_updateq("spip_facd_conversions",array('statut'=>'erreur','infos'=>serialize($infos)),"id_facd_conversion=".intval($id_facd));
			$reussite = false;
		}
	}
	/**
	 * Si la conversion n'est pas ok ...
	 * On donne un statut "erreur" dans la file afin de ne pas la bloquer
	 */
	else{
		$reussite = false;
		$info['erreur'] = 'Le retour de la fonction d\'encodage n\'est pas un array';
		sql_updateq("spip_facd_conversions",array('statut'=>'erreur','infos'=>serialize($infos)),"id_facd_conversion=".intval($id_facd));
	}

	/**
	 * Invalidation du cache
	 */
	include_spip('inc/invalideur');
	suivre_invalideur("0",true);

	/**
	 * On lance un encodage direct pour éviter d'attendre le prochain cron
	 */
	$conversion_directe = charger_fonction('facd_convertir_direct','inc');
	$conversion_directe();
	
	return $reussite;
}
?>