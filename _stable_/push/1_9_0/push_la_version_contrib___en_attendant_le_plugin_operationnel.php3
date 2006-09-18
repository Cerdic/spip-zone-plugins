<?php

include ("inc.php3");
include_ecrire("inc_mail.php3");

// Parametrage Email 
$serverPop="";
$user="";
$password="";
$port=110;

//positionner à "oui" pour supprimer l'email une fois sauvegardé 
define ("SUPPRIMER_MESSAGE_POP3","non");

//Un compte rendu sera envoye a cette adresse
define ("EMAIL_CONFIRMATION","ben.spip@free.fr");

// La rubrique se situe dans le sujet du mail. mais si elle n'est pas trouvee, on utilisera celle ci
define ("ID_RUBRIQUE_PAR_DEFAUT","1");
define ("UTILISER_RUBRIQUE_PAR_DEFAUT","0"); // 0 ou 1 

// on peux mettre au choix prop(proposé),publie(publié) 
define ("STATUT_PAR_DEFAUT","prop"); 

// L'auteur est normalement l'expediteur du mail. 
// Mais si ce n'est pas un utilisateur du site, on utilisera ce numero d'auteur
define ("ID_AUTEUR_PAR_DEFAUT","1");

// Expression utilisee pour reconnaitre un mail qui doit etre publie
$expressionReguliere="SPIPOUNET";


$mailLog="";

$mbox = get_mailBox($serverPop , $user , $password,$port);

// Lecture des mails 
for ($i = 1; $i <= imap_num_msg($mbox); $i++) {
  //recuperation du header
  $header = imap_headerinfo($mbox, $i,80,80) or die ('probleme de lecture du mail ');
  //Le sujet du mail
  $subject=$header->fetchsubject;
  benDebug("<HR>Message N°$i $subject ");
  
  //Doit respecter l'expression reguliere 
  if(strpos($subject,$expressionReguliere)!==false) {
    benDebug("Message à traiter ");
    
    $_surtitre="";
    $_titre="";
    $_soustitre="";
    $_descriptif="";
    $_chapo="";
    $_texte="";
    $_ps="";
    $_id_rubrique=ID_RUBRIQUE_PAR_DEFAUT;
    $_statut=STATUT_PAR_DEFAUT;
    
    $_id_auteur=getAuteur($header,ID_AUTEUR_PAR_DEFAUT) ;
    
    //Recuperation du contenu
    $messageBody = quoted_printable_decode(imap_body($mbox, $i));
    $_titre=getTitre($messageBody);
    $_id_rubrique=getRubrique($messageBody);
    $_texte=getTexte($messageBody);
    $_lang=getLang($messageBody,$_id_rubrique);
    $idMotsCles=getIdMotCle($messageBody);
    
    $passerSql=0;    
    // Quelques vérifs 
	if ($_titre=="") // ajout JLuc
	  $_titre=trim(str_replace($expressionReguliere, "", $subject));
    if ($_titre=="") {
      benLog("Erreur Titre non Trouvé "); 
      $passerSql+=1;
    }
    
    if ($passerSql==0) {
      benDebug("<h2>Sauvegarde en base</h2>"); 
    
      //Sauvegarde en base dans la table spip_article 
      $_date=date('Y-m-d H:i:s',time());
      $sql="insert into spip_articles (lang,surtitre,titre,soustitre,id_rubrique,descriptif,chapo,texte,ps,statut,accepter_forum,date) ";
      $sql.="VALUES( '".addslashes($_lang)."','".addslashes($_surtitre)."','".addslashes($_titre)."','".addslashes($_soustitre)."',$_id_rubrique,'".addslashes($_descriptif)."','".addslashes($_chapo)."','".addslashes($_texte)."','".addslashes($_ps)."','$_statut','pos','$_date')";
      benDebug($sql);
      spip_query($sql) or die ("erreur SQL :$sql") ;
      
      //sauvegarde en base de la table spip_auteurs_articles
      $id_nouvel_article = spip_insert_id();
      $sql="INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($_id_auteur, $id_nouvel_article)";
      benDebug($sql);
      spip_query($sql)  or die ("erreur SQL :$sql");		
      
      //sauvegarde en base de la table spip_mots_articles
      if ($idMotsCles) {
        reset ($idMotsCles);
        while (list(, $idMot) = each($idMotsCles)) {
          $sql = "INSERT INTO spip_mots_articles (id_mot, id_article) VALUES ($idMot, $id_nouvel_article)";
          benDebug($sql);
          spip_query($sql)  or die ("erreur SQL :$sql");		
        }
      }
      benLog("Article N° $id_nouvel_article sauvegardé");
			if (SUPPRIMER_MESSAGE_POP3=="oui" ) {
				benDebug("<h1>Email N°$i supprimé</h1> ");
     		imap_delete($mbox, $i);
				imap_expunge ($mbox);  
			}
    } // passerSql
  } // pregmatch 
}
benLog("nombre d'emails lus : ".($i-1)."");
//envoyer_mail(EMAIL_CONFIRMATION,"Publication automatique par Email", $mailLog);
echo "<HR><H2>Dans Spip.log:</h2>$mailLog";




//Recuperation de l'email de l'auteur 
function getAuteur ($header,$auteurParDefaut){
    benDebug("<H1>getAuteur</H1>");
    $auteurTrouve=false;
    $from = $header->from;
    foreach($from as $addr)
      $fromadress = $addr->mailbox.'@'.$addr->host;
    
    benDebug("adresse de l'expéditeur : $fromadress ");
    
    //Recherche de l'auteur dans la base spip à partir de l'email 
    $sql="SELECT id_auteur,login,nom FROM spip_auteurs WHERE email='$fromadress' ";
    $result_nombres_auteurs = spip_query($sql);
    benDebug($sql);
    
    while ($row = spip_fetch_array($result_nombres_auteurs)) {
      benDebug("Auteur trouvé : ". $row['id_auteur'] ."-" . $row['login'] ."-" . $row['nom'] );
      //s'il y en a plusieurs (possible ? ) on ne prend que le dernier
      $_id_auteur=$row['id_auteur'];
      benLog ("Auteur Email : $fromadress id_auteur:".$row['id_auteur'] ." Login:" . $row['login'] ." Nom:" . $row['nom']); 
      $auteurTrouve=true;
    }
    if ($auteurTrouve==false) {
      benLog("Auteur non trouvé, on utilise l'auteur par défaut : id_auteur=$auteurParDefaut"); 
      $_id_auteur=ID_AUTEUR_PAR_DEFAUT;      
    }
  return  $_id_auteur;
}


function getIdMotCle ($messageBody) {
    benDebug("<H1>getIdMotCle</H1>");
	// les id des mots clés sont dans le sujet -mot:1,3,5-
	
 if (preg_match("/(#mot-cle:)([^#]*)/", $messageBody,$res)) 
 	{
		benDebug("id_mots cles tr  ouvés : ".$res[2]);
		benLog("Mot clé N°".$res[2]);
		$id_mots_return=split(',',trim($res[2]));
	}
	else {
			benDebug("#mot-cle non trouvé ");
	}
	return $id_mots_return ;
}

function getTexte ($messageBody) 
{
    benDebug("<H1>getTexte</H1>");
  
  if (preg_match("/(#texte:)([^#]*)/", $messageBody,$res)) 
  {
    $_texte= $res[2];
    benDebug("#texte $_texte");
  }
  else {
    benDebug ("#texte non trouvé");
  }
  return $_texte ;
}


function getLang ($messageBody,$idRubrique) {
  benDebug("<h1>getLang</h1> ");
  $lang="";
  if (preg_match("/(#lang:)([^#]*)/", $messageBody,$res)){ 
    $lang= trim($res[2]);
    benDebug("#lang -$lang- ");
    benLog("Lang : -$lang- ");
  }
  else {
    benDebug( "lang non trouvée ... Tant pis on laisse tomber, mais on peux aussi chercher la langue de la rubruique ou du secteur " ) ;
  }
  return $lang ;
}


function getTitre ($messageBody) {
    benDebug("<h1>getTitre</h1> ");
  $titre="";
  if (preg_match("/(#titre:)([^#]*)/", $messageBody,$res)){ 
    $titre= trim($res[2]);
    benDebug("Titre OK : $titre ");
    benLog("Titre : $titre ");
  }
  else {
    benDebug( " #titre non trouvé $messageBody " ) ;
  }
  return $titre ;
}

function getRubrique ($messageBody) {
    benDebug("<h1>getRubrique</h1> ");
  // un mot de la rubrique est dans le sujet -rub:bla bla-
  if (preg_match("/(#rubrique:)([^#]*)/", $messageBody,$res)) {
    $titreRubriqueSujet=trim($res[2]);
    benDebug("Trouvé dans le mail : $titreRubriqueSujet");
    
    //le titre de toutes les rubriques 
    $query="SELECT id_rubrique,titre FROM spip_rubriques ";
    $result=spip_query($query);
    $rubrique_trouvee=false;
    while($row=spip_fetch_array($result)){
      $id_rubrique=$row['id_rubrique'];
      $titre_rubrique=typo($row['titre']);
      $pos=strpos( $titre_rubrique,$titreRubriqueSujet);
        if($pos!==false) {
        benDebug("ON UTILISERA cette rubrique : $titre_rubrique $id_rubrique ");
        benLog("Rubrique N°: $id_rubrique - $titre_rubrique");
        // si plusieurs rubriques correspondent, on prends la derniere 
        $id_rubrique_return=$id_rubrique;
        $rubrique_trouvee=true;
      }
    }
    if ($rubrique_trouvee==false) {
      benDebug("[RUBRIQUE] cette partie ($titreRubriqueSujet) n'a pas été trouvée dans un des titres de rubrique . La rubrique par defaut est utilisée ");
      $id_rubrique_return=ID_RUBRIQUE_PAR_DEFAUT;
    }
  }
  else {
    benDebug ("#rubrique non trouvé on utilise la rubrique par defaut ");
    benLog ("rubrique non trouvé on utilise la rubrique par defaut : N° ".ID_RUBRIQUE_PAR_DEFAUT);
    $id_rubrique_return=ID_RUBRIQUE_PAR_DEFAUT;
  }
  return $id_rubrique_return ; 		
 }

function get_mailBox($server , $user , $passwd , $port=110){
  $mbox= imap_open("{".$server."/pop3:$port"."}", $user, $passwd) or die("Probleme : ". imap_last_error());
  return $mbox;
}

function benDebug ($texte){
  print "$texte <BR>";
  spip_log("[PUSH] $texte");
}

function benLog ($texte) {
  global $mailLog;
  spip_log("[PUSH] $texte");
  $mailLog.="$texte<BR>\n";
}

/*
	RealET	idée : dans les header du mail
	RealET	oui : Content-Transfer-Encoding: quoted-printable
	RealET	Et aussi : Content-Type: text/plain; charset=ISO-8859-1; format=flowed
*/

?>
