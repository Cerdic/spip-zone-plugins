<?php

/**
 * Doit-on charger TinyMCE ? 
 * => oui si page d'edition d'un objet valide en config
 */
function tinymce_doitetrecharge() {	
	$tiny_config = tinymce_config();
	$exec = _request('exec');
	if (1===preg_match('~^([a-z_]+)_(edit)$~', $exec, $match)) {
		if (true===in_array($match[1], $tiny_config['objets'])) {
			return true;
		}
		$singulier = substr($match[1], 0, strlen($match[1])-1);
		if (true===in_array($singulier, $tiny_config['objets'])) {
			return true;
		}
	}
	return false;
}

/**
 * Doit-on recharger la page ? (oui si ajax)
 */
function tinymce_doitrechargerpage() {	
	$var_ajax = _request('var_ajax');
	if (true===tinymce_doitetrecharge() && 1==$var_ajax) {
		return true;
	}
	return false;
}

/**
 * Renvoie l'objet edite depuis l'URL courant
 */
function tinymce_trouverobjet() {
	$tiny_config = tinymce_config();
	$exec = _request('exec');
	if (1===preg_match('~^([a-z_]+)_(edit)$~', $exec, $match)) {
		if (true===in_array($match[1], $tiny_config['objets'])) {
			return $match[1];
		}
		$singulier = substr($match[1], 0, strlen($match[1])-1);
		if (true===in_array($singulier, $tiny_config['objets'])) {
			return $singulier;
		}
	}
	return '';
}

/**
 * Recupere la config du plugin, definit en BO si present, sinon par defaut
 */
function tinymce_config() {	
	$def_cfg = $GLOBALS['tinymce_config_def'];
	$usr_cfg = !empty($GLOBALS['meta']['tinymce']) ? unserialize($GLOBALS['meta']['tinymce']) : array();
	if (
		true===isset($GLOBALS['meta']['tinymce']) && 
		0<count($GLOBALS['meta']['tinymce'])
	){
		$tinymce_cfg = $usr_cfg;
	} else {
		$tinymce_cfg = $def_cfg;
	}
	return $tinymce_cfg;
}

/**
 * Recupere la liste des objets editoriaux du SPIP courant
 */
function tinymce_listerobjetsspip() {	
	$full_list = lister_tables_objets_sql();
	$objets_spip = array();
	foreach($full_list as $name=>$table){
		if (true===isset($table['editable']) && 'oui'===$table['editable']){
			foreach($table['field'] as $fieldname=>$fieldsql){
				if (1===preg_match('/longtext/i', $fieldsql)){
					$_name = str_replace('spip_', '', substr($name, 0, strlen($name)-1));
					if ('groupes_mot'===$_name){
						$_name = 'groupe_mot';
					}
					$objets_spip[ $_name ] = _T( $table['texte_objets'] );
				}
			}
		}
	}
	return $objets_spip;
}

/**
 * Recupere la liste des config de barres TinyMCE disponibles
 * => liste des squelettes 'fonds/tinymce_XXX.html'
 */
function tinymce_listerfondsconfig() {
	$liste_squelettes = find_all_in_path('', 'fonds/tinymce/(.*)', true);
	$liste_fonds = array();
	foreach($liste_squelettes as $name=>$path){
		$liste_fonds[] = str_replace('.html', '', $name);
	}
	return $liste_fonds;	
}

/**
 * Code javascript pour rechargement de la page
 */
function tinymce_jsrechargerpage() {	
		return '
<script type="text/javascript">
document.location.href = "'.str_replace('&amp;', '&', self()).'";
</script>
		';
}

/**
 * Code de chargement en en-tete de TinyMCE
 */
function tinymce_chargerenheader() {	
		$spip_objet = tinymce_trouverobjet();
		$tinymce_jq_js = find_in_path('jscripts/tiny_mce/jquery.tinymce.js');
		$tinymce_jsconfig = generer_url_public('tinymce_jsconfig', array('objet'=>$spip_objet));
		return '
<!-- Load TinyMCE -->
<script type="text/javascript" src="'.$tinymce_jq_js.'"></script>
<script type="text/javascript" src="'.$tinymce_jsconfig.'"></script>
<script type="text/javascript">
function barre_outils_forum(){}
function barre_outils_edition() {}
$(document).ready(function(){ TinyMCE_Spip_init(); });
</script>
<!-- /TinyMCE -->
	';
}

/**
 * On tente de retirer la librairie par defaut de SPIP (!! c'est moche !!)
 */
function tinymce_nettoyerheader( $content ) {	
		$lines = explode("\n", $content);
		$n_flux='';
		foreach($lines as $_line)
		{
			if (
				0==substr_count($_line, 'jquery.markitup_pour_spip') &&
				0==substr_count($_line, 'jquery.previsu_spip')
			) $n_flux .= $_line."\n";
		}
		$content = $n_flux;
		return $content;
}
?>