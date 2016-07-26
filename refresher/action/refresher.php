<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_refresher_dist() {
	include_spip('inc/autoriser');
	if(autoriser('configurer')){
		$cache_folders = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
		$action = _request("refresher_action");
		
		switch ($action) {
	  	case "vider_refresher":
		  	include_spip("inc/queue");
		  	$res = sql_allfetsel("id_job","spip_jobs","fonction='refresh_url'");
				if (is_array($res)) {
					foreach ($res as $row)
						queue_remove_job($row['id_job'],0);
				}
	    	spip_log("Attempt to manually delete all URLs from job_queue", 'refresher');
	    	break;
	    case "delete_cache_cdn":
	    	include_spip("inc/refresher_functions");
	    	$url_to_delete = _request("url_to_delete");
	    	spip_log("Attempt to manually refresh URL ".$url_to_delete, 'refresher');
	    	refresh_url($url_to_delete);
	    	break;
	    case "remove_spip_cache":
	    	$file_to_delete = _request("file_to_delete");
	    	spip_log("Attempt to manually delete cache files matching ".$file_to_delete, 'refresher');
	    	if(strpos($file_to_delete, '/') !== false){
	    		$arr = explode('/', $file_to_delete);
	    		if(in_array($arr[0], $cache_folders, TRUE)){
	    			exec("rm -f -- ".$_SERVER['DOCUMENT_ROOT'].'/'._DIR_CACHE.$file_to_delete);
	    		}
	    	}
	    	else{
	  			foreach($cache_folders as $cache_folder){
	  				exec("rm -f -- ".$_SERVER['DOCUMENT_ROOT'].'/'._DIR_CACHE.$cache_folder.'/'.$file_to_delete);
	  			}
	  		}
	    	break;
	    case "remove_spip_cache_period":
	    	$from = _request('date_jour_from')." "._request('date_heure_from');
	    	$to = _request('date_jour_to')." "._request('date_heure_to');
				spip_log("Attempt to manually delete cache files from ".$from." to ".$to, 'refresher');
				$date_obj_from = date_create_from_format('Y-m-d H:i', $from);
				$date_from = date_format($date_obj_from, 'U');
				$date_obj_to = date_create_from_format('Y-m-d H:i', $to);
				$date_to = date_format($date_obj_to, 'U');
				$n = 0;
				foreach($cache_folders as $cache_folder){
					$folder = $_SERVER['DOCUMENT_ROOT'].'/'._DIR_CACHE.$cache_folder.'/';
					if (is_dir($folder)) {
						if ($dh = opendir($folder)) {
					  	while (($file = readdir($dh)) !== false) {
					  		if($file != '.' && $file != '..'){
					  			$file_date = filemtime($folder.$file);
					  			if($file_date > $date_from && $file_date < $date_to){
					  				@unlink($folder.$file);
					  				$n++;
					    		}
					    	}
					    }
					    closedir($dh);
					  }
					}
				}
				break;
			case "add_url_cron":
	    	$url = _request('url');
	    	$frequence = _request('frequence');
				spip_log("Attempt to add cron job ".$url." with a frequency of ".$frequence, 'refresher');
				if($url != '' && is_numeric($frequence)){
					$res = sql_select("url", "refresher_cron", "url=".sql_quote($url), "", "", 1);
					if(sql_count($res) == 0) sql_insertq("refresher_cron", array("url" => $url, "frequence" => intval($frequence), "last_hit" => 'NOW()'));
					else sql_updateq("refresher_cron", array("frequence" => intval($frequence)), "url=".sql_quote($url));
				}
				break;
			case "remove_url_cron":
	    	$urls = _request('urls');
				if(is_array($urls)){
					foreach($urls as $url){ 
						spip_log("Attempt to remove cron job ".$url, 'refresher');
						sql_delete("refresher_cron", "url=".sql_quote($url));
					}
				}
				break;
		}
	}
	if(isset($n)) header("Location: /ecrire/?exec=configurer_refresher&removed=".$n);
	else header("Location: /ecrire/?exec=configurer_refresher");
	die('You will be redirected soon...');
}

?>