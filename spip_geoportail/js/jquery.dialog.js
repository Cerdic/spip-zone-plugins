/**
 * Dialog plugin
 *
 * Examples and documentation at: http://
 *
 * @author: Jean-Marc Viglino
 * @version: 1.0
 *
 * Copyright (c) 2009 Jean-Marc Viglino (ign.fr)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
/**
 *	Show a javascript dialog
 *
 *	You can pass a single string or more options.
 *	Handle the keyboard ENTER and ESC key to validate or undo
 *	Use the replay function to redraw the last dialog.
 *
 */
(function($) { // Pour jQuery.noConflict()

	$.jqDialog = function(title, options)
	{	// Options par defaut
		var param = $.jqDialog.param = $.extend({}, $.jqDialog.defaults, options);
		if (typeof(options) == 'string') param.dialog = options;
		
		// Info sur la fenetre
		var de = document.documentElement;
		var wt = $("body").width() + parseInt( $("body").css("margin-right")) + parseInt( $("body").css("margin-left")) +15;
		var ht = $("body").height() + parseInt( $("body").css("margin-top")) + parseInt( $("body").css("margin-bottom")) +15;
		var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
		var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight
		if (wt<w) wt=w;
		if (ht<h) ht=h;
		var scrollX = (typeof( window.pageXOffset ) == 'number') ? window.pageXOffset : (document.body && document.body.scrollLef) ? document.body.scrollLef : (document.documentElement) ? document.documentElement.scrollLeft : 0;
		var scrollY = (typeof( window.pageYOffset ) == 'number') ? window.pageYOffset : (document.body && document.body.scrollTop) ? document.body.scrollTop : (document.documentElement) ? document.documentElement.scrollTop : 0;
		// Creer ce dont on a besoins :
		if (jQuery('#jqDialog_back').length != 0) jQuery('#jqDialog_back').remove();
		var back = $("<div class='jqDialog_back "+param.classe+"' id=jqDialog_back "
			+"style='position:absolute; background-color:black; z-index:2000; display:block; ' >"
			+"</div>").width(wt).height(ht).css("left",0).css("top",0).css("opacity",param.bgopacity).appendTo("body");
		
		// Le dialogue
		if (jQuery('#jqDialog').length != 0) jQuery('#jqDialog').remove();
		var dialog = "<div class='"+param.classe+"' id=jqDialog "
			+"style='position:absolute; z-index:2001; display:block; ' >"
			+(param.closebt ? "<div class='jqCloseButton' onclick='javascript:jQuery.jqDialog.action(\"undo\")' ></div>" : "")
//			+"<input type=image class='jqCloseButton' onclick='javascript:jQuery.jqDialog.action(\"undo\")' value='' />"
			+(title ? "<p class='titre'>"+title+"</p>" : "")
			+"<div class=jqDialogBlock >";
		if (param.icon) 
		{	dialog += "<table width=100% border=0 ><tr valign=top>"
					+ "<td width=1%><img class=jqDialogImg src='"+param.icon+"' style='float:left' /></td><td>";
		}
		if (param.dialog) dialog += "<div class=jqDialogInner>"+param.dialog+"</div>"
		if (param.icon) dialog += "</tr></table>"
		dialog += "<div class=button style='text-align:right; '>";
		if (param.ok) dialog += "<input class='jqDialogButton jqDialogButtonOk' onclick='javascript:jQuery.jqDialog.action(\"ok\")' type=button value='"+param.ok.replace("\'","&acute;")+"' />";
		if (param.no) dialog += "<input class='jqDialogButton jqDialogButtonNo' onclick='javascript:jQuery.jqDialog.action(\"no\")' type=button value='"+param.no.replace("\'","&acute;")+"' />";
		if (param.undo) dialog += "<input class='jqDialogButton jqDialogButtonCancel' onclick='javascript:jQuery.jqDialog.action(\"undo\")' type=button value='"+param.undo.replace("\'","&acute;")+"' />";
		dialog += "</div></div></div>";
		
		var d = $(dialog).hide().appendTo("body");

		// Fenetre proportionnelle
		var dw=param.width;
		var dh=param.height;
		var prop="";
		
		if (dw && dh)
		{	if (dw>$(window).width()*0.9) 
			{	dw = Math.round($(window).width()*0.9);
				dh = Math.round(param.height * dw / param.width );
			}
			if (dh>$(window).height()*0.9)
			{	dh = Math.round($(window).height()*0.9);
				dw = Math.round(param.width * dh / param.height);
			}
			d.width(dw).height(dh);
		}
		// ou pas trop grande
		else if (d.height() > 0.8*h) $("#jqDialog .jqDialogInner").height(Math.round(0.8*h));
		
		// Fermer si clickout
		if (param.clickout) back.click(jQuery.jqDialog.cout);
		if (param.clickin) d.click(jQuery.jqDialog.cin);

		// Verifier la coherence
		if (!$('#jqDialog .jqDialogBlock').css('padding')
		|| $('#jqDialog .jqDialogBlock').css('padding')=='0px') $('#jqDialog .jqDialogBlock').css('padding','0 1em 1em 1em');
		if (d.css('background-color')=='transparent') d.css('background-color','#FFF');
		if (d.width()<200) d.width(200);
		
		// Centrer la fenetre pour le choix
		jQuery('#jqDialog').css('left',scrollX+(w-jQuery('#jqDialog').width())/2);
		jQuery('#jqDialog').css('top',scrollY+(h-jQuery('#jqDialog').height())/3);

		// Ombrage
		if (jQuery('#jqDialogShadow').length != 0) jQuery('#jqDialogShadow').remove();
		if (param.shadow)
		{	var shadow = "<div class='"+param.classe+"' id=jqDialogShadow style='position:absolute; z-index:2000; display:none'></div>";
			var sh = $(shadow).width(d.width()).height(d.height()).css("opacity",0.3).appendTo("body");
			if (sh.css('background-color')=='transparent') sh.css('background-color','#000');
			sh.css('left',scrollX+(w-jQuery('#jqDialog').width())/2 +param.shadow);
			sh.css('top',scrollY+(h-jQuery('#jqDialog').height())/3 +param.shadow);
		}
		
		// Affichage
		$.jqDialog.replay(param.speed);
		
		// Focus 
		jQuery('#jqDialog input[type=text]').focus().select();
		
	};
	
	// Rejouer le dialogue (si champs mal remplis)
	$.jqDialog.replay = function(speed)
	{	jQuery('#jqDialog_back').show();
		jQuery('#jqDialog').fadeIn(speed);
		jQuery('#jqDialogShadow').fadeIn(speed);
	};
	
	// Fermer le dialog
	$.jqDialog.close = function()
	{	$.jqDialog.action("undo");
	}
	// Clic dans le dialogue
	$.jqDialog.cin = function()
	{	$.jqDialog.action("in");
	}
	// Clic hors du dialogue
	$.jqDialog.cout = function()
	{	$.jqDialog.action("out");
	}
	
	// On a fini (ne pas detruire ici si on veut rejouer)
	$.jqDialog.action = function(action)
	{	jQuery('#jqDialog_back').hide();
		jQuery('#jqDialog').hide();
		jQuery('#jqDialogShadow').hide();
		$.jqDialog.param.callback (action);
	};
	
	// Attente (sans dialogue)
	$.jqDialog.wait = function(noclick)
	{	$.jqDialog ("", { dialog:"", classe:"waiting", clickout:(noclick?false:true), clickin:(noclick?false:true), undo:false, ok:false });
		$("#jqDialog").css("opacity",0.8)
	}
	
	// Options
	$.jqDialog.defaults = {
 		ok		: ' Ok ',			// OK (Yes) button value (null if no button)
 		no		: null,				// No button value (null if no button)
 		undo	: 'Annuler',		// Cancel button value (null if no button)
 		icon	: null,				// Icon file
 		classe	: 'jqDialog',		// Dialog class (for css manipulation)
 		dialog	: '...',			// The dialog
 		width	: null,				// Dialog size (proportion)
 		height	: null,				// 
 		clickout: false,			// Close the dialog when clickout
 		clickin	:false,				// Close the dialog when clickin
 		bgopacity : 0.4,			// Background Opacity
 		shadow	: 0,				// Shadow offset (px)
 		speed	: 0,				// Speed to fade in
 		closebt	: true,				// Dialog has a close button
 		callback : function(action)	// Function to callback when closing
 		{	// NB: $('#jqDialog') is not destroy at this step, 
 			// You can access the input you put in the dialog param
 			/*
			switch (action)
			{	// input value
				val = $('#jqDialog input').val();
				// Do something
				case 'ok': alert('ok'); break;
				case 'no': alert('no'); break;
				default: alert('undo'); break;
			}
			*/
		}
	};
	
	$.jqDialog.param = $.jqDialog.defaults;
	
})(jQuery);

// Gestion des raccourcis clavier
jQuery(document).keydown(function (e) 
{	var code = (e.keyCode ? e.keyCode : e.which); 
	if (jQuery("#jqDialog .jqDialogButtonOk").length && ! jQuery("textarea:focus").length && code == 13) { $.jqDialog.action('ok'); return false; }
	if (jQuery("#jqDialog .jqCloseButton").length && code == 27) { $.jqDialog.action('undo'); return false; }
	return true;
});
