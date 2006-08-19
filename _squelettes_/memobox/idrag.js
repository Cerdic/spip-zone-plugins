/*
 * interface drag - http://www.eyecon.ro/interface/
 *
 * Copyright (c) 2006 Stefan Petre
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 */

jQuery.drug = null;
jQuery.dragHelper = null;

jQuery.dragstart = function(e)
{
    if (jQuery.drug != null) {
		jQuery.dragstop(e);
    }
  jQuery.dragmoved=false;//flag to store if movement has happened
	elm = this.dE;
	elm.d.oScr = jQuery.getScroll();
	if (window.event) {
		window.event.cancelBubble = true;
		window.event.returnValue = false;
		elm.d.sx = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
		elm.d.sy = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
	} else {
		e.preventDefault();
		e.stopPropagation();
		elm.d.sx = e.clientX;
		elm.d.sy = e.clientY;
	}
	dEs = elm.style;
	if (jQuery.className.has(elm.parentNode, 'fxWrapper')) {
		p = jQuery(elm.parentNode);
		p.before(elm);
		dEs.top = p.css('top') + 'px';
		dEs.left = p.css('left') + 'px';
		dEs.position = p.css('position');
		dEs.marginTop = elm.om.t + 'px';
		dEs.marginRight = elm.om.r + 'px';
		dEs.marginBottom = elm.om.b + 'px';
		dEs.marginLeft = elm.om.l + 'px';
		p.remove();
	}
	elm.d.oC = jQuery.getPos(elm);
    elm.d.dX = elm.d.sx - elm.d.oC.x;
    elm.d.dY = elm.d.sy - elm.d.oC.y;
    elm.d.oD = jQuery.css(elm, 'display');
    elm.d.oP = jQuery.css(elm, 'position');
	elm.d.oM = jQuery.getMargins(elm);
	if (elm.d.oP != 'relative' && elm.d.oP != 'absolute') {
		//jQuery(elm).css('position', 'relative');
		dEs.position = 'relative';
	}
	
	//forget why I did this. For now affect the sortables
	dEs.marginTop = '0px';
	dEs.marginRight = '0px';
	dEs.marginBottom = '0px';
	dEs.marginLeft = '0px';
	//for now jQuery return undefined
    elm.d.oF = jQuery.css(elm, 'float');

//remember the old relative position
	elm.d.oR = {
		x : jQuery.intval(jQuery.css(elm, 'left')),
		y : jQuery.intval(jQuery.css(elm, 'top'))
	};
	jQuery.dragHelper.html('');
	
	c = elm.cloneNode(true);
	
	if (elm.d.opacity) {
		jQuery(c).css('opacity', elm.d.opacity);
		if (window.ActiveXObject) {
			jQuery(c).css('filter', 'alpha(opacity=' + elm.d.opacity * 100 + ')');
		}
	}
	//jQuery(c)..css('width', elm.d.oC.wb + 'px').css('height',elm.d.oC.hb + 'px');
	jQuery(c).css(
		{
			display:	'block',
			left:		'0px',
			top: 		'0px'
		}
	);
	$(elm).before(jQuery.dragHelper[0]);
	jQuery.dragHelper.append(c);/*
				.css('left', elm.d.oC.x + 'px')
				.css('top', elm.d.oC.y + 'px' )
				.css('width', elm.d.oC.wb + 'px')
				.css('height',elm.d.oC.hb + 'px' )
				.css(
					{
						display:	'block',
						overflow:	'hidden'
					}
				);*/
	dhs = jQuery.dragHelper[0].style;
	dhs.left = elm.d.oC.x + 'px';
	dhs.top = elm.d.oC.y + 'px';
	dhs.width = elm.d.oC.wb + 'px';
	dhs.height = elm.d.oC.hb + 'px';
	dhs.display = 'block';
	dhs.marginTop = '0px';
	dhs.marginRight = '0px';
	dhs.marginBottom = '0px';
	dhs.marginLeft = '0px';
	if (elm.d.ghosting == false) {
		//jQuery(elm).css('display', 'none');
		dEs.display = 'none';
	}
    jQuery.drug = elm;
	jQuery.drug.d.prot = false;
	if (elm.d.contaiment) {
		if (elm.d.contaiment.constructor == String) {
			if (elm.d.contaiment == 'parent') {
				elm.d.cont = jQuery.getPos(elm.parentNode);
				if (elm.d.si && elm.d.fractions) {
					elm.d.gx = 	(elm.d.cont.w)/(elm.d.fractions > 0 ? elm.d.fractions : 1) || 1;
					elm.d.gy = 	(elm.d.cont.h)/(elm.d.fractions > 0 ? elm.d.fractions : 1) || 1;
					/*elm.d.gx = elm.d.gx > 0 ? elm.d.gx : 1;
					elm.d.gy = elm.d.gy > 0 ? elm.d.gy : 1;*/
				}
			} else if (elm.d.contaiment == 'document') {
				clnt = jQuery.getClient();
				elm.d.cont = {
					x : 0,
					y : 0,
					w : clnt.w,
					h : clnt.h
				};
			};
		} else if (elm.d.contaiment.constructor == Array && elm.d.contaiment.length == 4) {
			elm.d.cont = {
				x : jQuery.intval(elm.d.contaiment[0]),
				y : jQuery.intval(elm.d.contaiment[1]),
				w : jQuery.intval(elm.d.contaiment[2]),
				h : jQuery.intval(elm.d.contaiment[3])
			};
		}
	}
	if (jQuery.droppables && jQuery.droppables.atLeast > 0 ){
		jQuery.drophighlight();
	}
	if (elm.d.zIndex != false) {
		jQuery.dragHelper.css('zIndex', elm.d.zIndex);
	}
	jQuery.drag(e,true);
};

jQuery.dragstop = function(e)
{
    if (jQuery.drug == null || jQuery.drug.d.prot == true) {
		return;
    }
	if (jQuery.drug.d.so == true) {
		jQuery(jQuery.drug).css('position', jQuery.drug.d.oP);
	}
	dEs = jQuery.drug.style;
	//in dragstart the margins where reseted
	dEs.marginTop = jQuery.drug.d.oM.t + 'px';
	dEs.marginRight = jQuery.drug.d.oM.r + 'px';
	dEs.marginBottom = jQuery.drug.d.oM.b + 'px';
	dEs.marginLeft = jQuery.drug.d.oM.l + 'px';
	hp = jQuery.getPos(jQuery.dragHelper[0]);
	if (jQuery.drug.d.revert == false) {
		nx = jQuery.drug.d.oR.x + (jQuery.drug.d.nx - jQuery.drug.d.oC.x);
		ny = (jQuery.drug.d.oR.y + (jQuery.drug.d.ny - jQuery.drug.d.oC.y));
		if (jQuery.drug.d.fx > 0 && nx != jQuery.drug.d.oC.x && ny != jQuery.drug.d.oC.y) {
			x = new jQuery.fx(jQuery.drug,jQuery.drug.d.fx, 'left');
			y = new jQuery.fx(jQuery.drug,jQuery.drug.d.fx, 'top');
			x.custom(jQuery.drug.d.oC.x,nx);
			y.custom(jQuery.drug.d.oC.y,ny);
		} else {
			jQuery.drug.style.left = nx + 'px';
			jQuery.drug.style.top = ny + 'px';
		}
		jQuery.dragHelper.empty();
		jQuery.dragHelper[0].style.display = 'none';
	} else {
		if (jQuery.drug.d.fx > 0) {
			jQuery.drug.d.prot = true;
			y = new jQuery.fx(jQuery.dragHelper[0],{duration:jQuery.drug.d.fx}, 'top');
			x = new jQuery.fx(
				jQuery.dragHelper[0],
				{
					duration : jQuery.drug.d.fx,
					complete : function()
					{
						 jQuery.dragHelper.empty();
						//jQuery(this).css('display','none');
						this.style.display = 'none';
						if (jQuery.drug.d.ghosting == false) {
							//jQuery(jQuery.drug).css('display', jQuery.drug.d.oD);
							jQuery.drug.style.display = jQuery.drug.d.oD;
						}
						jQuery.drug = null;
					}
				},
				'left'
			);
			if(jQuery.overzone && jQuery.sortables) {
				dh = jQuery.getPos(jQuery.sortHelper[0]);
			} else {
				dh = false;
			}
			x.custom(hp.x,dh ? dh.x : jQuery.drug.d.oC.x);
			y.custom(hp.y,dh ? dh.y : jQuery.drug.d.oC.y);
		} else {
			//jQuery.dragHelper.css('display','none');
			jQuery.dragHelper.empty();
			jQuery.dragHelper[0].style.display = 'none';
		}
	}
	
	if (jQuery.droppables && jQuery.droppables.atLeast > 0 ){
		jQuery.dropcheck(jQuery.drug);
	}
	if (jQuery.sortables && jQuery.drug.d.so == true) {
		jQuery.sortcheck(jQuery.drug);
	}
	if(!jQuery.dragmoved){
		if (jQuery.drug.tagName=='A' && !jQuery.drug.onclick) window.location.href=jQuery.drug.href;
	}
	if (jQuery.drug && jQuery.drug.d.prot == false) {
		if (jQuery.drug.d.ghosting == false) {
			jQuery(jQuery.drug).css('display', jQuery.drug.d.oD);
		}
	    jQuery.drug = null;
	}
};

jQuery.drag = function(e,init)
{
    if (jQuery.drug == null || jQuery.drug.d.prot == true) {
		return;
    }
    if(!init)jQuery.dragmoved=true;
	if (window.event) {
		sx = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
		sy = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
	} else {
		sx = e.clientX;
		sy = e.clientY;
	}
	if (e.pageX) {
		nScr = jQuery.getScroll();
		if (jQuery.drug.d.oScr.t != nScr.t) {
			sy += nScr.t - jQuery.drug.d.oScr.t;
		}
		if (jQuery.drug.d.oScr.l != nScr.l) {
			sx += nScr.l - jQuery.drug.d.oScr.l;
		}
	}
    jQuery.drug.d.nx = (sx - jQuery.drug.d.dX);
    jQuery.drug.d.ny = (sy - jQuery.drug.d.dY);

	if (jQuery.drug.d.gx) {
		jQuery.drug.d.nx = parseInt((jQuery.drug.d.nx + jQuery.drug.d.gx/2)/jQuery.drug.d.gx) * jQuery.drug.d.gx;
		jQuery.drug.d.ny = parseInt((jQuery.drug.d.ny + jQuery.drug.d.gy/2)/jQuery.drug.d.gy) * jQuery.drug.d.gy;
	}

	if (jQuery.drug.d.cont) {
		jQuery.drug.d.nx = jQuery.drug.d.nx < jQuery.drug.d.cont.x ? jQuery.drug.d.cont.x : ((jQuery.drug.d.nx + jQuery.drug.d.oC.w) > (jQuery.drug.d.cont.x + jQuery.drug.d.cont.w) ? (jQuery.drug.d.cont.x + ((jQuery.drug.d.cont.w - jQuery.drug.d.oC.w) >= 0 ? (jQuery.drug.d.cont.w - jQuery.drug.d.oC.w) : jQuery.drug.d.cont.w)) : jQuery.drug.d.nx);
		jQuery.drug.d.ny = jQuery.drug.d.ny < jQuery.drug.d.cont.y ? jQuery.drug.d.cont.y : ((jQuery.drug.d.ny + jQuery.drug.d.oC.h) > (jQuery.drug.d.cont.y + jQuery.drug.d.cont.h) ? (jQuery.drug.d.cont.y + ((jQuery.drug.d.cont.h - jQuery.drug.d.oC.h) >= 0 ? (jQuery.drug.d.cont.h - jQuery.drug.d.oC.h) : jQuery.drug.d.cont.h)) : jQuery.drug.d.ny);
	}

	jQuery.dragHelper[0].style.left = jQuery.drug.d.nx + 'px';
	jQuery.dragHelper[0].style.top = jQuery.drug.d.ny + 'px';
	
	if (jQuery.drug.d.si && jQuery.drug.d.onslide) {
		x = (jQuery.drug.d.oR.x + (jQuery.drug.d.nx - jQuery.drug.d.oC.x));
		y = (jQuery.drug.d.oR.y + (jQuery.drug.d.ny - jQuery.drug.d.oC.y));
		maxx = (jQuery.drug.d.cont.w - jQuery.drug.d.oC.wb);
		maxy = (jQuery.drug.d.cont.h - jQuery.drug.d.oC.hb);
		jQuery.drug.d.onslide(jQuery.drug, jQuery.intval(x * 100 / maxx), jQuery.intval(y * 100 / maxy));
	}
	
	if (jQuery.droppables && jQuery.droppables.atLeast > 0 ){
		jQuery.dropcheckhover(sx, sy, jQuery.drug.d.nx, jQuery.drug.d.ny);
	}
};
jQuery.draginit = false;

jQuery.fn.Draggable = function(o)
{
    if (!jQuery.draginit) {
		jQuery('body').bind('mousemove', jQuery.drag).bind('mouseup', jQuery.dragstop);
		jQuery.draginit = true;
    }
    if (!jQuery.dragHelper) {
		jQuery('body').append('<div id="dragHelper"></div>');
		jQuery.dragHelper = jQuery('#dragHelper');
		jQuery.dragHelper[0].style.position = 'absolute';
		jQuery.dragHelper[0].style.display = 'none';
		jQuery.dragHelper[0].style.cursor = 'move';
		jQuery.dragHelper[0].style.listStyle = 'none';
		/*jQuery.dragHelper.css(
			{
				position:	'absolute',
				display:	'none',
				cursor:		'move'
			}
		);*/
		if (window.ActiveXObject) {
			jQuery.dragHelper[0].onselectstart = function(){return false;};
			jQuery.dragHelper[0].ondragstart = function(){return false;};
		}
    }
    if (!o) {
		o = {};
	}
    return this.each(
		function()
		{
			if (this.isDraggable)
				return;
			if (window.ActiveXObject) {
				this.onselectstart = function(){return false;};
				this.ondragstart = function(){return false;};
			}
			var dhe = o.handle ? jQuery(this).find(o.handle) : jQuery(this);
			this.d = {
				revert : o.revert ? true : false,
				ghosting : o.ghosting ? true : false,
				so : o.so ? o.so : false,
				si : o.si ? o.si : false,
				zIndex : o.zIndex ? jQuery.intval(o.zIndex) : false,
				opacity : o.opacity ? parseFloat(o.opacity) : false,
				fx : jQuery.intval(o.fx),
				hpc : o.hpc ? o.hpc : false
				
			};
			if (o.contaiment && ((o.contaiment.constructor == String && (o.contaiment == 'parent' || o.contaiment == 'document')) || (o.contaiment.constructor == Array && o.contaiment.length == 4) )) {
				this.d.contaiment = o.contaiment;
			}
			if(o.fractions) {
				this.d.fractions = o.fractions;
			}
			if(o.grid){
				if(typeof o.grid == 'number'){
					this.d.gx = this.d.gy = jQuery.intval(o.grid);
				} else if (o.grid.length == 2) {
					this.d.gx = jQuery.intval(o.grid[0]);
					this.d.gy = jQuery.intval(o.grid[1]);
				}
			}
			if (o.onslide && o.onslide.constructor == Function) {
				this.d.onslide = o.onslide;
			}
			dhe.get(0).dE = this;
			dhe.bind('mousedown', jQuery.dragstart);
		}
	);
};
