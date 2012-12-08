<?php
/* Description : 
 *     $toolbars = tableau de barres d'outils
 *     une barre d'outils = tableau dont les clés sont les noms des outils et les valeurs
 *     des tableaux : (0=>taille de l'outil, 1=>cet outil doit-il être activé par défaut, 2=>cet outil est-il compatible avec la typo spip, 3=>numéro de l'icons ou unset)
 */
$GLOBALS['toolbars'] = array(
	array('Source'=>array(66,0,0,0)),
	array('SpipSave'=>array(24,0,1,2),'NewPage'=>array(24,0,1,3),'ZpipPreview'=>array(24,0,1,4)),
	array('Templates'=>array(24,0,1,5)),
	array('Cut'=>array(24,1,1,6),'Copy'=>array(24,1,1,7),'Paste'=>array(24,1,1,8),'PasteText'=>array(24,1,1,9),'PasteFromWord'=>array(24,0,0,10)),
	array('Print'=>array(24,0,1,11),'SpellChecker'=>array(24,1,1,12),'Scayt'=>array(32,1,1,12)),
	array('Undo'=>array(24,1,1,13),'Redo'=>array(24,1,1,14)),
	array('Find'=>array(24,1,1,15),'Replace'=>array(24,1,1,16)),
	array('SelectAll'=>array(24,1,1,17),'RemoveFormat'=>array(24,1,1,18)),
	array('Form'=>array(24,0,0,47),'Checkbox'=>array(24,0,0,48),'Radio'=>array(24,0,0,49),'TextField'=>array(24,0,0,50),'Textarea'=>array(24,0,0,51),'Select'=>array(24,0,0,52),'Button'=>array(24,0,0,53),'ImageButton'=>array(24,0,0,54),'HiddenField'=>array(24,0,0,55)),
	array('Bold'=>array(24,1,1,19),'Italic'=>array(24,1,1,20),'Underline'=>array(24,1,1,21),'Strike'=>array(24,1,0,22)),
	array('Subscript'=>array(24,1,1,23),'Superscript'=>array(24,1,1,24)),
	array('NumberedList'=>array(24,1,1,25),'BulletedList'=>array(24,1,1,26), 'Outdent'=>array(24,1,1,27),'Indent'=>array(24,1,1,28),'Blockquote'=>array(24,0,0,72)),
	array('JustifyLeft'=>array(24,1,1,29),'JustifyCenter'=>array(24,1,1,30),'JustifyRight'=>array(24,1,1,31),'JustifyBlock'=>array(24,1,1,32)),
	array('Spip'=>array(24,1,1,81),'Link'=>array(24,1,1,33),'Unlink'=>array(24,1,1,34),'Anchor'=>array(24,1,1,35),'Iframe'=>array(24,0,0,80)),
	array('SpipModeles'=>array(88,1,1)),
	array('SpipDoc'=>array(24,1,1,82),'Image'=>array(24,0,0,36),'Flash'=>array(24,0,1,37),'Table'=>array(24,1,1,38),'HorizontalRule'=>array(24,1,1,39),'Smiley'=>array(24,1,1,40),'SpecialChar'=>array(24,1,1,41),'PageBreak'=>array(24,0,0,42)),
	array('Styles'=>array(91,1,0),'Format'=>array(91,1,1),'Font'=>array(91,1,0),'FontSize'=>array(56,1,0)),
	array('TextColor'=>array(32,1,0,44),'BGColor'=>array(32,1,0,45)),
	array('Maximize'=>array(24,1,1,55), 'ShowBlocks'=>array(24,0,1,71),'About'=>array(24,1,1,46))
) ;

?>
