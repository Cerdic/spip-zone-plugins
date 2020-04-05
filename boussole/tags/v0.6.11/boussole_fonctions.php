<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// ----------------------- Balises propres a Boussole ---------------------------------

/**
 * La balise #BOUSSOLE_INFOS renvoie :
 * - soit le tableau des infos contenues dans la meta boussole_infos_<alias> si l'alias est fourni
 * - soit la liste de tous les tableaux d'infos des meta boussole_infos_xxxx sinon
 *
 * @param string $p
 * @return array
 */

// $p	=> alias de la boussole ou vide
function balise_BOUSSOLE_INFOS($p) {
	
	$alias = interprete_argument_balise(1,$p);
	$alias = isset($alias) ? str_replace('\'', '"', $alias) : '""';

	$p->code = 'calcul_boussole_infos('.$alias.')';

	return $p;
}

function calcul_boussole_infos($alias) {

	$infos = array();
	
	$where = array();
	$group_by = array();
	if ($alias)
		$where[] = 'aka_boussole=' . sql_quote($alias);
	else
		$group_by[] = 'aka_boussole';

	$akas_boussole = sql_allfetsel('aka_boussole', 'spip_boussoles', $where, $group_by);
	if ($akas_boussole) {
		foreach (array_map('reset', $akas_boussole) as $_aka_boussole) {
			$meta = sql_fetsel('valeur, maj', 'spip_meta', 'nom=' . sql_quote('boussole_infos_' . $_aka_boussole));
			if ($meta) {
				if ($alias)
					$infos = array_merge(unserialize($meta['valeur']), array('maj' => $meta['maj']));
				else
					$infos[] = array_merge(unserialize($meta['valeur']), array('maj' => $meta['maj']));
			}
		}
	}

	return $infos;
}


// ----------------------- Filtres propres a Boussole ---------------------------------

/**
 * Renvoie la traduction d'un champ d'une boussole, d'un groupe ou d'un site
 *
 * @param string $aka_boussole
 * @param string $champ
 * @param string $alias
 * @return string
 */

// $aka_boussole	=> alias de la boussole
// $champ			=> champ a traduire
// $alias			=> alias du groupe ou du site
function boussole_traduire($aka_boussole, $champ, $alias='') {
	if ($champ == 'non')
		return false;
	$champs_boussole = array('nom_boussole', 'slogan_boussole','descriptif_boussole', 'titre_actualite');
	$champs_groupe_site = array('nom_groupe', 'nom_site', 'slogan_groupe', 'slogan_site', 'nom_slogan_site', 'descriptif_site');

	$traduction = '';
	if ($aka_boussole) {
		if (in_array($champ, $champs_boussole))
			$traduction = _T('boussole:' . $champ . '_' . $aka_boussole);
		elseif (in_array($champ, $champs_groupe_site))
			if ($champ != 'nom_slogan_site')
				$traduction = _T('boussole:' . $champ . '_' . $aka_boussole . '_' . $alias);
			else
				$traduction = _T('boussole:nom_site_' . $aka_boussole . '_' . $alias) . ' - ' .
							  _T('boussole:slogan_site_' . $aka_boussole . '_' . $alias);
	}

	return $traduction;
}

/**
 * Renvoie la la chaine habituelle informant sur la taille d'un logo (lxh pixels)
 *
 * @param string $logo
 * @return string
 */

// $logo	=> fichier logo
function boussole_informer_taille($logo) {
	$taille = '';
	if ($taille = @getimagesize($logo))
		$taille = _T('info_largeur_vignette', array('largeur_vignette' => $taille[0], 'hauteur_vignette' => $taille[1]));
	else
		$taille = _T('boussole:info_aucun_logo_boussole');
	
	return $taille;
}


// -------------------- Filtres de compatibilite avec SPIP 2.0 ------------------------

if (substr($GLOBALS['spip_version_branche'],0, 3) == '2.0') {
	function bouton_action($libelle, $url, $class="", $confirm="", $title=""){
		$onclick = $confirm?" onclick='return confirm(\"" . attribut_html($confirm) . "\");'":"";
		$title = $title ? " title='$title'" : "";
	
		return "<form class='bouton_action_post $class' method='post' action='$url'><div>".form_hidden($url)
			."<button type='submit' class='submit'$title$onclick>$libelle</button></div></form>";
	}

	function singulier_ou_pluriel($nb,$chaine_un,$chaine_plusieurs,$var='nb'){
		if (!$nb=intval($nb)) return "";
		if ($nb>1) return _T($chaine_plusieurs, array($var => $nb));
		else return _T($chaine_un);
	}
}

?>
