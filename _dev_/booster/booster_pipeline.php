<?php

function booster_genere_htaccess(){
	/* debug*/
	if (_request('clear_booster')!==NULL)		$GLOBALS['meta']['booster'] = "";
	include_spip('inc/meta');
	ecrire_meta('booster',$GLOBALS['meta']['booster']);
	ecrire_metas();
	
	$ht = "";
	$liste_pages = explode("\n",$GLOBALS['meta']['booster']);
	foreach($liste_pages as $rewrite) {
		$rewrite = explode('!',$rewrite);
		if (count($rewrite)==2) {
			# une fraction de temps donnee basse sur la valeur des secondes
			# on sert par apache
			$url = parse_url($rewrite[0]);
			$ht .= "
RewriteCond %{HTTP_HOST} ^".$url['host']."$ [NC]
RewriteCond %{TIME_SEC} <30
RewriteRule ^".$url['path']."$ ".$rewrite[1]." [L]";
		}
	}
	ecrire_fichier(_DIR_TMP."htaccess.txt",$ht);
}

function booster_affichage_final($flux) {
	if (isset($GLOBALS['page']['entetes']['X-Cache-Apache'])) {
		$url = "http://".$_SERVER['HTTP_HOST'] . self(true);
		$nom_cache = sous_repertoire(_DIR_CACHE,"apache").md5($url).".html";
		if ( 
				!($e=file_exists($nom_cache))
			OR ( ($d=filemtime($nom_cache)) AND ($d+$GLOBALS['page']['entetes']['X-Spip-Cache']<time()))
			) {
			spip_log("$url : $nom_cache",'boost');
			ecrire_fichier($nom_cache,$flux);
			# booster est une simple chaine pour eviter la deserialisation a chaque hit et preferer un strpos plus rapide
			# url!nom_cache\n
			if (/*debug*/!$e OR
			 strpos($GLOBALS['meta']['booster'],"$url!")===FALSE) {
				$GLOBALS['meta']['booster'] .= "$url!$nom_cache\n";
				booster_genere_htaccess();
			}
		}
	}
	return $flux;
}

?>