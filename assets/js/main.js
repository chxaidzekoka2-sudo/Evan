/**
 * ====================================================================
 * main.js — გვერდის ინტერაქტივობა: მენიუ, ენის ღილაკი, კალათა, popup.
 *
 * კალათა ინახება ბრაუზერის localStorage-ში — ანუ ეხლა ის მხოლოდ
 * ამ ბრაუზერში/მოწყობილობაზე მუშაობს, სერვერი მასზე არაფერს არ იცის.
 *
 * როცა PHP-ს დაწერ და checkout ღილაკს დააკავშირებ რეალურ backend-თან,
 * ორი გზა გაქვს:
 *   1) checkout ღილაკზე დაჭერისას, JS-ით გაუგზავნე კალათის შემცველობა
 *      fetch('/checkout.php', { method:'POST', body: JSON.stringify(cart) })-ით
 *   2) ან უბრალო <form method="POST" action="checkout.php"> გამოიყენე და
 *      cart-ის მონაცემები hidden input-ებში ჩაწერე submit-ის წინ.
 * ორივე მისაღებია.
 * ====================================================================
 */

const CART_KEY = 'evan_cart_v1';
const PROMO_SEEN_KEY = 'evan_promo_seen';
const PROMO_CODE = 'EVAN10';

/**
 * პროდუქტის სახელიდან ორი ინიციალის ამოღება (მაგ. "Oversized Wool Coat" -> "OW"),
 * კალათის ხაზზე პატარა წრისთვის. products.js-ს ეს აღარ სჭირდება (რეალურ
 * ფოტოებზე გადავედით), მაგრამ main.js-ის cart drawer-ს კვლავ სჭირდება.
 */
function monogram(name) {
  return (name || '').split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

function readCart() {
  try {
    return JSON.parse(localStorage.getItem(CART_KEY)) || [];
  } catch (e) {
    return [];
  }
}

function writeCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
  renderCartDrawer();
}

function addToCart(item) {
  const cart = readCart();
  const existing = cart.find(l => l.id === item.id && l.size === item.size);
  if (existing) {
    existing.qty += 1;
  } else {
    cart.push({ ...item, qty: 1 });
  }
  writeCart(cart);
  openCart();
}

function removeFromCart(index) {
  const cart = readCart();
  cart.splice(index, 1);
  writeCart(cart);
}

function money(n) {
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' GEL';
}

// ---------- Cart drawer rendering ----------
function renderCartDrawer() {
  const cart = readCart();
  const itemsEl = document.getElementById('cartItems');
  const countEl = document.getElementById('cartCount');
  const subtotalEl = document.getElementById('cartSubtotal');
  if (!itemsEl) return;

  const totalQty = cart.reduce((sum, l) => sum + l.qty, 0);
  if (countEl) countEl.textContent = totalQty;

  const lang = getCurrentLang();
  const emptyText = TRANSLATIONS[lang].cart_empty;

  if (cart.length === 0) {
    itemsEl.innerHTML = `<p class="cart-empty-msg">${emptyText}</p>`;
  } else {
    itemsEl.innerHTML = cart.map((l, i) => `
      <div class="cart-line">
        <div class="cart-line__mono">${monogram(l.name)}</div>
        <div class="cart-line__info">
          <span class="cart-line__name">${l.name}</span>
          <span class="cart-line__meta">${l.size} · ${l.qty} × ${money(l.price)}</span>
          <button class="cart-line__remove" data-remove="${i}">✕</button>
        </div>
      </div>
    `).join('');
  }

  const subtotal = cart.reduce((sum, l) => sum + l.qty * l.price, 0);
  if (subtotalEl) subtotalEl.textContent = money(subtotal);
}

function openCart() {
  document.getElementById('cartDrawer')?.classList.add('is-open');
  document.getElementById('overlay')?.classList.add('is-visible');
}
function closeCart() {
  document.getElementById('cartDrawer')?.classList.remove('is-open');
  document.getElementById('overlay')?.classList.remove('is-visible');
}

function openPromo() {
  document.getElementById('promoModal')?.classList.add('is-open');
  document.getElementById('overlay')?.classList.add('is-visible');
  sessionStorage.setItem(PROMO_SEEN_KEY, '1');
}
function closePromo() {
  document.getElementById('promoModal')?.classList.remove('is-open');
  if (!document.getElementById('cartDrawer')?.classList.contains('is-open')) {
    document.getElementById('overlay')?.classList.remove('is-visible');
  }
}

/**
 * ეს ფუნქცია იძახება products.js-დან, ყოველი ჯერზე რაც პროდუქტების
 * ბადე თავიდან იხატება — უკავშირებს ზომის ღილაკებსა და "დამატება
 * კალათაში" ღილაკებს ახლად დამატებულ ბარათებში.
 */
function bindProductCardEvents(container) {
  container.querySelectorAll('.product-card').forEach(card => {
    const pills = card.querySelectorAll('.size-pill');
    pills.forEach(pill => {
      pill.addEventListener('click', () => {
        pills.forEach(p => p.classList.remove('is-active'));
        pill.classList.add('is-active');
      });
    });

    card.querySelector('.add-to-cart-btn')?.addEventListener('click', () => {
      const activeSize = card.querySelector('.size-pill.is-active');
      addToCart({
        id: card.dataset.id,
        name: card.dataset.name,
        price: parseFloat(card.dataset.price),
        size: activeSize ? activeSize.textContent.trim() : ''
      });
    });

    // მობილურზე (hover არ არსებობს) - სურათზე ტაპი ხსნის ზომის პანელს
    const media = card.querySelector('.product-card__media');
    media?.addEventListener('click', (e) => {
      if (window.matchMedia('(hover: none)').matches) {
        const panel = card.querySelector('.product-card__sizes');
        const alreadyOpen = panel.classList.contains('is-touch-open');
        document.querySelectorAll('.product-card__sizes.is-touch-open')
          .forEach(p => p.classList.remove('is-touch-open'));
        if (!alreadyOpen) {
          e.preventDefault();
          panel.classList.add('is-touch-open');
        }
      }
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  renderCartDrawer();

  // მობილური მენიუს ჩართვა/გამორთვა
  document.getElementById('menuToggle')?.addEventListener('click', () => {
    document.getElementById('mobileNav')?.classList.toggle('is-open');
  });

  // ენის dropdown გახსნა/დახურვა
  const langSwitch = document.getElementById('langSwitch');
  document.getElementById('langCurrent')?.addEventListener('click', (e) => {
    e.stopPropagation();
    langSwitch?.classList.toggle('is-open');
  });
  document.addEventListener('click', () => langSwitch?.classList.remove('is-open'));

  // ენის არჩევანზე დაჭერა
  document.querySelectorAll('[data-lang-option]').forEach(btn => {
    btn.addEventListener('click', () => {
      setLanguage(btn.getAttribute('data-lang-option'));
      renderCartDrawer(); // cart empty-text თარგმანიც განახლდეს
      langSwitch?.classList.remove('is-open');
    });
  });

  // კალათის გახსნა/დახურვა
  document.getElementById('cartToggle')?.addEventListener('click', openCart);
  document.getElementById('cartClose')?.addEventListener('click', closeCart);
  document.getElementById('cartContinue')?.addEventListener('click', closeCart);
  document.getElementById('overlay')?.addEventListener('click', () => {
    closeCart();
    closePromo();
  });

  // ნივთის წაშლა კალათიდან (ერთი listener ყველა ღილაკზე, რადგან ისინი დინამიურად იხატება)
  document.getElementById('cartItems')?.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-remove]');
    if (btn) removeFromCart(Number(btn.dataset.remove));
  });

  // მისალმების popup — სესიაში ერთხელ
  if (!sessionStorage.getItem(PROMO_SEEN_KEY)) {
    setTimeout(openPromo, 1200);
  }
  document.getElementById('promoClose')?.addEventListener('click', closePromo);
  document.getElementById('promoCopy')?.addEventListener('click', () => {
    navigator.clipboard?.writeText(PROMO_CODE).then(() => {
      const btn = document.getElementById('promoCopy');
      const original = btn.textContent;
      btn.textContent = '✓';
      setTimeout(() => (btn.textContent = original), 1500);
    });
  });
});
