/*
  	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

 	/*****************************************************
	Copyright (C) 2007-2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Amocles.
	
	Amocles is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Amocles is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Amocles; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Amocles. 
	
	Amocles est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiee par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Amocles est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.
	
	*****************************************************/
 
$(document).ready(function(){
	
	var amocles_lang = false
	, amocles_spip_textarea = '#page>table>tbody>tr>td.serif>div.cadre-formulaire>form>div>div.serif>textarea.forml:eq(1)'
	, amocles_values
	;
	amocles_values = new Array();
	
	$('#amocles-menu a').click( function() { 
		$('#amocles-menu a').removeClass('lang-sel');
		$(this).addClass('lang-sel');
		amocles_lang = $(this).attr('lang');
		
		var ori, ii = $('#amocles-ventre input[@type=hidden][@name=' + amocles_lang + ']').val();
		
		$('#amocles-ventre-titre').html(ii);
		$('#amocles-ventre').show();
		
		ori = ii = $.trim($(amocles_spip_textarea).text());
		ii = ii.replace(/\s+/gm, ' ');
		
		/* valide l'entree. Si multi, explode, sinon, place le texte
			dans la valeur de langue courante
		*/
		ii = ii.match(/^<multi>(.*?)<\/multi>$/);
		if(ii) {
			ii = ii[1];
			ii = ii.split('[');
			var z, m, r, x;
			for(z = 0, m = ii.length; z < m; z++)  {
				r = $.trim(ii[z]);
				if(r && r.length > 0) {
					x = r.substr(0,2);
					amocles_values[x] = $.trim(r.substr(3));
				}
			}
		}
		else {
			amocles_values[amocles_lang] = ori;
		}
		
		if(amocles_values[amocles_lang]) {
			$('#amocles-ventre-texte').val(amocles_values[amocles_lang]);
		}
	});
	
	$('#amocles-ventre-texte').change( function() { 
		amocles_values[amocles_lang] = this.value;
		var ii = '<multi>';
		for (var cle in amocles_values) {
      	ii += '[' + cle + ']' + amocles_values[cle];
		}
		ii += '</multi>';
		$(amocles_spip_textarea).text(ii);
	});

	/* insertion de la boite generateur en articles */
	/* ?exec=articles&id_article=1 */
	var request_uri = window.location.search;
	var article_uri = "/^\?exec=articles&id_article=/";
	if(request_uri.match(/^\?exec=articles&id_article=/)) 
	{
		var article_id = request_uri.replace(/^\?exec=articles&id_article=/, "");
		if(article_id > 0)
		{
			/* SPIP 1.92 ? */
			if ($("#editer_mot-" + article_id).is('div'))
			{
				var boite_mots = "#editer_mot-" + article_id;
				var boite_spip_mots = boite_mots + " .cadre-padding";
				var deplier_boite_mots = boite_mots + " .swap-couche";
			}
			/* SPIP 2 ? */
			else if ($("#editer_mots-" + article_id).is('div'))
			{
				var boite_mots = "#lesmots";
				var boite_spip_mots = "#lesmots";
				var deplier_boite_mots = boite_mots + " .titrem";
			}
			if(boite_mots && amocles_articles_boite_mots_url) 
			{
				$(boite_spip_mots).after("<div id='amocles_generer_mots'></div>");

				if ($("#amocles_generer_mots").html() == "") {
					/* ajoute le lien qui doit se deplier en meme temps que la boite mots-cles */
					$.ajax({
						type: "POST"
						, data : ""
						, url: amocles_articles_boite_mots_url
						, success: function(msg){
							$("#amocles_generer_mots").html(msg);
						}
					});
				}
				
				$(deplier_boite_mots).click( function() { 
					/* le lien generer apparait en meme temps que la boite des mots */
					$("#amocles_generer_mots").toggle();
				});
			}
		}
	}
	
	/* Formulaire des mots vides, sélecteur de langue */
	$(".amocles-select-lang").click( function() { 
		var bug_display = false;
		if(!bug_display)
		{
			$("#form_stopwords textarea").width($("#form_stopwords textarea.block").width());
		}
		$(".amocles-select-lang").removeClass("lang-sel");
		$(this).addClass('lang-sel');
		$("#form_stopwords textarea").hide().removeClass('block');
		$("#form_stopwords textarea[@lang=" + $(this).attr('lang') + "]").show().addClass('block');
	});
	
});