document.addEventListener('DOMContentLoaded', function () {
  var track   = document.getElementById('produkTrack');
  var btnPrev = document.getElementById('btnPrev');
  var btnNext = document.getElementById('btnNext');

  if (track && btnPrev && btnNext) {
    var current = 0;
    var autoPlayDelay = 5000; // Geser otomatis setiap 5 detik

    function cardWidth() {
      var c = track.querySelector('.product-card');
      return c ? c.offsetWidth + 16 : 226;
    }

    function visible() {
      return Math.floor(track.parentElement.offsetWidth / cardWidth());
    }

    function total() {
      return track.querySelectorAll('.product-card').length;
    }

    function goTo(n) {
      var max = Math.max(0, total() - visible());
      current = n;
      
      // Loop: Kembali ke awal jika sudah di ujung
      if (current > max) current = 0;
      if (current < 0) current = max;

      track.style.transform = 'translateX(-' + (current * cardWidth()) + 'px)';
      btnPrev.style.opacity = current === 0 ? '0.4' : '1';
      btnNext.style.opacity = current >= max ? '0.4' : '1';
    }

    // Logika Otomatis
    var slideInterval = setInterval(function() {
      goTo(current + 1);
    }, autoPlayDelay);

    // Reset timer jika user klik manual
    function resetTimer() {
      clearInterval(slideInterval);
      slideInterval = setInterval(function() {
        goTo(current + 1);
      }, autoPlayDelay);
    }

    btnNext.addEventListener('click', function () { 
      goTo(current + 1); 
      resetTimer(); 
    });

    btnPrev.addEventListener('click', function () { 
      goTo(current - 1); 
      resetTimer(); 
    });

    goTo(0);
    window.addEventListener('resize', function () { goTo(current); });
  }
});