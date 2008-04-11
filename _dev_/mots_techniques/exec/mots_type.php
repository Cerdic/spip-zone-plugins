<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

// http://doc.spip.org/@exec_mots_type_dist
function exec_mots_type_dist()
{
	$id_groupe= intval(_request('id_groupe'));
	$present = false;
	
	// un groupe est present, on recupere ses donnees
	if ($id_groupe && ($row = sql_fetsel("*", "spip_groupes_mots", "id_groupe=$id_groupe"))) {
		$present = true;
		$row['type'] = $row['titre']; 
		$row['titre'] = typo($row['titre']);		
	// sinon valeurs par defaut	
	} else {
		$row = array(
			'type' => filtrer_entites(_T('titre_nouveau_groupe')),
			'titre' => filtrer_entites(_T('titre_nouveau_groupe')),
			'nouveau' => true, //" onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"",
			'ancien_type' => '', // ?
			'unseul' => 'non',
			'obligatoire' => 'non',
			'articles' => 'oui',
			'breves' => 'oui',
			'rubriques' => 'non',
			'syndic' => 'oui',
			'minirezo' => 'oui',
			'comite' => 'oui',
			'forum' => 'non',
			'technique' => ''
		);	
	}

	if (($id_groupe AND !$present) OR
	    !autoriser($id_groupe?'modifier' : 'creer', 'groupemots', $id_groupe)) {
		include_spip('inc/minipres');
		echo minipres();
	} else { 

	pipeline('exec_init',array('args'=>array('exec'=>'mots_type','id_groupe'=>$id_groupe),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("&laquo; $titre &raquo;", "naviguer", "mots");
	
	echo debut_gauche('', true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'mots_type','id_groupe'=>$id_groupe),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'mots_type','id_groupe'=>$id_groupe),'data'=>''));
	echo debut_droite('', true);

	echo debut_cadre_formulaire("", true);
	echo groupemots_edit_presentation($row['titre']);	
	$editer_groupemots = charger_fonction('editer_groupemots', 'inc');
	echo $editer_groupemots($id_groupe, $row);
	echo fin_cadre_formulaire(true);

	echo pipeline('affiche_milieu', array('args' => array('exec' => 'mots_type', 'id_groupe' => $id_groupe), 'data'=>''));
	echo fin_gauche(), fin_page();
		
	}
}

function groupemots_edit_presentation($titre=''){
	global $spip_lang_right;
	
	return
		icone_inline(_T('icone_retour'), generer_url_ecrire("mots_tous"), "mot-cle-24.gif", "rien.gif", $spip_lang_right) .
	 	_T('titre_groupe_mots') .
		gros_titre($titre,'',false) . 
		"<hr />\n";	
}

?>
