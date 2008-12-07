<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Yohann Prigent (potter64) repris des travaux de Bernard Blazin (http://www.plugandspip.com )
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	include_spip('inc/presentation');
	function exec_repondre(){
	global $connect_statut, $connect_toutes_rubriques;
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('guestbook:lelivre'), "", "");
	if (isset($_GET['id_message'])) {
		echo "<br /><br />";
	echo '<div style="margin:auto; width :50%;">';
  
	echo"<br />";
	icone_horizontale(_T('guestbook:retour'), generer_url_ecrire("livre"), '../'._DIR_PLUGIN_LIVRE.'/img_pack/livredor.png');
		$rep=$_GET['id_message'];
		
		
		$sqlask ="SELECT nom, message FROM spip_guestbook WHERE id_message=".$rep."";
		$spask=spip_query($sqlask);
		$ask= spip_fetch_array($spask);
		$asp= $ask['nom'];
		$tex=$ask['message'];

$texte='';
$nom='';

		
	$texte=$_POST['message'];
	$nom=$_POST['nom'];
	$texte = addslashes($texte);
	$texte=nl2br($texte); 
		
		if($nom == '' OR $texte=''){
			
		echo "<form action='?exec=repondre&id_message=".$rep."' method='post'>";
		echo '<input type="hidden" name="id_rep" value="'.$rep.'" />';
	echo $asp.' a &eacute;crit :<br /> <i>"'.$tex.'"</i><br /><br />';
	$sql2= spip_query("SELECT auteur, message FROM  spip_guestbook_reponses WHERE id_message='".$rep."'");
	while ($ask2= mysql_fetch_array($sql2))
	{
		$name= $ask2['auteur'];
		$repons=$ask2['message'];
	echo $name.' a répondu  : <i>"'.$repons.'"</i><br />';
	}
	echo 'R&eacute;ponse: <textarea name="reponses" cols="50" rows="5" >'.$texte.'</textarea><br />';
	$nom=$GLOBALS['visiteur_session']['login'];
	echo 'Votre nom:  '.$nom.' <input name="nom" type="hidden" value="'.$nom.'"><br /><br />';
	echo "Remplissez tous les champs! <br /><br />";
	echo ' <input name="submit" type="submit" value="Envoyer"><br />';
	echo '</form>';
		
	}
	
	else {

$text=$_POST['reponses'];
		$sql="INSERT INTO spip_guestbook_reponses(id_message, date, message, auteur)  VALUES ('$rep', '$date', '$text', '$nom')";     
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		echo $asp.' a &eacute;crit : <i>"'.$tex.'"</i><br />';
		echo $nom.' a r&eacute;pondu : <i>"'.$text.'"</i><br />';
		
	}
	echo '</div>';
	}
	else{ 
		echo '<br /><br /> '.gros_titre(Warning);
				debut_boite_info();
				
				echo '<a href="?exec=livre">Vous n\'avez pas s&eacute;l&eacute;ctionn&eacute; d\'utilisateur. Retourner &agrave; la page des commenatires</a>'; }
	
		fin_cadre_relief();
	fin_page();
	exit;
	}
?>