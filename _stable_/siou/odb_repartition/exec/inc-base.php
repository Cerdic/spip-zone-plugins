<?php

/** Recupere le tableau de capacite d'accueil d'un centre de composition
 * @param string annee : annee
 * @param int $idCentre : centre de composition
 * @param string $typeSalle : type de salle
 * @return array : tableau des capacites (par id_salle : capacite_type, capacite_salle, dispo, salle)
 */
function getCapacite($annee,$idCentre,$typeSalle) {
   $sql = 'SELECT * , nb_salles * capacite capacite_type , nb_salles * capacite - nb_repartis dispo '
        . ' FROM odb_ref_salle salle '
        . ' LEFT JOIN ( '
        . '  SELECT id_salle , count( * ) nb_repartis '
        . '  FROM odb_repartition rep2 '
        . "  WHERE annee=$annee"
        . '  GROUP BY id_salle '
        . " ) rep ON salle.id = rep.id_salle  "
        . " WHERE salle.annee=$annee AND salle.id_etablissement=$idCentre AND salle.salle='$typeSalle' "
        . ' ORDER BY dispo DESC'
        ;
   $result=odb_query($sql,__FILE__,__LINE__);
   while($row=mysql_fetch_array($result)) { // on prend seulement la 1e ligne
      $id_salle=$row['id'];
      $dispo=$row['dispo'];
      if($dispo=='') $dispo=$row['capacite_type'];
      $tab_capacite[$id_salle]['capacite_type']=$row['capacite_type'];
      $tab_capacite[$id_salle]['capacite_salle']=$row['capacite'];
      $tab_capacite[$id_salle]['dispo']=$dispo;
      $tab_capacite[$id_salle]['salle']=$row['salle'];
   }
   return $tab_capacite;
}

/** Recupere le tableau des candidats a repartir 
 * @param string $annee
 * @param array $par : peut etre un tableau
 * @param string $filtre : '' si $par est un tableau
 * @param int $limit
 * @return array : tableau des candidats (par id_saisie : dept, ville, etablissement)
 */
function getCandidatsARepartir($annee,$par,$filtre,$limit) {
   $sql="SELECT id_saisie, serie, etablissement, ville, departement, (year( ne_le ) + CAST( ne_en AS unsigned ) + CAST( ne_vers AS unsigned)) as ann\n"
      . " from odb_candidats can\n"
      . " where annee=$annee\n"
      . " and id_saisie NOT IN (select id_saisie from odb_repartition where annee=$annee)\n"
      ;
   if(is_array($par)) {
      foreach($par as $key=>$val) {
         $$key=$val;
         if($val>0) $sql.=" AND $key='$val'\n";
      }
   } elseif($filtre!='') $sql.=" AND $par='$filtre'\n";
   $sql.=" order by departement, serie, ann, ne_en, nom, prenoms\n";
   if($limit>0) $sql.=" LIMIT 0, $limit";
   //echo $sql;
   $result=odb_query($sql,__FILE__,__LINE__);
   while($row=mysql_fetch_array($result)) {
      $id_saisie=$row['id_saisie'];
      $tab_candidat[$id_saisie]['id_departement']=$row['departement'];
      $tab_candidat[$id_saisie]['id_ville']=$row['ville'];
      $tab_candidat[$id_saisie]['id_etablissement']=$row['etablissement'];
   }
   return $tab_candidat;
}

/** Recupere le plus grand id_table disponible pour le centre de composition
 *
 * @param string $annee
 * @param int $idCentre
 * @param string $typeSalle : type de salle (facultatif)
 * @return string : id_table ('' si aucun id_table n'existe dans ce centre)
 */
function getIdTableMax($annee,$idCentre,$typeSalle='') {
	$sql="SELECT max(id_table) id_table\n from odb_repartition\n"
		. " where id_table like '$idCentre-$typeSalle%' and annee=$annee";
	$result=odb_query($sql,__FILE__,__LINE__);
	$row=mysql_fetch_array($result);
	$id=$row['id_table'];
	return $id;
}

/** Recupere le rang (numero de salle et de table) d'un candidat dans une salle
 * 
 * @param string $annee
 * @param int $idCentre : centre de composition
 * @param string $typeSalle : type de salle
 * @return array : rang du candidat (numSalle=>rangCandidatDansSalle)
 */
function getRangCandidatDansSalle($annee,$idCentre,$typeSalle) {
	$idTableMax=getIdTableMax($annee,$idCentre,$typeSalle);
	if($idTableMax=='') {
		$salle=$typeSalle.'001';
	} else list($id,$salle,$num)=explode('-',$idTableMax);
	$numSalle=(int)substr($salle,strlen($typeSalle));
	//echo "salle $salle ($idCentre/$typeSalle)<br/>";
	
	// combien y a-t-il de candidats dans cette salle ?
	$sql="SELECT COUNT(*) nbCan FROM odb_repartition\n WHERE id_table like '$idCentre-$salle-%' and annee=$annee";
	$result=odb_query($sql,__FILE__,__LINE__);
	$row=mysql_fetch_array($result);
	$rangCandidatDansSalle=$row['nbCan'];
	
	// quel est le plus grand numero (4 derniers chiffres) pour ce centre ?
	$sql="SELECT MAX(SUBSTRING(id_table,".(1+strlen("$idCentre-$salle-")).")) numero\n"
		. " FROM odb_repartition\n WHERE annee=$annee AND id_table LIKE '$idCentre-%'";
	//echo $sql;
	$result=odb_query($sql,__FILE__,__LINE__);
	$row=mysql_fetch_array($result);
	$numero=$row['numero'];
	$tRang=array(
		'rang'=>$rangCandidatDansSalle,
		'salle'=>$salle,
		'numSalle'=>$numSalle,
		'numero'=>$numero
	);
	//if($typeSalle!='') echo "<br/>tRang<pre>";print_r($tRang);echo"</pre>";
	return $tRang;
}

/** Cree le numero de table du candidat id_saisie
 *
 */
function setRepartition($annee,$idCentre,$id_saisie,$id_table,$id_salle,$num_salle,$cptCan) {
	//echo "Candidat <b>".($cptCan+($num_salle-1)*$capacite_salle)."</b> : $id_table - $capacite_salle - $capacite_type - salle $id_salle ($salle)<hr size=1/>\n";
	$sql="INSERT into odb_repartition (`id_saisie`, `id_table`, `annee`, `id_etablissement`, `id_salle`, `num_salle`, `numero`)\n"
		. " VALUES ($id_saisie,'$id_table',$annee,$idCentre,$id_salle,$num_salle,$cptCan)"
		;
	//echo "<hr/>$sql\n";
	odb_query($sql,__FILE__,__LINE__);
}
/** Synchronise les id_table de la table odb_repartition vers la table odb_candidats
 * @param string $annee
 */
function setSynchroIdTableRepartition2Candidats($annee) {
	$sql="UPDATE odb_candidats can, odb_repartition rep set can.id_table=rep.id_table where can.id_saisie = rep.id_saisie and can.annee=$annee and rep.annee=$annee";
	odb_query($sql,__FILE__,__LINE__);
}

/** Recupere le nombre de candidast pour un departement et/ou une serie donnee
 * @param string $annee
 * @param int $idDept (nul si tout departement)
 * @param int $idSerie (nul si toute serie)
 * @return int : nombre de candidats
 */
function getNbCandidats($annee,$idDept=0,$idSerie=0) {
	$where='';
	if($idDept>0) $where.=" AND departement=$idDept";
	if($idSerie>0) $where.=" AND serie=$idSerie";
	$sql="SELECT count(*)\n from odb_candidats\n WHERE annee=$annee $where";
	$result=odb_query($sql,__FILE__,__LINE__);
	$row=mysql_fetch_array($result);
	$nbCandidats=$row[0];
	return (int)$nbCandidats;
}

/** Recupere le nombre de candidats NON REPARTIS (sans id_table) pour un departement et/ou une serie donnee
 * @param string $annee
 * @param int $idDept (nul si tout departement)
 * @param int $idSerie (nul si toute serie)
 * @return int : nombre de candidats
 */
function getNbCandidatsARepartir($annee,$idDept=0,$idSerie=0) {
	$where='';
	if($idDept>0) $where.=" AND departement=$idDept";
	if($idSerie>0) $where.=" AND serie=$idSerie";
	$sql="SELECT count(*)\n from odb_candidats\n WHERE id_table='0' and annee=$annee $where";
	$result=odb_query($sql,__FILE__,__LINE__);
	$row=mysql_fetch_array($result);
	$nbCandidats=$row[0];
	return (int)$nbCandidats;
}



/** Recupere le nombre de candidats dans chaque serie pour une salle donnee
 * 
 * @param string $annee
 * @param int $idCentre : centre de composition
 * @param string $typeSalle : type de salle dans lequel on cherche l'information
 * @return array : tableau des series=>nbCandidats
 */
function getSeriesDansSalle($annee,$idCentre,$typeSalle) {
	$idTableMax=getIdTableMax($annee,$idCentre,$typeSalle);
	if($idTableMax=='') return false;
	else {
		list($centre,$numSalle,$numCan)=explode('-',getIdTableHumain($idTableMax));
		$salle=$numSalle[0];
		$numSalle=substr($numSalle,1);
	}
	
	$sql="SELECT ser.serie, count(*) nbCan\n"
		. " FROM odb_candidats can, odb_ref_serie ser, odb_repartition rep, odb_ref_salle sal\n"
		. " WHERE can.annee=$annee and rep.annee=$annee and sal.annee=$annee and can.id_saisie=rep.id_saisie and rep.id_etablissement=$idCentre\n"
		. " and ser.id=can.serie and sal.id=rep.id_salle and salle='$typeSalle' and num_salle=$numSalle\n"
		. " GROUP BY ser.serie,sal.salle\n ORDER BY ser.serie";
	$result=odb_query($sql,__FILE__,__LINE__);
	while($row=mysql_fetch_array($result)) {
		$serie=$row['serie'];
		$tSeries[$serie]=$row['nbCan'];
	}
	//echo"<hr/>$sql<br/>";print_r($tSeries);
	return $tSeries;
}

/** Recupere le nombre de candidats dans chaque serie pour un centre donne
 * 
 * @param string $annee
 * @param int $idCentre : centre de composition
 * @param string $typeSalle : type de salle dans lequel on cherche l'information
 * @return array : tableau des series=>nbCandidats
 */
function getSeriesDansCentre($annee,$idCentre,$typeSalle='') {
	$where='';$from='';
	if($typeSalle!='') {
		$where=" AND salle='$typeSalle' and sal.id=rep.id_salle and sal.annee=$annee ";
		$from=", odb_ref_salle sal";
	}
	$sql="SELECT ser.serie, count(*) nbCan\n"
		. " FROM odb_candidats can, odb_repartition rep, odb_ref_serie ser $from\n"
		. " WHERE can.annee=$annee and rep.annee=$annee and can.id_saisie=rep.id_saisie\n"
		. " and ser.id=can.serie and rep.id_etablissement=$idCentre $where\n"
		. " GROUP BY ser.serie\n ORDER BY ser.serie";
	$result=odb_query($sql,__FILE__,__LINE__);
	while($row=mysql_fetch_array($result)) {
		$serie=$row['serie'];
		$tSeries[$serie]=$row['nbCan'];
	}
	//echo"<hr/>$sql<br/>";print_r($tSeries);
	return $tSeries;	
}

/**
 * @param int idEtablissement
 * @return string : libelle etablissement
 */
function getLibelleEtablissement($idEtablissement) {
	$sql="SELECT etablissement from odb_ref_etablissement\n WHERE id=$idEtablissement";
	$result=odb_query($sql,__FILE__,__LINE__);
	$row=mysql_fetch_array($result);
	$lib=$row[0];
	return $lib;
}

function getIdVilleEtablissement($idEtablissement) {
	$sql="SELECT id_ville from odb_ref_etablissement\n WHERE id=$idEtablissement";
	$result=odb_query($sql,__FILE__,__LINE__);
	$row=mysql_fetch_array($result);
	$idVille=$row[0];
	return $idVille;	
}

function getLibelleVille($idVille) {
	$sql="SELECT ville from odb_ref_ville\n WHERE id=$idVille";
	$result=odb_query($sql,__FILE__,__LINE__);
	$row=mysql_fetch_array($result);
	$lib=$row[0];
	return $lib;	
}
?>
