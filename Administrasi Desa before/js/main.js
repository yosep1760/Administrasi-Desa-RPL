// ============================================
// NAMAWEB - Sistem Layanan Administrasi Desa
// File: main.js - JavaScript Utama
// ============================================

// ---- Jalankan setelah DOM siap ----
document.addEventListener('DOMContentLoaded', function () {

  // ---- Inisialisasi komponen berdasarkan halaman saat ini ----
  initNavbar();         // Navbar responsif
  initFAQ();            // Accordion FAQ
  initTimeline();       // Timeline interaktif
  initScrollAnimasi();  // Animasi scroll
  initSidebar();        // Sidebar dashboard
});

// ============================================
// NAVBAR
// ============================================
function initNavbar() {
  const navbar = document.getElementById('navbar');
  const tombolHamburger = document.getElementById('tombolHamburger');
  const menuNavbar = document.getElementById('menuNavbar');

  if (!navbar) return; // Keluar jika tidak ada navbar

  // Efek shadow saat scroll
  window.addEventListener('scroll', function () {
    if (window.scrollY > 20) {
      navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.08)';
    } else {
      navbar.style.boxShadow = 'none';
    }
  });

  // Toggle menu hamburger untuk mobile
  if (tombolHamburger && menuNavbar) {
    tombolHamburger.addEventListener('click', function () {
      const terbuka = menuNavbar.classList.toggle('terbuka');
      tombolHamburger.setAttribute('aria-expanded', terbuka);
    });
  }

  // Tutup menu saat link diklik (mobile)
  if (menuNavbar) {
    menuNavbar.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        menuNavbar.classList.remove('terbuka');
      });
    });
  }
}

// ============================================
// ACCORDION FAQ
// ============================================
function initFAQ() {
  const itemFAQ = document.querySelectorAll('.item-faq');

  itemFAQ.forEach(function (item) {
    const tombol = item.querySelector('.pertanyaan-faq');
    const jawaban = item.querySelector('.jawaban-faq');

    if (!tombol || !jawaban) return;

    tombol.addEventListener('click', function () {
      const sedangTerbuka = item.classList.contains('terbuka');

      // Tutup semua item FAQ yang terbuka
      itemFAQ.forEach(function (semua) {
        semua.classList.remove('terbuka');
        const j = semua.querySelector('.jawaban-faq');
        if (j) j.style.maxHeight = null;
      });

      // Buka item yang diklik (jika belum terbuka)
      if (!sedangTerbuka) {
        item.classList.add('terbuka');
        jawaban.style.maxHeight = jawaban.scrollHeight + 'px';
      }
    });
  });
}

// ============================================
// TIMELINE ALUR PENGAJUAN (INTERAKTIF)
// ============================================
function initTimeline() {
  const itemTimeline = document.querySelectorAll('.item-timeline');

  itemTimeline.forEach(function (item) {
    item.addEventListener('click', function () {
      // Hapus kelas aktif dari semua item
      itemTimeline.forEach(function (semua) {
        semua.classList.remove('aktif');
      });
      // Tambahkan kelas aktif pada item yang diklik
      item.classList.add('aktif');
    });
  });
}

// ============================================
// ANIMASI SAAT SCROLL
// ============================================
function initScrollAnimasi() {
  // Observasi elemen untuk animasi
  const observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-in');
          observer.unobserve(entry.target); // Hentikan observasi setelah animasi
        }
      });
    },
    { threshold: 0.1, rootMargin: '0px 0px -50px 0px' }
  );

  // Tambahkan observasi ke elemen yang perlu dianimasikan
  document.querySelectorAll('.kartu-layanan, .kartu-statistik, .item-timeline').forEach(function (el) {
    observer.observe(el);
  });
}

// ============================================
// SIDEBAR DASHBOARD
// ============================================
function initSidebar() {
  const sidebar = document.getElementById('sidebar');
  const tombolBuka = document.getElementById('tombolBukaSidebar');
  const overlay = document.getElementById('overlaySidebar');

  if (!sidebar) return; // Keluar jika tidak ada sidebar

  // Fungsi buka sidebar
  function bukaSidebar() {
    sidebar.classList.add('terbuka');
    if (overlay) overlay.classList.add('aktif');
    document.body.style.overflow = 'hidden';
  }

  // Fungsi tutup sidebar
  function tutupSidebar() {
    sidebar.classList.remove('terbuka');
    if (overlay) overlay.classList.remove('aktif');
    document.body.style.overflow = '';
  }

  // Tombol hamburger membuka sidebar
  if (tombolBuka) {
    tombolBuka.addEventListener('click', bukaSidebar);
  }

  // Overlay menutup sidebar
  if (overlay) {
    overlay.addEventListener('click', tutupSidebar);
  }

  // Tandai link aktif berdasarkan halaman saat ini
  const urlSaatIni = window.location.pathname.split('/').pop();
  sidebar.querySelectorAll('.sidebar-link').forEach(function (link) {
    const href = link.getAttribute('href') || '';
    if (href === urlSaatIni || (urlSaatIni === '' && href === 'dashboard.html')) {
      link.classList.add('aktif');
    }
  });

  // Toggle sub-menu di sidebar
  document.querySelectorAll('.sidebar-label').forEach(function (label) {
    label.addEventListener('click', function () {
      const ikonEl = label.querySelector('.sidebar-label-ikon');
      const subMenu = label.nextElementSibling;

      // Toggle ikon panah
      if (ikonEl) {
        const sedangTerbuka = ikonEl.textContent.includes('∧');
        ikonEl.textContent = sedangTerbuka ? '∨' : '∧';
      }

      // Toggle visibilitas sub-menu
      if (subMenu && subMenu.classList.contains('sidebar-sub')) {
        subMenu.style.display =
          subMenu.style.display === 'none' ? '' : 'none';
      }
    });
  });
}

// ============================================
// VALIDASI FORM AUTENTIKASI
// ============================================

/**
 * Validasi form login
 * @param {Event} e - Event submit form
 */
function validasiLogin(e) {
  e.preventDefault();
  const username = document.getElementById('loginUsername').value.trim();
  const password = document.getElementById('loginPassword').value;
  const pesanError = document.getElementById('pesanErrorLogin');

  // Bersihkan pesan error sebelumnya
  if (pesanError) pesanError.style.display = 'none';

  // Validasi input kosong
  if (!username || !password) {
    tampilkanError(pesanError, 'Mohon isi username dan password!');
    return false;
  }

  // Validasi panjang password minimal
  if (password.length < 6) {
    tampilkanError(pesanError, 'Password minimal 6 karakter!');
    return false;
  }

  // Simulasi login berhasil - arahkan ke dashboard
  tampilkanLoading(e.submitter);

  setTimeout(function () {
    // Simpan info user di session (simulasi)
    sessionStorage.setItem('user', JSON.stringify({ nama: username, peran: 'Warga' }));
    window.location.href = 'dashboard.html';
  }, 800);
}

/**
 * Validasi form register
 * @param {Event} e - Event submit form
 */
function validasiRegister(e) {
  e.preventDefault();
  const username = document.getElementById('regUsername').value.trim();
  const email = document.getElementById('regEmail').value.trim();
  const password = document.getElementById('regPassword').value;
  const pesanError = document.getElementById('pesanErrorReg');

  if (pesanError) pesanError.style.display = 'none';

  // Validasi field kosong
  if (!username || !email || !password) {
    tampilkanError(pesanError, 'Semua field harus diisi!');
    return false;
  }

  // Validasi format email
  const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!reEmail.test(email)) {
    tampilkanError(pesanError, 'Format email tidak valid!');
    return false;
  }

  // Validasi panjang password
  if (password.length < 6) {
    tampilkanError(pesanError, 'Password minimal 6 karakter!');
    return false;
  }

  // Simulasi register berhasil
  tampilkanLoading(e.submitter);

  setTimeout(function () {
    alert('Registrasi berhasil! Silakan login.');
    window.location.href = 'login.html';
  }, 800);
}

// ---- Helper: Tampilkan pesan error ----
function tampilkanError(elPesan, teks) {
  if (!elPesan) {
    alert(teks);
    return;
  }
  elPesan.textContent = teks;
  elPesan.style.display = 'flex';
}

// ---- Helper: Tampilkan loading pada tombol ----
function tampilkanLoading(tombol) {
  if (!tombol) return;
  const teksAsli = tombol.textContent;
  tombol.textContent = 'Memuat...';
  tombol.disabled = true;

  // Kembalikan teks setelah 3 detik (jika tidak dialihkan)
  setTimeout(function () {
    tombol.textContent = teksAsli;
    tombol.disabled = false;
  }, 3000);
}

// ============================================
// VALIDASI FORM PENGAJUAN SURAT
// ============================================

/**
 * Validasi dan kirim form pengajuan surat
 * @param {Event} e - Event submit form
 */
function kirimPengajuan(e) {
  e.preventDefault();

  // Kumpulkan semua input yang wajib diisi
  const inputWajib = e.target.querySelectorAll('[required]');
  let valid = true;

  inputWajib.forEach(function (input) {
    if (!input.value.trim()) {
      // Tandai field yang kosong
      input.style.borderColor = 'var(--warna-bahaya)';
      valid = false;
    } else {
      input.style.borderColor = ''; // Reset border jika sudah diisi
    }
  });

  if (!valid) {
    alert('Mohon lengkapi semua field yang wajib diisi!');
    return;
  }

  // Simulasi pengiriman berhasil
  tampilkanLoading(e.submitter);

  setTimeout(function () {
    alert('Pengajuan surat berhasil dikirim! Anda akan diarahkan ke halaman riwayat.');
    window.location.href = 'riwayat.html';
  }, 1000);
}

// ============================================
// UTILITAS UMUM
// ============================================

/**
 * Format tanggal ke format Indonesia
 * @param {string|Date} tanggal - Tanggal yang akan diformat
 * @returns {string} Tanggal dalam format "DD Bulan YYYY"
 */
function formatTanggal(tanggal) {
  const namaBulan = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];
  const d = new Date(tanggal);
  return d.getDate() + ' ' + namaBulan[d.getMonth()] + ' ' + d.getFullYear();
}

/**
 * Scroll halus ke elemen dengan ID tertentu
 * @param {string} idElemen - ID elemen tujuan
 */
function scrollKe(idElemen) {
  const elemen = document.getElementById(idElemen);
  if (elemen) {
    elemen.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

/**
 * Scroll kembali ke paling atas halaman
 */
function scrollKeAtas() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================
// FUNGSI HALAMAN SPESIFIK
// ============================================

/**
 * Filter tabel berdasarkan input pencarian
 * @param {string} idInput - ID input pencarian
 * @param {string} idTabel - ID tabel yang difilter
 */
function filterTabel(idInput, idTabel) {
  const input = document.getElementById(idInput);
  const tabel = document.getElementById(idTabel);

  if (!input || !tabel) return;

  input.addEventListener('input', function () {
    const query = this.value.toLowerCase();
    const baris = tabel.querySelectorAll('tbody tr');

    baris.forEach(function (bari) {
      const teks = bari.textContent.toLowerCase();
      bari.style.display = teks.includes(query) ? '' : 'none';
    });
  });
}

/**
 * Reset form pengajuan surat
 * @param {string} idForm - ID form yang akan direset
 */
function resetForm(idForm) {
  const form = document.getElementById(idForm);
  if (form) {
    form.reset();
    // Reset semua border merah validasi
    form.querySelectorAll('input, select, textarea').forEach(function (el) {
      el.style.borderColor = '';
    });
  }
}

/**
 * Tampilkan nama file yang dipilih pada input file
 * @param {HTMLInputElement} input - Elemen input file
 * @param {string} idTampilan - ID elemen untuk menampilkan nama file
 */
function tampilkanNamaFile(input, idTampilan) {
  const elTampilan = document.getElementById(idTampilan);
  if (!elTampilan) return;

  if (input.files && input.files.length > 0) {
    elTampilan.textContent = input.files[0].name;
  } else {
    elTampilan.textContent = 'Tidak ada file dipilih';
  }
}

/**
 * Logout pengguna dan hapus sesi
 */
function logout() {
  if (confirm('Apakah Anda yakin ingin keluar?')) {
    sessionStorage.removeItem('user');
    window.location.href = 'login.html';
  }
}

/**
 * Pagination untuk tabel (sederhana)
 * Menampilkan X baris per halaman
 * @param {string} idTabel - ID tabel
 * @param {number} perHalaman - Jumlah baris per halaman
 */
function initPaginasiTabel(idTabel, perHalaman) {
  const tabel = document.getElementById(idTabel);
  if (!tabel) return;

  const semuaBaris = Array.from(tabel.querySelectorAll('tbody tr'));
  let halamanSaatIni = 1;
  const totalHalaman = Math.ceil(semuaBaris.length / perHalaman);

  // Tampilkan baris untuk halaman tertentu
  function tampilHalaman(halaman) {
    const mulai = (halaman - 1) * perHalaman;
    const akhir = mulai + perHalaman;

    semuaBaris.forEach(function (baris, indeks) {
      baris.style.display = indeks >= mulai && indeks < akhir ? '' : 'none';
    });
  }

  tampilHalaman(halamanSaatIni);
  return { total: totalHalaman, tampil: tampilHalaman };
}
