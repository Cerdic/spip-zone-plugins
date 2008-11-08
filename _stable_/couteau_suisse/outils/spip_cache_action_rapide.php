<?

// module inclu dans la description de l'outil en page de configuration
// ici, un bouton : "vider le cache"

function spip_cache_action_rapide() {
	include_spip('inc/invalideur');
	if ($n = taille_du_cache())
	  $info = _T('taille_cache_octets', array('octets' => taille_en_octets($n)));
	else
	  $info = _T('taille_cache_vide');
	return redirige_action_post('purger', 'cache', 'admin_couteau_suisse', "cmd=descrip&outil=spip_cache",
			"\n<div style='text-align: center;'><input class='fondo' type='submit' value=\"" .
			attribut_html(_T('bouton_vider_cache')) .
			"\" />&nbsp;($info)</div>");
}

?>