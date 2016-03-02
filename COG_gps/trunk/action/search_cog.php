<?php

function action_search_cog_dist()
{	header("Content-Type: text/json; charset=UTF-8; ");


	function select_match ($q,  $code, $extended='normal')
	{	
		
		$q=str_replace(array('é','è','à','œ'),array('e','e','a','oe'),$q);
		$q = utf8_decode($q);
		$q2=str_replace(array('oe'),array('œ'),$q);
		
		
		switch ($extended)
		{	// Recherche etendue
			case 'extended': $query = "MATCH(nom) AGAINST ('*".addslashes($q)."*' IN BOOLEAN MODE) OR MATCH(nom) AGAINST ('*".addslashes($q2)."*' IN BOOLEAN MODE)"; break;
			// recherche stricte mais avec notion de pertinence
			case 'normal': $query = "MATCH(nom) AGAINST ('".addslashes($q)."') "; break;
			// Strict
			default : $query = "nom = '".addslashes($q)."' "; break;
		}
		// Limiter la recheche
		if ($code && $code !='') $query .= " AND departement = '".$code."' ";
		
		// Pas trop !
		$query .= " LIMIT 0,100";
		// Lancer la recherche
                $tab = sql_allfetsel('*', 'spip_cog_communes',$query);
		$trouve = false;
		foreach($tab as $row)
		{	if ($trouve) echo ",\n";
			else $trouve = true;
			// resultat
			echo '{"name":"'.trim(str_replace(array('(',')'),'',$row['article']).' '.$row['nom']).'", "nadm":"'.$row['departement'].'", "adm":"'.trim(cog_departement($row['departement'])).'", "insee":"'.$row['departement'].$row['code'].'", "fcode":"commune", "lon":"'.$row['lon'].'", "lat":"'.$row['lat'].'"}';
		}
		// on a trouve quelque chose ?
		return ($trouve);
	}


	
	// Recuperer les parametres
	$q =_request('q');
	$code =_request('code');



	// Recherche sur un toponyme
	if ($q)
	{	
		echo "[";//.$q."\n";

		// Recherche de la commune 
		if (!select_match ($q,  $code))
			if (!select_match ($q,  $code, 'extended'))
				select_match ($q,  $code, 'strict');

		// FIN
		echo "]";
	}
	else echo '[]';
}
function cog_departement($code)
{
    return sql_getfetsel('nom','spip_cog_departements','code='.sql_quote($code));
}

?>