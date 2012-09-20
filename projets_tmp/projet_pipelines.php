<?php
/**
 * Plugin Projet pour SPIP
 * Eric Lupinacci, Quentin Drouet
 *
 * Fichier recensant l'ensemble des pipelines utilisés par le plugin Projet
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajouter le bouton de menu dans le prive pour accéder à la page des projets
 *
 * @param unknown $boutons_admin
 * @return unknown
 */
function projet_ajouter_boutons($boutons_admin) {
	// si on est habilité à voir les projets
	if (autoriser('voir','projet')) {
	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu['projets_page']= new Bouton(
		_DIR_PLUGIN_PROJET."/prive/images/projet-24.gif",  // icone
		_T('projet:voir_projets')	// titre
		);
	}
	return $boutons_admin;
}

/**
 * Insertion d'un formulaire d'association d'un projet a un objet en colonne droite
 *
 * @param Array $flux
 */
function projet_affiche_droite($flux){
	return $flux;
}

/**
 * Insertion d'un formulaire d'association d'un projet dans l'affichage de l'objet
 *
 * @param Array $flux
 */
function projet_affiche_milieu($flux){
	include_spip('inc/autoriser');
	$exec = $flux['args']['exec'];
	switch($exec){
		case 'articles' :
			$id_type= $flux['args']['id_article'];
			if(autoriser('modifier','article',$id_type)){
				$type = 'article';
			}
		break;
		case 'naviguer' :
			$id_type=$flux['args']['id_rubrique'];
			if(autoriser('modifier','rubrique',$id_type)){
				$type = 'rubrique';
			}
		break;
		case 'auteur_infos' :
			$id_type=$flux['args']['id_auteur'];
			if(autoriser('modifier','auteur',$id_auteur)){
				$type = 'auteur';
			}
		break;
	}
	if(in_array($type, lire_config('projet/ligatures/objets',array()))){
		$flux['data'] .= "<div id='pave_associer_projet'>";
		$flux['data'] .= debut_cadre_enfonce("", true, "", _T('projet:titre_formulaire_associer'));
		$flux['data'] .= recuperer_fond('prive/contenu/inc-affiche_milieu', array('objet'=>$type,'id_objet'=>$id_type));
		$flux['data'] .= fin_cadre_enfonce(true);
		$flux['data'] .= "</div>";
	}
	return $flux;
}

/**
 * Insertion dans le pipeline accueil informations de l'etat des projets
 * @param string $flux
 * @return string $flux
 */
function projet_accueil_informations($flux){
	global $spip_lang_left;

	$q = sql_select("COUNT(*) AS cnt, statut", 'spip_projets', '', 'statut', '','', "COUNT(*)<>0");

	$cpt = array();
	$cpt2 = array();
	$defaut = $where ? '0/' : '';
	while($row = sql_fetch($q)) {
	  $cpt[$row['statut']] = $row['cnt'];
	  $cpt2[$row['statut']] = $defaut;
	}

	if ($cpt) {
		if ($where) {
			$q = sql_select("COUNT(*) AS cnt, statut", 'spip_projets', $where, "statut");
			while($row = sql_fetch($q)) {
				$r = $row['statut'];
				$cpt2[$r] = intval($row['cnt']) . '/';
			}
		}
		$res .= afficher_plus(generer_url_ecrire("projets_page",""))."<b>".ucfirst(_T('projet:projets'))."</b>";
		$res .= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
		if (isset($cpt['prepa'])) $res .= "<li>"._T('texte_statut_en_cours_redaction').": ".$cpt2['prepa'].$cpt['prepa'] . '</li>';
		if (isset($cpt['prop'])) $res .= "<li>"._T('texte_statut_attente_validation').": ".$cpt2['prop'] .$cpt['prop'] .'</li>';
		if (isset($cpt['publie'])) $res .= "<li><b>"._T('texte_statut_publies').": ".$cpt2['publie'] .$cpt['publie'] . "</b>" .'</li>';
		$res .= "</ul>";
	}

	$flux .= "<div class='verdana1'>" . $res . "</div>";
	return $flux;
}

/**
 * Insertion dans le pipeline accueil gadgets le bouton de creation d'un projet
 * @param string $gadget
 * @return string $gadget
 */
function projet_accueil_gadgets($gadget){

	include_spip('inc/tickets_autorisations');
	if (autoriser('creer', 'projet')) {
		$icone = icone_horizontale(_T('projet:bouton_creer_projet'), parametre_url(generer_url_ecrire('projets_edit','new=oui'),'redirect',self()), chemin('projet-24.gif','prive/images/'), 'creer.gif', false);

		$colonnes = extraire_balises($gadget, 'td');
		$derniere_colonne = fmod(floor(count($colonnes)/2), 4) == 0 ? true : false;
		if ($derniere_colonne) {
			$gadget .= "<table><tr><td>$icone</td></tr></table>";
		}
		else {
			$gadget = preg_replace(",</tr></table>$,is", "<td>$icone</td></tr></table>", $gadget);
		}
	}
	return $gadget;
}

/**
 * Insertion dans le pipeline infos_tables du plugin Gouverneur
 * Donne des informations sur l'objet projet utilisable ensuite par d'autres plugins
 *
 * @param Array $array L'array de description des objets
 * @return
 */
function projet_gouverneur_infos_tables($array){
	$array['spip_projets'] = array(
								'table_objet' => 'projets',
								'type' => 'projet',
								'url_voir' => 'projets',
								'texte_retour' => 'projet:icone_retour_projet',
								'url_edit' => 'projets_edit',
								'texte_modifier' => 'projet:icone_modifier_projet',
								'icone_objet' => 'projet-24.png',
								'texte_unique' => 'projet:projet',
								'texte_multiple' => 'projet:projets',
								// Pour le plugin revisions en 2.1
								'champs_versionnes' => array('id_parent', 'titre', 'descriptif', 'texte', 'date', 'date_modif', 'statut')
							);
	return $array;
}

function projet_editer_contenu_objet($flux){
	// recuperer les champs crees par les plugins
	if (($flux['args']['type'] == 'rubrique') && in_array('rubrique',lire_config('projet/ligatures/objets',array()))) {

		/*
		 * Récupération de la saisie de projets
		 */
		if(intval($flux['args']['id'])){
			$contexte['valeur'] = sql_getfetsel('id_projet','spip_projets_liens','id_objet ='.$flux['args']['id'].' AND objet="rubrique"');
			spip_log($contexte['valeur']);
		}
		$contexte['nom'] = 'id_projet';
		$contexte['type_saisie'] = 'liste_projets';
		$contexte['option_intro'] = _T('projets:aucun');
		$contexte['label'] = _T('projet:label_selecteur_projet');

		$inserer_projet = recuperer_fond('saisies/_base', $contexte);

		$flux['data'] = preg_replace('%(<li class="editer_parent(.*?)</li>)%is', '$1'."\n".$inserer_projet."\n", $flux['data']);
	}

	return $flux;
}

function projet_post_edition($flux){
	if ($flux['args']['table'] == "spip_rubriques") {

		if(_request('id_projet')){
			$associer_projet = charger_fonction('associer_projet','action');
			$associer_projet(_request('id_projet'),'rubrique',$flux['args']['id_objet'],$type);
		}
	}

	return $flux;
}

?>