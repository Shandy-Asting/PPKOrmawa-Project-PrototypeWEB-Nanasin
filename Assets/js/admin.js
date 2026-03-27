/* ============================================================
   UMKM DESA — admin.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  // SIDEBAR TOGGLE (mobile)
  var toggle  = document.getElementById('sidebarToggle');
  var sidebar = document.getElementById('adminSidebar');
  var overlay = document.createElement('div');
  overlay.id = 'sidebarOverlay';
  overlay.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:199;';
  document.body.appendChild(overlay);

  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    });
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('open');
      overlay.style.display = 'none';
    });
  }

  // KONFIRMASI HAPUS
  document.querySelectorAll('.btn-delete[data-confirm]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      if (!confirm(this.dataset.confirm || 'Yakin ingin menghapus data ini?')) {
        e.preventDefault();
      }
    });
  });

  // AUTO HIDE ALERT setelah 4 detik
  document.querySelectorAll('.alert-umkm').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 500);
    }, 4000);
  });

  // PREVIEW nama file upload
  document.querySelectorAll('input[type="file"]').forEach(function (input) {
    input.addEventListener('change', function () {
      var label = document.querySelector('label[for="' + this.id + '"] .file-name');
      if (label && this.files[0]) {
        label.textContent = this.files[0].name;
      }
    });
  });

});