<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Bernard Blazin  http://www.libertyweb.info & Yohann Prigent (potter64)
	 * http://www.plugandspip.com 
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	include_spip('inc/presentation');
	function exec_repondre(){
	global $connect_statut, $connect_toutes_rubriques;
		$commencer_page = charger_fonction('commencer_page', 'inc');
	echo	$commencer_page(_T('guestbook:lelivre'), "", "");

	if (isset($_GET['id_message'])) {
		echo "<br /><br />";
	gros_titre(_T('guestbook:repondremdl'));
	echo '<div style="margin:auto; width :50%;">';
	debut_boite_info();
  
	echo"<br />";
	icone_horizontale(_T('guestbook:retour'), generer_url_ecrire("livre"), '../'._DIR_PLUGIN_LIVRE.'/img_pack/livredor.png');
		$rep=$_GET['id_message'];
		
		
		$sqlask ="SELECT nom, texte FROM spip_livre WHERE id_messages=".$rep."";
		$ask= mysql_fetch_array(spip_query($sqlask));
		$asp= $ask['nom'];
		$tex=$ask['texte'];

$texte='';
$nom='';

		
	$texte=$_POST['reponses'];
	$nom=$_POST['nom'];
	$texte = addslashes($texte);
	$texte=nl2br($texte); 
		
		if($nom == '' OR $texte=''){
			
		echo "<form action='?exec=repondre&id_message=".$rep."' method='post'>";
		echo '<input type="hidden" name="id_rep" value="'.$rep.'" />';
	echo $asp.' a &eacute;crit :<br /> <i>"'.$tex.'"</i><br /><br />';
	$sql2= spip_query("SELECT nom, reponses FROM  spip_reponses_livre WHERE id_messages='".$rep."'");
	while ($ask2= mysql_fetch_array($sql2))
	{
		$name= $ask2['nom'];
		$repons=$ask2['reponses'];
	echo $name.' a répondu  : <i>"'.$repons.'"</i><br />';
	}
	echo 'R&eacute;ponse: <textarea name="reponses" cols="50" rows="5" >'.$texte.'</textarea><br />';
	echo 'Votre nom: <input name="nom" type="text" value="'.$nom.'"><br /><br />';
	echo "Remplissez tous les champs! <br /><br />";
	echo ' <input name="submit" type="submit" value="Envoyer"><br />';
	echo '</form>';
		
	}
	
	else {

$text=$_POST['reponses'];
		$sql="INSERT INTO spip_guestbook_reponses(id_messages, date, reponses, nom)  VALUES ('$rep', '$date', '$text', '$nom')";     
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