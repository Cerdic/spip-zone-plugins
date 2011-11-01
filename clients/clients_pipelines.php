<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function clients_header_prive($flux)
{
	$flux .= '<script type="text/javascript">/* <![CDATA[ */';
	$flux .= '$(document).ready(function() {';
	$flux .= '$("li.editer_elm #champ_elm_1").each(function(){';
	$flux .= 'if (!$(this).is(":checked")){';
	$flux .= '	$("#champ_elm_2").hide();';
	$flux .= '	$("#champ_elm_2").siblings().hide();';
	$flux .= '	$(".editer_type_civ").hide();';
	$flux .= '	$(".editer_elm_civ").hide();';
	$flux .= '}});';
	$flux .= '$("li.editer_elm #champ_elm_1").click(function(){';
	$flux .= 'if ($(this).is(":checked")){';
	$flux .= '	$("#champ_elm_2").slideDown();';
	$flux .= '	$("#champ_elm_2").siblings().slideDown();';
	$flux .= '	$(".editer_type_civ").slideDown();';
	$flux .= '}else{';
	$flux .= '	$("#champ_elm_2").slideUp();';
	$flux .= '	$("#champ_elm_2").siblings().slideUp();';
	$flux .= '	$(".editer_type_civ").slideUp();';
	$flux .= '}';
	$flux .= '});';
		
	$flux .= '$("li.editer_type_civ #champ_type_civ_2").each(function(){';
	$flux .= 'if (!$(this).is(":checked")){';
	$flux .= '	$(".editer_elm_civ").hide();';
	$flux .= '}});';
	$flux .= '$("li.editer_type_civ #champ_type_civ_2, li.editer_elm #champ_elm_1").click(function(){';	
	$flux .= 'if ($(this).is(":checked")){';
	$flux .= '	$(".editer_elm_civ").slideDown();';
	$flux .= '}else{';
	$flux .= '	$(".editer_elm_civ").slideUp();';
	$flux .= '}';
	$flux .= '});';
	
	$flux .= '$("li.editer_type_civ #champ_type_civ_1").each(function(){';
	$flux .= 'if ($(this).is(":checked")){';
	$flux .= '	$(".editer_elm_civ").hide();';
	$flux .= '}});';
	$flux .= '$("li.editer_type_civ #champ_type_civ_1").click(function(){';
	$flux .= 'if ($(this).is(":checked")){';
	$flux .= '	$(".editer_elm_civ").slideUp();';
	$flux .= '}else{';
	$flux .= '	$(".editer_elm_civ").slideDown();';
	$flux .= '}});';
	
	$flux .= '$("li.editer_elm #champ_elm_3").each(function(){';
	$flux .= 'if (!$(this).is(":checked")){';
	$flux .= '	$("#champ_elm_4").hide();';
	$flux .= '	$("#champ_elm_4").siblings().hide();';
	$flux .= '}});';
	$flux .= '$("li.editer_elm #champ_elm_3").click(function(){';
	$flux .= 'if ($(this).is(":checked")){';
	$flux .= '	$("#champ_elm_4").slideDown();';
	$flux .= '	$("#champ_elm_4").siblings().slideDown();';
	$flux .= '}else{';
	$flux .= '	$("#champ_elm_4").slideUp();';
	$flux .= '	$("#champ_elm_4").siblings().slideUp();';
	$flux .= '}';
	$flux .= '});';
	
	$flux .= '$("li.editer_elm #champ_elm_6").each(function(){';
	$flux .= 'if (!$(this).is(":checked")){';
	$flux .= '	$("#champ_elm_7").hide();';
	$flux .= '	$("#champ_elm_7").siblings().hide();';
	$flux .= '}});';
	$flux .= '$("li.editer_elm #champ_elm_6").click(function(){';
	$flux .= 'if ($(this).is(":checked")){';
	$flux .= '	$("#champ_elm_7").slideDown();';
	$flux .= '	$("#champ_elm_7").siblings().slideDown();';
	$flux .= '}else{';
	$flux .= '	$("#champ_elm_7").slideUp();';
	$flux .= '	$("#champ_elm_7").siblings().slideUp();';
	$flux .= '}';
	$flux .= '});';	
	
	$flux .= '});';	
	$flux .= '/* ]]> */</script>';

    return $flux;
}
 
?>