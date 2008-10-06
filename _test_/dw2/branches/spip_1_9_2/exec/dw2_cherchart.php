<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| iframe de "affect_doc", recup articles de Rub
+--------------------------------------------+
*/
include_spip('inc/presentation');

function exec_dw2_cherchart() {
	
	global $id_rub;
	
	if($id_rub) {
		$id_rub=intval($id_rub);
	}
	init_entete('','');
	
	echo "<body>\n";

	// regenerer le formulaire
	echo "<form name='table_art' action='".generer_url_ecrire("dw2_cherchart")."' method='post' >
			<input type='hidden' name='id_rub' /></form>";
	

	
	//
	// requete .. les articles de la rub selectionnee
	//
	$query="SELECT id_article, titre FROM spip_articles WHERE id_rubrique=$id_rub";
	$result=spip_query($query);
	
		# efface options de precedente recherche
		print("<script>");
		print("with (parent.document){");
		print("var index = designedoc.proposition.options.length;");
		print("for (var i=0; i<index; i++){designedoc.proposition.options[i]=null;}");
		print("designedoc.proposition.options.length=0;");
		print("}");
		print("</script>");
	
	if(!spip_num_rows($result)) {
		# rub sans article .. on le dit
		print("<script>");
		print("with (parent.document){");
		print("n = new Option('"._T('dw:rub_sans_art')."','');");
		print("var index = designedoc.proposition.options.length;");
		print("designedoc.proposition.options[index]=n;");
		print("}");
		print("</script>");
		return;
	} else {
		# articles ! .. propose de choisir
		print("<script>");
		print("with (parent.document){");
		print("n = new Option('"._T('dw:choix_article_liste')."','');");
		print("var index = designedoc.proposition.options.length;");
		print("designedoc.proposition.options[index]=n;");
		print("}");
		print("</script>");
		
		# les articles dispo
		while ($row = spip_fetch_array($result)) {
			$id_article=$row['id_article'];
			$titre=$row['titre'];
			print("<script>");
			print("with (parent.document){");
			print("n = new Option(\"$titre\",\"$id_article\");");
			print("var index = designedoc.proposition.options.length;");
			print("designedoc.proposition.options[index]=n;");
			print("}");
			print("</script>");
		}
	}
	echo "\n</body>\n</html>";
}
?>
