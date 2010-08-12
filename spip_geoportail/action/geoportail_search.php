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
function action_geoportail_search_dist()
{	header("Content-Type: text/json; charset=UTF-8; ");
	#HTTP_HEADER{Content-Type: text/json; charset=UTF-8; }
	//-- Protection du script (interdir l'acces hors du site)
	include_spip ('inc/geoportail_protect');
	if (!geoportail_good_referer('jqsearch')) { echo "[{ error:'Bad Referer '}]"; return; }
	
	include_spip('inc/compat_192');
	include_spip('inc/geoupload');

	function select_match ($q, $zone, $code, $extended=false)
	{	// Recherche etendue
		if ($extended) $query = "MATCH(asciiname,cpostal) AGAINST ('*".addslashes($q)."*' IN BOOLEAN MODE) ";
		// recherche stricte mais avec notion de pertinence
		else $query = "MATCH(asciiname,cpostal) AGAINST ('".addslashes($q)."') ";
		// Limiter la recheche
		if ($zone && $zone !='') $query .= " AND zone = '".$zone."' ";
		if ($code && $code !='') $query .= " AND feature_class = '".$code."' ";
		// Recherche une commune
		else $query .= " AND feature_class != '0' ";
		// Proposer un classement en mode etendu
		if ($extended) $query .= " ORDER BY population DESC";
		// Pas trop !
		$query .= " LIMIT 0,100";
		// Lancer la recherche
		$res = spip_query("SELECT * FROM spip_georgc WHERE ".$query);
		$trouve = false;
		while ($row =spip_fetch_array($res))
		{	if ($trouve) echo ",\n";
			else $trouve=true;
			// resultat
			echo '{"name":"'.$row['name'].'", "nadm":"'.$row['id_dep'].'", "adm":"'.geoportail_departement($row['id_dep']).'", "insee":"'.$row['id_dep'].$row['id_com'].'", "fcode":"'.$row['feature_class'].'", "carte":"'.$row['map'].'", "population":"'.$row['population'].'", "lon":'.$row['lon'].', "lat":'.$row['lat'].'}';
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
	{	$q = utf8_decode($q);
		echo "[\n";//.$q."\n";

		// Recherche de la commune 
		if (!select_match ($q, $zone, $code)) select_match ($q, $zone, $code, true);

		// FIN
		echo "]";
	}
	// Recherche sur des coordonnees
	else if (geoportail_chercher_adm($lon, $lat, $adm))
	{	echo '[{"name":"'			.$adm['name']
			.'", "nadm":"'		.$adm['id_dep']
			.'", "adm":"'			.$adm['departement']
			.'", "insee":"'		.$adm['insee']
			.'", "fcode":"'		.$adm['feature_class']
			.'", "carte":"'		.$adm['map']
			.'", "population":"'	.$adm['population']
			.'", "d":'			.$adm['dist']
			.'}]';
	}
	else echo '[]'; 
}
?>