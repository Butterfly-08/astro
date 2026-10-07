(() => {
    document.documentElement.classList.add('page-loading');

    document.addEventListener('DOMContentLoaded', () => {
        document.documentElement.classList.remove('page-loading');
    }, { once: true });
})();
