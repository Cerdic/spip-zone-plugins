<?php
/**
 * Gestion du formulaire de d'édition de selections_contenu
 *
 * @plugin     Sélections éditoriales
 * @copyright  2014
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Selections_editoriales\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_selections_contenu
 *     Identifiant du selections_contenu. 'new' pour un nouveau selections_contenu.
 * @param int $id_selection
 *     Identifiant de la sélection dont fait partie ce contenu
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du selections_contenu, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_selections_contenu_identifier_dist($id_selections_contenu='new', $id_selection=0, $retour='', $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_selections_contenu)));
}

/**
 * Chargement du formulaire d'édition de selections_contenu
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_selections_contenu
 *     Identifiant du selections_contenu. 'new' pour un nouveau selections_contenu.
 * @param int $id_selection
 *     Identifiant de la sélection dont fait partie ce contenu
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du selections_contenu, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_selections_contenu_charger_dist($id_selections_contenu='new', $id_selection=0, $retour='', $config_fonc='', $row=array(), $hidden=''){
	// Si c'est une création et qu'il n'y a pas de sélection parente explicite, on arrête
	if (intval($id_selections_contenu) <= 0 and intval($id_selection) <= 0){
		return false;		
	}
	
	$valeurs = formulaires_editer_objet_charger('selections_contenu',$id_selections_contenu,'',0,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de selections_contenu
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_selections_contenu
 *     Identifiant du selections_contenu. 'new' pour un nouveau selections_contenu.
 * @param int $id_selection
 *     Identifiant de la sélection dont fait partie ce contenu
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du selections_contenu, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_selections_contenu_verifier_dist($id_selections_contenu='new', $id_selection=0, $retour='', $config_fonc='', $row=array(), $hidden=''){

	return formulaires_editer_objet_verifier('selections_contenu',$id_selections_contenu, array('titre'));

}

/**
 * Traitement du formulaire d'édition de selections_contenu
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_selections_contenu
 *     Identifiant du selections_contenu. 'new' pour un nouveau selections_contenu.
 * @param int $id_selection
 *     Identifiant de la sélection dont fait partie ce contenu
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du selections_contenu, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_selections_contenu_traiter_dist($id_selections_contenu='new', $id_selection=0, $retour='', $config_fonc='', $row=array(), $hidden=''){
	// Si création, on met en mémoire la sélection parente
	if (intval($id_selections_contenu) <= 0 and intval($id_selection) > 0){
		set_request('id_selection', intval($id_selection));
	}
	
	// On appelle le traitement générique
	$retours = formulaires_editer_objet_traiter('selections_contenu',$id_selections_contenu,'',0,$retour,$config_fonc,$row,$hidden);
	
	// On va chercher la vrai sélection si on a bien un contenu
	if ($id_contenu = intval($retours['id_selections_contenu'])) {
		$id_selection = intval(sql_getfetsel('id_selection', 'spip_selections_contenus', 'id_selections_contenu = '.$id_contenu));
	}
	
	// Si pas de $retour, on vide le redirect et on recharge le bloc parent
	if ($retours['id_selections_contenu'] and !$retour) {
		$retours['redirect'] = null;
		
		// Animation de ce qu'on vient de modifier
		$callback = "jQuery('#selection$id_selection-contenu$id_contenu').animateAppend();";
		// Rechargement du conteneur de la sélection
		$js = "if (window.jQuery) jQuery(function(){ajaxReload('selection$id_selection', {args:{editer_contenu:'non'}, callback:function(){ $callback }});});";
		$js = "<script type='text/javascript'>$js</script>";
		if (isset($retours['message_erreur']))
			$retours['message_erreur'].= $js;
		else
			$retours['message_ok'] .= $js;
	}
	
	// Contournement de bug pour garder le bon URL
	$retours['id_selection'] = $id_selection;
	
	return $retours;
}


?>
