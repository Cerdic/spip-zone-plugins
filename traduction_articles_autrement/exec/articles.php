<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions');

// http://doc.spip.org/@exec_articles_dist
function exec_articles_dist()
{
	exec_articles_args(intval(_request('id_article')));
}

// http://doc.spip.org/@exec_articles_args
function exec_articles_args($id_article)
{
	pipeline('exec_init',array('args'=>array('exec'=>'articles','id_article'=>$id_article),'data'=>''));

	$row = sql_fetsel("*", "spip_articles", "id_article=$id_article");

	if (!$row
	OR !autoriser('voir', 'article', $id_article)) {
		include_spip('inc/minipres');
		echo minipres(_T('public:aucun_article'));
	} else {
		$row['titre'] = sinon($row["titre"],_T('info_sans_titre'));

		$res = debut_gauche('accueil',true)
		  .  articles_affiche($id_article, $row, _request('cherche_auteur'), _request('ids'), _request('cherche_mot'), _request('select_groupe'), _request('trad_err'), _request('debut'),$row['id_trad'])/*MODIFICATION passe l'id_trad*/
		  . "<br /><br /><div class='centered'>"
		. "</div>"
		. fin_gauche();

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page("&laquo; ". $row['titre'] ." &raquo;", "naviguer", "articles", $row['id_rubrique']);

		echo debut_grand_cadre(true),
			afficher_hierarchie($row['id_rubrique'],_T('titre_cadre_interieur_rubrique'),$id_article,'article',$row['id_secteur'],($row['statut'] == 'publie')),
			fin_grand_cadre(true),
			$res,
			fin_page();
	}
}

// http://doc.spip.org/@articles_affiche
function articles_affiche($id_article, $row, $cherche_auteur, $ids, $cherche_mot,  $select_groupe, $trad_err, $debut_forum=0, $statut_forum='prive',$id_trad='')/*MODIFICATION re&ccedil;oit l'id_trad*/
{
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
	
	/* MODIFICATION Cacher le menu sélection langue
	$referencer_traduction = charger_fonction('referencer_traduction', 'inc'); 
	 */
	 
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

	$navigation =
	  debut_boite_info(true). $boite . fin_boite_info(true)
	  . $icone
		. (_INTERFACE_ONGLETS?"":boites_de_config_articles($id_article))
	  . ($flag_editable ? boite_article_virtuel($id_article, $virtuel):'')//MODIFICATION insertion du formulaire de la mediathèque
	  .recuperer_fond('prive/editer/docs',array('objet'=>'articles','id_objet'=>$id_article,'editable'=>'ok'),array('ajax'=>true))
	  .afficher_documents_colonne($id_article, 'article')	  
	  . pipeline('affiche_gauche',array('args'=>array('exec'=>'articles','id_article'=>$id_article),'data'=>''));

	$extra = creer_colonne_droite('', true)
		. $meme_rubrique($id_rubrique, $id_article, 'article')
	  . pipeline('affiche_droite',array('args'=>array('exec'=>'articles','id_article'=>$id_article),'data'=>''))
	  . debut_droite('',true);

	// affecter les globales dictant les regles de typographie de la langue
	changer_typo($row['lang']);
	
	/* MODIFICATION Cacher le bouton si édition seule active dans cfg*/
	
	if(!$edition_seule=lire_config('taa/edition_seule') ){
		$actions =($flag_editable ? bouton_modifier_articles($id_article, $id_rubrique, $modif, _T('avis_article_modifie', $modif), "article-24.gif", "edit.gif",$spip_lang_right) : "");
  		}


/*MODIFICATION cr&eacute;ation des onglets traduction*/

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
			
			$options =  '<div class="options form_lier">'.redirige_action_auteur("referencer_traduction",
				$id_article,
				"articles&id_article=$id_article",
				"id_article=$id_article",
				("<label for='lier_trad'>" . _T('trad_lier') . "</label>" .
				 "\n<input type='text' class='fondl' name='lier_trad' id='lier_trad' size='5' />\n"),
				_T('bouton_valider'),
				" class='fondl'").'</div>';
			}
	
		
		foreach($langues_dispos as $key => $value){
			if($traductions[$value]!=$id_article){
				if(array_key_exists($value,$traductions)){
					$onglets_traduction.='<div class="traduit onglet ajax"><a href="?exec=articles&id_article='.$traductions[$value].'">'.traduire_nom_langue($value).'</a></div>';
					}
				else{
					$onglets_traduction.= '<div class="non_traduit onglet"><a href="?exec=articles_edit&new=oui&lier_trad='.$id_trad.'&id_rubrique='.$id_rubrique.'&lang='.$value.'">'.traduire_nom_langue($value).'</a></div>';
					}
				}
			else{
				$onglets_traduction.='<div class="onglet_off onglet">'.traduire_nom_langue($value).'</div>';
				}
			}
		}
		
	if(!autoriser('modifier','article',$id_article))$options='';
	$haut =
		"<div class='bandeau_actions'>$actions</div>".
		(_INTERFACE_ONGLETS?"":"<span $dir_lang class='arial1 spip_medium'><b>" . typo($surtitre) . "</b></span>\n")
		. gros_titre($titre, '' , false)
		. (_INTERFACE_ONGLETS?"":"<span $dir_lang class='arial1 spip_medium'><b>" 
		. typo($soustitre) . "</b></span>" /*ajout des onglets traductions*/
		. '<div class="onglets_traduction articles">'.$onglets_traduction.$options.'</div>');
		
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
	else{
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
	  . (_INTERFACE_ONGLETS?"":$onglet_discuter)
;
}

//
// Boites de configuration avancee
//

// http://doc.spip.org/@boites_de_config_articles
function boites_de_config_articles($id_article)
{
	if (autoriser('modererforum', 'article', $id_article)) {
		$regler_moderation = charger_fonction('regler_moderation', 'inc');
		$regler = $regler_moderation($id_article,"articles","id_article=$id_article") . '<br />';
	}

	$petitionner = charger_fonction('petitionner', 'inc');
	$petition = $petitionner($id_article,"articles","id_article=$id_article");

	$masque = $regler . $petition;

	if (!$masque) return '';

	$invite = "<b>"
	. _T('bouton_forum_petition')
	. aide('confforums')
	. "</b>";

	return
		cadre_depliable("forum-interne-24.gif",
		  $invite,
		  true,//$visible = strstr($masque, '<!-- visible -->')
		  $masque,
		  'forumpetition');
}

// http://doc.spip.org/@boite_article_virtuel
function boite_article_virtuel($id_article, $virtuel)
{
	if (!$virtuel
	AND $GLOBALS['meta']['articles_redirection'] != 'oui')
		return '';

	$invite = '<b>'
	._T('bouton_redirection')
	. '</b>'
	. aide ("artvirt");

	$virtualiser = charger_fonction('virtualiser', 'inc');

	return cadre_depliable("site-24.gif",
		$invite,
		$virtuel,
		$virtualiser($id_article, $virtuel, "articles", "id_article=$id_article"),
		'redirection');
}

// http://doc.spip.org/@bouton_modifier_articles
function bouton_modifier_articles($id_article, $id_rubrique, $flag_modif, $mode, $ip, $im, $align='')
{
	if ($flag_modif) {
		return icone_inline(_T('icone_modifier_article'), generer_url_ecrire("articles_edit","id_article=$id_article"), $ip, $im, $align, false)
		. "<span class='arial1 spip_small'>$mode</span>"
		. aide("artmodif");
	}
	else return icone_inline(_T('icone_modifier_article'), generer_url_ecrire("articles_edit","id_article=$id_article"), "article-24.gif", "edit.gif", $align);
}

// http://doc.spip.org/@afficher_corps_articles
function afficher_corps_articles($id_article, $virtuel, $row)
{
	$res = '';
	if ($row['statut'] == 'prop') {
		$res .= "<p class='article_prop'>"._T('text_article_propose_publication');

		if ($GLOBALS['meta']['forum_prive_objets'] != 'non')
			$res .= ' '._T('text_article_propose_publication_forum');

		$res.= "</p>";
	}

	if ($virtuel) {
		$res .= debut_boite_info(true)
		.  "\n<div style='text-align: center'>"
		. _T('info_renvoi_article')
		. " "
		.  propre("[->$virtuel]")
		. '</div>'
		.  fin_boite_info(true);
	}
	else {
		$type = 'article';
		$contexte = array(
			'id'=>$id_article,
			'id_rubrique'=>$row['id_rubrique'],
			'id_secteur' => $row['id_secteur']
		);
		$fond = recuperer_fond("prive/contenu/$type",$contexte);
		// permettre aux plugin de faire des modifs ou des ajouts
		$fond = pipeline('afficher_contenu_objet',
			array(
			'args'=>array(
				'type'=>$type,
				'id_objet'=>$id_article,
				'contexte'=>$contexte),
			'data'=> ($fond)));
	
		$res .= "<div id='wysiwyg'>$fond</div>";
	}
	return $res;
}

?>
