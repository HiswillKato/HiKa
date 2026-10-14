/* ==========================================================================
   hika — website behaviour
   Plain JavaScript, no libraries. Every block first checks that the element
   it needs is on the page, so the one file is safe to load anywhere.
   ========================================================================== */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        /* ------------------------------------------------------------------
           1. THEME BUTTONS
           The colour is chosen before the page paints by the tiny script in
           the head. This only handles the button and remembers the choice.
           Mobile has its own copy by the navbar toggler, so every match on
           the page is wired — both copies stay in step.
           ------------------------------------------------------------------ */
        var toggles = document.querySelectorAll('.theme-toggle');

        toggles.forEach(function (toggle) {
            var moon = toggle.querySelector('.fa-moon');
            var sun = toggle.querySelector('.fa-sun');

            var reflect = function () {
                var dark = document.documentElement.getAttribute('data-theme') === 'dark';
                moon.style.display = dark ? '' : 'none';
                sun.style.display = dark ? 'none' : '';
                toggle.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
            };

            reflect();

            toggle.addEventListener('click', function () {
                var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                document.documentElement.setAttribute('data-bs-theme', next);
                try {
                    localStorage.setItem('hika-theme', next);
                } catch (error) {
                    /* Private browsing can block storage. Not a problem. */
                }
                reflect();
            });
        });

        /* ------------------------------------------------------------------
           2. BACK TO TOP
           ------------------------------------------------------------------ */
        var top = document.getElementById('top');

        if (top) {
            window.addEventListener('scroll', function () {
                top.hidden = window.scrollY < 450;
            }, { passive: true });

            top.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        /* ------------------------------------------------------------------
           3. COPY A VALUE TO THE CLIPBOARD
           An element with data-copy holds the text; one with data-copy-done
           receives the little confirmation.
           ------------------------------------------------------------------ */
        document.querySelectorAll('[data-copy]').forEach(function (button) {
            button.addEventListener('click', function () {
                var text = button.getAttribute('data-copy');
                var done = document.querySelector('[data-copy-done]');

                var confirm = function () {
                    if (done) {
                        done.textContent = 'Copied';
                        window.setTimeout(function () { done.textContent = ''; }, 2000);
                    } else {
                        button.classList.add('copied');
                        window.setTimeout(function () { button.classList.remove('copied'); }, 1200);
                    }
                };

                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(confirm);
                } else {
                    var helper = document.createElement('textarea');
                    helper.value = text;
                    document.body.appendChild(helper);
                    helper.select();
                    document.execCommand('copy');
                    document.body.removeChild(helper);
                    confirm();
                }
            });
        });

        /* ------------------------------------------------------------------
           4. THE TYPING LINE IN THE HERO
           The element carries a JSON array of strings. They type in, wait,
           rub out, and move to the next, forever.
           ------------------------------------------------------------------ */
        var typed = document.querySelector('[data-typing]');

        if (typed) {
            var lines = [];

            try {
                lines = JSON.parse(typed.getAttribute('data-typing')) || [];
            } catch (error) {
                lines = [];
            }

            if (lines.length) {
                var line = 0;
                var at = 0;
                var rubbing = false;

                var step = function () {
                    var text = lines[line];
                    typed.textContent = text.substring(0, at);

                    if (!rubbing && at < text.length) {
                        at++;
                        window.setTimeout(step, 70);
                    } else if (!rubbing) {
                        rubbing = true;
                        window.setTimeout(step, 1600);
                    } else if (at > 0) {
                        at--;
                        window.setTimeout(step, 35);
                    } else {
                        rubbing = false;
                        line = (line + 1) % lines.length;
                        window.setTimeout(step, 300);
                    }
                };

                step();
            }
        }

        /* ------------------------------------------------------------------
           5. REVEAL ON SCROLL
           Anything with .reveal fades up once, the first time it is seen.
           ------------------------------------------------------------------ */
        var reveals = document.querySelectorAll('.reveal');

        if (reveals.length && 'IntersectionObserver' in window) {
            var watcher = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in');
                        watcher.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            reveals.forEach(function (item) { watcher.observe(item); });
        } else {
            reveals.forEach(function (item) { item.classList.add('in'); });
        }

        /* ------------------------------------------------------------------
           6. THE YEAR IN THE FOOTER
           ------------------------------------------------------------------ */
        document.querySelectorAll('[data-year]').forEach(function (slot) {
            slot.textContent = new Date().getFullYear();
        });

        /* ------------------------------------------------------------------
           7. SLUG PREVIEW
           While a title is typed on a new project, show the address it will
           get. Marked [data-slug-new] because a saved address is frozen and
           must not be overwritten by the title.
           ------------------------------------------------------------------ */
        var slugSource = document.querySelector('[data-slug-source]');
        var slugPreview = document.querySelector('[data-slug-preview][data-slug-new]');

        if (slugSource && slugPreview) {
            var showSlug = function () {
                var text = slugSource.value.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s-]+/g, '-')
                    .replace(/^-+|-+$/g, '');

                slugPreview.textContent = text === '' ? 'your-project' : text;
            };

            slugSource.addEventListener('input', showSlug);
            showSlug();
        }
    });
})();
