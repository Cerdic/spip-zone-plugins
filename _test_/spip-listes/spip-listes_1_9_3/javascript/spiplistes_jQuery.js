// JavaScript Document

$(document).ready(function(){
$("#ajax-loader").hide();
	$("#ajax-loader").ajaxStart(function(){
			$(this).show();
	});
		
	$("#ajax-loader").ajaxStop(function(){
		$(this).hide();
	});

	$("#template").submit(function(){
		var	 data = $('input,textarea,radio,select, checkbox', this).serialize();
		$.ajax({ type: "POST", 
					url: "./?exec=spiplistes_courrier_previsu", 
					data: data, 
					success: function(msg){  $("#apercu").html(msg); }
			});
		return false;
	});
});