<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_once _ROOT_RESTREINT . "inc/afficher_objets.php";

/**
 * affichage des liste d'objets
 * surcharge pour aiguiller vers la mise en skel
 *
 * @param string $type
 * @param string $titre
 * @param array $requete
 * @param string $formater
 * @param bool $force
 * @return string
 */
function inc_afficher_objets($type, $titre,$requete,$formater='', $force=false){
	$res = ""; // debug
	// routage sur le squel de liste si besoin
	include_spip('base/connect_sql');
	$table = table_objet($type);
	$fond = "prive/liste/$table";
	if (find_in_path("$fond.html")){
		$contexte = $_GET;
		unset($contexte['where']); // securite
		// passer le where
		foreach($requete as $k=>$v)
			$contexte[strtolower($k)] = $v;
		if (isset($contexte['limit'])){
			$contexte['limit'] = explode(',',$contexte['limit']);
			$contexte['nb'] = end($contexte['limit'])+1;
			unset($contexte['limit']);
		}
		if (isset($contexte['order by'])){
			$contexte['order by'] = explode(' ',$contexte['order by']);
			$sens = (end($contexte['order by'])=='DESC')?-1:1;
			$contexte['order by'] = explode(',',reset($contexte['order by']));
			$contexte['order by'] = explode('.',reset($contexte['order by']));
			$contexte['order'] = end($contexte['order by']);
			if ($contexte['order']=='date')
				$contexte['date_sens'] = $sens;
		}

		// cas particuliers tordus avec jointures, en attendant la recriture
		if (preg_match(",(?:A|articles).id_article=(?:lien|L).id_article AND (?:lien|L).id_auteur=([0-9]+),i",$contexte['where'],$regs)
		OR preg_match(",(?:lien|L).id_auteur=([0-9]+),i",$contexte['where'],$regs)){
			$contexte['id_auteur'] = $regs[1];
			$contexte['where'] = str_replace($regs[0],"(1=1)",$contexte['where']);
			$contexte['where'] = str_replace("A.","",$contexte['where']);
		}
		if (preg_match(",(lien|L).id_mot=([0-9]+),i",$contexte['where'],$regs)){
			$contexte['id_mot'] = $regs[2];
			$contexte['where'] = str_replace($regs[0],"(1=1)",$contexte['where']);
		}

		//$contexte['where'] = str_replace("$table.","",$contexte['where']);

		#var_dump($contexte);
		$contexte['titre']=$titre;
		$contexte['sinon']=($force ? $titre:'');
		$res = recuperer_fond($fond,$contexte,array('ajax'=>true));
		if (_request('var_liste'))
			var_dump($contexte);
		
		if (!_request('var_liste'))
			return $res;
	}

	// pas de skel pour cet objet,
	// on se rabat sur le core
	return inc_afficher_objets_dist($type, $titre, $requete, $formater, $force);

}

?>
