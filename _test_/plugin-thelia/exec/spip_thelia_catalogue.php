<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_spip_thelia_catalogue_dist()
{
	if (function_exists('debut_page')) {
		// SPIP Version 1.9.x
		debut_page(_T("spip_thelia:catalogue_thelia"), _T("spip_thelia:catalogue_thelia"), _T("spip_thelia:catalogue_thelia"));
	} else {
		// SPIP >= 2.0
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T("spip_thelia:catalogue_thelia"),_T("spip_thelia:catalogue_thelia"),_T("spip_thelia:catalogue_thelia"));
	}

	$thelia_url = '../'._THELIA_ADMIN.'/';
	if (_request('thelia_url')) $thelia_url .= _request('thelia_url');
	
	echo "<script type='text/javascript' src='".find_in_path('javascript/jquery.dimensions.min.js')."'></script>
		<iframe src='$thelia_url' style='width:100%;height:600px;' frameborder='0' scrolling='auto' id='iFrameToAdjust' class='autoHeight'></iframe>
		<script type='text/javascript' >
			function autoHeight() {
				var theFrame = jQuery('#iFrameToAdjust', parent.document.body);
				var H = $(window).height() - jQuery('#haut-page').height() - jQuery('#page .table_page').outerHeight({ margin: true });
				if (H>300) {
					theFrame.height(H - 4);
				}
			}
			jQuery(window)
				.resize(autoHeight)
				.load(autoHeight);
		</script>";
	echo fin_page();

}
?>
