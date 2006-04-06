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

include_ecrire('inc_forms');

function exec_forms_reponses(){
	global $id_form;
	global $supp_reponse;
  include_ecrire("inc_presentation");

	$id_form = intval($id_form);

	if ($id_form) {
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$schema = unserialize($row['schema']);
			$sondage = $row['sondage'];
		}
	}


	debut_page("&laquo; ".textebrut(typo($titre))." &raquo;", "redacteurs", "suivi-forms");
	debut_gauche();

	if ($id_form) {
		debut_boite_info();

		icone_horizontale(_L("Aller au formulaire"), "?exec=forms_edit&id_form=$id_form", "../"._DIR_PLUGIN_FORMS. "/img_pack/form-24.png", "rien.gif");

		if (Forms_form_administrable($id_form)) {
			icone_horizontale(_L("T&eacute;l&eacute;charger les r&eacute;ponses"),
				"?exec=forms_telecharger&id_form=$id_form", "doc-24.gif", "rien.gif");
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
		gros_titre(_L("Suivi du formulaire")."&nbsp;: &laquo;&nbsp;".typo($titre)."&nbsp;&raquo;");
	}
	else {
		gros_titre(_L("Suivi des formulaires"));
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
	list($total) = spip_fetch_array($result);

	if ($total > $tranche) {
		echo "<br />";
		for ($i = 0; $i < $total; $i = $i + $tranche){
			if ($i > 0) echo " | ";
			if ($i == $debut)
				echo "<strong>$i</strong>";
			else {
				//objet link supprime remplace par parametre_url()
				//$link = new Link();
				//$link->addVar('debut', strval($i));
				//echo "<a href='".$link->getUrl()."'>$i</a>";
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
	$schemas = array();
	$form_unique = $id_form;

	$query = "SELECT r.*, a.nom FROM spip_reponses AS r LEFT JOIN spip_auteurs AS a USING (id_auteur) ".
		"$where r.statut='valide' AND r.date > DATE_SUB(NOW(), INTERVAL 6 MONTH) ".
		"ORDER BY r.date DESC LIMIT $debut, $tranche";
	$result = spip_query($query);
	while ($row = spip_fetch_array($result)) {
		$id_form = $row['id_form'];
		$id_reponse = $row['id_reponse'];
		$date = $row['date'];
		$ip = $row['ip'];
		$id_auteur = $row['id_auteur'];
		$nom_auteur = $row['nom'];

		// Preparer la table de traduction code->valeur
		$query_form = "SELECT titre, schema FROM spip_forms WHERE id_form=$id_form";
		/*$row_form = spip_fetch_array(spip_query($query_form));
		$schema = unserialize($row_form['schema']);
		$titre_form = $row_form['titre'];*/
		list($titre_form, $schema) = spip_fetch_array(spip_query($query_form));
		$schema = unserialize($schema);
		if (!$trans[$id_form]) {
			foreach ($schema as $index => $t) {
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
					while ($row = spip_fetch_array($result_mot)) {
						$id_mot = $row['id_mot'];
						$titre = $row['titre'];
						$trans[$id_form][$code][$id_mot] = "<a href='mots_edit.php?id_mot=$id_mot'>".typo($titre)."</a>";
					}
				}
			}
		}

		// Lire les valeurs entrees
		$query2 = "SELECT * FROM spip_reponses_champs WHERE id_reponse=$id_reponse";
		$result2 = spip_query($query2);
		$valeurs = array();
		while ($row2 = spip_fetch_array($result2)) {
			$champ = $row2['champ'];
			if ($types[$id_form][$champ] == 'fichier') {
				$valeurs[$champ][] = "<a href='?exec=forms_telecharger.php&id_reponse=$id_reponse&champ=$champ'>".
					$row2['valeur']."</a>";
			}
			else if (isset($trans[$id_form][$champ][$row2['valeur']]))
				$valeurs[$champ][] = $trans[$id_form][$champ][$row2['valeur']];
			else
				$valeurs[$champ][] = propre($row2['valeur']);
		}

		echo "<br />\n";

		debut_cadre_relief("../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png");

		//$link = new Link();
		//$link->addVar('supp_reponse', $id_reponse);
		//icone(_L("Supprimer cette r&eacute;ponse"), $link->getUrl(),"../"._DIR_PLUGIN_FORMS."/form-24.png", "supprimer.gif", "right");
		$link=parametre_url(self(),'supp_reponse', $id_reponse);
		icone(_L("Supprimer cette r&eacute;ponse"), $link,"../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png", "supprimer.gif", "right");

		echo _L("R&eacute;ponse envoy&eacute;e le ").affdate($date)."<br />\n";
		if (!$form_unique) {
			//echo "<div style='padding: 2px; background-color: $couleur_claire;'>";
			echo _L("&agrave;")." ";
			echo "<strong class='verdana3' style='font-weight: bold;'><a href='?exec=forms_reponses&id_form=$id_form'>"
				.typo($titre_form)."</a></strong> ";
			//echo "<br />\n";
			//echo "</div>\n";
		}

		if ($id_auteur) {
			$s = "<a href='auteur_infos.php3?id_auteur=$id_auteur'>".typo($nom_auteur)."</a>";
			echo _T('forum_par_auteur', array('auteur' => $s));
			echo "<br />\n";
		}
		echo "<br />\n";

		foreach ($schema as $index => $t) {
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
