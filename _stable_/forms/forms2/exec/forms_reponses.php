<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * � 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/forms');

function exec_forms_reponses(){
	global $id_form;
	global $supp_reponse;
	$debut = _request('debut');
  include_spip("inc/presentation");
  Forms_install();

	$id_form = intval($id_form);

	if ($id_form) {
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$sondage = $row['sondage'];
		}
	}


	debut_page("&laquo; ".textebrut(typo($titre))." &raquo;", "redacteurs", "suivi-forms");
	debut_gauche();

	if ($id_form) {
		debut_boite_info();

		icone_horizontale(_T("forms:formulaire_aller"), "?exec=forms_edit&id_form=$id_form", "../"._DIR_PLUGIN_FORMS. "/img_pack/form-24.png", "rien.gif");

		if (Forms_form_administrable($id_form)) {
			$retour = urlencode(self());
			icone_horizontale(_T("forms:telecharger_reponses"),
				generer_url_ecrire("forms_telecharger","id_form=$id_form&retour=$retour"), "doc-24.gif", "rien.gif");
		}

		fin_boite_info();
	}

	debut_droite();


	if (!Forms_form_administrable($id_form)) {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}

	if ($id_form) {
		gros_titre(_T("forms:suivi_formulaire")."&nbsp;: &laquo;&nbsp;".typo($titre)."&nbsp;&raquo;");
	}
	else {
		gros_titre(_T("forms:suivi_formulaires"));
	}


	if ($id_reponse = intval($supp_reponse)) {
		$query = "DELETE FROM spip_reponses WHERE id_reponse=$id_reponse";
		$result = spip_query($query);
		$query = "DELETE FROM spip_reponses_champs WHERE id_reponse=$id_reponse";
		$result = spip_query($query);
	}

	if ($id_form)
		$where = "WHERE id_form=$id_form AND ";
	else
		$where = "WHERE ";

	//
	// Sondage : afficher les cumuls
	//

	if ($id_form && $sondage != 'non') {
		echo "<br />\n";
		debut_cadre_enfonce("statistiques-24.gif");
		include_spip('forms_fonctions');
		echo Forms_afficher_reponses_sondage($id_form);
		fin_cadre_enfonce();
	}

	//
	// Afficher les liens vers les tranches
	//
	$debut = intval($debut);
	$tranche = 10;

	$query = "SELECT COUNT(*) AS cnt FROM spip_reponses ".
		"$where statut='valide' AND date > DATE_SUB(NOW(), INTERVAL 6 MONTH)";
	$result = spip_query($query);
	list($total) = spip_fetch_array($result,SPIP_NUM);

	if ($total > $tranche) {
		echo "<br />";
		for ($i = 0; $i < $total; $i = $i + $tranche){
			if ($i > 0) echo " | ";
			if ($i == $debut)
				echo "<strong>$i</strong>";
			else {
				$link=parametre_url(self(),'debut', strval($i));
				echo "<a href='".$link."'>$i</a>";
				
			}
		}
	}
	echo "<br />\n";


	//
	// Afficher les reponses
	//
	$trans = array();
	$types = array();
	$form_unique = $id_form;

	$query = "SELECT r.*, a.nom, f.titre FROM spip_reponses AS r LEFT JOIN spip_auteurs AS a USING (id_auteur) 
		JOIN spip_forms AS f ON r.id_form=f.id_form
		$where r.statut='valide' AND r.date > DATE_SUB(NOW(), INTERVAL 6 MONTH)
		ORDER BY r.date DESC LIMIT $debut, $tranche";
	$result = spip_query($query);
	while ($row = spip_fetch_array($result)) {
		$id_form = $row['id_form'];
		$id_reponse = $row['id_reponse'];
		$id_article_export = $row['id_article_export'];
		$date = $row['date'];
		$ip = $row['ip'];
		$url = $row['url'];
		$id_auteur = $row['id_auteur'];
		$nom_auteur = $row['nom'];
		$titre_form = $row['titre'];

		echo "<br />\n";
		debut_cadre_relief("../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png");

		$link=parametre_url(self(),'supp_reponse', $id_reponse);
		icone(_T("forms:supprimer_reponse"), $link,"../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png", "supprimer.gif", "right");
		
		if ($id_article_export!=0){
			$row=spip_fetch_array(spip_query("SELECT statut FROM spip_articles WHERE id_article="._q($id_article_export)));
			if (!$row OR ($row['statut']=='poubelle'))
				$id_article_export = 0;
		}
		if ($id_article_export==0){
			icone(_T("forms:exporter_article"), generer_action_auteur('forms_exporte_reponse_article',"$id_reponse",self()),"article-24.gif", "creer.gif", "right");
		}
		else 
			icone(_T("forms:voir_article"), generer_url_ecrire('articles',"id_article=".$row['id_article_export']),"article-24.gif", "", "right");
		

		echo _T("forms:reponse_envoyee").affdate($date)."<br />\n";
		if (!$form_unique) {
			echo _T("forms:reponse_envoyee_a")." ";
			echo "<strong class='verdana3' style='font-weight: bold;'><a href='?exec=forms_reponses&id_form=$id_form'>"
				.typo($titre_form)."</a></strong> ";
		}

		if ($id_auteur) {
			$s = "<a href='".generer_url_ecrire("auteur_infos","id_auteur=$id_auteur")."'>".typo($nom_auteur)."</a>";
			echo _T('forum_par_auteur', array('auteur' => $s));
			echo "<br />\n";
		}
		echo _T("forms:reponse_depuis")."<a href='"._DIR_RACINE."$url'>".$url."</a><br />\n";
		
		echo "<br />\n";

		list($lib,$values,$urls) = 	Forms_extraire_reponse($id_reponse);

		foreach ($lib as $cle => $titre) {
			$s = '';
			foreach ($values[$cle] as $id=>$valeur){
				$valeur = typo($valeur);
				if(strlen($s)) $s .= ", ";
				if ($lien = $urls[$cle][$id])
					$s .= "<a href='$lien'>$valeur</a>";
				else
					$s .= $valeur;
			}
			echo "<span class='verdana1' style='text-transform: uppercase; font-weight: bold; color: #404040;'>";
			echo typo($titre)."&nbsp;:</span>";
			echo " ".$s;
			echo "<br />\n";
		}

		echo "<div style='clear: both;'></div>";

		fin_cadre_relief();
	}



	fin_page();
}

?>
