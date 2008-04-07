<?php

/** Recupere le nombre de candidats selon divers criteres
 * 
 * @param string $annee : annee
 * @param int $jury : jury (facultatif)
 * @param string $type : type d'epreuve (Oral, Ecrit, Pratique, Divers - Facultatif)
 * @return int : nombre de candidats
 */
function getNbCandidats($annee,$jury=0,$type='') {
    if($jury>0) $where=" and jury=$jury";
    $sql="SELECT count(*) nb from odb_repartition where annee=$annee $where";
    if($type!='')
        $sql="SELECT count(*) nb FROM odb_notes notes, odb_repartition rep\n"
            ."WHERE rep.id_table=notes.id_table and notes.annee=$annee and rep.annee=$annee and notes.type='$type' and rep.jury=$jury and note is not null";
    $result=odb_query($sql,__FILE__,__LINE__);
    $nbCandidats=mysql_result($result,0,0);
    return $nbCandidats;
}

/** Recupere le nombre notes saisies pour un jury / une serie (/ une matiere)
 *
 */
function getNbCandidatsNotes($annee,$jury,$id_serie,$id_matiere=0,$type='') {
    $where='';
    if($id_matiere!=0) $where.=" AND id_matiere=$id_matiere and note is not null";
    if($type!='') $where.=" AND type='$type'";
    $sql="SELECT count(*) nb, id_matiere FROM odb_notes WHERE annee=$annee and jury=$jury and id_serie=$id_serie $where GROUP BY id_matiere LIMIT 0,1";
    $result=odb_query($sql,__FILE__,__LINE__);
    if(mysql_num_rows($result)>0)
        $nb=mysql_result($result,0,0);
    else $nb=0;
    if($type=='Divers' && $id_matiere!=0) die(KO." - Veuillez utiliser les fonctions getNbCandidatsEPS et getNbCandidatsEF");
    return $nb;
}

function getNbCandidatsEPS($annee,$jury,$id_serie=0) {
	if($id_serie>0) $where=" and can.serie=$id_serie";
	else $where='';
	$sql="SELECT count(*) FROM odb_candidats can, odb_decisions decis, odb_repartition rep, odb_ref_eps eps\n"
		."WHERE can.annee=$annee and decis.annee=$annee and rep.annee=$annee and can.id_table=rep.id_table and can.id_table=decis.id_table"
		." $where and rep.jury=$jury and can.eps=eps.id and eps.eps='Apte' and decis.delib1='Admissible'"
		;
    $result=odb_query($sql,__FILE__,__LINE__);
    if(mysql_num_rows($result)>0)
        $nb=mysql_result($result,0,0);
    else $nb=0;
	return $nb;	
}

function getNbCandidatsEF($annee,$jury,$id_matiere,$id_serie=0) {
	$where='';
	if($id_serie>0) $where.=" and can.serie=$id_serie";
	$ef=abs($id_matiere);
	$sql="SELECT count(*) FROM odb_candidats can, odb_decisions decis, odb_repartition rep\n"
		."WHERE can.annee=$annee and decis.annee=$annee and rep.annee=$annee and can.id_table=rep.id_table and can.id_table=decis.id_table"
		." $where and rep.jury=$jury and can.ef$ef<>0 and decis.delib1='Admissible'"
		;
    $result=odb_query($sql,__FILE__,__LINE__);
    if(mysql_num_rows($result)>0)
        $nb=mysql_result($result,0,0);
    else $nb=0;
	return $nb;	
}

/** Recupere le nombre de notes saisies pour un type donne */
function getNbNotesSaisiesType($annee,$type,$jury) {
	$sql="SELECT count(*) from odb_notes where annee=$annee and jury=$jury and type='$type' and note IS NOT NULL";
	$result=odb_query($sql,__FILE__,__LINE__);
	$nb=mysql_result($result,0,0);
	if($nb>0) return $nb;
	else return 0;
}

/** Recupere le nombre de notes a saisir pour un type donne */
function getNbNotesASaisirType($annee,$type,$jury,$id_serie=0) {
	if($id_serie>0) {
		$where_notes=" and notes.id_serie=$id_serie";
		$where_can=" and can.serie=$id_serie";
		$where_exa=" and exa.id_serie=$id_serie";
	}
	if($id_matiere>0) $where_notes.=" and notes.id_matiere=$id_matiere";
	switch($type) {
		case 'Ecrit':
		case 'Pratique': 
			$sql="SELECT count(*) from odb_notes notes where annee=$annee and jury=$jury and type='$type' $where_notes";
			$result=odb_query($sql,__FILE__,__LINE__);
			$nb=mysql_result($result,0,0);
			break;
		case 'Divers':
			$nb=getNbCandidatsEPS($annee,$jury,$id_serie)+getNbCandidatsEF($annee,$jury,1,$id_serie)+getNbCandidatsEF($annee,$jury,2,$id_serie);
			break;
		case 'Oral':
			// le nombre de notes a saisir est le nombre de candidats devant passer l'oral du jury FOIS le nombre de matieres d'oral pour cette serie
			$sql="SELECT count(*) from odb_candidats can, odb_decisions decis\n"
				." where can.annee=$annee and decis.annee=$annee and can.id_table=decis.id_table $where_can and (decis.delib2='Oral' or decis.delib2='Reserve')"
				;
			$result=odb_query($sql,__FILE__,__LINE__);
			$nb=mysql_result($result,0,0);
			$sql="SELECT count(*) FROM odb_ref_examen exa where type='$type' and annee=$annee $where_exa";
			$result=odb_query($sql,__FILE__,__LINE__);
			$nbMatieres=mysql_result($result,0,0);
			$nb*=$nbMatieres; 
			break;
		default:die(KO." - type $type inconnu");
	}
	//echo "$type : $sql<br/>";
	if($nb>0) return $nb;
	else return 0;
}


/** Recupere un tableau de jury
 *
 * @global $gauche, $deliberationCentre
 * @param string $login : login dont on veut recuperer les jurys
 * @param string $statutUtilisateur : statut de l'utilisateur dont on veut recuperer les jurys
 * @param string $annee : annee
 * @return array : tableau de jurys
 */
function getJurys($login,$statutUtilisateur,$annee) {
    global $gauche, $deliberationCentre;
    //echo "Jurys $login / $statutUtilisateur / $annee :";
    if($statutUtilisateur=='Notes') {
            $sql='SELECT id_deliberation, deliberation deliberationCentre, jury1, jury2, jury3, jury4 '
            . 'from odb_ref_operateur ope, odb_ref_deliberation delib '
            . "where ope.annee=$annee and delib.id=ope.id_deliberation "
            . "and LCASE(operateur)='".substr($login,0,strlen($login)-1).'\'';
            //echo "<br/>$sql<br/>";
            $result=odb_query($sql,__FILE__,__LINE__);
            $row=mysql_fetch_array($result);
            
            foreach (array('deliberationCentre','jury1','jury2','jury3','jury4') as $col) {
                    $$col=$row[$col];
            }
            foreach(array('jury1','jury2','jury3','jury4') as $col) {
                    if($$col>0) {
                            $tJurys[]=$$col; $msgJurys[]="<A HREF='".generer_url_ecrire('odb_notes')."&jury=".$$col."&step2=manuel'>".$$col."</A>";
                    }
            }
            $gauche.='<b>'.ODB_BIO_OPERATEUR."</b><br/>$deliberationCentre<hr size=1/>\nVous avez acc&egrave;s aux jurys ".implode(', ',$msgJurys);

    } elseif($statutUtilisateur=='Admin') {
            $gauche.='<b>Administrateur SIOU</b><br/>Vous avez acc&egrave;s &agrave; tous les jurys';
            $deliberationCentre='B&eacute;nin';
            $sql="SELECT min(jury) mini, max(jury) maxi from odb_repartition where annee='$annee'";
            $result=odb_query($sql,__FILE__,__LINE__);
            $row=mysql_fetch_array($result);
            $tJurys=range((int)$row['mini'],(int)$row['maxi']);
    } else die(KO.' statut incorrect : '.$statutUtilisateur);
    return $tJurys;
}

/** Recupere le tableau des series (idSerie=>serie) d'un jury
 * @param string $jury
 * @param string $annee
 * @return array : tableau de series idSerie=>serie
 */
function getSeriesFromJury($jury, $annee) {
    $sql="SELECT DISTINCT ser.id id_serie, ser.serie\n FROM odb_ref_serie ser, odb_repartition rep, odb_candidats can\n".
    " WHERE can.id_saisie = rep.id_saisie AND jury = $jury AND ser.id = can.serie and rep.annee=$annee and can.annee=$annee\n ORDER BY ser.serie";
    $result=odb_query($sql,__FILE__,__LINE__);
    while($row=mysql_fetch_array($result)) {
        $sSerie=$row['serie'];
        $iIdSerie=$row['id_serie'];
        $tSeries[$iIdSerie]=$sSerie;
    }

    return $tSeries;
}

/** Recupere le tableau jury=>serie de l'annee
 * @param string $annee
 * @return array : tableau de series par jury
 */
function getSeries($annee) {
    $sql="SELECT DISTINCT jury, ser.id id_serie, ser.serie\n FROM odb_ref_serie ser, odb_repartition rep, odb_candidats can\n".
        " WHERE can.id_saisie = rep.id_saisie AND ser.id = can.serie and rep.annee=$annee and can.annee=$annee\n ORDER BY jury, ser.serie";
    $result=odb_query($sql,__FILE__,__LINE__);
    while($row=mysql_fetch_array($result)) {
        $jury=$row['jury'];
        $tSeries[$jury][]=$row['serie'];
    }
    return $tSeries;
}

function getIdAnonyme($annee, $id_table) {
	$sql="SELECT id_anonyme from odb_notes where id_table='$id_table' and annee=$annee limit 0,1";
	$result=odb_query($sql,__FILE__,__LINE__);
	$id_anonyme=mysql_result($result,0,0);
	return $id_anonyme;
}
?>
