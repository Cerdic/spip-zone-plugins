<?php session_start();//--On démarre la session
if(isset($_GET['char'])) {$utf=trim($_GET['char']);}else{$utf='utf-8';}
header('Content-Type: text/html; charset='.$utf);
$fichiercatchat='../db_catchat/'.$_SESSION['spipcatchatcode'].'/'.$_SESSION['spipcatchatcode'].'.catchat';
 $ref=(time()-$_GET['ref'])-4;
if(filemtime($fichiercatchat)>$ref ||  empty($_SESSION['catchatcache'.$_SESSION['spipcatchatcode']]) || $_SESSION['catchatcache'])
{ //Si le fichier CatChat est plus récent que le temps actuel moins le time code REFRESH et si le cache n'existe pas on actualise les données, si non c'est du pseudo cache !
include('../fonction/fonction.php');  //------Les fichiers à inclure-----//
	$id=$_GET['auteur']; $count=false; $text='';
	$json['annonce'] = html_entity_decode($_SESSION['spipcatchatnomsalon']);// Affichage de l'annonce 
	$i = 1; $prev = 0; 
		if($_SESSION['catchathistorique']==trim($_GET['h'])) // historique
				{ $historique=$_SESSION['catchathistorique'];} 
		else	
				{ unset( $historique,$_SESSION['catchathistorique']);}
	if(false!=($data_base=fopen($fichiercatchat,'r')))
	{while (!feof($data_base)) {
		$data=json_decode(fgets($data_base),true);
		$date_message=$data[2];
		if($_SESSION['catchattime'] <= $date_message+$historique) //l'heure du message plus 1h30 si historique 
		{ if($i != 1) {// On change la couleur dès que l'ID du membre est différent du précédent
			$idNew = $data[0];		
			if($idNew != $id) {
				if($colId == 1) { $color = 'class="alter"'; $colId = 0;} else { $color = 'class="ego"'; $colId = 1;}
	    $id = $idNew;
			} else {$color = $color;} 	} else { $color = 'class="ego"'; $id = $data[0]; $colId = 1; }
		$text .= '<tr><td width="5%">';
		$timeCode=$id.$i;
			if($lienLogo=logo_auteur_chat($id,$_SESSION['catchatplugin'],$timeCode))
			{	$logo=$lienLogo; }
		if($prev != $data[0]) // Si le dernier message est du même membre, on écrit pas de nouveau son pseudo
		{// contenu du message	
			$text .= '<span class="HiddenAuteur"  onmouseout="logoSpipHidden(\'WO'.$timeCode.'\');" onmouseover="logoSpipShow(\'WO'.$timeCode.'\');return false;">'; //logo de l'auteur
			$text .= '<span class="CloCk">'.date('H:i', $date_message).'</span>'; // l'heure du message
			$text .= '&nbsp;<span '.$color.'>'.$data[1].$logo.'</span></span>'; // Le nom de l'auteur
		}		
		$text .='</td><td class="TDCATTEXT"><div class="TEXTOCAT">&nbsp; ';
		$text .= htmlspecialchars($data[3]); // On supprime les balises HTML // On ajoute le message en remplaçant les liens par des URLs cliquables
		$text .=' &nbsp;</div></td></tr>'; unset($timeCode,$lienLogo,$logo); $i++; 
		$prev = $data[0];
$count=true;}	
	  } fclose($data_base);
	}else{if($_SESSION['catchatcache'.$_SESSION['spipcatchatcode']]){echo $_SESSION['catchatcache'.$_SESSION['spipcatchatcode']];}} 	
	$json['messages']=$text;/* On crée la colonne messages dans le tableau json qui contient l'ensemble des messages */
	$json['cache']='non';
if(!$count){ $json['messages'] = $_GET['aucunmessage'];}  // Il n'y a aucun messages
echo json_encode($json);// Encodage de la variable tableau json et affichage
if(!$_SESSION['catchatcache']){$json['cache']='oui';$_SESSION['catchatcache'.$_SESSION['spipcatchatcode']]=json_encode($json);}} // Enregistrement du cache
else {echo $_SESSION['catchatcache'.$_SESSION['spipcatchatcode']];} // Affichage de la session cache
?>