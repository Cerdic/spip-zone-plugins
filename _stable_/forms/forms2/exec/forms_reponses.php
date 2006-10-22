<?php
/*
 * forms
 * version plug-in de spip_form
 *
 * Auteur :
 * Antoine Pitrou
 * adaptation en 182e puis plugin par cedric.morin@yterium.com
 * © 2005,2006 - Distribue sous licence GNU/GPL
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
			$structure = unserialize($row['structure']);
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
	$structures = array();
	$form_unique = $id_form;

	$query = "SELECT r.*, a.nom FROM spip_reponses AS r LEFT JOIN spip_auteurs AS a USING (id_auteur) ".
		"$where r.statut='valide' AND r.date > DATE_SUB(NOW(), INTERVAL 6 MONTH) ".
		"ORDER BY r.date DESC LIMIT $debut, $tranche";
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

		// Preparer la table de traduction code->valeur
		$query_form = "SELECT titre, structure FROM spip_forms WHERE id_form=$id_form";
		/*$row_form = spip_fetch_array(spip_query($query_form));
		$structure = unserialize($row_form['structure']);
		$titre_form = $row_form['titre'];*/
		list($titre_form, $structure) = spip_fetch_array(spip_query($query_form),SPIP_NUM);
		$structure = unserialize($structure);
		if (!$trans[$id_form]) {
			foreach ($structure as $index => $t) {
				$code = $t['code'];
				$type = $t['type'];
				$type_ext = $t['type_ext'];
				$types[$id_form][$code] = $type;
				$trans[$id_form][$code] = array();

				if ($type == 'select' || $type == 'multiple') {
					$trans[$id_form][$code] = array_map('typo', $t['type_ext']);
				}
				else if ($type == 'mot') {
					$id_groupe = intval($t['type_ext']['id_groupe']);
					$query_mot = "SELECT id_mot, titre FROM spip_mots WHERE id_groupe=$id_groupe";
					$result_mot = spip_query($query_mot);
					while ($row2 = spip_fetch_array($result_mot)) {
						$id_mot = $row2['id_mot'];
						$titre = $row2['titre'];
						$trans[$id_form][$code][$id_mot] = "<a href='".generer_url_ecrire("mots_edit","id_mot=$id_mot")."'>".typo($titre)."</a>";
					}
				}
			}
		}

		// Lire les valeurs entrees
		$query2 = "SELECT * FROM spip_reponses_champs WHERE id_reponse=$id_reponse";
		$result2 = spip_query($query2);
		$valeurs = array();
		$retour = urlencode(self());
		while ($row2 = spip_fetch_array($result2)) {
			$champ = $row2['champ'];
			if ($types[$id_form][$champ] == 'fichier') {
				$valeurs[$champ][] = "<a href='".generer_url_ecrire("forms_telecharger","id_reponse=$id_reponse&champ=$champ&retour=$retour")."'>".
					$row2['valeur']."</a>";
			}
			else if (isset($trans[$id_form][$champ][$row2['valeur']]))
				$valeurs[$champ][] = $trans[$id_form][$champ][$row2['valeur']];
			else
				$valeurs[$champ][] = propre($row2['valeur']);
		}

		echo "<br />\n";

		debut_cadre_relief("../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png");

		$link=parametre_url(self(),'supp_reponse', $id_reponse);
		icone(_T("forms:supprimer_reponse"), $link,"../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png", "supprimer.gif", "right");
		
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

		foreach ($structure as $index => $t) {
			$nom = $t['nom'];
			$code = $t['code'];
			$type = $t['type'];
			if (!$v = $valeurs[$code]) continue;
			$n = count($v);
			if ($n > 1) {
				$s = join(', ', $v)."\n";
			}
			else $s = join('', $v);
			echo "<span class='verdana1' style='text-transform: uppercase; font-weight: bold; color: #404040;'>";
			echo propre($nom)." :</span>";
			echo "&nbsp; ".$s;
			echo "<br />\n";
		}

		echo "<div style='clear: both;'></div>";

		fin_cadre_relief();
	}



	fin_page();
}

?>
