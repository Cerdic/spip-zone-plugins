<?php
/**
 * Affiche les notes d'un candidat
 *
 * @param int $jury : numero de jury dont on souhaite afficher les resultats
 * @param int $id_serie : identifiant de la serie dont on souhaite afficher les resultats
 * @param string $where : clause WHERE de la requete SQL (remplace jury et id_serie si
 * @param int $deliberation : numero de deliberation (1 par defaut) si 1 affichage anonyme (>2 : on affiche numeros de table, filtre aux candidats correspondants)
 * @param boolean $isVoiler : vrai si la colonne de la moyenne doit etre masquee
 * @param int $iPrecision : nombre de chiffres apres la virgule pour l'arrondi de la moyenne
 * @return string : resultats du candidat
 */
function afficherNotes($jury, $id_serie, $annee=0, $where='', $deliberation=1, $isVoiler=false,$iPrecision=1) {
	include_once(DIR_ODB_COMMUN.'inc-html.php');
	global $PDF_A3_PAYSAGE, $PDF_A3_PORTRAIT;
	if($annee==0) $annee=isset($_REQUEST['annee'])?$_REQUEST['annee']:date('Y');
	if($serie=='') {
		$tRefSerie=getReferentiel('serie');
		$serie=$tRefSerie[$id_serie];
	}
	
	for ($i=1; $i<=$deliberation; $i++)	{
		odb_maj_decisions($annee,$jury,$iPrecision,$i);
	}
	
	
	$identifiant='notes.id_table';
	$nbCanTotal=getNbCandidats($annee,$jury);
	if($deliberation==2) {
		$where.=" and rep.id_table in (select id_table from odb_decisions where annee=$annee and delib1='Admissible')";
	}
	if($deliberation==3) {
		$where.=" and rep.id_table in (select id_table from odb_decisions where annee=$annee and delib1='Admissible' and (delib2='Oral' or delib2='Reserve'))";
	}
	$sql = "SELECT dep.departement, eta.etablissement centre, rep.jury, pre.prefixe, can.nom, can.prenoms, can.serie, $identifiant id_candidat, id_matiere, matiere, note, coeff, type"
	. " FROM $from odb_notes notes, odb_ref_matiere mat, odb_ref_serie ser, odb_repartition rep, odb_ref_departement dep, odb_ref_etablissement eta, odb_candidats can"
	. ' LEFT JOIN odb_ref_prefixe pre on can.prefixe=pre.id'
	. " where mat.id=notes.id_matiere and notes.annee=$annee and can.annee=$annee and rep.annee=$annee and notes.id_table=can.id_table and notes.id_table=rep.id_table and ser.id=can.serie "
	. " $where and can.serie=$id_serie and eta.id=rep.id_etablissement and dep.id=eta.id_departement "
	. ' ORDER BY id_candidat';
	if($deliberation==1)
		$sql="SELECT id_anonyme id_candidat, id_matiere, matiere, note, coeff, type\n FROM odb_notes notes, odb_ref_matiere mat\n"
		   . " WHERE annee=$annee and jury=$jury and id_serie=$id_serie and mat.id=notes.id_matiere\n ORDER BY id_candidat"
		   ;

	//echo "deliberation $deliberation : $sql";
	$result=odb_query($sql,__FILE__,__LINE__);
	while($row=mysql_fetch_array($result)) {
		foreach(array('id_candidat','id_matiere','matiere','note','coeff','type','prefixe','nom','prenoms') as $col) $$col=$row[$col];
		while(substr_count($matiere,'  ')>0) $matiere=odb_propre($matiere);
		if($deliberation!=1) {
			$id_candidat=getIdTableHumain($id_candidat);
			$tNom[$id_candidat]=stripslashes("$prefixe <b>".strtoupper($nom)."</b> ".ucwords(strtolower($prenoms)));
		}
		$matiere=ucwords(strtolower($matiere));
		$tNotes[$id_candidat][$type][$matiere]=$note;
		$tMatieres[$type][$matiere]['coeff']=$coeff;
		$tMatieres[$type][$matiere]['id_matiere']=$id_matiere;
		if($note<0) $note='<b>N/C</b>';
		$pdf[$id_candidat][getRewriteString("$type $id_matiere")]=$note;
	}

	$lignes[0]="\n\t<th>R&eacute;sultats</th>\n";
	if($deliberation!=1) $lignes[0].="\n\t<th>Candidat</th>\n";
	$lignes[1]="<td ".($deliberation==1?'':'colspan=2').">Jury $jury s&eacute;rie $serie</td>\n";
	$cptCol=($deliberation==1?2:3);
	if(!is_array($tMatieres))
	return "Aucune note n'a encore &eacute;t&eacute; saisie dans le jury $jury s&eacute;rie $serie (qui contient $nbCanTotal candidats)<br/>\n";
	$diviseurSerie=0;
	$pdf_cols['id']=html_entity_decode('Num&eacute;ro');
	if($deliberation!=1) $pdf_cols['candidat']='Candidat(e)';
	foreach(array('Pratique','Ecrit','Divers','Oral') as $type) {
		$t1=$tMatieres[$type];
		if(count($t1)>0) {
			ksort($t1);
			$lignes[0].="\t<th colspan='".count($t1)."'>$type</th>\n";
			$cptCol+=count($t1);
			$isTypeDejaEcrit=false;
			//echo "$type<pre>";print_r($t1);echo "</pre>\n";
			foreach($t1 as $matiere=>$t2) {
				$coeff=$t2['coeff'];
				$diviseurSerie+=$coeff;
				$id_matiere=$t2['id_matiere'];
				$style='';
				if(strlen($matiere)>10) {
					$matiere2='';
					$style=" style='cursor:help;'";
					$tTmp=explode(' ',$matiere);
					foreach($tTmp as $mot) {
						if(strlen($mot)>6) {
							$mot=substr($mot,0,6).'.';
						}
						$mot.=' ';
						$matiere2.=$mot;
					}
					$matiere_aff="<span title=\"header=[Mati&egrave;re] body=[$matiere]\">$matiere2</span>";
					$pdf_cols[getRewriteString("$type $id_matiere")]=html_entity_decode(utf8_decode("\n$matiere2"))." ($coeff)";
				} else {
					$pdf_cols[getRewriteString("$type $id_matiere")]=html_entity_decode(utf8_decode("\n$matiere"))." ($coeff)";
					$matiere_aff=$matiere;
				}
				if(!$isTypeDejaEcrit) {
					$isTypeDejaEcrit=true;
					$pdf_cols[getRewriteString("$type $id_matiere")]="<b>$type</b>".$pdf_cols[getRewriteString("$type $id_matiere")];
				}
				$lignes[1].="\t<td $style>$matiere_aff ($coeff)</td>\n";
				// on mets N/S aux candidats sans note
				foreach($tNotes as $id=>$tN1) {
					if($tN1[$type][$matiere]=='') {
						if($type!='Divers') {
							$tNotes[$id][$type][$matiere]='N/S';
							$tNS[$type][$matiere][$id]=$id_matiere;
						} else $tNotes[$id][$type][$matiere]='';
					}
				}
			}
		}
	}
	$lignes[0].="\t<th rowspan=2>Moyenne</th>\n\n";
	$cpt=1;
	foreach($tNotes as $id=>$t1) {
		$isDispense[$id]=false;
		$pdf[$id]['id']=$id;
		if($deliberation!=1) $pdf[$id]['candidat']=utf8_decode($tNom[$id]);
		$total[$id]=0;
		$diviseur[$id]=$diviseurSerie;
		$cpt++;
		$sql="SELECT 0 from odb_histo_notes notes where $identifiant='$id' and annee=$annee";
		$result=odb_query($sql,__FILE__,__LINE__);
		$nbHisto=mysql_num_rows($result);
		if($nbHisto>0) {
			if($nbHisto>1) $s='s'; else $s='';
			$urlRetour=urlencode(generer_url_ecrire('odb_notes')."&jury=$jury&id_serie=$id_serie&resultats");
			$msgRetour=urlencode("Retour aux r&eacute;sultats des candidats du jury $jury s&eacute;rie $serie");
			$id_aff="<A HREF='".generer_url_ecrire('odb_notes')."&historique&id=$id&annee=$annee&msgRetour=$msgRetour&urlRetour=$urlRetour'".
			" title=\"header=[$id : $nbHisto modif$s] body=[Cliquez pour visualiser l'historique de ce candidat]\">$id</A>";
		} else $id_aff=$id;
		$lignes[$cpt]="\t<th>$id_aff</th>\n";
		if($deliberation!=1) $lignes[$cpt].="\t<th>$tNom[$id]</th>\n";
		foreach(array('Pratique','Ecrit','Divers','Oral') as $type) {
			$t2=$t1[$type];
			if(count($t2)>0) {
				ksort($t2);
				$isCasReserve=false;
				foreach($t2 as $matiere=>$note) {
					$id_matiere=$tMatieres[$type][$matiere]['id_matiere'];
					$coeff=$tMatieres[$type][$matiere]['coeff'];
					if($note<0) {
						$note_aff='<span style="color:#f00;font-weight:bold;">N/C</span>';
						$suite='<br/>N\'a pas compos&eacute;';
						$diviseur[$id]-=$coeff;
						$tNbNC[$matiere][]=$id;
					} elseif($note=='N/S') {
						$note_aff='<span style="color:#00f;font-weight:bold;">N/S</span>';
						$suite='<br/>Non saisi';
						$diviseur[$id]-=$coeff;
						$tNbNS[$matiere][]=$id;
					} elseif($note=='') {
						$note_aff='&nbsp;';
						if($id_matiere==ID_MATIERE_EPS) $isDispense[$id]=true;
						$diviseur[$id]-=$coeff;
					} elseif($note==0 && $type!='Divers') {
						$note_aff='<span style="color:#f00;font-weight:bold;">0</span>';
						$suite="<br/><b>$note</b>/20 <small>soit ".($note*$coeff)."/".(20*$coeff)."</small>";
						$tNb0[$id][]=$matiere;
						$isCasReserve=true;
					} else {
						if($note==0 && $id_matiere==ID_MATIERE_EPS) {
							$isCasReserve=true;
							$isAbsEps=true;
							$diviseur[$id]-=$coeff;
						}
						$note_aff=$note;
						$total[$id]+=(int)$coeff*(int)$note;
						if($coeff==0) $total[$id]+=(int)$note; // bonus EF
						$suite="<br/><b>$note</b>/20 <small>soit ".($note*$coeff)."/".(20*$coeff)."</small>";
					}
					$lignes[$cpt].="\t<td style='cursor:help;' title=\"header=[Candidat $id] body=[<b>$matiere</b> ($type)$suite]\">$note_aff</td>\n";
				}
			}
		}
		if($diviseur[$id]==0) {
			$moy=-1;
			$moy_aff='Absent';
			$style="color:#f00;";
			$isAbsent=true;
		} else {
			$moy=round($total[$id]/($diviseur[$id]),$iPrecision);
			if($diviseur[$id]!=$diviseurSerie) {
				if($diviseur[$id]==($diviseurSerie-1) && ($isDispense[$id] || $isAbsEps))
					$isAbsent=false;
				else $isAbsent=true;
			} else $isAbsent=false;
			if($moy<5) {
				$style="color:#f00;font-weight:bold;";
				$moy_aff="Ajourn&eacute; ($moy)";
			} elseif($moy<9) {
				$style="color:#f00;";
				$moy_aff="Refus&eacute; ($moy)";
			} else {
				$moy_aff="$moy";
				if($isAbsent) $style='color:#c80;';
				elseif($isCasReserve && ($moy<10 || $isAbsEps)) {
					$style='color:#369;';
					$moy_aff="<b>Cas r&eacute;serv&eacute;</b> ($moy)";
				}
				else $style='';
			}
			if($isAbsent) {
				$moy_aff="Absent ($moy)";
				$style.='font-weight:bold;';
			}
		}
		$total_aff="<b>$total[$id]</b>/".(20*(int)$diviseur[$id]);
		if($isVoiler) $moy_aff=$total_aff;
		else $pdf[$id]['moy']=html_entity_decode($moy_aff);
		$lignes[$cpt].="\t<td style='$style' title=\"header=[Candidat $id] body=[Total : $total_aff]\">$moy_aff</td>\n";
		if($diviseur[$id]==0) $pdf[$id]['total']='';
		else $pdf[$id]['total']=html_entity_decode($total_aff);
		
		/*if(substr($moy_aff,0,4)!='Adm.') {
			unset($lignes[$cpt]);
			unset($pdf[$id]);
		}*/
	}
	$nbCan=count($tNotes);
	$cpt++;
	$lignes[$cpt++]="<th colspan='$cptCol'>$nbCan candidats sur $nbCanTotal ".$deliberation==1?"(encore ".($nbCanTotal-$nbCan).")":''."</th>\n";
	$str=odb_html_table("R&eacute;sultats des candidats du jury $jury dans la s&eacute;rie $serie",$lignes,'','petition-24.gif');
	$nom_pdf=getRewriteString("Resultats Jury $jury-Serie $serie");
	$_SESSION['data'][$nom_pdf]=$pdf;
	if($deliberation==1) $sTmp='Brouillard'; else $sTmp="Relev&eacute; de notes - $deliberation&deg; d&eacute;lib&eacute;ration";
	$_SESSION['pied'][$nom_pdf]=html_entity_decode("$sTmp jury $jury - s&eacute;rie $serie");
	$_SESSION['titre'][$nom_pdf]=html_entity_decode("$sTmp - Jury $jury, s&eacute;rie $serie");
	$pdf_cols['total']='<b>Total</b>';
	$pdf_cols['moy']='<b>Moyenne</b>';
	$_SESSION['cols'][$nom_pdf]=$pdf_cols;
	$_SESSION['options'][$nom_pdf]=$PDF_A3_PAYSAGE;
	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
	$tmp2=" G&eacute;n&eacute;rer le $sTmp du jury <b>$jury</b> s&eacute;rie <b>$serie</b> en PDF</A><br/><br/>";
	$lien=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer le $sTmp du jury $jury s&eacute;rie $serie").$tmp2;
	if(is_array($tNb0)) {
		$nbTmp=count($tNb0);
		if($nbTmp>1) $msgTmp="candidats ont";
		else $msgTmp="candidat a";
		$msg0="<b>$nbTmp</b> $msgTmp eu au moins un 0 :<br/>\n".
		"<table class='spip'>\n<tr><th>Candidat</th><th>Mati&egrave;res</th></tr>\n";
		foreach($tNb0 as $id=>$tTmp)
		$msg0.="<tr>\n\t<td>$id</td>\n\t<td>".implode(', ',$tTmp)."</td>\n</tr>\n";
		$msg0.="</table>\n";
	}
	if(is_array($tNbNS)) {
		$nbMat=count($tNbNS);
		if($nbMat>1) $msgTmp="des <b>$nbMat</b>  mati&egrave;res suivantes :";
		else $msgTmp="de la mati&egrave;re suivante :";
		$msgNS="Les notes doivent &ecirc;tre saisies pour le(s) candidat(s) $msgTmp<br/>\n".
		"<table class='spip'>\n<tr><th>Mati&egrave;re</th><th>Candidats</th></tr>\n";
		foreach($tNbNS as $matiere=>$tTmp) {
			$id_matiere=$tMatieres['Ecrit'][$matiere]['id_matiere'];
			$nbTmp=count($tTmp);
			$msgTmp="<b>$nbTmp</b> candidats";
			if($nbTmp<=10) $msgTmp.=" :<br/>".implode(', ',$tTmp);
			$msgNS.="<tr>\n\t<td>".
			"<b><A HREF='".generer_url_ecrire('odb_notes')."&jury=$jury&serie=$serie&matiere=$matiere&id_matiere=$id_matiere&id_serie=$id_serie&type=Ecrit&step3=manuel'>$matiere</A></b>\n".
			"</td>\n\t<td>$msgTmp</td>\n</tr>\n";
		}
		$msgNS.="</table>\n";
	}
	if(is_array($tNbNC)) {
		$nbMat=count($tNbNC);
		if($nbMat>1) $msgTmp="les <b>$nbMat</b>  mati&egrave;res suivantes :";
		else $msgTmp="la mati&egrave;re suivante :";
		$msgNC="Un ou plusieurs candidat(s) n'ont pas compos&eacute; dans $msgTmp<br/>\n".
		"<table class='spip'>\n<tr><th>Mati&egrave;re</th><th>Candidats</th></tr>\n";
		foreach($tNbNC as $matiere=>$tTmp) {
			$nbTmp=count($tTmp);
			$msgTmp="<b>$nbTmp</b> candidats";
			if($nbTmp<=10) $msgTmp.=" :<br/>".implode(', ',$tTmp);
			$msgNC.="<tr>\n\t<td>$matiere</td>\n\t<td>$msgTmp</td>\n</tr>\n";
		}
		$msgNC.="</table>\n";
	}
	$msgInfo='';
	if(trim($msgNS)!='') $msgInfo=boite_important($msgNS);
	$msgInfo.=$msg0.$msgNC;
	return $msgInfo.$lien.$str.$lien;
}

/**
 * Permet aux operateurs de saisie d'acceder aux listes de resultat en fonction
 * des autorisations cochées dans la partie 'gestion des deliberations' (config)
 *
 * @param int $jury
 * @param string $serie
 * @param int $annee
 * @param array $tSeries : tableau des series de ce jury
 * @param $iPrecision : nb de chiffres apres la virgule pour les calculs de moyenne
 * @return string
 */
function afficherImpressions($jury,$serie,$annee,$tSeries,$iPrecision=3) {
	$tParam=getParametresODB();
	$deliberation=guessDeliberation($annee, $jury, $tParam);
	odb_maj_decisions($annee,$jury,$iPrecision,1);
	$msg="<TABLE class='spip'/>";
	$msg.="<tr><td colspan=3><hr size=1/><b>1<sup>&egrave;re</sup></b> d&eacute;lib&eacute;ration<hr size=1/></td></tr>\n";
	$sql="SELECT decis.id_table, sex.sexe, pre.prefixe, nom, prenoms, eta.etablissement, can.serie idSerie, eps.eps, ef1.ef ef1, ef2.ef ef2\n".
	"FROM odb_ref_eps eps, odb_ref_sexe sex, odb_ref_etablissement eta, odb_decisions decis, odb_repartition rep, odb_candidats can\n".
	"left join odb_ref_prefixe pre on pre.id=can.prefixe\n".
	"left join odb_ref_ef ef1 on can.ef1=ef1.id \n".
	"left join odb_ref_ef ef2 on can.ef2=ef2.id \n".
	"WHERE rep.id_table=decis.id_table and can.id_table=decis.id_table and can.sexe=sex.id and can.etablissement=eta.id\n".
	" and decis.delib1='Admissible' and can.annee=$annee and decis.annee=$annee and rep.annee=$annee and rep.jury=$jury\n".
	" and can.eps=eps.id\n".
	"ORDER BY nom, prenoms";
	//echo "<pre>".str_replace($pass,'*****',$sql)."</pre>";
	$result=odb_query($sql,__FILE__,__LINE__);
	$cpt=0;
	while($row=mysql_fetch_array($result)) {
		$cpt++;
		foreach(array('id_table','sexe','prefixe','nom','prenoms','etablissement','idSerie','ef1','ef2','eps') as $col) $$col=$row[$col];
		$id_table=getIdTableHumain($id_table);
		$tDelib1[$idSerie][$cpt]['id_table']=$id_table;
		$nom=$prefixe." <b>$nom</b>";
		if($sexe=='F') $sexe='Mlle'; else $sexe='M.';
		$tDelib1[$idSerie][$cpt]['candidat']=utf8_decode("$sexe $nom $prenoms");
		$tDelib1[$idSerie][$cpt]['etablissement']=utf8_decode($etablissement);
		if($eps=='Apte') {
			$tDelib1Eps[$cpt]['id_table']=$id_table;
			$tDelib1Eps[$cpt]['candidat']=utf8_decode("$sexe $nom $prenoms");
			$tDelib1Eps[$cpt]['eps']=utf8_decode($eps);
			$tDelib1Eps[$cpt]['etablissement']=utf8_decode($etablissement);
		}
		if($ef1!='' || $ef2!='') {
			$tDelib1EF[$cpt]['id_table']=$id_table;
			$tDelib1EF[$cpt]['candidat']=utf8_decode("$sexe $nom $prenoms");
			$tDelib1EF[$cpt]['Ep_fac_1']=utf8_decode($ef1);
			$tDelib1EF[$cpt]['Ep_fac_2']=utf8_decode($ef2);
			$tDelib1EF[$cpt]['etablissement']=utf8_decode($etablissement);
		}
	}
	$msg.="<script type='text/javascript'>function bon_format_date(chaine) {\n".
	'var exp=new RegExp("^[0-9]{1,2}\/[01]?[0-9]\/[0-9]{4}$","g");return exp.test(chaine);}</script>';
	//echo"<pre>";print_r($tDelib1);echo"</pre>\n";
	include_once(DIR_ODB_COMMUN.'inc-html.php'); // pour getRewriteString et vignette
	foreach($tSeries as $iIdSerie=>$sSerie) {
		$nom_pdf=getRewriteString("Resultats Jury $jury-Serie $sSerie - deliberation 1");
		$_SESSION['cols'][$nom_pdf]=array('id_table'=>html_entity_decode('Num&eacute;ro table'),'candidat'=>'Candidat','etablissement'=>html_entity_decode('&Eacute;tablissement'));
		$_SESSION['format'][$nom_pdf]=array('taille'=>'A3','orientation'=>'portrait');
		$_SESSION['options'][$nom_pdf]=$PDF_A3_PORTRAIT;
		$_SESSION['data'][$nom_pdf]=$tDelib1[$iIdSerie];
		$_SESSION['pied'][$nom_pdf]=html_entity_decode("Liste des admissibles (1&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury - s&eacute;rie $sSerie");
		$_SESSION['titre'][$nom_pdf]=html_entity_decode("Admissibles (1&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury - s&eacute;rie $sSerie");
		$_SESSION['post'][$nom_pdf]=html_entity_decode("Le pr&eacute;sident du jury,");
		$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
		$tmp2=" <b>1<sup>&egrave;re</sup> d&eacute;lib&eacute;ration</b><br/>Jury $jury s&eacute;rie <b>$sSerie</b></A><br/><br/>";
		$lien=$tmp1.$tmp2;
		$msg.="<tr><td>".vignette('pdf',"1&deg; d&eacute;lib&eacute;ration jury $jury s&eacute;rie $sSerie")."</td><td colspan=2>$lien</td></tr>\n";
	}
	// EPS
	$nom_pdf=getRewriteString("Admissibles eps jury $jury-Serie $sSerie deliberation 1");
	$_SESSION['cols'][$nom_pdf]=array('id_table'=>html_entity_decode('Num&eacute;ro table'),'candidat'=>'Candidat','eps'=>'EPS','etablissement'=>html_entity_decode('&Eacute;tablissement'));
	$_SESSION['format'][$nom_pdf]=array('taille'=>'A3','orientation'=>'portrait');
	$_SESSION['options'][$nom_pdf]=$PDF_A3_PORTRAIT;
	$_SESSION['data'][$nom_pdf]=$tDelib1Eps;
	$_SESSION['pied'][$nom_pdf]=html_entity_decode("Liste des admissibles aptes en EPS (1&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury");
	$_SESSION['titre'][$nom_pdf]=html_entity_decode("Admissibles aptes en EPS (1&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury");
	$_SESSION['post'][$nom_pdf]=html_entity_decode("Le pr&eacute;sident du jury,");
	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
	$tmp2=" <b>Admissibles aptes en EPS<br/>1<sup>&egrave;re</sup> d&eacute;lib&eacute;ration</b> jury $jury</A><br/><br/>";
	$lien=$tmp1.$tmp2;
	$msg.="<tr><td>".vignette('pdf',"Admissibles aptes en EPS - 1&deg; d&eacute;lib&eacute;ration jury $jury")."</td><td colspan=2>$lien</td></tr>\n";
	// EF
	$nom_pdf=getRewriteString("Admissibles ef jury $jury-Serie $sSerie deliberation 1");
	$_SESSION['cols'][$nom_pdf]=array(
		'id_table'=>html_entity_decode('Num&eacute;ro table'),
		'candidat'=>'Candidat',
		'Ep_fac_1'=>html_entity_decode('&Eacute;p. Fac. 1'),
		'Ep_fac_2'=>html_entity_decode('&Eacute;p. Fac. 2'),
		'etablissement'=>html_entity_decode('&Eacute;tablissement')
	);
	$_SESSION['format'][$nom_pdf]=array('taille'=>'A3','orientation'=>'portrait');
	$_SESSION['options'][$nom_pdf]=$PDF_A3_PORTRAIT;
	$_SESSION['data'][$nom_pdf]=$tDelib1EF;
	$_SESSION['pied'][$nom_pdf]=html_entity_decode("&Eacute;preuves facultatives des admissibles (1&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury");
	$_SESSION['titre'][$nom_pdf]=html_entity_decode("&Eacute;preuves facultatives des admissibles (1&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury");
	$_SESSION['post'][$nom_pdf]=html_entity_decode("Le pr&eacute;sident du jury,");
	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
	$tmp2=" <b>&Eacute;preuves facultatives des admissibles<br/>1<sup>&egrave;re</sup> d&eacute;lib&eacute;ration</b> jury $jury</A><br/><br/>";
	$lien=$tmp1.$tmp2;
	$msg.="<tr><td>".vignette('pdf',"&Eacute;preuves facultatives des admissibles - 1&deg; d&eacute;lib&eacute;ration jury $jury")."</td><td colspan=2>$lien</td></tr>\n";
	// Non admissibles
	$aujourdhui=date('xx/m/Y');
	$verif="onSubmit=\"if(document.forms['form_jury'].nom_jury.value=='' || document.forms['form_jury'].nom_jury.value=='Nom')\n".
	" {alert('Veuillez saisir le nom du president du jury');return false;}\n".
	"if(document.forms['form_jury'].lieu_jury.value=='' || document.forms['form_jury'].lieu_jury.value=='Lieu')\n".
	" {alert('Veuillez saisir la ville du centre de deliberation');return false;}\n".
	"if(!bon_format_date(document.forms['form_jury'].date_jury.value))\n".
	" {alert('Veuillez saisir une date de deliberation correcte');return false;}\n".
	"return true;\"";
	$msg.="<tr><td>".vignette('pdf',"Non admissibles jury $jury")."</td>\n".
	"<form name='form_jury' action='../plugins/odb/odb_commun/inc-pdf-resultats.php' $verif method='POST'>\n".
	"<input type='hidden' name='jury' value='$jury'/>\n".
	"<input type='hidden' name='deliberation' value='1'/>\n".
	"<input type='hidden' name='annee' value='$annee'/>\n".
	"<input type='hidden' name='exec' value='odb_notes'/>\n".
	"<td><small><label for='nom_jury'>Pr&eacute;sident du jury</label></small><br/>\n<input name='nom_jury' class='fondo' size=10 value='Nom' onFocus=\"this.value=''\"/><br/>\n".
	"<small><label for='lieu_jury'>Ville de d&eacute;lib&eacute;ration</label></small><br/><input name='lieu_jury' class='fondo' size=10 value='Lieu' onFocus=\"this.value=''\"/><br/>\n".
	"<small><label for='date_jury'>Date de d&eacute;lib&eacute;ration</label></small><br/><input name='date_jury' class='fondo' size=10 value='$aujourdhui' onFocus=\"this.select();\"/></td>\n".
	"<td><input type='submit' value='Non\nadmissibles\n1e deliberation' class='fondo' /></td></form></tr>\n";

	////////////////////// deliberation 2
	if($deliberation>1) {
		$msg.="<tr><td colspan=3><hr size=1/><b>2<sup>&egrave;me</sup></b> d&eacute;lib&eacute;ration<hr size=1/></td></tr>\n";
		// On verifie si les notes d'EPS et EF sont toutes bien saisies
		$nbNotesASaisir=getNbNotesASaisirType($annee,'Divers',$jury);
		$nbNotesSaisies=getNbNotesSaisiesType($annee,'Divers',$jury);
		$sTmp='<br/>Pour les candidats absents, <b>saisissez 0</b>.';
		if($nbNotesSaisies<$nbNotesASaisir) $msg.="<tr><td colspan=3 style='color:#f00;'>Veuillez saisir toutes les notes d'EPS et EF du jury $jury $sTmp</td></tr>\n";
		else {
			odb_maj_decisions($annee,$jury,$iPrecision,2);
			$sql="SELECT decis.id_table, sex.sexe, pre.prefixe, nom, prenoms, eta.etablissement, can.serie idSerie, decis.delib2 delib\n".
			"FROM odb_ref_sexe sex, odb_ref_etablissement eta, odb_decisions decis, odb_repartition rep, odb_candidats can\n".
			"left join odb_ref_prefixe pre on pre.id=can.prefixe\n".
			"WHERE rep.id_table=decis.id_table and can.id_table=decis.id_table and can.sexe=sex.id and can.etablissement=eta.id\n".
			" and decis.delib1='Admissible' and can.annee=$annee and decis.annee=$annee and rep.annee=$annee and rep.jury=$jury\n".
			"ORDER BY nom, prenoms";
			//echo $sql;
			$result=odb_query($sql,__FILE__,__LINE__);
			$cpt=0;
			while($row=mysql_fetch_array($result)) {
				$cpt++;
				foreach(array('id_table','sexe','prefixe','nom','prenoms','etablissement','idSerie','delib') as $col) $$col=$row[$col];
				$id_table=getIdTableHumain($id_table);
				$nom=$prefixe." <b>$nom</b>";
				if($sexe=='F') $sexe='Mlle'; else $sexe='M.';
				if(in_array(strtolower($delib),array('passable','abien','bien','tbien'))) $cle='Admis';
				else $cle=$delib;
				$tDelib[$cle][$idSerie][$cpt]['id_table']=$id_table;
				$tDelib[$cle][$idSerie][$cpt]['candidat']=utf8_decode("$sexe $nom $prenoms");
				$tDelib[$cle][$idSerie][$cpt]['etablissement']=utf8_decode($etablissement);
				$tDelib[$cle][$idSerie][$cpt]['delib']=utf8_decode($delib);
			}

			foreach($tSeries as $iIdSerie=>$sSerie) {
				$nom_pdf=getRewriteString("Deliberation 2 - Resultats Jury $jury-Serie $sSerie");
				$_SESSION['cols'][$nom_pdf]=array('id_table'=>html_entity_decode('Num&eacute;ro table'),'candidat'=>'Candidat','etablissement'=>html_entity_decode('&Eacute;tablissement'));
				$_SESSION['format'][$nom_pdf]=array('taille'=>'A3','orientation'=>'portrait');
				$_SESSION['options'][$nom_pdf]=$PDF_A3_PORTRAIT;
				$_SESSION['data'][$nom_pdf]=$tDelib['Oral'][$iIdSerie];
				$_SESSION['pied'][$nom_pdf]=html_entity_decode("Liste des admissibles (2&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury - s&eacute;rie $sSerie");
				$_SESSION['titre'][$nom_pdf]=html_entity_decode("Autoris&eacute;s aux &eacute;preuves orales (2&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury - s&eacute;rie $sSerie");
				$_SESSION['post'][$nom_pdf]=html_entity_decode("Le pr&eacute;sident du jury,");
				$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
				$tmp2=" <b>2<sup>&egrave;re</sup> d&eacute;lib&eacute;ration - Oral</b><br/>Jury $jury s&eacute;rie <b>$sSerie</b></A><br/><br/>";
				$lien=$tmp1.$tmp2;
				$msg.="<tr><td>".vignette('pdf',"2&deg; d&eacute;lib&eacute;ration jury $jury s&eacute;rie $sSerie")."</td><td colspan=2>$lien</td></tr>\n";
			}
			unset($tDelib['Admissible']);
			if(is_array($tDelib['Reserve'])) {
				$tReserve=array();
				foreach($tDelib['Reserve'] as $iIdSerie=>$tTmp) $tReserve=array_merge($tReserve,$tTmp);
				unset($tDelib['Reserve']);
				ksort($tReserve);
				$nom_pdf=getRewriteString("Deliberation 2 - Cas reserves Jury $jury");
				$_SESSION['cols'][$nom_pdf]=array('id_table'=>html_entity_decode('Num&eacute;ro table'),'candidat'=>'Candidat','etablissement'=>html_entity_decode('&Eacute;tablissement'));
				$_SESSION['format'][$nom_pdf]=array('taille'=>'A3','orientation'=>'portrait');
				$_SESSION['options'][$nom_pdf]=$PDF_A3_PORTRAIT;
				$_SESSION['data'][$nom_pdf]=$tReserve;
				$_SESSION['pied'][$nom_pdf]=html_entity_decode("Cas r&eacute;serv&eacute;s (2&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury");
				$_SESSION['titre'][$nom_pdf]=html_entity_decode("Cas r&eacute;serv&eacute;s (2&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury");
				$_SESSION['post'][$nom_pdf]=html_entity_decode("Le pr&eacute;sident du jury,");
				$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
				$tmp2=" <b>2<sup>&egrave;re</sup> d&eacute;lib&eacute;ration</b> Cas r&eacute;serv&eacute;s<br/>Jury $jury</A><br/><br/>";
				$lien=$tmp1.$tmp2;
				$msg.="<tr><td>".vignette('pdf',"2&deg; d&eacute;lib&eacute;ration - Cas reserv&eacute;s - jury $jury")."</td><td colspan=2>$lien</td></tr>\n";
			}
			foreach($tSeries as $iIdSerie=>$sSerie) {
				$nom_pdf=getRewriteString("Deliberation 2 - Admis Jury $jury-Serie $sSerie");
				$_SESSION['cols'][$nom_pdf]=array('id_table'=>html_entity_decode('Num&eacute;ro table'),'candidat'=>'Candidat','delib'=>'Mention','etablissement'=>html_entity_decode('&Eacute;tablissement'));
				$_SESSION['format'][$nom_pdf]=array('taille'=>'A3','orientation'=>'portrait');
				$_SESSION['options'][$nom_pdf]=$PDF_A3_PORTRAIT;
				//echo"tDelib<pre>";print_r($tDelib);echo"</pre>";
				$_SESSION['data'][$nom_pdf]=$tDelib['Admis'][$iIdSerie];
				$_SESSION['pied'][$nom_pdf]=html_entity_decode("Admis 1&deg; groupe (2&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury - s&eacute;rie $sSerie");
				$_SESSION['titre'][$nom_pdf]=html_entity_decode("Admis 1&deg; groupe (2&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury - s&eacute;rie $sSerie");
				$_SESSION['post'][$nom_pdf]=html_entity_decode("Le pr&eacute;sident du jury,");
				$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
				$tmp2="<b>Admis 1&deg; groupe</b><br/>Jury $jury s&eacute;rie <b>$sSerie</b></A><br/><br/>";
				$lien=$tmp1.$tmp2;
				$msg.="<tr><td>".vignette('pdf',"Admis 1&deg; groupe - Jury $jury s&eacute;rie $sSerie")."</td><td colspan=2>$lien</td></tr>\n";
			}
			$verif="onSubmit=\"if(document.forms['form_jury_admis1'].nom_jury.value=='' || document.forms['form_jury_admis1'].nom_jury.value=='Nom')\n".
			" {alert('Veuillez saisir le nom du president du jury');return false;}\n".
			"if(document.forms['form_jury_admis1'].lieu_jury.value=='' || document.forms['form_jury_admis1'].lieu_jury.value=='Lieu')\n".
			" {alert('Veuillez saisir la ville du centre de deliberation');return false;}\n".
			"if(!bon_format_date(document.forms['form_jury_admis1'].date_jury.value))\n".
			" {alert('Veuillez saisir une date de deliberation correcte');return false;}\n".
			"return true;\"";
			$msg.="<tr><td>".vignette('pdf',"Admis 1er groupe jury $jury")."</td>\n".
			"<form name='form_jury_admis1' action='../plugins/odb/odb_commun/inc-pdf-resultats.php' $verif method='POST'>\n".
			"<input type='hidden' name='jury' value='$jury'/>\n".
			"<input type='hidden' name='deliberation' value='2'/>\n".
			"<input type='hidden' name='annee' value='$annee'/>\n".
			"<input type='hidden' name='exec' value='odb_notes'/>\n".
			"<td><small><label for='nom_jury'>Pr&eacute;sident du jury</label></small><br/>\n<input name='nom_jury' class='fondo' size=10 value='Nom' onFocus=\"this.value=''\"/><br/>\n".
			"<small><label for='lieu_jury'>Ville de d&eacute;lib&eacute;ration</label></small><br/><input name='lieu_jury' class='fondo' size=10 value='Lieu' onFocus=\"this.value=''\"/><br/>\n".
			"<small><label for='date_jury'>Date de d&eacute;lib&eacute;ration</label></small><br/><input name='date_jury' class='fondo' size=10 value='$aujourdhui' onFocus=\"this.select();\"/></td>\n".
			"<td><input type='submit' value='Admis\n1er groupe' class='fondo' /></td></form></tr>\n";
		}
	}
	////////////////////// deliberation 3
		if($deliberation>2) {
		odb_maj_decisions($annee,$jury,$iPrecision,3);
		$msg.="<tr><td colspan=3><hr size=1/><b>3<sup>&egrave;me</sup></b> d&eacute;lib&eacute;ration<hr size=1/></td></tr>\n";

		$sql="SELECT decis.id_table, sex.sexe, pre.prefixe, nom, prenoms, eta.etablissement, can.serie idSerie, decis.delib3 delib\n".
		"FROM odb_ref_sexe sex, odb_ref_etablissement eta, odb_decisions decis, odb_repartition rep, odb_candidats can\n".
		"left join odb_ref_prefixe pre on pre.id=can.prefixe\n".
		"WHERE rep.id_table=decis.id_table and can.id_table=decis.id_table and can.sexe=sex.id and can.etablissement=eta.id\n".
		" and decis.delib1='Admissible' and (decis.delib2='Oral' or decis.delib2='Reserve') and can.annee=$annee and decis.annee=$annee and rep.annee=$annee and rep.jury=$jury\n".
		"ORDER BY nom, prenoms";
		//echo $sql;
		$result=odb_query($sql,__FILE__,__LINE__);
		$cpt=0;
		$tDelib=array();
		while($row=mysql_fetch_array($result)) {
			$cpt++;
			foreach(array('id_table','sexe','prefixe','nom','prenoms','etablissement','idSerie','delib') as $col) $$col=$row[$col];
			$id_table=getIdTableHumain($id_table);
			$nom=$prefixe." <b>$nom</b>";
			if($sexe=='F') $sexe='Mlle'; else $sexe='M.';
			if(in_array(strtolower($delib),array('passable','abien','bien','tbien'))) $cle='Admis';
			else $cle=$delib;
			$tDelib[$cle][$idSerie][$cpt]['id_table']=$id_table;
			$tDelib[$cle][$idSerie][$cpt]['candidat']=utf8_decode("$sexe $nom $prenoms");
			$tDelib[$cle][$idSerie][$cpt]['etablissement']=utf8_decode($etablissement);
			$tDelib[$cle][$idSerie][$cpt]['delib']=utf8_decode($delib);
		}
		//echo"<pre>";print_r($tDelib);echo"</pre>";
		if(is_array($tDelib['Reserve'])) {
			$tReserve=array();
			foreach($tDelib['Reserve'] as $iIdSerie=>$tTmp) $tReserve=array_merge($tReserve,$tTmp);
			unset($tDelib['Reserve']);
			ksort($tReserve);
			$nom_pdf=getRewriteString("Deliberation 2 - Cas reserves Jury $jury-Serie $sSerie");
			$_SESSION['cols'][$nom_pdf]=array('id_table'=>html_entity_decode('Num&eacute;ro table'),'candidat'=>'Candidat','etablissement'=>html_entity_decode('&Eacute;tablissement'));
			$_SESSION['format'][$nom_pdf]=array('taille'=>'A3','orientation'=>'portrait');
			$_SESSION['options'][$nom_pdf]=$PDF_A3_PORTRAIT;
			$_SESSION['data'][$nom_pdf]=$tReserve;
			$_SESSION['pied'][$nom_pdf]=html_entity_decode("Cas r&eacute;serv&eacute;s (3&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury - s&eacute;rie $sSerie");
			$_SESSION['titre'][$nom_pdf]=html_entity_decode("Cas r&eacute;serv&eacute;s (3&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury - s&eacute;rie $sSerie");
			$_SESSION['post'][$nom_pdf]=html_entity_decode("Le pr&eacute;sident du jury,");
			$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
			$tmp2=" <b>3<sup>&egrave;re</sup> d&eacute;lib&eacute;ration</b> Cas r&eacute;serv&eacute;s<br/>Jury $jury s&eacute;rie <b>$sSerie</b></A><br/><br/>";
			$lien=$tmp1.$tmp2;
			$msg.="<tr><td>".vignette('pdf',"3&deg; d&eacute;lib&eacute;ration - Cas reserv&eacute;s - jury $jury s&eacute;rie $sSerie")."</td><td colspan=2>$lien</td></tr>\n";
		}
		reset($tSeries);
		foreach($tSeries as $iIdSerie=>$sSerie) {
			$nom_pdf=getRewriteString("Deliberation 3 - Admis Jury $jury-Serie $sSerie");
			$_SESSION['cols'][$nom_pdf]=array('id_table'=>html_entity_decode('Num&eacute;ro table'),'candidat'=>'Candidat','delib'=>'Mention','etablissement'=>html_entity_decode('&Eacute;tablissement'));
			$_SESSION['format'][$nom_pdf]=array('taille'=>'A3','orientation'=>'portrait');
			$_SESSION['options'][$nom_pdf]=$PDF_A3_PORTRAIT;
			//echo"tDelib<pre>";print_r($tDelib);echo"</pre>";
			$_SESSION['data'][$nom_pdf]=$tDelib['Admis'][$iIdSerie];
			$_SESSION['pied'][$nom_pdf]=html_entity_decode("Admis 2&deg; groupe (3&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury - s&eacute;rie $sSerie");
			$_SESSION['titre'][$nom_pdf]=html_entity_decode("Admis 2&deg; groupe (3&deg; d&eacute;lib&eacute;ration) - ann&eacute;e $annee - jury $jury - s&eacute;rie $sSerie");
			$_SESSION['post'][$nom_pdf]=html_entity_decode("Le pr&eacute;sident du jury,");
			$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
			$tmp2="<b>Admis 2&deg; groupe</b><br/>Jury $jury s&eacute;rie <b>$sSerie</b></A><br/><br/>";
			$lien=$tmp1.$tmp2;
			$msg.="<tr><td>".vignette('pdf',"Admis 2&deg; groupe - Jury $jury s&eacute;rie $sSerie")."</td><td colspan=2>$lien</td></tr>\n";
		}
		if(is_array($tDelib['Admis'])) {
			$verif="onSubmit=\"if(document.forms['form_jury_admis2'].nom_jury.value=='' || document.forms['form_jury_admis2'].nom_jury.value=='Nom')\n".
			" {alert('Veuillez saisir le nom du president du jury');return false;}\n".
			"if(document.forms['form_jury_admis2'].lieu_jury.value=='' || document.forms['form_jury_admis2'].lieu_jury.value=='Lieu')\n".
			" {alert('Veuillez saisir la ville du centre de deliberation');return false;}\n".
			"if(!bon_format_date(document.forms['form_jury_admis2'].date_jury.value))\n".
			" {alert('Veuillez saisir une date de deliberation correcte');return false;}\n".
			"return true;\"";
			$msg.="<tr><td>".vignette('pdf',"Admis 2e groupe jury $jury")."</td>\n".
			"<form name='form_jury_admis2' action='../plugins/odb/odb_commun/inc-pdf-resultats.php' $verif method='POST'>\n".
			"<input type='hidden' name='jury' value='$jury'/>\n".
			"<input type='hidden' name='deliberation' value='3'/>\n".
			"<input type='hidden' name='annee' value='$annee'/>\n".
			"<input type='hidden' name='exec' value='odb_notes'/>\n".
			"<td><small><label for='nom_jury'>Pr&eacute;sident du jury</label></small><br/>\n<input name='nom_jury' class='fondo' size=10 value='Nom' onFocus=\"this.value=''\"/><br/>\n".
			"<small><label for='lieu_jury'>Ville de d&eacute;lib&eacute;ration</label></small><br/><input name='lieu_jury' class='fondo' size=10 value='Lieu' onFocus=\"this.value=''\"/><br/>\n".
			"<small><label for='date_jury'>Date de d&eacute;lib&eacute;ration</label></small><br/><input name='date_jury' class='fondo' size=10 value='$aujourdhui' onFocus=\"this.select();\"/></td>\n".
			"<td><input type='submit' value='Admis\n2e groupe' class='fondo' /></td></form></tr>\n";
		}
	}
	
	
	$msg.="</table>\n";
	return $msg;
}


/**
 * Affiche l'historique du candidat passé en param
 *
 * @param string $id : identifiant du candidat dont on souhaite connaitre l'historique (detection du type d'identifiant auto)
 * @param string $annee : annee
 * @return string : resultats du candidat
 */
function afficherHistorique($id,$annee) {
	include_once(DIR_ODB_COMMUN.'inc-html.php');
	if((int)substr($id,(strlen($id)-4))>0) {
		$identifiant="notes.id_anonyme";
	} else $identifiant='notes.id_table';
	$sql = "SELECT '$id' id_candidat, id_matiere, matiere, note, coeff, type, operateur, nom, maj\n"
	. " FROM odb_ref_matiere mat, odb_histo_notes notes left join (select nom, login from spip_auteurs) spip_auteurs on(spip_auteurs.login=notes.operateur)\n"
        . " where mat.id=notes.id_matiere and notes.annee=$annee and $identifiant='$id'\n"
	. ' ORDER BY type, matiere';
	$result=odb_query($sql,__FILE__,__LINE__);
	$cpt=0;
	foreach(array('Candidat','Mati&egrave;re','Note','Coeff','Type','Operateur','Date de mise &agrave; jour') as $col)
	$lignes[$cpt].="\t<th>$col</th>\n";
	while($row=mysql_fetch_array($result)) {
		$cpt++;
		$nom=$row['nom'];
		foreach(array('id_candidat','matiere','note','coeff','type','operateur','maj') as $col) {
			$$col=$row[$col];
			if($col=='operateur' && $nom!='') $operateur="$nom ($operateur)";
			$lignes[$cpt].="\t<td>".$$col."</td>\n";
		}
	}
	$msg=odb_html_table("Historique du candidat $id",$lignes,'','historique-24.gif');
	return $msg;
}

/** Devine le numero de deliberation en cours
 * @param string $annee
 * @param int $jury
 * @param array $tParam : tableau des parametres ODB
 * @return int : numero de deliberation
 */
function guessDeliberation($annee,$jury,$tParam) {
    if(isset($tParam["_delib1_$annee"][$jury])) {
    	if(getNbNotesASaisirType($annee,'Divers',$jury)<>getNbNotesSaisiesType($annee,'Divers',$jury))
			$deliberation=2;
		else $deliberation=3;
    } else {
		$deliberation=1;
    }
    //echo "$annee|$jury|".$tParam["_delib1_$annee"][$jury];
    return $deliberation;
}

?>
