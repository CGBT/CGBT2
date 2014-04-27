(function(e) {
	var f = {}, a, c = !1,
		b = document.documentElement,
		c = b.firstElementChild || b.firstChild,
		d = document.createElement("div");
	d.style.cssText = "position:absolute;top:-100em;width:1.1px";
	b.insertBefore(d, c);
	c = 0 !== (d.getBoundingClientRect().width || 1) % 1;
	b.removeChild(d);
	c || (b = /msie ([\w.]+)/.exec(navigator.userAgent.toLowerCase())) && (c = 8 == parseInt(b[1], 10) || 9 == parseInt(b[1], 10));
	a = c;
	var g = {};
	e.matchHeight = function(a, b, c) {
		var d = e(window),
			f = a && g[a];
		if (!f) {
			var f = g[a] = {
				id: a,
				elements: b,
				deepest: c,
				match: function() {
					var a = this.revert(),
						b = 0;
					e(this.elements).each(function() {
						b = Math.max(b, e(this).outerHeight())
					}).each(function(c) {
						var d = "outerHeight";
						"border-box" == a[c].css("box-sizing") && (d = "height");
						var g = e(this);
						c = a[c];
						d = c.height() + (b - g[d]());
						c.css("min-height", d + "px")
					})
				},
				revert: function() {
					var a = [],
						b = this.deepest;
					e(this.elements).each(function() {
						var c = b ? e(this).find(b + ":first") : e(this);
						a.push(c.css("min-height", ""))
					});
					return a
				},
				remove: function() {
					d.unbind("debouncedresize orientationchange", j);
					this.revert();
					delete g[this.id]
				}
			}, j = function() {
					f.match()
				};
			d.bind("debouncedresize orientationchange", j)
		}
		return f
	};
	e.matchWidth = function(b, c, d) {
		var f = e(window),
			k = b && g[b];
		if (!k) {
			if (a) return g[b] = {
				match: function() {},
				revert: function() {},
				remove: function() {}
			}, g[b];
			var k = g[b] = {
				id: b,
				elements: c,
				selector: d,
				match: function() {
					this.revert();
					e(this.elements).each(function() {
						var a = e(this),
							b = a.width(),
							c = a.children(d),
							f = 0;
						c.each(function(a) {
							a < c.length - 1 ? f += e(this).width() : e(this).width(b - f)
						})
					})
				},
				revert: function() {
					e(c).children(d).css("width", "")
				},
				remove: function() {
					f.unbind("debouncedresize orientationchange", j);
					this.revert();
					delete g[this.id]
				}
			}, j = function() {
					k.match()
				};
			f.bind("debouncedresize orientationchange", j)
		}
		return k
	};
	e.fn.matchHeight = function(a) {
		var b = 0,
			c = [];
		this.each(function() {
			var b = a ? e(this).find(a + ":first") : e(this);
			c.push(b);
			b.css("min-height", "")
		});
		this.each(function() {
			b = Math.max(b, e(this).outerHeight())
		});
		return this.each(function(a) {
			var d = e(this);
			a = c[a];
			d = a.height() + (b - d.outerHeight());
			a.css("min-height", d + "px")
		})
	};
	e.fn.matchWidth = function(a) {
		return this.each(function() {
			var b = e(this),
				c = b.children(a),
				d = 0;
			c.width(function(a, f) {
				return a < c.length - 1 ? (d += f, f) : b.width() - d
			})
		})
	};
	e.fn.smoothScroller = function(a) {
		a = e.extend({
			duration: 1E3,
			transition: "easeOutExpo"
		}, a);
		return this.each(function() {
			e(this).bind("click", function() {
				var b = this.hash,
					c = e(this.hash).offset().top;
				if (window.location.href.replace(window.location.hash, "") + b == this) return e("html:not(:animated),body:not(:animated)").animate({
					scrollTop: c
				}, a.duration, a.transition, function() {
					window.location.hash = b.replace("#", "")
				}), !1
			})
		})
	}
})(jQuery);
(function(e) {
	e.easing.jswing = e.easing.swing;
	e.extend(e.easing, {
		def: "easeOutQuad",
		swing: function(f, a, c, b, d) {
			return e.easing[e.easing.def](f, a, c, b, d)
		},
		easeInQuad: function(f, a, c, b, d) {
			return b * (a /= d) * a + c
		},
		easeOutQuad: function(f, a, c, b, d) {
			return -b * (a /= d) * (a - 2) + c
		},
		easeInOutQuad: function(f, a, c, b, d) {
			return 1 > (a /= d / 2) ? b / 2 * a * a + c : -b / 2 * (--a * (a - 2) - 1) + c
		},
		easeInCubic: function(f, a, c, b, d) {
			return b * (a /= d) * a * a + c
		},
		easeOutCubic: function(f, a, c, b, d) {
			return b * ((a = a / d - 1) * a * a + 1) + c
		},
		easeInOutCubic: function(f, a, c, b, d) {
			return 1 > (a /= d / 2) ? b / 2 * a * a * a + c : b / 2 * ((a -= 2) * a * a + 2) + c
		},
		easeInQuart: function(f, a, c, b, d) {
			return b * (a /= d) * a * a * a + c
		},
		easeOutQuart: function(f, a, c, b, d) {
			return -b * ((a = a / d - 1) * a * a * a - 1) + c
		},
		easeInOutQuart: function(f, a, c, b, d) {
			return 1 > (a /= d / 2) ? b / 2 * a * a * a * a + c : -b / 2 * ((a -= 2) * a * a * a - 2) + c
		},
		easeInQuint: function(f, a, c, b, d) {
			return b * (a /= d) * a * a * a * a + c
		},
		easeOutQuint: function(f, a, c, b, d) {
			return b * ((a = a / d - 1) * a * a * a * a + 1) + c
		},
		easeInOutQuint: function(f, a, c, b, d) {
			return 1 > (a /= d / 2) ? b / 2 * a * a * a * a * a + c : b / 2 * ((a -= 2) * a * a * a * a + 2) + c
		},
		easeInSine: function(f, a, c, b, d) {
			return -b * Math.cos(a / d * (Math.PI / 2)) + b + c
		},
		easeOutSine: function(f, a, c, b, d) {
			return b * Math.sin(a / d * (Math.PI / 2)) + c
		},
		easeInOutSine: function(f, a, c, b, d) {
			return -b / 2 * (Math.cos(Math.PI * a / d) - 1) + c
		},
		easeInExpo: function(f, a, c, b, d) {
			return 0 == a ? c : b * Math.pow(2, 10 * (a / d - 1)) + c
		},
		easeOutExpo: function(f, a, c, b, d) {
			return a == d ? c + b : b * (-Math.pow(2, -10 * a / d) + 1) + c
		},
		easeInOutExpo: function(f, a, c, b, d) {
			return 0 == a ? c : a == d ? c + b : 1 > (a /= d / 2) ? b / 2 * Math.pow(2, 10 * (a - 1)) + c : b / 2 * (-Math.pow(2, -10 * --a) + 2) + c
		},
		easeInCirc: function(f, a, c, b, d) {
			return -b * (Math.sqrt(1 - (a /= d) * a) - 1) + c
		},
		easeOutCirc: function(f, a, c, b, d) {
			return b * Math.sqrt(1 - (a = a / d - 1) * a) + c
		},
		easeInOutCirc: function(f, a, c, b, d) {
			return 1 > (a /= d / 2) ? -b / 2 * (Math.sqrt(1 - a * a) - 1) + c : b / 2 * (Math.sqrt(1 - (a -= 2) * a) + 1) + c
		},
		easeInElastic: function(f, a, c, b, d) {
			f = 1.70158;
			var g = 0,
				e = b;
			if (0 == a) return c;
			if (1 == (a /= d)) return c + b;
			g || (g = 0.3 * d);
			e < Math.abs(b) ? (e = b, f = g / 4) : f = g / (2 * Math.PI) * Math.asin(b / e);
			return -(e * Math.pow(2, 10 * (a -= 1)) * Math.sin((a * d - f) * 2 * Math.PI / g)) + c
		},
		easeOutElastic: function(f, a, c, b, d) {
			f = 1.70158;
			var g = 0,
				e = b;
			if (0 == a) return c;
			if (1 == (a /= d)) return c + b;
			g || (g = 0.3 * d);
			e < Math.abs(b) ? (e = b, f = g / 4) : f = g / (2 * Math.PI) * Math.asin(b / e);
			return e * Math.pow(2, -10 * a) * Math.sin((a * d - f) * 2 * Math.PI / g) + b + c
		},
		easeInOutElastic: function(f, a, c, b, d) {
			f = 1.70158;
			var e = 0,
				h = b;
			if (0 == a) return c;
			if (2 == (a /= d / 2)) return c + b;
			e || (e = d * 0.3 * 1.5);
			h < Math.abs(b) ? (h = b, f = e / 4) : f = e / (2 * Math.PI) * Math.asin(b / h);
			return 1 > a ? -0.5 * h * Math.pow(2, 10 * (a -= 1)) * Math.sin((a * d - f) * 2 * Math.PI / e) + c : 0.5 * h * Math.pow(2, -10 * (a -= 1)) * Math.sin((a * d - f) * 2 * Math.PI / e) + b + c
		},
		easeInBack: function(e, a, c, b, d, g) {
			void 0 == g && (g = 1.70158);
			return b * (a /= d) * a * ((g + 1) * a - g) + c
		},
		easeOutBack: function(e, a, c, b, d, g) {
			void 0 == g && (g = 1.70158);
			return b * ((a = a / d - 1) * a * ((g + 1) * a + g) + 1) + c
		},
		easeInOutBack: function(e, a, c, b, d, g) {
			void 0 == g && (g = 1.70158);
			return 1 > (a /= d / 2) ? b / 2 * a * a * (((g *= 1.525) + 1) * a - g) + c : b / 2 * ((a -= 2) * a * (((g *= 1.525) + 1) * a + g) + 2) + c
		},
		easeInBounce: function(f, a, c, b, d) {
			return b - e.easing.easeOutBounce(f, d - a, 0, b, d) + c
		},
		easeOutBounce: function(e, a, c, b, d) {
			return (a /= d) < 1 / 2.75 ? b * 7.5625 * a * a + c : a < 2 / 2.75 ? b * (7.5625 * (a -= 1.5 / 2.75) * a + 0.75) +
				c : a < 2.5 / 2.75 ? b * (7.5625 * (a -= 2.25 / 2.75) * a + 0.9375) + c : b * (7.5625 * (a -= 2.625 / 2.75) * a + 0.984375) + c
		},
		easeInOutBounce: function(f, a, c, b, d) {
			return a < d / 2 ? 0.5 * e.easing.easeInBounce(f, 2 * a, 0, b, d) + c : 0.5 * e.easing.easeOutBounce(f, 2 * a - d, 0, b, d) + 0.5 * b + c
		}
	})
})(jQuery);
(function(e) {
	function f(a) {
		var b = {}, c = /^jQuery\d+$/;
		e.each(a.attributes, function(a, d) {
			d.specified && !c.test(d.name) && (b[d.name] = d.value)
		});
		return b
	}

	function a() {
		var a = e(this);
		a.val() === a.attr("placeholder") && a.hasClass("placeholder") && (a.data("placeholder-password") ? a.hide().next().show().focus() : a.val("").removeClass("placeholder"))
	}

	function c() {
		var b, c = e(this);
		if ("" === c.val() || c.val() === c.attr("placeholder")) {
			if (c.is(":password")) {
				if (!c.data("placeholder-textinput")) {
					try {
						b = c.clone().attr({
							type: "text"
						})
					} catch (d) {
						b = e("<input>").attr(e.extend(f(c[0]), {
							type: "text"
						}))
					}
					b.removeAttr("name").data("placeholder-password", !0).bind("focus.placeholder", a);
					c.data("placeholder-textinput", b).before(b)
				}
				c = c.hide().prev().show()
			}
			c.addClass("placeholder").val(c.attr("placeholder"))
		} else c.removeClass("placeholder")
	}
	var b = "placeholder" in document.createElement("input"),
		d = "placeholder" in document.createElement("textarea");
	e.fn.placeholder = b && d ? function() {
		return this
	} : function() {
		return this.filter((b ? "textarea" : ":input") + "[placeholder]").bind("focus.placeholder", a).bind("blur.placeholder", c).trigger("blur.placeholder").end()
	};
	e(function() {
		e("form").bind("submit.placeholder", function() {
			var b = e(".placeholder", this).each(a);
			setTimeout(function() {
				b.each(c)
			}, 10)
		})
	});
	e(window).bind("unload.placeholder", function() {
		e(".placeholder").val("")
	})
})(jQuery);
(function(e) {
	if (!e.event.special.debouncedresize) {
		var f = e.event,
			a, c;
		a = f.special.debouncedresize = {
			setup: function() {
				e(this).on("resize", a.handler)
			},
			teardown: function() {
				e(this).off("resize", a.handler)
			},
			handler: function(b, d) {
				var e = this,
					h = arguments,
					l = function() {
						b.type = "debouncedresize";
						f.dispatch.apply(e, h)
					};
				c && clearTimeout(c);
				d ? l() : c = setTimeout(l, a.threshold)
			},
			threshold: 150
		}
	}
})(jQuery);
(function(a, f, g) {
	function d(a) {
		k.innerHTML = '&shy;<style media="' + a + '"> #mq-test-1 { width: 42px; }</style>';
		b.insertBefore(j, l);
		e = 42 == k.offsetWidth;
		b.removeChild(j);
		return e
	}

	function h(a) {
		var b = d(a.media);
		if (a._listeners && a.matches != b) {
			a.matches = b;
			for (var b = 0, c = a._listeners.length; b < c; b++) a._listeners[b](a)
		}
	}

	function c(a, b, c) {
		var d;
		return function() {
			var f = this,
				e = arguments,
				j = c && !d;
			clearTimeout(d);
			d = setTimeout(function() {
				d = null;
				c || a.apply(f, e)
			}, b);
			j && a.apply(f, e)
		}
	}
	if (!f.matchMedia || a.userAgent.match(/(iPhone|iPod|iPad)/i)) {
		var e, b = g.documentElement,
			l = b.firstElementChild || b.firstChild,
			j = g.createElement("body"),
			k = g.createElement("div");
		k.id = "mq-test-1";
		k.style.cssText = "position:absolute;top:-100em";
		j.style.background = "none";
		j.appendChild(k);
		f.matchMedia = function(a) {
			var b, e = [];
			b = {
				matches: d(a),
				media: a,
				_listeners: e,
				addListener: function(a) {
					"function" === typeof a && e.push(a)
				},
				removeListener: function(a) {
					for (var b = 0, c = e.length; b < c; b++) e[b] === a && delete e[b]
				}
			};
			f.addEventListener && f.addEventListener("resize", c(function() {
				h(b)
			}, 150), !1);
			g.addEventListener && g.addEventListener("orientationchange", function() {
				h(b)
			}, !1);
			return b
		}
	}
})(navigator, window, document);
(function(a, f, g) {
	if (!a.onMediaQuery) {
		var d = {}, h = f.matchMedia && f.matchMedia("only all").matches;
		a(g).ready(function() {
			for (var c in d) a(d[c]).trigger("init"), d[c].matches && a(d[c]).trigger("valid")
		});
		a(f).bind("load", function() {
			for (var c in d) d[c].matches && a(d[c]).trigger("valid")
		});
		a.onMediaQuery = function(c, e) {
			var b = c && d[c];
			b || (b = d[c] = f.matchMedia(c), b.supported = h, b.addListener(function() {
				a(b).trigger(b.matches ? "valid" : "invalid")
			}));
			a(b).bind(e);
			return b
		}
	}
})(jQuery, window, document);
(function(a, f, g) {
	a.fn.responsiveMenu = function(d) {
		function h(c, e) {
			var b = "";
			a(c).children().each(function() {
				var c = a(this);
				c.children("a, span.separator").each(function() {
					var d = a(this),
						f = d.is("a") ? d.attr("href") : "",
						g = d.is("span") ? " disabled" : "",
						m = 1 < e ? Array(e).join("-") + " " : "",
						p = d.find(".title").length ? d.find(".title").text() : d.text();
					b += '<option value="' + f + '" class="' + d.attr("class") + '"' + g + ">" + m + p + "</option>";
					c.find("ul.level" + (e + 1)).each(function() {
						b += h(this, e + 1)
					})
				})
			});
			return b
		}
		d = a.extend({
			current: ".current"
		}, d);
		return this.each(function() {
			var c = a(this),
				e = a("<select/>"),
				b = "";
			c.find("ul.menu").each(function() {
				b += h(this, 1)
			});
			e.append(b).change(function() {
				g.location.href = e.val()
			});
			e.find(d.current).attr("selected", !0);
			/iPhone|iPad|iPod/.test(f.platform) && (/OS [1-5]_[0-9_]* like Mac OS X/i.test(f.userAgent) && -1 < f.userAgent.indexOf("AppleWebKit")) && e.find(":disabled").remove();
			c.after(e)
		})
	}
})(jQuery, navigator, window);
(function(a, f) {
	function g() {
		c.setAttribute("content", b);
		l = !0
	}

	function d(b) {
		m = b.accelerationIncludingGravity;
		j = Math.abs(m.x);
		k = Math.abs(m.y);
		n = Math.abs(m.z);
		(!a.orientation || 180 === a.orientation) && (7 < j || (6 < n && 8 > k || 8 > n && 6 < k) && 5 < j) ? l && (c.setAttribute("content", e), l = !1) : l || g()
	}
	if (/iPhone|iPad|iPod/.test(f.platform) && /OS [1-5]_[0-9_]* like Mac OS X/i.test(f.userAgent) && -1 < f.userAgent.indexOf("AppleWebKit")) {
		var h = a.document;
		if (h.querySelector) {
			var c = h.querySelector("meta[name=viewport]"),
				h = c && c.getAttribute("content"),
				e = h + ",maximum-scale=1",
				b = h + ",maximum-scale=10",
				l = !0,
				j, k, n, m;
			c && (a.addEventListener("orientationchange", g, !1), a.addEventListener("devicemotion", d, !1))
		}
	}
})(this, navigator);
(function(e) {
	var a = function() {};
	e.extend(a.prototype, {
		name: "accordionMenu",
		options: {
			mode: "default",
			display: null,
			collapseall: !1,
			toggler: "span.level1.parent",
			content: "ul.level2",
			onaction: function() {}
		},
		initialize: function(a, b) {
			var b = e.extend({}, this.options, b),
				f = a.find(b.toggler);
			f.each(function(a) {
				var c = e(this),
					d = c.next(b.content).wrap("<div>").parent();
				d.data("height", d.height());
				c.hasClass("active") || a == b.display ? d.show() : d.hide().css("height", 0);
				c.bind("click", function() {
					g(a)
				})
			});
			var g = function(a) {
				var c = e(f.get(a)),
					d = e([]);
				c.hasClass("active") && (d = c, c = e([]));
				b.collapseall && (d = f.filter(".active"));
				switch (b.mode) {
					case "slide":
						c.next().stop().show().animate({
							height: c.next().data("height")
						}, 400);
						d.next().stop().animate({
							height: 0
						}, 400, function() {
							d.next().hide()
						});
						setTimeout(function() {
							b.onaction.apply(this, [c, d])
						}, 401);
						break;
					default:
						c.next().show().css("height", c.next().data("height")), d.next().hide().css("height", 0), b.onaction.apply(this, [c, d])
				}
				c.addClass("active").parent().addClass("active");
				d.removeClass("active").parent().removeClass("active")
			}
		}
	});
	e.fn[a.prototype.name] = function() {
		var h = arguments,
			b = h[0] ? h[0] : null;
		return this.each(function() {
			var f = e(this);
			if (a.prototype[b] && f.data(a.prototype.name) && "initialize" != b) f.data(a.prototype.name)[b].apply(f.data(a.prototype.name), Array.prototype.slice.call(h, 1));
			else if (!b || e.isPlainObject(b)) {
				var g = new a;
				a.prototype.initialize && g.initialize.apply(g, e.merge([f], h));
				f.data(a.prototype.name, g)
			} else e.error("Method " + b + " does not exist on jQuery." + a.name)
		})
	}
})(jQuery);
(function(d) {
	var e = function() {};
	d.extend(e.prototype, {
		name: "dropdownMenu",
		options: {
			mode: "default",
			itemSelector: "li",
			firstLevelSelector: "li.level1",
			dropdownSelector: "ul",
			duration: 600,
			remainTime: 800,
			remainClass: "remain",
			matchHeight: !0,
			transition: "easeOutExpo",
			withopacity: !0,
			centerDropdown: !1,
			reverseAnimation: !1,
			fixWidth: !1,
			fancy: null,
			boundary: d(window),
			boundarySelector: null
		},
		initialize: function(e, g) {
			this.options = d.extend({}, this.options, g);
			var a = this,
				h = null,
				t = !1;
			this.menu = e;
			this.dropdowns = [];
			this.options.withopacity = d.support.opacity ? this.options.withopacity : !1;
			if (this.options.fixWidth) {
				var u = 5;
				this.menu.children().each(function() {
					u += d(this).width()
				});
				this.menu.css("width", u)
			}
			this.options.matchHeight && this.matchHeight();
			this.menu.find(this.options.firstLevelSelector).each(function(s) {
				var l = d(this),
					b = l.find(a.options.dropdownSelector).css({
						overflow: "hidden"
					});
				if (b.length) {
					b.css("overflow", "hidden").show();
					b.data("init-width", parseFloat(b.css("width")));
					b.data("columns", b.find(".column").length);
					b.data("single-width", 1 < b.data("columns") ? b.data("init-width") / b.data("columns") : b.data("init-width"));
					var f = d("<div>").css({
						overflow: "hidden"
					}).append("<div></div>"),
						e = f.find("div:first");
					b.children().appendTo(e);
					f.appendTo(b);
					a.dropdowns.push({
						dropdown: b,
						div: f,
						innerdiv: e
					});
					b.hide()
				}
				l.bind({
					mouseenter: function() {
						t = !0;
						a.menu.trigger("menu:enter", [l, s]);
						if (h) {
							if (h.index == s) return;
							h.item.removeClass(a.options.remainClass);
							h.div.hide().parent().hide()
						}
						if (b.length) {
							b.parent().find("div").css({
								width: "",
								height: "",
								"min-width": "",
								"min-height": ""
							});
							b.removeClass("flip").removeClass("stack");
							l.addClass(a.options.remainClass);
							f.stop().show();
							b.show();
							a.options.centerDropdown && b.css("margin-left", -1 * (parseFloat(b.data("init-width")) / 2 - l.width() / 2));
							var c = b.css("width", b.data("init-width")).data("init-width");
							dpitem = a.options.boundarySelector ? d(a.options.boundarySelector, f) : f;
							boundary = {
								top: 0,
								left: 0,
								width: a.options.boundary.width()
							};
							e.css({
								"min-width": c
							});
							try {
								d.extend(boundary, a.options.boundary.offset())
							} catch (g) {}
							if (dpitem.offset().left < boundary.left || dpitem.offset().left + c - boundary.left > boundary.width) b.addClass("flip"), dpitem.offset().left < boundary.left && (b.removeClass("flip").addClass("stack"), c = b.css("width", b.data("single-width")).data("single-width"), e.css({
								"min-width": c
							}), a.options.centerDropdown && b.css({
								"margin-left": ""
							}));
							var m = parseFloat(b.height());
							switch (a.options.mode) {
								case "showhide":
									c = {
										width: c,
										height: m
									};
									f.css(c);
									break;
								case "diagonal":
									var j = {
										width: 0,
										height: 0
									}, c = {
											width: c,
											height: m
										};
									a.options.withopacity && (j.opacity = 0, c.opacity = 1);
									f.css(j).animate(c, a.options.duration, a.options.transition);
									break;
								case "height":
									j = {
										width: c,
										height: 0
									};
									c = {
										height: m
									};
									a.options.withopacity && (j.opacity = 0, c.opacity = 1);
									f.css(j).animate(c, a.options.duration, a.options.transition);
									break;
								case "width":
									j = {
										width: 0,
										height: m
									};
									c = {
										width: c
									};
									a.options.withopacity && (j.opacity = 0, c.opacity = 1);
									f.css(j).animate(c, a.options.duration, a.options.transition);
									break;
								case "slide":
									b.css({
										width: c,
										height: m
									});
									f.css({
										width: c,
										height: m,
										"margin-top": -1 * m
									}).animate({
										"margin-top": 0
									}, a.options.duration, a.options.transition);
									break;
								default:
									j = {
										width: c,
										height: m
									}, c = {}, a.options.withopacity && (j.opacity = 0, c.opacity = 1), f.css(j).animate(c, a.options.duration, a.options.transition)
							}
							h = {
								item: l,
								div: f,
								index: s
							}
						} else h = active = null
					},
					mouseleave: function(c) {
						if (c.srcElement && d(c.srcElement).hasClass("module")) return !1;
						t = !1;
						b.length ? window.setTimeout(function() {
							if (!(t || "none" == f.css("display"))) {
								a.menu.trigger("menu:leave", [l, s]);
								var b = function() {
									l.removeClass(a.options.remainClass);
									h = null;
									f.hide().parent().hide()
								};
								if (a.options.reverseAnimation) switch (a.options.mode) {
									case "showhide":
										b();
										break;
									case "diagonal":
										var c = {
											width: 0,
											height: 0
										};
										a.options.withopacity && (c.opacity = 0);
										f.stop().animate(c, a.options.duration, a.options.transition, function() {
											b()
										});
										break;
									case "height":
										c = {
											height: 0
										};
										a.options.withopacity && (c.opacity = 0);
										f.stop().animate(c, a.options.duration, a.options.transition, function() {
											b()
										});
										break;
									case "width":
										c = {
											width: 0
										};
										a.options.withopacity && (c.opacity = 0);
										f.stop().animate(c, a.options.duration, a.options.transition, function() {
											b()
										});
										break;
									case "slide":
										f.stop().animate({
											"margin-top": -1 * parseFloat(f.data("dpheight"))
										}, a.options.duration, a.options.transition, function() {
											b()
										});
										break;
									default:
										c = {}, a.options.withopacity && (c.opacity = 0), f.stop().animate(c, a.options.duration, a.options.transition, function() {
											b()
										})
								} else b()
							}
						}, a.options.remainTime) : a.menu.trigger("menu:leave")
					}
				})
			});
			if (this.options.fancy) {
				var k = d.extend({
					mode: "move",
					transition: "easeOutExpo",
					duration: 500,
					onEnter: null,
					onLeave: null
				}, this.options.fancy),
					n = this.menu.append('<div class="fancy bg1"><div class="fancy-1"><div class="fancy-2"><div class="fancy-3"></div></div></div></div>').find(".fancy:first").hide(),
					q = this.menu.find(".active:first"),
					p = null,
					v = function(a, d) {
						if (!d || !(p && a.get(0) == p.get(0))) n.stop().show().css("visibility", "visible"), "move" == k.mode ? !q.length && !d ? n.hide() : n.animate({
							left: a.position().left + "px",
							width: a.width() + "px"
						}, k.duration, k.transition) : d ? n.css({
							opacity: q ? 0 : 1,
							left: a.position().left + "px",
							width: a.width() + "px"
						}).animate({
							opacity: 1
						}, k.duration) : n.animate({
							opacity: 0
						}, k.duration), p = d ? a : null
					};
				this.menu.bind({
					"menu:enter": function(a, d, b) {
						v(d, !0);
						if (k.onEnter) k.onEnter(d, b, n)
					},
					"menu:leave": function(a, d, b) {
						v(q, !1);
						if (k.onLeave) k.onLeave(d, b, n)
					},
					"menu:fixfancy": function() {
						p && n.stop().show().css({
							left: p.position().left + "px",
							width: p.width() + "px"
						})
					}
				});
				q.length && "move" == k.mode && v(q, !0)
			}
		},
		matchHeight: function() {
			this.menu.find("li.level1.parent").each(function() {
				var e = 0;
				d(this).find("ul.level2").each(function() {
					var g = d(this),
						a = g.parents(".dropdown:first").show();
					e = Math.max(g.height(), e);
					a.hide()
				}).css("min-height", e)
			})
		}
	});
	d.fn[e.prototype.name] = function() {
		var r = arguments,
			g = r[0] ? r[0] : null;
		return this.each(function() {
			var a = d(this);
			if (e.prototype[g] && a.data(e.prototype.name) && "initialize" != g) a.data(e.prototype.name)[g].apply(a.data(e.prototype.name), Array.prototype.slice.call(r, 1));
			else if (!g || d.isPlainObject(g)) {
				var h = new e;
				e.prototype.initialize && h.initialize.apply(h, d.merge([a], r));
				a.data(e.prototype.name, h)
			} else d.error("Method " + g + " does not exist on jQuery." + e.name)
		})
	}
})(jQuery);
(function($) {
	$(document).ready(function() {
		var config = $('body').data('config') || {};
		$('.menu-sidebar').accordionMenu({
			mode: 'slide'
		});
		$('#menu').dropdownMenu({
			mode: 'slide',
			dropdownSelector: 'div.dropdown'
		});
		$('a[href="#page"]').smoothScroller({
			duration: 500
		});
	});
	$.onMediaQuery('(min-width: 960px)', {
		init: function() {
			if (!this.supported) this.matches = true;
		},
		valid: function() {
			$.matchWidth('grid-block', '.grid-block', '.grid-h').match();
			$.matchHeight('main', '#maininner, #sidebar-a, #sidebar-b').match();
			$.matchHeight('top-a', '#top-a .grid-h', '.deepest').match();
			$.matchHeight('top-b', '#top-b .grid-h', '.deepest').match();
			$.matchHeight('bottom-a', '#bottom-a .grid-h', '.deepest').match();
			$.matchHeight('bottom-b', '#bottom-b .grid-h', '.deepest').match();
			$.matchHeight('innertop', '#innertop .grid-h', '.deepest').match();
			$.matchHeight('innerbottom', '#innerbottom .grid-h', '.deepest').match();
		},
		invalid: function() {
			$.matchWidth('grid-block').remove();
			$.matchHeight('main').remove();
			$.matchHeight('top-a').remove();
			$.matchHeight('top-b').remove();
			$.matchHeight('bottom-a').remove();
			$.matchHeight('bottom-b').remove();
			$.matchHeight('innertop').remove();
			$.matchHeight('innerbottom').remove();
		}
	});
	var pairs = [];
	$.onMediaQuery('(min-width: 480px) and (max-width: 959px)', {
		valid: function() {
			$.matchHeight('sidebars', '.sidebars-2 #sidebar-a, .sidebars-2 #sidebar-b').match();
			pairs = [];
			$.each(['.sidebars-1 #sidebar-a > .grid-box', '.sidebars-1 #sidebar-b > .grid-box', '#top-a .grid-h', '#top-b .grid-h', '#bottom-a .grid-h', '#bottom-b .grid-h', '#innertop .grid-h', '#innerbottom .grid-h'], function(i, selector) {
				for (var i = 0, elms = $(selector), len = parseInt(elms.length / 2); i < len; i++) {
					var id = 'pair-' + pairs.length;
					$.matchHeight(id, [elms.get(i * 2), elms.get(i * 2 + 1)], '.deepest').match();
					pairs.push(id);
				}
			});
		},
		invalid: function() {
			$.matchHeight('sidebars').remove();
			$.each(pairs, function() {
				$.matchHeight(this).remove();
			});
		}
	});
	$.onMediaQuery('(max-width: 767px)', {
		valid: function() {
			var header = $('#header-responsive');
			if (!header.length) {
				header = $('<div id="header-responsive"/>').prependTo('#header');
				$('#logo').clone().removeAttr('id').addClass('logo').appendTo(header);
				$('.searchbox').first().clone().removeAttr('id').appendTo(header);
				$('#menu').responsiveMenu().next().addClass('menu-responsive').appendTo(header);
			}
		}
	});
})(jQuery);
jQuery(function(d) {
	function p() {
		g.remove(l);
		h.clear();
		k = new FSS.Plane(e.width * h.width, e.height * h.height, e.segments, e.slices);
		q = new FSS.Material(e.ambient, e.diffuse);
		l = new FSS.Mesh(k, q);
		g.add(l);
		var b, a;
		for (b = k.vertices.length - 1; 0 <= b; b--) a = k.vertices[b], a.anchor = FSS.Vector3.clone(a.position), a.step = FSS.Vector3.create(Math.randomInRange(0.2, 1), Math.randomInRange(0.2, 1), Math.randomInRange(0.2, 1)), a.time = Math.randomInRange(0, Math.PIM2)
	}

	function r(b, a) {
		h.setSize(b, a);
		FSS.Vector3.set(s, h.halfWidth, h.halfHeight);
		p()
	}

	function t() {
		m = Date.now() - u;
		var c, a, d, f, j, l = e.depth / 2;
		FSS.Vector3.copy(b.bounds, s);
		FSS.Vector3.multiplyScalar(b.bounds, b.xyScalar);
		FSS.Vector3.setZ(n, b.zOffset);
		c = Math.sin(b.step[0] * m * b.speed);
		a = Math.cos(b.step[1] * m * b.speed);
		FSS.Vector3.set(n, b.bounds[0] * c, b.bounds[1] * a, b.zOffset);
		for (c = g.lights.length - 1; 0 <= c; c--) a = g.lights[c], FSS.Vector3.setZ(a.position, b.zOffset), d = Math.clamp(FSS.Vector3.distanceSquared(a.position, n), b.minDistance, b.maxDistance), d = b.gravity * a.mass / d, FSS.Vector3.subtractVectors(a.force, n, a.position), FSS.Vector3.normalise(a.force), FSS.Vector3.multiplyScalar(a.force, d), FSS.Vector3.set(a.acceleration), FSS.Vector3.add(a.acceleration, a.force), FSS.Vector3.add(a.velocity, a.acceleration), FSS.Vector3.multiplyScalar(a.velocity, b.dampening), FSS.Vector3.limit(a.velocity, b.minLimit, b.maxLimit), FSS.Vector3.add(a.position, a.velocity);
		for (f = k.vertices.length - 1; 0 <= f; f--) j = k.vertices[f], c = Math.sin(j.time + j.step[0] * m * e.speed), a = Math.cos(j.time + j.step[1] * m * e.speed), d = Math.sin(j.time + j.step[2] * m * e.speed), FSS.Vector3.set(j.position, e.xRange * k.segmentWidth * c, e.yRange * k.sliceHeight * a, e.zRange * l * d - l), FSS.Vector3.add(j.position, j.anchor);
		k.dirty = !0;
		h.render(g);
		requestAnimationFrame(t)
	}

	function v() {
		r(f.offsetWidth, f.offsetHeight);
		h.render(g)
	}
	if (void 0 === window.orientation) {
		var c = document.createElement("canvas");
		if (!c.getContext || !c.getContext("2d")) d(".user-box").css({
			background: "url(" + WarpThemePath + "/images/background/polygon/polygon.jpg) no-repeat fixed 50% 50%",
			"background-size": "cover"
		});
		else {
			d(".user-box").prepend('<div id="animatedbg" style="height:140px;margin-left:-20px;opacity:0.6;"></div>');
			var e = {
				width: 1.5,
				height: 11.1,
				depth: 10,
				segments: 21,
				slices: 8,
				xRange: 0.2,
				yRange: 0.2,
				zRange: 0.2,
				ambient: "#005454",
				diffuse: "#666eee",
				speed: 5E-4
			}, b = {
					count: 2,
					xyScalar: 0.8,
					zOffset: 200,
					ambient: "#000066",
					diffuse: "#FF8800",
					speed: 8E-4,
					gravity: 600,
					dampening: 0.8,
					minLimit: 0,
					maxLimit: 15,
					minDistance: 10,
					maxDistance: 400,
					bounds: FSS.Vector3.create(),
					step: FSS.Vector3.create(Math.randomInRange(0.6, 1), Math.randomInRange(0.6, 1), Math.randomInRange(0.6, 1))
				}, m, u = Date.now(),
				s = FSS.Vector3.create(),
				n = FSS.Vector3.create(),
				f = document.getElementById("animatedbg"),
				h = new FSS.CanvasRenderer,
				g, l, k, q;
			h.setSize(f.offsetWidth, f.offsetHeight);
			f.appendChild(h.element);
			g = new FSS.Scene;
			p();
			for (d = g.lights.length - 1; 0 <= d; d--) c = g.lights[d], g.remove(c);
			h.clear();
			for (d = 0; d < b.count; d++) c = new FSS.Light(b.ambient, b.diffuse), c.ambientHex = c.ambient.format(), c.diffuseHex = c.diffuse.format(), g.add(c), c.mass = Math.randomInRange(0.5, 1), c.velocity = FSS.Vector3.create(), c.acceleration = FSS.Vector3.create(), c.force = FSS.Vector3.create();
			window.addEventListener("resize", v);
			r(f.offsetWidth, f.offsetHeight);
			t()
		}
	}
});
FSS = {
	FRONT: 0,
	BACK: 1,
	DOUBLE: 2,
	SVGNS: "http://www.w3.org/2000/svg"
};
FSS.Array = "function" == typeof Float32Array ? Float32Array : Array;
FSS.Utils = {
	isNumber: function(a) {
		return !isNaN(parseFloat(a)) && isFinite(a)
	}
};
(function() {
	for (var a = 0, b = ["ms", "moz", "webkit", "o"], c = 0; b.length > c && !window.requestAnimationFrame; ++c) window.requestAnimationFrame = window[b[c] + "RequestAnimationFrame"], window.cancelAnimationFrame = window[b[c] + "CancelAnimationFrame"] || window[b[c] + "CancelRequestAnimationFrame"];
	window.requestAnimationFrame || (window.requestAnimationFrame = function(b) {
		var c = (new Date).getTime(),
			f = Math.max(0, 16 - (c - a)),
			g = window.setTimeout(function() {
				b(c + f)
			}, f);
		return a = c + f, g
	});
	window.cancelAnimationFrame || (window.cancelAnimationFrame = function(a) {
		clearTimeout(a)
	})
})();
Math.PIM2 = 2 * Math.PI;
Math.PID2 = Math.PI / 2;
Math.randomInRange = function(a, b) {
	return a + (b - a) * Math.random()
};
Math.clamp = function(a, b, c) {
	return a = Math.max(a, b), Math.min(a, c)
};
FSS.Vector3 = {
	create: function(a, b, c) {
		var e = new FSS.Array(3);
		return this.set(e, a, b, c), e
	},
	clone: function(a) {
		var b = this.create();
		return this.copy(b, a), b
	},
	set: function(a, b, c, e) {
		return a[0] = b || 0, a[1] = c || 0, a[2] = e || 0, this
	},
	setX: function(a, b) {
		return a[0] = b || 0, this
	},
	setY: function(a, b) {
		return a[1] = b || 0, this
	},
	setZ: function(a, b) {
		return a[2] = b || 0, this
	},
	copy: function(a, b) {
		return a[0] = b[0], a[1] = b[1], a[2] = b[2], this
	},
	add: function(a, b) {
		return a[0] += b[0], a[1] += b[1], a[2] += b[2], this
	},
	addVectors: function(a, b, c) {
		return a[0] = b[0] + c[0], a[1] = b[1] + c[1], a[2] = b[2] + c[2], this
	},
	addScalar: function(a, b) {
		return a[0] += b, a[1] += b, a[2] += b, this
	},
	subtract: function(a, b) {
		return a[0] -= b[0], a[1] -= b[1], a[2] -= b[2], this
	},
	subtractVectors: function(a, b, c) {
		return a[0] = b[0] - c[0], a[1] = b[1] - c[1], a[2] = b[2] - c[2], this
	},
	subtractScalar: function(a, b) {
		return a[0] -= b, a[1] -= b, a[2] -= b, this
	},
	multiply: function(a, b) {
		return a[0] *= b[0], a[1] *= b[1], a[2] *= b[2], this
	},
	multiplyVectors: function(a, b, c) {
		return a[0] = b[0] * c[0], a[1] = b[1] * c[1], a[2] = b[2] * c[2], this
	},
	multiplyScalar: function(a, b) {
		return a[0] *= b, a[1] *= b, a[2] *= b, this
	},
	divide: function(a, b) {
		return a[0] /= b[0], a[1] /= b[1], a[2] /= b[2], this
	},
	divideVectors: function(a, b, c) {
		return a[0] = b[0] / c[0], a[1] = b[1] / c[1], a[2] = b[2] / c[2], this
	},
	divideScalar: function(a, b) {
		return 0 !== b ? (a[0] /= b, a[1] /= b, a[2] /= b) : (a[0] = 0, a[1] = 0, a[2] = 0), this
	},
	cross: function(a, b) {
		var c = a[0],
			e = a[1],
			d = a[2];
		return a[0] = e * b[2] - d * b[1], a[1] = d * b[0] - c * b[2], a[2] = c * b[1] - e * b[0], this
	},
	crossVectors: function(a, b, c) {
		return a[0] = b[1] * c[2] - b[2] * c[1], a[1] = b[2] * c[0] - b[0] * c[2], a[2] = b[0] * c[1] - b[1] * c[0], this
	},
	min: function(a, b) {
		return b > a[0] && (a[0] = b), b > a[1] && (a[1] = b), b > a[2] && (a[2] = b), this
	},
	max: function(a, b) {
		return a[0] > b && (a[0] = b), a[1] > b && (a[1] = b), a[2] > b && (a[2] = b), this
	},
	clamp: function(a, b, c) {
		return this.min(a, b), this.max(a, c), this
	},
	limit: function(a, b, c) {
		var e = this.length(a);
		return null !== b && b > e ? this.setLength(a, b) : null !== c && e > c && this.setLength(a, c), this
	},
	dot: function(a, b) {
		return a[0] * b[0] + a[1] * b[1] + a[2] * b[2]
	},
	normalise: function(a) {
		return this.divideScalar(a, this.length(a))
	},
	negate: function(a) {
		return this.multiplyScalar(a, -1)
	},
	distanceSquared: function(a, b) {
		var c = a[0] - b[0],
			e = a[1] - b[1],
			d = a[2] - b[2];
		return c * c + e * e + d * d
	},
	distance: function(a, b) {
		return Math.sqrt(this.distanceSquared(a, b))
	},
	lengthSquared: function(a) {
		return a[0] * a[0] + a[1] * a[1] + a[2] * a[2]
	},
	length: function(a) {
		return Math.sqrt(this.lengthSquared(a))
	},
	setLength: function(a, b) {
		var c = this.length(a);
		return 0 !== c && b !== c && this.multiplyScalar(a, b / c), this
	}
};
FSS.Vector4 = {
	create: function(a, b, c) {
		var e = new FSS.Array(4);
		return this.set(e, a, b, c), e
	},
	set: function(a, b, c, e, d) {
		return a[0] = b || 0, a[1] = c || 0, a[2] = e || 0, a[3] = d || 0, this
	},
	setX: function(a, b) {
		return a[0] = b || 0, this
	},
	setY: function(a, b) {
		return a[1] = b || 0, this
	},
	setZ: function(a, b) {
		return a[2] = b || 0, this
	},
	setW: function(a, b) {
		return a[3] = b || 0, this
	},
	add: function(a, b) {
		return a[0] += b[0], a[1] += b[1], a[2] += b[2], a[3] += b[3], this
	},
	multiplyVectors: function(a, b, c) {
		return a[0] = b[0] * c[0], a[1] = b[1] * c[1], a[2] = b[2] * c[2], a[3] = b[3] * c[3], this
	},
	multiplyScalar: function(a, b) {
		return a[0] *= b, a[1] *= b, a[2] *= b, a[3] *= b, this
	},
	min: function(a, b) {
		return b > a[0] && (a[0] = b), b > a[1] && (a[1] = b), b > a[2] && (a[2] = b), b > a[3] && (a[3] = b), this
	},
	max: function(a, b) {
		return a[0] > b && (a[0] = b), a[1] > b && (a[1] = b), a[2] > b && (a[2] = b), a[3] > b && (a[3] = b), this
	},
	clamp: function(a, b, c) {
		return this.min(a, b), this.max(a, c), this
	}
};
FSS.Color = function(a, b) {
	this.rgba = FSS.Vector4.create();
	this.hex = a || "#000000";
	this.opacity = FSS.Utils.isNumber(b) ? b : 1;
	this.set(this.hex, this.opacity)
};
FSS.Color.prototype = {
	set: function(a, b) {
		a = a.replace("#", "");
		var c = a.length / 3;
		return this.rgba[0] = parseInt(a.substring(0 * c, 1 * c), 16) / 255, this.rgba[1] = parseInt(a.substring(1 * c, 2 * c), 16) / 255, this.rgba[2] = parseInt(a.substring(2 * c, 3 * c), 16) / 255, this.rgba[3] = FSS.Utils.isNumber(b) ? b : this.rgba[3], this
	},
	hexify: function(a) {
		a = Math.ceil(255 * a).toString(16);
		return 1 === a.length && (a = "0" + a), a
	},
	format: function() {
		var a = this.hexify(this.rgba[0]),
			b = this.hexify(this.rgba[1]),
			c = this.hexify(this.rgba[2]);
		return this.hex = "#" +
			a + b + c, this.hex
	}
};
FSS.Object = function() {
	this.position = FSS.Vector3.create()
};
FSS.Object.prototype = {
	setPosition: function(a, b, c) {
		return FSS.Vector3.set(this.position, a, b, c), this
	}
};
FSS.Light = function(a, b) {
	FSS.Object.call(this);
	this.ambient = new FSS.Color(a || "#FFFFFF");
	this.diffuse = new FSS.Color(b || "#FFFFFF");
	this.ray = FSS.Vector3.create()
};
FSS.Light.prototype = Object.create(FSS.Object.prototype);
FSS.Vertex = function(a, b, c) {
	this.position = FSS.Vector3.create(a, b, c)
};
FSS.Vertex.prototype = {
	setPosition: function(a, b, c) {
		return FSS.Vector3.set(this.position, a, b, c), this
	}
};
FSS.Triangle = function(a, b, c) {
	this.a = a || new FSS.Vertex;
	this.b = b || new FSS.Vertex;
	this.c = c || new FSS.Vertex;
	this.vertices = [this.a, this.b, this.c];
	this.u = FSS.Vector3.create();
	this.v = FSS.Vector3.create();
	this.centroid = FSS.Vector3.create();
	this.normal = FSS.Vector3.create();
	this.color = new FSS.Color;
	this.polygon = document.createElementNS(FSS.SVGNS, "polygon");
	this.polygon.setAttributeNS(null, "stroke-linejoin", "round");
	this.polygon.setAttributeNS(null, "stroke-miterlimit", "1");
	this.polygon.setAttributeNS(null, "stroke-width", "1");
	this.computeCentroid();
	this.computeNormal()
};
FSS.Triangle.prototype = {
	computeCentroid: function() {
		return this.centroid[0] = this.a.position[0] + this.b.position[0] + this.c.position[0], this.centroid[1] = this.a.position[1] + this.b.position[1] + this.c.position[1], this.centroid[2] = this.a.position[2] + this.b.position[2] + this.c.position[2], FSS.Vector3.divideScalar(this.centroid, 3), this
	},
	computeNormal: function() {
		return FSS.Vector3.subtractVectors(this.u, this.b.position, this.a.position), FSS.Vector3.subtractVectors(this.v, this.c.position, this.a.position), FSS.Vector3.crossVectors(this.normal, this.u, this.v), FSS.Vector3.normalise(this.normal), this
	}
};
FSS.Geometry = function() {
	this.vertices = [];
	this.triangles = [];
	this.dirty = !1
};
FSS.Geometry.prototype = {
	update: function() {
		if (this.dirty) {
			var a, b;
			for (a = this.triangles.length - 1; 0 <= a; a--) b = this.triangles[a], b.computeCentroid(), b.computeNormal();
			this.dirty = !1
		}
		return this
	}
};
FSS.Plane = function(a, b, c, e) {
	FSS.Geometry.call(this);
	this.width = a || 100;
	this.height = b || 100;
	this.segments = c || 4;
	this.slices = e || 4;
	this.segmentWidth = this.width / this.segments;
	this.sliceHeight = this.height / this.slices;
	var d, f, g;
	c = [];
	d = -0.5 * this.width;
	f = 0.5 * this.height;
	for (a = 0; this.segments >= a; a++) {
		c.push([]);
		for (b = 0; this.slices >= b; b++) e = new FSS.Vertex(d + a * this.segmentWidth, f - b * this.sliceHeight), c[a].push(e), this.vertices.push(e)
	}
	for (a = 0; this.segments > a; a++)
		for (b = 0; this.slices > b; b++) e = c[a + 0][b + 0], d = c[a + 0][b +
			1
		], f = c[a + 1][b + 0], g = c[a + 1][b + 1], t0 = new FSS.Triangle(e, d, f), t1 = new FSS.Triangle(f, d, g), this.triangles.push(t0, t1)
};
FSS.Plane.prototype = Object.create(FSS.Geometry.prototype);
FSS.Material = function(a, b) {
	this.ambient = new FSS.Color(a || "#444444");
	this.diffuse = new FSS.Color(b || "#FFFFFF");
	this.slave = new FSS.Color
};
FSS.Mesh = function(a, b) {
	FSS.Object.call(this);
	this.geometry = a || new FSS.Geometry;
	this.material = b || new FSS.Material;
	this.side = FSS.FRONT;
	this.visible = !0
};
FSS.Mesh.prototype = Object.create(FSS.Object.prototype);
FSS.Mesh.prototype.update = function(a, b) {
	var c, e, d, f, g;
	if (this.geometry.update(), b)
		for (c = this.geometry.triangles.length - 1; 0 <= c; c--) {
			e = this.geometry.triangles[c];
			FSS.Vector4.set(e.color.rgba);
			for (d = a.length - 1; 0 <= d; d--) f = a[d], FSS.Vector3.subtractVectors(f.ray, f.position, e.centroid), FSS.Vector3.normalise(f.ray), g = FSS.Vector3.dot(e.normal, f.ray), this.side === FSS.FRONT ? g = Math.max(g, 0) : this.side === FSS.BACK ? g = Math.abs(Math.min(g, 0)) : this.side === FSS.DOUBLE && (g = Math.max(Math.abs(g), 0)), FSS.Vector4.multiplyVectors(this.material.slave.rgba, this.material.ambient.rgba, f.ambient.rgba), FSS.Vector4.add(e.color.rgba, this.material.slave.rgba), FSS.Vector4.multiplyVectors(this.material.slave.rgba, this.material.diffuse.rgba, f.diffuse.rgba), FSS.Vector4.multiplyScalar(this.material.slave.rgba, g), FSS.Vector4.add(e.color.rgba, this.material.slave.rgba);
			FSS.Vector4.clamp(e.color.rgba, 0, 1)
		}
	return this
};
FSS.Scene = function() {
	this.meshes = [];
	this.lights = []
};
FSS.Scene.prototype = {
	add: function(a) {
		return a instanceof FSS.Mesh && !~this.meshes.indexOf(a) ? this.meshes.push(a) : a instanceof FSS.Light && !~this.lights.indexOf(a) && this.lights.push(a), this
	},
	remove: function(a) {
		return a instanceof FSS.Mesh && ~this.meshes.indexOf(a) ? this.meshes.splice(this.meshes.indexOf(a), 1) : a instanceof FSS.Light && ~this.lights.indexOf(a) && this.lights.splice(this.lights.indexOf(a), 1), this
	}
};
FSS.Renderer = function() {
	this.halfHeight = this.halfWidth = this.height = this.width = 0
};
FSS.Renderer.prototype = {
	setSize: function(a, b) {
		return this.width !== a || this.height !== b ? (this.width = a, this.height = b, this.halfWidth = 0.5 * this.width, this.halfHeight = 0.5 * this.height, this) : void 0
	},
	clear: function() {
		return this
	},
	render: function() {
		return this
	}
};
FSS.CanvasRenderer = function() {
	FSS.Renderer.call(this);
	this.element = document.createElement("canvas");
	this.element.style.display = "block";
	this.context = this.element.getContext("2d");
	this.setSize(this.element.width, this.element.height)
};
FSS.CanvasRenderer.prototype = Object.create(FSS.Renderer.prototype);
FSS.CanvasRenderer.prototype.setSize = function(a, b) {
	return FSS.Renderer.prototype.setSize.call(this, a, b), this.element.width = a, this.element.height = b, this.context.setTransform(1, 0, 0, -1, this.halfWidth, this.halfHeight), this
};
FSS.CanvasRenderer.prototype.clear = function() {
	return FSS.Renderer.prototype.clear.call(this), this.context.clearRect(-this.halfWidth, -this.halfHeight, this.width, this.height), this
};
FSS.CanvasRenderer.prototype.render = function(a) {
	FSS.Renderer.prototype.render.call(this, a);
	var b, c, e, d, f;
	this.clear();
	this.context.lineJoin = "round";
	this.context.lineWidth = 1;
	for (b = a.meshes.length - 1; 0 <= b; b--)
		if (c = a.meshes[b], c.visible) {
			c.update(a.lights, !0);
			for (e = c.geometry.triangles.length - 1; 0 <= e; e--) d = c.geometry.triangles[e], f = d.color.format(), this.context.beginPath(), this.context.moveTo(d.a.position[0], d.a.position[1]), this.context.lineTo(d.b.position[0], d.b.position[1]), this.context.lineTo(d.c.position[0], d.c.position[1]), this.context.closePath(), this.context.strokeStyle = f, this.context.fillStyle = f, this.context.stroke(), this.context.fill()
		}
	return this
};
FSS.WebGLRenderer = function() {
	FSS.Renderer.call(this);
	this.element = document.createElement("canvas");
	this.element.style.display = "block";
	this.lights = this.vertices = null;
	return this.gl = this.getContext(this.element, {
		preserveDrawingBuffer: !1,
		premultipliedAlpha: !0,
		antialias: !0,
		stencil: !0,
		alpha: !0
	}), this.unsupported = !this.gl, this.unsupported ? "WebGL is not supported by your browser." : (this.gl.clearColor(0, 0, 0, 0), this.gl.enable(this.gl.DEPTH_TEST), this.setSize(this.element.width, this.element.height), void 0)
};
FSS.WebGLRenderer.prototype = Object.create(FSS.Renderer.prototype);
FSS.WebGLRenderer.prototype.getContext = function(a, b) {
	var c = !1;
	try {
		if (!(c = a.getContext("experimental-webgl", b))) throw "Error creating WebGL context.";
	} catch (e) {
		console.error(e)
	}
	return c
};
FSS.WebGLRenderer.prototype.setSize = function(a, b) {
	return FSS.Renderer.prototype.setSize.call(this, a, b), this.unsupported ? void 0 : (this.element.width = a, this.element.height = b, this.gl.viewport(0, 0, a, b), this)
};
FSS.WebGLRenderer.prototype.clear = function() {
	return FSS.Renderer.prototype.clear.call(this), this.unsupported ? void 0 : (this.gl.clear(this.gl.COLOR_BUFFER_BIT | this.gl.DEPTH_BUFFER_BIT), this)
};
FSS.WebGLRenderer.prototype.render = function(a) {
	if (FSS.Renderer.prototype.render.call(this, a), !this.unsupported) {
		var b, c, e, d, f, g, n, h, j, l, p;
		e = !1;
		var m = a.lights.length,
			k = 0;
		if (this.clear(), this.lights !== m) {
			if (this.lights = m, !(0 < this.lights)) return;
			this.buildProgram(m)
		}
		if (this.program) {
			for (b = a.meshes.length - 1; 0 <= b; b--) c = a.meshes[b], c.geometry.dirty && (e = !0), c.update(a.lights, !1), k += 3 * c.geometry.triangles.length;
			if (e || this.vertices !== k)
				for (g in this.vertices = k, this.program.attributes) {
					h = this.program.attributes[g];
					h.data = new FSS.Array(k * h.size);
					j = 0;
					for (b = a.meshes.length - 1; 0 <= b; b--) {
						c = a.meshes[b];
						e = 0;
						for (d = c.geometry.triangles.length; d > e; e++) {
							f = c.geometry.triangles[e];
							l = 0;
							for (p = f.vertices.length; p > l; l++) {
								switch (vertex = f.vertices[l], g) {
									case "side":
										this.setBufferData(j, h, c.side);
										break;
									case "position":
										this.setBufferData(j, h, vertex.position);
										break;
									case "centroid":
										this.setBufferData(j, h, f.centroid);
										break;
									case "normal":
										this.setBufferData(j, h, f.normal);
										break;
									case "ambient":
										this.setBufferData(j, h, c.material.ambient.rgba);
										break;
									case "diffuse":
										this.setBufferData(j, h, c.material.diffuse.rgba)
								}
								j++
							}
						}
					}
					this.gl.bindBuffer(this.gl.ARRAY_BUFFER, h.buffer);
					this.gl.bufferData(this.gl.ARRAY_BUFFER, h.data, this.gl.DYNAMIC_DRAW);
					this.gl.enableVertexAttribArray(h.location);
					this.gl.vertexAttribPointer(h.location, h.size, this.gl.FLOAT, !1, 0, 0)
				}
			this.setBufferData(0, this.program.uniforms.resolution, [this.width, this.height, this.width]);
			for (b = m - 1; 0 <= b; b--) c = a.lights[b], this.setBufferData(b, this.program.uniforms.lightPosition, c.position), this.setBufferData(b, this.program.uniforms.lightAmbient, c.ambient.rgba), this.setBufferData(b, this.program.uniforms.lightDiffuse, c.diffuse.rgba);
			for (n in this.program.uniforms) switch (h = this.program.uniforms[n], b = h.location, a = h.data, h.structure) {
				case "3f":
					this.gl.uniform3f(b, a[0], a[1], a[2]);
					break;
				case "3fv":
					this.gl.uniform3fv(b, a);
					break;
				case "4fv":
					this.gl.uniform4fv(b, a)
			}
		}
		return this.gl.drawArrays(this.gl.TRIANGLES, 0, this.vertices), this
	}
};
FSS.WebGLRenderer.prototype.setBufferData = function(a, b, c) {
	if (FSS.Utils.isNumber(c)) b.data[a * b.size] = c;
	else
		for (var e = c.length - 1; 0 <= e; e--) b.data[a * b.size + e] = c[e]
};
FSS.WebGLRenderer.prototype.buildProgram = function(a) {
	if (!this.unsupported) {
		var b = FSS.WebGLRenderer.VS(a),
			c = FSS.WebGLRenderer.FS(a),
			e = b + c;
		if (!this.program || this.program.code !== e) {
			var d = this.gl.createProgram(),
				b = this.buildShader(this.gl.VERTEX_SHADER, b),
				c = this.buildShader(this.gl.FRAGMENT_SHADER, c);
			return (this.gl.attachShader(d, b), this.gl.attachShader(d, c), this.gl.linkProgram(d), !this.gl.getProgramParameter(d, this.gl.LINK_STATUS)) ? (a = this.gl.getError(), d = this.gl.getProgramParameter(d, this.gl.VALIDATE_STATUS), console.error("Could not initialise shader.\nVALIDATE_STATUS: " + d + "\nERROR: " + a), null) : (this.gl.deleteShader(c), this.gl.deleteShader(b), d.code = e, d.attributes = {
				side: this.buildBuffer(d, "attribute", "aSide", 1, "f"),
				position: this.buildBuffer(d, "attribute", "aPosition", 3, "v3"),
				centroid: this.buildBuffer(d, "attribute", "aCentroid", 3, "v3"),
				normal: this.buildBuffer(d, "attribute", "aNormal", 3, "v3"),
				ambient: this.buildBuffer(d, "attribute", "aAmbient", 4, "v4"),
				diffuse: this.buildBuffer(d, "attribute", "aDiffuse", 4, "v4")
			}, d.uniforms = {
				resolution: this.buildBuffer(d, "uniform", "uResolution", 3, "3f", 1),
				lightPosition: this.buildBuffer(d, "uniform", "uLightPosition", 3, "3fv", a),
				lightAmbient: this.buildBuffer(d, "uniform", "uLightAmbient", 4, "4fv", a),
				lightDiffuse: this.buildBuffer(d, "uniform", "uLightDiffuse", 4, "4fv", a)
			}, this.program = d, this.gl.useProgram(this.program), d)
		}
	}
};
FSS.WebGLRenderer.prototype.buildShader = function(a, b) {
	if (!this.unsupported) {
		var c = this.gl.createShader(a);
		return this.gl.shaderSource(c, b), this.gl.compileShader(c), this.gl.getShaderParameter(c, this.gl.COMPILE_STATUS) ? c : (console.error(this.gl.getShaderInfoLog(c)), null)
	}
};
FSS.WebGLRenderer.prototype.buildBuffer = function(a, b, c, e, d, f) {
	d = {
		buffer: this.gl.createBuffer(),
		size: e,
		structure: d,
		data: null
	};
	switch (b) {
		case "attribute":
			d.location = this.gl.getAttribLocation(a, c);
			break;
		case "uniform":
			d.location = this.gl.getUniformLocation(a, c)
	}
	return f && (d.data = new FSS.Array(f * e)), d
};
FSS.WebGLRenderer.VS = function(a) {
	return ["precision mediump float;", "#define LIGHTS " + a, "attribute float aSide;\nattribute vec3 aPosition;\nattribute vec3 aCentroid;\nattribute vec3 aNormal;\nattribute vec4 aAmbient;\nattribute vec4 aDiffuse;\nuniform vec3 uResolution;\nuniform vec3 uLightPosition[LIGHTS];\nuniform vec4 uLightAmbient[LIGHTS];\nuniform vec4 uLightDiffuse[LIGHTS];\nvarying vec4 vColor;\nvoid main() {\nvColor = vec4(0.0);\nvec3 position = aPosition / uResolution * 2.0;\nfor (int i = 0; i < LIGHTS; i++) {\nvec3 lightPosition = uLightPosition[i];\nvec4 lightAmbient = uLightAmbient[i];\nvec4 lightDiffuse = uLightDiffuse[i];\nvec3 ray = normalize(lightPosition - aCentroid);\nfloat illuminance = dot(aNormal, ray);\nif (aSide == 0.0) {\nilluminance = max(illuminance, 0.0);\n} else if (aSide == 1.0) {\nilluminance = abs(min(illuminance, 0.0));\n} else if (aSide == 2.0) {\nilluminance = max(abs(illuminance), 0.0);\n}\nvColor += aAmbient * lightAmbient;\nvColor += aDiffuse * lightDiffuse * illuminance;\n}\nvColor = clamp(vColor, 0.0, 1.0);\ngl_Position = vec4(position, 1.0);\n}"].join("\n")
};
FSS.WebGLRenderer.FS = function() {
	return "precision mediump float;\nvarying vec4 vColor;\nvoid main() {\ngl_FragColor = vColor;\n}"
};
FSS.SVGRenderer = function() {
	FSS.Renderer.call(this);
	this.element = document.createElementNS(FSS.SVGNS, "svg");
	this.element.setAttribute("xmlns", FSS.SVGNS);
	this.element.setAttribute("version", "1.1");
	this.element.style.display = "block";
	this.setSize(300, 150)
};
FSS.SVGRenderer.prototype = Object.create(FSS.Renderer.prototype);
FSS.SVGRenderer.prototype.setSize = function(a, b) {
	return FSS.Renderer.prototype.setSize.call(this, a, b), this.element.setAttribute("width", a), this.element.setAttribute("height", b), this
};
FSS.SVGRenderer.prototype.clear = function() {
	FSS.Renderer.prototype.clear.call(this);
	for (var a = this.element.childNodes.length - 1; 0 <= a; a--) this.element.removeChild(this.element.childNodes[a]);
	return this
};
FSS.SVGRenderer.prototype.render = function(a) {
	FSS.Renderer.prototype.render.call(this, a);
	var b, c, e, d, f, g;
	for (b = a.meshes.length - 1; 0 <= b; b--)
		if (c = a.meshes[b], c.visible) {
			c.update(a.lights, !0);
			for (e = c.geometry.triangles.length - 1; 0 <= e; e--) d = c.geometry.triangles[e], d.polygon.parentNode !== this.element && this.element.appendChild(d.polygon), f = this.formatPoint(d.a) + " ", f += this.formatPoint(d.b) + " ", f += this.formatPoint(d.c), g = this.formatStyle(d.color.format()), d.polygon.setAttributeNS(null, "points", f), d.polygon.setAttributeNS(null, "style", g)
		}
	return this
};
FSS.SVGRenderer.prototype.formatPoint = function(a) {
	return this.halfWidth + a.position[0] + "," + (this.halfHeight - a.position[1])
};
FSS.SVGRenderer.prototype.formatStyle = function(a) {
	return "fill:" + a + ";" + ("stroke:" + a + ";")
};