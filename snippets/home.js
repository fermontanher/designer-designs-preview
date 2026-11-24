
/* 0.11.0 Home: scroll reveals (staggered per section) and the reviews fade carousel. */
(function () {
	var items = document.querySelectorAll('.dd-rv');
	if (!items.length) { return; }
	if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		items.forEach(function (el) { el.classList.add('is-in'); });
		return;
	}
	document.documentElement.classList.add('dd-js');
	document.querySelectorAll('.dd-potm__copy, .dd-studio__copy, .dd-steps__row').forEach(function (group) {
		group.querySelectorAll('.dd-rv').forEach(function (el, i) { el.style.setProperty('--d', (i * 120) + 'ms'); });
	});
	var io = new IntersectionObserver(function (entries) {
		entries.forEach(function (e) {
			if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
		});
	}, { rootMargin: '0px 0px -12% 0px', threshold: 0.12 });
	items.forEach(function (el) { io.observe(el); });
})();
(function () {
	var box = document.querySelector('[data-dd-hrev]');
	if (!box) { return; }
	var slides = box.querySelectorAll('.dd-hrev__item');
	var dots = box.querySelectorAll('.dd-hrev__dots button');
	if (slides.length < 2) { return; }
	var cur = 0, timer = null, reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	function go(n) {
		cur = (n + slides.length) % slides.length;
		slides.forEach(function (s, i) { s.classList.toggle('is-active', i === cur); s.setAttribute('aria-hidden', i === cur ? 'false' : 'true'); });
		dots.forEach(function (d, i) {
			d.classList.remove('is-active'); void d.offsetWidth;
			if (i === cur) { d.classList.add('is-active'); }
		});
		restart();
	}
	function restart() { clearTimeout(timer); if (!reduce && !box.classList.contains('is-paused')) { timer = setTimeout(function () { go(cur + 1); }, 7000); } }
	dots.forEach(function (d, i) { d.addEventListener('click', function () { go(i); }); });
	box.addEventListener('mouseenter', function () { box.classList.add('is-paused'); clearTimeout(timer); });
	box.addEventListener('mouseleave', function () { box.classList.remove('is-paused'); restart(); });
	box.addEventListener('focusin', function () { box.classList.add('is-paused'); clearTimeout(timer); });
	box.addEventListener('focusout', function () { box.classList.remove('is-paused'); restart(); });
	restart();
})();
