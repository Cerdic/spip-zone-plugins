<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip ('inc/navigation_modules');

function exec_voir_activites(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'activites') OR !test_plugin_actif('agenda')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$id_evenement= intval(_request('id'));

		if ( isset ($_POST['statut'] )) { $statut =  $_POST['statut']; }
		else { $statut= "%"; }

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;

		association_onglets(_T('asso:titre_onglet_activites'));

		echo debut_gauche("",true);

		echo debut_boite_info(true);
		echo association_date_du_jour();

		$query = sql_select("*", "spip_evenements", "id_evenement=$id_evenement") ;
		while ($data = sql_fetch($query)) {
				echo '<p><strong>'.$data['date_debut'].'<br />'.$data['titre'].'</strong></p>';
				echo '<p>'._T('asso:activite_liste_legende').'</p>';
		}

		// TOTAUX
		$query = sql_select("sum(inscrits) AS inscrits, sum(montant) AS encaisse ", "spip_asso_activites", "id_evenement='$id_evenement' AND statut ='ok' " );
		while ($data = sql_fetch($query)) {
			echo '<p><strong style="color:blue;">';
			echo _T('asso:activite_liste_nombre_inscrits',array('total' => $data['inscrits'])).'</strong><br />';
			echo '<strong style="color: #9F1C30;">';
			echo _T('asso:activite_liste_total_participations',array('total' => number_format($data['encaisse'], 2, ',', ' ')));
			echo '</strong><br/></p>';
		}
		echo fin_boite_info(true);


		$res=association_icone(_T('asso:activite_bouton_ajouter_inscription'),  generer_url_ecrire('edit_activite', 'id_evenement='.$id_evenement), 'panier_in.gif');
		$res.=association_icone(_T('asso:activite_bouton_voir_liste_inscriptions'),  generer_url_ecrire('pdf_activite','id='.$id_evenement), "print-24.png");

		echo bloc_des_raccourcis($res);
		echo debut_droite("",true);
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_inscriptions_activites'));


	// PAGINATION ET FILTRES
		echo '<table width="100%">';
		echo '<tr>';
		$data = sql_fetsel("*", "spip_evenements", "id_evenement=$id_evenement") ;
		$date = substr($data['date_debut'],0,10);
		$date = association_datefr($date); // ne sert pas ????
		$titre = $data['titre']; // non plus
		echo "<td style='text-align:right;'>\n";
		echo '<form method="post" action="'.$url_voir_activites.'"><div>';
		echo '<input type="hidden" name="id" value="'.$id_evenement.'" />';
		echo "<select name='statut' class='fondl' onchange='form.submit()'>\n";
		echo '<option value="%"';
		if ($statut=="%") {echo ' selected="selected"';}
		echo '>'._T('asso:activite_entete_toutes').'</option>';
		echo '<option value="ok"';
		if ($statut=="ok") { echo ' selected="selected"'; }
		echo '>'._T('asso:activite_entete_validees').'</option>';
		echo "</select></div></form></td></tr></table>\n";

	//TABLEAU
		echo '<form action="'.generer_url_ecrire('action_activites').'" method="post">';
		echo "\n<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th style="text-align: center;">'._T('asso:activite_entete_id')."</th>\n";
		echo '<th style="text-align: center;">'._T('asso:activite_entete_date')."</th>\n";
		echo '<th style="text-align: center;">'._T('asso:activite_entete_nom')."</th>\n";
		echo '<th style="text-align: center;">'._T('asso:activite_entete_adherent')."</th>\n";
		echo '<th style="text-align: center;">'._T('asso:activite_entete_inscrits')."</th>\n";
		echo '<th style="text-align: center;">'._T('asso:activite_entete_montant')."</th>\n";
		echo '<th colspan="3" style="text-align: center;">'._T('asso:activite_entete_action')."</th>\n";
		echo '</tr>';
		$query = sql_select("*", "spip_asso_activites", "id_evenement=$id_evenement AND statut like '$statut'  ", '', "id_activite") ;

		while ($data = sql_fetch($query)) {

			$id = $data['id_adherent'];
			$adh = !$id ? 'X' :
			  ("<a href='" .generer_url_ecrire('voir_adherent', "id=$id") . "'>$id</a>");
			if($data['statut']=="ok") { $class= "valide"; }
			else { $class="pair"; }

			echo "\n<tr>";
			echo '<td style="text-align:right;" class="'.$class. ' border1">'.$data['id_activite']."</td>\n";
			echo '<td style="text-align: center;;" class="'.$class. ' border1">'.association_datefr($data['date'])."</td>\n";
			echo '<td class="'.$class. ' border1">';
			if(empty($data['email'])) { echo $data['nom']; }
			else { echo '<a href="mailto:'.$data['email'].'">'.$data['nom'].'</a>'; }
			echo "</td>\n";
			echo '<td style="text-align: right;" class="'.$class. ' border1">'.$adh."</td>\n";
			echo '<td style="text-align: right;" class="'.$class. ' border1">'.$data['inscrits']."</td>\n";
			echo '<td style="text-align: right;" class="'.$class. ' border1">'.number_format($data['montant'], 2, ',', ' ')."</td>\n";
			echo '<td style="text-align: center;" class="'.$class. ' border1">', association_bouton(_T('asso:activite_bouton_maj_inscription'), 'edit-12.gif', 'edit_activite','id='.$data['id_activite']), "</td>\n";
			echo '<td style="text-align: center;" class="'.$class. ' border1">', association_bouton(_T('asso:activite_bouton_ajouter_inscription'), 'cotis-12.gif', 'ajout_participation', 'id='.$data['id_activite']), "</td>\n";
			echo '<td style="text-align: center;" class="'.$class. ' border1"><input name="delete[]" type="checkbox" value="'.$data['id_activite'].'" /></td>';
			echo '</tr>';
			if ($data['commentaire']) {	echo '<tr><td colspan="10" style="text-align: justify;" class ='.$class.'>'.$data['commentaire']."</td></tr>\n"; }
		}
		echo '</table>';

		echo "\n<table width='100%'><tr><td style='text-align: right;'>";
		echo '<input type="submit" value="'._T('asso:activite_bouton_supprimer').'" class="fondo" />';
		echo "</td></tr></table>\n";
		echo '</form>';

		fin_cadre_relief();
		echo fin_page_association();
	}
}
?>
