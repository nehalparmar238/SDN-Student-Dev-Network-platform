document.addEventListener('DOMContentLoaded', function () {
    var menuButton = document.querySelector('.menu-toggle');
    var navLinks = document.querySelector('#nav-links');

    if (menuButton && navLinks) {
        menuButton.addEventListener('click', function () {
            var isExpanded = menuButton.getAttribute('aria-expanded') === 'true';
            menuButton.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
            navLinks.classList.toggle('is-open', !isExpanded);
        });
    }
});

document.addEventListener('click', function (event) {
    if (!(event.target instanceof Element)) {
        return;
    }

    var confirmElement = event.target.closest('[data-confirm]');

    if (confirmElement && confirmElement.tagName !== 'FORM') {
        var message = confirmElement.getAttribute('data-confirm');

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    }
});

document.addEventListener('submit', function (event) {
    var form = event.target;

    if (form instanceof HTMLFormElement && form.hasAttribute('data-confirm')) {
        var message = form.getAttribute('data-confirm');

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    }
});