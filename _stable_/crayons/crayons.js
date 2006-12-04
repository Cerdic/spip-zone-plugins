/*
 *  crayons.js (c) Fil , toggg 2006 -- licence GPL
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
cfgCrayons.prototype.mkimg = function(what) {
  return '<img class="crayon-' + what +
    '" src="' + this.imgPath + '/' + this.img[what].file +
    '" title="' + this.img[what].txt + '" />';
}
cfgCrayons.prototype.iconclick = function(c) {

  // le + qui passe en prive pour editer tout si classe type--id
  var link = c.match(/\b(\w+)--(\d+)\b/);
  link = link ? 
    '<a href="ecrire/?exec=' + link[1] + 's_edit&id_' + link[1] + '=' + link[2] +
    '">' + this.mkimg('edit') + '</a><br />' : '';

  var cray = c.match(/\b\w+-\w+-\d+\b/);
  cray = !cray ? '' : this.mkimg('pencil') + '<br />';

  return "<span class='crayon-icones'><span>" +
      cray + link +
      this.mkimg('img-changed') +
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
$.fn.opencrayon = function(evt) {
  if (evt.stopPropagation) {
    evt.stopPropagation();
  }
  return this
  .each(function(){
    // verifier que je suis un crayon
    if (!$(this).is('.crayon'))
      return;

    // voir si je dispose deja du crayon comme voisin
    if ($(this).is('.crayon-has')) {
      $(this)
      .hide()
      .next()
        .show();
    }
    // sinon charger le formulaire
    else {
      $(this)
      .append(configCrayons.mkimg('searching')); // icone d'attente
      var me=this;
      $.getJSON(configCrayons.url_crayons_html,
        {
          'w': $(this).width(),
          'h': $(this).height(),
          'wh': window.innerHeight,
          'em': $(this).css('fontSize'),
          'class': me.className
        },
        function (c) {
          $(me)
          .find("img.crayon-searching")
            .remove();
          if (c.$erreur) {
            uniAlert(c.$erreur);
            return false;
          }
          $(me)
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
$.fn.cancelcrayon = function() {
  return this.prev()
    .filter('.crayon-has')
    .show()
    .removeClass('crayon-has')
    .removeClass('crayon-changed')
  .next()
    .remove();
}

// masque le crayon ouvert
$.fn.hidecrayon = function() {
  return this
  .filter('.crayon-has')
  .show()
  .next()
    .hide()
    .removeClass('crayon-hover');
}

// active un crayon qui vient d'etre charge
$.fn.activatecrayon = function() {
  return this
  .click(function(e){
    e.stopPropagation();
  })
  .each(function(){
    var me = this;
    var w,h;
    $(me)
    .find('form')
      .ajaxForm({"dataType":"json",
			"after":function(d){
        $(me)
          .find("img.crayon-searching")
            .remove();
        if (d.$erreur > '') {
          if (d.$annuler) {
            if (d.$erreur > ' ') {
              uniAlert(d.$erreur);
            }
            $(me)
              .cancelcrayon();
          } else {
              uniAlert(d.$erreur+'\n'+configCrayons.txt.error);
              $(me)
                .find(".crayon-boutons")
                  .show(); // boutons de validation
          }
          return false;
        }

        $(me)
        .prev()
          .html(
            d[$('input.crayon-id', me).val()]
          )
          .iconecrayon();
        $(me)
          .cancelcrayon();
      }}).onesubmit(function(){
        $(this)
        .append(configCrayons.mkimg('searching')) // icone d'attente
        .find(".crayon-boutons")
          .hide(); // boutons de validation
      }).keyup(function(){
        $(this)
        .find(".crayon-boutons")
          .show();
        $(me)
        .prev()
          .addClass('crayon-changed');
      })
      .find(".crayon-active")
        .css({
            'fontSize': $(me).prev().css('fontSize'),
            'fontFamily': $(me).prev().css('fontFamily'),
            'fontWeight': $(me).prev().css('fontWeight'),
            'lineHeight': $(me).prev().css('lineHeight'),
            'color': $(me).prev().css('color'),
            'backgroundColor': $(me).prev().css('backgroundColor')
        })
        .each(function(n){
          if (n==0)
            this.focus();
        })
        .keypress(function(e){
          if (e.keyCode == 27) {
            $(me)
            .cancelcrayon();
          }
          var maxh = this.className.match(/\bmaxheight(\d+)?\b/);
          if (maxh) {
            maxh = maxh[1] ? parseInt(maxh[1]) : 200;
            maxh = this.scrollHeight < maxh ? this.scrollHeight : maxh;
            if (maxh > this.clientHeight) {
            	$(this).css('height', maxh + 'px');
            }
          }
        })
      .end()
      .find(".crayon-submit")
        .click(function(e){
          e.stopPropagation();
          $(this)
          .ancestors("form").eq(0)
          .submit();
        })
      .end()
      .find(".crayon-cancel")
        .click(function(e){
          e.stopPropagation();
          $(me)
          .cancelcrayon();
        })
      .end()
      .find(".crayon-hide")
        .click(function(e){
          e.stopPropagation();
          $(me)
          .prev()
          .hidecrayon();
        })
      .end()
      .each(function(){
        // rendre les boutons visibles (cf. plugin jquery/dimensions.js)
        var buttonpos = (this.offsetTop || 0) + $(this).height();
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
$.fn.iconecrayon = function(){
  return this.each(function() {
    $(this).prepend(configCrayons.iconclick(this.className))
    .find('.crayon-pencil') // le pencil a clicker lui-meme
      .click(function(e){
        $(this).ancestors('.crayon').eq(0).opencrayon(e);
      });
    });
}

// initialise les crayons (cree le clone actif)
$.fn.initcrayon = function(){
  this
  .addClass('crayon-autorise')
  .click(function(e){
    e.stopPropagation();
  })
  .dblclick(function(e){
    $(this).opencrayon(e);
  })
  .iconecrayon();

  // :hover pour MSIE
  if (jQuery.browser.msie) {
    this.hover(
      function(){
        $(this).addClass('crayon-hover');
      },function(){
        $(this).removeClass('crayon-hover');
      }
    );
  }

  return this;
}

// demarrage
$(document).ready(function() {
  if (!configCrayons.droits) return;
  if (!jQuery.getJSON) return; // jquery >= 1.0.2

  // sortie, demander pour sauvegarde si oubli
  if (configCrayons.txt.sauvegarder) {
    $(window).unload(function(e) {
      var chg = $(".crayon-changed");
      if (chg.length && uniConfirm(configCrayons.txt.sauvegarder)) {
        chg.next().find('form').submit();
      }
    });
  }

  $(".crayon").filter(configCrayons.droits).initcrayon();

  // fermer tous les crayons ouverts
  $("html")
  .click(function() {
    $(".crayon.crayon-has:hidden")
    .hidecrayon();
  });
});

function dump(elt)
{var txt=''; for (var prop in elt) {txt += prop+'='+elt[prop]+'/';} alert(txt);}

