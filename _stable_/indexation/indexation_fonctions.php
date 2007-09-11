<?php

function balise_EXTRAIT_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	$objet = $p->boucles[$b]->type_requete;

	include_spip('inc/indexation');
	$type = id_index_table('spip_'.$objet);

	$objet = preg_replace(',s$,', '', $objet);
	$_id_objet = champ_sql(id_table_objet($objet), $p);
	$p->code = 'extrait_recherche(@$Pile[0]["recherche"],'.$type.','.$_id_objet.')';
	
	return $p;

}

function extrait_recherche($recherche, $type, $id) {
	$s = spip_query($q = 'SELECT texte FROM spip_indexation WHERE id='._q($id).' AND type='._q($type));
	if ($t = sql_fetch($s)) {
		return google_like($t['texte'], $recherche);
	}
}

function google_like($string, $recherche) {
	$query = rtrim(str_replace("+", " ", $recherche));
	$qt = explode(" ", $query);
	$num = count ($qt);
	$cc = ceil(200 / $num);
		for ($i = 0; $i < $num; $i++) {
			$tab[$i] = preg_split("/($qt[$i])/i",$string,2, PREG_SPLIT_DELIM_CAPTURE);
			if(count($tab[$i])>1){
				$avant[$i] = substr($tab[$i][0],-$cc,$cc);
	    	    	        $apres[$i] = substr($tab[$i][2],0,$cc);
		    	        $string_re .= ".. $avant[$i]<strong>".$tab[$i][1]."</strong>$apres[$i] .. ";
	       }
	 }
	 return $string_re;
}
?>