<?php
/**
 * Utilisations de pipelines par Pensebete
 *
 * @plugin     Pensebetes
 * @copyright  2019
 * @author     Vincent CALLIES
 * @licence    GNU/GPL
 * @package    SPIP\Pensebetes\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ajout de la feuille de style de Pensebetes,
 * dans l'espace public si demandé
 * Appelé aussi depuis le privé avec $prive à true.
 *
 * @pipeline insert_head_css
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */

function pensebetes_insert_head_css($flux, $prive = false) {
	// toujours autoriser pour le prive.
	if ($prive or lire_config('pensebetes/espacepublic')) {
        $css = direction_css(produire_fond_statique('css/style_plugin_pensebetes.css'), lang_dir());
        $flux .= '<link rel="stylesheet" href="'.$css.'" type="text/css" />';
	}
	return $flux;
}

/**
 * Ajout de la CSS du Pensebetes au head privé
 *
 * @pipeline insert_header_prive_css
 * @param string $flux Contenu du head
 * @return string Contenu du head complété
 */
function pensebetes_insert_head_prive_css($flux) {
	return pensebetes_insert_head_css($flux, true);
}


/**
 * Ajout de contenu à gauche de la page,
 *
 * @pipeline affiche_gauche
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function pensebetes_affiche_gauche($flux){
    if (in_array($flux['args']['exec'],array('auteur','accueil','murs','sur','mur'))) {
		$mes_boites=lire_config('pensebetes/mes_boites');
		if (in_array($flux['args']['exec'],$mes_boites) OR in_array($flux['args']['exec'],array('murs','sur','mur'))) {
			include_spip('inc/presentation');
			$balise_img = chercher_filtre('balise_img');
			$img = $balise_img(chemin_image('pensebete-16.png'), "", '');
			if (in_array($flux['args']['exec'],array('accueil','murs','sur','mur')))
			 	$flux['args']['id_auteur']=$GLOBALS['visiteur_session']['id_auteur'];
    		if ($flux['args']['id_auteur']==$GLOBALS['visiteur_session']['id_auteur']) {
				$titre ="<a href='".generer_url_ecrire('murs')."'>"._T('pensebete:titre_activite_mur')."</a></h3>";
        		$soustitre2 = "<a href='".generer_url_ecrire('murs','quoi=mien')."'>".$img." </a>";
        		$soustitre1 = "<a href='".generer_url_ecrire('murs','quoi=autres')."'>".$img." </a>";
   			}
    		else {
	    		$titre = _T('pensebete:titre_activite_mur'); 
    			$soustitre1=$soustitre2= "$img ";
    			$out="auteur";
    		}
	    	$flux['data'] .= debut_cadre_relief('mur-24.png',true,'',$titre); 
    		$flux['data'] .= $soustitre1;
    		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/pensebetes_donnes',array('id_auteur'=>$flux['args']['id_auteur'],'out'=>$out));
   			$flux['data'] .= $soustitre2; 
    		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/pensebetes_recus',array('id_auteur'=>$flux['args']['id_auteur'],'out'=>$out));
    		$flux['data'] .= fin_cadre_relief(true);
    	}
    }
    return $flux;
}


/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function pensebetes_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// Poser des pense-bêtes sur les objets éditoriaux éditables sélectionnés dans la configuration
	if (in_array($e['type'], lire_config('pensebetes/mes_objets')) AND !$e['edition']) {
			$texte = recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'pensebetes',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']],
			'id_donneur' => $GLOBALS['visiteur_session']['id_auteur'],
			'editable'=>autoriser('associerpensebetes',$e['type'],$e['id_objet'])?'oui':'non'
			));
	}

	// Poser des pense-bêtes dans les lieux sélectionnés dans la configuration (le mur d'accueil de l'auteur pour l'instant)
	if (in_array($flux['args']['exec'], lire_config('pensebetes/mes_lieux'))) {
		$ajout= recuperer_fond('prive/squelettes/inclure/pensebete_'.$flux['args']['exec'],
				array(
					'id_receveur' => $GLOBALS['visiteur_session']['id_auteur']
					)
				);
		$flux['data']=$ajout.$flux['data'];
	}
	
	// injection des données
	if ($texte) {
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}

/**
 * Insérer des évènements dans le calendrier privé
 *
 * On y insère les Pense-bêtes de l'auteur. Les données sont formatées en json
 * et utilisées par la librairie javascript Fullcalendar
 * au travers du Plugin Organiseur.
 *
 * @pipeline quete_calendrier_prive
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/

function pensebetes_quete_calendrier_prive($flux) {
	
	$id_auteur =$GLOBALS['visiteur_session']['id_auteur'] ;

	if ($mes_pensebetes = sql_allfetsel('id_pensebete, titre, texte, date', 'spip_pensebetes', 'id_receveur=' . intval($id_auteur). ' AND date >= "'.$flux['args']['start'].'" AND date <= "'.$flux['args']['end'].'"')) {
	foreach ($mes_pensebetes as $un) {
		$texte=generer_url_ecrire('pensebete','id_pensebete='.$un['id_pensebete']);// fonction dans urls.php
		$texte=str_replace ('&amp;','&',$texte); // on injecte du json pas du HTML
		$flux['data'][]=array ('id' => 0,
			'title' => $un['titre'],
			'allDay' => 1,
			'start' => substr ($un['date'],0,10),
			'end' => substr ($un['date'],0,10),
			'url' => liens_absolus($texte, ''), //fonction de filtres_mini.php
			'className' => 'calendrier-event calendrier-couleur01',
			'description' =>$un['texte']);
	}
}

	return $flux;
}
