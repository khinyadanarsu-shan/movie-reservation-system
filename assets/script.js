document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('seatForm');
  if (!form) return;

  const BASE = (typeof window.BASE_PRICE === 'number') ? window.BASE_PRICE : 0;
  const countEl = document.getElementById('count');
  const totalEl = document.getElementById('total');

  function recalc() {
    let count = 0;
    let total = 0;
    document.querySelectorAll('.seat input[type=checkbox]').forEach(chk => {
      if (chk.checked) {
        count++;
        const isCouple = chk.closest('.seat').classList.contains('couple');
        total += isCouple ? BASE * 1.5 : BASE;
      }
    });
    countEl.textContent = String(count);
    totalEl.textContent = total.toFixed(2);
  }

  document.querySelectorAll('.seat input[type=checkbox]').forEach(chk => {
    chk.addEventListener('change', recalc);
  });
  recalc();
});

