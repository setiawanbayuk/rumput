(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    var body = document.body;
    var nav = document.querySelector('.pkk-guest-nav');
    var toggle = document.querySelector('.pkk-guest-menu-toggle');
    var closeBtn = document.querySelector('.pkk-guest-menu-close');
    var backdrop = document.querySelector('.pkk-guest-menu-backdrop');
    var links = document.querySelectorAll('.pkk-guest-menu a');

    function closeMenu() {
      body.classList.remove('pkk-guest-menu-open');
      if (toggle) toggle.setAttribute('aria-expanded', 'false');
    }

    function openMenu() {
      body.classList.add('pkk-guest-menu-open');
      if (toggle) toggle.setAttribute('aria-expanded', 'true');
    }

    if (toggle) {
      toggle.addEventListener('click', function () {
        if (body.classList.contains('pkk-guest-menu-open')) closeMenu();
        else openMenu();
      });
    }

    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (backdrop) backdrop.addEventListener('click', closeMenu);

    links.forEach(function (link) {
      link.addEventListener('click', function () {
        closeMenu();
      });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closeMenu();
    });

    function checkScroll() {
      if (!nav) return;
      if (window.scrollY > 8) nav.classList.add('is-scrolled');
      else nav.classList.remove('is-scrolled');
    }

    checkScroll();
    window.addEventListener('scroll', checkScroll, { passive: true });
  });
})();
