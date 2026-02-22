/**
 * PROOMNES — Interactive Animation Engine v3.0
 * Scroll animations, parallax, counters, tilt effects, smooth interactions
 */
(function () {
  'use strict';

  /* ──────────────────────────────────────────
     1. SCROLL-REVEAL (IntersectionObserver)
     Supports: .fade-up, .fade-left, .fade-right, .scale-in, .rotate-in, .blur-in
  ────────────────────────────────────────── */
  function initScrollReveal() {
    var selectors = '.fade-up, .fade-left, .fade-right, .scale-in, .rotate-in, .blur-in';
    var els = document.querySelectorAll(selectors);
    if (!els.length) return;

    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            e.target.classList.add('visible');
            io.unobserve(e.target);
          }
        });
      }, { threshold: 0.06, rootMargin: '0px 0px -50px 0px' });
      els.forEach(function (el) { io.observe(el); });
    } else {
      els.forEach(function (el) { el.classList.add('visible'); });
    }
  }

  /* ──────────────────────────────────────────
     2. ANIMATED COUNTER (counts up to data-count)
  ────────────────────────────────────────── */
  function initCounters() {
    var counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (!e.isIntersecting) return;
        var el = e.target;
        var target = parseInt(el.getAttribute('data-count'), 10) || 0;
        var suffix = el.getAttribute('data-suffix') || '';
        var prefix = el.getAttribute('data-prefix') || '';
        var duration = parseInt(el.getAttribute('data-duration'), 10) || 2000;
        var start = 0;
        var startTime = null;

        function easeOutExpo(t) { return t === 1 ? 1 : 1 - Math.pow(2, -10 * t); }

        function step(ts) {
          if (!startTime) startTime = ts;
          var progress = Math.min((ts - startTime) / duration, 1);
          var current = Math.floor(easeOutExpo(progress) * target);
          el.textContent = prefix + current.toLocaleString() + suffix;
          if (progress < 1) requestAnimationFrame(step);
          else el.textContent = prefix + target.toLocaleString() + suffix;
        }
        requestAnimationFrame(step);
        io.unobserve(el);
      });
    }, { threshold: 0.3 });

    counters.forEach(function (c) { io.observe(c); });
  }

  /* ──────────────────────────────────────────
     3. BACK TO TOP BUTTON
  ────────────────────────────────────────── */
  function initBackToTop() {
    var btn = document.getElementById('backToTop');
    if (!btn) {
      // Create button dynamically
      btn = document.createElement('button');
      btn.id = 'backToTop';
      btn.className = 'back-to-top';
      btn.setAttribute('aria-label', 'Back to top');
      btn.innerHTML = '<svg viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg>';
      document.body.appendChild(btn);
    }

    var ticking = false;
    window.addEventListener('scroll', function () {
      if (!ticking) {
        requestAnimationFrame(function () {
          btn.classList.toggle('visible', window.scrollY > 400);
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });

    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ──────────────────────────────────────────
     4. READING PROGRESS BAR
  ────────────────────────────────────────── */
  function initProgressBar() {
    var bar = document.querySelector('.reading-progress');
    if (!bar) {
      bar = document.createElement('div');
      bar.className = 'reading-progress';
      document.body.prepend(bar);
    }

    var ticking = false;
    window.addEventListener('scroll', function () {
      if (!ticking) {
        requestAnimationFrame(function () {
          var scrollTop = window.scrollY;
          var docHeight = document.documentElement.scrollHeight - window.innerHeight;
          var progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
          bar.style.width = progress + '%';
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }

  /* ──────────────────────────────────────────
     5. CARD TILT EFFECT (3D hover)
  ────────────────────────────────────────── */
  function initTiltCards() {
    var cards = document.querySelectorAll('.card, .work-card, .plan');

    cards.forEach(function (card) {
      card.addEventListener('mousemove', function (e) {
        var rect = card.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        var centerX = rect.width / 2;
        var centerY = rect.height / 2;
        var rotateX = ((y - centerY) / centerY) * -4;
        var rotateY = ((x - centerX) / centerX) * 4;

        card.style.transform = 'perspective(800px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-4px)';
      });

      card.addEventListener('mouseleave', function () {
        card.style.transform = '';
      });
    });
  }

  /* ──────────────────────────────────────────
     6. MAGNETIC BUTTONS (subtle cursor follow)
  ────────────────────────────────────────── */
  function initMagneticButtons() {
    var btns = document.querySelectorAll('.btn-primary, .btn-outline');

    btns.forEach(function (btn) {
      btn.addEventListener('mousemove', function (e) {
        var rect = btn.getBoundingClientRect();
        var x = e.clientX - rect.left - rect.width / 2;
        var y = e.clientY - rect.top - rect.height / 2;
        btn.style.transform = 'translate(' + (x * 0.15) + 'px, ' + (y * 0.15) + 'px) translateY(-2px)';
      });
      btn.addEventListener('mouseleave', function () {
        btn.style.transform = '';
      });
    });
  }

  /* ──────────────────────────────────────────
     7. PARALLAX SECTIONS (subtle)
  ────────────────────────────────────────── */
  function initParallax() {
    var parallaxEls = document.querySelectorAll('.hero .visual, .hero::before');
    if (!parallaxEls.length && !document.querySelector('.hero')) return;

    var visual = document.querySelector('.hero .visual');
    if (!visual) return;

    var ticking = false;
    window.addEventListener('scroll', function () {
      if (!ticking) {
        requestAnimationFrame(function () {
          var scrolled = window.scrollY;
          if (scrolled < 800) {
            visual.style.transform = 'translateY(' + (scrolled * 0.08) + 'px)';
          }
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }

  /* ──────────────────────────────────────────
     8. SMOOTH REVEAL FOR SECTIONS
  ────────────────────────────────────────── */
  function initSectionReveal() {
    var sections = document.querySelectorAll('.section, section');
    if (!sections.length) return;

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('section-visible');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.05 });

    sections.forEach(function (s) { io.observe(s); });
  }

  /* ──────────────────────────────────────────
     9. TYPING EFFECT (optional, for hero badge)
  ────────────────────────────────────────── */
  function initTypewriter() {
    var el = document.querySelector('[data-typewriter]');
    if (!el) return;
    
    var words = (el.getAttribute('data-typewriter') || '').split('|');
    if (!words.length) return;

    var wordIndex = 0;
    var charIndex = 0;
    var isDeleting = false;
    var speed = 100;

    function type() {
      var current = words[wordIndex];
      if (isDeleting) {
        el.textContent = current.substring(0, charIndex - 1);
        charIndex--;
        speed = 50;
      } else {
        el.textContent = current.substring(0, charIndex + 1);
        charIndex++;
        speed = 100;
      }

      if (!isDeleting && charIndex === current.length) {
        speed = 2000;
        isDeleting = true;
      } else if (isDeleting && charIndex === 0) {
        isDeleting = false;
        wordIndex = (wordIndex + 1) % words.length;
        speed = 500;
      }

      setTimeout(type, speed);
    }
    type();
  }

  /* ──────────────────────────────────────────
     10. NAVBAR ENHANCED
  ────────────────────────────────────────── */
  function initNavbar() {
    var nav = document.querySelector('.navbar');
    if (!nav) return;

    var lastScroll = 0;
    var ticking = false;

    window.addEventListener('scroll', function () {
      if (!ticking) {
        requestAnimationFrame(function () {
          var currentScroll = window.scrollY;
          nav.classList.toggle('scrolled', currentScroll > 10);

          // Auto-hide on scroll down, show on scroll up (mobile only)
          if (window.innerWidth < 768) {
            if (currentScroll > lastScroll && currentScroll > 100) {
              nav.style.transform = 'translateY(-100%)';
            } else {
              nav.style.transform = 'translateY(0)';
            }
          } else {
            nav.style.transform = '';
          }
          lastScroll = currentScroll;
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });

    // Add transition for navbar hide/show
    nav.style.transition = 'transform 0.3s cubic-bezier(0.4,0,0.2,1), background 0.4s, box-shadow 0.25s';
  }

  /* ──────────────────────────────────────────
     11. RIPPLE EFFECT ON BUTTONS
  ────────────────────────────────────────── */
  function initRipple() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.btn');
      if (!btn) return;

      var rect = btn.getBoundingClientRect();
      var x = ((e.clientX - rect.left) / rect.width) * 100;
      var y = ((e.clientY - rect.top) / rect.height) * 100;
      btn.style.setProperty('--ripple-x', x + '%');
      btn.style.setProperty('--ripple-y', y + '%');
    });
  }

  /* ──────────────────────────────────────────
     12. SMOOTH ANCHOR SCROLL
  ────────────────────────────────────────── */
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var target = document.querySelector(a.getAttribute('href'));
        if (target) {
          e.preventDefault();
          var offset = 80; // account for sticky navbar
          var top = target.getBoundingClientRect().top + window.scrollY - offset;
          window.scrollTo({ top: top, behavior: 'smooth' });
        }
      });
    });
  }

  /* ──────────────────────────────────────────
     13. LAZY LOAD IMAGES
  ────────────────────────────────────────── */
  function initLazyImages() {
    var images = document.querySelectorAll('img[data-src]');
    if (!images.length) return;

    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var img = entry.target;
            img.src = img.getAttribute('data-src');
            img.removeAttribute('data-src');
            img.classList.add('loaded');
            io.unobserve(img);
          }
        });
      }, { rootMargin: '100px' });
      images.forEach(function (img) { io.observe(img); });
    } else {
      images.forEach(function (img) {
        img.src = img.getAttribute('data-src');
        img.removeAttribute('data-src');
      });
    }
  }

  /* ──────────────────────────────────────────
     INIT ALL
  ────────────────────────────────────────── */
  function init() {
    initScrollReveal();
    initCounters();
    initBackToTop();
    initProgressBar();
    initNavbar();
    initRipple();
    initSmoothScroll();
    initLazyImages();
    initTypewriter();
    initSectionReveal();

    // Desktop-only effects (skip on mobile for performance)
    if (window.innerWidth > 768) {
      initTiltCards();
      initMagneticButtons();
      initParallax();
    }
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();