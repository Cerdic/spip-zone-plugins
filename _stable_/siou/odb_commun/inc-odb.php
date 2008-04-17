<?php
// Fonctions communes ODB, hors referentiel et affichage/html

/**
 * Recupere les parametres ODB
 *
 * @param string $param : parametre dont on cherche la valeur (facultatif)
 * @return mixed : tableau de parametres ou valeur
 */
function getParametresODB($param='') {
	$isParam=false;
	if($param!='') {
		$where="where param='$param'\n";
		$isParam=true;
	}
	$sql="select param, uncompress(valeur) valeur\n from odb_param\n $where order by param";
	//echo "<pre>$sql</pre>";
	$result=odb_query($sql,__FILE__,__LINE__);
	while($row=mysql_fetch_array($result)) {
		if($isParam) return $row['valeur'];
		$param=$row['param'];
		if($param[0]!='_') {
			$tParam[$param]=$row['valeur']; // les parametres commençant par un _ sont des tableaux serialises
		}
		else {
			$tParam[$param]=unserialize($row['valeur']);
		}
	}
	return $tParam;
}

/** Determine le type d'utilisateur
  *
  * @return string : 'Operateur', 'Encadrant', 'Admin'
  */
function getStatutUtilisateur() {
   if($GLOBALS["auteur_session"]['statut']=='0minirezo') return 'Admin';
   else {
   	list($email,$serveur)=explode('@',$GLOBALS["auteur_session"]['email']);
   	switch (strtolower($email))
		{
			case 'encadrant':
				return 'Encadrant';
				break;
			case 'operateur':
				return 'Operateur';
				break;
			case 'notes':
				return 'Notes';
				break;
			case 'etablissement':
				return 'Etablissement';
				break;
			default:
				return 'Inconnu';
		}
	}
}

/**  Interdit l'acces a un utilisateur qui n'est pas dans le tableau $tOK
 *
 * @param array $tOK : tableau des types d'utilisateur autorises
 * @return boolean : true si autorise (sinon, die)
 */
function isAutorise($tOK) {
	if(!in_array(getStatutUtilisateur(),$tOK))
		die(KO.' - Vous n\'&ecirc;tes pas autoris&eacute;(e) &agrave; acc&eacute;der &agrave; ce module');
	return true;
}

/** Execute une requete sql
 *
 * @param string $sql : code SQL a executer
 * @param string $fichier : nom du fichier (par exemple, passer __FILE__)
 * @param int $ligne : ligne (passer __LINE__)
 * @param string $obsc : texte a dissimuler (mot de passe, par exemple)
 * @return resource : resultset correspondant
 */
function odb_query($sql,$fichier,$ligne,$obsc='****') {
	$cherche='/plugins/';
	if(substr_count($sql,'DECODE(')>0 && $obsc=='****') {
		$tmp=stristr($sql,'decode(');
		$tmp=substr($tmp,0,strpos($tmp,')'));
		list($rien,$obsc)=explode(',',$tmp);
		$obsc=trim(str_replace(array('\'','"'),'',$obsc));
	}
	$fichier=substr($fichier,strpos($fichier,$cherche));
	$result = mysql_query($sql) or die("<div style='margin:5px;border:1px outset red;background-color:#ddf;'>"
		."<div style='border:1px none red;background-color:#bbf;'>".KO." - Erreur dans la requete</div><pre>"
		.wordwrap(str_replace($obsc,'****',$sql),65)
		."</pre><small>$fichier<b>[$ligne]</b></small><br/><div style='border:1px none red;background-color:#bbf;'>"
		.htmlentities(str_replace($obsc,'****',mysql_error()))."</div></div>");
	//echo "<br/>$sql (<b>$fichier</b>:$ligne)";
	return $result;
}

/** affiche un numero de table de facon lisible pour un humain
 *
 * @param string $id_table : numero de table recupere en base
 * @return string : numero de table
 */
function getIdTableHumain($id_table) {
	$t=explode('-',$id_table);
	if(count($t)==1) return $id_table;
	$milieu=$t[1][0].(int)substr($t[1],1);
	$id_table=$t[0]."-$milieu-".(int)$t[2];
	return $id_table;
}

/** Recupere les informations de connection bdd du fichier de conf spip
 *
 * @param string $conf : (facultatif) : element de la configuration a recuperer : host, user, pass, bdd
 * @return array : host, user, pass, bdd (ou element si specifie)
 */
function getBddConf($conf='') {
	//echo realpath('.');
	//include_once('../../../ecrire/inc/utils.php');
	//$fichier=find_in_path ('config/connect.php');
	$chemin='';
	for($cpt=0;$cpt<5;$cpt++) {
		if(file_exists($chemin.'config/connect.php'))
			$fichier=$chemin.'config/connect.php';
		$chemin.='../';
	}
	
	$connect=file_get_contents($fichier);
	$connect=substr($connect,strpos($connect,"spip_connect_db"));
	$tab=explode("'",$connect);
	$host=$tab[1];
	$user=$tab[5];
	$pass=$tab[7];
	$bdd=$tab[9];
	if($conf!='')
		return $$conf;
	foreach(array('host','user','pass','bdd') as $col)
		$tBddConf[$col]=$$col;
	return $tBddConf;
}

/** Recupere le nom complet SPIP correspondant a l'identifiant SPIP $login 
 */
function getNomComplet($login) {
	$sql="SELECT nom FROM spip_auteurs WHERE login='$login'";
	$result=odb_query($sql,__FILE__,__LINE__);
	$nom=mysql_result($result,0,0);
	return $nom;
}

/** Leve l'anonymat pour le jury choisi
 * 
 * @param string $annee
 * @param int jury
 * @return boolean : true si l'anonymat a ete leve, false si rien n'a change en base
 */
function leveeAnonymat($annee, $jury) {
	$sql="UPDATE odb_notes notes,odb_repartition rep SET notes.id_table=rep.id_table\n"
	." WHERE notes.annee=$annee and rep.annee=$annee and rep.jury=$jury"
	." AND notes.id_anonyme=DECODE(rep.id_anonyme,'".getParametresODB('code')."')";
	odb_query($sql,__FILE__,__LINE__);
	if(mysql_affected_rows()>0) return true;
	else return false;
}

/**
 * Met les decisions a jour
 *
 * @param int $annee
 * @param int $jury : jury dont il faut mettre les decicions a jour (tous par defaut)
 * @param int $iPrecision : precision de la moyenne (1 par defaut)
 * @param $deliberation : numero de la deliberation qu'on veut mettre a jour (les deliberations precedentes doivent avoir ete mises a jour)
 */	

function odb_maj_decisions($annee,$jury=0,$iPrecision=3,$deliberation=1) {
if($jury>0) {
 if($deliberation==1) {
		// supprime les notes d'eps des candidats dispenses (logiquement pour corrrection des cas reserves)
		$tSql[]="DELETE from odb_notes notes using odb_notes notes, odb_candidats can, odb_ref_eps eps\n".
		" where can.id_table=notes.id_table and can.annee=$annee and notes.annee=$annee and can.eps=eps.id and eps.eps!='Apte' and notes.id_matiere=-3";
		//$tSql[]="UPDATE odb_notes notes $from_jury SET notes.note='0' WHERE type='Divers' and note<0 $where_jury and notes.annee=$annee";
		$tSql[]="REPLACE INTO odb_decisions( id_table, id_anonyme,`annee` , `moyenne` , coeff, `delib1` ) (\n".
		"SELECT notes.id_table, notes.id_anonyme, notes.annee, if(min(note)<0,-1,ROUND(sum( coeff * note ) / sum( coeff ),$iPrecision)) moy,sum( coeff ) coeff , null \n".
		"FROM odb_notes notes, odb_repartition rep\n WHERE notes.annee=$annee and notes.type!='Divers' AND notes.type!='Oral' AND rep.id_table=notes.id_table and rep.annee=$annee and rep.jury=$jury\n GROUP BY notes.id_table, notes.annee \n);";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.moyenne = ROUND(moyenne,1) where moyenne<9  AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib1 = 'Absent' WHERE moyenne=-1  AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib1 = 'Ajourne' WHERE 0<=moyenne and moyenne < 5  AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib1 = 'Refuse' WHERE 5<=moyenne and moyenne < 9  AND rep.id_table=decis.id_table and rep.annee = $annee and rep.jury=$jury and decis.annee=$annee";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib1 = 'Admissible' WHERE moyenne >= 9  AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		//TODO rendre parametrable : on refuse les candidats ayant eu un 0 et moins de 10 en Ecrit ou en Pratique (il est donc possible d'avoir 0 en Divers ou en Oral)
		//FIXME: créer une table des parametres de décision de l'année en cours
		$tSql[]="update odb_decisions decis, odb_notes notes, odb_repartition rep\n SET delib1='Refuse'\n".
		"WHERE decis.delib1 = 'Admissible' AND decis.id_table=notes.id_table\n AND notes.note=0 AND notes.type!='Divers' AND notes.type!='Oral' AND moyenne<9\n".
		" AND decis.annee=$annee AND notes.annee=$annee  AND rep.id_table=notes.id_table and rep.annee=$annee and rep.jury=$jury";
		$tSql[]="update odb_decisions decis, odb_notes notes, odb_repartition rep\n SET delib1='Reserve'\n".
		"WHERE decis.delib1 = 'Admissible' AND decis.id_table = notes.id_table\n AND notes.note=0 AND notes.type!='Divers' AND notes.type!='Oral' AND moyenne>=9 \n".
		" AND decis.annee=$annee AND notes.annee=$annee  AND rep.id_table=notes.id_table and rep.annee=$annee and rep.jury=$jury";
		foreach($tSql as $sql) {
			//echo "<pre>$sql</pre>\n";
			odb_query($sql,__FILE__,__LINE__);
		}
	} elseif($deliberation==2) {
		$sql="SELECT notes.id_table, note, id_matiere from odb_notes notes, odb_decisions decis, odb_repartition rep where decis.id_table=notes.id_table and decis.annee=$annee	and delib2='' and (delib1='Admissible' || delib1='Reserve') and notes.type='Divers' and notes.annee=$annee  AND rep.id_table=notes.id_table and rep.annee=$annee and rep.jury=$jury";
		$result=odb_query($sql,__FILE__,__LINE__);
		while($row=mysql_fetch_array($result)) {
			foreach(array('id_matiere','id_table','note') as $col) $$col=$row[$col];
			if($note>=0){
				$tNote[$id_table]+=$note;
				if($id_matiere==-3) $tEps[$id_table]=true;
			}
		}
		if(is_array($tNote)) {
			foreach($tNote as $id_table=>$points) {
				if($points>=0){
					if($tEps[$id_table]) $sql="UPDATE odb_decisions decis, odb_repartition rep SET moyenne=ROUND((moyenne*coeff+$points)/(coeff+1),$iPrecision), coeff=coeff+1 where decis.id_table='$id_table' and decis.annee=$annee and rep.annee=$annee and rep.jury=$jury";
					else $sql="UPDATE odb_decisions decis, odb_repartition rep SET moyenne=ROUND((moyenne*coeff+$points)/coeff,$iPrecision) where decis.id_table='$id_table' and decis.annee=$annee and rep.jury=$jury and rep.annee=$annee";
					//echo "<br/>$sql";
					odb_query($sql,__FILE__,__LINE__);
				}
			}
		}
		$tSql[]="UPDATE odb_decisions decis, odb_notes notes, odb_repartition rep SET decis.delib2 = 'Reserve'\n".
		" WHERE notes.id_table=decis.id_table  AND rep.id_table=notes.id_table and rep.annee=$annee and rep.jury=$jury\n and decis.annee=$annee and notes.annee=$annee and notes.id_matiere=-3 and notes.note<0";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib2 ='Refuse'\n WHERE moyenne<9 AND (decis.delib1='Reserve' OR decis.delib1='Admissible') AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib2='Oral'\n WHERE moyenne>=9 AND moyenne<10 AND (decis.delib1='Admissible' OR decis.delib1='Reserve') AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib2 = 'Reserve'\n WHERE moyenne >= 10 AND decis.delib1='Reserve' AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib2 = 'Passable'\n WHERE moyenne >= 10 and moyenne<12 AND decis.delib2!='Reserve' AND decis.delib1='Admissible' AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib2 = 'ABien'\n WHERE moyenne >= 12 and moyenne<14 AND decis.delib2!='Reserve' AND decis.delib1='Admissible' AND rep.id_table=decis.id_table and rep.annee = $annee and rep.jury=$jury and decis.annee=$annee";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib2 = 'Bien'\n WHERE moyenne >= 14 and moyenne<16 AND decis.delib2!='Reserve' AND decis.delib1='Admissible' AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		$tSql[]="UPDATE odb_decisions decis, odb_repartition rep SET decis.delib2 = 'TBien'\n WHERE moyenne >= 16 AND decis.delib2!='Reserve' AND decis.delib1='Admissible' AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		foreach($tSql as $sql) {
			odb_query($sql,__FILE__,__LINE__);
			//echo mysql_affected_rows()." lignes :<pre>$sql</pre>\n";
		}
	} elseif ($deliberation==3) {
		$sql="SELECT notes.id_table, note, id_matiere, notes.coeff\n from odb_notes notes, odb_decisions decis, odb_repartition rep\n"
			." where decis.id_table=notes.id_table and delib2='Oral' and decis.annee=$annee and delib3='-' and notes.type='Oral' AND rep.id_table=notes.id_table and rep.annee=$annee and notes.annee=$annee and rep.jury=$jury";
		//die("<pre>$sql");
		$result=odb_query($sql,__FILE__,__LINE__);
		$tCR=array();
		while($row=mysql_fetch_array($result)) {
			foreach(array('coeff','id_table','note') as $col) $$col=$row[$col];
			if($note<=0) {
				$tCR[$id_table]=true; // cas reserve
				$tNote[$id_table]+=0;
				$tCoeff[$id_table]+=$coeff;
				//echo "$id_table $note/20 $coeff - $tCoeff[$id_table] -  RESERVE<br/>";
			} else {
				$tCoeff[$id_table]+=$coeff;
				$tNote[$id_table]+=(int)$note*$coeff;
				//echo "$id_table $note/20 $coeff<br/>";
			}
			
			//echo "$id_table $note/20 $coeff - $tCoeff[$id_table]<br/>";
		}
		if(is_array($tNote)) {
			foreach($tNote as $id_table=>$points) {
				$coeff=$tCoeff[$id_table];
				if($tCR[$id_table]) {
					$sql="UPDATE odb_decisions notes SET delib3='Reserve', moyenne=ROUND((moyenne*coeff+$points)/(coeff+$coeff),$iPrecision), coeff=coeff+$coeff\n where notes.id_table='$id_table' and notes.annee=$annee";
					//echo "$id_table RESERVE<br/>";
				} else {
					$sql="UPDATE odb_decisions notes SET delib3='Passable', moyenne=ROUND((moyenne*coeff+$points)/(coeff+$coeff),$iPrecision), coeff=coeff+$coeff\n where notes.id_table='$id_table' and notes.annee=$annee";
				}
				//echo "<br/>$sql";
				odb_query($sql,__FILE__,__LINE__);
				
			}
		}
		$sql="UPDATE odb_decisions decis, odb_repartition rep SET delib3='Refuse' WHERE delib3='Passable' AND ROUND(moyenne,2)<10  AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury  and decis.annee=$annee";
		odb_query($sql,__FILE__,__LINE__);
		$sql="UPDATE odb_decisions decis, odb_repartition rep SET delib3='Reserve'\n where (delib2='Reserve' || delib1='Reserve') AND rep.id_table=decis.id_table and rep.annee=$annee and rep.jury=$jury and decis.annee=$annee";
		odb_query($sql,__FILE__,__LINE__);
		
		return mysql_affected_rows();

	} else die(KO." - Deliberation inattendue : $deliberation");
 }
}
?>
