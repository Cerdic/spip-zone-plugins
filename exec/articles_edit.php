<?php

include_spip('inc/presentation');
include_spip('inc/article_select');
include_spip('inc/documents');
// http://doc.spip.org/@exec_articles_edit_dist
function exec_articles_edit()
{
  articles_edit(_request('id_article'), // intval plus tard
        intval(_request('id_rubrique')),
        intval(_request('lier_trad')),
        intval(_request('id_version')),
        ((_request('new') == 'oui') ? 'new' : ''),
        'articles_edit_config');
}
// http://doc.spip.org/@articles_edit
function articles_edit($id_article, $id_rubrique, $lier_trad, $id_version, $new, $config_fonc)
{
$id_individu = $_GET['id_individu'].$_POST['id_individu'];
$url_action_document=generer_url_ecrire('fiche_document');

    $row = article_select($id_article ? $id_article : $new, $id_rubrique,  $lier_trad, $id_version);
    $id_article = $row['id_article'];
    $id_rubrique = $row['id_rubrique'];
    
    $commencer_page = charger_fonction('commencer_page', 'inc');
    if (!$row
      OR ($new AND !autoriser('creerarticledans','rubrique',$id_rubrique)) 
      OR (!$new AND (!autoriser('voir', 'article', $id_article) OR !autoriser('modifier','article', $id_article))) 
      ) {
        echo $commencer_page(_T('info_modifier_titre', array('titre' => $titre)), "naviguer", "rubriques", $id_rubrique);
        echo "<strong>"._T('avis_acces_interdit')."</strong>";
        echo fin_page();
        exit;
    }

    pipeline('exec_init',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article,'id_individu'=>$id_individu),'data'=>''));
    
    if ($id_version) $titre.= ' ('._T('version')." $id_version)";
    else $titre = $row['titre'];
    $url_site="essai";
    echo $commencer_page(_T('titre_page_articles_edit', array('titre' => $titre, 'url_site' => $url_site)), "naviguer", "articles", $id_rubrique);

    debut_grand_cadre();
    echo "<table width='100%'><tr><td width='50%'>";
    echo afficher_hierarchie($id_rubrique);
    echo "</td><td>";
if ($id_individu<>NULL){
        echo debut_boite_info();
        echo gros_titre(_T("Fiche n&ordm; ".$_GET['id_individu'].$_POST['id_individu']));
        genespip_tester_document($id_individu,$id_article,"articles_edit");
        echo fin_boite_info();
}
    echo "</td></tr></table>";
    fin_grand_cadre();

    debut_gauche();

    // Pave "documents associes a l'article"
    
    if (!$new){
        # affichage sur le cote des pieces jointes, en reperant les inserees
        # note : traiter_modeles($texte, true) repere les doublons
        # aussi efficacement que propre(), mais beaucoup plus rapidement
        traiter_modeles(join('',$row), true);
        echo afficher_documents_colonne($id_article, 'article');
    } else {
        # ICI GROS HACK
        # -------------
        # on est en new ; si on veut ajouter un document, on ne pourra
        # pas l'accrocher a l'article (puisqu'il n'a pas d'id_article)...
        # on indique donc un id_article farfelu (0-id_auteur) qu'on ramassera
        # le moment venu, c'est-ˆ-dire lors de la creation de l'article
        # dans editer_article.
        echo afficher_documents_colonne(
            0-$GLOBALS['auteur_session']['id_auteur'], 'article');
    }

    echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article,'id_individu=$id_individu'),'data'=>''));
    creer_colonne_droite();
    echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article,'id_individu=$id_individu'),'data'=>''));
    debut_droite();
    
    debut_cadre_formulaire();
    echo articles_edit_presentation($new, $row['id_rubrique'], $lier_trad, $row['id_article'], $row['titre']);
    $editer_article = charger_fonction('editer_article', 'inc');
    echo $editer_article($new, $id_rubrique, $lier_trad, generer_url_ecrire("articles","id_individu=".$id_individu), $config_fonc, $row);
    fin_cadre_formulaire();
    echo pipeline('affiche_milieu',array('args'=>array('exec'=>'articles_edit','id_article'=>$id_article,'id_individu'=>$id_individu),'data'=>''));

    echo fin_gauche(), fin_page();
}

// http://doc.spip.org/@articles_edit_presentation
function articles_edit_presentation($new, $id_rubrique, $lier_trad, $id_article, $titre)
{
$id_individu = $_GET['id_individu'].$_POST['id_individu'];
    $oups = ($lier_trad ?
         generer_url_ecrire("articles","id_article=".$lier_trad."&id_individu=".$id_individu)
         : ($new
        ? generer_url_ecrire("naviguer","id_rubrique=".$id_rubrique."&id_individu=".$id_individu)
        : generer_url_ecrire("articles","id_article=".$id_article."&id_individu=".$id_individu)
        ));

    return
        "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>" .
        "<tr>" .
        "\n<td>" .
        icone(_T('icone_retour'), $oups, "article-24.gif", "rien.gif", '',false) .
        "</td>\n<td>" .
        "<img src='" .
        _DIR_IMG_PACK . "rien.gif' width='10' alt='' />" .
        "</td>\n" .
        "<td style='width: 100%'>" .
        _T('texte_modifier_article') .
        gros_titre($titre,'',false) . 
        "</td></tr></table><hr />\n";
}
?>



