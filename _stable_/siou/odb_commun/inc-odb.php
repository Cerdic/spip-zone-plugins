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

?>
