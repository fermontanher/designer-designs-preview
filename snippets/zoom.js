
/* 0.11.2 Piece of the month: the image zooms in where the cursor is and follows it smoothly. */
(function () {
	var box = document.querySelector('.dd-potm__media');
	if (!box || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) { return; }
	var img = box.querySelector('img');
	if (!img) { return; }
	var tx = 50, ty = 50, cx = 50, cy = 50, raf = null;
	var still = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	function tick() {
		cx += (tx - cx) * (still ? 1 : 0.12);
		cy += (ty - cy) * (still ? 1 : 0.12);
		box.style.setProperty('--zx', cx.toFixed(2) + '%');
		box.style.setProperty('--zy', cy.toFixed(2) + '%');
		raf = (Math.abs(tx - cx) > 0.05 || Math.abs(ty - cy) > 0.05) ? requestAnimationFrame(tick) : null;
	}
	function aim(e) {
		var r = box.getBoundingClientRect();
		tx = Math.min(100, Math.max(0, (e.clientX - r.left) / r.width * 100));
		ty = Math.min(100, Math.max(0, (e.clientY - r.top) / r.height * 100));
		if (!raf) { raf = requestAnimationFrame(tick); }
	}
	box.addEventListener('mouseenter', function (e) {
		aim(e); cx = tx; cy = ty; tick();
		box.classList.remove('is-zoomout'); box.classList.add('is-zoom');
	});
	box.addEventListener('mousemove', aim);
	box.addEventListener('mouseleave', function () {
		box.classList.remove('is-zoom'); box.classList.add('is-zoomout');
	});
})();
