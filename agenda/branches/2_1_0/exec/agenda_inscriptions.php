<?php

/**
 * Plugin Agenda pour Spip 2.0
 * Liste des inscriptions
 *
 * Pour le moment, seul les administrateurs ont accès à cette page.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Julien Tessier <julien@cahri.com>
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_agenda_inscriptions_dist()
{
	
	$id_evenement = intval(_request('id_evenement'));	
	$evenement = sql_fetsel(array('titre','date_debut'), 'spip_evenements', array("id_evenement=$id_evenement","inscription=1")); // recupere les infos de l'evenement
	$format = _request('format');	
	
	if ($GLOBALS['auteur_session']['statut'] != '0minirezo') {
		include_spip('inc/minipres');
		echo minipres(_T('info_acces_refuse'));
	} elseif ($evenement === false) {
		include_spip('inc/minipres');
		echo minipres(_T('agenda:aucun_evenement'));
	} else {
		
		$retirer_auteur = intval(_request('retirer_auteur'));	
		if ($retirer_auteur) {
			sql_delete('spip_evenements_participants', "id_auteur=$retirer_auteur AND id_evenement=$id_evenement");
		}

		if ($format == 'csv') {
			
			if (_request('mode') == 'inline') { // passer &mode=inline pour voir le fichier au lieu de le télécharger
				header("Content-type: text/plain; charset=".$GLOBALS['meta']['charset']);				
			} else {
				header('Content-Disposition: attachment; filename="'._T('agenda:liste_inscrits').' - '.addslashes($evenement['titre']).'.csv"'); // nom du fichier = Inscriptons #TITRE au #AUJOURDHUI
				if ($GLOBALS['meta']['charset']) header("Content-type: text/csv; charset=".$GLOBALS['meta']['charset']);
				else header("Content-type: text/csv");
			}
			// liste des champs à inclure dans le CSV
			$champs = array(
				'nom'     => 'Nom',
				'login'   => 'Identifiant',
				'email'   => 'Email',
				'date'    => 'Date d\'inscription',
				'reponse' => 'Réponse',
			);
			$res = sql_allfetsel(array_keys($champs), 'spip_evenements_participants INNER JOIN spip_auteurs USING (id_auteur)', array("id_evenement=$id_evenement", "reponse = 'oui' OR reponse = '?'"), "date ASC");
			$csv = '';
			// ligne d'en-tete
			foreach($champs as $champ => $legende) {
				$csv .= '"'.str_replace('"','""', $legende).'",';
			}
			$csv = substr($csv, 0, -1); // on supprime la derniere virgule
			foreach($res as $row) {
				$csv .= "\r\n";
				foreach($champs as $champ => $legende) {
					if (isset($row[$champ])) {
						if ($champ == 'date') $row[$champ] = affdate($row[$champ], 'd/m/Y H:i:s');
						$csv .= '"'.str_replace('"','""', $row[$champ]).'"';
					}
					$csv .= ',';
				}
				$csv = substr($csv, 0, -1); // on supprime la derniere virgule
			}
			echo $csv;
			
		} else {
			$inscrits = sql_allfetsel(array('nom', 'reponse', 'id_auteur', 'date'), 'spip_evenements_participants INNER JOIN spip_auteurs USING (id_auteur)', array("id_evenement=$id_evenement"), "date ASC");
			
			pipeline('exec_init',array('args'=>array('exec'=>'agenda_inscriptions', 'id_evenement'=>$id_evenement),'data'=>''));
	
			$titre = $evenement['titre'].' ('.affdate($evenement['date_debut']).') - '._T('agenda:liste_inscrits');
			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page($titre, "auteurs","redacteurs");
	
			echo pipeline('affiche_milieu',array('args'=>array('exec'=>'agenda_inscriptions', 'id_evenement'=>$id_evenement),'data'=>''));
			
		  	echo debut_gauche('', true);
			echo pipeline('affiche_gauche',array('args'=>array('exec'=>'agenda_inscriptions', 'id_evenement'=>$id_evenement),'data'=>''));
			echo bloc_des_raccourcis(
				icone_horizontale (_T('agenda:telecharger').' (CSV)', generer_url_ecrire("agenda_inscriptions", "id_evenement=$id_evenement&format=csv"), "synchro-24.gif", "", false)
			);
	
			echo creer_colonne_droite('', true);
			echo pipeline('affiche_droite',array('args'=>array('exec'=>'agenda_inscriptions', 'id_evenement'=>$id_evenement),'data'=>''));
		
			echo debut_droite('', true), gros_titre($titre,'',false);
	
			echo debut_cadre('liste','auteur-24.gif','','','inscriptions');
			echo "\n<br /><table  class='arial2' cellpadding='2' cellspacing='0' style='width: 100%; border: 0px;'>\n";
			
			echo "<tr class='titrem'><th style='width: 20px;'></th><th style='width: 20px;'></th><th>Nom</th><th>Date</th><th>Réponse</th><th></th></tr>";
			
			$formater_auteur = charger_fonction('formater_auteur', 'inc');
			foreach ($inscrits as $row) {
				list($s, $mail, $nom, $w, $p) = $formater_auteur($row['id_auteur']);				
				echo "\n<tr class='tr_liste'>"
					. "\n<td>"
					. $s
					. "</td><td>"
					. $mail
					. "</td>"
					. "\n<td class='verdana1'>"
					. '<a href="'.generer_url_ecrire("auteur_infos","id_auteur=".$row['id_auteur']).'">' . $nom . '</a>'
					."</td>"
					. "\n<td class='arial1'>"
					. affdate($row['date']).' '.affdate($row['date'], 'H:i')
					."</td>"
					."\n<td class='arial1'>"
					. $row['reponse']
					. "</td><td class='arial1'>"
					. 	'<a class="arial1 editer_auteurs" href="'.generer_url_ecrire("agenda_inscriptions","id_evenement=$id_evenement&retirer_auteur=".$row['id_auteur']).'" onclick="return (confirm(\''.texte_script(_T('agenda:confirm_suppression_inscription')).'\'));">'._T('agenda:lien_desinscrire')."&nbsp;". http_img_pack('croix-rouge.gif', "X", " class='puce' style='vertical-align: bottom;'").'</a>'
					.  "</td></tr>\n";
			}
			
			echo "</table>\n<br />";
	
			echo fin_cadre();
			
			echo fin_gauche(), fin_page();
		}
	}
}
?>
