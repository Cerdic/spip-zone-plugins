<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
include_spip('inc/forms');

function action_forms_exporte_reponse_article(){
	$id_reponse = _request('arg');
	$hash = _request('hash');
	$id_auteur = _request('id_auteur');
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
	include_spip("inc/actions");
	if (verifier_action_auteur("forms_exporte_reponse_article-$id_reponse",$hash,$id_auteur)==TRUE){
		// preparer l'article
		$id_article = 0;
		$res = spip_query("SELECT * FROM spip_reponses AS r LEFT JOIN spip_forms AS f ON f.id_form = r.id_form WHERE r.id_reponse="._q($id_reponse));
		if ($row=spip_fetch_array($res)){
			$id_form = $row['id_form'];
			$titre = _T("forms:reponse",array('id_reponse'=>$id_reponse));
			$soustitre = $row['titre'];
			$date = $row['date'];
			list($lib,$values,$urls) = 	Forms_extraire_reponse($id_reponse);
			$texte = "";
			$res = spip_query("SELECT * FROM spip_forms_champs AS forms WHERE id_form="._q($id_form)." ORDER BY cle");
			while ($row = spip_fetch_array($res)){
				$titre = $row['titre'];
				$champ = $row['champ'];
				$cle = $row['cle'];
				$type = $row['type'];
				if (!isset($values[$cle])){
					switch ($type){
						case 'textestatique':	$texte .= "\n{{{$titre}}}\n\n";	break;
						case 'separateur':	$texte .= "\n{{{{$titre}}}}\n\n";	break;
					}
				}
				else {
					$s = '';
					if (count($values[$cle])>1) $s = "\n-* ";
					foreach ($values[$cle] as $id=>$valeur){
						$valeur = typo($valeur);
						if(strlen($s)) $s .= "\n-* ";
						if ($lien = $urls[$cle][$id])
							$s .= "[$valeur -> $lien]";
						else
							$s .= $valeur;
					}
					switch ($type){
						case 'texte':	$texte .= "\n{{{$titre}}}\n_ $s\n";	break;
						case 'url':	$texte .= "_ {{{$titre}}} : [$s -> $s]\n";	break;
						case 'email':	$texte .= "_ {{{$titre}}} : [$s -> mailto:$s]\n";	break;
						default:
							$texte .= "_ {{{$titre}}} : $s\n";	break;
					}
				}
			}
				
			// creer un article
			include_spip('base/abstract_sql');
			$id_article = spip_abstract_insert("spip_articles",
			"(titre,soustitre,texte,date,statut)",
			"("._q($titre).","._q($soustitre).","._q($texte).","._q($date).",'prepa')");
			
			if ($id_article!=0){
				spip_query("UPDATE spip_reponses SET id_article_export=$id_article WHERE id_reponse="._q($id_reponse));
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