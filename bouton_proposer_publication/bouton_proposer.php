<?php

function bouton_proposer_interface ( $vars="" ) {
		$exec = $vars["args"]["exec"];
		$id_article = $vars["args"]["id_article"];
		
		
		
		if ($exec == "articles" && $GLOBALS["auteur_session"]["statut"] == "1comite" && autoriser('modifier', 'article', $id_article)) {
		
			$q = sql_select("id_article", "spip_articles", "id_article=$id_article AND statut='prepa'");
			
			if ($row = sql_fetch($q)) {
				$id_auteur = $GLOBALS["auteur_session"]["id_auteur"];
			
				$q_auteur = sql_select("id_article", "spip_auteurs_articles", "id_article=$id_article AND id_auteur=$id_auteur");
				if ($row_auteur = sql_fetch($q_auteur)) {
					$ret .= debut_cadre_relief("", true);
					$ret .= "<div class='verdana3' style='text-align: center;'>";
					$ret .= "<div>"._T("texte_proposer_publication")."</div>";

					$href = redirige_action_auteur('instituer_article',$id_article,'articles', "id_article=$id_article");
					
					$ret .= "<div style='padding: 5px; font-weight: bold;'><a href='"
						. parametre_url($href,'statut_nouv',"prop")
						. "' onclick='return confirm(confirm_changer_statut);' style='color: red;'>"._T("bouton_demande_publication")."</a></div>";

					$ret .= "</div>";
					$ret .= fin_cadre_relief(true);
						
						
				}
			}
			
		}

	$vars["data"] .= $ret;
		
	return $vars;
}

?>