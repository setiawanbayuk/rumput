(function () {
  'use strict';

  var MOBILE_MAX = 991.98;
  var resizeTimer = null;
  var observer = null;

  function isMobile() {
    return window.innerWidth <= MOBILE_MAX;
  }

  function body() {
    return document.body;
  }

  function ensureOverlay() {
    var overlay = document.querySelector('.simpkk-mobile-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'simpkk-mobile-overlay';
      overlay.setAttribute('aria-hidden', 'true');
      document.body.appendChild(overlay);
      overlay.addEventListener('click', closeMenu);
      overlay.addEventListener('touchend', closeMenu, { passive: true });
    }
    return overlay;
  }

  function ensureCloseButton() {
    var sidebar = document.querySelector('.main-sidebar');
    if (!sidebar) return;
    if (sidebar.querySelector('.simpkk-mobile-sidebar-close')) return;

    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'simpkk-mobile-sidebar-close';
    btn.setAttribute('aria-label', 'Tutup menu');
    btn.innerHTML = '<i class="fas fa-times"></i>';
    sidebar.appendChild(btn);
    btn.addEventListener('click', closeMenu);
  }

  function openMenu() {
    if (!isMobile()) return;
    body().classList.add('simpkk-mobile-menu-open');
    body().classList.add('sidebar-show');
    body().classList.remove('sidebar-gone');
    updateTogglerLabel();
  }

  function closeMenu() {
    body().classList.remove('simpkk-mobile-menu-open');
    body().classList.remove('sidebar-show');
    body().classList.add('sidebar-gone');
    updateTogglerLabel();
  }

  function toggleMenu(event) {
    if (!isMobile()) return;
    if (event) {
      event.preventDefault();
      event.stopPropagation();
      if (event.stopImmediatePropagation) event.stopImmediatePropagation();
    }
    if (body().classList.contains('simpkk-mobile-menu-open') || body().classList.contains('sidebar-show')) {
      closeMenu();
    } else {
      openMenu();
    }
  }

  function updateTogglerLabel() {
    var toggler = document.querySelector('[data-toggle="sidebar"]');
    if (!toggler) return;
    var opened = body().classList.contains('simpkk-mobile-menu-open') || body().classList.contains('sidebar-show');
    toggler.setAttribute('aria-label', opened ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    toggler.setAttribute('role', 'button');
  }

  function bindToggler() {
    var toggler = document.querySelector('[data-toggle="sidebar"]');
    if (!toggler || toggler.dataset.simpkkMobileBound === '1') return;
    toggler.dataset.simpkkMobileBound = '1';
    toggler.addEventListener('click', toggleMenu, true);
    updateTogglerLabel();
  }

  function closeOnMenuClick() {
    document.querySelectorAll('.main-sidebar .sidebar-menu a').forEach(function (link) {
      if (link.dataset.simpkkMobileCloseBound === '1') return;
      link.dataset.simpkkMobileCloseBound = '1';
      link.addEventListener('click', function () {
        var href = link.getAttribute('href') || '';
        var isDropdown = link.classList.contains('has-dropdown') || href === '#' || href === 'javascript:void(0)';
        if (isMobile() && !isDropdown) {
          window.setTimeout(closeMenu, 120);
        }
      });
    });
  }

  function decorateTables() {
    if (!isMobile()) return;
    document.querySelectorAll('.main-content table').forEach(function (table) {
      if (!table.closest('.dataTables_wrapper') && !table.closest('.table-responsive') && !table.closest('.simpkk-table-scroll')) {
        var wrapper = document.createElement('div');
        wrapper.className = 'simpkk-table-scroll';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
      }

      var headers = Array.prototype.map.call(table.querySelectorAll('thead th'), function (th) {
        return (th.textContent || '').replace(/\s+/g, ' ').trim();
      });

      if (headers.length) {
        table.querySelectorAll('tbody tr').forEach(function (row) {
          Array.prototype.forEach.call(row.children, function (cell, index) {
            if (headers[index] && !cell.getAttribute('data-label')) {
              cell.setAttribute('data-label', headers[index]);
            }
          });
        });
      }
    });
  }

  function decorateDataTables() {
    if (!isMobile()) return;
    document.querySelectorAll('.dataTables_filter input').forEach(function (input) {
      input.setAttribute('placeholder', input.getAttribute('placeholder') || 'Cari data...');
      input.setAttribute('autocomplete', 'off');
      input.setAttribute('inputmode', 'search');
    });
  }

  function markAuthenticated() {
    if (document.querySelector('.main-sidebar') || document.querySelector('.main-navbar .nav-link-user')) {
      body().classList.add('simpkk-authenticated');
    } else {
      body().classList.remove('simpkk-authenticated');
    }
  }

  function syncState() {
    markAuthenticated();
    if (isMobile()) {
      body().classList.add('simpkk-mobile-ready');
      if (!body().classList.contains('simpkk-mobile-menu-open') && !body().classList.contains('sidebar-show')) {
        body().classList.add('sidebar-gone');
      }
    } else {
      body().classList.remove('simpkk-mobile-ready', 'simpkk-mobile-menu-open', 'sidebar-gone', 'sidebar-show');
    }
    updateTogglerLabel();
  }

  function runDecorators() {
    decorateTables();
    decorateDataTables();
  }

  function bindKeyboard() {
    if (document.documentElement.dataset.simpkkKeyboardBound === '1') return;
    document.documentElement.dataset.simpkkKeyboardBound = '1';
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && isMobile()) closeMenu();
    });
  }

  function observeContent() {
    if (observer) return;
    var target = document.querySelector('.main-content');
    if (!target || typeof MutationObserver === 'undefined') return;
    observer = new MutationObserver(function () {
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(runDecorators, 120);
    });
    observer.observe(target, { childList: true, subtree: true });
  }

  function init() {
    ensureOverlay();
    ensureCloseButton();
    bindToggler();
    closeOnMenuClick();
    bindKeyboard();
    syncState();
    runDecorators();
    observeContent();

    window.addEventListener('resize', function () {
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(function () {
        syncState();
        runDecorators();
      }, 120);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
