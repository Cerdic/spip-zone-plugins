<?php
#---------------------------------------------------#
#  Plugin  : E-Learning                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : https://contrib.spip.net/Plugin-E-learning  #
#-----------------------------------------------------------------#

function elearning_jeux_caracteristiques($jeux_caracteristiques){

	// Le séparateur qui défini le jeu
	define('_JEUX_QUESTION_OUVERTE', 'question_ouverte');
	
	$jeux_caracteristiques['SEPARATEURS']['question_ouverte'] = array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_QUESTION_OUVERTE, _JEUX_REPONSE, _JEUX_CONFIG);
	$jeux_caracteristiques['SIGNATURES']['question_ouverte'] = array(_JEUX_QUESTION_OUVERTE);
	$jeux_caracteristiques['TYPES']['question_ouverte'] = _T('question_ouverte:question_ouverte');
	
	return $jeux_caracteristiques;

}

?>
