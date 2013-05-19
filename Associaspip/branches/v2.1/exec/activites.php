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
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');

function exec_activites(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'activites') OR !test_plugin_actif('agenda')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets(_T('asso:titre_onglet_activite'));
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite("",true);
		include_spip('inc/plugin');
		$liste = liste_plugin_actifs();

		if (isset($liste['AGENDA']))
			exec_activites_evenements(_request('mot'));
		else echo _T('asso:config_libelle_activites');
		echo fin_page_association();
	}
}


function exec_activites_evenements($mot){
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_toutes_activites'));

		// FILTRES
		if (!preg_match('/^[\w%]+$/', $mot))  $mot= "%";

		echo '<table width="100%">';
		echo '<tr>';
		echo '<td>';
		$annee=$_GET['annee'];
		if(empty($annee)){$annee = date('Y');}
		$query = sql_select("date_format( date_debut, '%Y' )  AS annee", "spip_evenements", "", "annee", "annee");
		while ($data = sql_fetch($query)) {
			if ($data['annee']==$annee) { echo ' <strong>'.$data['annee'].'</strong> '; }
			else { echo '<a href="'. generer_url_ecrire('activites','annee='.$data['annee'].'&mot='.$mot).'">'.$data['annee'].'</a> ';}
		}
		echo "</td>\n";
		echo '<td style="text-align:right;">';
		echo '<form method="post" action="activites"><div>';
		echo '<select name ="mot" class="fondl" onchange="form.submit()">';
		echo '<option value="%"';
		if ($mot=="%") { echo ' selected="selected"'; }
		echo '> Toutes</option>';
		$query = sql_select("titre", "spip_mots", "type='Evènements'");
		while($data = sql_fetch($query)) {
			echo '<option value="'.$data["titre"].'"';
			if ($mot==$data["titre"]) { echo ' selected="selected"'; }
			echo '> '.$data["titre"]."</option>\n";
		}
		echo '</select>';
		echo '</div></form>';
		echo "</td></tr></table>\n";

		//TABLEAU
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th style="text-align:right;">' . _T('asso:id') . "</th>";
		echo '<th>'._T('asso:activite_entete_date')."</th>";
		echo '<th>'._T('asso:activite_entete_heure')."</th>";
		echo '<th>'._T('asso:activite_entete_intitule')."</th>";
		echo '<th>'._T('asso:activite_entete_lieu')."</th>";
		echo '<th>'._T('asso:activite_entete_inscrits')."</th>";
		echo '<th colspan="3" style="text-align:center;">'._T('asso:activite_entete_action')."</th>";
		echo '</tr>';

		$max_par_page=30;
		$debut=intval($_GET['debut']);
		if (!$debut) { $debut=0; }

		$query = sql_select('*, E.id_evenement, E.titre AS intitule, M.titre AS motact', 'spip_evenements AS E LEFT JOIN spip_mots_evenements AS A ON  A.id_evenement=E.id_evenement LEFT JOIN spip_mots AS M ON A.id_mot=M.id_mot', "date_format( date_debut, '%Y' ) = $annee AND (M.titre like '$mot' OR M.titre IS NULL)", '', "date_debut DESC",  "$debut,$max_par_page");
		while ($data = sql_fetch($query)) {
			$date = substr($data['date_debut'],0,10);
			$heure = substr($data['date_debut'],10,6);
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['id_evenement']."</td>\n";
			//echo '<td >'.$jour.'-'.$mois. '-'.$annee."</td>\n";
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.association_datefr($date)."</td>\n";
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.$heure."</td>\n";
			echo '<td style="border-top: 1px solid #CCCCCC;">'.$data['intitule']."</td>\n";
			echo '<td style="border-top: 1px solid #CCCCCC;">'.$data['lieu']."</td>\n";
			$sql = sql_select("sum(inscrits) AS total", "spip_asso_activites", "id_evenement=".$data['id_evenement']);
			while ($inscrits = sql_fetch($sql)) { echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;">'.$inscrits['total']."</td>\n"; }
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:center">' . association_bouton(_T('asso:activite_bouton_modifier_article'), 'edit-12.gif', 'articles', 'id_article='.$data['id_article']) . "</td>\n";
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:center">' . association_bouton(_T('asso:activite_bouton_ajouter_inscription'), 'creer-12.gif', 'edit_activite', 'id_evenement='.$data['id_evenement']) . "</td>\n";
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:center">' . association_bouton(_T('asso:activite_bouton_voir_liste_inscriptions'), 'voir-12.png', 'voir_activites', 'id='.$data['id_evenement']) . "</td>\n";
			echo '</tr>';
		}
		echo '</table>';

		echo "\n<table width='100%'>\n";
		echo '<tr>';

		//SOUS-PAGINATION
		echo '<td>';
		$nombre_selection=sql_countsel("spip_evenements", "date_format( date_debut, '%Y' ) = $annee");

		$pages=ceil($nombre_selection/$max_par_page);

		if ($pages == 1) { echo ''; }
		else {
			for ($i=0;$i<$pages;$i++) {
				$position= $i * $max_par_page;
				if ($position == $debut)
				  { echo ' <strong>'.$position.' </strong> '; }
				else { echo '<a href="'.generer_url_ecrire('activites','annee='.$annee.'&debut='.$position.'&imputation='.$imputation).'">'.$position.'</a>  '; }
			}
		}
		echo "</td>\n";
		echo '</table>';

		fin_cadre_relief();
}
?>
