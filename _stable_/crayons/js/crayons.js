/*
 *  crayons.js (c) Fil, toggg 2006-2007 -- licence GPL
 */

// le prototype configuration de Crayons
function cfgCrayons(options)
{
  this.url_crayons_html = options['dir_racine']+'spip.php?action=crayons_html';
  this.img = {
    'searching':{'file':'searching.gif','txt':'En attente du serveur ...'},
    'edit':{'file':'crayon.png','txt':'Editer'},
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
    '">' + this.mkimg('edit', ' (' + link[1] + ' ' + link[2] + ')') + '</a>' : '';

  var cray = c.match(/\b\w+-(\w+)-\d+\b/);
  var boite = !cray ? '' : this.mkimg('crayon', ' (' + cray[1] + ')');

  return "<span class='crayon-icones'><span>" + boite +
      this.mkimg('img-changed', cray ? ' (' + cray[1] + ')': '') +
      link +"</span></span>";
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

// donne le crayon d'un element
jQuery.fn.crayon = function(){
  if (this.length)
    return jQuery(
      jQuery.map(this, function(a){
        return '#'+(jQuery(a).find('.crayon-icones').attr('rel'));
      })
      .join(','));
  else
    return jQuery([]);
}

// ouvre un crayon
jQuery.fn.opencrayon = function(evt, percent) {
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
      .css('visibility','hidden')
      .crayon()
        .show();
    }
    // sinon charger le formulaire
    else {
      // sauf si je suis deja en train de le charger (lock)
      if (jQuery(this).find("img.crayon-searching").length) {
        return;
      }
      jQuery(this)
      .find('.crayon-icones span')
      .append(configCrayons.mkimg('searching')); // icone d'attente
      var me=this;
      var params = {
        'w': jQuery(this).width(),
        'h': jQuery(this).height(),
        'wh': window.innerHeight,
        'em': jQuery(this).css('fontSize'),
        'class': me.className,
        'color': jQuery(this).css('color'),
        'font-size': jQuery(this).css('fontSize'),
        'font-family': jQuery(this).css('fontFamily'),
        'font-weight': jQuery(this).css('fontWeight'),
        'line-height': jQuery(this).css('lineHeight'),
        'background-color': jQuery(this).css('backgroundColor')
      };
      if (params['background-color'] == 'transparent'
      || params['background-color'] == 'rgba(0, 0, 0, 0)') {
        jQuery(me).parents()
        .each(function(){
          var bg = jQuery(this).css('backgroundColor');
          if (bg != 'transparent'
          && (params['background-color'] == 'transparent'
          || params['background-color'] == 'rgba(0, 0, 0, 0)'))
            params['background-color'] = bg;
        });
      }
      jQuery.getJSON(configCrayons.url_crayons_html,
        params,
        function (c) {
          jQuery(me)
          .find("img.crayon-searching")
            .remove();
          if (c.$erreur) {
            uniAlert(c.$erreur);
            return false;
          }
          id_crayon++;
          jQuery(me)
          .css('visibility','hidden')
          .addClass('crayon-has')
          .find('.crayon-icones')
            .attr('rel','crayon_'+id_crayon);
          var pos = jQuery(me).offset({'scroll':false});
          jQuery('<div class="crayon-html" id="crayon_'+id_crayon+'"></div>')
          .css({
            'position':'absolute',
            'top':pos['top']-1,
            'left':pos['left']-1
          })
          .appendTo('body')
          .html(c.$html);
          jQuery(me)
          .activatecrayon(percent);
        }
      );
    }
  });
}

// annule le crayon ouvert (fonction destructive)
jQuery.fn.cancelcrayon = function() {
  this
    .filter('.crayon-has')
    .css('visibility','visible')
    .removeClass('crayon-has')
    .removeClass('crayon-changed')
  .crayon()
    .remove();
  return this;
}

// masque le crayon ouvert
jQuery.fn.hidecrayon = function() {
  this
  .filter('.crayon-has')
  .css('visibility','visible')
  .crayon()
    .hide()
    .removeClass('crayon-hover');
  return this;
}

// active un crayon qui vient d'etre charge
jQuery.fn.activatecrayon = function(percent) {
  this
  .crayon()
  .click(function(e){
    e.stopPropagation();
  })
  this
  .each(function(){
    var me = jQuery(this);
    var crayon = jQuery(this).crayon();
    crayon
    .find('form')
      .ajaxForm({"dataType":"json",
      "after":function(d){
        me
        .find("img.crayon-searching")
          .remove();
        if (d.$erreur > '') {
          if (d.$annuler) {
            if (d.$erreur > ' ') {
              uniAlert(d.$erreur);
            }
            me
            .cancelcrayon();
          } else {
              uniAlert(d.$erreur+'\n'+configCrayons.txt.error);
              crayon
              .find(".crayon-boutons")
                .show(); // boutons de validation
          }
          return false;
        }
        me
        .cancelcrayon()
        .html(
          d[jQuery('input.crayon-id', crayon).val()]
        )
        .iconecrayon();
      }})
      .one('submit', function(){
        crayon
        .append(configCrayons.mkimg('searching')) // icone d'attente
        .find(".crayon-boutons")
          .hide(); // boutons de validation
      })
      // keyup pour les input et textarea ...
      .keyup(function(e){
        crayon
        .find(".crayon-boutons")
          .show();
        me
        .addClass('crayon-changed');
        e.cancelBubble = true; // ne pas remonter l'evenement vers la page
      })
      // ... change pour les select : ici on submit direct, pourquoi pas
      .change(function(e){
        crayon
        .find(".crayon-boutons")
          .show();
        me
        .addClass('crayon-changed');
        e.cancelBubble = true;
      })
      .keypress(function(e){
        e.cancelBubble = true;
      })
      .find(".crayon-active")
        .each(function(n){
          // focus pour commencer a taper son texte directement dans le champ
          // on essaie de positionner la selection (la saisie) au niveau du clic
          if (n==0) {
            this.focus();
            // premiere approximation, en fonction de la hauteur du clic
            var position = parseInt(percent * this.textLength);
            this.selectionStart=position;
            this.selectionEnd=position;
          }
        })
        .keypress(function(e){
          if (e.keyCode == 27) {
            me
            .cancelcrayon();
          }
          // Clavier pour sauver
          if (
          (e.ctrlKey && (
            /* ctrl-s ou ctrl-maj-S, firefox */
            ((e.charCode||e.keyCode) == 115) || ((e.charCode||e.keyCode) == 83))
            /* ctrl-s, safari */
            || (e.charCode==19 && e.keyCode==19)
          ) || (!e.charCode && e.keyCode == 119 /* F8, windows */)
          ) {
            crayon
            .find("form.formulaire_spip")
            .submit();
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
          .parents("form").eq(0)
          .submit();
        })
      .end()
      .find(".crayon-cancel")
        .click(function(e){
          e.stopPropagation();
          me
          .cancelcrayon();
        })
      .end()
      // decaler verticalement si la fenetre d'edition n'est pas visible
      .each(function(){
        var offset = jQuery(this).offset({'scroll':false});
        var hauteur = parseInt(jQuery(this).css('height'));
        var scrolltop = jQuery(window).scrollTop();
        var h = jQuery(window).height();
        if (offset['top'] - 5 <= scrolltop)
          jQuery(window).scrollTop(offset['top'] - 5);
        else if (offset['top'] + hauteur - h + 20 > scrolltop)
          jQuery(window).scrollTop(offset['top'] + hauteur - h + 30);
        // Si c'est textarea, on essaie de caler verticalement son contenu
        // et on lui ajoute un resizehandle
        jQuery("textarea", this)
        .each(function(){
          if (percent && this.scrollHeight > hauteur) {
            this.scrollTop = this.scrollHeight * percent - hauteur;
          }
        })
        .resizehandle()
          // decaler les boutons qui suivent un resizer de 16px vers le haut
          .next('.resizehandle')
            .next('.crayon-boutons')
            .css('margin-top', '-16px');
      })
    .end();
  });
}

// insere les icones dans l'element
jQuery.fn.iconecrayon = function(){
  return this.each(function() {
    jQuery(this).prepend(configCrayons.iconclick(this.className))
    .find('.crayon-crayon, .crayon-img-changed') // le crayon a clicker lui-meme et sa memoire
      .click(function(e){
        jQuery(this).parents('.crayon').eq(0).opencrayon(e);
      });
    });
}

// initialise les crayons
jQuery.fn.initcrayon = function(){
  this
  .addClass('crayon-autorise')
  .dblclick(function(e){
    jQuery(this).opencrayon(e,
      // calcul du "percent" du click par rapport a la hauteur totale du div
      ((e.pageY ? e.pageY : e.clientY) - document.body.scrollTop - this.offsetTop)
      / this.clientHeight);
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

/* une fonction pour initialiser les crayons dynamiquement */
jQuery.fn.initcrayons = function(){
  this
  .find('.crayon')
  .not('.crayon-autorise')
  .filter(configCrayons.droits)
  .initcrayon();
}

// demarrage
jQuery(document).ready(function() {
  if (!configCrayons.droits) return;
  id_crayon = 0; // global

  // sortie, demander pour sauvegarde si oubli
  if (configCrayons.txt.sauvegarder) {
    jQuery(window).unload(function(e) {
      var chg = jQuery(".crayon-changed");
      if (chg.length && uniConfirm(configCrayons.txt.sauvegarder)) {
        chg.crayon().find('form').submit();
      }
    });
  }

  // on limite l'init auto aux 1000 premiers crayons
  // setTimeout sert a passer en execution asynchrone pour confort d'affichage
  if ((typeof crayons_init_dynamique == 'undefined') || (crayons_init_dynamique==false))
    setTimeout(function(){
      jQuery(".crayon:lt(1000)")
      .filter(configCrayons.droits)
      .initcrayon();
    }, 300);

  // un clic en dehors ferme tous les crayons ouverts ?
  if (configCrayons.cfg.clickhide)
  jQuery("html")
  .click(function(){
    jQuery('.crayon-has')
    .hidecrayon();
  });
});
