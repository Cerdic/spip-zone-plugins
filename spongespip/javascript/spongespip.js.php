<?php
include("../../../ecrire/inc/utils.php");	
	
?>
$(document).ready(function() {
function bidule() {
			$('div.ligne:odd').addClass('ligne-impair');
			$('div.ligne:even').addClass('ligne-pair');
} 
	$('#ctn_sps').each(function() {
	bidule()
	})
$(".plus_details").each(function()
	{
	$(this).click(function() {
$("#details-"+this.id.split('-')[1]).slideToggle("slow");
	})});
$(".onglet a").each(function()
	{
	$(this).click(function() {
	$("#ctn_sps").html('');
	$('#chargement').ajaxStart(function(){$(this).show();}).ajaxStop(function(){$(this).hide();});

	var page= $(this).attr("href").split('=')[2].split('&')[0];
	$.get(document.location.href+"&onglet="+page+'','',function(txt){$("#ctn_sps").html(txt);bidule();});

	return false;
	})});
});