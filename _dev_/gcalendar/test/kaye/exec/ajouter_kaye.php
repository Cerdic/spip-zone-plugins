<?php
/**
	 * Kayé
	 * Le cahier de texte électronique spip spécial primaire
	 * Copyright (c) 2007
	 * Cédric Couvrat
	 * http://alecole.ac-poitiers.fr/
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
**/
	include_spip('inc/presentation');
	include_spip('inc/date');
	include_spip('inc/extra');
	include_spip('base/abstract_sql');

	function exec_ajouter_kaye() {
  		global $connect_statut, $connect_toutes_rubriques, $connect_id_auteur;
		
		debut_page(_T('livre:Cahier de Texte'), "naviguer", "kaye");

		
		debut_gauche();
		debut_cadre_formulaire();
		gros_titre(_T('Cahier de texte'));
		fin_cadre_formulaire();
		debut_raccourcis();
		icone_horizontale(_T('livre:Consulter les devoirs inscrits'), generer_url_ecrire("kaye_noter"), '../'._DIR_PLUGIN_KAYE.'/img_pack/ecole.gif');
		fin_raccourcis();
		
		debut_droite();
		debut_cadre_formulaire();
		gros_titre(_T('Cahier de texte: Ajouter un devoir'));
		
		echo '<form id="formulaire" name="formulaire" action="" method="POST">';
		echo '<table width="100%" border="0">';
		echo '<tr>';
		echo '  <td>Classe</td>';
  		//requete de recup du nom et de l'id de la classe (id_classe et titre)
		$queryclasse = "SELECT id_classe, titre FROM spip_classekaye";
		$valclasse = spip_query (${queryclasse}) ;		
		echo '<td><select id="classe" name="classe">';
		echo '<option selected="selected" value="0" >... </option>';
				while ($rep = mysql_fetch_assoc($valclasse))
						{
		echo '<option value="', $rep['id_classe'],'">', $rep['titre'],'</option>'; 				
						}	
		echo '</select></td>';
		echo ' </tr>';
		echo '<tr>';
		echo '  <td>Mati&egrave;re</td>';
		echo ' <td><input name="discipline" type="text"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<tr>';
  		echo '  <td>Pour le: </td>';
  		echo '  <td><input class="calendarSelectDate" name="date_proposee" type="text"></td>';
		echo ' </tr>';
		echo '  <td>Texte</td>';
		
		include_spip('inc/barre');
		$afficher_barre = '<div>' 
		.  afficher_barre('document.formulaire.descriptif')
		. '</div>';
		
		echo '  <td>'.$afficher_barre.'<textarea id="descriptif" name="descriptif" cols="20" rows="5" class="formo"></textarea></td>';
		echo ' </tr>';
		echo '</table> ';
		echo '<div align="right"><input name="submit" type="submit" value="valider" class="fondo"></div';
		echo'</form>';
		echo'<div id="calendarDiv"></div>';
		fin_cadre_formulaire();
		
	    $discipline= $_POST['discipline'];
		$descriptif= addslashes($_POST['descriptif']);
		$id_classe= $_POST['classe'];
		list($jour, $mois, $annee) = explode("-", $_POST['date_proposee']);
		$date_echeance= $annee.'-'.$mois.'-'.$jour;
		$date_jour = date("Y-m-d");
if($id_classe == ''){
      echo '';
}
else if($id_classe == '0'){
      echo 'selectionnez une classe';
}  
else {
	echo 'devoirs enregistr&eacute;s:<br>', $date_echeance,'<br>', $_POST['date_echeance'];
	icone_horizontale(_T('livre:Consulter les devoirs inscrits'), generer_url_ecrire("kaye_noter"), '../'._DIR_PLUGIN_KAYE.'/img_pack/ecole.gif');

	
	$sql="INSERT INTO spip_kaye (id_classe, discipline, descriptif, id_auteur, date_jour, date_echeance)
		VALUES ('$id_classe', '$discipline', '$descriptif', '$connect_id_auteur', '$date_jour', '$date_echeance')";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	mysql_close();
	}	
		 fin_page(); } ?> 