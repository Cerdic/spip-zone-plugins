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

function afficheListeAvecLabel($label,$nom,$tableau,$valDefaut,$options="") {
	echo "<p>$label : ";
	afficheListe($nom,$tableau,$valDefaut,$options);
	echo "</p>";
}
	
function afficheListe($nom,$tableau,$valDefaut,$options="") {
	
	echo "\n<select name='$nom' class='fondl' align='middle' $options>\n";
	echo "<option value='$valDefaut' selected>".$tableau[$valDefaut]."</option>\n";
	reset ($tableau);
	while (list($key,$val) = each ($tableau)) {
		if ($key <> $valDefaut)
			echo "<option value='$key'>$val</option>\n";
	}
	echo "</select>";
}	
?>