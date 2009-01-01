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
	include_spip('base/abstract_sql');
	
	echo '<script language="JavaScript" type="text/javascript">
	$(document).ready(function()	
    { 
        $("#tableau_tout").tablesorter();
    } 
	);
	</script>';

function exec_kaye_noter() {
	global $connect_statut, $connect_toutes_rubriques, $connect_id_auteur, $spip_display;

	/* test super admin (ominirezo + acces à toutes les rubriques)
	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	echo 'id_auteur=', $connect_id_auteur;
	echo 'connect_statut=', $connect_statut;
	echo 'connect_toutes_rubriques=', $connect_toutes_rubriques;
	*/
	//$link = generer_url_ecrire('proposer_domaine');
	
	debut_page(_T('livre:Cahier de texte'), "naviguer", "Cahier de texte");
	
	debut_gauche();
		
		debut_cadre_formulaire();
		gros_titre(_T('Cahier de texte'));
		fin_cadre_formulaire();
		
		debut_raccourcis();
		icone_horizontale(_T('livre:Ecrire les devoirs'), generer_url_ecrire("ajouter_kaye"), '../'._DIR_PLUGIN_KAYE.'/img_pack/ecole.gif', 'creer.gif');
   		fin_raccourcis();
		
	debut_droite();

		$query = "SELECT * FROM spip_kaye WHERE id_auteur=$connect_id_auteur";
		$val = spip_query (${query}) ;
		$nbval = spip_num_rows (${query}) ;
		$i=0;

		debut_cadre_formulaire();
		gros_titre(_T('Liste des devoirs r&eacute;dig&eacute;s'));
		fin_cadre_formulaire();
		echo '<br>';		
		if (!$val)
			{ 
			echo 'Pas de devoirs sous votre identifiant'; //à vérifier
			}
		else
			{
			echo '<table width="100%" border="1" id="tableau_tout" class="tablesorter">
				<thead>
		 			<tr>
						<td>MATIERE</td>
						<td>DONNE LE</td>
						<td>POUR LE</td>
						<td>TEXTE</td>
				  	</tr>
				</thead>';
			while ($data = mysql_fetch_assoc($val))
				{	
					
					echo '<tr>';
					echo '<td>', $data['discipline'],'</td>';
					list($annee, $mois, $jour) = explode("-", $data['date_jour']);
					$date_jour= $jour.'-'.$mois.'-'.$annee;
					echo '<td>',$date_jour,'</td>';
					list($annee, $mois, $jour) = explode("-", $data['date_echeance']);
					$date_echeance= $jour.'-'.$mois.'-'.$annee;
					echo '<td>', $date_echeance,'</td>';
					echo '<td>', propre($data['descriptif']),'</td>';
	  				echo '</tr>';
					
				}
				
			echo '</table>';
			echo '<br>';
			
			}
	
	}

?>
