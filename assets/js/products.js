/**
 * ====================================================================
 * products.js — პროდუქტების ჩატვირთვა და ბარათების აწყობა.
 *
 * ნაბიჯი 2: DEMO_PRODUCTS-ის მაგივრად ეხლა რეალურ PHP API-დან იტვირთება
 * (api/products.php), PDO/MySQL ბაზიდან. ფოტოები რეალურია
 * (uploads/products/-დან), აღარ არის მონოგრამა-placeholder.
 * ====================================================================
 */

/**
 * @param {object} filters — { category, size, price_min, price_max, is_new }
 * @returns {Promise<Array>}
 */
async function fetchProducts(filters = {}) {
  const params = new URLSearchParams();
  params.set('lang', getCurrentLang());
  Object.entries(filters).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== '') {
      params.set(key, value);
    }
  });

  const base = window.BASE_URL || '';
  const res = await fetch(`${base}/api/products.php?${params.toString()}`);
  if (!res.ok) throw new Error('Failed to load products');
  return res.json();
}

function renderProductCard(p) {
  const base = window.BASE_URL || '';
  const sizesHtml = (p.sizes || [])
    .map((s, i) => `<button type="button" class="size-pill${i === 0 ? ' is-active' : ''}">${s}</button>`)
    .join('');

  return `
    <article class="product-card" data-id="${p.id}" data-name="${escapeHtml(p.name)}" data-price="${p.price}">
      <a href="${base}/product.php?id=${p.id}" class="product-card__media">
        <img src="${p.image}" alt="${escapeHtml(p.name)}" loading="lazy">
      </a>
      <div class="product-card__sizes">
        <span class="product-card__label" data-i18n="select_size">Select Size</span>
        <div class="size-pills">${sizesHtml}</div>
        <button type="button" class="btn btn--dark btn--full add-to-cart-btn" data-i18n="add_to_cart">Add to Cart</button>
      </div>
      <a href="${base}/product.php?id=${p.id}" class="product-card__info">
        <span class="product-card__name">${escapeHtml(p.name)}</span>
        <span class="product-card__price">${p.price.toFixed(2)} GEL</span>
      </a>
    </article>
  `;
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

async function renderProductGrid(containerId, filters = {}) {
  const el = document.getElementById(containerId);
  if (!el) return;

  el.innerHTML = '<p class="grid-loading">...</p>';

  try {
    const products = await fetchProducts(filters);
    if (products.length === 0) {
      el.innerHTML = `<p class="grid-empty" data-i18n="filter_all">No products found</p>`;
      applyLanguage();
      return;
    }
    el.innerHTML = products.map(renderProductCard).join('');
    applyLanguage();
    bindProductCardEvents(el);
  } catch (err) {
    console.error(err);
    el.innerHTML = '<p class="grid-empty">Error loading products.</p>';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  renderProductGrid('newInGrid', { is_new: 1 });
  renderProductGrid('shoesGrid', { category: 'shoes' });
});
