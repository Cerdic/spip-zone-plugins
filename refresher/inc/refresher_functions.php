<?php

// function to refresh pages when SPIP object is updated
function refresh($type, $arr){
	include_spip("inc/queue");
	
	$champs = $arr["data"];
	$args = $arr["args"];
	
	$urls = array('push' => array(), 'pull' => array());
	if($type == "lien"){
		$objet_source = $args['objet_source'];
		$id_objet_source = $args['id_objet_source'];
		$objet = $args['objet'];
		$id_objet = $args['id_objet'];
		$action = $args['action'];
		if($objet == 'article') $date = article_is_published($id_objet);
		$pf = "prerefresh_lien_".$action."_".$objet_source."_".$objet;
		$f = "refresh_lien_".$action."_".$objet_source."_".$objet;
		if(function_exists($pf)) $urls = $pf($urls, $id_objet_source, $id_objet, $arr);
		if(function_exists($f)) $urls = $f($urls, $id_objet_source, $id_objet, $arr);
	}
	if($type == 'objet'){
		if(isset($args['type'])) $objet = $args['type'];
		//WARNING!!! UGLY! need to find the function that gets object type from table name
		else $objet = substr(str_replace("spip_", "", $args['table']), 0, -1);
		$id_objet = $args['id_objet'];
		$action = $args['action'];
		
		// if this is about publishing an article, we set the refresh date to the publish date
		if($action == 'instituer' && $objet == 'article'){ 
			$date = article_is_published($id_objet);
			// if action is 'instituer' but no old status given as parameter, it is a regular article fields edition
			if(!isset($args['statut_ancien'])) $action = 'modifier';
		}
		// forum posted on article : we refresh immediately - we need to find a more generic way to deal with this case
		if($args['table'] == 'spip_forum' && $objet == 'article'){
			if(lire_config('refresher/forum_insert_article') == 'on'){ 
				array_push($urls, generer_url_entite($id_objet, 'article', '', '', true));
				$date = -1;
			}
		}
		
		$pf = "prerefresh_objet_".$action."_".$objet;
		$f = "refresh_objet_".$action."_".$objet;
		if(function_exists($pf)) $urls = $pf($urls, $id_objet, $arr);
		if(function_exists($f)) $urls = $f($urls, $id_objet, $arr);
	}
	// generer_url_entite($id_article, 'article', '', '', true)
	if(sizeof($urls['push']) == 0 && sizeof($urls['pull']) == 0) return;
	
	// if any doubles we get rid of them, no need to refresh the same URL twice
	$urls['push'] = array_unique($urls['push'], SORT_REGULAR);
	$urls['pull'] = array_unique($urls['pull'], SORT_REGULAR);
	
	// if specific date we delete potential existing jobs from the same origin on other dates
	if($date > 0){ 
		$res = sql_allfetsel("id_job","spip_jobs_liens","id_objet=".$id_objet." and objet=".sql_quote($objet));
		if(is_array($res)){
			foreach ($res as $row)
				queue_remove_job($row['id_job'],0);
		}
	}
	
	// those will be used if we want to add a pause between refreshing pages
	$pause = 0;
	if(is_numeric(lire_config('refresher/refresher_delay'))) $pause = lire_config('refresher/refresher_delay');
	// if date between 0 and now, we set to now (an old past date could be a problem in job queue)
	if($date < time() && $date >= 0) $date = time();
	$sql_date = date("Y-m-d H:i:s", $date);
	
	/* first take care of pull system */
	if(sizeof($urls['pull']) > 0){
		if($date < 0){ 
			traiter_pull($urls['pull']);
		}
		// else we put task in job queue
		else{
			$id_job = job_queue_add('traiter_pull',"Pull invalidation of ".$urls['pull'][0]."|".$urls['pull'][sizeof($url)-1]." number of items: ".sizeof($urls['pull']),array($urls['pull']),"inc/refresher_functions",TRUE,$date);
			// if date in the future, we link the task to the object for a better control (if we change the date of refreshing, for example)
			job_queue_link($id_job, array('objet' => $objet, 'id_objet' => $id_objet));
		}
	}
	
	/* Now we take care of push system */
	if(sizeof($urls['push']) > 0){
		// we change objects into URIs
		foreach($urls['push'] as $urlkey => $url){
			if(strpos($url, '|') !== false){
				$obj = explode('|', $url);
				$urls['push'][$urlkey] = generer_url_entite($obj[0], $obj[1], '', '', true);
			}
		}
		
		// We will define the refresh dates according to delay defined in backend, if any.
		// there cannot be 2 refresh jobs within less than this delay.
		$res = sql_allfetsel("id_job,date,descriptif","spip_jobs","date>='".$sql_date."' and fonction=".sql_quote("refresh_url"), '', 'date');
		$date_list = array();
		$nb_urls = sizeof($urls['push']);
	  $current_date = $date - $pause;
		$i_res = 0;
		for($i = 0; $i < $nb_urls; $i++){
			if(isset($res[$i_res])){
				$skip = false;
				$next_date = strtotime($res[$i_res]['date']);
				while(($next_date - $current_date <= ($pause*2)) && isset($res[$i_res]) && !$skip){
					// if we come accross the refreshing of this exact URL then we don't need to add it again
					if($res[$i_res]['descriptif'] == "Refresh of ".$urls['push'][$i]){ 
						spip_log("skip current job  because same job found while postponing ".$urls['push'][$i], 'refresher');
						$skip = true;
					}
					$current_date = $next_date;
					$i_res ++;
					if(isset($res[$i_res])) $next_date = strtotime($res[$i_res]['date']);
				}
			}
			$current_date += $pause;
			
			// if we skip this URL because we found the same one already in job queue, we remove it from list
			if($skip){ 
				$urls['push'][$i] = null;
			}
			$date_list[] = $current_date;
		}
		
		// now we are ready to insert our jobs in job_queue
		$i_date_list = 0;
		foreach($urls['push'] as $url){
			if($url !== null){
				spip_log("about to send to job_queue:".$url, 'refresher');
				// if instant refresh needed, we call function now
				if($date < 0) refresh_url($url);
				// else we put task in job queue
				else{
					$id_job = job_queue_add('refresh_url',"Refresh of ".$url,array($url),"inc/refresher_functions",TRUE,$date_list[$i_date_list]);
					// if date in the future, we link the task to the object for a better control (if we change the date of refreshing, for example)
					if($date > 0){ 
						job_queue_link($id_job, array('objet' => $objet, 'id_objet' => $id_objet));
					}
				}
				$i_date_list ++;
			}
		}
	}
	return;
}


function prerefresh_objet_modifier_article($urls, $id_article, $arr){
	// regular edition of article (fields update)
	// only do it if article already published online
	if(article_is_published($id_article) == 1){
		// refresh article
		if(is_pull('article')) array_push($urls['pull'], $id_article.'|article');
		if(lire_config('refresher/article_modifier_article') == 'on'){ 
			array_push($urls['push'], $id_article.'|article');
		}
		// refresh homepage
		if(lire_config('refresher/article_modifier_homepage') == 'on') array_push($urls['push'], "");
		
		// get rubrique(s) of article
		$rubrique_inv_type = lire_config('refresher/article_modifier_rubrique');
		if($rubrique_inv_type == 'parent'){
			$res = sql_select("id_rubrique", "spip_articles", "id_article=".intval($id_article), "", "", 1);
			if($row = sql_fetch($res)){ 
				array_push($urls['push'], $row['id_rubrique'].'|rubrique');
			}
		}
		if($rubrique_inv_type == 'branch' || is_pull('rubrique')){
			// get rubrique of article and all branch
			$res = sql_select("id_rubrique", "spip_articles", "id_article=".intval($id_article), "", "", 1);
			if($row = sql_fetch($res)){ 
				$id_rubrique = $row['id_rubrique'];
				while($id_rubrique != 0){
					if(is_pull('rubrique')) array_push($urls['pull'], $id_rubrique.'|rubrique');
					if($rubrique_inv_type == 'branch') array_push($urls['push'], $id_rubrique.'|rubrique');
					$res_rub = sql_select("id_parent", "spip_rubriques", "id_rubrique=".intval($id_rubrique));
					if($row_rub = sql_fetch($res_rub)) $id_rubrique = $row_rub["id_parent"];
					else $id_rubrique = 0;
				}
			}
		}
		
		// get keywords
		if(lire_config('refresher/article_modifier_mots') == 'on' || is_pull('mot')){
			$liste_groupes = lire_config('refresher/refresher_groupes_mots');
			if(!is_array($liste_groupes)) $liste_groupes = array(0);
			$res = sql_select("m.id_mot", "spip_mots m, spip_mots_liens l", "l.id_objet=".intval($id_article)." and l.objet='article' and l.id_mot=m.id_mot and m.id_groupe in(".implode(",", array_keys($liste_groupes)).")");
			while($row = sql_fetch($res)){
				if(is_pull('mot')) array_push($urls['pull'], $row['id_mot'].'|mot');
				if(lire_config('refresher/article_modifier_mots') == 'on') array_push($urls['push'], $row['id_mot'].'|mot');
			}
		}
		
		// get authors
		if(lire_config('refresher/article_modifier_auteurs') == 'on' || is_pull('auteur')){ 
			$res = sql_select("id_auteur", "spip_auteurs_liens", "id_objet=".intval($id_article)." and objet='article'");
			while($row = sql_fetch($res)){ 
				$id_auteur = $row['id_auteur'];
				if(is_pull('auteur')) array_push($urls['pull'], $id_auteur.'|auteur');
				if(lire_config('refresher/article_modifier_auteurs') == 'on') array_push($urls['push'], $id_auteur.'|auteur');
			}
		}
			
	}
	return $urls;
}

function prerefresh_objet_instituer_article($urls, $id_article, $arr){
	$champs = $arr["data"];
	$args = $arr["args"];
	// refresh when set status to published or if we unpublish or date of publication changed
	if(($champs['statut'] == 'publie' && $args['action'] == 'instituer') || $champs['statut_ancien'] == 'publie'){
		// if we unset published status but article was not online yet there is nothing to refresh
		if($champs['statut_ancien'] == 'publie' && article_is_published($args['id_objet']) != 1) return $urls;
				
		// refresh homepage
		if(lire_config('refresher/article_instituer_homepage') == 'on') array_push($urls['push'], "");
		
		// get rubrique(s) of article
		$rubrique_inv_type = lire_config('refresher/article_instituer_rubrique');
		if($rubrique_inv_type == 'parent'){
			$res = sql_select("id_rubrique", "spip_articles", "id_article=".intval($id_article), "", "", 1);
			if($row = sql_fetch($res)){ 
				array_push($urls['push'], $row['id_rubrique'].'|rubrique');
			}
		}
		if($rubrique_inv_type == 'branch' || is_pull('rubrique')){
			// get rubrique of article and all branch
			$res = sql_select("id_rubrique", "spip_articles", "id_article=".intval($id_article), "", "", 1);
			if($row = sql_fetch($res)){ 
				$id_rubrique = $row['id_rubrique'];
				while($id_rubrique != 0){
					if(is_pull('rubrique')) array_push($urls['pull'], $id_rubrique.'|rubrique');
					if($rubrique_inv_type == 'branch') array_push($urls['push'], $id_rubrique.'|rubrique');
					$res_rub = sql_select("id_parent", "spip_rubriques", "id_rubrique=".intval($id_rubrique));
					if($row_rub = sql_fetch($res_rub)) $id_rubrique = $row_rub["id_parent"];
					else $id_rubrique = 0;
				}
			}
		}
		
		// get authors
		if(lire_config('refresher/article_instituer_auteurs') == 'on' || is_pull('auteur')){ 
			$res = sql_select("id_auteur", "spip_auteurs_liens", "id_objet=".intval($id_article)." and objet='article'");
			while($row = sql_fetch($res)){ 
				$id_auteur = $row['id_auteur'];
				if(is_pull('auteur')) array_push($urls['pull'], $id_auteur.'|auteur');
				if(lire_config('refresher/article_instituer_auteurs') == 'on') array_push($urls['push'], $id_auteur.'|auteur');
			}
		}
		
		// get keywords
		if(lire_config('refresher/article_instituer_mots') == 'on' || is_pull('mot')){
			$liste_groupes = lire_config('refresher/refresher_groupes_mots');
			if(!is_array($liste_groupes)) $liste_groupes = array(0);
			$res = sql_select("m.id_mot", "spip_mots m, spip_mots_liens l", "l.id_objet=".intval($id_article)." and l.objet='article' and l.id_mot=m.id_mot and m.id_groupe in(".implode(",", array_keys($liste_groupes)).")");
			while($row = sql_fetch($res)){
				if(is_pull('mot')) array_push($urls['pull'], $row['id_mot'].'|mot');
				if(lire_config('refresher/article_instituer_mots') == 'on') array_push($urls['push'], $row['id_mot'].'|mot');
			}
		}	
	}
	
	return $urls;
}

function prerefresh_lien_delete_mot_article($urls, $id_mot, $id_article, $arr){
	return prerefresh_lien_mot_article($urls, $id_mot, $id_article, $arr);
}
function prerefresh_lien_insert_mot_article($urls, $id_mot, $id_article, $arr){
	return prerefresh_lien_mot_article($urls, $id_mot, $id_article, $arr);
}
function prerefresh_lien_mot_article($urls, $id_mot, $id_article, $arr){
	// get id_groupe
	$res = sql_select("id_groupe", "spip_mots", "id_mot=".intval($id_mot));
	if($row = sql_fetch($res)){
		$id_groupe = $row['id_groupe'];
		
		// refresh article
		if(is_pull('article')) array_push($urls['pull'], $id_article.'|article');
		if(lire_config('refresher/mot_article_article') == 'on') array_push($urls['push'], $id_article.'|article');

		// refresh homepage
		if(lire_config('refresher/mot_article_homepage') == 'on') array_push($urls['push'], "");
		
		// get rubrique(s) of article
		$rubrique_inv_type = lire_config('refresher/mot_article_rubrique');
		if($rubrique_inv_type == 'parent'){
			$res = sql_select("id_rubrique", "spip_articles", "id_article=".intval($id_article), "", "", 1);
			if($row = sql_fetch($res)){ 
				array_push($urls['push'], $row['id_rubrique'].'|rubrique');
			}
		}
		if($rubrique_inv_type == 'branch' || is_pull('rubrique')){
			// get rubrique of article and all branch
			$res = sql_select("id_rubrique", "spip_articles", "id_article=".intval($id_article), "", "", 1);
			if($row = sql_fetch($res)){ 
				$id_rubrique = $row['id_rubrique'];
				while($id_rubrique != 0){
					if(is_pull('rubrique')) array_push($urls['pull'], $id_rubrique.'|rubrique');
					if($rubrique_inv_type == 'branch') array_push($urls['push'], $id_rubrique.'|rubrique');
					$res_rub = sql_select("id_parent", "spip_rubriques", "id_rubrique=".intval($id_rubrique));
					if($row_rub = sql_fetch($res_rub)) $id_rubrique = $row_rub["id_parent"];
					else $id_rubrique = 0;
				}
			}
		}
		
		// refresh mot
		if(lire_config('refresher/mot_article_mot') == 'on' || is_pull('mot')) {
			$liste_groupes = lire_config('refresher/refresher_groupes_mots');
			// only if mot in the allowed group list
			if(is_array($liste_groupes) && isset($liste_groupes[$id_groupe])){
				if(is_pull('mot')) array_push($urls['pull'], $id_mot.'|mot');
				if(lire_config('refresher/mot_article_mot') == 'on') array_push($urls['push'], $id_mot.'|mot');
			}
		}
	
		// get authors
		if(lire_config('refresher/mot_article_auteurs') == 'on' || is_pull('auteur')){ 
			$res = sql_select("id_auteur", "spip_auteurs_liens", "id_objet=".intval($id_article)." and objet='article'");
			while($row = sql_fetch($res)){ 
				$id_auteur = $row['id_auteur'];
				if(is_pull('auteur')) array_push($urls['pull'], $id_auteur.'|auteur');
				if(lire_config('refresher/mot_article_auteurs') == 'on') array_push($urls['push'], $id_auteur.'|auteur');
			}
		}
	
	}
	return $urls;
}

function prerefresh_lien_delete_document_article($urls, $id_document, $id_article, $arr){
	return prerefresh_lien_document_article($urls, $id_document, $id_article, $arr);
}
function prerefresh_lien_insert_document_article($urls, $id_document, $id_article, $arr){
	return prerefresh_lien_document_article($urls, $id_document, $id_article, $arr);
}
function prerefresh_lien_document_article($urls, $id_document, $id_article, $arr){
	// check if article is published
	if(article_is_published($id_article) == 1){
		// refresh article
		if(is_pull('article')) array_push($urls['pull'], $id_article.'|article');
		if(lire_config('refresher/document_article_article') == 'on'){ 
			array_push($urls['push'], $id_article.'|article');
		}
		// refresh homepage
		if(lire_config('refresher/document_article_homepage') == 'on') array_push($urls['push'], "");
		
		// get rubrique(s) of article
		$rubrique_inv_type = lire_config('refresher/document_article_rubrique');
		if($rubrique_inv_type == 'parent'){
			$res = sql_select("id_rubrique", "spip_articles", "id_article=".intval($id_article), "", "", 1);
			if($row = sql_fetch($res)){ 
				array_push($urls['push'], $row['id_rubrique'].'|rubrique');
			}
		}
		if($rubrique_inv_type == 'branch' || is_pull('rubrique')){
			// get rubrique of article and all branch
			$res = sql_select("id_rubrique", "spip_articles", "id_article=".intval($id_article), "", "", 1);
			if($row = sql_fetch($res)){ 
				$id_rubrique = $row['id_rubrique'];
				while($id_rubrique != 0){
					if(is_pull('rubrique')) array_push($urls['pull'], $id_rubrique.'|rubrique');
					if($rubrique_inv_type == 'branch') array_push($urls['push'], $id_rubrique.'|rubrique');
					$res_rub = sql_select("id_parent", "spip_rubriques", "id_rubrique=".intval($id_rubrique));
					if($row_rub = sql_fetch($res_rub)) $id_rubrique = $row_rub["id_parent"];
					else $id_rubrique = 0;
				}
			}
		}
				
		// any other document page of this article
		if(lire_config('refresher/document_article_documents') == 'on' || is_pull('document')){ 	
			$res = sql_select("id_document", "spip_documents_liens", "objet='article' and id_objet=".intval($id_article));
			while($row = sql_fetch($res)){ 
				if(is_pull('document')) array_push($urls['pull'], $row['id_document'].'|document');
				if(lire_config('refresher/document_article_documents') == 'on') array_push($urls['push'], $row['id_document'].'|document');
			}
		}
		
		// get keywords
		if(lire_config('refresher/document_article_mots') == 'on' || is_pull('mot')){
			$liste_groupes = lire_config('refresher/refresher_groupes_mots');
			if(!is_array($liste_groupes)) $liste_groupes = array(0);
			$res = sql_select("m.id_mot", "spip_mots m, spip_mots_liens l", "l.id_objet=".intval($id_article)." and l.objet='article' and l.id_mot=m.id_mot and m.id_groupe in(".implode(",", array_keys($liste_groupes)).")");
			while($row = sql_fetch($res)){
				if(is_pull('mot')) array_push($urls['pull'], $row['id_mot'].'|mot');
				if(lire_config('refresher/document_article_mots') == 'on') array_push($urls['push'], $row['id_mot'].'|mot');
			}
		}	
		
		// get authors
		if(lire_config('refresher/document_article_auteurs') == 'on' || is_pull('auteur')){ 
			$res = sql_select("id_auteur", "spip_auteurs_liens", "id_objet=".intval($id_article)." and objet='article'");
			while($row = sql_fetch($res)){ 
				$id_auteur = $row['id_auteur'];
				if(is_pull('auteur')) array_push($urls['pull'], $id_auteur.'|auteur');
				if(lire_config('refresher/document_article_auteurs') == 'on') array_push($urls['push'], $id_auteur.'|auteur');
			}
		}
	}	
	return $urls;
}

function prerefresh_objet_modifier_mot($urls, $id_mot, $arr){
	// refresh keyword page
	if(lire_config('refresher/mot_modifier_mot') == 'on' || is_pull('mot')){
		$res = sql_select("id_groupe", "spip_mots", "id_mot=".intval($id_mot));
		if($row = sql_fetch($res)){
			$id_groupe = $row['id_groupe'];
			$liste_groupes = lire_config('refresher/refresher_groupes_mots');
			// only if mot in the allowed group list
			if(is_array($liste_groupes) && isset($liste_groupes[$id_groupe])){
				if(is_pull('mot')) array_push($urls['pull'], $id_mot.'|mot');
				if(lire_config('refresher/mot_modifier_mot') == 'on') array_push($urls['push'], $id_mot.'|mot');
			}
		}
	}
	
	// refresh home
	if(lire_config('refresher/mot_modifier_homepage') == 'on') array_push($urls['push'], "");
	
	return $urls;
}

function prerefresh_objet_modifier_rubrique($urls, $id_rubrique, $arr){
	// refresh rubrique page
	if(is_pull('rubrique')) array_push($urls['pull'], $id_rubrique.'|rubrique');
	if(lire_config('refresher/rubrique_modifier_rubrique') == 'on'){ 
		array_push($urls['push'], $id_rubrique.'|rubrique');
	}
	// refresh home
	if(lire_config('refresher/rubrique_modifier_homepage') == 'on') array_push($urls['push'], "");
	
	return $urls;
}


function prerefresh_objet_modifier_document($urls, $id_document, $arr){
	// refresh article page
	if(lire_config('refresher/document_modifier_article') == 'on' || is_pull('article')){
		$res = sql_select("id_objet", "spip_documents_liens", "objet='article' and id_document=".intval($id_document));
		if($row = sql_fetch($res)){
			$id_article = $row['id_objet'];
			// only if article is online
			if(article_is_published($id_article) == 1){ 
				if(is_pull('article')) array_push($urls['pull'], $id_article.'|article');
				if(lire_config('refresher/document_modifier_article') == 'on') array_push($urls['push'], $id_article.'|article');
			}
		}	
	}
	
	// refresh document page
	if(is_pull('document')) array_push($urls['pull'], $id_document.'|document');
	if(lire_config('refresher/document_modifier_document') == 'on'){ 
		array_push($urls['push'], $id_document.'|document');
	}
	
	return $urls;
}

// traiter_pull : we take care of pull invalidations
function traiter_pull($pull_list){
	$url_list = array();
	foreach($pull_list as $object){
		// if we gave 'id|object' format
		if(strpos($object, '|') !== false){
			$arr = explode('|', $object);
			$res = sql_select("uri, id", "refresher_urls", "objet=".sql_quote($arr[1])." and id_objet=".sql_quote($arr[0]));
			$ids = array();
			while($row = sql_fetch($res)){ 
				array_push($ids,$row['id']);
				array_push($url_list, $GLOBALS['meta']['adresse_site'].'/'.$row['uri']);
			}
			if(sizeof($ids) > 0) $res = sql_delete("refresher_urls", "id in (".implode(',', $ids).")");
		}
		// if we gave straight URI
		else{
			array_push($url_list, $GLOBALS['meta']['adresse_site'].'/'.$object);
			sql_delete("refresher_urls", "uri = ".sql_quote($object));
		}
	}
	if(sizeof($url_list) > 0) invalider_url_cdn($url_list);
}

// we take care of push invalidations
function refresh_url($url){
	// first add domain name, if not done yet
	if(strpos($url, '://') === false){
		if($url != '' && substr($url, 0,1) == '/') $url = $GLOBALS['meta']['adresse_site'].$url;
		else $url = $GLOBALS['meta']['adresse_site'].'/'.$url;
	}
	
	// we delete the cache in CDN for this URL if activated in backend
	invalider_url_cdn($url);
	
	// reminder: URLs containing '*' are generic CDN purges only
	if(lire_config('refresher/use_refresher_spip') == 'yes'){ 
		spip_log("try to refresh in SPIP url:".$url, 'refresher');
		$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
		
		define('POSTURL', $url);
		$ch = curl_init(POSTURL);
		curl_setopt($ch, CURLOPT_POST, 1);
	  define('POSTVARS', 'var_mode=calcul');  // POST VARIABLES TO BE SENT
	  curl_setopt($ch, CURLOPT_POSTFIELDS, POSTVARS);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		$data = curl_exec($ch);
		curl_close($ch);
	}
	
	return;
}

function invalider_url_cdn($url){
	if(lire_config('refresher/use_invalideur_cdn') != 'no'){
		spip_log("CDN purge:".$url, 'refresher');
		if(lire_config('refresher/use_invalideur_cdn') == 'akamai') invalider_url_cdn_akamai($url);
		if(lire_config('refresher/use_invalideur_cdn') == 'edgecast') invalider_url_cdn_edgecast($url);
		if(lire_config('refresher/use_invalideur_cdn') == 'cloudflare') invalider_url_cdn_cloudflare($url);
	}
	return;
}

function invalider_url_cdn_cloudflare($url){
	if(!is_array($url)) $url = array($url);
	
	$token = lire_config('refresher/cloudflare_token');
	$account_email = lire_config('refresher/cloudflare_email');
	$zone_id = lire_config('refresher/cloudflare_zone_id');
	
	spip_log("about to purge on Cloudflare:".$url[0]." to ".$url[sizeof($url)-1]." number of urls: ".sizeof($url), 'refresher');
	
	// action can be set to 'remove' or 'invalidate'
	$headers = array("X-Auth-Email: ".$account_email, "X-Auth-Key: ".$token, "Content-type: application/json");
	$data = array("files" => $url);
  $data_string = json_encode($data);
  $data_string = str_replace("\\/", '/', $data_string);
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
  curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/".$zone_id."/purge_cache");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $http_result = curl_exec($ch);
  $response = json_decode($http_result);
  $error       = curl_error($ch);
  $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  
  if ($http_code != 200) {
      spip_log ("Something went wrong (1). Cloudflare purge request failed for ".$url[0]." to ".$url[sizeof($url)-1].". number of urls: ".sizeof($url)." -> ".$response->httpStatus."(".$response->title.")",'refresher'._LOG_ERREUR);
  } else {
      spip_log ("Purge Success (Cloudflare) for:".$url[0]." to ".$url[sizeof($url)-1].". number of urls: ".sizeof($url),'refresher');
  }
}

function invalider_url_cdn_edgecast($url, $load = false){
	if(is_array($url)){
		spip_log("we don't purge lists on Edgecast!", 'refresher'._LOG_ERREUR);
		return;
	}
	spip_log("about to purge on Edgecast:".$url, 'refresher');
	
	$token = lire_config('refresher/edgecast_token');     // found on the cdn admin My Settings
	$account_number = lire_config('refresher/edgecast_account');        // found on the cdn admin Top Right corner
	
  // Build the request
  $request_params = (object) array('MediaPath' =>  $url, 'MediaType' => 8);   // MediaType 8=small 3=large
  $data = json_encode($request_params);

  // setup the connection and call for a purge.
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.edgecast.com/v2/mcc/customers/'.$account_number.'/edge/purge');
  curl_setopt($ch, CURLOPT_PORT , 443);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLINFO_HEADER_OUT, 1);                  // For debugging
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);             // no caching  
  curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);            // no caching  
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
  curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: tok:'.$token, 'Content-Type: application/json','Accept: application/json', 'Content-length: '.strlen($data), 'Host: api.edgecast.com'));
  $head = curl_exec($ch);
  $httpCode = curl_getinfo($ch);
  curl_close($ch);
	
	// check if error
  if ($httpCode['http_code'] != 200) spip_log("URL ".$url." COULD NOT be deleted from CDN cache: ERROR PURGE ", 'refresher'._LOG_ERREUR);
  else spip_log("URL ".$url." - CDN API PURGE done", 'refresher');
	
	// now we load if it is a specific URL
	if($load && strpos($url, '*') === false){ 
		$ch = curl_init();
  	spip_log("URL ".$url." trying load function", 'refresher');
  	curl_setopt($ch, CURLOPT_URL, 'https://api.edgecast.com/v2/mcc/customers/'.$account_number.'/edge/load');
  	curl_setopt($ch, CURLOPT_PORT , 443);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($ch, CURLOPT_HEADER, 0);
	  curl_setopt($ch, CURLINFO_HEADER_OUT, 1);                  // For debugging
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);             // no caching  
	  curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);            // no caching  
	  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	  curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: tok:'.$token, 'Content-Type: application/json','Accept: application/json', 'Content-length: '.strlen($data), 'Host: api.edgecast.com'));
	  $head = curl_exec($ch);
	  $httpCode = curl_getinfo($ch);
	  curl_close($ch);
	  
	  // check if error
	  if ($httpCode['http_code'] != 200) spip_log("URL ".$url." COULD NOT be loaded from CDN cache: ERROR LOAD ", 'refresher'._LOG_ERREUR);
	  else spip_log("EDGECAST CDN API LOAD done for url:".$url, 'refresher');
	}
	
	return;
}

function invalider_url_cdn_akamai($url){ // 'pap' stands for Php Akamai Purge 
	if(!is_array($url)) $url = array($url);
	
	spip_log("about to purge on Akamai:".$url[0]." to ".$url[sizeof($url)-1]." number of urls: ".sizeof($url), 'refresher');
	$user = lire_config('refresher/akamai_user');
	$password = lire_config('refresher/akamai_password');
	
	// action can be set to 'remove' or 'invalidate'
	$data = array("type" => "arl", "action" => "remove", "objects" => $url);
  $data_string = json_encode($data);
  $data_string = str_replace("\\/", '/', $data_string);

  $ch = curl_init("https://api.ccu.akamai.com/ccu/v2/queues/default");
  curl_setopt_array($ch, array(
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"),
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_USERPWD => "$user:$password",
      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_POSTFIELDS => $data_string,
      CURLOPT_FOLLOWLOCATION => TRUE,
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: '.strlen($data_string)))
  ));

  $response = curl_exec($ch);
  $response = json_decode($response);

  if ($response->httpStatus < 300) { // success
		spip_log ("Purge Success for:".$url[0]." to ".$url[sizeof($url)-1].". number of urls: ".sizeof($url),'refresher');
	} else {
		spip_log ("Something went wrong (1). Akamai purge request failed for ".$url[0]." to ".$url[sizeof($url)-1].". number of urls: ".sizeof($url)." -> ".$response->httpStatus."(".$response->title.")",'refresher'._LOG_ERREUR);
	}
}


// very useful function to see if an article is already published, 
// if so, we return 1, else we return unix date of publication
// if article's status is not 'published' we return 0
// this will determine if we need to refresh some pages or not
function article_is_published($id_article){
	$res = sql_select("date", "spip_articles", "id_article=".intval($id_article)." and statut='publie'", "", "", 1);
	if($row = sql_fetch($res)){
		$date = $row['date'];
		$date_obj = date_create_from_format('Y-m-d H:i:s', $date);
		$date_tmp = date_format($date_obj, 'U');
		if(time() < $date_tmp) return $date_tmp;
		else return 1; 
	}
	return 0;
}

// determine if an object is supposed to be refreshed by a push (default) or a pull
// if there is a rule for this object in the setup then this is a pull instead
function is_pull($objet){
	if(isset($GLOBALS['refresher_objets'])){
		foreach($GLOBALS['refresher_objets'] as $obj){
			if($objet == $obj[1]) return true;
		}
	}
	return false;	
}

?>