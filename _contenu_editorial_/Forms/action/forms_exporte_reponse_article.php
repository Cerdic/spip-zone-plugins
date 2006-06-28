<?php

function action_forms_exporte_reponse_article(){
	$id_reponse = _request('arg');
	$hash = _request('hash');
	$id_auteur = _request('id_auteur');
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
	
	if (verifier_action_auteur("forms_exporte_reponse_article-$id_reponse",$hash,$id_auteur)==TRUE){
		// preparer l'article
		$id_article = 0;
		$res = spip_query("SELECT * FROM spip_reponses AS r LEFT JOIN spip_forms AS f ON f.id_form = r.id_form WHERE r.id_reponse=".spip_abstract_quote($id_reponse));
		if ($row=spip_fetch_array($res)){
				$id_form = $row['id_form'];
				$titre = _L("Reponse $id_reponse");
				$soustitre = _L($row['titre']);
				$date = $row['date'];
				
				$structure = unserialize($row['structure']);
				foreach ($structure as $index => $t) {
					$code = $t['code'];
					$type = $t['type'];
					$type_ext = $t['type_ext'];
					$types[$id_form][$code] = $type;
					$trans[$id_form][$code] = array();
	
					if ($type == 'select' || $type == 'multiple') {
						$trans[$id_form][$code] = $t['type_ext'];
					}
					else if ($type == 'mot') {
						$id_groupe = intval($t['type_ext']['id_groupe']);
						$query_mot = "SELECT id_mot, titre FROM spip_mots WHERE id_groupe=$id_groupe";
						$result_mot = spip_query($query_mot);
						while ($row = spip_fetch_array($result_mot)) {
							$id_mot = $row['id_mot'];
							$titre = $row['titre'];
							$trans[$id_form][$code][$id_mot] = $titre;
						}
					}
				}
				// Lire les valeurs entrees
				$query2 = "SELECT * FROM spip_reponses_champs WHERE id_reponse=".spip_abstract_quote($id_reponse);
				$result2 = spip_query($query2);
				$valeurs = array();
				while ($row2 = spip_fetch_array($result2)) {
					$champ = $row2['champ'];
					if ($types[$id_form][$champ] == 'fichier') {
						$valeurs[$champ][] = $row2['valeur'];
					}
					else if (isset($trans[$id_form][$champ][$row2['valeur']]))
						$valeurs[$champ][] = $trans[$id_form][$champ][$row2['valeur']];
					else
						$valeurs[$champ][] = $row2['valeur'];
				}
				
				$texte = "";
				foreach ($structure as $index => $t) {
					$nom = $t['nom'];
					$code = $t['code'];
					$type = $t['type'];
					if (!$v = $valeurs[$code]){
						switch ($type){
							case 'textestatique':	$texte .= "\n{{{$nom}}}\n\n";	break;
							case 'separateur':	$texte .= "\n{{{{$nom}}}}\n\n";	break;
						}
					}
					else {
						$n = count($v);
						if ($n > 1) {
							$s = "\n-* " . join("\n-* ", $v);
						}
						else $s = join('', $v);
					
						switch ($type){
							case 'texte':	$texte .= "\n{{{$nom}}}\n_ $s\n";	break;
							case 'url':	$texte .= "_ {{{$nom}}} : [$s -> $s]\n";	break;
							case 'email':	$texte .= "_ {{{$nom}}} : [$s -> mailto:$s]\n";	break;
							default:
								$texte .= "_ {{{$nom}}} : $s\n";	break;
						}
					}
				}
				
				// creer un article
				include_spip('base/abstract_sql');
				$id_article = spip_abstract_insert("spip_articles",
				"(titre,soustitre,texte,date,statut)",
				"(".spip_abstract_quote($titre).",".spip_abstract_quote($soustitre).",".spip_abstract_quote($texte).",".spip_abstract_quote($date).",'prepa')");
				
				if ($id_article!=0){
					spip_query("UPDATE spip_reponses SET id_article_export=$id_article WHERE id_reponse=".spip_abstract_quote($id_reponse));
				}
		}
		if ($id_article!=0)
			redirige_par_entete(generer_url_ecrire('articles_edit',"id_article=$id_article",true));
		else
			redirige_par_entete($redirect);
	}
	else
		redirige_par_entete($redirect);
}
?>