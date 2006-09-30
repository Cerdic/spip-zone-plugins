<?php

	function article_pdf_insertion_racourci($arg){
		$icone = find_in_path('img_pack/article_pdf.png');
		$url = generer_url_public('article_pdf',$arg);
		$code = "<a href='$url' title='Enregistrer au format PDF'><img src='$icone' width='24' height='24' alt='Creer un PDF' />Enregistrer au format PDF</a>";
		return $code;
	}
	function balise_ARTICLE_PDF_dist($p) {
		if ($p->param && !$p->param[0][0]){
			$quoi =  $p->param[0][1];
			$nom = ($quoi[0]->type=='texte') ? $quoi[0]->texte : "";
		}
	
		if (!$nom) {
			// pas de parametre en argument
			// on cherche dans le contexte d'abord id_article, puis sinon id_rubrique
			$_id_article = champ_sql('id_article', $p);
			$arg = "'id_article='.".$_id_article;
			if (!$_id_article){
				$_id_rubrique = champ_sql('id_rubrique', $p);
				$arg = "'$nom='.".$_id_rubrique;
			}
		}
		else {		
			if ($nom=='id_article'){
				$_id_article = champ_sql('id_article', $p);
				$arg = "'$nom='.".$_id_article;
			}
			else if ($nom=='id_rubrique'){
				$_id_rubrique = champ_sql('id_rubrique', $p);
				$arg = "'$nom='.".$_id_rubrique;
			}
			else 
				$arg="'$nom'";
		}
		$icone = find_in_path('img_pack/article_pdf.png');
		$url = generer_url_public('article_pdf',$arg);
		if ($arg)
		$p->code = "article_pdf_insertion_racourci($arg)";
	
		#$p->interdire_scripts = true;
	
		return $p;
	}	
?>
