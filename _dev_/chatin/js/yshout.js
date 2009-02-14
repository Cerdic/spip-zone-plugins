String.prototype.sReplace = function(find, replace) {
	return this.split(find).join(replace);
};

String.prototype.repeat = function(times) {
	var rep = new Array(times + 1);
	return rep.join(this);
}

var YShout = function() {
	var self = this;
	var args = arguments;
	$(document).ready(function(){
		self.init.apply(self, args);
	});
}

var yShout;

YShout.prototype = {
	init: function(options) {
		yShout = this;
		if ($('#yshout').size() == 0) return;

		var dOptions = {
			yPath: 'yshout/',
			log: 1
		};

		this.options = jQuery.extend(dOptions, options);
		this.ajax(this.initialLoad, { 
			reqType: 'init',
			yPath: this.options.yPath,
			log: this.options.log
		});

		this.postNum = 0;
		this.floodAttempt = 0;

	},

	initialLoad: function(updates) {
		this.d('In initialLoad');

		this.prefs = updates.prefs;
		this.initForm();
		this.initRefresh();
		this.initCP();
		if (this.prefs.flood) this.initFlood();

		if (updates.nickname)
			$('#ys-input-nickname')
				.removeClass( 'ys-before-focus')
				.addClass( 'ys-after-focus')
				.val(updates.nickname);

		if (updates)
			this.updates(updates);

		if (!this.prefs.inverse) {
			var postsDiv = $('#ys-posts')[0];
			postsDiv.scrollTop = postsDiv.scrollHeight;
		}
		
	},

	initForm: function() {
		this.d('In initForm');
		var postForm = 
			'<form id="ys-post-form"><fieldset>' +
				'<input id="ys-input-nickname" value="' + this.prefs.defaultNickname + '" type="text" accesskey="N" maxlength="' + this.prefs.nicknameLength + '" class="ys-before-focus" />' +
				'<input id="ys-input-message" value="' + this.prefs.defaultMessage + '" type="text" accesskey="M" maxlength="' + this.prefs.messageLength + '" class="ys-before-focus" />' +
				(this.prefs.showSubmit ? '<input id="ys-input-submit" value="' + this.prefs.defaultSubmit + '" accesskey="S" type="submit" />' : '') +
				(this.prefs.showCPLink ? '<a title="Launch Admin CP" id="ys-cp-launch" href="#">Admin CP</a>' : '') +
			'</fieldset></form>';

		var postsDiv = '<div id="ys-posts"></div>';

		if (this.prefs.inverse) $('#yshout').html(postForm + postsDiv);
		else $('#yshout').html(postsDiv + postForm);

		var self = this;

		var defaults = { 
			'ys-input-nickname': self.prefs.defaultNickname, 
			'ys-input-message': self.prefs.defaultMessage
		};

		var keypress = function(e) { 
			var key = window.event ? e.keyCode : e.which; 
			if (key == 13 || key == 3) {
				self.send.apply(self);
				return false;
			}
		};

		var focus = function() { 
			if (this.value == defaults[this.id])
				$(this).removeClass('ys-before-focus').val('');
		};

		var blur = function() { 
			if (this.value == '')
				$(this).addClass('ys-before-focus').val(defaults[this.id]); 
		};

		$('#ys-input-message').keypress(keypress).focus(focus).blur(blur);
		$('#ys-input-nickname').keypress(keypress).focus(focus).blur(blur);

		$('#ys-input-submit').click(function(){ self.send.apply(self) });
		$('#ys-post-form').submit(function(){ return false });
	},

	initRefresh: function() {
		var self = this;
		this.refreshTimer = setInterval(function() {
			self.ajax(self.updates, { reqType: 'refresh' });
		}, 3000);
	},

	initFlood: function() {
		this.d('in initFlood');
		var self = this;
		this.floodCount = 0;
		this.floodControl = false;

		this.floodTimer = setInterval(function() {
			self.floodCount = 0;
		}, this.prefs.floodTimeout);
	},

	initCP: function() {
		var self = this;

		$('#ys-cp-launch').click(function() {
			self.openCP.apply(self);
			return false;
		});
	},

	openCP: function(url) {
		var self = this;
		if (this.cpOpen) return;
		this.cpOpen = true;
		if (!url) url = this.options.yPath + 'cp/';

		$('body').append('<div id="ys-cp-overlay"></div><div id="ys-cp"><a title="Close Admin CP" href="#" id="ys-cp-close">Close</a><object id="cp-browser" data="' + url +'" type="text/html">Something went horribly wrong.</object></div>');

		
		var checkScroll = function() {
			var scrollTop = self.scrollTop();

			if (scrollTop != 0)
				$('#ys-cp-overlay').css('margin-top', scrollTop + 'px');
				$('#cp-browser').css('margin-top', scrollTop + 'px');
				$('#ys-cp-close').css('margin-top',  (scrollTop  - 290) + 'px');
		}

		checkScroll();
//		$(document).scroll(checkScroll);
		
		$('#ys-cp-overlay, #ys-cp-close').click(function() { 
			self.reload.apply(self, [true]);
			self.closeCP.apply(self);
			return false; 
		}); 
	},

	closeCP: function() {
		this.cpOpen = false;
		$('#ys-cp-overlay, #ys-cp').remove();
	},

	send: function() {
		if (!this.validate()) return;
		if (this.prefs.flood && this.floodControl) return;

		var  postNickname = $('#ys-input-nickname').val(), postMessage = $('#ys-input-message').val();

		if (postMessage == '/cp')
			this.openCP();
		else
			this.ajax(this.updates, {
				reqType: 'post',
				nickname: postNickname,
				message: postMessage
			});

		$('#ys-input-message').val('')

		if (this.prefs.flood) this.flood();
	},

	validate: function() {
		var nickname = $('#ys-input-nickname').val(),
				message = $('#ys-input-message').val(),
				error = false;

		// Check for auto-ban words
		if (this.prefs.censorWords.length > 0) {
			var banWords = this.prefs.censorWords.split(' ');
			
			for(var i = 0; i < banWords.length; i++) {
				if (nickname.indexOf(banWords[i]) > -1 || message.indexOf(banWords[i]) > -1) {
					if (this.prefs.censorAutoban) {
						this.ban('You have been banned for trying to say a word on the banlist!');
						this.reload();
						return false;
					} else {
						$('#ys-input-nickname').val($('#ys-input-nickname').val().sReplace(banWords[i], '*'.repeat(banWords[i].length)));
						$('#ys-input-message').val($('#ys-input-message').val().sReplace(banWords[i], '*'.repeat(banWords[i].length)));
					}
				}
			}
		}

		var showInvalid = function(input) {
			$(input).removeClass('ys-input-valid').addClass('ys-input-invalid')[0].focus();
			error = true;
		}

		var showValid = function(input) {
			$(input).removeClass('ys-input-invalid').addClass('ys-input-valid');
		}

		if (nickname == '' ||	nickname == this.prefs.defaultNickname)
			showInvalid('#ys-input-nickname');
		else
			showValid('#ys-input-nickname');

		if (message == '' || message == this.prefs.defaultMessage)
			showInvalid('#ys-input-message');
		else
			showValid('#ys-input-message');

		return !error;
	},

	flood: function() {
		var self = this;
		this.d('in flood');
		if (this.floodCount < this.prefs.floodMessages) {
			this.floodCount++;
			return;
		}

		this.floodAttempt++;
		this.disable();

		if (this.floodAttempt == this.prefs.autobanFlood)
			this.ban('You have been banned for flooding the shoutbox!');
			
		setTimeout(function() {
			self.floodCount = 0;
			self.enable.apply(self);
		}, this.prefs.floodDisable);
	},

	disable: function () {
		$('#ys-input-submit')[0].disabled = true;
		this.floodControl = true;
	},

	enable: function () {
		$('#ys-input-submit')[0].disabled = false;
		this.floodControl = false;
	},

	updates: function(updates) {
		if (!updates) return;

		if (updates.prefs) this.prefs = updates.prefs;
		if (updates.posts) this.posts(updates.posts);
	},

	posts: function(p) {
		for (var i = 0; i < p.length; i++)
			this.post(p[i]);

		this.truncate();
	},

	post: function(post) {
		var pad = function(n) { return n > 9 ? n : '0' + n; };
		var date = function(ts) { return new Date(ts * 1000); };
		var time = function(ts) { 
			var d = date(ts);
			var h = d.getHours(), m = d.getMinutes();

			if (self.prefs.timestamp == 12) {
				h = (h > 12 ? h - 12 : h);
				if (h == 0) h = 12;
			}

			return pad(h) + ':' + pad(m);
		};

		var dateStr = function(ts) {
			var t = date(ts);

		  var Y = t.getFullYear();
		  var M = t.getMonth();
		  var D = t.getDay();
		  var d = t.getDate();
		  var day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][D];
		  var mon = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
		             'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][M];

		  return day + ' ' + mon + '. ' + d + ', ' + Y;
		};

		var self = this;

		this.postNum++;
		var id = 'ys-post-' + this.postNum;
		post.message = this.links(post.message);
		post.message = this.smileys(post.message);
		post.message = this.bbcode(post.message);
		var html = 
			'<div id="' + id + '" class="ys-post' + (post.admin ? ' ys-admin-post' : '') + (post.banned ? ' ys-banned-post' : '') + '">' +
				(this.prefs.timestamp> 0 ? '<span class="ys-post-timestamp">' + time(post.timestamp) + '</span> ' : '') +
				'<span class="ys-post-nickname">' + post.nickname + this.prefs.nicknameSeparator + '</span> ' +
				'<span class=" ys-post-message">' + post.message + '</span> ' +
				'<span class="ys-post-info' + (this.prefs.info == 'overlay' ? ' ys-info-overlay' : ' ys-info-inline') + '">' + (post.adminInfo ? '<em>IP:</em> ' + post.adminInfo.ip + ', ' : '') + '<em>Posted:</em> ' + dateStr(post.timestamp) + ' at ' + time(post.timestamp)  + '.</span>' +
				'<span class="ys-admin-actions"><a title="Show post information" class="ys-info-link" href="#">Info</a>'  + (post.adminInfo ? ' | ' + (post.banned ? '<a title="Unban ' + post.nickname + '" class="ys-unban-link" href="#">Unban</a>' : '<a title="Ban ' + post.nickname + '" class="ys-ban-link" href="#">Ban</a>') : '') + '</span>' +
			'</div>';

		if (this.prefs.inverse) $('#ys-posts').prepend(html);
		else $('#ys-posts').append(html);

		$('#' + id)
			.find('.ys-info-link').toggle(function() {
				$('#' + id + ' .ys-post-info').css('display', 'block');
				this.innerHTML = 'Close Info';
				return false;
			}, function() {
				$('#' + id + ' .ys-post-info').css('display', 'none');
				this.innerHTML = 'Info';
				return false;
			}).end()
			.find('.ys-ban-link').click(function() {
				if (this.innerHTML == 'Banning...') return; 
	
				var pars = {
					reqType: 'ban',
					ip: post.adminInfo.ip
				};
	
				self.ajax(function(json) {
					if (json.error) {
						switch(json.error) {
							case 'admin':
								self.error('You\'re not an admin. Log in through the admin CP to ban people.');
								break;
							case 'already':
								self.reload();
								break;
						}
						return;
					}
					self.reload();
				}, pars);

				this.innerHTML = 'Banning...';
				return false;
			}).end()
			.find('.ys-unban-link').click(function() {
				if (this.innerHTML == 'Unbanning...') return;
	
				var pars = {
					reqType: 'unban',
					ip: post.adminInfo.ip
				};
	
				self.ajax(function(json) {
					if (json.error) {
						switch(json.error) {
							case 'admin':
								self.error('You\'re not an admin. Log in through the admin CP to ban people.');
								break;
							case 'already':
								self.reload();
								break;
						}
						return;
					}
					self.reload();
				}, pars);
	
				this.innerHTML = 'Unbanning...';
				return false;
			})

	},

	ban: function(reason) {
		var self = this;
		this.ajax(function(json) {
			if (json.error == false) {
				alert(reason)
				self.reload();
			}
		}, {reqType: 'banself' });
	},

	bbcode: function(s) {
		s = s.sReplace('[i]', '<i>');
		s = s.sReplace('[/i]', '</i>');
		s = s.sReplace('[I]', '<i>');
		s = s.sReplace('[/I]', '</i>');

		s = s.sReplace('[b]', '<b>');
		s = s.sReplace('[/b]', '</b>');
		s = s.sReplace('[B]', '<b>');
		s = s.sReplace('[/B]', '</b>');

		s = s.sReplace('[u]', '<u>');
		s = s.sReplace('[/u]', '</u>');
		s = s.sReplace('[U]', '<u>');
		s = s.sReplace('[/U]', '</u>');

		return s;
	},
	
	smileys: function(s) {
		var yp = this.options.yPath;
		
		var smile = function(str, smiley, image) {
			return str.sReplace(smiley, '<img src="' + yp + 'smileys/' + image + '" />');
		};

		s = smile(s, ':twisted:',  'twisted.gif');
		s = smile(s, ':cry:',  'cry.gif');
		s = smile(s, ':shock:',  'eek.gif');
		s = smile(s, ':evil:',  'evil.gif');
		s = smile(s, ':lol:',  'lol.gif');
		s = smile(s, ':mrgreen:',  'mrgreen.gif');
		s = smile(s, ':oops:',  'redface.gif');
		s = smile(s, ':roll:',  'rolleyes.gif');

		s = smile(s, ':?',  'confused.gif');
		s = smile(s, ':D',  'biggrin.gif');
		s = smile(s, '8)',  'cool.gif');
		s = smile(s, ':x',  'mad.gif');
		s = smile(s, ':|',  'neutral.gif');
		s = smile(s, ':P',  'razz.gif');
		s = smile(s, ':(',  'sad.gif');
		s = smile(s, ':)',  'smile.gif');
		s = smile(s, ':o',  'surprised.gif');
		s = smile(s, ';)',  'wink.gif');

		return s;
	},

	links: function(s) {
		return s.replace(/((https|http|ftp|ed2k):\/\/[\S]+)/gi, '<a  href="$1" target="_blank">$1</a>');
	},

	truncate: function(clearAll) {
		var truncateTo = clearAll ? 0 : this.prefs.truncate;
		var posts = $('div.ys-post').size();
		if (posts <= truncateTo) return;

		if (this.prefs.inverse)
			$('div.ys-post:gt(' + truncateTo + ')').remove();
		else
			$('div.ys-post:lt(' + (posts - truncateTo) + ')').remove();
	},

	reload: function(everything) {
		var self = this;

		if (everything) {
			this.ajax(function(json) { 
				$('#yshout').html(''); 
				clearInterval(this.refreshTimer);
				clearInterval(this.floodTimer);
				this.initialLoad(json); 
			}, { 
				reqType: 'init',
				yPath: this.options.yPath,
				log: this.options.log
			});
		} else {
			this.ajax(function(json) { this.truncate(true); this.updates(json); },{
				reqType: 'reload'
			});
		}
	},

	error: function(str) {
		alert(str);
	},

	json: function(parse) {
		this.d('In json: ' + parse);
		var json = eval('(' + parse + ')');
		if (!this.checkError(json)) return json;
	},

	checkError: function(json) {
		if (!json.yError) return false;

		this.d('Error: ' + json.yError);
		return true;
	},

	scrollTop: function (){
		var scrollTop;
		
		return window.pageYOffset ? window.pageYOffset :
		(document.documentElement && document.documentElement.scrollTop) ? document.documentElement.scrollTop :
		document.body ? document.body.scrollTop : null;
	},

	ajax: function(callback, pars) {
		pars = jQuery.extend({
			reqFor: 'shout'
		}, pars);

		var self = this;

		$.post(this.options.yPath + 'index.php', pars, function(parse) {
				if (parse)
					callback.apply(self, [self.json(parse)]);
				else
					callback.apply(self);
		});
	},

	d: function(message) {
		$('#debug').css('display', 'block').prepend('<p>' + message + '</p>');
		return message;
	}
};