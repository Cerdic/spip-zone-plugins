<?
function AjouteClause($requete,$clause,$operateur="AND") {
		if (!strstr($requete,"WHERE")) $requete.=" WHERE (".$clause.")";
		else $requete.=" $operateur (".$clause." )";
		return($requete);
	}

function AjouteClauseCond($requete,$clause,$operateur="OR") {
		if (($requete=="") AND ($clause!="")) $requete.=" AND ( (".$clause.")";
		if ($clause=="") return($requete." )");
		$requete.=" $operateur (".$clause." )";
		return($requete);
	}	

?>