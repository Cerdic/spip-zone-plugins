<?php
function getNbCandidats($annee='') {
	if($annee!='') $where="WHERE annee=$annee";
	else $where='';
	$sql="SELECT count(*) FROM odb_candidats $where";
	$result=odb_query($sql,__FILE__,__LINE__);
	$nb=mysql_result($result,0,0);
	return $nb;
}
?>
