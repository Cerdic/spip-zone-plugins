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
		var back = $("<div class=jqDialog_back id=jqDialog_back "
			+"style='position:absolute; background-color:black; z-index:2000; display:block; ' >"
			+"</div>").width(wt).height(ht).css("left",0).css("top",0).css("opacity",0.4).appendTo("body");
		
		if (jQuery('#jqDialog').length != 0) jQuery('#jqDialog').remove();
		var dialog = "<div class='"+param.classe+"' id=jqDialog "
			+"style='position:absolute; z-index:2001; display:block; ' >"
			+"<div class='jqCloseButton' onclick='javascript:jQuery.jqDialog.action(\"undo\")'></div>"
//			+"<input type=image class='jqCloseButton' onclick='javascript:jQuery.jqDialog.action(\"undo\")' value='' />"
			+"<p>"+title+"</p>"
			+"<div class=jqDialogBlock ><table width=100% border=0><tr valign=top>";
		if (param.icon) dialog += "<td width=1%><img class=jqDialogImg src='"+param.icon+"' style='float:left' /></td>"
		dialog += "<td><div class=jqDialogInner style='padding-bottom:1em;' >"+param.dialog+"</div></td></tr></table>"
			+"<div style='text-align:right; '>";
		if (param.ok) dialog += "<input class=jqDialogButton onclick='javascript:jQuery.jqDialog.action(\"ok\")' type=button value='"+param.ok.replace("\'","&acute;")+"' />";
		if (param.no) dialog += "<input class=jqDialogButton onclick='javascript:jQuery.jqDialog.action(\"no\")' type=button value='"+param.no.replace("\'","&acute;")+"' />";
		if (param.undo) dialog += "<input class=jqDialogButton onclick='javascript:jQuery.jqDialog.action(\"undo\")' type=button value='"+param.undo.replace("\'","&acute;")+"' />";
		dialog += "</div></div></div>";
		
		var d = $(dialog).appendTo("body");

		// Verifier la coherence
		if (!$('#jqDialog .jqDialogBlock').css('padding')
		|| $('#jqDialog .jqDialogBlock').css('padding')=='0px') $('#jqDialog .jqDialogBlock').css('padding','0 1em 1em 1em');
		if (d.css('background-color')=='transparent') d.css('background-color','#FFF');
		if (d.width()<200) d.width(200);
		
		// Centrer la fenetre pour le choix
		jQuery('#jqDialog').css('left',scrollX+(w-jQuery('#jqDialog').width())/2);
		jQuery('#jqDialog').css('top',scrollY+(h-jQuery('#jqDialog').height())/3);

		// Gestion des raccourcis clavier
		jQuery('#jqDialog').keydown(function (e) 
		{	var code = (e.keyCode ? e.keyCode : e.which); 
			if (code == 13) { $.jqDialog.action('ok'); return false; }
			if (code == 27) { $.jqDialog.action('undo'); return false; }
 			return true;
		});
		input = jQuery('#jqDialog input');
		if (input.length) input[0].focus();
		
	};
	
	// Rejouer le dialogue (si champs mal remplis)
	$.jqDialog.replay = function()
	{	jQuery('#jqDialog_back').show();
		jQuery('#jqDialog').show();
	};
	
	// On a fini (ne pas detruire ici si on veut rejouer)
	$.jqDialog.action = function(action)
	{	jQuery('#jqDialog_back').hide();
		jQuery('#jqDialog').hide();
		$.jqDialog.param.callback (action);
	};
	
	// Options
	$.jqDialog.defaults = {
 		ok		: ' Ok ',			// OK (Yes) button value (null if no button)
 		no		: null,				// No button value (null if no button)
 		undo	: 'Annuler',		// Cancel button value (null if no button)
 		icon	: null,				// Icon file
 		classe	: 'jqDialog',		// Dialog class (for css manipulation)
 		dialog	: '...',			// The dialog
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

