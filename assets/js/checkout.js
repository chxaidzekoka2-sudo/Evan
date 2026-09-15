/**
 * ====================================================================
 * checkout.js — checkout.php-ის ლოგიკა.
 * კითხულობს კალათას localStorage-დან (main.js-ის readCart()), აჩვენებს
 * summary-ს, submit-ზე უგზავნის /api/create-order.php-ს და გადამისამართებს
 * BOG-ის გადახდის გვერდზე.
 * ====================================================================
 */

function renderCheckoutSummary() {
  const cart = readCart();
  const summaryEl = document.getElementById('checkoutCartSummary');
  const totalEl = document.getElementById('checkoutTotal');
  const emptyMsg = document.getElementById('checkoutEmptyMsg');
  const content = document.getElementById('checkoutContent');

  if (cart.length === 0) {
    emptyMsg.style.display = 'block';
    content.style.display = 'none';
    return;
  }

  summaryEl.innerHTML = cart.map(l => `
    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--blush); font-size:14px;">
      <span>${l.name} ${l.size ? `(${l.size})` : ''} × ${l.qty}</span>
      <span>${(l.price * l.qty).toFixed(2)} GEL</span>
    </div>
  `).join('');

  const total = cart.reduce((sum, l) => sum + l.qty * l.price, 0);
  totalEl.textContent = money(total);
}

document.addEventListener('DOMContentLoaded', () => {
  renderCheckoutSummary();

  document.getElementById('checkoutForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const cart = readCart();
    if (cart.length === 0) return;

    const errorEl = document.getElementById('checkoutError');
    const submitBtn = document.getElementById('checkoutSubmitBtn');
    errorEl.style.display = 'none';
    submitBtn.disabled = true;
    submitBtn.textContent = '...';

    const formData = new FormData(e.target);
    const payload = {
      customer: {
        name: formData.get('name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        address: formData.get('address'),
      },
      cart: cart.map(l => ({ id: l.id, name: l.name, price: l.price, qty: l.qty, size: l.size })),
    };

    try {
      const res = await fetch(`${window.BASE_URL || ''}/api/create-order.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const data = await res.json();

      if (!res.ok || !data.redirect_url) {
        throw new Error(data.error || 'Something went wrong');
      }

      // შეკვეთა შექმნილია — გადავამისამართოთ BOG-ის გადახდის გვერდზე
      window.location.href = data.redirect_url;
    } catch (err) {
      errorEl.textContent = 'გადახდის დაწყება ვერ მოხერხდა. სცადეთ ისევ.';
      errorEl.style.display = 'block';
      submitBtn.disabled = false;
      submitBtn.textContent = 'გადახდაზე გადასვლა';
      console.error(err);
    }
  });
});
