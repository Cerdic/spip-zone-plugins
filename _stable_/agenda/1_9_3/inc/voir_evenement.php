<?php
include_spip("inc/agenda_gestion");

function inc_voir_evenement_dist($id_evenement, $flag_editable){
	$out = "";
	$del = _request('del');
	
	$out .= "<div class='agenda-visu-evenement'>";

	if ($id_evenement!=NULL){
		$res = spip_query("SELECT evenements.* FROM spip_evenements AS evenements WHERE evenements.id_evenement="._q($id_evenement));
		if ($row = sql_fetch($res)){
			if (!isset($neweven)){
				$fid_evenement=$row['id_evenement'];
				$ftitre=attribut_html(typo($row['titre']));
				$flieu=attribut_html(typo($row['lieu']));

				$fhoraire=attribut_html($row['horaire']);
				$fdescriptif=attribut_html(typo($row['descriptif']));
				$fstdatedeb=strtotime($row['date_debut']);
				$fstdatefin=strtotime($row['date_fin']);
				$fid_evenement_source=$row['id_evenement_source'];
			}
	 	}
		$res2 = spip_query("SELECT articles.* FROM spip_articles AS articles LEFT JOIN spip_evenements AS J ON J.id_article=articles.id_article WHERE J.id_evenement="._q($id_evenement));
		if ($row2 = sql_fetch($res2)){
			$out .= "<div class='article-evenement'>";
			$out .= "<a href='".generer_url_ecrire('articles',"id_article=".$row2['id_article'])."'>";
			$out .= http_img_pack("article-24.gif", "", "width='24' height='24' style='border:none;'");
			$out .= entites_html(typo($row2['titre']))."</a>";
			$out .= "</div>\n";
		}
		
		$out .= "<div class='agenda-visu-evenement-bouton-fermer'>";
		$url=self();
		$url=parametre_url($url,'edit','');
		$url=parametre_url($url,'neweven','');
		$url=parametre_url($url,'del','');
		$url=parametre_url($url,'id_evenement','');

		$out .= "<a href='$url' onclick=\"$('#voir_evenement-0').html('');return false;\">";
		$out .= "<img src='"._DIR_PLUGIN_AGENDA."img_pack/croix.png' width='12' height='12' style='border:none;' alt='' /></a>";
		$out .= "</div>\n";

		$fobjet = entites_html($fobjet,ENT_QUOTES);
		$flieu = entites_html($flieu,ENT_QUOTES);
		$fdescription = entites_html($fdescription,ENT_QUOTES);

		$out .= "<div class='titre-titre'>";
		$out .= _T('agenda:evenement_titre');
		$out .= "</div><div class='titre-visu'>$ftitre &nbsp;</div>\n";

		$out .= "<div class='lieu-titre'>";
		$out .= _T('agenda:evenement_lieu');
		$out .= "</div><div class='lieu-visu'>$flieu &nbsp;</div>\n";
		$out .= "<div class='horaire-titre'>&nbsp;</div>";

		$out .= "<div class='date-titre'>";
		$out .= _T('agenda:evenement_date'); 
		$out .= "</div>";
		$out .= "<div class='date-visu'>";
		$out .= _T('agenda:evenement_date_du'); 
		$out .= " ".affdate_jourcourt(date("Y-m-d H:i",$fstdatedeb))." ";



		if ($fhoraire=='oui')
			$out .= _T('agenda:evenement_date_a_immediat'); 
			$out .= " ".date("H:i",$fstdatedeb);

		$out .= " <br/>\n";

		$out .= _T('agenda:evenement_date_au'); 
		$out .= " ".affdate_jourcourt(date("Y-m-d H:i",$fstdatefin))." ";

		if ($fhoraire=='oui')
			$out .= _T('agenda:evenement_date_a_immediat'); 
			$out .= " ".date("H:i",$fstdatefin);

		$out .= " <br/>\n";
		$out .= "</div>\n";

		$out .= "<div class='descriptif-titre'>";
		$out .= _T('agenda:evenement_descriptif'); 
		$out .= "</div><div class='descriptif-visu'>$fdescriptif &nbsp;</div>\n";

		$out .=  "<div class='agenda_mots_cles'>";
		$res = spip_query("SELECT * FROM spip_groupes_mots WHERE evenements='oui' ORDER BY titre");
		$sep = "";
		while ($row = mysql_fetch_array($res,MYSQL_ASSOC)){
			$id_groupe = $row['id_groupe'];
			$row2 = sql_fetch(
						spip_query("SELECT mots.titre FROM spip_mots_evenements AS mots_evenements
								LEFT JOIN spip_mots AS mots ON mots.id_mot=mots_evenements.id_mot 
								WHERE mots.id_groupe="._q($id_groupe).
								" AND mots_evenements.id_evenement="._q($id_evenement)));
			if ($row2){
				$out .= $sep . supprimer_numero($row['titre'])."&nbsp;:&nbsp;".supprimer_numero($row2['titre']);
				$sep = "\n, ";
			}
		}
		$out .= "</div>\n";
		

		$url = parametre_url(self(),'annee','');
		$url = parametre_url($url,'mois','');
		$url = parametre_url($url,'jour','');

		$out .= "<div class='repetitions-calendrier'>";
		$id_source = $fid_evenement_source?$fid_evenement_source:$id_evenement;
		$res2 = spip_query("SELECT * FROM spip_evenements WHERE id_evenement="._q($id_source)." OR id_evenement_source="._q($id_source)." ORDER BY date_debut");
		if (sql_count($res2)>1){
			$out .= _T('agenda:evenement_autres_occurences');
			while($row2 = sql_fetch($res2)){
				if ($row2['id_evenement']!=$fid_evenement){
					$url = parametre_url(self(),'id_evenement',$row2['id_evenement']);
					$out .= " <a href='$url'>" . affdate_jourcourt($row2['date_debut']) ."</a>";
				}
			}
		}
		$out .= "</div>";
	
		if ($fid_evenement_source!=0){
			$res2 = spip_query("SELECT evenements.* FROM spip_evenements AS evenements WHERE evenements.id_evenement="._q($fid_evenement_source));
			if ($row2 = sql_fetch($res2)){
				$url = parametre_url($url,'id_evenement',$row2['id_evenement']);
			  $out .= "<div class='edition-bouton'>";
			  $out .= _T('agenda:repetition_de')." <a href='";
			  $out .= $url;
			  $out .= "'>".($row2['titre']?typo($row2['titre']):_T('agenda:sans_titre'))."</a>";
			  $out .= "</div>";
			}
		}
		else if ($flag_editable){
			$url=self();
			$url=parametre_url($url,'edit','');
			$url=parametre_url($url,'neweven','');
			$url=parametre_url($url,'del','');
			$url=parametre_url($url,'id_evenement','');
			$form = "";
			if ($del==1)	{ //---------------Suppression RDV ------------------------------
			  //$out .= "<form name='edition_rdv' action='$url' method='post'>";
			  $form .= "<input type='hidden' name='id_evenement' value='$fid_evenement' />\n";
			  $form .= "<input type='hidden' name='suppr' value='1' />\n";
			  $form .= "<div class='edition-bouton'>";
			  $form .= "<input type='submit' name='submit' value='Annuler' />";
			  $form .= "<input type='submit' name='submit' value='Confirmer la suppression' />";
			  $form .= "</div>";
			  //$out .= "</form>";
	  	}
	  	else {
				$url=parametre_url($url,'id_evenement',$id_evenement);
				$url=parametre_url($url,'edit',1);
			  //$out .= "<form name='edition_rdv' action='$url' method='post'>";
			  $form .= "<div class='edition-bouton'>";
				$form .= "<div style='text-align:$spip_lang_right'><input type='submit' name='modifier' value='"._T('bouton_modifier')."' class='fondo'></div>";
			  $form .= "</div>";
			  //$out .= "</form>";
	  	}
			$args = explode('?',parametre_url($url,'exec','','&'));
	  	$out .= ajax_action_auteur('voir_evenement',"0-voir","calendrier",end($args),$form,'','wc_init');
		}
	}
	$out .= "</div>";
	return $out;	
}

?>