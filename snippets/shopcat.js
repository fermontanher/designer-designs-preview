
/* 0.10.0 Shop by category rail: arrows scroll ~one view, disabled at the ends. */
(function () {
	document.querySelectorAll('[data-dd-rail]').forEach(function (rail) {
		var track = rail.querySelector('[data-dd-rail-track]');
		var prev = rail.querySelector('[data-dd-rail-prev]');
		var next = rail.querySelector('[data-dd-rail-next]');
		if (!track || !prev || !next) { return; }
		function step() {
			var item = track.querySelector('li');
			var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
			var w = item ? item.getBoundingClientRect().width + gap : track.clientWidth * 0.8;
			return Math.max(w, Math.floor(track.clientWidth * 0.8 / w) * w);
		}
		function sync() {
			var max = track.scrollWidth - track.clientWidth - 2;
			prev.disabled = track.scrollLeft <= 2;
			next.disabled = track.scrollLeft >= max;
		}
		prev.addEventListener('click', function () { track.scrollBy({ left: -step() }); });
		next.addEventListener('click', function () { track.scrollBy({ left: step() }); });
		track.addEventListener('scroll', function () { window.requestAnimationFrame(sync); }, { passive: true });
		window.addEventListener('resize', sync);
		sync();
	});
})();
