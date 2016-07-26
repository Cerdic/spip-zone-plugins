<?php
include_spip("inc/config");
include_spip("inc/refresher_functions");
define('_LOG_FILTRE_GRAVITE',8);


$GLOBALS['refresher_objets'] = array(
		/* define pull objects here */
		/*array('mot_skel', 'mot')*/
);

// we stop invalidating the whole cache when there's an update (basic SPIP feature)
$GLOBALS['derniere_modif_invalide'] = false;

if(isset($_GET['var_mode']) && ($_GET['var_mode'] == 'calcul' || $_GET['var_mode'] == 'recalcul')){
	$who_recalculates = lire_config('refresher/who_recalculates');
	$redirect = false;
	switch($who_recalculates){
		case 'authors' : 
			if(!isset($GLOBALS['visiteur_session']) || !isset($GLOBALS['visiteur_session']['id_auteur']) || $GLOBALS['visiteur_session']['id_auteur']=='') $redirect = true;/*unset($_GET['var_mode']);*/
			break;
		case 'webmasters' : 
			if(!isset($GLOBALS['visiteur_session']) || !isset($GLOBALS['visiteur_session']['id_auteur']) | !defined('_ID_WEBMESTRES') || !in_array($GLOBALS['visiteur_session']['id_auteur'], explode(':', _ID_WEBMESTRES))) $redirect = true;/*unset($_GET['var_mode']);*/
			break;
		case 'none' : 
			unset($_GET['var_mode']);
			break;
		default : 
			break;
	}
}
	
// in case the recalcul is in POST form we turn it into GET to notice SPIP. This is a refresh.
if(isset($_POST["var_mode"]) && $_POST["var_mode"]=="calcul"){
	$_SERVER['REQUEST_METHOD']="GET";
	$_GET["var_mode"] = "calcul";
	spip_log("calcul (PUSH) ".$GLOBALS['meta']['adresse_site'].$GLOBALS['REQUEST_URI'], 'refresher');
}

if(isset($GLOBALS['refresher_objets']) && is_array($GLOBALS['refresher_objets'])){
	foreach($GLOBALS['refresher_objets'] as $item){
		$page = $item[0];
		$objet = $item[1];
		if($page == $_GET['page'] && (isset($_GET['id_'.$objet]) | isset($_GET[$objet]))){
			if(isset($_GET['id'.$item[1]])) $id_objet = $_GET['id_'.$item[1]];
			else $id_objet = $_GET[$item[1]];
			if(is_array($id_objet)) 
			$id_objet = '|'.implode('|', $id_objet).'|';
			$res = sql_select("id_objet, objet", "refresher_urls", "uri=".sql_quote($url_sv)." and objet=".sql_quote($objet)." and id_objet=".sql_quote($id_objet), "", "", 1);
			if($row = sql_fetch($res)){}
			else{
				sql_insertq('refresher_urls', array('uri' => $url_sv, 'objet' => 'multi_kw', 'id_objet' => $id_objet, 'squelette' => $_GET['page']));
				// forcer le calcul
				if (!defined('_VAR_MODE')) {
					define('_VAR_MODE', 'calcul');
				}
			}
		}
	}
}

// exemple of customizing an invalidation : editing an article
// NOTE: all URLs given must be without the domain. 
// i.e for http://www.mysite.com/mypath/mypage.html we only give 'mypath/mypage.html'
/*
function refresh_objet_modifier_article($urls, $id_article, $arr){
	// only do it if article already published online
	if(article_is_published($id_article) == 1){
		// refresh article page with 'push'
		array_push($urls['push'], $id_article.'|article'));
		// refresh article page with 'push' (useless if we already used push on it)
		array_push($urls['pull'], $id_article.'|article'));
	}
	return $urls;
}
*/

/* examples of functions we can use:
- refresh_objet_modifier_article -> editing an article
- refresh_objet_instituer_article -> publishing/unpublishing an article
- refresh_lien_delete_document_article
- refresh_lien_insert_document_article
- refresh_lien_delete_mot_article
- refresh_lien_insert_mot_article
- refresh_objet_modifier_mot
... and probably more. You might want to try other combinationsor object, it might work!
*/

?>