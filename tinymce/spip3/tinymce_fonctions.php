<?php

/**
 * Doit-on charger TinyMCE ? 
 * => oui si page d'edition d'un objet valide en config
 */
function tinymce_doitetrecharge() {	
	// si on demande explicitement le porte-plume
	$arg_barre = _request( $GLOBALS['tinymce_arg_barre'] );
	if ('porteplume'===$arg_barre) {
		return false;
	}
	// si les preferences utilisateur interdisent TMCE
	if (!empty($GLOBALS['visiteur_session']) && true===isset($GLOBALS['visiteur_session']['prefs']['tinymce']) &&
		'non'===$GLOBALS['visiteur_session']['prefs']['tinymce']) {
		return false;
	}
	// si on est sur une page d'edition et que la config est ok pour TMCE
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
	// non par defaut
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
 * @param bool $obligatoire Doit-on ajouter les entrées obligatoires ?
 */
function tinymce_config( $obligatoire=true ) {	
	$meta_tinymce = true===empty($GLOBALS['meta']['tinymce']) ? array() : 
		( is_string($GLOBALS['meta']['tinymce']) ? unserialize($GLOBALS['meta']['tinymce']) : $GLOBALS['meta']['tinymce'] );
	if (0<count($meta_tinymce)){
		$tinymce_cfg = $meta_tinymce;
	} else {
		$tinymce_cfg = $GLOBALS['tinymce_config_def'];
	}
	if (true===$obligatoire){
		foreach($tinymce_cfg as $_var=>$_val){
			if (true===isset($GLOBALS['tinymce_config_obligatoire']) && true===isset($GLOBALS['tinymce_config_obligatoire'][$_var])){
				if (true===is_array($_val)){
					$tinymce_cfg[$_var] = array_unique(
						array_filter(
							array_merge( $_val, $GLOBALS['tinymce_config_obligatoire'][$_var] )
						)
					);
				} else {
					if (0==preg_match('/'.$GLOBALS['tinymce_config_obligatoire'][$_var].'/', $_val)){
						$tinymce_cfg[$_var] .= ' '.$GLOBALS['tinymce_config_obligatoire'][$_var];
					}
				}
			}
		}
	}	
//echo '<pre>';var_export($tinymce_cfg);exit('yo');
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
					// cas particulier de 'groupes_mot'
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
 * On tente de retirer tout le porte-plume (!! c'est moche !!)
 */
function tinymce_nettoyerheader( $content ) {	
		$lines = explode("\n", $content);
		$n_flux='';
		foreach($lines as $_line)
		{
/*
plugins-dist/porte_plume
			if (
				0==substr_count($_line, 'jquery.markitup_pour_spip') &&
				0==substr_count($_line, 'jquery.previsu_spip')
			) $n_flux .= $_line."\n";
*/
			if (0==substr_count($_line, 'porte_plume')) {
				$n_flux .= $_line."\n";
			}
		}
		$content = $n_flux;
		return $content;
}

?>