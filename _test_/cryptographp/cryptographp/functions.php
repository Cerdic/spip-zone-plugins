<?php

// -----------------------------------------------
// Cryptographp v1.3
// (c) 2006 Sylvain BRISON 
//
// www.cryptographp.com 
// cryptographp@alphpa.com 
//
// Licence CeCILL (Voir Licence_CeCILL_V2-fr.txt)
// -----------------------------------------------

 if(session_id() == "") session_start();
 $_SESSION['cryptdir'] =  str_replace ($_SERVER['DOCUMENT_ROOT'],'',str_replace('\\','/',dirname(__FILE__)))."/";
 
 function dsp_crypt($cfg=0,$reload=1,$ret=0) {
 // Affiche le cryptogramme
 $out =  "<table><tr><td>";
 $out.= dsp_crypt_img($cfg,1);
 $out.= "</td>";

 if ($reload) 
 {
 	$out.= "<td>";
	$out.= dsp_crypt_btn($cfg,1);
 	$out.= "</td>";
 }
 $out .= "</tr></table>";

 if ( $ret ) return $out;
 echo $out;
 }

 function dsp_crypt_img($cfg=0,$ret=0) {
	$out = "<img id='cryptogram' src='".$_SESSION['cryptdir']."cryptographp.php?cfg=".$cfg.(SID==""?'':"&amp;".SID)."' alt='' title='' />";

 	if ( $ret ) return $out;
 	echo $out;
 }

 function dsp_crypt_btn($cfg=0,$ret=0) {
 	$out = "<a title='".($reload==1?'':$reload)."' style=\"cursor:pointer\" onclick=\"javascript:document.images.cryptogram.src='".$_SESSION['cryptdir']."cryptographp.php?cfg=".$cfg.(SID==""?'':"&amp;".SID)."&amp;'+Math.round(Math.random(0)*1000)+1\"><img src=\"".$_SESSION['cryptdir']."images/reload.png\" alt='' title='' /></a>";
	
 	if ( $ret ) return $out;
 	echo $out;
 }


 function chk_crypt($code) {
 // Vérifie si le code est correct
 include ($_SESSION['configfile']);
 $code = addslashes ($code);
 $code = ($difuplow?$code:strtoupper($code));
 switch (strtoupper($cryptsecure)) {    
        case "MD5"  : $code = md5($code); break;
        case "SHA1" : $code = sha1($code); break;
        }
 if ($_SESSION['cryptcode'] and ($_SESSION['cryptcode'] == $code))
    {
    unset($_SESSION['cryptreload']);
    return true;
    }
    else {
         $_SESSION['cryptreload']= true;
         return false;
         }
 }

?>
