/// <reference path="jquery-1.2.6-vsdoc.js" />
(function($) {

	$.fn.annotateImage = function(options) {
		///	<summary>
		///		Creates annotations on the given image.
		///     Images are loaded from the "getUrl" propety passed into the options.
		///	</summary>
		var opts = $.extend({}, $.fn.annotateImage.defaults, options);
		var image = this;

		this.image = this;
		this.mode = 'view';
		
		// Add a reverse reference to the DOM object
		var $annotateImage = $(this);
		$annotateImage.data("annotateImage", this);

		// Assign defaults
		this.getUrl = opts.getUrl;
		this.editUrl = opts.editUrl;
		this.listUrl = opts.listUrl;
		this.listtarget = opts.listtarget;
		this.addButton = opts.addButton;
		this.id_document = opts.id_document;
		this.echelle = opts.echelle;
		this.editable = opts.editable;
		this.useAjax = opts.useAjax;
		this.notes = opts.notes;

		// Add the canvas
		this.canvas = $('<div class="image-annotate-canvas"><div class="image-annotate-view"></div><div class="image-annotate-edit"><div class="image-annotate-edit-area"><div class="inner"></div></div></div></div>');
		this.canvas.children('.image-annotate-edit').hide();
		this.canvas.children('.image-annotate-view').hide();
		this.image.after(this.canvas);

		// Give the canvas and the container their size and background
		this.canvas.height(this.height());
		this.canvas.width(this.width());
		this.canvas.css('background-image', 'url("' + this.attr('src') + '")');
		this.canvas.children('.image-annotate-view, .image-annotate-edit').height(this.height());
		this.canvas.children('.image-annotate-view, .image-annotate-edit').width(this.width());

		// Add the behavior: hide/show the notes when hovering the picture
		this.canvas.hover(function() {
			if ($(this).children('.image-annotate-edit').css('display') == 'none') {
				$(this).children('.image-annotate-view').show();
			}
		}, function() {
			$(this).children('.image-annotate-view').hide();
		});

		this.canvas.children('.image-annotate-view').hover(function() {
			$(this).show();
		}, function() {
			$(this).hide();
		});

		// load the notes
		if (this.useAjax) {
			$.fn.annotateImage.ajaxLoad(this);
		} else {
			$.fn.annotateImage.load(this);
		}

		// Add the "Add a note" button
		if (this.editable) {
			if (!this.addButton) {
				this.button = $('<a class="image-annotate-add" id="image-annotate-add" href="#">Ajouter une note</a>');
				this.canvas.after(this.button);
			}else{
				this.button = $(this.addButton);
			}
			this.button.click(function() {
				$.fn.annotateImage.add(image);
				return false;
			});
		}

		if(this.listtarget){
			if(($(this.listtarget).html().length == 0) && ($(this.listtarget).not(':hidden'))){
				$(this.listtarget).addClass('annotate-hide').hide();
			}
		}
		
		// Hide the original
		this.hide();

		return this;
	};
	
	/// This function breaks the chain, but returns
	/// the annotateImage if it has been attached to the object.
	$.fn.getannotateImage = function(){
		return this.data("annotateImage");
	}

	/**
	* Plugin Defaults
	**/
	$.fn.annotateImage.defaults = {
		getUrl: '',
		editUrl: '',
		listUrl: '',
		listtarget:null,
		addButton: '',
		id_document: '0',
		echelle: '1',
		editable: true,
		useAjax: true,
		notes: new Array()
	};

	$.fn.annotateImage.clear = function(image) {
		///	<summary>
		///		Clears all existing annotations from the image.
		///	</summary>
		image.canvas.children('.image-annotate-view').children('.image-annotate-area').remove();
		image.notes = new Array();
	};

	$.fn.annotateImage.ajaxLoad = function(image) {
		///	<summary>
		///		Loads the annotations from the "getUrl" property passed in on the
		///     options object.
		///	</summary>
		$.getJSON(image.getUrl, function(data) {
			if (data)
				image.notes = data;
			$.fn.annotateImage.load(image);
		});
	};

	$.fn.annotateImage.ajaxDestroy = function(image){
		///	<summary>
		///		Loads an html content from the "listUrl" inside an jQuery selector target "listtarget"
		///	</summary>
		if(image.listUrl && image.listtarget){
			$.get(image.listUrl, function(data) {
				image.listtarget.html(data);
				if($(image.listtarget).hasClass('annotate-hide')){
					$(image.listtarget).removeClass('annotate-hide').slideDown();
				}else if((data.length == 0) && ($(image.listtarget).not(':hidden'))){
					$(image.listtarget).addClass('annotate-hide').slideUp();
				}
			});
		}
	}
	
	$.fn.annotateImage.load = function(image) {
		///	<summary>
		///		Loads the annotations from the notes property passed in on the
		///     options object.
		///	</summary>
		for (var i = 0; i < image.notes.length; i++) {
			image.notes[image.notes[i]] = new $.fn.annotateView(image, image.notes[i]);
		}
	};

	$.fn.annotateImage.add = function(image) {
		///	<summary>
		///		Adds a note to the image.
		///	</summary>
		if (image.mode == 'view') {
			image.mode = 'edit';

			// Create/prepare the editable note elements
			var editable = new $.fn.annotateEdit(image);

			$.fn.annotateImage.saveForm(editable, image);
			$.fn.annotateImage.cancelForm(editable, image);
		}
	};

	$.fn.annotateImage.saveForm = function(editable, image, note) {
		///	<summary>
		///		valider le formulaire de la legende
		///	</summary>
		var ok = $('#image-annotate-edit-form form input[name=valider]');

		ok.click(function() {
			var form = $('#image-annotate-edit-form form');
			var text = $('#image-annotate-edit-form form textarea[name=texte]').val();
			$.fn.annotateImage.updatePosition(form, editable);
			// destroy est appelé depuis le formulaire CVT après son rechargement ajax
			// on recharge aussi les notes au retour du formulaire
		});
	};

	$.fn.annotateImage.cancelForm = function(editable, image) {
		///	<summary>
		///		annuler le formulaire de la legende
		///	</summary>
		var cancel = $('#image-annotate-edit-form form input[name=annuler]');
		cancel.click(function() {
			editable.destroy();
			image.mode = 'view';
		});
	};
	
	// recuperer le formulaire en ajax synchrone
	$.fn.annotateGetForm = function(editurl,id,id_document) {
		var ret;
		$.ajax({
			url: editurl,
			data:{id_legende: id,id_document: id_document},
			dataType: "html",
			async: false,
			success:function(c) {
				ret = c;
			}
		});
		return ret;
	};
	
	$.fn.annotateEdit = function(image, note) {
		///	<summary>
		///		Defines an editable annotation area.
		///	</summary>
		this.image = image;

		// Add a reverse reference to the DOM object
		var $annotateEdit = $(image);
		$annotateEdit.data("annotateEdit", this);

		if (note) {
			this.note = note;
		} else {
			var newNote = new Object();
			newNote.id = "new";
			newNote.top = parseInt((this.image.height()/5)/this.image.echelle);
			newNote.left = parseInt((this.image.width()/5)/this.image.echelle);
			newNote.width = parseInt((this.image.width()/5)/this.image.echelle);
			newNote.height = parseInt((this.image.height()/5)/this.image.echelle);
			newNote.text = "";
			newNote.id_document = this.image.id_document;
			this.note = newNote;
		}
		
		// Set area
		var area = image.canvas.children('.image-annotate-edit').children('.image-annotate-edit-area');
		this.area = area;
		this.area.css('height', parseInt(this.note.height*this.image.echelle) + 'px');
		this.area.css('width', parseInt(this.note.width*this.image.echelle) + 'px');
		this.area.css('left', parseInt(this.note.left*this.image.echelle) + 'px');
		this.area.css('top', parseInt(this.note.top*this.image.echelle) + 'px');

		// Show the edition canvas and hide the view canvas
		image.canvas.children('.image-annotate-view').hide();
		image.canvas.children('.image-annotate-edit').show();
		// on indique que ça charge
		this.area.addClass('loading').css('opacity',0.8);

		// Add the note (which we'll load with the form afterwards)
		//var form = $('<div id="image-annotate-edit-form"><form><textarea id="image-annotate-text" name="text" rows="3" cols="30">' + this.note.text + '</textarea></form></div>');
		
		var form = $("'<div id=\"image-annotate-edit-form\"></div>'");
		this.form = form;

		this.form.css('left', this.area.offset().left + 'px');
		this.form.css('top', (parseInt(this.area.offset().top) + parseInt(this.area.height()) + 2) + 'px');

		// Set the area as a draggable/resizable element contained in the image canvas.
		// Would be better to use the containment option for resizable but buggy
		area.resizable({
			handles: 'all',
			resize: function(e, ui) {
				if (parseInt(area.position().top) + parseInt(area.height()) + 2 > parseInt(image.canvas.height())) {
					area.height(parseInt(image.canvas.height()) - parseInt(area.position().top) - 2);
				}
				if (parseInt(area.position().left) + parseInt(area.width()) + 2 > parseInt(image.canvas.width())) {
					area.width(parseInt(image.canvas.width()) - parseInt(area.position().left) - 2);
				}
				if (parseInt(area.position().top) < 0) {
					area.height(parseInt(image.canvas.height())).css('top', 0);
				}
				if (parseInt(area.position().left) < 0) {
					area.width(parseInt(image.canvas.width())).css('left', 0);
				}
				form.css('left', area.offset().left + 'px');
				form.css('top', (parseInt(area.offset().top) + parseInt(area.height()) + 2) + 'px');
			},
			stop: function(e, ui) {
				form.css('left', area.offset().left + 'px');
				form.css('top', (parseInt(area.offset().top) + parseInt(area.height()) + 2) + 'px');
			}
		})
		.draggable({
			containment: image.canvas,
			drag: function(e, ui) {
				form.css('left', ui.offset.left + 'px');
				form.css('top', (parseInt(ui.offset.top) + parseInt(area.height()) + 2) + 'px');
			},
			stop: function(e, ui) {
				form.css('left', ui.offset.left + 'px');
				form.css('top', (parseInt(ui.offset.top) + parseInt(area.height()) + 2) + 'px');
			}
		});
		// on aplique formulaire_dyn_ajax() de ajaxcallback.js pour activer l'ajax du CVT
		this.form.append($.fn.annotateGetForm(this.image.editUrl, this.note.id, this.note.id_document))
			.children('div.ajax').formulaire_dyn_ajax();
		$('body').append(this.form);
		// chargement terminé on vire la classe css et l'opacité
		this.area.removeClass('loading').css('opacity',1);
		return this;
	};

	/// This function breaks the chain, but returns
	/// the annotateEdit if it has been attached to the object.
	$.fn.getannotateEdit = function(){
		return this.data("annotateEdit");
	}

	$.fn.annotateEdit.prototype.destroy = function() {
		///	<summary>
		///		Destroys an editable annotation area.
		///	</summary>
		this.image.canvas.children('.image-annotate-edit').hide();
		this.area.resizable('destroy');
		this.area.draggable('destroy');
		this.area.css('height', '');
		this.area.css('width', '');
		this.area.css('left', '');
		this.area.css('top', '');
		this.form.remove();
		// destroy est appele au retour du formulaire, donc si creation ou suppression
		// on efface donc toutes les notes et on recharge
		$.fn.annotateImage.clear(this.image);
		$.fn.annotateImage.ajaxLoad(this.image);
		$.fn.annotateImage.ajaxDestroy(this.image);
	}

	$.fn.annotateView = function(image, note) {
		///	<summary>
		///		Defines a annotation area.
		///	</summary>
		this.image = image;

		this.note = note;
		this.note.id_document = this.image.id_document;

		this.editable = (note.editable && image.editable);

		// Add the area
		this.area = $('<div class="image-annotate-area' + (this.editable ? ' image-annotate-area-editable' : '') + '"><div class="inner"></div></div>');
		image.canvas.children('.image-annotate-view').prepend(this.area);

		// Add the note
		this.form = $('<div class="image-annotate-note"><div class="note-text">' + note.text + '</div></div>');
		this.form.hide();
		this.area.append(this.form);
		this.form.children('span.actions').hide();

		// Set the position and size of the note
		this.setPosition();

		// Add the behavior: hide/display the note when hovering the area
		var annotation = this;
		this.area.hover(function() {
			annotation.show();
		}, function() {
			annotation.hide();
		});

		// Edit a note feature
		if (this.editable) {
			var form = this;
			this.area.children('.inner').click(function() {
				form.edit();
			});
		}
	};

	$.fn.annotateView.prototype.setPosition = function() {
		///	<summary>
		///		Sets the position of an annotation.
		///	</summary>
		this.area.children('.inner').height((parseInt((this.note.height*this.image.echelle)) - 2) + 'px');
		this.area.children('.inner').width((parseInt((this.note.width*this.image.echelle)) - 2) + 'px');
		this.area.css('left', (parseInt(this.note.left*this.image.echelle)) + 'px');
		this.area.css('top', (parseInt(this.note.top*this.image.echelle)) + 'px');
		this.form.css('left', '0');
		this.form.css('top', '100%');
	};

	$.fn.annotateView.prototype.show = function() {
		///	<summary>
		///		Highlights the annotation
		///	</summary>
		this.form.fadeIn(250);
		if (!this.editable) {
			this.area.addClass('image-annotate-area-hover');
		} else {
			this.area.addClass('image-annotate-area-editable-hover');
		}
	};

	$.fn.annotateView.prototype.hide = function() {
		///	<summary>
		///		Removes the highlight from the annotation.
		///	</summary>
		this.form.fadeOut(250);
		this.area.removeClass('image-annotate-area-hover');
		this.area.removeClass('image-annotate-area-editable-hover');
	};

	$.fn.annotateView.prototype.destroy = function() {
		///	<summary>
		///		Destroys the annotation.
		///	</summary>
		this.area.remove();
		this.form.remove();
	}

	$.fn.annotateView.prototype.edit = function() {
		///	<summary>
		///		Edits the annotation.
		///	</summary>
		if (this.image.mode == 'view') {
			this.image.mode = 'edit';
			var annotation = this;

			// Create/prepare the editable note elements
			var editable = new $.fn.annotateEdit(this.image, this.note);

			$.fn.annotateImage.saveForm(editable, this.image, annotation);
			$.fn.annotateImage.cancelForm(editable, this.image);

			// Add the delete button
			var del = $('#image-annotate-edit-form form input[name=effacer]');
			del.click(function() {
				var form = $('#image-annotate-edit-form form');

				$.fn.annotateImage.updatePosition(form, editable)
				
				annotation.image.mode = 'view';
				// destroy est appelé depuis le formulaire CVT après son rechargement ajax
				//editable.destroy();
				annotation.destroy();
			});
		}
	};

	$.fn.annotateImage.updatePosition = function(form, editable) {
		///	<summary>
		///		modifier les coordonnees de l'annotation dans les champs du formulaire
		///	</summary>
		form.children('input[name=height]').val(parseInt(editable.area.height()/editable.image.echelle));
		form.children('input[name=width]').val(parseInt(editable.area.width()/editable.image.echelle));
		form.children('input[name=top]').val(parseInt(editable.area.position().top/editable.image.echelle));
		form.children('input[name=left]').val(parseInt(editable.area.position().left/editable.image.echelle));
	}

	$.fn.annotateView.prototype.resetPosition = function(editable, text) {
		///	<summary>
		///		Sets the position of an annotation.
		///	</summary>
		this.form.html(text);
		this.form.hide();

		// Resize
		this.area.children('div').height(editable.area.height() + 'px');
		this.area.children('div').width((editable.area.width() - 2) + 'px');
		this.area.css('left', (editable.area.position().left) + 'px');
		this.area.css('top', (editable.area.position().top) + 'px');
		this.form.css('left', (editable.area.position().left) + 'px');
		this.form.css('top', (parseInt(editable.area.position().top) + parseInt(editable.area.height()) + 7) + 'px');

		// Save new position to note
		this.note.top = parseInt(editable.area.position().top/editable.image.echelle);
		this.note.left = parseInt(editable.area.position().left/editable.image.echelle);
		this.note.height = parseInt(editable.area.height()/editable.image.echelle);
		this.note.width = parseInt(editable.area.width()/editable.image.echelle);
		this.note.text = text;
		this.note.id = editable.note.id;
		this.editable = true;
	};

})(jQuery);