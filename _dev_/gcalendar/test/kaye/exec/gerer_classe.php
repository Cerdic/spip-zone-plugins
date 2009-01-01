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


	function exec_gerer_classe() {
  		global $connect_statut, $connect_toutes_rubriques;

		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}
		
		if($_POST['bt_modifier'] == 'modifier'){
			echo 'je modifie', $_POST['id_coche'];
			}    
		else if ($_POST['bt_supprimer'] == 'supprimer'){
			$sup = $_POST['id_coche'];
			echo 'Classe ', $_POST['id_coche'];
			echo ' supprim&eacute;e';
				//$query = "SELECT * FROM spip_kaye";
				//$val = spip_query (${query}) ;
				$sql = "DELETE FROM spip_classekaye WHERE id_classe=$sup";
				$req = spip_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());	
				mysql_close();
			}	

		debut_page(_T('livre:cahier_de_texte'), "naviguer", "kaye");

		debut_gauche();
		
			debut_cadre_formulaire();
			gros_titre(_T('Cahier de texte'));
			fin_cadre_formulaire();

			debut_raccourcis();
			icone_horizontale(_T('livre:Ajouter une classe'), generer_url_ecrire("ajouter_classe"), '../'._DIR_PLUGIN_KAYE.'/img_pack/gerer_ref.gif', 'creer.gif');
			fin_raccourcis();
		
    	debut_droite();
		debut_cadre_formulaire();
			gros_titre(_T('Les classes'));
			echo '<br>';
			
			$query = "SELECT * FROM spip_classekaye WHERE id_parent=0";
			$val = spip_query (${query}) ;
			if (!$val)
			{ 
			echo 'Il n\'y aucune classe'; //à vérifier
			}
			else
			{
			echo "<form action='$action' method='POST'>";
			while ($data = mysql_fetch_assoc($val))
					{	
						echo '<hr>';
						echo '<table><tr><td><input name="id_coche" type="radio" value="', $data['id_classe'],'" /></td>';
						echo '<td>N&deg;: ', $data['id_classe'],'&nbsp;<br>';
						echo 'Classe: ', $data['titre'],'&nbsp;<br>';
						echo 'Zone prot&eacute;g&eacute;e n&deg;: ',$data['id_zone'],'&nbsp;<br>';
						echo 'Niveau: ',$data['niveau'],'&nbsp;<br>';
						echo 'Descriptif: ',$data['descriptif'],'&nbsp;<br>';
						echo 'Enseignant n&deg;: ',$data['id_auteur'],'&nbsp;<br></td></tr></table>';
						
						
					}
			echo '<br>';
			//echo '<input name="bt_modifier" type="submit" value="modifier" /><input name="bt_supprimer" type="submit" value="supprimer" />';
			echo '<input name="bt_supprimer" type="submit" value="supprimer" />';

			echo'</form>';
			}
		fin_cadre_formulaire();

		 fin_page(); } ?> 