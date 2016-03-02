<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* Recherche d'une communes dans le RGC 
* et renvoie en JSON pour traitement en Ajax
*
* Parametres :
*	q : la question (le nom de la commune recherchee)
*	code : le code de la commune (');
*	zone : la zone geograpique
*	lon : la longitude
*	lat : la latitude
* Si q est remplit, la recherche se fait sur le nom et renvoit une liste de commune et leurs les coordonnes.
* Si q n'est pas remplit, la recherche se fait sur les coordonnees (lon, lat) et renvoit une liste de commune
*
* - exemple : spip.php?action=geoportail_search&q=paris&zone=FXX&hash=#GEOPORTAIL_PROTECT{jqsearch}
* => renvoit : 
*	 {name:"nom", nadm:"num dep", adm:"nom dep", fcode:"code", carte:"top25", lon:x, lat:y }, ...
*
* - exemple : spip.php?action=geoportail_search&lon=X&lat=Y&zone=FXX&hash=#GEOPORTAIL_PROTECT{jqsearch}
* => renvoit : 
*	 { name:"paris", nadm:"num dep", adm:"nom dep", fcode:"code", carte:"top25", d:distance }, ...';
*
*/
function action_geoportail_search_cog_dist()
{	header("Content-Type: text/json; charset=UTF-8; ");
	#HTTP_HEADER{Content-Type: text/json; charset=UTF-8; }
	//-- Protection du script (interdir l'acces hors du site)
	include_spip ('inc/geoportail_protect');
	if (!geoportail_good_referer('jqsearch')) { echo "[{ error:'Bad Referer '}]"; return; }
	
	include_spip('inc/compat_192');
	include_spip('inc/geoupload');

	function select_match ($q, $zone, $code, $extended='normal')
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
		if ($zone && $zone !='') $query .= " AND departement = '".$zone."' ";
		
		// Pas trop !
		$query .= " LIMIT 0,100";
		// Lancer la recherche
                $res = sql_query("SELECT * FROM spip_cog_communes WHERE ".$query);
		$trouve = false;
		include_spip ('public/geoportail_boucles');
		while ($row =sql_fetch_array($res))
		{	if ($trouve) echo ",\n";
			else $trouve=true;
			// resultat
			echo '{"name":"'.trim(str_replace(array('(',')'),'',$row['article']).' '.$row['nom']).'", "nadm":"'.$row['departement'].'", "adm":"'.trim(cog_departement($row['departement'])).'", "insee":"'.$row['departement'].$row['code'].'", "fcode":"commune", "lon":"'.$row['lon'].'", "lat":"'.$row['lat'].'"}';
		}
		// on a trouve quelque chose ?
		return ($trouve);
	}
	
	// Recuperer les parametres
	$q =_request('q');
	$code =_request('code');
	$zone =_request('zone');
	$lon =_request('lon');
	$lat =_request('lat');
	
	// Recherche sur un toponyme
	if ($q)
	{	
		echo "[";//.$q."\n";

		// Recherche de la commune 
		if (!select_match ($q, $zone, $code)) if (!select_match ($q, $zone, $code, 'extended')) select_match ($q, $zone, $code, 'strict');

		// FIN
		echo "]";
	}
	// Recherche sur des coordonnees
	else if (geoportail_chercher_adm($lon, $lat, $adm))
	{	echo '[{"name":"'		.$adm['name']
			.'", "nadm":"'		.$adm['id_dep']
			.'", "adm":"'		.$adm['departement']
			.'", "insee":"'		.$adm['insee']
			.'", "fcode":"'		.$adm['feature_class']
			.'", "carte":"'		.$adm['map']
		//	.'", "population":"'	.$adm['population']
			.'", "d":'		.$adm['dist']
			.'}]';
	}
	else echo '[]'; 
}
function cog_departement($code)
{
    return sql_getfetsel('nom','spip_cog_departements','code='.sql_quote($code));
}

?>