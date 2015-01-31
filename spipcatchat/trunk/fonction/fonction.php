<?php
function ouestspip() 
{ $r=__DIR__; if(file_exists($r.'/spip.php')) { $r=$r.'/'; return $r;	}
	else {$i=0;
		do{ $c=preg_replace('"[/]{1}([a-z 0-9.@&%+#_-]){1,}$|[\\\]{1}([a-z 0-9.@&%+#_-]){1,}$"i',"",$r);
			if(file_exists($c.'/spip.php')) { $c=$c.'/'; return $c; break; } else { $r=$c; } $i++;
		}while($i!=10);
	}
}
function salon($id_auteur='',$id_salon='',$fonction='',$charset)
	{ include_once('../obj/salon.class.php');
    $salon=new salon('../db_catchat/',$charset);	
	$p['nom']=$salon->execute('texte',$id_salon,'');
	$p['code']=$salon->execute('texte',$id_salon+1,'');
	$p['public']=$salon->execute('texte',$id_salon+2,'');
	$p['admin']=$salon->execute('texte',$id_salon+3,'');
	if(file_exists($liste='../db_catchat/'.$p['code'].'/'.$p['code'].'.js'))
	{ if(false!=($fileDB=file_get_contents($liste))){$id_liste=json_decode($fileDB,true);}
		if($fonction=='autorite')
		{	if( $p['public']=='true' && $p['admin']==$id_auteur) { return 2;}
			elseif( $p['public']=='false' && $p['admin']==$id_auteur) { return 3;}
			elseif( in_array($id_auteur,$id_liste)=='true' && $p['public']=='false') {  return 4; }
			elseif( $p['admin']!=$id_auteur && $p['public']=='true') { return 1;}
			else { return 0;}
		}
	} unset($salon); return $p;
}
function nom($code)
	{ preg_match('#^id[0-9]{1,}_{1}(.*)#',$code,$date); return $date[1]; }
function id($code)
	{ preg_match('#^id([0-9]{1,})_{1}#',$code,$date); return $date[1]; }
function statut($code)
	{ preg_match('#^([1-3]{1})_{1}#',$code,$date); return $date[1]; }
function chatdate($code)
	{ preg_match('#^[1-3]{1}_{1}([0-9]{8,20})$#',$code,$date); return $date[1]; }
function onlineChat($id_auteur='',$url='',$fonc='',$statut='')
	{ include_once('../obj/online.class.php');
	$line = new online('../db_catchat/'.$url.'/'.$url);	
	$p=$line->execute($fonc,$id_auteur,$statut); unset($line);
return $p;
} ?>