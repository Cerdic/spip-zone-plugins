<?php

function inc_termes_recherches_to_array($referers){
	foreach($referers as $r){
		$termes_bruts[] = strtolower(
							preg_replace("`\"|'`","",urldecode(
								preg_replace("`&.*$`","",
									preg_replace("`^.*recherche=`","",$r)
										))));
	}
	
	foreach($termes_bruts as $t){
		$termes[$t]++ ;
	}
	
	arsort($termes);
	
	return $termes ;
}
