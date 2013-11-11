<?php
/**
 * Plugin wp2spip
 * 
 * GNU/GPL v3
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de récupération des liens internes dans les textes issus de WordPress
 * 
 * Les liens dans wordpress sont en dur dans la base de donnée, on tente de les réécrire sous une 
 * forme SPIP [texte->artXX] :
 * 
 * - Les ../?page_id=XX
 * - Les /?page_id=XX
 * - Les ?page_id=XX
 * - Les http://url/?page_id=XX
 * - Les http://url/?p=XX
 * 
 * Seuls ceux dont le XX est existant sont modifiés
 */
function action_wp2spip_liens_internes_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!autoriser('configurer', 'plugins'))
		die('erreur');
	
	$modifies = array();
	// On va remplacer les ../?page_id=XX par ->artXX
	$articles = sql_select('*','spip_articles','texte LIKE "%->../?page_id=%"');
	while($article = sql_fetch($articles)){
		$texte = $article['texte'];
		preg_match_all("/->\.\.\/\?page_id=(\d*)]/Uims",$texte,$matches);
		foreach($matches[1] as $i=>$id){
			if($id_article = sql_getfetsel('id_article','spip_articles','id_article='.intval($id)))
				$texte = str_replace('->../?page_id='.$id.']','->art'.$id.']',$texte);
		}
		if($texte != $article['texte']){
			sql_updateq('spip_articles',array('texte'=>$texte),'id_article='.intval($article['id_article']));
			$modifies[] = $article['id_article'];
		}
	}
	spip_log(count($modifies).' articles modifiés après correction des ../?page_id=...','wp2spip');
	spip_log($modifies,'wp2spip');
	
	$modifies = array();
	// On va remplacer les ->/?page_id=XX par ->artXX
	$articles = sql_select('*','spip_articles','texte LIKE "%->/?page_id=%"');
	while($article = sql_fetch($articles)){
		$texte = $article['texte'];
		preg_match_all("/->\/\?page_id=(\d*)]/Uims",$texte,$matches);
		foreach($matches[1] as $i=>$id){
			if($id_article = sql_getfetsel('id_article','spip_articles','id_article='.intval($id)))
				$texte = str_replace('->/?page_id='.$id.']','->art'.$id.']',$texte);
		}
		if($texte != $article['texte']){
			sql_updateq('spip_articles',array('texte'=>$texte),'id_article='.intval($article['id_article']));
			$modifies[] = $article['id_article'];
		}
	}
	spip_log(count($modifies).' articles modifiés après correction des /?page_id=...','wp2spip');
	spip_log($modifies,'wp2spip');
	
	$url_wordpress = sql_getfetsel('option_value','wp_options','option_name='.sql_quote('siteurl'));
	$url = preg_quote($url_wordpress);
	$modifies = array();
	// On va remplacer les url/?page_id=XX par ->artXX
	$articles = sql_select('*','spip_articles','texte LIKE "%->'.$url_wordpress.'/?page_id=%"');
	while($article = sql_fetch($articles)){
		$texte = $article['texte'];
		$pattern = "->".preg_quote($url_wordpress)."\/\?page_id=(\d*)]";
		preg_match_all("|$pattern|",$texte,$matches);
		if(is_array($matches)){
			foreach($matches[1] as $i=>$id){
				if($id_article = sql_getfetsel('id_article','spip_articles','id_article='.intval($id)))
					$texte = str_replace('->'.$url_wordpress.'/?page_id='.$id.']','->art'.$id.']',$texte);
			}
			if($texte != $article['texte']){
				sql_updateq('spip_articles',array('texte'=>$texte),'id_article='.intval($article['id_article']));
				$modifies[] = $article['id_article'];
			}
		}
	}
	spip_log(count($modifies).' articles modifiés après correction des '.$url_wordpress.'/?page_id=...','wp2spip');
	spip_log($modifies,'wp2spip');
	
	$modifies = array();
	// On va remplacer les url/?p=XX par ->artXX
	$articles = sql_select('*','spip_articles','texte LIKE "%->'.$url_wordpress.'/?p=%"');
	while($article = sql_fetch($articles)){
		$texte = $article['texte'];
		$pattern = "->".preg_quote($url_wordpress)."\/\?p=(\d*)]";
		preg_match_all("|$pattern|",$texte,$matches);
		if(is_array($matches)){
			foreach($matches[1] as $i=>$id){
				if($id_article = sql_getfetsel('id_article','spip_articles','id_article='.intval($id)))
					$texte = str_replace('->'.$url_wordpress.'/?p='.$id.']','->art'.$id.']',$texte);
			}
			if($texte != $article['texte']){
				sql_updateq('spip_articles',array('texte'=>$texte),'id_article='.intval($article['id_article']));
				$modifies[] = $article['id_article'];
			}
		}
	}
	spip_log(count($modifies).' articles modifiés après correction des '.$url_wordpress.'/?p=...','wp2spip');
	spip_log($modifies,'wp2spip');
	
	$modifies = array();
	// On va remplacer les ?page_id=XX par ->artXX
	$articles = sql_select('*','spip_articles','texte LIKE "%->?page_id=%"');
	while($article = sql_fetch($articles)){
		$texte = $article['texte'];
		preg_match_all("/->\?page_id=(\d*)]/Uims",$texte,$matches);
		if(is_array($matches)){
			foreach($matches[1] as $i=>$id){
				if($id_article = sql_getfetsel('id_article','spip_articles','id_article='.intval($id)))
					$texte = str_replace('->?page_id='.$id.']','->art'.$id.']',$texte);
			}
			if($texte != $article['texte']){
				sql_updateq('spip_articles',array('texte'=>$texte),'id_article='.intval($article['id_article']));
				$modifies[] = $article['id_article'];
			}
		}
	}
	spip_log(count($modifies).' articles modifiés après correction des ?page_id=...','wp2spip');
	spip_log($modifies,'wp2spip');
}
?>