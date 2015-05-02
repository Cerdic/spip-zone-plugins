function spipcatchatemoticonpublic(emoticon)
{
	$('#message').val($('#message').val()+''+emoticon);	
	$('#emoticon').trigger("play");
}

function session() {
    $(location).attr('href', 'spip.php')
    
}
function spipcatchatrestartmenu(e, t) {
    $('#selectUser').val(e);
    $('#auto').val(t)
}
function spipcatchatadduser(e, t) {
    (id = $('#selectUser').val()) ? $.ajax({
        type: 'POST',
        url: t + 'phpscripts/set-users.php',
        data: 'id=' + id + '&stat=' + e,
        success: function () {
            $('#auto').val('');
            $('#selectUser').val('');
            spipcatchatrest(t)
        }
    })  : ($('#spipcatchatmembreajout').css('display', 'block'), $('#spipcatchat').css('display', 'none'), setTimeout(function () {
        $('#spipcatchatmembreajout').css('display', 'none');
        $('#spipcatchat').css('display', 'block')
    }, 4000), $('#auto').val(''), $('#selectUser').val(''));
    spipcatchatrest(t)
}
function spipcatchatrest(e) {
    $.getJSON(e + 'phpscripts/get-users-liste.php', function (e) {
        if (0 != e) {
            $('#BoxUsers').css('display', 'block');
            $('.spipcatchatmembreschat').css('display', 'none');
            for (var t in e) $('#idauteurcatchat' + e[t]).css('display', 'block')
        } else $('#BoxUsers').css('display', 'none')
    })
}
function spipcatchatShowChat(e, t, n, r, i, s, o, u, pack) {
    $.ajax({
        url: e + 'phpscripts/get-autorisation.php',
        type: 'POST',
        data: 'id_auteur=' + t + '&id_salon=' + n + '&nom=' + encodeURIComponent(s) + '&url=' + encodeURIComponent(e) + '&char=' + encodeURIComponent(o),
        success: function (s) {
            '1' == s || '4' == s ? (startchat(u, '125000', n, t, e, i, r, o, pack), $('#spipcatchatselectsalon').css('display', 'block'))  : '2' == s ? (startchat(u, '125000', n, t, e, i, r, o, pack), $('#pepoletrash').css('display', 'block'), $('#spipcatchatselectsalon').css('display', 'block'))  : '3' == s ? (startchat(u, '125000', n, t, e, i, r, o, pack), $('#pepoletrash').css('display', 'block'), $('#pepoleadd').css('display', 'block'), $('#spipcatchatselectsalon').css('display', 'block'))  : ($('#spipcatchatsalonprive').css('display', 'block'), setTimeout(function () {
                window.location = r
            }, 7000))
        }
    })
}
function spipcatchatsalon(e, t, n, r) {
    $.getJSON(e + 'phpscripts/get-selected-salon.php', {
        lang: t,
        'char': n,
        auteur: r
    }, function (e) {
        $('#spipcatchatselectedsalonchat').html(html_entity_decode(e))
    })
}
function spipcatchatShowSalon(e, t, n, r) {
    i && clearInterval(i);
    spipcatchatsalon(e, t, n, r);
    var i = setInterval(function () {
        spipcatchatsalon(e, t, n, r)
    }, 100000)
}
function logoSpipHidden(e) {
    $('.' + e).css('display', 'none')
}
function logoSpipShow(e) {
    $('.' + e).css('display', 'block')
}
function quit(e) {
    $(location).attr('href', e)
}
function spipcatchattrash(e, t, n, r) {
    $('#container').addClass('spipcatchatpause');
    $.ajax({
        type: 'POST',
        url: t + 'phpscripts/set-trash.php',
        data: 'char=' + encodeURIComponent(r),
        success: function (t) {
            1 != t && alert(n + '' + t);
            $(location).attr('href', e)
        }
    })
}
function unlocked(e, t) {
    'false' != document.getElementById('public').value ? ($('#public').attr('src', e), $('#public').val('false'))  : ($('#public').attr('src', t), $('#public').val('true'))
}
function spipcatchataddsalon(e, t, n) {
    var r = encodeURIComponent(htmlentities($('#newSalon').val())),
    s = encodeURIComponent($('#public').val()),
    o = encodeURIComponent(n[2]),
    u = document.getElementsByTagName('option'),
    a = 1;
    i = 1;
    for (y = $('#newSalon').val(); u.length > i; ) {
        x = u[i].firstChild.nodeValue;
        if (x == '[√] ' + y || x == '[–] ' + y || x == '[x] ' + y) a = 0;
        i++
    }
    r && a ? ($('fieldset').addClass('spipcatchatpause'), $.ajax({
        type: 'POST',
        url: e + '/phpscripts/set-addsalon.php',
        data: 'newsalon=' + r + '&public=' + s + '&catchatid=' + o + '&char=' + encodeURIComponent(n[3]),
        success: function () {
            alert(n[0]);
            $('fieldset').removeClass('spipcatchatpause');
            spipcatchatsalon(e, t, n[3], n[2])
        }
    }))  : 0 == a && alert(n[1])
}
function spipcatchathelp(e) {
    window.open(e + '/doc/Guide de l utilisateur.pdf', '_blank')
}
function getMessages(e, t, n, r, i, pack) { 
    $.getJSON(n + 'phpscripts/get-message.php', {
        auteur: t,
        ref: e / 1000,
        aucunmessage: r[0],
        'char': i
    }, function (e) {
        var t = $('#text');
        $('#spipcatchatannonce').html('<span class="spipcatchatinfo"><b>' + e.annonce + '</b></span>');
        $('#text').html(spipcatchattypo(e.messages, n, pack.trim()));
        1 != scrollBar && (t[0].scrollTop = t[0].scrollHeight, scrollBar = !0);
        void 0 !== t && t[0].childNodes.length > nombreMessage && (t[0].scrollTop = t[0].scrollHeight, $('#soundGet').trigger('play'));
        void 0 !== t && (nombreMessage = t[0].childNodes.length)
    })
}
function spipcatchatsetmessage(e, t, n, r) {
    var i = encodeURIComponent($('#message').val());
    $('#message').val('');
    $.ajax({
        type: 'POST',
        url: e + '/phpscripts/set-message.php',
        data: 'message=' + i + '&auteur=' + t + '&char=' + encodeURIComponent(n) + '&ref=' + encodeURIComponent(r),
        success: function (e) {
            $('#soundPost').trigger('play');
            $('#soundGet').trigger('stop');
            1 != e && $('#responsePost').html(e).slideDown('slow');
            $('#message').focus()
        },
        error: function (e) {
            alert('Erreur')
        }
    })
}
function startchat(e, t, n, r, i, s, o, u, pack) {
    document.getElementById('message') && (getOnlineUsers(n, r, i, s, o, u), statusStart = window.setInterval(function () {
        getOnlineUsers(n, r, i, s, o, u)
    }, t), window.setInterval(function () {
        getMessages(e, r, i, s, u, pack)
    }, e), $('#message').focus())
}
function getOnlineUsers(e, t, n, r, i, s) {
    $.getJSON(n + 'phpscripts/get-online.php', {
        auteur: t,
        salon: e
    }, function (e) {
        if (1 == e.autorisation) {
            var r = '',
            s,
            o;
            for (o in e.list) 'busy' == e.list[o].status ? (texte = 'Occupé(e) [X]', s = 'inactive', t == e.list[o].id && $('#SpipCatChatStatus option[value=2]').attr('selected', 'selected'))  : 'inactive' == e.list[o].status ? (texte = ' Absent(e) [-] ', s = 'neutral', t == e.list[o].id && $('#SpipCatChatStatus option[value=1]').attr('selected', 'selected'))  : (texte = 'En ligne [&radic;]', s = 'active', t == e.list[o].id && $('#SpipCatChatStatus option[value=3]').attr('selected', 'selected')),
            r += '<span title="' + texte + '"><img src="' + n + '/images/status-' + s + '.png" /> ' + e.list[o].login + '</span><br/>';
            $('#users').html(r)
        } else window.location = i
    })
}
function SpipCatChatsetStatus(e, t, n) {
    $.ajax({
        type: 'POST',
        url: n + '/phpscripts/set-status.php',
        data: 'status=' + e + '&auteur=' + t,
        success: function (e) {
            $('#users-td').addClass('spipcatchatpause');
            $('#SpipCatChatStatus').addClass('spipcatselectpause');
            $('#statutattentechange').css('display', 'block');
            $('#statusResponse').html('<div class="SpipCatChatTux-loading-indicator"></div>');
            setTimeout(rmResponse, 15000)
        },
        error: function (e) {
            $('#statusResponse').html('<span class="erreur">[ Erreur - Status ]</span>');
            setTimeout(rmResponse, 15000)
        }
    })
}
function rmResponse() {
    $('#statusResponse').html('');
    $('#users-td').removeClass('spipcatchatpause');
    $('#SpipCatChatStatus').removeClass('spipcatselectpause');
    $('#statutattentechange').css('display', 'none')
}
var scrollBar = !1,
nombreMessage
