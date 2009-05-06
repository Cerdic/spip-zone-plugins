<?php


	/**
	 * SPIP-Plans
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined("_ECRIRE_INC_VERSION")) return;
 	include_spip('inc/presentation');
	include_spip('plans_fonctions');


	function exec_plans() {
		
		global $couleur_foncee, $couleur_claire;

		if (!autoriser('voir', 'plans')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$plan = new plan($_GET['id_plan']);
		
		pipeline('exec_init',array('args'=>array('exec'=>'plans','id_plan'=>$plan->id_plan),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($plan->titre, "naviguer", "plans_tous");

		echo debut_gauche('', true);
		echo '<div class="cadre cadre-info verdana1">';
		echo '<div class="cadre_padding">';
		echo '<div class="infos">';
		echo '<div class="numero">';
		echo _T('plans:plan_numero').' :';
		echo '<p>'.$plan->id_plan.'</p>';
		echo '</div>';

		echo '<ul class="instituer instituer_article">';
		echo '<li>';
		echo '<strong>'._T('plans:ce_plan').'</strong>';
		echo '<ul>';
		if ($plan->statut == 'hors_ligne') {
			echo '<li class="prepa selected">'.http_img_pack('puce-blanche.gif', 'puce-blanche', '')._T('plans:hors_ligne').'</li>';
			echo '<li class="publie"><a href="'.generer_url_action('statut_plan', 'id_plan='.$plan->id_plan.'&statut=en_ligne', false, true).'">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('plans:a_mettre_en_ligne').'</a></li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_plan', 'id_plan='.$plan->id_plan.'&statut=poubelle', false, true).'">'.http_img_pack('puce-poubelle.gif', 'puce-poubelle', '')._T('plans:a_supprimer').'</a></li>';
		}
		if ($plan->statut == 'en_ligne') {
			echo '<li class="prepa"><a href="'.generer_url_action('statut_plan', 'id_plan='.$plan->id_plan.'&statut=hors_ligne', false, true).'">'.http_img_pack('puce-blanche.gif', 'puce-blanche', '')._T('plans:a_mettre_hors_ligne').'</a></li>';
			echo '<li class="publie selected">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('plans:en_ligne').'</li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_plan', 'id_plan='.$plan->id_plan.'&statut=poubelle', false, true).'">'.http_img_pack('puce-poubelle.gif', 'puce-poubelle', '')._T('plans:a_supprimer').'</a></li>';
		}
		echo '</ul>';
		echo '</li>';
		echo '</ul>';

		echo '</div>';
		echo '</div>';
		echo '</div>';

		$iconifier = charger_fonction('iconifier', 'inc');
		echo $iconifier('id_plan', $plan->id_plan, 'plans');

		echo bloc_des_raccourcis(
				icone_horizontale(_T('plans:creer_nouveau_plan'), generer_url_ecrire('plans_edit', 'id_plan=-1'), _DIR_PLUGIN_PLAN."/prive/images/plan-24.png", 'creer.gif', false).
				icone_horizontale(_T('plans:aller_liste_des_plans'), generer_url_ecrire('plans_tous'), _DIR_PLUGIN_PLAN."/prive/images/plan-24.png", 'rien.gif', false)
			);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'plans','id_plan'=>$plan->id_plan),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'plans','id_plan'=>$plan->id_plan),'data'=>''));

   		echo debut_droite('', true);

		echo '<div class="fiche_objet">';

		echo '<div class="bandeau_actions">';
		echo '<div style="float: right;">';
		echo icone_inline(_T('plans:modifier_plan'), generer_url_ecrire("plans_edit", "id_plan=".$plan->id_plan), _DIR_PLUGIN_PLAN.'/prive/images/plan-24.png', "edit.gif", $GLOBALS['spip_lang_left']);
		echo '</div>';
		echo '</div>';

		echo '<h1>'.$plan->titre.'</h1>';
		
		echo '<br class="nettoyeur" />';
	
		$editer_mots = charger_fonction('editer_mots', 'inc');
		$onglet_proprietes = $editer_mots('plan', $plan->id_plan, $cherche_mot, $select_groupe, true, '', 'plans');

		$contexte = array('id' => $plan->id_plan);
		$fond = recuperer_fond("prive/contenu/plan", $contexte);
		$fond = pipeline('afficher_contenu_objet', array('args' => array('type' => 'plan', 'id_objet' => $plan->id_plan, 'contexte' => $contexte), 'data' => $fond));

		$onglet_contenu.= '<style type="text/css" media="screen">';
		$onglet_contenu.= recuperer_fond("css_plan_prive", array('couleur_foncee' => str_replace('#', '', $couleur_foncee), 'couleur_claire' => str_replace('#', '', $couleur_claire)));
		$onglet_contenu.= '</style>';
		$onglet_contenu.= '<div id="wysiwyg">'.$fond.'</div>';

	  	echo afficher_onglets_pages(
			  	array(
				  	'props' => _T('onglet_proprietes'),
				  	'voir' => _T('onglet_contenu')
				),
			  	array(
				    'props' => $onglet_proprietes,
				    'voir' => $onglet_contenu
				)
			);

		echo afficher_objets('point', _T('plans:points'), array('FROM' => 'spip_points', 'WHERE' => 'id_plan='.intval($plan->id_plan), 'ORDER BY' => 'z_index ASC'));

		echo icone_inline(_T('plans:ajouter_nouveau_point'), generer_url_ecrire("points_edit", 'id_plan='.$plan->id_plan.'&id_point=-1'), _DIR_PLUGIN_PLAN.'/prive/images/point-24.png', "creer.gif", $GLOBALS['spip_lang_right']);
		echo '<br class="nettoyeur" />';

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'plans','id_plan'=>$plan->id_plan),'data'=>''));

		echo '</div><!-- fin fiche_objet -->';

		echo fin_gauche();

		echo fin_page();

	}


?>