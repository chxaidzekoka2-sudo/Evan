/**
 * ====================================================================
 * product.js — product.php-ის ლოგიკა.
 * იტვირთება api/product-detail.php-დან, აგებს გალერეას/ინფოს,
 * კალათაში დამატებისთვის იყენებს main.js-ის addToCart()-ს.
 * ====================================================================
 */

let currentProduct = null;
let selectedSize = null;

async function loadProductDetail() {
  const container = document.getElementById('productDetail');
  if (!container) return;

  const productId = container.dataset.productId;
  const base = window.BASE_URL || '';

  if (!productId || productId === '0') {
    container.innerHTML = '<p class="grid-empty">Product not found.</p>';
    return;
  }

  try {
    const res = await fetch(`${base}/api/product-detail.php?id=${productId}&lang=${getCurrentLang()}`);
    if (!res.ok) throw new Error('not found');
    currentProduct = await res.json();
    renderProductDetail(currentProduct);
  } catch (err) {
    console.error(err);
    container.innerHTML = '<p class="grid-empty">Product not found.</p>';
  }
}

function renderProductDetail(p) {
  const container = document.getElementById('productDetail');
  selectedSize = null;

  const thumbsHtml = p.images.map((img, i) => `
    <img src="${img}" data-index="${i}" class="${i === 0 ? 'is-active' : ''}" alt="${escapeHtml(p.name)}">
  `).join('');

  const sizesHtml = p.sizes.map(s => `
    <button type="button" class="size-pill--detail" data-size="${s.label}" data-stock="${s.stock_qty}" ${s.stock_qty <= 0 ? 'disabled' : ''}>
      ${s.label}
    </button>
  `).join('');

  container.innerHTML = `
    <div class="product-detail__layout">
      <div class="product-gallery">
        <div class="product-gallery__main">
          <img src="${p.images[0]}" id="galleryMainImg" alt="${escapeHtml(p.name)}">
        </div>
        <div class="product-gallery__thumbs">${thumbsHtml}</div>
      </div>
      <div class="product-info">
        <span class="product-info__category">${escapeHtml(p.category)}</span>
        <h1 class="product-info__name">${escapeHtml(p.name)}</h1>
        <span class="product-info__price">${p.price.toFixed(2)} GEL</span>
        <p class="product-info__description">${escapeHtml(p.description || '')}</p>

        <div class="product-info__sizes">
          <h4 data-i18n="select_size">Select Size</h4>
          <div class="product-info__size-pills">${sizesHtml || '<span style="font-size:13px; opacity:.6;">—</span>'}</div>
          <p class="product-info__stock-msg" id="stockMsg"></p>
        </div>

        <button type="button" class="btn btn--dark btn--full" id="addToCartDetailBtn" data-i18n="add_to_cart">Add to Cart</button>
        <p class="product-info__added-msg" id="addedMsg" data-i18n="added_msg">Added to cart</p>
      </div>
    </div>
  `;

  applyLanguage();
  bindProductDetailEvents(p);
}

function bindProductDetailEvents(p) {
  const mainImg = document.getElementById('galleryMainImg');
  document.querySelectorAll('.product-gallery__thumbs img').forEach(thumb => {
    thumb.addEventListener('click', () => {
      document.querySelectorAll('.product-gallery__thumbs img').forEach(t => t.classList.remove('is-active'));
      thumb.classList.add('is-active');
      mainImg.src = p.images[thumb.dataset.index];
    });
  });

  const stockMsg = document.getElementById('stockMsg');
  document.querySelectorAll('.size-pill--detail').forEach(pill => {
    pill.addEventListener('click', () => {
      if (pill.disabled) return;
      document.querySelectorAll('.size-pill--detail').forEach(b => b.classList.remove('is-active'));
      pill.classList.add('is-active');
      selectedSize = pill.dataset.size;
      const stock = parseInt(pill.dataset.stock, 10);
      stockMsg.textContent = stock <= 5 ? `მხოლოდ ${stock} ცალია დარჩენილი` : '';
    });
  });

  document.getElementById('addToCartDetailBtn')?.addEventListener('click', () => {
    if (p.sizes.length > 0 && !selectedSize) {
      stockMsg.textContent = 'გთხოვთ აირჩიოთ ზომა';
      return;
    }
    addToCart({
      id: p.id,
      name: p.name,
      price: p.price,
      size: selectedSize || '',
    });

    const addedMsg = document.getElementById('addedMsg');
    addedMsg.classList.add('is-visible');
    setTimeout(() => addedMsg.classList.remove('is-visible'), 2000);
  });
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', () => {
  loadProductDetail();

  // ენის შეცვლისას პროდუქტის ხელახლა ჩატვირთვა (სახელი/აღწერა ითარგმნება)
  document.querySelectorAll('[data-lang-option]').forEach(btn => {
    btn.addEventListener('click', () => {
      setTimeout(loadProductDetail, 0);
    });
  });
});
