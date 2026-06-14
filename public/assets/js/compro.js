/* ======================================================================
   KAWULO HALAL — MODERN THEME JS
   Interactions: navbar scroll, scroll progress bar, animated counters,
   ripple buttons, back-to-top, mobile menu auto-close
   ====================================================================== */

(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        initAOS();
        initScrollProgress();
        initNavbarScroll();
        initCounters();
        initRipple();
        initBackToTop();
        initMobileMenuAutoClose();
    });

    /* ---------------------------------------------------------------
       AOS (Animate on Scroll) init
    --------------------------------------------------------------- */
    function initAOS() {
        if (typeof AOS !== "undefined") {
            AOS.init({
                duration: 800,
                once: true,
                offset: 100,
                easing: "ease-out-cubic"
            });
        }
    }

    /* ---------------------------------------------------------------
       Top scroll progress bar
    --------------------------------------------------------------- */
    function initScrollProgress() {
        const bar = document.createElement("div");
        bar.className = "scroll-progress";
        document.body.appendChild(bar);

        window.addEventListener("scroll", function () {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
            bar.style.width = progress + "%";
        });
    }

    /* ---------------------------------------------------------------
       Navbar background change on scroll
    --------------------------------------------------------------- */
    function initNavbarScroll() {
        const navbar = document.getElementById("navbar");
        if (!navbar) return;

        function toggleScrolled() {
            if (window.scrollY > 50) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        }

        toggleScrolled();
        window.addEventListener("scroll", toggleScrolled);
    }

    /* ---------------------------------------------------------------
       Animated counters for stats section
    --------------------------------------------------------------- */
    function initCounters() {
        const statsSection = document.getElementById("stats");
        if (!statsSection) return;

        function animateCounter(el) {
            const target = parseInt(el.dataset.target, 10) || 0;
            const duration = 2000;
            const start = performance.now();

            function update(now) {
                const elapsed = now - start;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const value = Math.floor(eased * target);
                el.textContent = value.toLocaleString("id-ID") + (target > 100 ? "+" : "");
                if (progress < 1) requestAnimationFrame(update);
            }
            requestAnimationFrame(update);
        }

        const counterObserver = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.querySelectorAll(".stat-number").forEach(animateCounter);
                        counterObserver.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.5 }
        );

        counterObserver.observe(statsSection);
    }

    /* ---------------------------------------------------------------
       Ripple effect on buttons (.btn-kh, .btn-kh-outline)
    --------------------------------------------------------------- */
    function initRipple() {
        const buttons = document.querySelectorAll(".btn-kh, .btn-kh-outline");

        buttons.forEach(function (btn) {
            btn.classList.add("btn-ripple");
            btn.addEventListener("click", function (e) {
                const rect = btn.getBoundingClientRect();
                const circle = document.createElement("span");
                const size = Math.max(rect.width, rect.height);

                circle.className = "ripple-circle";
                circle.style.width = circle.style.height = size + "px";
                circle.style.left = (e.clientX - rect.left - size / 2) + "px";
                circle.style.top = (e.clientY - rect.top - size / 2) + "px";

                btn.appendChild(circle);

                setTimeout(function () {
                    circle.remove();
                }, 600);
            });
        });
    }

    /* ---------------------------------------------------------------
       Back to top button visibility + smooth scroll
    --------------------------------------------------------------- */
    function initBackToTop() {
        const backToTop = document.getElementById("back-to-top");
        if (!backToTop) return;

        function toggleVisibility() {
            if (window.scrollY > 300) {
                backToTop.style.display = "flex";
                backToTop.style.opacity = "1";
            } else {
                backToTop.style.opacity = "0";
                setTimeout(function () {
                    if (window.scrollY <= 300) backToTop.style.display = "none";
                }, 300);
            }
        }

        backToTop.style.transition = "opacity 0.3s ease";
        backToTop.style.display = "none";
        toggleVisibility();
        window.addEventListener("scroll", toggleVisibility);

        backToTop.addEventListener("click", function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }

    /* ---------------------------------------------------------------
       Auto close mobile menu after clicking a nav link
    --------------------------------------------------------------- */
    function initMobileMenuAutoClose() {
        const navLinks = document.querySelectorAll(".nav-link-kh, .navbar-collapse .btn");
        const collapseEl = document.getElementById("navbarSupportedContent");
        if (!collapseEl) return;

        navLinks.forEach(function (link) {
            link.addEventListener("click", function () {
                if (collapseEl.classList.contains("show")) {
                    if (typeof bootstrap !== "undefined") {
                        const collapse = bootstrap.Collapse.getOrCreateInstance(collapseEl);
                        collapse.hide();
                    } else {
                        collapseEl.classList.remove("show");
                    }
                }
            });
        });
    }

    /* Provided for backward compatibility with onclick="topFunction()" */
    window.topFunction = function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    };
})();