/**
 * Yoga — mejoras visuales interactivas.
 * Sin dependencias externas; usa IntersectionObserver y eventos nativos.
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function ready(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	/* --------------------------------------------------------------
	 * Cabecera fija al hacer scroll.
	 * Se usa position: fixed + un espaciador, en vez de sticky: el
	 * contenedor de la cabecera es corto y sticky no podría recorrer
	 * la página. El espaciador evita que el contenido salte.
	 * ---------------------------------------------------------- */
	function initStickyHeader() {
		var header = document.querySelector('.yoga-standardhead');
		if (!header || !header.parentNode) {
			return;
		}

		var spacer = document.createElement('div');
		spacer.setAttribute('aria-hidden', 'true');
		header.parentNode.insertBefore(spacer, header);

		var stuck = false;

		function setStuck(shouldStick) {
			if (shouldStick === stuck) {
				return;
			}
			if (shouldStick) {
				// Se mide la altura natural ANTES de aplicar la clase.
				var natural = header.offsetHeight;
				header.classList.add('is-stuck');
				spacer.style.height = natural + 'px';
			} else {
				header.classList.remove('is-stuck');
				spacer.style.height = '0px';
			}
			stuck = shouldStick;
		}

		var ticking = false;
		function update() {
			var y = window.pageYOffset || document.documentElement.scrollTop;
			setStuck(y > 40);
			ticking = false;
		}

		window.addEventListener('scroll', function () {
			if (!ticking) {
				window.requestAnimationFrame(update);
				ticking = true;
			}
		}, { passive: true });

		window.addEventListener('resize', function () {
			if (stuck) {
				spacer.style.height = header.offsetHeight + 'px';
			}
		});

		update();

		// ?yoga_stuck=1 fuerza el estado "desplazado" (útil para depurar).
		if (/[?&]yoga_stuck=1/.test(window.location.search)) {
			setStuck(true);
		}
	}

	/* --------------------------------------------------------------
	 * Estado de scroll (botón volver arriba)
	 * ---------------------------------------------------------- */
	function initScrollState() {
		var top = document.querySelector('.yoga-to-top');
		var ticking = false;

		function update() {
			var y = window.pageYOffset || document.documentElement.scrollTop;

			if (top) {
				top.classList.toggle('is-visible', y > 420);
			}
			ticking = false;
		}

		window.addEventListener('scroll', function () {
			if (!ticking) {
				window.requestAnimationFrame(update);
				ticking = true;
			}
		}, { passive: true });

		update();

		if (top) {
			top.addEventListener('click', function () {
				window.scrollTo({
					top: 0,
					behavior: reduceMotion ? 'auto' : 'smooth'
				});
			});
		}
	}

	/* --------------------------------------------------------------
	 * Botón "volver arriba"
	 * ---------------------------------------------------------- */
	function initBackToTop() {
		if (document.querySelector('.yoga-to-top')) {
			return;
		}
		var btn = document.createElement('button');
		btn.className = 'yoga-to-top';
		btn.type = 'button';
		btn.setAttribute('aria-label', 'Volver arriba');
		btn.innerHTML = '<span aria-hidden="true">&#8593;</span>';
		document.body.appendChild(btn);
	}

	/* --------------------------------------------------------------
	 * Animación de entrada al hacer scroll
	 * ---------------------------------------------------------- */
	function initReveal() {
		var targets = document.querySelectorAll(
			'#service .yoga-service, ' +
			'#home-news .yoga-blog-post-box, ' +
			'.yoga-blog-section .yoga-heading, ' +
			'.yoga-callout-inner, ' +
			'.yoga-section .yoga-heading'
		);

		if (!targets.length) {
			return;
		}

		if (reduceMotion || !('IntersectionObserver' in window)) {
			return;
		}

		var groups = [];
		var byParent = {};

		Array.prototype.forEach.call(targets, function (el) {
			var parent = el.parentNode;
			if (!byParent[parent]) {
				byParent[parent] = groups.length;
				groups.push([]);
			}
			groups[byParent[parent]].push(el);
		});

		groups.forEach(function (group) {
			group.forEach(function (el, i) {
				el.classList.add('yoga-reveal');
				el.setAttribute('data-delay', String(Math.min(i, 5)));
			});
		});

		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					observer.unobserve(entry.target);
				}
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

		groups.forEach(function (group) {
			group.forEach(function (el) {
				observer.observe(el);
			});
		});
	}

	/* --------------------------------------------------------------
	 * Menú móvil: marcar el estado abierto
	 * ---------------------------------------------------------- */
	function initMobileNav() {
		var toggle = document.querySelector('.navbar-toggle');
		if (!toggle) {
			return;
		}
		toggle.addEventListener('click', function () {
			document.body.classList.toggle('yoga-nav-open');
		});
	}

	ready(function () {
		initBackToTop();
		initStickyHeader();
		initScrollState();
		initReveal();
		initMobileNav();
	});
})();
