/*
 * multilang_mot_cles
 *
 * Copyright (c) 2006 Renato Formato (renatoformato@virgilio.it)
 * Licensed under the GPL License:
 *   http://www.gnu.org/licenses/gpl.html
 *
 */
 
var multilang_containers={},forms_fields={},multilang_forms,multilang_menu_lang;
var match_multi = /(?:\[([a-z_]+)\]|^[\s\n]*)((?:.|\n)*?)(?=\[[a-z_]+\]|$)/ig;
var multilang_css_link,multilang_css_cur_link={},multilang_root,multilang_fields_selector;
multilang_css_link = {"cursor":"pointer","margin":"2px 5px","float":"left"};
$.extend(multilang_css_cur_link,multilang_css_link);
$.extend(multilang_css_cur_link,{fontWeight:"bold"});

function multilang_init_lang(page,root,main_menu,forms,fields) {
	//Detect if we're on the right page and if multilinguism is activated. If not return.
	if(window.location.search.indexOf("exec=mots_edit")==-1 || multilang_avail_langs.length<=1) return;
	//set the root element of all processing
	root = root || document;
	multilang_root = $(root);
	//set the main menu element
	multilang_containers = main_menu ? $(main_menu,multilang_root) : $("empty");
	//create menu lang template 
	multilang_menu_lang =$("<div>");
	$.each(multilang_avail_langs,function() {
		multilang_menu_lang.append($("<a>").html("["+this+"]").css(this==multilang_def_lang?multilang_css_cur_link:multilang_css_link)[0]);
	});
	//store all the fields forms
	forms = forms || "form";
	multilang_forms = $(forms,multilang_root).submit(forms_multi_submit);
	//create menu lang for the global form
	if(multilang_containers.size()) forms_make_menu_lang(multilang_containers);
	//init fields
	multilang_fields_selector = fields;
	forms_init_multi({"forms_selector":forms,"fields_selector":fields});
}

function forms_make_menu_lang(container,target) {
	target = target || multilang_forms;
	$(multilang_menu_lang).clone().find("a").click(function() {forms_change_lang(this,container,target)}).end().
	append("<div style='clear:left'></div>").appendTo(container);
}

function forms_change_lang(el,container,target) {
	var lang = el.innerHTML;
	container = container || multilang_containers;
	//update lang menu with current selection
	container.find("a").each(function(){
		$(this).css("fontWeight",lang==this.innerHTML?"bold":"normal");
	}).end();
	lang = lang.slice(1,-1);
	//store the fields inputs for later use (usefull for select)
	var target_name = target!=multilang_forms?target[0].hash.value:"undefined";
	if(!forms_fields[target_name]) forms_fields[target_name] = $(multilang_fields_selector,target);
	//save the current values
	forms_fields[target_name].each(function(){
		forms_save_lang(this,this.form.form_lang);
	});
	//change current lang	
	target.each(function(){this.form_lang = lang});
	//reinit fields to current lang
	forms_fields[target_name].each(function(){forms_set_lang(this,lang)});
}

function forms_init_multi(options) {
	var target = options.target;
	var forms = options.forms_selector;
	//Update the list of form if this is an update
	if(target) multilang_forms.add($(forms,target).get());
	forms_fields = {};
	forms_fields["undefined"] = $(multilang_fields_selector,multilang_forms);
	//store the fields of the target if any
	var init_forms = target?$(forms,target):multilang_forms;
	//init the value of the field to current lang
	//add a container for the language menu before the form
	init_forms.each(function() { 
		this.form_lang = multilang_def_lang;
		$(this).before("<div>"); 
	}); 
	$(multilang_fields_selector,init_forms).each(function(){
		forms_init_field(this,this.form.form_lang);
	});
	//create menu for each form. The menu is just before the form
	init_forms.prev().empty().each(function() {
		//store all form containers to allow menu lang update on each container
		//when it is triggered by global menu
		multilang_containers.add(this);
		forms_make_menu_lang($(this),$(this).next());
	}).end();
}

function forms_init_field(el,lang) {
	//Retrieves the following data 
	//1)the title element of the field 
	//2)boolean multi = the fields has a multi value
	//3)various lang string
	//if already inited just return
	if(el.field_lang) return;
	var langs;
	var m = el.value.match(/<multi>((?:.|\n)*?)<\/multi>/);
	el.field_lang = {};
	el.titre_el = $("#titre_"+el.id);
	if(m!=null) {
		el.multi = true;
		match_multi.lastIndex=0;
		while((langs=match_multi.exec(m[1]))!=null) {
			el.field_lang[langs[1]||multilang_def_lang] = langs[2]; 
		}
		//Put the current lang string only in the field
		forms_set_lang(el,lang);
	} else {
		el.multi = false;
		el.field_lang[lang] = el.value; 		
	}
}

function forms_set_lang(el,lang) {
	//if current lang is not setted use default lang value
	if(el.field_lang[lang]==undefined)
			el.field_lang[lang] = el.field_lang[multilang_def_lang];
	el.value = el.field_lang[lang];
	el.titre_el.html(el.value);
}

function forms_save_lang(el,lang) {
	//if the lang value is equal to the def lang do nothing
	//else save value but if the field is not empty, delete lang value
	if(el.field_lang[multilang_def_lang]!=el.value) { 
		if(!el.value) {
			delete el.field_lang[lang];
			return;
		}
		el.multi = true;
		el.field_lang[lang] = el.value;
	}
}

//This func receives the form that is going to be submitted
function forms_multi_submit(params) {
	if(multilang_avail_langs.length<=1) return;
	var form = this;
	//remove the current form from the list of forms
	multilang_forms.not(this);
	//remove the current menu lang container from the list
	multilang_containers.not($(this).prev());
	//build the input values
	$(multilang_fields_selector,this).each(function(){
		//save data before submit
		forms_save_lang(this,form.form_lang || multilang_def_lang);
		//build the string value
		var def_value = this.field_lang[multilang_def_lang];
		if(!this.multi) this.value = def_value;
		else {
			var value="",count=0;
			$.each(this.field_lang,function(name){
				//save default lang value and other lang values if different from
				//the default one
				if(this!=def_value || name==multilang_def_lang) {
					value += "["+name+"]"+this;
					count++;
				}
			});
			this.value = count!=1?"<multi>"+value+"</multi>":value.replace(/^\[[a-z_]+\]/,'');
		} 
	});
	//save back the params
	if(params) $.extend(params,$(form).formToArray(false));
}
