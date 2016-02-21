<?php 	session_start();//--On démarre la session
if(isset($_GET['char'])) {$utf=trim($_GET['char']);}else{$utf='utf-8';}
header('Content-Type: text/html; charset='.$utf);
$fichiercatchat='../db_catchat/'.$_SESSION['spipcatchatprivecode'].'/'.$_SESSION['spipcatchatprivecode'].'.catchat'; // Construction du lien pointant sur le fichier du salon de discussion 
	$ref=(time()-$_GET['ref'])-4;
	if(trim($_GET['cache'])=="oui")  // L'on vérifie le paramètre de gestion du cache de la configuration du formulaire commun 
	{$spipcatcache=true;}
	else
	{$spipcatcache=false;} 
		if(filemtime($fichiercatchat)>$ref ||  empty($_SESSION['catchatcache'.$_SESSION['spipcatchatprivecode']]) || $spipcatcache)
		{ //Si le fichier CatChat est plus récent que le temps actuel moins le time-code REFRESH et si le cache n'existe pas on actualise les données, sinon c'est du pseudo cache !
			$id=$_GET['auteur']; $count=false; $text=''; $i = 1; $prev = 0;  //Initialisation de nos variables
			if(trim($_SESSION['catchathistorique'])==trim($_GET['historique']))
				{ $historique=trim($_SESSION['catchathistorique']);}
			else
				{unset( $historique,$_SESSION['catchathistorique']);}
				if(false!=($data_base=fopen($fichiercatchat,'r')))
				{while (!feof($data_base))
					{
					$data=json_decode(fgets($data_base),true);
					$date_message=$data[2]; // Initialisation de la variable de l'heure du message plus
					if($_SESSION['catchatprivetime'] <= $date_message+$historique)  //l'heure du message plus 1h30 si historique 
					{ if($i != 1) {// On change la couleur dès que l'ID du membre est différent du précédent
						$idNew = $data[0];		
						if($idNew != $id)
						{
							if($colId == 1) { $color = 'class="alter"'; $colId = 0;} else { $color = 'class="ego"'; $colId = 1;}
				    $id = $idNew;
						} else {$color = $color;} 	} else { $color = 'class="ego"'; $id = $data[0]; $colId = 1; }
					$text .= '<tr><td width="140px">';
					$timeCode=$id.$i;
						if($prev != $data[0]) // Si le dernier message est du même membre, on écrit pas de nouveau son pseudo
						{// contenu du message	
						  $text .= '<span class="CloCk">'.date('H:i', $date_message).'</span>'; // l'heure du message
						  $text .= '&nbsp;<span '.$color.'>'.$data[1].$logo.'</span></span>'; // Le nom de l'auteur
						}		
					$text .='</td><td><div>&nbsp; ';
					$text .= htmlspecialchars($data[3]);  // On supprime les balises HTML // On ajoute le message en remplaçant les liens par des URLs cliquables
					$text .=' &nbsp;</div></td></tr>'; unset($timeCode,$lienLogo,$logo); $i++; 
					$prev = $data[0];
					$count=true;}
				  } 
				  fclose($data_base); // fermeture du fichier du salon
				}
			else{if($_SESSION['catchatcache'.$_SESSION['spipcatchatprivecode']]){echo $_SESSION['catchatcache'.$_SESSION['spipcatchatprivecode']];}} 	
			$json['messages']=$text;/* On crée la colonne messages dans le tableau json qui contient l'ensemble des messages */
		if(!$count){ $json['messages'] = $_GET['aucunmessage'];}  // Il n'y a aucun messages
		echo json_encode($json);// Encodage de la variable tableau json et affichage
		if(!$spipcatcache)
								{   $json['cache']='oui'; $_SESSION['catchatcache'.$_SESSION['spipcatchatprivecode']]=json_encode($json);} // Enregistrement du cache si le paramètre de gestion de celui-ci est activé dans le formulaire
		} 
	else {echo $_SESSION['catchatcache'.$_SESSION['spipcatchatprivecode']]; } // Affichage de la session cache
?>