<?php

function exec_googleajaxsearch_dist(){
	global $connect_statut;
	debut_page(_T('googleajaxsearch:config_plug'));

// Google map KEY	
	echo '<div align="center" style="width:700px;margin:20px">';
	if ($connect_statut == "0minirezo") {
		  
		echo debut_cadre('r');
    $google_key = $GLOBALS['meta']['google_key'];
 
    if ($google_key=="") { 
        // no google key available
        // look if GIS is setup and if a google key is available
        $query = "SELECT * FROM spip_gis_config WHERE name='googlemapkey'";
		    $result = spip_query($query);
		    $row = spip_fetch_array($result);		    
		    if (isset($row['value'])) {
		        $google_key = $row['value'];
		        ecrire_meta('google_key',$google_key);
    			  ecrire_metas();   
        }
		    
    }
    		
		if($_POST['ok']){	
            $google_key = $_POST['key'];				
		        ecrire_meta('google_key', $google_key);
    			  ecrire_metas();
		}
		
		echo '<br/>';
		echo '<a href="http://www.google.com/apis/maps" target="_blank" ><img src="'._DIR_PLUGIN_GOOGLEAJAXSEARCH.'img_pack/google_code.png" border="0" align="left" hspace="10" ></a>';
		echo '<form name="googlemapkey" method="post" action="'.generer_url_ecrire('googleajaxsearch').'">';
		echo '<br/>';
		echo '<label>Google AJAX Search API <a href="http://code.google.com/apis/ajaxsearch/signup.html" target="_blank" >'._T('googleajaxsearch:getkey').'</a></label> <input type="text" name="key" value="'.$google_key.'" size="30" />';
		echo '<input type="submit" name="ok" value="ok" />';
		echo '</form>';
		
		if($_POST['ok']){
			echo '<div align="center" style="margin:20px">';
			echo ''._T('googleajaxsearch:addkey').'<code>'.$_POST['key'].'</code>';
			echo '</div>';
		}
		echo fin_cadre(true);
	}
	echo '</div>';	
	fin_page();
}

?>
