<?php

function visiteurs_connectes_compter(){
 	    return count(preg_files(_DIR_TMP.'visites/','.'));
 	}
?>