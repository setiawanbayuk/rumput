(function () {
  'use strict';

  const MOBILE_QUERY = '(max-width: 991.98px)';
  const mq = window.matchMedia(MOBILE_QUERY);

  function isMobile() {
    return mq.matches;
  }

  function textOf(el) {
    return (el ? el.textContent : '').replace(/\s+/g, ' ').trim();
  }

  function iconFor(label) {
    const t = (label || '').toLowerCase();
    if (t.includes('beranda') || t.includes('dashboard')) return 'ri-home-5-line';
    if (t.includes('pelayanan') || t.includes('surat')) return 'ri-file-list-3-line';
    if (t.includes('rekap')) return 'ri-bar-chart-box-line';
    if (t.includes('rating')) return 'ri-star-smile-line';
    if (t.includes('profil')) return 'ri-user-3-line';
    if (t.includes('tools')) return 'ri-tools-line';
    return 'ri-apps-2-line';
  }

  function getSidebarLinkByText(sidebar, needle) {
    const links = Array.from(sidebar.querySelectorAll('a.sidebar-link[href]'));
    return links.find((a) => textOf(a).toLowerCase().includes(needle.toLowerCase()) && a.getAttribute('href') !== '#');
  }

  function setOpenState(sidebar, open) {
    if (!sidebar) return;
    if (open) {
      sidebar.classList.add('expand');
      document.body.classList.add('esuket-sidebar-open');
    } else {
      sidebar.classList.remove('expand');
      document.body.classList.remove('esuket-sidebar-open');
    }
  }

  function syncBodyState(sidebar) {
    if (!sidebar || !isMobile()) {
      document.body.classList.remove('esuket-sidebar-open');
      return;
    }
    document.body.classList.toggle('esuket-sidebar-open', sidebar.classList.contains('expand'));
  }

  function setupOverlay(sidebar) {
    if (!sidebar) return;
    let overlay = document.querySelector('.esuket-mobile-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'esuket-mobile-overlay';
      overlay.setAttribute('aria-hidden', 'true');
      document.body.appendChild(overlay);
    }
    overlay.addEventListener('click', function () {
      setOpenState(sidebar, false);
    });
  }

  function setupTitle() {
    const topbarLeft = document.querySelector('.topbar-left');
    if (!topbarLeft || topbarLeft.querySelector('.esuket-mobile-title')) return;

    const title = document.createElement('div');
    title.className = 'esuket-mobile-title';

    const activeSidebar = document.querySelector('#sidebar .sidebar-link.active, #sidebar .sidebar-item.active > .sidebar-link');
    let pageTitle = textOf(activeSidebar) || textOf(document.querySelector('.main h3, .main h4, .main h5')) || 'E-SUKET';
    pageTitle = pageTitle.replace(/Logout/i, '').trim() || 'E-SUKET';

    title.innerHTML = '<strong>' + pageTitle + '</strong><span>Mode operasional mobile</span>';
    const brand = topbarLeft.querySelector('.sidebar-brand');
    if (brand && brand.nextSibling) {
      topbarLeft.insertBefore(title, brand.nextSibling);
    } else {
      topbarLeft.appendChild(title);
    }
  }

  function setupTabbar(sidebar) {
    if (!sidebar || document.querySelector('.esuket-mobile-tabbar')) return;

    const beranda = getSidebarLinkByText(sidebar, 'Beranda');
    const pelayanan = getSidebarLinkByText(sidebar, 'Pelayanan');
    const rekap = getSidebarLinkByText(sidebar, 'Rekap') || getSidebarLinkByText(sidebar, 'Profil');

    const items = [];
    if (beranda) items.push({ label: 'Beranda', href: beranda.href, icon: 'ri-home-5-line' });
    if (pelayanan) items.push({ label: 'Layanan', href: pelayanan.href, icon: 'ri-file-list-3-line' });
    if (rekap) items.push({ label: textOf(rekap).includes('Rekap') ? 'Rekap' : 'Profil', href: rekap.href, icon: iconFor(textOf(rekap)) });
    items.push({ label: 'Menu', href: '#menu', icon: 'ri-menu-4-line', menu: true });

    const nav = document.createElement('nav');
    nav.className = 'esuket-mobile-tabbar';
    nav.setAttribute('aria-label', 'Navigasi cepat mobile E-SUKET');

    const current = window.location.href.split('#')[0].replace(/\/$/, '');

    items.slice(0, 4).forEach((item) => {
      const el = item.menu ? document.createElement('button') : document.createElement('a');
      if (item.menu) {
        el.type = 'button';
        el.addEventListener('click', function () {
          setOpenState(sidebar, true);
        });
      } else {
        el.href = item.href;
        const cleanHref = item.href.split('#')[0].replace(/\/$/, '');
        if (cleanHref === current || current.includes(cleanHref + '/')) el.classList.add('active');
      }
      el.innerHTML = '<i class="' + item.icon + '"></i><span>' + item.label + '</span>';
      nav.appendChild(el);
    });

    document.body.appendChild(nav);
  }

  function setupSidebarBehavior(sidebar) {
    if (!sidebar) return;
    setupOverlay(sidebar);

    const toggles = document.querySelectorAll('.toggle-btn');
    toggles.forEach((btn) => {
      btn.setAttribute('aria-label', 'Buka menu E-SUKET');
      btn.addEventListener('click', function () {
        window.setTimeout(function () { syncBodyState(sidebar); }, 0);
      });
    });

    sidebar.querySelectorAll('a.sidebar-link[href]').forEach((link) => {
      link.addEventListener('click', function () {
        if (isMobile() && link.getAttribute('href') !== '#') {
          window.setTimeout(function () { setOpenState(sidebar, false); }, 120);
        }
      });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && isMobile()) setOpenState(sidebar, false);
    });

    const observer = new MutationObserver(function () { syncBodyState(sidebar); });
    observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });

    if (isMobile()) setOpenState(sidebar, false);
    syncBodyState(sidebar);
  }

  function setupTables() {
    document.querySelectorAll('.table-responsive').forEach((wrap) => {
      wrap.setAttribute('data-mobile-hint', 'true');
    });
  }

  function init() {
    const sidebar = document.querySelector('#sidebar');
    setupSidebarBehavior(sidebar);
    setupTitle();
    setupTabbar(sidebar);
    setupTables();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  if (mq.addEventListener) {
    mq.addEventListener('change', function () {
      const sidebar = document.querySelector('#sidebar');
      if (sidebar) {
        if (!isMobile()) {
          sidebar.classList.add('expand');
          document.body.classList.remove('esuket-sidebar-open');
        } else {
          setOpenState(sidebar, false);
        }
      }
    });
  }
})();
