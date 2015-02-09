<?php

/**
 * Fonction qui recherche la presence d'un lien et de sa liaison ds la table spip_linkchecks
 * 
 * @param string $url
 * 	URL que l'on recherche
 * @param int $id_obj
 *   Identifiant de l'objet attaché
 * @param string $type
 *   Type de l'objet attaché
 * @return array
 *   tableau associatif avec une clé 'etat' 3 valeur possible 
 *   - 0 : le lien n'a pas été trouvé
 *   - 1 : le lien a été trouvé mais il n'est pas rattaché à l'objet
 * 	 - 2 : le lien a été trouvé et il est bien rattaché à l'objet
 * 	 Si la clé "etat" est égaleà 1, le tableau indique par la clé "id" l'identifiant du lien
 */

function linkcheck_tester_presence_lien($url, $id_obj, $type){
	
	$retour = array('etat'=>0);
	// on recherche le lien par l'URL
	$id_linkcheck = sql_getfetsel('id_linkcheck','spip_linkchecks', 'url='.sql_quote($url));
	if($id_linkcheck){
		// si on l'a trouvé on verifie si est attaché à l'objet passé en paramatre
		$sel = sql_getfetsel('count(id_linkcheck)','spip_linkchecks_liens', 'id_linkcheck='.$id_linkcheck.' AND id_objet='.intval($id_obj).' AND objet='.sql_quote($type));
		if( $sel == 0 ){
			$retour['etat']=1;
			$retour['id']=$id_linkcheck;
		}else{
			$retour['etat']= 2;
		}
	}
	return $retour;
}




/**
 * Fonction qui liste les liens dans un texte ou un tableau 
 *
 * @param string|array $champs
 * 		Une chaine de caractere ou un tableau de chaine de caractere
 * 		contenant les liens
 * @return array 
 * 		La liste des liens trouvé dans la variable $champs
 */

 
function linkcheck_lister_liens($champs){
	
	
	include_spip('inc/lien');
	include_spip('inc/linkcheck_vars');
	$liens = array(); //tableau des liens pour detection des liens supprimés dans l'objet
	
	if(!is_array($champs))
		$champs=array("texte" => $champs);
	$classe_alpha='a-zA-Z0-9âäéèëêïîôöùüû²';
	$tab_expreg = array(
	"('|\"| |\.|\->|\]|,|;|\s)(((((http|https|ftp|ftps)://)?www\.)|((http|https|ftp|ftps)://([a-zA-Z0-9\-]*\.)?))([a-zA-Z0-9\-]*\.)+[a-zA-Z0-9]{2,4}(/[".$classe_alpha."=.?&_\-/%#]*)?)('|\"| |\.|\->|\]|,|;|\s)?",
	"(\->)([a-zA-Z]{3,10}[0-9]{1,})\]"	);
		
	
	foreach($champs as $nom_champ=>$champ_value){
		$champ_value=trim($champ_value);
		if(!empty($champ_value)){
			$tab_temp = array();
			// trouvé les URLs
			foreach ($tab_expreg as $expreg) {
				if (preg_match_all("`".$expreg."`u", ' '.$champ_value.' ', $matches) > 0) {
					foreach($matches[2] as $m)
						if (!empty($m))
							$tab_temp[]= $m;
				}
			}
			// Ajout du prefix http:// si nessecaire
			foreach ($tab_temp as &$url_site) {
				$temp=$url_site;
				if (preg_match('#^([a-zA-Z0-9\-]*\.)([a-zA-Z0-9\-]*\.)#', $temp)) {
					$url_site = 'http://' . $url_site;
				}
			}
			 
			// Ajout au tableau du lien
			$url_site = trim($url_site, '/');
			if (!empty($tab_temp)) {
				$liens=array_unique($tab_temp);
			}
		}
	}
	return $liens;
}


/**
 * Fonction qui ajoute les liens dans la base
 *
 * @param array $tab_liens
 * 		Tableau de liens à ajouter
 * @param string $type_objet
 * 		Type de l'objet à lier
 * @param int $id_objet
 * 		Identifiant de l'objet à lier
 * 
 * @return array $ret
 */
function linkcheck_ajouter_liens($tab_liens,$type_objet,$id_objet){
	
foreach($tab_liens as $lien){
	//on test si c'est un lien interne ou externe
	$distant = (strpos($lien, '.')) ? true : false;
		
	//on test son existence dans la base
	$exi = linkcheck_tester_presence_lien($lien, $id_objet, $type_objet);

	//s'il existe
	if($exi['etat']>0){
			
		if($exi['etat'] == 1){
			//on l'ajoute ds la table de liaison
			$ins = sql_insertq('spip_linkchecks_liens', array('id_linkcheck'=>$exi['id'], 'id_objet'=>$id_objet, 'objet'=>$type_objet));
		}
			
	//s'il existe pas	
	}else{

		//on l'insere dans la base des url
		$ins = sql_insertq('spip_linkchecks', array('url'=>$lien, 'distant'=>$distant, 'date'=> date('Y-m-d H:i:s')));
			
		//et ds la base qui lie un url a un objet
		$ins2 = sql_insertq('spip_linkchecks_liens', array('id_linkcheck'=>$ins, 'id_objet'=>$id_objet, 'objet'=>$type_objet));
		
	}
}	
	
	
}





/**
 * Retourne le statut de l'url externe
 *
 * @param string $url
 * 		L'url externe à tester
 * 
 * @return array $ret
 * 		
 */
function linkcheck_tester_lien_externe($url){
	
	include_spip('inc/linkcheck_vars');
	
	$tabStatus = linkcheck_etats_liens();
	
	if(strpos($url, 'http://')===false && strpos($url, 'https://')===false) 
		$url = 'http://'.$url;

	$ret['etat'] = $tabStatus[0][4];
	$ret['code'] = 'no-code';	

	$head = @get_headers($url);
	if(!$head)
		$head = @linkcheck_get_headers($url);

	if($head){	
		
		if(preg_match('`[0-9]{3}`', $head[0], $status)){ 
			$ret['etat'] = $tabStatus[0][$status[0][0]];
			$ret['code'] = $status[0];
			}

		}

	return $ret;
	
}

function linkcheck_get_headers($url) {

       $url_info=parse_url($url);
       if (isset($url_info['scheme']) && $url_info['scheme'] == 'https') {
           $port = 443;
           @$fp=fsockopen('ssl://'.$url_info['host'], $port, $errno, $errstr, 10);
       } else {
           $port = isset($url_info['port']) ? $url_info['port'] : 80;
           @$fp=fsockopen($url_info['host'], $port, $errno, $errstr, 10);
       }
       if($fp) {
           stream_set_timeout($fp, 10);
           $head = "HEAD ".@$url_info['path']."?".@$url_info['query'];
           $head .= " HTTP/1.0\r\nHost: ".@$url_info['host']."\r\n\r\n";
           fputs($fp, $head);
           while(!feof($fp)) {
               if($header=trim(fgets($fp, 1024))) {
                       $sc_pos = strpos( $header, ':' );
                       if( $sc_pos === false ) {
                           $headers['status'] = $header;
                       } else {
                           $label = substr( $header, 0, $sc_pos );
                           $value = substr( $header, $sc_pos+1 );
                           $headers[strtolower($label)] = trim($value);
                       }
               }
           }
           return $headers;
       }
       else {
           return false;
       }
   }



/**
 * Retourne le statut de l'objet ciblé par $url
 *
 * @param string $url
 * 		L'url interne à tester
 * 
 * @return array $ret
 * 		
 */

function linkcheck_tester_lien_interne($url){
	
	include_spip('inc/lien');
	include_spip('inc/linkcheck_vars');
	
	$tabStatus = linkcheck_etats_liens();
	
	if(strpos($url, '#')===0){$ret['etat'] = $tabStatus[1]['publie']; $ret['code'] = 'publie';}
	

	$rac = typer_raccourci($url); 

	if(count($rac) && !empty($rac[0]) && !empty($rac[2])){

		$type_objet = $rac[0]; 
		$id_objet = $rac[2]; 
		$table_sql = table_objet_sql($type_objet);
		$nom_champ_id = id_table_objet($type_objet);
		$statut_objet = sql_getfetsel('statut', $table_sql, $nom_champ_id.'='.$id_objet);
		
		if(!empty($statut_objet)){
		
			if($type_objet!='auteur') { 
				$ret['etat'] = $tabStatus[1][$statut_objet]; $ret['code'] = $statut_objet;}
			else { 
				$ret['etat'] = $tabStatus[1]['publie']; $ret['code'] = 'publie';}
			
		}else {
			$ret['etat'] =  $tabStatus[1]['poubelle'];
			$ret['code'] = 'poubelle';
		}
	
	}else{
			
		$ret['etat'] = $tabStatus[1]['poubelle'];
		$ret['code'] = 'poubelle';
	}
	
	return $ret;
	
}




/**
 * Fonction de verifications des liens rescencés
 * 
 * Les liens stockés dans la table spip_linkchecks sont verifié 
 * suivant leurs statuts, ou pas, par lots, ou pas.
 *
 * @param string $etatLien
 *     L'url interne à tester
 * @param string $etatLien
 *     L'url interne à tester
 * @param string $id
 *     Identifiant d'un lien à verfier
 * 
 * @return void
 * 		
 */
function linkcheck_tests($cron=false,$etat=null, $id=0){
		
	$cpt = 0;
	include_spip('inc/config');
	$dil = lire_config('linkcheck_dernier_id_lien');
	$where =($id) ? 'id_linkcheck='.intval($id) : 'id_linkcheck > '.$dil;		
	$where .= (is_null($etat)) ? ' AND etat='.sql_quote($etat) : '';
	$limit = $cron ? 50:10;
			
	$sql = sql_select( '*','spip_linkchecks',$where,'','etat, id_linkcheck ASC','0,'.$limit);
				
	while($res = sql_fetch($sql)){
			
			linkcheck_maj_etat($res);
			ecrire_meta('linkcheck_dernier_id_lien', $res['id_linkcheck']);
			$cpt++;
				
		if($cpt>$limit){
			break;
		}
	}
	ecrire_meta('linkcheck_dernier_id_lien', 0);	
	
			
}


/**
 * Fonction qui parcours les liens d'un objet afin de les insérer ds la base et qui peux retourner un tableau des liens
 *
 * @param array $flux
 * @param array $ret
 */


function linkcheck_maj_etat($res){
	
	//si le champ est inférieur à 6
	if($res['essais']<6){
		//on incrémente le champ
		sql_updateq('spip_linkchecks', array('essais' => $res['essais']++), 'id_linkcheck='.$res['id_linkcheck']);
		$test = ($res['distant']) ? linkcheck_tester_lien_externe($res['url']) : linkcheck_tester_lien_interne($res['url']);
		
	}else{
		//on abandonne les essais
		include_spip('inc/linkcheck_vars');
		$tabStatus = linkcheck_etats_liens();
		
		$test['etat'] = $tabStatus[0][1];
		$test['code'] = '119';
		
	}
	//on met le champ à 0
	sql_updateq('spip_linkchecks', array('essais' => 0), 'id_linkcheck='.$res['id_linkcheck']);
	//sinon on le signale comme malade
	sql_updateq('spip_linkchecks', array('etat' => $test['etat'], 'code' => $test['code'], 'maj' => date('Y-m-d H:i:s')), 'id_linkcheck='.$res['id_linkcheck']);
	
}



?>
