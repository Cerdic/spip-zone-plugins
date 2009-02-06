<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
 * Inspire de Spip-Listes
 * $Id$
*/

	// Fonction utilitaires
	function abomailmans_abomailman_editable($id_abomailman = 0) {
		global $connect_statut;
		return $connect_statut == '0minirezo';
	}
	
	function abomailmans_abomailman_administrable($id_abomailman = 0) {
		global $connect_statut;
		spip_log('connect_statut ='.$connect_statut);
		return $connect_statut == '0minirezo';
	}

//* Envoi de mail via Mailman
	function abomailman_mail ($from_nom, $from_email, $to_nom, $to_email, $subject="", $body="", $html="", $charset="") {	
		include_spip ("class/phpmailer/class.phpmailer");
		$mail = new PHPMailer();
		$mail ->CharSet  =  $charset;
		if ($html==true) $mail->IsHTML(true);
		$mail->FromName = $from_nom;
		$mail->From = $from_email;
	 	$mail->AddAddress($to_email);
		$mail->Subject = $subject;
		$mail->Body = $body;

		if(!$mail->Send())	{
	  		return false; }
		else {
			return true;
		}
	}


	function abomailman_http_build_query($data,$prefix=null,$sep='',$key=''){
		if(!function_exists('http_build_query')) {
		    function http_build_query($data,$prefix=null,$sep='',$key='') {
		        $ret = array();
		            foreach((array)$data as $k => $v) {
		                $k    = urlencode($k);
		                if(is_int($k) && $prefix != null) {
		                    $k    = $prefix.$k;
		                };
		                if(!empty($key)) {
		                    $k    = $key."[".$k."]";
		                };
		
		                if(is_array($v) || is_object($v)) {
		                    array_push($ret,http_build_query($v,"",$sep,$k));
		                }
		                else {
		                    array_push($ret,$k."=".urlencode($v));
		                };
		            };
		
		        if(empty($sep)) {
		            $sep = ini_get("arg_separator.output");
		        };
		        return implode($sep, $ret);
		    };
		};
		return http_build_query($data);
	}

// Afficher l'arbo
function  abomailman_arbo_rubriques($id_rubrique,  $rslt_id_rubrique="") {
    global $ran;
    $ran ++;
    
    $marge="&nbsp;&nbsp;&nbsp;|";
    for ($g=0;$g<$ran;$g++) {
        if (($ran-1)==0) {
            $marge="&bull;";
        }
        else {
            $marge .="---";
        }
    }
    $marge .="&nbsp;";

    $rqt_rubriques = spip_query ("SELECT id_rubrique, id_parent, titre FROM spip_rubriques WHERE id_parent='".$id_rubrique."'");
    while ($row = spip_fetch_array($rqt_rubriques)) {
        $id_rubrique = $row['id_rubrique'];
        $id_parent = $row['id_parent'];
        $titre = $row['titre'];
        $arbo .="<option value='".$id_rubrique."'>" . $marge  . supprimer_numero (typo($titre)) . "</option>";
        $arbo .= abomailman_arbo_rubriques($id_rubrique,   $rslt_id_parent);
    }
    
    $ran --;
    return $arbo;
    
}

?>
