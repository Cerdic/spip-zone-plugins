<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_spip_thelia_catalogue_dist(){
	if (function_exists('debut_page')){
		// SPIP Version 1.9.x
		debut_page(_T("spip_thelia:catalogue_thelia"), _T("spip_thelia:catalogue_thelia"), _T("spip_thelia:catalogue_thelia"));
	} else {
		// SPIP >= 2.0
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T("spip_thelia:catalogue_thelia"), _T("spip_thelia:catalogue_thelia"), _T("spip_thelia:catalogue_thelia"));
	}

	if (function_exists('lire_config')){
		$thelia_url = '../' . _RACINE_THELIA . lire_config('spip_thelia/repadmin_spip_thelia', 'admin') . '/';
	} else {
		$thelia_url = '../' . _RACINE_THELIA . _THELIA_ADMIN . '/';
	}

	if (find_in_path($thelia_url)){

		if (_request('thelia_url')) $thelia_url .= _request('thelia_url');

		echo "<script type='text/javascript' src='" . find_in_path('javascript/jquery.dimensions.min.js') . "'></script>
		      <iframe src='$thelia_url' style='width:100%;height:600px;' frameborder='0' scrolling='auto' id='iFrameToAdjust' ></iframe>
		      <script type='text/javascript' >
			      function autoHeight() {
				      var theFrame = jQuery('#iFrameToAdjust', parent.document.body);
				      var H = jQuery('html').innerHeight() - jQuery('#haut-page').innerHeight() - jQuery('#page .table_page').innerHeight();
				      if (H>20) {
					      theFrame.height(H - 20);  // .table_page possede un margin-top de 13px ; les 7px sont pour IE (3px sontnecessaires pour Firefox)
				      }
			      }
			      jQuery(window)
				      .resize(autoHeight)
				      .load(autoHeight);
		      </script>";


	} else {
		echo("Le dossier d'admin de Th&eacute;lia n'a pas &eacute;t&eacute; trouv&eacute;. V&eacute;rifiez que le chemin est correct dans le <a href='?exec=cfg&cfg=spip_thelia'>formulaire CFG de param&eacute;trage du plugin</a>.");
	}

	echo fin_page();

}
