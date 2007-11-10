<?php
/*
 * Expresso
 * Gestion d'un cache statique Apache
 *
 * Auteurs :
 * Cedric Morin, Yterium.com
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

// ration de cache, exprime en 60e de temps :
// 0 : pas de cache apache
// 60 : toutes les requetes passent par apache (ne jamais faire ca !)
// il faut laisser passer une partie des requetes pour que spip mette les pages a jour
@define('_EXPRESSO_CACHE_RATIO',50);

function expresso_fenetre_temps($duree){
}
function expresso_genere_htaccess(){
	/* debug*/
	include_spip('inc/meta');
	ecrire_meta('expresso',$GLOBALS['meta']['expresso']);
	ecrire_metas();
	
	lire_fichier('.htaccess',$htaccess);
	if (strpos($htaccess,'###EXPRESSO###')!==FALSE) {
		$express = "";
		$liste_pages = explode("\n",$GLOBALS['meta']['expresso']);
		foreach($liste_pages as $rewrite) {
			$rewrite = explode('!',$rewrite);
			if (count($rewrite)==2) {
				# une fraction de temps donnee basse sur la valeur des secondes
				# on sert par apache
				$url = parse_url($rewrite[0]);
				$query = $url['query'];
				$host = $url['host'];
				$url = substr($rewrite[0],strlen($GLOBALS['meta']['adresse_site']));
				if ($url{0}=='/') $url = substr($url,1);
				if (($p=strpos($url,"?"))!==FALSE)
					$url = substr($url,0,$p);
				$start = rand(0,59);
				$r = "RewriteCond %{HTTP_HOST} ^$host$ [NC]
RewriteCond %{QUERY_STRING} ^$query$ [NC]";
				if (_EXPRESSO_CACHE_RATIO==59){
					$express .= $r . "
RewriteCond %{TIME_SEC} !=$start
RewriteRule ^$url$ ".$rewrite[1]." [L]

";					
				}
				else {
					$end = modulo($start+round(_EXPRESSO_CACHE_RATIO),60);
					$start--;
					$end++;
					if ($start<$end)
						$express .= $r ."
RewriteCond %{TIME_SEC} >$start
RewriteCond %{TIME_SEC} <$end
RewriteRule ^$url$ ".$rewrite[1]." [L]
	
";
					else
						$express .= $r . "
RewriteCond %{TIME_SEC} >$start
RewriteRule ^$url$ ".$rewrite[1]." [L]
$r
RewriteCond %{TIME_SEC} <$end
RewriteRule ^$url$ ".$rewrite[1]." [L]
	
";
				}
			}
		}
		$htaccess = preg_replace(",###EXPRESSO###.*###/EXPRESSO###,ms","###EXPRESSO###",$htaccess);
		$htaccess = str_replace("###EXPRESSO###","###EXPRESSO###\n$express###/EXPRESSO###",$htaccess);
		ecrire_fichier('.htaccess',$htaccess);
	}
}

function expresso_nettoie($flux){
	$flux =	str_replace("</body>","<span style='font-size:xx-small;'>expresso</span></body>",$flux);
	$flux = preg_replace(",<div[^>]*spip-admin[^>]*>.*</div>,Uims","",$flux);
	return $flux;
}

function expresso_affichage_final($flux) {
	if (isset($GLOBALS['page']['entetes']['X-Expresso'])
	&& ($url = self(true))
	//&& (strpos($url,"?")===FALSE)
	) {
		$url = "http://".$_SERVER['HTTP_HOST']. $url;
		$nom_cache = _DIR_VAR . "apache/".md5($url).".html";
		if ( 
			($GLOBALS['var_mode']=='calcul')
			OR ($GLOBALS['var_mode']=='recalcul')
			OR !($e=file_exists($nom_cache))
			OR ( ($d=filemtime($nom_cache)) AND ($d+$GLOBALS['page']['entetes']['X-Spip-Cache']<time()))
			) {
			spip_log("$url : $nom_cache",'boost');
			ecrire_fichier($nom_cache,expresso_nettoie($flux));
			# expresso est une simple chaine pour eviter la deserialisation a chaque hit et preferer un strpos plus rapide
			# url!nom_cache\n
			if (strpos($GLOBALS['meta']['expresso'],"$url!")===FALSE) {
				$GLOBALS['meta']['expresso'] .= "$url!$nom_cache\n";
				expresso_genere_htaccess();
			}
			elseif($GLOBALS['var_mode']=='recalcul')
				expresso_genere_htaccess();
		}
	}
	return $flux;
}

?>