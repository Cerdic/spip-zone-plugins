<?php

function liste ($texte, $arg1='liste_spip' )
	{
	$s="";
	$lenstring=strlen ($texte);
	
	$s=$s."<SELECT name=\"$arg1\">\n";
	$string=$texte;
	while (strpos ($string, ",", 0)>0)
		{
		$pos=strpos ($string, ",", 0) ;
		$pointure=substr($string,0,($pos));
		$s=$s."<OPTION value=\"$pointure\">$pointure</OPTION>\n";
		$newstring=substr ($string, $pos+1, strlen ($string));
		$string=$newstring;
		}
	$pointure=substr($string,0,strlen ($string));
	$s=$s."<OPTION value=\"$pointure\">$pointure</OPTION>\n";
	$s=$s."</SELECT>";
	return $s ;
	}

function private_key()
	{
// génération du mot de passe 
	$chaine = "0123456789"; //caractères possibles 
	srand((double)microtime()*1000000); 
	for($i=0; $i<8; $i++) 
		{    
	//mot de passe de 8 caractères 
		$pass .= $chaine[rand()%strlen($chaine)];  
		}
	return $pass;  
	}


function create_boutique($texte)
	{
	if (!$link = boutique_mysql_connect ())
		{
		echo 'Traitement mysql interrompu';
		exit;
		}
	$code_session=private_key() ;
	$sql = "INSERT INTO `spip_ecommerce_sessions` (`code_session`, `ip_adresse`, `statut`) VALUES ('$code_session', '$_SERVER[REMOTE_ADDR]', 'create')";
	$result = mysql_query($sql, $link);
	if (!$result) 
		{
		echo "Erreur DB, impossible d'effectuer une requête\n";
		echo 'Erreur MySQL : ' . mysql_error();
		exit;
		}
	$sql = "SELECT `id_session` FROM `spip_ecommerce_sessions` where `code_session`='$code_session'";
	$result = mysql_query($sql, $link);
	if (!$result) 
		{
		echo "Erreur DB, impossible d'effectuer une requête\n";
		echo 'Erreur MySQL : ' . mysql_error();
		exit;
		}
	while ($row = mysql_fetch_array($result)) 
		{
		$id_session = $row['id_session'];
		}
	$id_session=$texte.$id_session ;
	return $id_session ;	
	mysql_close($link);
	}


function parametre_url_session ($parametres, $arg1='spip_parametre_url_session' )
	{
	// on recherche un champs contenant l'adresse ip en open
	$result=spip_num_rows(spip_query("SELECT ip_adresse FROM spip_ecommerce_sessions WHERE ip_adresse='$_SERVER[REMOTE_ADDR]' AND statut='processed'"));
	if ($result == 0)
		{
		$code_session=private_key() ;
		$result=spip_query("INSERT INTO spip_ecommerce_sessions (code_session, ip_adresse, statut) VALUES ('$code_session', '$_SERVER[REMOTE_ADDR]', 'processed')");
		$row=spip_fetch_array(spip_query("SELECT id_session FROM spip_ecommerce_sessions WHERE ip_adresse='$_SERVER[REMOTE_ADDR]' AND statut='processed'")) ;
		$result=$row['id_session'] ;
		return "$parametres&$arg1=$result";
		}
	else
		{
		$result=spip_query("UPDATE spip_ecommerce_sessions SET statut='create' WHERE ip_adresse = '$_SERVER[REMOTE_ADDR]'");
		$row=spip_fetch_array(spip_query("SELECT max(id_session) as id_session FROM spip_ecommerce_sessions WHERE ip_adresse='$_SERVER[REMOTE_ADDR]' AND statut='create'")) ;
		$result=$row['id_session'] ;
		return "$parametres&$arg1=$result";
		}
	return "$parametres&$arg1=notok";
	}

function somme ($texte, $arg1='0')
	{
	$pu=0; $qte=0; $total_article=0;
	$pu = $texte;
	settype($pu, "integer"); 
	$qte = $arg1 ;
	settype($qte, "integer"); 
	$total_article = $qte*$pu ;
	return $total_article ;
	}

function solde ($texte, $arg1="")
	{
	$s="" ;

	if (strcmp ($arg1, ""))
		{
		$s=$s."<span class=\"Style4\">\n" ;
		$s=$s."$arg1\n" ;
		$s=$s."</span>\n" ;
		$s=$s."<span class=\"Style3\">\n" ;
		$s=$s." &euro;UROS Solde a\n" ;
		$s=$s."</span>" ;
		}
	$s=$s."<span class=\"Style3\">$texte &euro;UROS <BR> frais de port en sus</span>";
	return $s;
	}
?>