jQuery(document).ready(function() {
	var bureau = jQuery("#bureau");
	var barre = jQuery("#barre");
	var aspirateur = jQuery("#aspirateur");

	//placement des fenetres
	var X =0;
	var Y =0;

	// taille du bureau
	var WIDTH = 0;
	var HEIGHT = 0;

	// conteneurs
	var fenetreActive = null;
	var tacheActive = null;

	// le zIndex de la fenetre en haut de la pile
	var zIndexTop = 80;
	var zIndexMin = 10;

	/**
	* la gestion du Drag&Drop est une adaptation du travail de Batiste Bieler
	*
	* Event delgation jQuery Drag & Drop and Resize Plug-in based on EasyDrag 1.4
	* Batiste Bieler : http://batiste.dosimple.ch/blog/
	*
	* Credits: http://fromvega.com
	*
	* Adaptation pour spip, edd@no-log.org
	*/

	var currentElement = null;
	var isDragMouseDown = false;
	var isResizeMouseDown = false;

	// global position records
	var lastMouseX;
	var lastMouseY;
	var lastElemTop;
	var lastElemLeft;
	var lastElemWidth;
	var lastElemHeight;

	// returns the mouse (cursor) current position
	var getMousePosition = function(e){
		if (e.pageX || e.pageY) {
			var posx = e.pageX;
			var posy = e.pageY;
		}
		else if (e.clientX || e.clientY) {
			var posx = e.clientX
			var posy = e.clientY
		}
		return { 'x': posx, 'y': posy };
	};
			
	var offset_snap_grip = function(grid, size) {
		var limit = grid / 2;
		if ((size % grid) > limit) {
			return grid-(size % grid);
		} else {
			return -size % grid;
		}
	};
	// updates the position of the current element being dragged
	var updatePosition = function(e, opts) {
		var pos = getMousePosition(e);

		var _left = (pos.x - lastMouseX) + lastElemLeft;
		var _top = (pos.y - lastMouseY) + lastElemTop;
		
		if(_top<0) _top=0;
		if(_left<0) _left=0;
	
		// gestion des bords
		if (_top +  lastElemHeight >  HEIGHT) _top = HEIGHT - lastElemHeight;
		if (_left +  lastElemWidth > WIDTH) _left = WIDTH - lastElemWidth;

		if($(currentElement).hasClass('snap-to-grid')) {
			_left = _left + offset_snap_grip(opts.grid, _left)
			_top = _top + offset_snap_grip(opts.grid, _top)
		}

		currentElement.style['top'] = _top + 'px';
		currentElement.style['left'] = _left + 'px';
	};

	var updateSize = function(e, opts) {
		var pos = getMousePosition(e);

		var _width = (pos.x - lastMouseX + lastElemWidth);
		var _height = (pos.y - lastMouseY + lastElemHeight);

		if(_width<120) _width=120;
		if(_height<100) _height=100;

		// gestion des bords
		if (_width +2+ lastElemLeft > WIDTH) _width = WIDTH - lastElemLeft-2;
		if (_height +3+ lastElemTop > HEIGHT) _height = HEIGHT - lastElemTop-3;

		if($(currentElement).hasClass('snap-to-grid')){
			_width = _width + offset_snap_grip(opts.grid, _width)
			_height = _height + offset_snap_grip(opts.grid, _height)
		}

		currentElement.style['width'] = _width + 'px';
		currentElement.style['height'] = _height + 'px';


		jQuery(currentElement).find("div.contenu").each(function() {
			this.style['width'] = (_width-15)+'px';
			this.style['height'] = (_height-56)+'px';
		});

	};

	// set children of an element as draggable and resizable
	jQuery.fn.drag = function(opts) {
		return this.each(function() {
			// when the mouse is moved while the mouse button is pressed
			$(this).mousemove(function(e) {
				if(isDragMouseDown) {
					updatePosition(e, opts);
					return false;
			        }
				else if(isResizeMouseDown) {
					updateSize(e, opts);
					return false;
				}
			});
    
			// when the mouse button is released
			$(this).mouseup(function(e) {
				isDragMouseDown = false;
				isResizeMouseDown = false;
			});

			// when an element receives a mouse press
			$(this).mousedown(function(e) {
				if(jQuery(e.target).parents('.fenetre-titre').hasClass('drag')) {
					e.target = jQuery(e.target).parents('.fenetre-titre');
				}
				if(jQuery(e.target).hasClass('drag')) {
					var el = jQuery(e.target).parents('.fenetre')[0];
					isDragMouseDown = true;
					currentElement = el;

					// retrieve positioning properties
					var pos = getMousePosition(e);
					lastMouseX = pos.x;
					lastMouseY = pos.y;

					lastElemLeft = el.offsetLeft;
					lastElemTop  = el.offsetTop;
					// + 2 pix pour les bordures ...
					lastElemWidth = jQuery(currentElement).width() +2;
					lastElemHeight = jQuery(currentElement).height() +3;
					updatePosition(e, opts);
				}
				if (jQuery(e.target).hasClass('resize')) {
					var el = jQuery(e.target).parents('.fenetre')[0];
					currentElement = el;
					isResizeMouseDown = true;
					
					var pos = getMousePosition(e);
					lastMouseX = pos.x;
					lastMouseY = pos.y;

					lastElemLeft = el.offsetLeft;
					lastElemTop  = el.offsetTop;

					lastElemWidth = jQuery(currentElement).width();
					lastElemHeight = jQuery(currentElement).height();

					updateSize(e, opts);
				}

				// un mouseDown sur une fenetre "passive" doit la rendre active
				if (el = jQuery(e.target).parents('.fenetre')[0]) {
					if (jQuery(el).hasClass("passive")) {
						fenetreActive = jQuery(el);
						fenetreActive.active();
					}
				}
				return false;
			});


		});
	};

	jQuery.fn.active = function() {
		return this.each(function() {
			jQuery("#bureau .fenetre").removeClass("active");
			jQuery("#bureau .fenetre").removeClass("passive");
			jQuery("#barre-taches div").removeClass("active");
			jQuery("#barre-taches div").removeClass("passive");

			var id_tache = "tache-" + jQuery(this).attr("id");
			tacheActive = jQuery("#" + id_tache);

			jQuery(this).addClass("active");
			jQuery("#bureau .fenetre").not(this).addClass("passive");

			jQuery("#bureau .fenetre").not(this).each(function(){
				var zIndexNew = jQuery(this).css("zIndex")- 5;
				if (zIndexNew < zIndexMin) zIndexNew = zIndexMin;
				jQuery(this).css({zIndex:zIndexNew});
			});

			jQuery(this).css({zIndex:zIndexTop});
			jQuery(tacheActive).addClass("active");
			jQuery("#barre-taches div").not(tacheActive).addClass("passive");
		});
	};

	// montre une fenetre en fonction de son id
	jQuery.fn.montreFenetre = function() {
		return this.each(function() {
			jQuery(this).click(function() {
				var id = "#" + jQuery(this).attr('href');
				jQuery(id).show('slow');
				fenetreActive = jQuery(id);
				fenetreActive.active();
				return false;
			});
		});
	};

	// minimise une fenetre
	jQuery.fn.minimiseFenetre = function() {
		return this.each(function() {
			jQuery(this).click(function() {
				var fenetre = jQuery(this).parent().parent();
				jQuery(fenetre).hide('slow');
				if (jQuery(fenetre).hasClass("active")) {
					var zIndexTmp = 0;
					var zIndexFen = null;
					jQuery("#bureau .fenetre").not(fenetre).each(function(){
						var tmp = jQuery(this).css("zIndex");
						if (tmp > zIndexTmp) {zIndexTmp = tmp; zIndexFen = jQuery(this);}
					});
					fenetreActive = zIndexFen;
					if (fenetreActive != null) fenetreActive.active();
				}
				return false;
			});
		});
	};

	jQuery.fn.fermeFenetre = function() {
		return this.each(function() {
			jQuery(this).click(function() {
				var fenetre = jQuery(this).parent().parent();
				var id_tache = "tache-" + jQuery(fenetre).attr("id");

				jQuery("#" + id_tache).remove();
				jQuery(fenetre).remove();

				// prendre la fenetre ayant le plus fort zIndex
				var zIndexTmp = 0;
				var zIndexFen = null;
				jQuery("#bureau .fenetre").each(function() {
					var tmp = jQuery(this).css("zIndex");
					if (tmp > zIndexTmp) {zIndexTmp = tmp; zIndexFen = jQuery(this);}
				});
				fenetreActive = zIndexFen;
				if (fenetreActive != null) fenetreActive.active();

				
				return false;
			});
		});
	};


	jQuery.fn.creerFenetreErreur = function(data) {
		return this.each(function() {
			var id =  generateGuid();
			var fenetre = 
			'<div class="fenetre" id="fenetre-'+id+'" style="width:400px;">'
			+'<div class="fenetre-titre ferme drag"><span>Erreur !</span></div>'
			+'<div class="contenu">'+data+'</div>'
			+'<div class="resize"></div>'
			+'</div>';


			jQuery(this).prepend(fenetre);
			fenetre = jQuery(".fenetre").eq(0);
			jQuery(fenetre).initFenetre();
			fenetreActive = fenetre;
			fenetreActive.active();
		});
	}

	jQuery.fn.ouvreFenetre = function() {
		return this.each(function() {
			jQuery(this).click(function() {
				// on passe par l'aspirateur pour éviter de flinguer le bureau ...
				AjaxSqueeze(jQuery(this).attr('href'),"aspirateur",function() {
					var fenetre = jQuery("#aspirateur .fenetre:last");
//					var aspirateur = jQuery("#aspirateur");

					if (jQuery(fenetre).length != 0) {
						bureau.prepend(fenetre);
						jQuery(fenetre).initFenetre();
						fenetreActive = fenetre;
						fenetreActive.active();
					}
					else {

						bureau.creerFenetreErreur(jQuery(aspirateur).html());
						aspirateur.html("");
					}
				});
				return false;
			});
		});
	};

	jQuery.fn.maximiseFenetre = function() {
		return this.each(function() {
			jQuery(this).click(function() {
				var fenetre = jQuery(this).parent().parent();
				var _width = WIDTH-3;
				var _height = HEIGHT-2;
				jQuery(fenetre).css('width',_width);
				jQuery(fenetre).css('height',_height);
				jQuery(fenetre).css('top',0);
				jQuery(fenetre).css('left',0);

				jQuery(fenetre).find("div.contenu").each(function() {
					this.style['width'] = (_width-15)+'px';
					this.style['height'] = (_height-56)+'px';
				});
			});
		});
	};
	jQuery.fn.boutonBarre = function() {
		return this.each(function() {
			jQuery(this).next().hide(); // on cache tous les éléments suivant un menu dans la barre
			jQuery(this).click(function() {	// un click sur un élément du menu provoque l'apparition de son menu

				jQuery("#barre div.toogle").not(jQuery(this)).each(function() {
					jQuery(this).next().slideUp('fast'); // on cache tous les éléments suivant un menu dans la barre
				});

				if (jQuery(this).next().children().html() != null) {
					jQuery(this).next().slideToggle("fast");
				}
				return false;
			});
		});
	};

	jQuery.fn.initFenetre = function() {
		return this.each(function() {

			// barre des taches
			var titre = jQuery(this).children(".fenetre-titre");
			var id = jQuery(this).attr("id");
			var id_tache = "tache-"  + id;
			var lien = "<div id='"+id_tache+"'><a class='montre' href='"
				+id
				+"'>"+jQuery(titre).children('span').html()+"</a></div>";
			jQuery("#barre-taches").append(lien);

			// placement des fenetres
			// 2 et 3 correspondent aux bordures ...
			var w = jQuery(this).width() +2;
			var h = jQuery(this).height() +3;

			// placement des fenetres 
			var top = Y - (h/2);
			if (top + h > HEIGHT) top = HEIGHT - h;
			this.style['top'] = top + 'px';
			var left = X -(w/2);
			if (left + w > WIDTH) left = WIDTH - w;
			this.style['left'] = left + 'px';

			X = X +50;
			Y = Y +50;

			// on la place au sommet de la pile
			this.style['zIndex'] = zIndexTop;

			// les liens ouvrant d'autres fenetres dans cette fenetre :
			jQuery(this).find("a.ouvre").each(function() {
				jQuery(this).ouvreFenetre();
			});

			// un double click sur le titre provoque un enroulement/deroulement
			jQuery(this).children("div.fenetre-titre").dblclick(function() {
				jQuery(this).next().slideToggle("slow");
			});

			jQuery(this).find("div.fenetre-menu").each(function() {
				jQuery(this).click(function() {
					jQuery(this).children('.contenu-menu').slideDown('fast');
					return false;
				});
				jQuery(this).find('.ferme-menu').each(function(){
					jQuery(this).click(function() {
						jQuery(this).parents('.contenu-menu').slideUp('fast');
						return false;
					});
				});
			});

			jQuery(this).find("a.minimise").each(function() {
				jQuery(this).minimiseFenetre();
			});
			jQuery(this).find("a.ferme").each(function() {
				jQuery(this).fermeFenetre();
			});
			jQuery(this).find("a.maximise").each(function() {
				jQuery(this).maximiseFenetre();
			});
		});
	};

	jQuery.fn.initWidthHeight = function() {
		return this.each(function() {
			WIDTH =  window.innerWidth;
			HEIGHT =  window.innerHeight-30;
			X =  WIDTH / 2;
			Y =  HEIGHT / 2;

			this.style['height'] = HEIGHT + 'px';
			this.style['width'] = WIDTH + 'px';

			return false;
		});
	};

	jQuery.fn.initBarre = function() {
		return this.each(function() {
			jQuery(this).find("div.toogle").each(function() {
				jQuery(this).boutonBarre();
			});
			jQuery(this).find("a.montre").each(function() {
				jQuery(this).montreFenetre();
			});
			jQuery(this).find("a.ouvre").each(function() {
				jQuery(this).ouvreFenetre();
			});
			// un click sur un lien dans un menu doit refermer le menu
			jQuery('#barre-menus div.menu .contenu div a').each(function() {
				jQuery(this).click(function() {
					jQuery(this).parents('.contenu').slideUp('slow');
				});
			});
		 	jQuery('.jclock').jclock();
		});
	};

	jQuery.fn.initBureau = function() {
		return this.each(function() {
			jQuery(this).initWidthHeight().drag({grid:20});
			jQuery(this).find(".fenetre").initFenetre();
			fenetreActive = jQuery(".fenetre").eq(0);
			jQuery(fenetreActive).active();
		});
	};

	// au demarage
	jQuery(function() {
		bureau.initBureau();
		barre.initBarre();
	});


	// a chaque fois que le dom change
	onAjaxLoad(function() {



		// une fenetre ne doit pas être plus grande que le bureau
		// on doit redimenssioner le contenu
		jQuery('.fenetre').find("div.contenu").each(function(){
			var hContenu = this.offsetHeight;
			var wContenu = this.offsetWidth;
			var parent = jQuery(this).parent()[0];
			// 15 : resize
			// 25 : titre
			// 2 : bordure fenetre
			// 7 : padding + bordure contenu
			// 10 : aucune idée d'ou viennent ces pixels !
			// = 59; ça tombe bien avec ff3, pour les autres je sais pas

			if (hContenu+59 > HEIGHT) {
				jQuery(this).css('height',HEIGHT-59);
				parent.style['top'] = 0+'px';
			}
			if (wContenu > WIDTH) {
				jQuery(this).css('width',WIDTH);
				parent.style['left'] = 0+'px';
			}
		});

		// minimise la fenetre au click sur l'icone minimse
		jQuery("a.ferme").each(function() {
			jQuery(this).fermeFenetre();
		});

		// remet en place une fenetre
		jQuery("a.montre").each(function() {
			jQuery(this).montreFenetre();
		});
		// minimise la fenetre au click sur l'icone minimse
		jQuery("a.minimise").each(function() {
			jQuery(this).minimiseFenetre();
		});

		jQuery("a.maximise").each(function() {
			jQuery(this).maximiseFenetre();
		});


	});
});

