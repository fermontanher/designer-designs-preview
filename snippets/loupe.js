
/* 0.11.6 Piece of the month: a soft magnifying loupe glides over the photo and follows the cursor. */
(function () {
	var box = document.querySelector('.dd-potm__media');
	if (!box || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) { return; }
	var img = box.querySelector('img');
	if (!img) { return; }
	var Z = 2.4, still = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var lens = document.createElement('span');
	lens.className = 'dd-loupe';
	lens.setAttribute('aria-hidden', 'true');
	box.appendChild(lens);
	var tx = 0, ty = 0, cx = 0, cy = 0, raf = null, on = false, geo = null;
	function src() {
		var best = img.currentSrc || img.src, w = 0;
		(img.getAttribute('srcset') || '').split(',').forEach(function (p) {
			var m = p.trim().split(/\s+/); var n = parseInt(m[1], 10) || 0;
			if (n > w) { w = n; best = m[0]; }
		});
		return best;
	}
	function measure() {
		var r = box.getBoundingClientRect();
		var nw = img.naturalWidth || r.width, nh = img.naturalHeight || r.height;
		var s = Math.max(r.width / nw, r.height / nh), rw = nw * s, rh = nh * s;
		var k = 1, t = getComputedStyle(img).transform;
		if (t && t !== 'none') { k = parseFloat(t.split('(')[1]) || 1; }
		geo = { r: r, rw: rw * k, rh: rh * k, ox: (r.width - rw * k) / 2, oy: (r.height - rh * k) / 2, L: lens.offsetWidth };
		lens.style.backgroundSize = (geo.rw * Z) + 'px ' + (geo.rh * Z) + 'px';
	}
	function paint() {
		lens.style.transform = 'translate3d(' + cx + 'px,' + cy + 'px,0)';
		var bx = -((cx - geo.ox) * Z - geo.L / 2), by = -((cy - geo.oy) * Z - geo.L / 2);
		lens.style.backgroundPosition = bx + 'px ' + by + 'px';
	}
	function tick() {
		var f = still ? 1 : 0.16;
		cx += (tx - cx) * f; cy += (ty - cy) * f;
		paint();
		raf = (on && (Math.abs(tx - cx) > 0.1 || Math.abs(ty - cy) > 0.1)) ? requestAnimationFrame(tick) : null;
	}
	function aim(e) {
		tx = e.clientX - geo.r.left; ty = e.clientY - geo.r.top;
		if (!raf) { raf = requestAnimationFrame(tick); }
	}
	box.addEventListener('mouseenter', function (e) {
		if (!lens.style.backgroundImage) { lens.style.backgroundImage = 'url("' + src() + '")'; }
		measure(); on = true;
		tx = cx = e.clientX - geo.r.left; ty = cy = e.clientY - geo.r.top; paint();
		box.classList.add('is-loupe');
	});
	box.addEventListener('mousemove', function (e) { if (!geo) { measure(); } aim(e); });
	box.addEventListener('mouseleave', function () { on = false; box.classList.remove('is-loupe'); });
	window.addEventListener('scroll', function () { if (on) { measure(); } }, { passive: true });
	window.addEventListener('resize', function () { if (on) { measure(); } });
})();
