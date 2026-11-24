/* Designer Designs — theme interactions. No build step, no dependencies beyond jQuery (WooCommerce events). */
(function ($) {
	'use strict';

	var D = window.DD || { i18n: {} };
	var reduce = false, touch = false;
	try {
		reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		touch = window.matchMedia('(hover: none)').matches;
	} catch (e) {}
	var store = {
		get: function (k, s) { try { return (s ? sessionStorage : localStorage).getItem(k); } catch (e) { return null; } },
		set: function (k, v, s) { try { (s ? sessionStorage : localStorage).setItem(k, v); } catch (e) {} }
	};
	var qs = function (sel, root) { return (root || document).querySelector(sel); };
	var qsa = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };
	var money = function (n) {
		return (D.currency || '£') + Number(n).toLocaleString('en-GB', { minimumFractionDigits: n % 1 ? 2 : 0, maximumFractionDigits: 2 });
	};

	/* ---------------- Toast ---------------- */
	var toastEl = qs('[data-dd-toast]'), toastT;
	function toast(msg) {
		if (!toastEl) return;
		toastEl.textContent = msg;
		toastEl.classList.add('is-on');
		clearTimeout(toastT);
		toastT = setTimeout(function () { toastEl.classList.remove('is-on'); }, 2600);
	}

	/* ---------------- Announcement bar: one message at a time on small screens ---------------- */
	(function () {
		var bar = qs('[data-dd-announce]');
		if (!bar) return;
		var items = qsa('span', bar), i = 0;
		if (items.length < 2) return;
		bar.classList.add('is-rotating');
		items[0].classList.add('is-on');
		if (reduce) return;
		setInterval(function () {
			items[i].classList.remove('is-on');
			i = (i + 1) % items.length;
			items[i].classList.add('is-on');
		}, 4500);
	})();

	/* ---------------- Mega menu ---------------- */
	(function () {
		var header = qs('[data-dd-header]');
		if (!header) return;
		var btns = qsa('[data-dd-mega]', header);
		var openKey = null, tOpen, tClose;

		function set(key) {
			openKey = key;
			btns.forEach(function (b) { b.setAttribute('aria-expanded', String(b.dataset.ddMega === key)); });
			qsa('[data-dd-panel]', header).forEach(function (p) { p.classList.toggle('is-open', p.dataset.ddPanel === key); });
		}
		btns.forEach(function (b) {
			b.addEventListener('click', function () { set(openKey === b.dataset.ddMega ? null : b.dataset.ddMega); });
			b.addEventListener('mouseenter', function () {
				clearTimeout(tClose); clearTimeout(tOpen);
				tOpen = setTimeout(function () { set(b.dataset.ddMega); }, openKey ? 0 : 90);
			});
			b.addEventListener('keydown', function (e) {
				if (e.key === 'ArrowDown') {
					e.preventDefault(); set(b.dataset.ddMega);
					var first = qs('[data-dd-panel="' + b.dataset.ddMega + '"] a', header);
					if (first) first.focus();
				}
			});
		});
		header.addEventListener('mouseleave', function () {
			clearTimeout(tOpen);
			tClose = setTimeout(function () { set(null); }, 220);
		});
		header.addEventListener('mouseenter', function () { clearTimeout(tClose); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && openKey) {
				var b = qs('[data-dd-mega="' + openKey + '"]', header);
				set(null); if (b) b.focus();
			}
		});
		document.addEventListener('click', function (e) { if (openKey && !header.contains(e.target)) set(null); });
		header.addEventListener('focusout', function () {
			setTimeout(function () { if (!header.contains(document.activeElement)) set(null); }, 0);
		});

		// Mobile nav + search
		var mBtn = qs('[data-dd-mobnav]'), mNav = qs('#dd-mobnav');
		if (mBtn && mNav) mBtn.addEventListener('click', function () {
			var on = !mNav.classList.contains('is-open');
			mNav.classList.toggle('is-open', on);
			mBtn.setAttribute('aria-expanded', String(on));
		});
		var sBtn = qs('[data-dd-search-toggle]'), sBox = qs('#dd-search');
		if (sBtn && sBox) sBtn.addEventListener('click', function () {
			var on = sBox.hasAttribute('hidden');
			if (on) sBox.removeAttribute('hidden'); else sBox.setAttribute('hidden', '');
			sBtn.setAttribute('aria-expanded', String(on));
			if (on) { set(null); var f = qs('input[type=search]', sBox); if (f) f.focus(); }
		});
	})();

	/* ---------------- Basket drawer ---------------- */
	var drawer = qs('[data-dd-drawer]'), lastFocus = null;
	function openDrawer() {
		if (!drawer) return;
		lastFocus = document.activeElement;
		drawer.classList.add('is-open');
		drawer.setAttribute('aria-hidden', 'false');
		document.documentElement.style.overflow = 'hidden';
		var c = qs('.dd-drawer__head [data-dd-drawer-close]', drawer);
		setTimeout(function () { if (c) c.focus(); }, 60);
	}
	function closeDrawer() {
		if (!drawer || !drawer.classList.contains('is-open')) return;
		drawer.classList.remove('is-open');
		drawer.setAttribute('aria-hidden', 'true');
		document.documentElement.style.overflow = '';
		if (lastFocus && lastFocus.focus) lastFocus.focus();
	}
	document.addEventListener('click', function (e) {
		if (e.target.closest('[data-dd-drawer-open]')) { e.preventDefault(); openDrawer(); }
		if (e.target.closest('[data-dd-drawer-close]')) { e.preventDefault(); closeDrawer(); }
	});
	document.addEventListener('keydown', function (e) {
		if (!drawer || !drawer.classList.contains('is-open')) return;
		if (e.key === 'Escape') closeDrawer();
		if (e.key === 'Tab') { // keep focus inside the panel
			var f = qsa('a[href],button:not([disabled]),input,select', qs('.dd-drawer__panel', drawer)).filter(function (n) { return n.offsetParent !== null; });
			if (!f.length) return;
			if (e.shiftKey && document.activeElement === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
			else if (!e.shiftKey && document.activeElement === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
		}
	});

	// WooCommerce events: open the drawer after a main add, a toast for small extras.
	$(document.body).on('added_to_cart', function (e, fragments, hash, $btn) {
		var name = '';
		if ($btn && $btn.length) {
			if ($btn.hasClass('dd-extra__add')) {
				var card = $btn.closest('.dd-extra');
				name = $.trim(card.find('a').eq(1).text());
				$btn.addClass('added').text(D.i18n.addedShort || 'Added');
				if (!drawer || !drawer.classList.contains('is-open')) { toast(name + ' ' + (D.i18n.added || 'added to your basket')); return; }
				return;
			}
		}
		openDrawer();
	});

	/* ---------------- AJAX add to basket on the product page ---------------- */
	$(document).on('submit', '[data-dd-buyform] form.cart', function (e) {
		var $f = $(this);
		if (!window.wc_add_to_cart_params) return; // fall back to normal submit
		var $btn = $f.find('.single_add_to_cart_button');
		if ($btn.is('.disabled')) return;
		var vid = $f.find('input[name=variation_id]').val();
		var pid = vid && vid !== '0' ? vid : ($f.find('[name=add-to-cart]').val() || $btn.val());
		if (!pid) return;
		e.preventDefault();
		var data = { product_id: pid, quantity: $f.find('input.qty').val() || 1 };
		$f.find('select[name^=attribute_]').each(function () { data[this.name] = this.value; });
		$btn.addClass('loading').attr('aria-busy', 'true');
		$.post(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'), data)
			.done(function (res) {
				if (!res || res.error) { $f.off('submit'); $f.trigger('submit'); return; }
				$(document.body).trigger('added_to_cart', [res.fragments, res.cart_hash, $btn]);
			})
			.always(function () { $btn.removeClass('loading').removeAttr('aria-busy'); });
	});

	/* ---------------- Opening (client 24/11): a black screen with one point of light.
	   Touching the light draws the gold crack, the dark surface breaks open and the light reveals the first piece. ---------------- */
	(function () {
		var hero = qs('[data-dd-hero]');
		var curtain = qs('[data-dd-curtain]');
		function light() { if (hero) hero.classList.add('is-on'); document.dispatchEvent(new CustomEvent('dd:lit')); }
		if (!curtain || reduce || store.get('ddIntro', true)) { light(); return; }

		var root = document.documentElement, opened = false, timers = [];
		var later = function (fn, ms) { timers.push(setTimeout(fn, ms)); };
		var crack = []; try { crack = JSON.parse(curtain.dataset.crack || '[]'); } catch (e) {}
		curtain.hidden = false;
		root.style.overflow = 'hidden';
		if (hero) hero.classList.remove('is-on');
		var btn = qs('[data-dd-curtain-open]', curtain);
		var seamC = qs('.dd-curtain__seam--c', curtain), spark = qs('.dd-curtain__spark', curtain);
		var whisper = qs('[data-dd-whisper]', curtain);

		// The line arrives letter by letter, like light finding its way in.
		if (whisper) {
			var text = whisper.textContent; whisper.textContent = '';
			whisper.setAttribute('aria-label', text);
			text.split('').forEach(function (ch, i) {
				var s = document.createElement('span'); s.textContent = ch; s.setAttribute('aria-hidden', 'true');
				s.style.transitionDelay = (600 + i * 45) + 'ms'; whisper.appendChild(s);
			});
		}
		later(function () { curtain.classList.add('is-awake'); }, 60);
		later(function () { curtain.classList.add('is-ready'); if (btn) btn.focus({ preventScroll: true }); }, 2200);
		later(function () { curtain.classList.add('is-late'); }, 4200);

		function px(pt) { return [pt[0] / 100 * innerWidth, pt[1] / 100 * innerHeight]; }
		function pointAt(k) {
			if (!crack.length) return [innerWidth / 2, k * innerHeight];
			var y = -2 + k * 104, i = 0;
			while (i < crack.length - 2 && crack[i + 1][1] < y) i++;
			var a = crack[i], b = crack[i + 1], f = Math.max(0, Math.min(1, (y - a[1]) / ((b[1] - a[1]) || 1)));
			return px([a[0] + (b[0] - a[0]) * f, y]);
		}
		function drawCrack(ms, then) {
			var t0 = null;
			function step(t) {
				if (!t0) t0 = t;
				var k = Math.min(1, (t - t0) / ms), e = 1 - Math.pow(1 - k, 3);
				seamC.style.setProperty('--draw', (e * 104) + '%');
				var p = pointAt(e);
				spark.style.transform = 'translate(' + p[0] + 'px,' + p[1] + 'px)';
				spark.style.opacity = k < 1 ? 1 : 0;
				if (k < 1) requestAnimationFrame(step); else then();
			}
			requestAnimationFrame(step);
		}
		function done() {
			timers.forEach(clearTimeout);
			curtain.hidden = true;
			root.style.overflow = '';
			document.removeEventListener('keydown', onKey);
			window.removeEventListener('wheel', onGesture);
			window.removeEventListener('touchmove', onGesture);
		}
		function sparks() {
			var pts = crack.slice(1, -1);
			for (var i = 0; i < 30; i++) {
				var c = pts[(Math.random() * pts.length) | 0] || [50, 50], p = px(c);
				var d = document.createElement('span'); d.className = 'dd-curtain__p';
				d.style.left = p[0] + 'px'; d.style.top = p[1] + 'px';
				curtain.appendChild(d);
				var ang = Math.random() * Math.PI * 2, dist = 80 + Math.random() * 240;
				var anim = d.animate([
					{ transform: 'translate(0,0) scale(1)', opacity: 1 },
					{ transform: 'translate(' + Math.cos(ang) * dist + 'px,' + (Math.sin(ang) * dist + 60) + 'px) scale(0)', opacity: 0 }
				], { duration: 800 + Math.random() * 900, easing: 'cubic-bezier(.16,.84,.24,1)' });
				anim.onfinish = (function (el) { return function () { el.remove(); }; })(d);
			}
		}
		function heroLayer() {
			var slide = hero ? qs('.dd-feat__slide.is-current', hero) : null, img = slide ? qs('img', slide) : null;
			if (!img) return null;
			var layer = document.createElement('div'); layer.className = 'dd-curtain__hero';
			var im = document.createElement('img'); im.src = img.currentSrc || img.src; im.alt = '';
			layer.appendChild(im);
			layer.style.cssText = 'left:0;top:0;width:' + innerWidth + 'px;height:' + innerHeight + 'px';
			curtain.insertBefore(layer, curtain.firstChild);
			return { layer: layer, img: im, slide: slide };
		}
		function open(instant) {
			if (opened) return;
			opened = true;
			store.set('ddIntro', '1', true);
			if (instant) { done(); light(); return; }
			curtain.classList.add('is-go');
			// 1. The crack draws itself through the dark, a spark running ahead of it.
			drawCrack(820, function () {
				var h = heroLayer();
				// 2. Flare: the crack blazes and branches, sparks fly.
				curtain.classList.add('is-flare');
				sparks();
				// 3. The dark surface breaks open; behind it the first piece fills the screen.
				later(function () {
					curtain.classList.add('is-shatter');
					if (h) {
						h.layer.style.opacity = 1;
						h.img.animate([{ transform: 'scale(1.14)', filter: 'brightness(1.5) saturate(.85)' }, { transform: 'scale(1)', filter: 'none' }],
							{ duration: 1600, easing: 'cubic-bezier(.16,.84,.24,1)', fill: 'forwards' });
					}
				}, 240);
				// 4. The piece settles into its frame in the banner.
				later(function () {
					curtain.classList.add('is-reveal');
					light();
					if (h) {
						var r = h.slide.getBoundingClientRect();
						h.layer.animate([
							{ left: '0px', top: '0px', width: innerWidth + 'px', height: innerHeight + 'px' },
							{ left: r.left + 'px', top: r.top + 'px', width: r.width + 'px', height: r.height + 'px' }
						], { duration: 1000, easing: 'cubic-bezier(.65,0,.2,1)', fill: 'forwards' });
					}
				}, 1650);
				later(function () { curtain.classList.add('is-fading'); }, 2700);
				later(done, 3150);
			});
		}
		function onKey(e) {
			if (e.key === 'Escape') open(true);
			else if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown' || e.key === 'PageDown') { if (document.activeElement && document.activeElement.matches && document.activeElement.matches('[data-dd-curtain-skip]')) return; e.preventDefault(); open(false); }
		}
		// Scrolling means "let me in": hand the page back at once.
		function onGesture() { if (opened) return; open(true); }
		if (btn) btn.addEventListener('click', function () { open(false); });
		var skip = qs('[data-dd-curtain-skip]', curtain);
		if (skip) skip.addEventListener('click', function () { open(true); });
		document.addEventListener('keydown', onKey);
		window.addEventListener('wheel', onGesture, { passive: true });
		window.addEventListener('touchmove', onGesture, { passive: true });
	})();

	/* ---------------- Featured pieces: image slider, one at a time, arrows + dots + swipe ---------------- */
	(function () {
		var box = qs('[data-dd-feat]');
		if (!box) return;
		var slides = qsa('[data-dd-slide]', box), dots = qsa('[data-dd-go]', box);
		var n = slides.length, cur = 0, timer = null, paused = false, started = false, DUR = 6000;
		if (n < 2) return;
		function go(i) {
			i = (i + n) % n;
			if (i === cur) return;
			slides[cur].classList.remove('is-current'); slides[cur].setAttribute('aria-hidden', 'true'); slides[cur].setAttribute('tabindex', '-1');
			if (dots[cur]) dots[cur].setAttribute('aria-current', 'false');
			cur = i;
			slides[cur].classList.add('is-current'); slides[cur].removeAttribute('aria-hidden'); slides[cur].removeAttribute('tabindex');
			if (dots[cur]) dots[cur].setAttribute('aria-current', 'true');
			restart();
		}
		function restart() {
			clearTimeout(timer);
			if (reduce || paused || !started) return;
			timer = setTimeout(function () { go(cur + 1); }, DUR);
		}
		dots.forEach(function (b) { b.addEventListener('click', function () { go(+b.dataset.ddGo); }); });
		var prev = qs('[data-dd-prev]', box), next = qs('[data-dd-next]', box);
		if (prev) prev.addEventListener('click', function () { go(cur - 1); });
		if (next) next.addEventListener('click', function () { go(cur + 1); });
		['mouseenter', 'focusin'].forEach(function (e) { box.addEventListener(e, function () { paused = true; clearTimeout(timer); }); });
		['mouseleave', 'focusout'].forEach(function (e) { box.addEventListener(e, function (ev) { if (ev.type === 'focusout' && box.contains(ev.relatedTarget)) return; paused = false; restart(); }); });
		box.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowRight') { e.preventDefault(); go(cur + 1); }
			if (e.key === 'ArrowLeft') { e.preventDefault(); go(cur - 1); }
		});
		var sx = 0, sy = 0, stage = qs('.dd-feat__stage', box);
		stage.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; sy = e.touches[0].clientY; }, { passive: true });
		stage.addEventListener('touchend', function (e) {
			var dx = e.changedTouches[0].clientX - sx, dy = e.changedTouches[0].clientY - sy;
			if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) { paused = false; go(cur + (dx < 0 ? 1 : -1)); }
		}, { passive: true });
		document.addEventListener('visibilitychange', function () { if (document.hidden) clearTimeout(timer); else restart(); });
		function start() { if (started) return; started = true; restart(); }
		if (box.classList.contains('is-on')) start(); else document.addEventListener('dd:lit', start);
	})();

	/* ---------------- Collections list: a quiet photo follows the pointer (desktop only) ---------------- */
	(function () {
		var sec = qs('[data-dd-collections]');
		if (!sec || touch) return;
		var peek = qs('[data-dd-peek]', sec), raf = null, x = 0, y = 0;
		if (!peek) return;
		function place() { raf = null; peek.style.transform = 'translate(' + (x + 28) + 'px,' + (y - 120) + 'px)'; }
		qsa('.dd-collections__item', sec).forEach(function (a) {
			a.addEventListener('mouseenter', function () {
				if (!a.dataset.img) return;
				if (peek.getAttribute('src') !== a.dataset.img) peek.src = a.dataset.img;
				peek.hidden = false; requestAnimationFrame(function () { peek.classList.add('is-on'); });
			});
			a.addEventListener('mouseleave', function () { peek.classList.remove('is-on'); });
		});
		sec.addEventListener('mousemove', function (e) { var r = sec.getBoundingClientRect(); x = e.clientX - r.left; y = e.clientY - r.top; if (!raf) raf = requestAnimationFrame(place); });
	})();

	/* ---------------- Strip of other pieces: drifts slowly, pauses on hover/touch, arrows ---------------- */
	(function () {
		var box = qs('[data-dd-strip]');
		if (!box) return;
		var track = qs('[data-dd-strip-track]', box), paused = false, raf = null, last = 0;
		var step = function () { var it = qs('.dd-strip__item', track); return it ? it.getBoundingClientRect().width + 14 : 300; };
		qs('[data-dd-strip-prev]', box).addEventListener('click', function () { track.scrollBy({ left: -step() * 2, behavior: 'smooth' }); });
		qs('[data-dd-strip-next]', box).addEventListener('click', function () { track.scrollBy({ left: step() * 2, behavior: 'smooth' }); });
		if (reduce) return;
		['mouseenter', 'touchstart', 'focusin'].forEach(function (e) { track.addEventListener(e, function () { paused = true; }, { passive: true }); });
		['mouseleave', 'focusout'].forEach(function (e) { track.addEventListener(e, function () { paused = false; }); });
		track.addEventListener('touchend', function () { setTimeout(function () { paused = false; }, 3000); });
		track.style.scrollSnapType = 'none';
		var acc = 0;
		function tick(t) {
			var dt = last ? t - last : 16; last = t;
			if (!paused && !document.hidden) {
				acc += dt * 0.03;
				if (acc >= 1) {
					var px = Math.floor(acc); acc -= px;
					if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 2) track.scrollLeft = 0;
					else track.scrollLeft += px;
				}
			}
			raf = requestAnimationFrame(tick);
		}
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (en) {
				en.forEach(function (x) {
					if (x.isIntersecting && !raf) { last = 0; raf = requestAnimationFrame(tick); }
					else if (!x.isIntersecting && raf) { cancelAnimationFrame(raf); raf = null; }
				});
			}).observe(track);
		}
	})();

	/* ---------------- Kintsugi band: the gold veins catch the light ---------------- */
	(function () {
		var band = qs('[data-dd-veins]');
		if (!band || touch) return;
		var raf = null, x = 50, y = 40;
		band.addEventListener('pointermove', function (e) {
			var r = band.getBoundingClientRect();
			x = ((e.clientX - r.left) / r.width) * 100; y = ((e.clientY - r.top) / r.height) * 100;
			if (!raf) raf = requestAnimationFrame(function () {
				raf = null;
				band.style.setProperty('--dd-vmask', 'radial-gradient(circle 260px at ' + x.toFixed(1) + '% ' + y.toFixed(1) + '%, #000 0%, rgba(0,0,0,.5) 40%, transparent 72%)');
			});
		});
	})();

	/* ---------------- Room: lights on as it scrolls in (fallback for browsers without scroll timelines) ---------------- */
	(function () {
		var room = qs('[data-dd-room]');
		if (!room) return;
		if ('IntersectionObserver' in window) {
			var io = new IntersectionObserver(function (en) {
				en.forEach(function (x) { if (x.isIntersecting) { room.classList.add('is-lit'); io.disconnect(); } });
			}, { threshold: 0.45 });
			io.observe(room);
		} else room.classList.add('is-lit');
		var hot = qs('[data-dd-hot]', room);
		if (hot) {
			var b = qs('button', hot);
			b.addEventListener('click', function () {
				var on = !hot.classList.contains('is-open');
				hot.classList.toggle('is-open', on); b.setAttribute('aria-expanded', String(on));
			});
			document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { hot.classList.remove('is-open'); b.setAttribute('aria-expanded', 'false'); } });
		}
	})();

	/* ---------------- Product gallery: thumbs, arrows, keyboard, lamplight lens ---------------- */
	(function () {
		var g = qs('[data-dd-gallery]');
		if (!g) return;
		var stage = qs('[data-dd-stage]', g), main = qs('[data-dd-main]', g);
		if (!stage || !main) return;
		var imgs = [];
		try { imgs = JSON.parse(stage.dataset.images || '[]'); } catch (e) {}
		var cur = 0, thumbs = qsa('[data-dd-thumb]', g), counter = qs('[data-dd-counter]', g);
		var lens = qs('.dd-lens', stage), lensImg = lens ? qs('img', lens) : null, zoom = 2.2, R = 120;

		function show(i) {
			if (!imgs.length) return;
			cur = (i + imgs.length) % imgs.length;
			main.src = imgs[cur].src; main.alt = imgs[cur].alt;
			if (lensImg) lensImg.src = imgs[cur].full;
			thumbs.forEach(function (t, j) { t.setAttribute('aria-current', String(j === cur)); });
			if (counter) counter.textContent = (cur + 1) + ' / ' + imgs.length;
		}
		// Preload the next images after the first paint.
		window.addEventListener('load', function () { imgs.slice(1, 4).forEach(function (m) { var im = new Image(); im.src = m.src; }); });

		thumbs.forEach(function (t) { t.addEventListener('click', function () { show(+t.dataset.ddThumb); }); });
		var prev = qs('[data-dd-prev]', g), next = qs('[data-dd-next]', g);
		if (prev) prev.addEventListener('click', function () { show(cur - 1); });
		if (next) next.addEventListener('click', function () { show(cur + 1); });
		stage.tabIndex = 0;
		stage.setAttribute('aria-label', 'Product photos. Use the arrow keys to change photo.');
		stage.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') { e.preventDefault(); show(cur - 1); }
			if (e.key === 'ArrowRight') { e.preventDefault(); show(cur + 1); }
		});
		// Swipe on touch.
		var sx = null;
		stage.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
		stage.addEventListener('touchend', function (e) {
			if (sx === null) return;
			var dx = e.changedTouches[0].clientX - sx; sx = null;
			if (Math.abs(dx) > 40) show(cur + (dx < 0 ? 1 : -1));
		});

		if (lens && lensImg && !touch) {
			var raf = null, lx = 0, ly = 0;
			stage.addEventListener('pointermove', function (e) {
				if (e.target.closest('.dd-stage__nav')) { lens.classList.remove('is-on'); return; }
				var r = main.getBoundingClientRect();
				lx = e.clientX - r.left; ly = e.clientY - r.top;
				if (raf) return;
				raf = requestAnimationFrame(function () {
					raf = null;
					var w = r.width, h = r.height;
					lens.style.left = (lx - R) + 'px'; lens.style.top = (ly - R) + 'px';
					lensImg.style.width = (w * zoom) + 'px'; lensImg.style.height = (h * zoom) + 'px';
					lensImg.style.transform = 'translate(' + (R - lx * zoom) + 'px,' + (R - ly * zoom) + 'px)';
					lens.classList.add('is-on');
				});
			});
			stage.addEventListener('pointerleave', function () { lens.classList.remove('is-on'); });
		}

		var scale = qs('[data-dd-goto-scale]');
		if (scale) {
			var idx = imgs.findIndex(function (m) { return /scale|person|beside/i.test(m.alt); });
			if (idx < 0) scale.hidden = true;
			else scale.addEventListener('click', function () { show(idx); g.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' }); });
		}
	})();

	/* ---------------- Size & colour buttons over WooCommerce's selects ---------------- */
	(function () {
		var wrap = qs('[data-dd-buyform]');
		if (!wrap) return;
		var data = {};
		try { data = JSON.parse(wrap.dataset.swatches || '{}'); } catch (e) {}
		var $form = $(wrap).find('form.variations_form');
		var priceEl = qs('[data-dd-price]'), priceDefault = priceEl ? priceEl.innerHTML : '';
		var klarnaEl = qs('[data-dd-klarna]');

		function klarna(p) {
			if (!klarnaEl || !D.klarna || !p) return;
			klarnaEl.textContent = (D.i18n.klarna || '%s').replace('%s', money(Math.round(p / 3 * 100) / 100));
		}

		qsa('table.variations select', wrap).forEach(function (sel) {
			var key = sel.name, info = data[key] || {};
			var isColour = /colou?r/i.test(key), isSize = /size/i.test(key);
			var row = sel.closest('tr'), label = row ? qs('th label', row) : null;
			var chosen = document.createElement('span'); chosen.className = 'dd-chosen';
			if (label) label.appendChild(chosen);

			var group = document.createElement('div');
			group.className = isColour ? 'dd-swatches' : 'dd-sizes';
			group.setAttribute('role', 'group');
			if (label) group.setAttribute('aria-label', label.textContent.trim());

			qsa('option', sel).forEach(function (o) {
				if (!o.value) return;
				var meta = info[o.value] || {};
				var b = document.createElement('button');
				b.type = 'button'; b.dataset.value = o.value; b.setAttribute('aria-pressed', 'false');
				if (isColour) {
					b.className = 'dd-swatch';
					b.setAttribute('aria-label', meta.label || o.textContent);
					b.title = meta.label || o.textContent;
					var dot = document.createElement('span'); dot.style.background = meta.hex || '#777'; b.appendChild(dot);
				} else {
					b.className = 'dd-size';
					b.textContent = meta.label || o.textContent;
					var small = [meta.note, isSize && meta.minLabel ? meta.minLabel : ''].filter(Boolean).join(' · ');
					if (small) { var s = document.createElement('small'); s.textContent = small; b.appendChild(s); }
				}
				b.addEventListener('click', function () {
					sel.value = sel.value === o.value ? '' : o.value;
					$(sel).trigger('change');
				});
				group.appendChild(b);
			});
			sel.classList.add('dd-hidden');
			sel.setAttribute('tabindex', '-1'); sel.setAttribute('aria-hidden', 'true');
			sel.parentNode.insertBefore(group, sel);

			function sync() {
				qsa('button', group).forEach(function (b) {
					var opt = qs('option[value="' + CSS.escape(b.dataset.value) + '"]', sel);
					b.setAttribute('aria-pressed', String(sel.value === b.dataset.value));
					b.disabled = !opt || opt.disabled;
				});
				var o = sel.options[sel.selectedIndex];
				chosen.textContent = sel.value && o ? ': ' + ((info[sel.value] || {}).label || o.textContent) : '';
			}
			$(sel).on('change', sync);
			$form.on('woocommerce_update_variation_values', sync);
			sync();
		});

		$form.on('found_variation', function (e, v) {
			if (priceEl && v.price_html) priceEl.innerHTML = $(v.price_html).html() || v.price_html;
			klarna(v.display_price);
		});
		$form.on('reset_data', function () {
			if (priceEl) priceEl.innerHTML = priceDefault;
			if (klarnaEl) klarna(parseFloat(klarnaEl.dataset.price));
		});

		// +/− around the quantity field.
		qsa('.quantity', wrap).forEach(function (q) {
			var input = qs('input.qty', q);
			if (!input || input.type === 'hidden') return;
			var mk = function (txt, d, lab) {
				var b = document.createElement('button'); b.type = 'button'; b.className = 'dd-qty-btn'; b.textContent = txt; b.setAttribute('aria-label', lab);
				b.addEventListener('click', function () {
					var min = parseFloat(input.min) || 1, max = parseFloat(input.max) || 99;
					input.value = Math.min(max, Math.max(min, (parseFloat(input.value) || 1) + d));
					$(input).trigger('change');
				});
				return b;
			};
			q.insertBefore(mk('−', -1, 'Fewer'), input);
			q.appendChild(mk('+', 1, 'More'));
		});
	})();

	/* ---------------- Size scale: hovering a size lights its piece ---------------- */
	qsa('[data-dd-scale]').forEach(function (box) {
		var set = function (size) {
			qsa('[data-size]', box).forEach(function (el) { el.classList.toggle('is-on', el.dataset.size === size); });
		};
		qsa('.dd-scale__label, .dd-scale__piece', box).forEach(function (el) {
			el.addEventListener('mouseenter', function () { set(el.dataset.size); });
			el.addEventListener('focus', function () { set(el.dataset.size); });
		});
		set('medium');
	});

	/* ---------------- Motion layer ---------------- */
	(function () {
		if (reduce || !('IntersectionObserver' in window)) return;
		var html = document.documentElement;
		html.classList.add('dd-motion');

		// Split headlines into words that rise out of a mask.
		qsa('.dd-h2, .dd-room__text h2, .dd-veins__quote, .dd-strip__title, .dd-page__title, .woocommerce-products-header__title, .related > h2').forEach(function (h) {
			if (h.dataset.split || h.querySelector('*')) return;
			h.dataset.split = '1';
			var words = h.textContent.trim().split(/\s+/);
			h.textContent = '';
			words.forEach(function (w, i) {
				var o = document.createElement('span'); o.className = 'dd-w';
				var n = document.createElement('span'); n.textContent = w; n.style.setProperty('--i', i);
				o.appendChild(n); h.appendChild(o);
				if (i < words.length - 1) h.appendChild(document.createTextNode(' '));
			});
			h.setAttribute('aria-label', words.join(' '));
			h.setAttribute('data-reveal-words', '');
		});

		// What reveals, and in what order.
		var groups = [
			['.dd-tiles', '.dd-tile', 'img'],
			['ul.products', 'li.product', 'item'],
			['.dd-reviews', '.dd-review', 'item'],
			['.dd-promises', ':scope > div', 'item'],
			['.dd-story__imgs', 'img', 'item']
		];
		groups.forEach(function (g) {
			qsa(g[0]).forEach(function (wrap) {
				qsa(g[1], wrap).forEach(function (el, i) {
					el.style.setProperty('--d', (i % 6) * 110 + 'ms');
					if (g[2] === 'img' || (g[2] === 'item' && el.matches('li.product'))) {
						var media = el.matches('li.product') ? qs('.dd-card__media', el) : el;
						if (media) media.setAttribute('data-reveal-img', '');
						if (el.matches('li.product')) { qsa('.dd-card__row, .dd-card__meta', el).forEach(function (t) { t.setAttribute('data-reveal', ''); t.style.setProperty('--d', (i % 6) * 110 + 250 + 'ms'); }); }
					} else el.setAttribute('data-reveal', '');
				});
			});
		});
		qsa('.dd-section-head p, .dd-section-head > a, .dd-room__text p, .dd-room__text .dd-btn, .dd-veins__content p, .dd-score, .dd-sizes-band .dd-btn, .dd-scale__labels, .dd-scale__note').forEach(function (el, i) {
			el.setAttribute('data-reveal', ''); el.style.setProperty('--d', '180ms');
		});
		qsa('.dd-scale__piece').forEach(function (el, i) { el.style.setProperty('--k', i); });

		// Clipped elements report no intersection, so each one is triggered by an unclipped sentinel:
		// its parent (cards, tiles) or itself (text).
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				if (!e.isIntersecting) return;
				(e.target._ddTargets || [e.target]).forEach(function (t) { t.classList.add('is-in'); });
				io.unobserve(e.target);
			});
		}, { rootMargin: '0px 0px -12% 0px', threshold: 0.12 });
		qsa('[data-reveal], [data-reveal-words], .dd-section-head, .dd-scale, .dd-veins').forEach(function (el) { io.observe(el); });
		qsa('[data-reveal-img]').forEach(function (el) {
			var s = el.matches('.dd-tile') ? el.parentElement : el.parentElement;
			if (el.matches('.dd-tile')) {
				// a tile is its own grid cell: use a zero-size sentinel inside the grid at the tile's position
				s = document.createElement('span');
				s.style.cssText = 'position:absolute;left:0;top:0;width:1px;height:1px;pointer-events:none';
				el.parentElement.style.position = el.parentElement.style.position || 'relative';
				s.style.left = el.offsetLeft + 'px'; s.style.top = (el.offsetTop + el.offsetHeight * 0.3) + 'px';
				el.parentElement.appendChild(s);
			}
			(s._ddTargets = s._ddTargets || []).push(el);
			io.observe(s);
		});

		// Count the rating up when it appears.
		var num = qs('.dd-score__num');
		if (num) {
			var target = parseFloat(num.textContent.replace(',', '.')) || 0;
			new IntersectionObserver(function (en, o) {
				if (!en[0].isIntersecting) return; o.disconnect();
				var t0 = performance.now();
				(function step(t) { var k = Math.min(1, (t - t0) / 1400), e = 1 - Math.pow(1 - k, 3); num.textContent = (target * e).toFixed(1); if (k < 1) requestAnimationFrame(step); })(t0);
			}).observe(num);
		}

		// Gold scroll thread.
		var bar = document.createElement('div'); bar.className = 'dd-progress'; bar.setAttribute('aria-hidden', 'true');
		document.body.appendChild(bar);
		var ticking = false;
		window.addEventListener('scroll', function () {
			if (ticking) return; ticking = true;
			requestAnimationFrame(function () {
				var h = document.documentElement.scrollHeight - innerHeight;
				bar.style.setProperty('--p', h > 0 ? (scrollY / h).toFixed(4) : 0);
				ticking = false;
			});
		}, { passive: true });

		if (touch) return;
		// The lamp follows the cursor across every piece.
		qsa('.dd-tile, .dd-mosaic__tile, .dd-card__media, .dd-strip__item, .dd-extra > a:first-child').forEach(function (el) {
			el.classList.add('dd-glow');
			el.addEventListener('pointermove', function (e) {
				var r = el.getBoundingClientRect();
				el.style.setProperty('--mx', (e.clientX - r.left) + 'px');
				el.style.setProperty('--my', (e.clientY - r.top) + 'px');
			});
		});
		// Opening mosaic: gentle depth as the pointer moves.
		var mosaic = qs('.dd-mosaic');
		if (mosaic) {
			var tiles = qsa('.dd-mosaic__tile', mosaic), raf = null, mx = 0, my = 0;
			mosaic.addEventListener('pointermove', function (e) {
				var r = mosaic.getBoundingClientRect();
				mx = (e.clientX - r.left) / r.width - 0.5; my = (e.clientY - r.top) / r.height - 0.5;
				if (raf) return;
				raf = requestAnimationFrame(function () {
					raf = null;
					tiles.forEach(function (t, i) {
						var d = 6 + (i % 3) * 5;
						t.style.setProperty('--px', (-mx * d).toFixed(1) + 'px');
						t.style.setProperty('--py', (-my * d).toFixed(1) + 'px');
					});
				});
			});
		}
		// Magnetic gold buttons.
		qsa('.dd-btn--gold').forEach(function (b) {
			b.addEventListener('pointermove', function (e) {
				var r = b.getBoundingClientRect();
				b.style.transform = 'translate(' + ((e.clientX - r.left - r.width / 2) * 0.18).toFixed(1) + 'px,' + ((e.clientY - r.top - r.height / 2) * 0.28).toFixed(1) + 'px)';
			});
			b.addEventListener('pointerleave', function () { b.style.transform = ''; });
		});
	})();

	/* ---------------- Free-delivery pop-up: small, once per visit, never twice after closing ---------------- */
	(function () {
		var box = qs('[data-dd-offer]');
		if (!box || !D.offerOn) return;
		var closed = parseInt(store.get('ddOfferClosed') || '0', 10);
		if (closed && Date.now() - closed < 1000 * 60 * 60 * 24 * 14) return;
		if (store.get('ddOfferSeen', true)) return;
		var shown = false;
		function show() {
			if (shown || (drawer && drawer.classList.contains('is-open'))) return;
			shown = true; store.set('ddOfferSeen', '1', true);
			box.classList.add('is-open');
		}
		// Never over the opening photos: wait until the visitor has scrolled past them.
		var armed = false;
		function arm() {
			if (armed || window.scrollY < window.innerHeight * 0.8) return;
			armed = true; window.removeEventListener('scroll', arm);
			setTimeout(show, Math.max(3, D.offerDelay || 12) * 1000);
		}
		if (qs('[data-dd-hero]')) window.addEventListener('scroll', arm, { passive: true });
		else { armed = true; setTimeout(show, Math.max(3, D.offerDelay || 12) * 1000); }
		document.addEventListener('mouseout', function (e) { if (armed && !e.relatedTarget && e.clientY < 8) show(); });
		qsa('[data-dd-offer-close]', box).forEach(function (b) {
			b.addEventListener('click', function () { box.classList.remove('is-open'); store.set('ddOfferClosed', String(Date.now())); });
		});
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && box.classList.contains('is-open')) { box.classList.remove('is-open'); store.set('ddOfferClosed', String(Date.now())); } });
	})();

})(jQuery);
