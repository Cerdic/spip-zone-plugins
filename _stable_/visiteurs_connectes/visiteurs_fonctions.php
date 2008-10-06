<?php

function compter_visiteurs(){
 	    return count(preg_files(_DIR_TMP.'visites/','.'));
 	}
?>