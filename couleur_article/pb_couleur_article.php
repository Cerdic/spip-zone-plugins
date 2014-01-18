<?php


function pb_couleur_article_gauche($vars){


		if ($_POST["pb_couleur_article"] && $GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
			$couleur = str_replace("#", "", $_POST["pb_couleur_article"]);
			$id_article = $_GET["id_article"];
			
			
			ecrire_meta("pb_couleur_article$id_article",$couleur);
			if ($_POST["supprimer"]) ecrire_meta("pb_couleur_article$id_article","");
			ecrire_metas();
			
		}


		$exec = $vars["args"]["exec"];
		$id_article = $vars["args"]["id_article"];
		$data =	$vars["data"];
		
		$ret = "";
		
		
		if ($exec == "articles" && $GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

			$deplier = true;
			$pb_couleur_article = pb_couleur_article($id_article);
			if (!$pb_couleur_article) {
				$pb_couleur_article = "999999";
				$deplier = false;
			}
			

			$titre = "Couleur de l&rsquo;article";
		
			$ret .= debut_cadre_enfonce(_DIR_PLUGIN_PB_COULEUR_ARTICLE."img_pack/icon-24.png", true, "rien.gif", bouton_block_depliable($titre, $deplier, "selection_couleur"));

			$ret .= debut_block_depliable($deplier, "selection_couleur");
		    $ret .= "<form method='post' action='index.php?exec=articles&id_article=$id_article'>";

			$ret .= "<div id='picker' style='margin-left: -5px;'></div>";
			$ret .= "<div style='float: right;'><input class='fondo' type='submit' value='"._L("Enregistrer")."' /></div>";
			$ret .= "<div style='text-align: left; padding-top: 2px;'><input type='text' id='pb_couleur_article' name='pb_couleur_article' value='#$pb_couleur_article'  class='colorwell' style='width: 70px;' /></div>\n"; 

			if ($deplier) {
				$ret .= "<input type='submit' class='fondl' name='supprimer' value='Supprimer la couleur' />";
			}
			
			$ret .= "</form>";
			$ret .= fin_block();
			$ret .= fin_cadre_enfonce(true);



    
    
    

			
		}


		$data .= $ret;
	
		$vars["data"] = $data;
		return $vars;
}



function pb_couleur_article_header($flux){
	if ($_GET["exec"]=="articles") {
		$flux .= "<link rel='stylesheet' type='text/css' href='"._DIR_FARBTASTIC_LIB."farbtastic.css' />\n";     
		$flux .= "<script src='"._DIR_FARBTASTIC_LIB."farbtastic.js' type=\"text/javascript\"></script>\n";
		
		
		$flux .= "<script type=\"text/javascript\">
$(document).ready(function() {
    var f = $.farbtastic('#picker');
    var p = $('#picker').css('opacity', 1);
    var selected;
    $('.colorwell')
      .each(function () { f.linkTo(this); $(this).css('opacity', 0.75); })
      .focus(function() {
        if (selected) {
          $(selected).css('opacity', 0.75).removeClass('colorwell-selected');          
        }
        f.linkTo(this);
        p.css('opacity', 1);
        $(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
});</script>
";
		
	}
	return $flux;

}

?>