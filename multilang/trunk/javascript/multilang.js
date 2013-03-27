/**
 * multilang
 *
 * Copyright (c) 2006-2010 Renato Formato (rformato@gmail.com)
 * Licensed under the GPL License:
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Modif Yffic mars 2010
 * - Correction bug : Fonctionnement incoherent si plusieurs menu dans la meme page (ex : portfolio dans page de
 *   presentation d'un article)
 * - On ne rajoute pas le menu s'il existe deja (cas d'un retour Ajax)
 * - On ne rajoute pas le menu dans forms d'upload (par ex vignette d'un doc)
 */


var multilang_containers={}, //menu containers
    multilang_forms_fields={};

/*
(?:\[([a-z_]+)\]|^[\s\n]*)
[lang] or white space

((?:.|\n)*?)
all chars not greedy

(?=\[[a-z_]+\]|$)
[lang] or end string
*/

/**
 * Initialisation de différentes variables :
 *
 */
var multilang_match_multi = /(?:\[([a-z_]+)\]|^[\s\n]*)((?:.|\n|\s)*?)(?=\[[a-z_]+\]|$)/ig;
var multilang_jq_root, //root of the search (jQuery object)
    multilang_root_opt,
    multilang_fields_selector,
    multilang_fields_selector_opt,
    multilang_menu_selector,
    multilang_containers,
    multilang_forms_toadd,
    multilang_forms, //forms to be processed (jQuery object)
    multilang_menu_lang, //template of the menu (jQuery object)
    multilang_forms_selector, //selector of the forms to be processed (string)
    multilang_init = false;

/**
 * options is a hash having the following values:
 * - fields (mandatory): a jQuery selector to set the fields that have to be internationalized.
 * - page (optional): a string to be searched in the current url. if found the plugin is applied.
 * - root (optional): the root element of all processing. Default value is 'document'. To speed up search
 * - forms (optional): a jQuery selector to set the forms that have to be internationalized. Default value is 'form'.
 * - main_menu (optional): a jQuery selector to set the container for the main menu to control all the selected forms.
 * - form_menu (optional): a jQuery selector to set the container for the form menus.
 */
function multilang_init_lang(options) {
	var init_done = options.init_done || multilang_init;
	//Detect if we're on the right page and if multilinguism is activated. If not return.
	if((options.page && window.location.search.indexOf(options.page)==-1) || multilang_avail_langs.length<=1) return;
	
	//set the root element of all processing

	var root = options.root || document;
	multilang_jq_root = $(root).add($(options.root_opt).parent());
	multilang_root_opt = options.root_opt;

	/**
	 * set the main menu element
	 * Plus utilisé pour l'instant
	 */
	multilang_containers = options.main_menu ? $(options.main_menu,multilang_jq_root) : $([]);

	multilang_forms_toadd = $([]);

	/**
	 * On crée le modèle du menu de langue
	 * C'est ce modèle qui est cloné au début de chaque formulaire
	 */
	multilang_menu_lang = $("<div class='langues'></div>");
	$.each(multilang_avail_langs,function() {
		var title = 'multilang_lang.title_lien_multi_'+this;
		multilang_menu_lang.append($("<a class='change_lang "+this+"' title='"+eval(title)+"'></a>").html("["+this+"]"));
	});
	multilang_menu_lang.append($("<a class='recover_lang' href='#'></a>").html("["+multilang_lang.lien_desactiver+"]"));

	//init fields
	multilang_fields_selector = options.fields;
	multilang_fields_selector_opt = options.fields_opt;

	//store all the internationalized forms
	multilang_forms_selector = options.forms || "form";

	if(init_done){
		multilang_forms_toadd = $(multilang_forms_selector,multilang_jq_root).not($(multilang_forms));
	}
	multilang_forms = $(multilang_forms_selector,multilang_jq_root);
	if(!init_done){
		multilang_forms_toadd = multilang_forms;
	}

	//create menu lang for the global form
	if(multilang_containers.size())
		multilang_make_menu_lang(multilang_containers);
	multilang_menu_selector = options.form_menu;

	multilang_init = true;

	// Modif Yffic : On va pas plus loin s'il n'y a pas de form
	if(multilang_forms_toadd.size()) multilang_init_multi();

}

/**
 * Initialisation des champs multi sur les formulaires
 *
 * @param options
 * @return
 */
function multilang_init_multi(options) {
	var target = options ? options.target : null;
	var init_forms;
	//Update the list of form if this is an update
	if(target) {
		//Verify the target is really a form to be internationalized (in case of an ajax request fired by onAjaxLoad)
		if(target==document) return;
		init_forms = $(target).find('form').in_set($(multilang_forms_selector,multilang_jq_root));
		if(!init_forms.length) return;
		multilang_forms.add(init_forms.each(multilang_attach_submit).get());
	} else {
		//attach multi processing to submit event
		init_forms = multilang_forms_toadd;
		multilang_forms_toadd.each(multilang_attach_submit);
	}

	multilang_forms_fields = {};
	multilang_forms_fields["undefined"] = $(multilang_fields_selector,multilang_forms);
	//init the value of the field to current lang
	//add a container for the language menu inside the form
	init_forms.each(function() {
		/*
		 * Je ne sais pas à quoi cela sert particulièrement, désactivé pour l'instant
		 */
		//$(this).find('input[type=submit],button').click(function(){
			//multilang_multi_submit.apply($(this).parents('form').get(0));
			//$(this).parents('form').submit();
			//return false;
		//});
		this.isfull = false;
		this.form_lang = multilang_def_lang;
		var container = multilang_menu_selector ? $(multilang_menu_selector,this) : $(this);
		// Pas de rajout s'il y en deja un
		if(!container.find('.menu_multilang').size())
			container.prepend("<div class='menu_multilang'></div>");
	});

	/**
	 * Initialisation de chaque input ou textarea
	 * On vérifie si on est dans un formulaire optionnel (dans ce cas on ne prend que
	 * les éléments qui on la class optionnelle) sinon on prend tous les champs qui
	 * matchent
	 */
	$(multilang_fields_selector,init_forms).each(function(){
	    var me = $(this);
	    if(me.closest(multilang_root_opt).length){
	        if(me.is(multilang_fields_selector_opt)){
	        	multilang_init_field(this,this.form.form_lang);
	        }
	    }else{
			multilang_init_field(this,this.form.form_lang);
		}
	});
	//create menu for each form. The menu is just before the form
	$("div.menu_multilang",init_forms).empty().each(function() {
		//store all form containers to allow menu lang update on each container
		//when it is triggered by global menu
		multilang_containers.add(this);
		multilang_make_menu_lang($(this),$(this).parents("form"));
	});
}

/**
 * Création du menu de langue
 * Liste les langues disponibles et ajoute un lien pour désactiver multilang
 *
 * @param container
 * @param target
 * @return
 */
function multilang_make_menu_lang(container,target) {
	target = target || multilang_forms;
	$(multilang_menu_lang).clone().find("a").click(function() {
		if($(this).is('.change_lang') && !$(this).is('.on')){
			$(this).parents('form > .menu_multilang').find('a.on').removeClass('on');
			$(this).parents('form > .menu_multilang').find('.multilang_message').detach();
			$(this).parents('form').find('li.editer_titre_numero,div.editer_titre_numero').show();
			$(this).addClass('on');
			multilang_change_lang(this,container,target);
		}else if(!$(this).is('.on') && $(this).is('.recover_lang')){
			$(this).parents('form > .menu_multilang').find('a.on').removeClass('on');
			$(this).parents('form > .menu_multilang').append('<div class="multilang_message"><p>'+multilang_lang.champs_readonly+'<\/p><\/div>');
			$(this).parents('form').find('li.editer_titre_numero,div.editer_titre_numero').hide();
			$(this).addClass('on');
			multilang_multi_recover(this,container,target,'submit');
		}
		return false;
	}).end().appendTo(container);
	$(target).find('.menu_multilang .langues a').eq(0).addClass('on');

	var target_id = multilang_init_target_id(target);
	multilang_forms_fields[target_id].each(function(){
		multilang_save_lang(this,this.form.form_lang);
	});
	// Maj du menu de langues
	multilang_mark_empty_langs(container,target);
}

/**
 * Initialise target_id
 *
 * @param target Le formulaire
 * @return
 */
function multilang_init_target_id(target){
	var target_id = target != multilang_forms ? jQuery.data(target[0]) : "undefined";
	multilang_forms_fields[target_id] = $(multilang_fields_selector,target);
	return(target_id);
}

/**
 * Affiche le contenu complet du champ
 * Utilisé lors de la désactivation de multilang et de la validation des formulaires
 *
 * @param el Le lien de désactivation
 * @param container Le container du formulaire
 * @param target Le formulaire
 * @return
 */
function multilang_multi_recover(el,container,target,event){
	if(target[0].isfull){
		return true;
	}
	if(event == 'submit'){
		lang = 'full';
		var target_id = multilang_init_target_id(target);
		target[0].isfull = true;
		multilang_forms_fields[target_id].each(function(){
			if(!this.totreat) return ;
			//save data before submit
			multilang_save_lang(this,this.form.form_lang);
			//build the string value
			multilang_field_set_background(this,lang);
			if(container && target){
				multilang_mark_empty_langs(container,target);
			}
			var def_value = this.field_lang[multilang_def_lang];
			if(!this.multi)
				this.value = (def_value==undefined?"":def_value);
			else {
				var value="",count=0;
				$.each(this.field_lang,function(name){
					if((name != 'full') && (this.length > 0)){
						//save default lang value and other lang values if different from the default one
						if(name == multilang_def_lang){
							value = "["+name+"]"+this+value;
							count++;
						}else if(this!=def_value) {
							value += "["+name+"]"+this;
							count++;
						}
					}
				});
				this.value = (count > 1 ? "<multi>"+value+"</multi>":value.replace(/^\[[a-z_]+\]/,''));
			}
			// Add the title number to the final value
			if(multilang_is_title(this) && ($('#'+this.id+'_numero').val() != ''))
				this.value= $('#'+this.id+'_numero').val().replace(/\.|\s+/,'') + ". " + this.value;
		});
		return true;
	}
}

/**
 * Défini si un id de champ correspond a un champ "numerotable"
 *
 * @param id chaine correspondant a l'id du champ
 */
function multilang_is_title(el) {
	return (el.id=='titre' || el.id=='champ_titre' || (el.id=='nom_site' && ($(el).parents('#configurer-accueil,.formulaire_configurer_identite,.formulaire_editer_auteur').size() < 1)) || el.id.match(/^titre_document[0-9]+/)!=null || el.name.match(/^content_[a-z0-9_]+_titre/)!=null || el.name.match(/^content_[a-z0-9_-]+nom_/)!=null)
}

/**
 * Initialisation du script sur un champ
 *
 * Récupère les données suivantes
 * - Le contenu de l'élément du champ
 * - Le booléen (true/false) multi (est ce un champs déjà multi)
 * - Les différentes chaines de langue dans un objet :
 * {fr="texte fr",en="texte en",full="<multi>[fr]texte fr[en]texte en</multi>"}
 * Si le champ est déjà initialisé, fait un simple return
 *
 * @param el Le champ a initialiser
 * @param lang La langue
 *
 */
function multilang_init_field(el,lang,force) {
	if(el.field_lang && !force) return;
	var langs;
	
	// On enlève les espaces, retours à la ligne et tabulations de début et de fin de chaine
	el.value.replace(/(?:^\s+|\s+$)/g, "");
	
	// Modif Yffic : ne pas considerer comme multi les champs qui contiennent du texte
	// en dehors des multi sauf un numero (\d+\.\s+)
	var m = el.value.match(/(\d+\.\s+)?<multi>((?:.|\n|\s)*?)<\/multi>(.|\n*)/);
	el.field_lang = {};
	el.field_pre_lang = ""; //this is the 01. part of the string, the will be put outside the <multi>
	el.titre_el = $("#titre_"+el.id);
	
	// Init the elements to treat
	if(m!=null) {
		if( m.index || (m[3]!=undefined && m[3]!="")){
			$(el).addClass('multi_lang_broken');
			el.totreat=false;
		}
		else
			el.totreat=true;
		if(el.totreat) {
			el.field_pre_lang = m[1] || "";
			// Suppress point and spaces
			el.field_pre_lang = el.field_pre_lang.replace(/\.|\s+/,'') ;
			el.multi = true;
			multilang_match_multi.lastIndex=0;
			el.field_lang['full'] = el.value;
			while((langs=multilang_match_multi.exec(m[2]))!=null) {
				var text = langs[2].match(/^(\d+\.\s+)((?:.|\n|\s)*)/), value;
				// Suppression du numero uniquement pour les titres
				if(multilang_is_title(el) && text!=null) {
					value = text[2];
					// Suppress point and spaces
					el.field_pre_lang = text[1].replace(/\.|\s+/,'') || "";
				} else {
					value = langs[2];
				}
				el.field_lang[langs[1]||multilang_def_lang] = value;
			}
		}
	} else {
		el.multi = false;
		el.totreat=true;

		// Suppression du numero uniquement pour les titres
		if(multilang_is_title(el)) {
			var n = el.value.match(/(\d+\.\s+)?(.*)/);
			el.field_pre_lang = n[1] || "";
			el.field_pre_lang = el.field_pre_lang.replace(/\.|\s+/,'') ;
			el.field_lang[multilang_def_lang] = n[2];
		} else {
			el.field_lang[multilang_def_lang] = el.value;
		}
	}

	// Put the current lang string only in the field
	multilang_set_lang(el,lang);

	/**
	 * Si le champ est un titre, on ajoute un champ facultatif "numéro" au formulaire permettant
	 * de traiter le cas où l'on utilise les numéros pour trier les objets
	 * Ajout d'Yffic le 30/03/2010
	 */
	if(!force && multilang_is_title(el)){
		numid=el.id+'_numero';
		/**
		 * Cas des crayons qui n'ont pas toujours de formalisme en ul > li
		 */
		if(el.name.match(/^content_[a-z0-9_]+_titre/)){
			if($(el).parent().is('li')){
				$(el).parent()
					.before('<li class="editer_'+numid+'"><label for="titre_numero">'+multilang_lang.numero+'</label><input id="'+numid+'" name="titre_numero" type="text" value="'+el.field_pre_lang+'" size="4" class="text nomulti" /></li>');
			}else{
				$(el)
				.before('<div class="editer_titre_numero"><label for="titre_numero">'+multilang_lang.numero+'</label><input id="'+numid+'" name="titre_numero" type="text" value="'+el.field_pre_lang+'" size="4" class="text nomulti" /><br /><br /></div>');
			}
		}else{
			$(el).parent()
				.before('<li class="editer_'+numid+'"><label for="titre_numero">'+multilang_lang.numero+'</label><input id="'+numid+'" name="titre_numero" type="text" value="'+el.field_pre_lang+'" size="4" class="text nomulti" /></li>');
		}
		$('#'+numid).totreat = false;
	}
}

/**
 * Action au click sur une langue du menu de langue
 *
 * @param el Le lien cliqué sur le menu
 * @param container Le conteneur du formulaire
 * @param target Le formulaire lui même
 *
 */
function multilang_change_lang(el,container,target) {
	var added_lang="";
	var target_id = multilang_init_target_id(target);
	var lang = el.innerHTML;

	lang = lang.slice(1,-1);

	if(target[0].isfull){
		// Maj du menu de langues avant multilang_init_field
		multilang_forms_fields[target_id].each(function(){
			var me = $(this);
			if(me.parents(multilang_root_opt).size()>0){
		        if(me.is(multilang_fields_selector_opt)){
		        	multilang_init_field(this,lang,true);
		        }
		    }else{
		    	multilang_init_field(this,lang,true);
		    }
		});
		target[0].isfull = false;
	}else{
		//store the fields inputs for later use (usefull for select)
		//save the current values
		multilang_forms_fields[target_id].each(function(){
			multilang_save_lang(this,this.form.form_lang);
		});
		// Maj du menu de langues apres multilang_save_lang
	}

	//change current lang
	target.each(function(){this.form_lang = lang});

	//reinit fields to current lang
	multilang_forms_fields[target_id].each(function(){
		multilang_set_lang(this,lang);
	});
	
	multilang_mark_empty_langs(container,target);
}

/**
 * Marquer dans le menu des langues, celles pour lesquelles
 * au moins un champ multi du formulaire n'est pas renseigne
 *
 * @param container Le conteneur du formulaire
 *
 */
function multilang_mark_empty_langs(container,target) {
	var langs_empty = [];
	var target_id = multilang_init_target_id(target);

	multilang_forms_fields[target_id].each(function(){
		var field_langs = [];
		// Mise sous forme de tableau
		if(typeof(this.field_lang) != 'undefined'){
			$.each(this.field_lang,function(name,value){
				if(value){
					field_langs.push(name);
				}
			});
		}
		// Trouver les elements non communs entre le tableau des langues availables et pour chaque champ,
		// celui des langues renseignees, si ce champ est multi
		// Si la langue d'origine n'est pas remplie (champ texte par exemple, on ne considère donc pas empty)
		if(this.multi) {
			// Comparaison des tableaux
			$.each(multilang_avail_langs,function(i,name){
				if ((jQuery.inArray(name, field_langs) == -1) && (jQuery.inArray(name, langs_empty) == -1) && (jQuery.inArray(multilang_def_lang, field_langs) != -1))
					langs_empty.push(name);
			});
		}else{
			// Comparaison des tableaux
			$.each(multilang_avail_langs,function(i,name){
				if ((jQuery.inArray(name, field_langs) == -1) && (jQuery.inArray(name, langs_empty) == -1) && (jQuery.inArray(multilang_def_lang, field_langs) != -1))
					langs_empty.push(name);
			});
		}
	});

	// On indique dans le menu de langue, celles qui ont au moins un champ non renseigne
	if(container!='') {
		$.each(multilang_avail_langs,function(i,name){
			if((jQuery.inArray(name, langs_empty) == -1)){
				var title = 'multilang_lang.title_lien_multi_'+name;
				container.find('a[class~='+name+']').removeClass('empty').attr('title',eval(title));
			}else{
				var title = 'multilang_lang.title_lien_multi_sans_'+name;
				container.find('a[class~='+name+']').addClass('empty').attr('title',eval(title));
			}
		});
	}
}

/**
 * Action au changement de la langue
 * Lorsque l'on clique sur une le menu de langue
 * On affiche pour le champ "el" son contenu dans la langue "lang"
 *
 * @param el Un champ du formulaire (input ou textarea)
 * @param lang La langue correspondante souhaitée
 * @return
 */
function multilang_set_lang(el,lang) {

	if(!el.totreat) return;

	//if current lang is not setted use default lang value
	if(el.field_lang[lang]==undefined) {
		el.field_lang[lang] = el.field_lang[multilang_def_lang];
	}

	el.value = (el.field_lang[lang] == undefined ? "" : el.field_lang[lang]);

	el.titre_el.html(el.value);

	multilang_field_set_background(el,lang);
}

/**
 * Change la class multi_lang_* d'un champ pour indiquer la présence ou non de multis
 * - multi_lang_(lang) si une langue particulière est disponible
 * - multi_lang_no_multi si pas de multis pour le champ
 *
 * @param el
 * @param lang
 * @return
 */
function multilang_field_set_background(el,lang) {
	if(lang != 'full'){
		if(el.totreat){
			$(el).removeAttr('readonly').removeClass('multilang_readonly');
			if(typeof($(el).attr('class')) != 'undefined'){
				$($(el).attr('class').split(' ')).each(function(){
					var m = this.match(/^multi_lang_*/);
					if(m!=null)
						$(el).removeClass(m.input).addClass('multi_lang_'+lang);
				});
			}
			$(el).addClass('multi_lang_'+(el.multi?lang:'no_multi'));
		}
		else{
			if(typeof($(el).attr('class')) != 'undefined'){
				$($(el).attr('class').split(' ')).each(function(){
					var m = this.match(/^multi_lang_*/);
					if(m!=null)
						$(el).removeClass(m.input);
				});
			}
			$(el).css({"background":"url("+multilang_dir_plugin+"/images/multi_forbidden.png) no-repeat right top"});
		}
	}else{
		$(el).attr('readonly','readonly').addClass('multilang_readonly');
		if(typeof($(el).attr('class')) != 'undefined'){
			$($(el).attr('class').split(' ')).each(function(){
				var m = this.match(/^multi_lang_*/);
				if(m!=null)
					$(el).removeClass(m.input);
			});
		}
	}
}

/**
 * Garde en mémoire dans l'array de langues du champs la valeur de la langue
 * sélectionnée
 *
 * @param el Le champ du formulaire concerné
 * @param lang La langue actuelle
 * @return
 */
function multilang_save_lang(el,lang) {

	if(!el.totreat) return ;

	// Suppression du numero uniquement pour les titres
	if(multilang_is_title(el)) {
		var m = el.value.match(/^(\d+\.\s+)((?:.|\n|\s)*)/);
		if(m!=null) {
			// Suppress point and spaces
			el.field_pre_lang = m[1].replace(/\.|\s+/,'');
			el.value = m[2];
		}
	}

	//if the lang value is equal to the def lang do nothing
	//else save value but if the field is not empty, delete lang value
	if(el.field_lang[multilang_def_lang]!= el.value) {
		if(!el.value) {
			delete el.field_lang[lang];
			return;
		}
		el.multi = true;
		el.field_lang[lang] = el.value;
	}else{
		el.field_lang[lang] = el.field_lang[multilang_def_lang];
		$.each(el.field_lang,function(index, value){
			if((index != multilang_def_lang) && (value == el.field_lang[multilang_def_lang])){
				delete el.field_lang[index];
			}
		});
	}
}

//This func receives the form that is going to be submitted
function multilang_multi_submit(params) {
	if(multilang_avail_langs.length<=1) return;
	var form = $(this);
	//remove the current form from the list of forms
	multilang_forms.not(this);
	//remove the current menu lang container from the list
	multilang_containers.not("div.menu_multilang",$(this));
	//build the input values
	multilang_multi_recover('','',form,'submit');
	//save back the params
	if(params) $.extend(params,$(form).formToArray(false));
}

/**
 * On attache nos évènements pour la validation du formulaire
 * - sur le submit pour tous les formulaires
 * - sur l'évènement 'form-pre-serialize' d'ajaxForms au cas où nous sommes dans
 * un formulaire ajax
 *
 * @return
 */
function multilang_attach_submit() {
	if($(this).parents('.ajax').size() && $(this).find('input[name=var_ajax]')){
		$(this).bind('form-pre-serialize',multilang_multi_submit);
	}else if($(this).is('.formulaire_crayon')){
		cQuery(this).bind('form-pre-serialize',function(){multilang_multi_submit.apply(this);});
	}else{
		var oldsubmit = this.onsubmit;
		this.onsubmit = "";
		if(oldsubmit && oldsubmit != ""){
			$(this).submit(function(){multilang_multi_submit.apply(this);return oldsubmit.apply(this);});
		}
		else if(oldsubmit != "")
			$(this).submit(multilang_multi_submit);
	}
}

(function($) {
	$.extend($.fn, {
		in_set: function(set) {
			var elements = this.get();
			var result = $.grep(set,function(i){
				var found = false;
				$.each(elements,function(){
					if(this==i) found=true;
				})
				return found;
			});
			return jQuery(result);
		}
	});
})(jQuery);