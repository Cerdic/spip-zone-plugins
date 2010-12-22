<?php
function inc_article_afficher_contenu_dist($id_article){
		$row = sql_fetsel("*", "spip_articles", "id_article=$id_article");
		
		global $spip_lang_right, $dir_lang;
		
		$id_rubrique = $row['id_rubrique'];
		$id_secteur = $row['id_secteur'];
		$statut_article = $row['statut'];
		$titre = $row["titre"];
		$surtitre = $row["surtitre"];
		$soustitre = $row["soustitre"];
		$descriptif = $row["descriptif"];
		$nom_site = $row["nom_site"];
		$url_site = $row["url_site"];
		$texte = $row["texte"];
		$ps = $row["ps"];
		$date = $row["date"];
		$date_redac = $row["date_redac"];
		$extra = $row["extra"];
		$id_trad = $row["id_trad"];
		
		$virtuel = (strncmp($row["chapo"],'=',1)!==0) ? '' :
		chapo_redirige(substr($row["chapo"], 1));
		
		$statut_rubrique = autoriser('publierdans', 'rubrique', $id_rubrique);
		$flag_editable = autoriser('modifier', 'article', $id_article);
		
		// Est-ce que quelqu'un a deja ouvert l'article en edition ?
		if ($flag_editable
			AND $GLOBALS['meta']['articles_modif'] != 'non') {
			include_spip('inc/drapeau_edition');
			$modif = mention_qui_edite($id_article, 'article');
		} else
			$modif = array();
		
		
		// chargement prealable des fonctions produisant des formulaires
		
		$dater = charger_fonction('dater', 'inc');
		$editer_mots = charger_fonction('editer_mots', 'inc');
		
		$editer_auteurs = charger_fonction('editer_auteurs', 'inc');
		if ($flag_editable) $edition_seule=lire_config('taa/edition_seule');
		
		if(!$edition_seule)$referencer_traduction = charger_fonction('referencer_traduction', 'inc');
		
		$discuter = charger_fonction('discuter', 'inc');
		
		$meme_rubrique = charger_fonction('meme_rubrique', 'inc');
		$iconifier = charger_fonction('iconifier', 'inc');
		$icone = $iconifier('id_article', $id_article,'articles', false, $flag_editable);
		
		$boite = pipeline ('boite_infos', array('data' => '',
							'args' => array(
							'type'=>'article',
							'id' => $id_article,
							'row' => $row
								)
							));
	if($edition_seule)$form_docs=recuperer_fond('prive/editer/docs',array('objet'=>'articles','id_objet'=>$id_article,'editable'=>'ok'),array('ajax'=>true)) ;
	
	
	// affecter les globales dictant les regles de typographie de la langue
	changer_typo($row['lang']);
	
	/*  Cacher le bouton si édition seule active dans cfg*/
	
	if(!$edition_seule){
		$actions =($flag_editable ? bouton_modifier_articles($id_article, $id_rubrique, $modif, _T('avis_article_modifie', $modif), "article-24.gif", "edit.gif",$spip_lang_right) : "");
		}
	
	
	/*cr&eacute;ation des onglets traduction*/
	
	if (lire_config('langues_multilingue')) $langues_dispos=explode(',',lire_config('langues_multilingue'));
	else $langues_dispos=explode(',',lire_config('langues_utilisees'));
	

	
	$traductions	= array();
	
	if($langues_dispos){		
	if($id_trad>0){
		$sql=sql_select('lang,id_article','spip_articles','id_trad='.$id_trad);
	
	
		while($row=sql_fetch($sql)){
			$traductions[$row['lang']]=$row['id_article'];
			}
	
		$clic = _T('trad_delier');	
		$options = '<div class="options delier">'.icone_inline($clic, ajax_action_auteur("referencer_traduction","$id_article,-$id_trad",'articles', "id_article=$id_article",array($clic)), "traductions-24.gif", "supprimer.gif",'right', false).'</div>';		
		}
	else{
		$id_trad=$id_article;
		$row=sql_fetsel('lang,id_article','spip_articles','id_article='.$id_article);
		$traductions[$row['lang']]=$row['id_article'];
		
		$options =  '<div class="options form_lier"><h2>'._T('taa:lier_traduction').'</h2>'.redirige_action_auteur("referencer_traduction",
			$id_article,
			"articles&id_article=$id_article",
			"id_article=$id_article",
			("<label for='lier_trad'>" . _T('trad_lier') . "</label>" .
			"\n<input type='text' class='fondl' name='lier_trad' id='lier_trad' size='5' />\n"),
			_T('bouton_valider'),
			" class='fondl'").'</div>';
		}
	
	$retour = generer_url_ecrire("articles","id_article=$id_article",false);
	foreach($langues_dispos as $key => $value){
		if($traductions[$value]!=$id_article){
			if(array_key_exists($value,$traductions)){
				$onglets_traduction.='<div class="traduit onglet ajax"><a href="?exec=articles&id_article='.$traductions[$value].'">'.traduire_nom_langue($value).'</a></div>';					
			}
			else{
				include_spip('ecrire/inc/plugin');
				$plugins = liste_chemin_plugin_actifs();
				// Si le plugin traduction rubriques est activé on regarde si on trouve la rubrique traduite
				if($plugins['TRADRUB']){
					$id_rubrique=rubrique_traduction($value,$id_rubrique);
					$section='oui';
					}
				$onglets_traduction.= '<div class="non_traduit onglet"><a href="'.generer_url_ecrire('articles_edit','new=oui&lier_trad='.$id_trad.'&id_rubrique='.$id_rubrique.'&lang_dest='.$value).'" title="'._T('ecrire:info_tout_site2').'">'.traduire_nom_langue($value).'</a></div>';
			
				$action=generer_action_auteur ('changer_langue',$id_article,$retour);
				// Si le plugin traduction rubriques est activé on affiche pas les onglets changement de langue car la langue se change en modifiant la rubrique
				if(!$section){
					$changer_traduction.='<div class="lang onglet"><a href="'.parametre_url($action,'changer_lang',$value).'">'.traduire_nom_langue($value).'</a></div>';					
					}

				}
			}
		else{
			$onglets_traduction.='<div class="onglet_off onglet">'.traduire_nom_langue($value).'</div>';
			}
		}
	}
	$contexte=array(
		'onglets_traduction'=>$onglets_traduction,
		'options'=>$options,
		'langue_article'=>$langue_article,
		'changer_traduction'=>$changer_traduction,
		'edition_seule'=>$edition_seule,					
		);	
	
	if(!autoriser('modifier','article',$id_article))$options='';
	$haut =
	"<div class='bandeau_actions'>$actions</div>".
	(_INTERFACE_ONGLETS?"":"<span $dir_lang class='arial1 spip_medium'><b>" . typo($surtitre) . "</b></span>\n")
	. gros_titre($titre, '' , false)
	. (_INTERFACE_ONGLETS?"":"<span $dir_lang class='arial1 spip_medium'><b>" 
			. typo($soustitre) . "</b></span>" /*ajout des onglets traductions*/
	. recuperer_fond('prive/editer/barre_traductions_article',$contexte,array('ajax'=>true)));
	
	//MODIFICATION insertion du formulaire d&eacute;edition
	if($edition_seule AND autoriser('modifier','article',$id_article)){ 
		$contexte = array(
		'icone_retour'=>icone_inline(_T('icone_retour'), $oups, "article-24.gif", "rien.gif",$GLOBALS['spip_lang_left']),
		'redirect'=>generer_url_ecrire("articles"),
		'titre'=>$titre,
		'new'=>$new?$new:$id_article,
		'id_rubrique'=>$id_rubrique,
		'id_secteur'=>$row['id_secteur'],
		'lier_trad'=>$lier_trad,
		'config_fonc'=>'articles_edit_config',
		// passer row si c'est le retablissement d'une version anterieure
		'row'=> $id_version
		? $row
		: null
		);
		
		$onglet_edition  = recuperer_fond("prive/editer/article_mod",$contexte,array('ajax'=>true));
		}
	else{echo '1';
		$onglet_contenu =afficher_corps_articles($id_article,$virtuel,$row);
		}
	
	$onglet_proprietes = ((!_INTERFACE_ONGLETS) ? "" :"")
	. $dater($id_article, $flag_editable, $statut_article, 'article', 'articles', $date, $date_redac)
	. $editer_auteurs('article', $id_article, $flag_editable, $cherche_auteur, $ids)
	. (!$editer_mots ? '' : $editer_mots('article', $id_article, $cherche_mot, $select_groupe, $flag_editable, false, 'articles'))
	. (!$referencer_traduction ? '' : $referencer_traduction($id_article, $flag_editable, $id_rubrique, $id_trad, $trad_err))
	. pipeline('affiche_milieu',array('args'=>array('exec'=>'articles','id_article'=>$id_article),'data'=>''))
	;
	
	$documenter_objet = charger_fonction('documenter_objet','inc');
	$onglet_documents = $documenter_objet($id_article,'article','articles',$flag_editable);
	$onglet_interactivite = (_INTERFACE_ONGLETS?boites_de_config_articles($id_article):"");
	
	$onglet_discuter = !$statut_forum ? '' : ($discuter($id_article, 'articles', 'id_article', $statut_forum, $debut_forum));
	
	
	return
	$navigation
	. $extra
	. "<div class='fiche_objet'>"
		. $haut
		. afficher_onglets_pages(
		array(
		'voir' => _T('onglet_contenu'),
		'props' => _T('onglet_proprietes'),
		'docs' => _T('onglet_documents'),
		'interactivite' => _T('onglet_interactivite'),
		'discuter' => _T('onglet_discuter')),
		array(
		'props'=>$onglet_edition.$onglet_proprietes, //MODIFICATION insertion de du formulaire d'édition
		'voir'=>$onglet_contenu,
		'docs'=>$onglet_documents,
		'interactivite'=>$onglet_interactivite,
		'discuter'=>_INTERFACE_ONGLETS?$onglet_discuter:""))
		. "</div>"
	. (_INTERFACE_ONGLETS?"":$onglet_discuter);
		}
	
?>