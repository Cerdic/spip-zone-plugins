<?php
/**
 * Plugin wp2spip
 * 
 * GNU/GPL v3
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de récupération des documents internes dans les textes issus de WordPress
 * 
 * Les documents dans wordpress sont en dur dans la base de donnée, on tente de les réécrire sous une 
 * forme SPIP [texte->docXX] :
 * 
 */
function action_wp2spip_documents_internes_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!autoriser('configurer', 'plugins'))
		die('erreur');
	
	$url_wordpress = sql_getfetsel('option_value','wp_options','option_name='.sql_quote('siteurl'));
	$modifies = array();
	// On va remplacer les url/wp-content/uploads/ par ->docXX
	$articles = sql_select('*','spip_articles','texte LIKE "%->'.$url_wordpress.'/wp-content/uploads/%"');
	while($article = sql_fetch($articles)){
		$texte = $article['texte'];
		$pattern = "->(".preg_quote($url_wordpress)."\/wp-content\/uploads\/(.*?))]";
		preg_match_all("|$pattern|",$texte,$matches);
		if(is_array($matches)){
			foreach($matches[1] as $i=>$id){
				if($id_document = sql_getfetsel('id_document','spip_documents','fichier='.sql_quote($id)))
					$texte = str_replace('->'.$id.']','->doc'.$id_document.']',$texte);
				else{
					include_spip('action/joindre');
					$joindre2 = charger_fonction('joindre2', 'inc');
					if($id_document = $joindre2($id, 'document', 'article', $article['id_article'], 0, $hash, $redirect, $documents_actifs, $iframe_redirect))
						$texte = str_replace('->'.$id.']','->doc'.$id_document.']',$texte);
				}
			}
			if($texte != $article['texte']){
				sql_updateq('spip_articles',array('texte'=>$texte),'id_article='.intval($article['id_article']));
				$modifies[] = $article['id_article'];
			}
		}
	}
	spip_log(count($modifies).' articles modifiés après correction des '.$url_wordpress.'/wp-content/uploads/...','wp2spip');
	spip_log($modifies,'wp2spip');
	
	$modifies = array();
	// On va remplacer les ../wp-content/uploads/ par ->docXX
	$articles = sql_select('*','spip_articles','texte LIKE "%->../wp-content/uploads/%"');
	while($article = sql_fetch($articles)){
		$texte = $article['texte'];
		$pattern = "->(\.\.\/wp-content\/uploads\/(.*?))]";
		preg_match_all("|$pattern|",$texte,$matches);
		if(is_array($matches)){
			spip_log($matches[1],'test');
			foreach($matches[1] as $i=>$id){
				$fichier = str_replace('..',$url_wordpress,$id);
				if($id_document = sql_getfetsel('id_document','spip_documents','fichier='.sql_quote($id)))
					$texte = str_replace('->'.$id.']','->doc'.$id_document.']',$texte);
				else{
					include_spip('action/joindre');
					$joindre2 = charger_fonction('joindre2', 'inc');
					if($id_document = $joindre2($fichier, 'document', 'article', $article['id_article'], 0, $hash, $redirect, $documents_actifs, $iframe_redirect))
						$texte = str_replace('->'.$id.']','->doc'.$id_document.']',$texte);
				}
			}
			if($texte != $article['texte']){
				sql_updateq('spip_articles',array('texte'=>$texte),'id_article='.intval($article['id_article']));
				$modifies[] = $article['id_article'];
			}
		}
	}
	spip_log(count($modifies).' articles modifiés après correction des '.$url_wordpress.'/wp-content/uploads/...','wp2spip');
	spip_log($modifies,'wp2spip');

}
?>