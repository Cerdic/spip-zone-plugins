/*
 *  crayons.js (c) Fil, toggg 2006 -- licence GPL
 */

// le prototype configuration de Crayons
function cfgCrayons(options)
{
  this.url_crayons_html = 'spip.php?action=crayons_html';
  this.img = {
    'searching':{'file':'searching.gif','txt':'En attente du serveur ...'},
    'edit':{'file':'pencil.png','txt':'Editer'},
    'img-changed':{'file':'changed.png','txt':'Deja modifie'}
  };
  this.txt = {
  };
  for (opt in options) {
    this[opt] = options[opt];
  }
}
cfgCrayons.prototype.mkimg = function(what, extra) {
  return '<img class="crayon-' + what +
    '" src="' + this.imgPath + '/' + this.img[what].file +
    '" title="' + this.img[what].txt + (extra ? extra : '') + '" />';
}
cfgCrayons.prototype.iconclick = function(c) {

  // le + qui passe en prive pour editer tout si classe type--id
  var link = c.match(/\b(\w+)--(\d+)\b/);
  link = link ? 
    '<a href="ecrire/?exec=' + link[1] + 's_edit&id_' + link[1] + '=' + link[2] +
    '">' + this.mkimg('edit', ' (' + link[1] + ' ' + link[2] + ')') + '</a><br />' : '';

  var cray = c.match(/\b\w+-(\w+)-\d+\b/);
  var boite = !cray ? '' : this.mkimg('pencil', ' (' + cray[1] + ')') + '<br />';

  return "<span class='crayon-icones'><span>" +
      boite + link +
      this.mkimg('img-changed', cray ? ' (' + cray[1] + ')': '') +
    "</span></span>";
}

function entity2unicode(txt)
{
  var reg = txt.split(/&#(\d+);/i);
  for (var i = 1; i < reg.length; i+=2) {
    reg[i] = String.fromCharCode(parseInt(reg[i]));
  }
  return reg.join('');
}

function uniAlert(txt)
{
  alert(entity2unicode(txt));
}

function uniConfirm(txt)
{
  return confirm(entity2unicode(txt));
}

// ouvre un crayon
jQuery.fn.opencrayon = function(evt) {
  if (evt.stopPropagation) {
    evt.stopPropagation();
  }
  return this
  .each(function(){
    // verifier que je suis un crayon
    if (!jQuery(this).is('.crayon'))
      return;

    // voir si je dispose deja du crayon comme voisin
    if (jQuery(this).is('.crayon-has')) {
      jQuery(this)
      .hide()
      .next()
        .show();
    }
    // sinon charger le formulaire
    else {
      jQuery(this)
      .append(configCrayons.mkimg('searching')); // icone d'attente
      var me=this;
      jQuery.getJSON(configCrayons.url_crayons_html,
        {
          'w': jQuery(this).width(),
          'h': jQuery(this).height(),
          'wh': window.innerHeight,
          'em': jQuery(this).css('fontSize'),
          'class': me.className
        },
        function (c) {
          jQuery(me)
          .find("img.crayon-searching")
            .remove();
          if (c.$erreur) {
            uniAlert(c.$erreur);
            return false;
          }
          jQuery(me)
          .hide()
          .addClass('crayon-has')
          .after('<div>'+c.$html+'</div>')
          .next()
            .activatecrayon();
        }
      );
    }
  });
}

// annule le crayon ouvert (fonction destructive)
jQuery.fn.cancelcrayon = function() {
  return this.prev()
    .filter('.crayon-has')
    .show()
    .removeClass('crayon-has')
    .removeClass('crayon-changed')
  .next()
    .remove();
}

// masque le crayon ouvert
jQuery.fn.hidecrayon = function() {
  return this
  .filter('.crayon-has')
  .show()
  .next()
    .hide()
    .removeClass('crayon-hover');
}

// active un crayon qui vient d'etre charge
jQuery.fn.activatecrayon = function() {
  return this
  .click(function(e){
    e.stopPropagation();
  })
  .each(function(){
    var me = this;
    var w,h;
    jQuery(me)
    .find('form')
      .ajaxForm({"dataType":"json",
      "after":function(d){
        jQuery(me)
          .find("img.crayon-searching")
            .remove();
        if (d.$erreur > '') {
          if (d.$annuler) {
            if (d.$erreur > ' ') {
              uniAlert(d.$erreur);
            }
            jQuery(me)
              .cancelcrayon();
          } else {
              uniAlert(d.$erreur+'\n'+configCrayons.txt.error);
              jQuery(me)
                .find(".crayon-boutons")
                  .show(); // boutons de validation
          }
          return false;
        }

        jQuery(me)
        .prev()
          .html(
            d[jQuery('input.crayon-id', me).val()]
          )
          .iconecrayon();
        jQuery(me)
          .cancelcrayon();
      }}).onesubmit(function(){
        jQuery(this)
        .append(configCrayons.mkimg('searching')) // icone d'attente
        .find(".crayon-boutons")
          .hide(); // boutons de validation
      }).keyup(function(){
        jQuery(this)
        .find(".crayon-boutons")
          .show();
        jQuery(me)
        .prev()
          .addClass('crayon-changed');
      })
      .find(".crayon-active")
        .css({
            'fontSize': jQuery(me).prev().css('fontSize'),
            'fontFamily': jQuery(me).prev().css('fontFamily'),
            'fontWeight': jQuery(me).prev().css('fontWeight'),
            'lineHeight': jQuery(me).prev().css('lineHeight'),
            'color': jQuery(me).prev().css('color'),
            'backgroundColor': jQuery(me).prev().css('backgroundColor')
        })
        .each(function(n){
          if (n==0)
            this.focus();
        })
        .keypress(function(e){
          if (e.keyCode == 27) {
            jQuery(me)
            .cancelcrayon();
          }
          var maxh = this.className.match(/\bmaxheight(\d+)?\b/);
          if (maxh) {
            maxh = maxh[1] ? parseInt(maxh[1]) : 200;
            maxh = this.scrollHeight < maxh ? this.scrollHeight : maxh;
            if (maxh > this.clientHeight) {
              jQuery(this).css('height', maxh + 'px');
            }
          }
        })
      .end()
      .find(".crayon-submit")
        .click(function(e){
          e.stopPropagation();
          jQuery(this)
          .ancestors("form").eq(0)
          .submit();
        })
      .end()
      .find(".crayon-cancel")
        .click(function(e){
          e.stopPropagation();
          jQuery(me)
          .cancelcrayon();
        })
      .end()
      .find(".crayon-hide")
        .click(function(e){
          e.stopPropagation();
          jQuery(me)
          .prev()
          .hidecrayon();
        })
      .end()
      .each(function(){
        // rendre les boutons visibles (cf. plugin jquery/dimensions.js)
        var buttonpos = (this.offsetTop || 0) + jQuery(this).height();
        var scrolltop = window.pageYOffset ||
          jQuery.boxModel && document.documentElement.scrollTop  ||
          document.body.scrollTop || 0;
        var scrollleft = window.pageXOffset || 
          jQuery.boxModel && document.documentElement.scrollLeft ||
          document.body.scrollLeft || 0;
        var h = window.innerHeight;
        if (buttonpos - h + 20 > scrolltop) {
          window.scrollTo(scrollleft, buttonpos - h + 30);
        }
      })
    .end();
  });
}

// insere les icones dans l'element
jQuery.fn.iconecrayon = function(){
  return this.each(function() {
    jQuery(this).prepend(configCrayons.iconclick(this.className))
    .find('.crayon-pencil') // le pencil a clicker lui-meme
      .click(function(e){
        jQuery(this).ancestors('.crayon').eq(0).opencrayon(e);
      });
    });
}

// initialise les crayons (cree le clone actif)
jQuery.fn.initcrayon = function(){
  this
  .addClass('crayon-autorise')
  .dblclick(function(e){
    jQuery(this).opencrayon(e);
  })
  .iconecrayon();

  // :hover pour MSIE
  if (jQuery.browser.msie) {
    this.hover(
      function(){
        jQuery(this).addClass('crayon-hover');
      },function(){
        jQuery(this).removeClass('crayon-hover');
      }
    );
  }

  return this;
}

// Gestion du Yellow Fade (fonctionnalite optionnelle)
function easeInOut(minValue,maxValue,totalSteps,actualStep,powr) {
	var delta = maxValue - minValue;
	var stepp = minValue+(Math.pow(((1 / totalSteps)*actualStep),powr)*delta);
	return Math.ceil(stepp)
}

function doBGFade(elem,startRGB,endRGB,finalColor,steps,intervals,powr) {
	if (elem.bgFadeInt) window.clearInterval(elem.bgFadeInt);
	var actStep = 0;
	elem.bgFadeInt = window.setInterval(
		function() {
			elem.style.backgroundColor = "rgb("+
				easeInOut(startRGB[0],endRGB[0],steps,actStep,powr)+","+
				easeInOut(startRGB[1],endRGB[1],steps,actStep,powr)+","+
				easeInOut(startRGB[2],endRGB[2],steps,actStep,powr)+")";
			actStep++;
			if (actStep > steps) {
			elem.style.backgroundColor = finalColor;
			window.clearInterval(elem.bgFadeInt);
			}
		}
		,intervals)
}

function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
}

// demarrage
jQuery(document).ready(function() {
  if (!configCrayons.droits) return;
  if (!jQuery.getJSON) {
    alert("version jquery < 1.0.2, Crayon ne peut pas fonctionner");
    return;
  }
  if (!jQuery.fn.ajaxForm) {
    alert("jQuery plugin form.js absent, Crayon ne peut pas fonctionner");
    return;
  }

  // sortie, demander pour sauvegarde si oubli
  if (configCrayons.txt.sauvegarder) {
    jQuery(window).unload(function(e) {
      var chg = jQuery(".crayon-changed");
      if (chg.length && uniConfirm(configCrayons.txt.sauvegarder)) {
        chg.next().find('form').submit();
      }
    });
  }

  jQuery(".crayon").filter(configCrayons.droits).initcrayon();

  // fermer tous les crayons ouverts
  jQuery("html")
  .click(function() {
    jQuery(".crayon.crayon-has:hidden")
    .hidecrayon();
  });
  
	if (configCrayons.cfg.yellow_fade) {
		// Activer le Yellow Fade pour les elements editables
		jQuery("div.crayon").hover(function(){doBGFade(this,[255,255,180],[255,255,255],'transparent',40,20,4);}, function(){});
	}
  
  if (configCrayons.cfg.filet) {
		// on rajoute une div supplémentaire qui se cale avec la div courante 
		// C'est elle qui va s'afficher lors du hover
		
		// esthetique
		jQuery('.crayon-icones img',this).css({
			'padding':'2px',
			'border':'2px solid #999',
			'border-left':'0',
			'background-color':'#FFF'
		});
			
		var test=0;
		
		jQuery('.crayon-autorise').each(
		function(){
				$(this)
					.prepend('<div class="survol"></div>')
					.find('.survol')
						.css('border','1px solid red')
						.css('display','none')
						.position('absolute')
						.height((this.offsetHeight - 2) + 'px')
						.width((this.offsetWidth - 2) + 'px');
				if (jQuery.browser.msie) {
			    $('#survol')
						.width(this.offsetWidth + 'px')
						.height(this.offsetHeight + 'px');
				}
			}
		);

		jQuery('.crayon-autorise').hover(
			function(){
				if (jQuery.browser.msie) jQuery(this).addClass('crayon-hover');
				$('.survol', this).css('display','block');
			},
			function(){
				if (jQuery.browser.msie) jQuery(this).removeClass('crayon-hover');
				$('.survol', this).css('display','none');
			}
		);
		
	}

});
