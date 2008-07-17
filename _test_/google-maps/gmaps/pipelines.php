<?php
	function gmaps_ajouter_boutons($boutons_admin) {
		 // on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu["gmaps"]= new Bouton(
			"../"._DIR_PLUGIN_GMAPS."images/maps.png",  // icone
			_T("gmaps:gmaps") //titre
		);
		return $boutons_admin;
	}

	function gmaps_header_priver($flux){
		if (_request('exec')=='gmaps'){
			//Lecture de la clef
			$filename=_DIR_TMP.'googlekey.txt';
			if($handle=@fopen($filename,'r'))
			{	$key = fread($handle, filesize($filename));
				fclose($handle);
			}else{
				$key = _T("gmaps:please_configure_key");
			}
			
			//Ajout des scripts
			$flux .=	//'<script src="'._DIR_PLUGIN_GMAPS.'javascript/nicEdit.js" type="text/javascript"></script>'."\n".
						'<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>'."\n".
						'<script type="text/javascript" src="http://www.google.com/jsapi?key='.$key.'"></script>'."\n".
						'<script language="JavaScript" type="text/javascript">'."\n".
						'	var dir_plugin="'._DIR_PLUGIN_GMAPS.'";'."\n".
						'	var bouton_ok="'._T('gmaps:bouton_ok').'";'."\n".
						'	var bouton_cancel="'._T('gmaps:bouton_cancel').'";'."\n".
						'	var bouton_delete="'._T('gmaps:bouton_delete').'";'."\n".
						'	var alert_cancel="'._T('gmaps:alert_cancel').'";'."\n".
						'	var alert_delete="'._T('gmaps:alert_delete').'";'."\n".
						'</script>'."\n".
						'<script language="JavaScript" type="text/javascript" src="'._DIR_PLUGIN_GMAPS.'javascript/exec.js"></script>'."\n";
		}
		return $flux;
	}
?>
