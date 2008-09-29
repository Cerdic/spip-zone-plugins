<?php


// teste la date de l'evenement (c.a.d date
// de l'evenement lui-meme et de tous les
// evenements associes) ; utilise dans la page
// historique pour savoir s'il faut afficher 
// l'evenement ou non. Retourne vrai lorsque l'
// evenement ou evenement associe est situe 
// dans le futur.
function ha_testdate($id_ev, $datelim)
{
	$id_ev = intval($id_ev);

	// selectionne date debut et date fin sur tous les
	// evenements lies a l'evenement source
	$sql = "select id_evenement,date_debut,date_fin from spip_evenements ".
		" where id_evenement_source=".$id_ev." or id_evenement=".$id_ev;
	$res = spip_query($sql);

	// si une des dates est superieure a aujourd'hui, on 
	// retourne vrai (on teste par rapport a hier pour
	// etre sur de rien oublier)

	// pour tester, on teste soit avec hier, soit 
	// avec une date donnee en param (cela pour traiter
	// les evenements passes et ne pas les tracer
	// dans les modifications)
	if (preg_match("/^[0-9]+$/", $datelim))
	{
		$tm = mktime(0,0,0,intval(substr($datelim,4,2)),
			     intval(substr($datelim,6,2)),
			     intval(substr($datelim,0,4)));
		$hier = $tm - 3600*24;
	}
	else
		$hier = time() - 3600*24;

	while($row = spip_fetch_array($res))
	{
		$dd = strtotime($row["date_debut"]);
		$df = strtotime($row["date_fin"]);
		if (($dd > $hier) || ($df > $hier))
			return true;
	}

	return false;
}

?>