/**
 * ====================================================================
 * shop.js — shop.php-ის ფილტრების ლოგიკა.
 *
 * მდგომარეობა ინახება URL query string-ში (?category=&size=&price_min=&price_max=),
 * რომ ბმული გაზიარებადი/დასამახსოვრებელი იყოს (მაგ. header-ის nav ბმულები
 * პირდაპირ /shop.php?category=shoes-ზე მიდის).
 * ====================================================================
 */

function getShopFiltersFromUrl() {
  const params = new URLSearchParams(window.location.search);
  return {
    category: params.get('category') || '',
    size: params.get('size') || '',
    price_min: params.get('price_min') || '',
    price_max: params.get('price_max') || ''
  };
}

function updateShopUrl(filters) {
  const params = new URLSearchParams();
  Object.entries(filters).forEach(([k, v]) => { if (v) params.set(k, v); });
  const qs = params.toString();
  const newUrl = window.location.pathname + (qs ? `?${qs}` : '');
  window.history.pushState({}, '', newUrl);
}

let currentFilters = getShopFiltersFromUrl();

async function loadFilterOptions() {
  const lang = getCurrentLang();

  // კატეგორიები
  try {
    const res = await fetch(`${window.BASE_URL || ''}/api/categories.php?lang=${lang}`);
    const categories = await res.json();
    const allLabel = { ka: 'ყველა', en: 'All', ru: 'Все' }[lang];
    const catEl = document.getElementById('filterCategory');
    catEl.innerHTML = [{ slug: '', name: allLabel }, ...categories].map(c => `
      <button type="button" class="filter-pill${currentFilters.category === c.slug ? ' is-active' : ''}" data-category="${c.slug}">
        ${c.name}
      </button>
    `).join('');
    catEl.querySelectorAll('[data-category]').forEach(btn => {
      btn.addEventListener('click', () => {
        currentFilters.category = btn.dataset.category;
        catEl.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');
        applyFilters();
      });
    });
  } catch (e) { console.error(e); }

  // ზომები
  try {
    const res = await fetch(`${window.BASE_URL || ''}/api/sizes.php`);
    const sizes = await res.json();
    const sizeEl = document.getElementById('filterSize');
    sizeEl.innerHTML = sizes.map(s => `
      <button type="button" class="filter-pill${currentFilters.size === s.label ? ' is-active' : ''}" data-size="${s.label}">
        ${s.label}
      </button>
    `).join('');
    sizeEl.querySelectorAll('[data-size]').forEach(btn => {
      btn.addEventListener('click', () => {
        // იგივე ზომაზე ხელახლა დაჭერა თუ იყო - გამორთვა (toggle)
        const isSame = currentFilters.size === btn.dataset.size;
        currentFilters.size = isSame ? '' : btn.dataset.size;
        sizeEl.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('is-active'));
        if (!isSame) btn.classList.add('is-active');
        applyFilters();
      });
    });
  } catch (e) { console.error(e); }

  // ფასის ინფუთები
  document.getElementById('filterPriceMin').value = currentFilters.price_min;
  document.getElementById('filterPriceMax').value = currentFilters.price_max;
}

function applyFilters() {
  updateShopUrl(currentFilters);
  renderProductGrid('shopGrid', currentFilters);
}

document.addEventListener('DOMContentLoaded', () => {
  loadFilterOptions();
  renderProductGrid('shopGrid', currentFilters);

  document.getElementById('filterApply')?.addEventListener('click', () => {
    currentFilters.price_min = document.getElementById('filterPriceMin').value;
    currentFilters.price_max = document.getElementById('filterPriceMax').value;
    applyFilters();
  });

  document.getElementById('filterClear')?.addEventListener('click', () => {
    currentFilters = { category: '', size: '', price_min: '', price_max: '' };
    document.getElementById('filterPriceMin').value = '';
    document.getElementById('filterPriceMax').value = '';
    document.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('is-active'));
    document.querySelectorAll('[data-category=""]').forEach(b => b.classList.add('is-active'));
    applyFilters();
  });

  // ენის შეცვლისას ფილტრის ხელახლა ჩატვირთვა (კატეგორიის სახელები ითარგმნება)
  document.querySelectorAll('[data-lang-option]').forEach(btn => {
    btn.addEventListener('click', () => {
      setTimeout(() => {
        loadFilterOptions();
        renderProductGrid('shopGrid', currentFilters);
      }, 0);
    });
  });
});
