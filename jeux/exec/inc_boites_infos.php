<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// pour tous les recuperer_fond()
include_spip('public/assembler');

// compatibilites
function jeux_debut_page($titre="", $rubrique="accueil", $sous_rubrique="accueil") {
        $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($titre, $rubrique, $sous_rubrique);
}

function jeux_compat_boite($b) {echo $b('', true);}//un truc à virer après

// Pagination sur les jeux disponibles
function jeux_navigation_pagination() {
	$texte = ''; $href = 'jeux_voir'; $nb_aff = 1; $deb_aff = 1;
	$self = self();
	$id_jeu = intval(_request('id_jeu'));

	// liste des jeux disponibles
	$fetch = 'sql_fetch';
	$q = sql_select('id_jeu', 'spip_jeux');
	$ids = array(); $i = 0;
	while($r = $fetch($q)) { 
		$ids[$i++] = $r['id_jeu'];
		if ($id_jeu==$r['id_jeu']) $deb_aff = $i;
	}
	$num_rows = count($ids);
	if($num_rows<2) return '';

	for ($i = 0; $i < $num_rows; $i += $nb_aff){
		$deb = $i + 1;
		// Pagination : si on est trop loin, on met des '...'
		if (abs($deb-$deb_aff)>10) {
			if ($deb<$deb_aff) {
				if (!isset($premiere)) { $premiere = '1 ... '; $texte .= $premiere; }
			} else {
				$derniere = ' | ... '.$num_rows; $texte .= $derniere; break;
			}
		} else {
			$fin = $i + $nb_aff;
			if ($fin > $num_rows) $fin = $num_rows;
			if ($deb > 1) $texte .= " |\n";
			if ($deb_aff >= $deb AND $deb_aff <= $fin) $texte .= "<strong>$deb</strong>";
			else {
				$script = parametre_url($self, 'id_jeu', $ids[$i]);
				$texte .= "<a href=\"$script\">$deb</a>";
			}
		}
	}
	return "<div class='verdana3' style='text-align: center;'>$texte</div>";
}

function boite_infos_auteur($id_auteur) {
	return recuperer_fond('fonds/jeux_boites_infos',array('id_auteur'=>$id_auteur, 'exec'=>_request('exec')));
}

function boite_infos_jeu($id_jeu) {
	return recuperer_fond('fonds/jeux_boites_infos',array('id_jeu'=>$id_jeu, 'exec'=>_request('exec')));
}

function boite_infos_accueil($id_foo=0) {
	return recuperer_fond('fonds/jeux_boites_infos',
		array('boite'=>'accueil', 'exec'=>_request('exec'), 'id_foo'=>$id_foo, 'config'=>function_exists('lire_config')?'oui':'non'));
}

// boite pour le pipeline affiche_droite
function boite_info_jeux_edit(){
	return debut_cadre_relief(find_in_path('img/jeu-voir.png'),true,'',_T('jeux:inserer_jeu'))
	. "<div>"._T('jeux:inserer_jeu_explication')."</div>"
	. icone_horizontale(_T('jeux:liste_jeux'),generer_url_ecrire('jeux_tous'),find_in_path('img/jeux-tous.png'),'','',false)
	. icone_horizontale(_T('jeux:nouveau_jeu'),generer_url_ecrire('jeux_edit','nouveau=oui'),find_in_path('img/jeu-nouveau.png'),'','',false)
	. fin_cadre_relief(true);
}

// boite pour le pipeline affiche_droite
function boite_infos_spip_auteur($id_auteur) {
	return debut_boite_info(true)
	. icone_horizontale(_T('jeux:voir_ses_resultats'),generer_url_ecrire('jeux_resultats_auteur',"id_auteur=$id_auteur"),find_in_path('img/jeu-laurier.png'),'',false)
	. fin_boite_info(true);
}	

?>