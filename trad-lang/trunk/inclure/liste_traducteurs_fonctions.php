<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Afficher l'initiale pour la navigation par lettres
 *
 * @staticvar string $memo
 * @param <type> $url
 * @param <type> $initiale
 * @param <type> $compteur
 * @param <type> $debut
 * @param <type> $pas
 * @return <type>
 */
function afficher_initiale($url,$initiale,$compteur,$debut,$pas){
	static $memo = null;
	static $res = array();
	$out = "";
	if (!$memo
		OR (!$initiale AND !$url)
		OR ($initiale!==$memo['initiale'])
		){
		$newcompt = intval(floor(($compteur-1)/$pas)*$pas);
		// si fin de la pagination et une seule entree, ne pas l'afficher, ca ne sert a rien
		if (!$initiale AND !$url AND !$memo['compteur']) $memo=null;
		if ($memo){
			$on = (($memo['compteur']<=$debut)
				AND (
						$newcompt>$debut OR ($newcompt==$debut AND $newcompt==$memo['compteur'])
						));
			$res[] = lien_ou_expose($memo['url'],$memo['initiale'],$on,'lien_pagination');
		}
		if ($initiale)
			$memo = array('entree'=>isset($memo['entree'])?$memo['entree']+1:0,'initiale'=>$initiale,'url'=>parametre_url($url,'i',$initiale),'compteur'=>$newcompt);
	}
	if (!$initiale AND !$url) {
		if (count($res)>1)
			$out = implode(' ',$res);
		$memo=$res=null;
	}
	return $out;
}

?>
