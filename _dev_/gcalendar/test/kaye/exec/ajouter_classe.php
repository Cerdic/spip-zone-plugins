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


	function exec_ajouter_classe() {
  		global $connect_statut, $connect_toutes_rubriques;

		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}

		debut_page(_T('livre:Cahier de Texte'), "naviguer", "kaye");

		debut_gauche();
			debut_cadre_formulaire();
			gros_titre(_T('Cahier de texte'));
			fin_cadre_formulaire();
		
			debut_raccourcis();
			icone_horizontale(_T('livre:Voir la liste des classes'), generer_url_ecrire("gerer_classe"), '../'._DIR_PLUGIN_KAYE.'/img_pack/gerer_ref.gif', 'creer.gif');
			fin_raccourcis();
		
		debut_droite();
		debut_cadre_formulaire();
		gros_titre(_T('Ajouter une classe'));
		echo '<form action="" method="POST">';
		echo '<table width="100%" border="0">';
	 	echo '<tr>';
	 	echo '  <td>Intitul&eacute;</td>';
	 	echo '  <td><input name="titre" type="text"></td>';
	 	echo '</tr>';
	 	echo '<tr>';
	 	echo '  <td>Niveau</td>';
	 	echo '  <td><input name="niveau" type="text"></td>';
		echo '</tr>';
	 	echo '<tr>';
	 	echo '  <td>Auteur r&eacute;f&eacute;rent (obligatoire)</td>';
	 			$val = spip_query("SELECT id_auteur, nom FROM spip_auteurs WHERE statut='0minirezo'");
		echo '<td><select name="n_auteur">';
		echo '<option selected="selected">... </option>';
		while ($data = mysql_fetch_assoc($val))
					{
					echo '<option value="', $data['id_auteur'],'">', $data['nom'],'</option>';	
					}
		echo ' </select></td>';
		echo '</tr>';

	 	echo '<tr>';
		echo '  <td>Appartient &agrave; la classe n&deg; (optionnel)</td>';
		echo '  <td><input name="n_parent" type="text"></td>';
		echo '</tr>';
	 	echo '<tr>';
		echo '  <td>Descriptif</td>';
		echo '  <td><input name="descriptif" type="text"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '  <td>Zone prot&eacute;g&eacute;e (si plugin acces_resteints install&eacute;)</td>';
			 	$val2 = spip_query("SELECT id_zone, titre FROM spip_zones");

		echo '  <td><select name="n_zone">';
		echo '  <option selected="selected">... </option>';
				while ($data2 = mysql_fetch_assoc($val2))
					{
					echo '<option value="', $data2['id_zone'],'">', $data2['titre'],'</option>';	
					}
		echo '  </select></td>';
		echo '</tr>';
		
		echo '</table> ';
		echo '<input name="submit" type="submit" value="ajouter" class="fondo">';
		echo'</form>';
			
		fin_cadre_formulaire();
		
		$titre=$_POST['titre'];
	    $niveau= $_POST['niveau'];
		$n_parent= $_POST['n_parent'];
		$n_auteur = $_POST['n_auteur'];
		$n_zone = $_POST['n_zone'];
		$descriptif= $_POST['descriptif'];
if($titre == ''){
      echo '';
}    
else {

echo 'Classe enregistr&eacute;e:';
icone_horizontale(_T('livre:Voir la liste des classes'), generer_url_ecrire("gerer_classe"), '../'._DIR_PLUGIN_KAYE.'/img_pack/gerer_ref.gif', 'creer.gif');


$sql="INSERT INTO spip_classekaye (titre, descriptif, niveau, id_zone, id_parent, id_auteur) VALUES ('$titre', '$descriptif', '$niveau', '$n_zone', '$n_parent', '$n_auteur')";



$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());




mysql_close();}	
		 fin_page(); } ?> 