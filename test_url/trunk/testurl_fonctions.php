<?php


function testurl_lister_url_du_site($objet="site",$branche=0){

$tab_expreg_mysql=array('(https?://|ftps?|www\.)[\.A-Za-z0-9\-]+\.[a-zA-Z]{2,4}/?','->[^\]]\]');
$tab_base = array("(((http|https|ftp|ftps)://|www\.)([a-zA-Z0-9\-]*\.)+[a-zA-Z0-9]{2,4}(/[a-zA-Z0-9=.?&_\-/%]*[a-zA-Z0-9=?&_\-/%])?)","->([a-zA-Z]{3,10}[0-9]{1,})\]");
$where_champs=array();
switch($objet)
{
	case 'article':
		$select='id_article as id_objet,titre,concat(descriptif,texte) as texte';
		$table='spip_articles';
		$where_champs=array('descriptif','texte');
	break;
	case 'rubrique':
		$select='id_rubrique as id_objet,titre,concat(descriptif,texte) as texte';
		$table='spip_rubriques';
		$where_champs=array('descriptif','texte');
	break;
	case 'site':
		$select='id_syndic as id_objet,nom_site as titre,concat(descriptif,\' \',url_site,\' \',url_syndic) as texte';
		$table='spip_syndic';
		$where_champs=array();

	break;
	default:
		return array();
	break;
}

$where=array();
foreach($tab_expreg_mysql as $expreg)
	foreach($where_champs as $wc)
		$where[]=$wc.' regexp(\''.$expreg.'\')';
$tab_url=array();
$where=(!empty($where))?'('.implode(' or ',$where).')':"";
$where.=(empty($where) or $branche<=0)?'':' AND ';
$where.=($branche>0)?'(id_rubrique IN('.implode(',',testurl_marmots($branche)).'))':"";
$tab_objet=sql_allfetsel($select,$table,$where);

foreach($tab_objet as $objet){
	$tab_temp=array();
	foreach($tab_base as $base){
		if(preg_match_all("#".$base."#", $objet['texte'], $matches)>0)
			$tab_temp=array_merge($matches[1]);
	}
	foreach($tab_temp as &$url_site)
		if(preg_match('#^www\.#',$url_site))
			$url_site='http://'.$url_site;
	$url_site=trim($url_site,'/');
	if(!empty($tab_temp))
		$tab_url[$objet['id_objet']]=array('titre'=>$objet['titre'],'liens'=>array_unique($tab_temp));
}
return $tab_url;

}



function testurl_createTree($list, $parent=0){
    $tree = array();
	foreach ($list as $l){
		if($l['id_parent']==$parent){
			$tree[$l['id_rubrique']] = testurl_createTree($list, $l['id_rubrique']);
		}
	}
return $tree;
}

function testurl_getTree($list,$branche=0,$sur_la_branche=false){
    $tree = array();
    if($branche==0)$sur_la_branche=true;
    foreach ($list as $k=>$l){
		if($branche==$k)
			$sur_la_branche=true;
		if($sur_la_branche){
			$tree[]=$k;
			}
		if(!empty($l)){
			$tree = array_merge($tree,testurl_getTree($l,$branche,$sur_la_branche));
		}
		if($branche==$k)
			break;
        
    } 
    return $tree;
}



function testurl_marmots($branche){
$tab_rub=sql_allfetsel('id_rubrique,id_parent','spip_rubriques');
$tab_rub=testurl_createTree($tab_rub,0);
$tab_rub=testurl_getTree($tab_rub,$branche);
return $tab_rub;
}

function filtre_testurl_nbliens($tab){
	$nb_liens=0;
	foreach($tab as $t)
		$nb_liens+=count($t['liens']);
	return $nb_liens;
}

function balise_TESTURL_LISTE_URL($p){
	
    $objet= interprete_argument_balise(1, $p);
    $branche= interprete_argument_balise(2, $p);
    $p->code = "testurl_lister_url_du_site($objet,$branche)";
    return $p;
}



	function message_erreur_curl($code_erreur) {
		static $erreurs = false;
		if ($erreurs === false) {
			$erreurs = array(
				204 => _T("testurl:cette_page_contient_rien"),
				206 => _T("testurl:contenu_partiel_page"),
				400 => _T("testurl:erreur_requete_http"),
				401 => _T("testurl:authentification_requise"),
				402 => _T("testurl:acces_page_payant"),
				403 => _T("testurl:acces_page_refuser"),
				404 => _T("testurl:page_inexistante"),
				405 => _T("testurl:methode_requete_non_autorise"),
				500 => _T("testurl:erreur_interne_serveur"),
				502 => _T("testurl:erreur_cause_passerelle_serveur"),
			);
		}
		return '<a href=?exec=sites&id_syndic='.$id_syndic.'>'.$nom_site.'</a> <span style="color:red;">'
				._T("testurl:site_incorrect_code_erreur"). ' ' .$code_erreur.': '. $erreurs[$code_erreur] . '</span><br />';
	}

	function filtre_check_url($url_site, $timeout = 10)
	{
		global $url_visite;
		
		if (empty($url_visite))
			 $url_visite=array();
	
		if(!isset($url_visite[$url_site]))
		{
			if(preg_match('#^(art|doc|rub|aut)([0-9]*)$#',$url_site,$matches))
			{
				$code=testurl_verifier_url_ecrire($matches[1],$matches[2]);
				
			}
			else
			{
				$ch = curl_init($url_site);
				curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				curl_setopt($ch, CURLOPT_NOBODY, TRUE);

				if (strpos($url_site, 'https://') === 0)
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

				if (!curl_exec($ch))
					$ret = 404;

				$ret = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);

				if ($ret == 0) {
					$ret = 404;
				}

				switch ($ret) {
					case 204:
					case 206:
					case 400:
					case 401:
					case 402:
					case 403:
					case 404:
					case 405:
					case 500:
					case 502:
						$code = message_erreur_curl( $ret);
						break;
					case 200:
					case 301:
					case 302:
					default:
						$code='';
						break;
						
				}
				
			}
			$url_visite[$url_site]=$code;
		}
		return $url_visite[$url_site];
	}



function filtre_testurl_transforme_en_url($url){
if(preg_match('#^(art|doc|rub|aut)([0-9]*)$#',$url,$matches))
	{
		switch($matches[1]){
			case 'rub':
				$url=generer_url_ecrire('rubrique','id_rubrique='.$matches[2]);
			break;
			case 'doc':
				$url=generer_url_ecrire('document_edit','id_document='.$matches[2]);
			break;
			case 'aut':
				$url=generer_url_ecrire('auteur','id_auteur='.$matches[2]);
			break;
			case 'default':
				$url=generer_url_ecrire('article','id_article='.$matches[2]);
			break;
		}
	}
elseif(preg_match('#^www\.#',$url,$matches)){
	$url='http://'.$url;
}	

return $url;
}

/*
function testurl_lister_email_du_site(){


 \w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*



}*/

function testurl_verifier_url_ecrire($objet,$id_objet){

	switch($objet){
	case 'art':
		$statut=sql_getfetsel('statut','spip_articles','id_article='.$id_objet);
		if($statut!='publie')
			return _T("testurl:erreur_article_introuvable",array('id'=>$fichier));
	break;
	case 'rub':
		$statut=sql_getfetsel('statut','spip_rubriques','id_rubrique='.$id_objet);
		if($statut!='publie')
			return _T("testurl:erreur_rubrique_introuvable",array('id'=>$fichier));
	break;
	
	case 'doc';
		$fichier=sql_getfetsel('fichier','spip_documents','id_document='.$id_objet);
		if(!file_exists(_DIR_IMG.$fichier))
			return _T("testurl:erreur_document_introuvable",array('fichier'=>$fichier));
	break;
	case 'aut';
		// TODO
	break;
	case 'default';
		// TODO
	break;
	}

	return '';
}


?>