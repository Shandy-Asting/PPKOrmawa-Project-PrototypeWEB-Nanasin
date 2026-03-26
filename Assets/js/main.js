/* ============================================================
   UMKM DESA — main.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  // ---- FILTER KATEGORI ----
  const filterBtns  = document.querySelectorAll('.btn-filter');
  const productItems = document.querySelectorAll('.product-item');

  filterBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filterBtns.forEach(function (b) { b.classList.remove('active'); });
      this.classList.add('active');

      var filter = this.dataset.filter;
      productItems.forEach(function (item) {
        if (filter === 'semua' || item.dataset.category === filter) {
          item.style.display = '';
          item.style.animation = 'fadeIn .3s ease';
        } else {
          item.style.display = 'none';
        }
      });
    });
  });

  // ---- TRACKING KLIK PRODUK ----
  document.querySelectorAll('[data-track-product]').forEach(function (el) {
    el.addEventListener('click', function () {
      fetch('../api/track.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'product', id: this.dataset.trackProduct })
      }).catch(function () {});
    });
  });

  // ---- TRACKING KLIK LINK MARKETPLACE ----
  document.querySelectorAll('[data-track-link]').forEach(function (el) {
    el.addEventListener('click', function () {
      fetch('../api/track.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'link', id: this.dataset.trackLink })
      }).catch(function () {});
    });
  });

  // ---- SMOOTH SCROLL untuk anchor link ----
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

});

// Animasi fadeIn untuk filter
(function () {
  var s = document.createElement('style');
  s.textContent = '@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }';
  document.head.appendChild(s);
})();