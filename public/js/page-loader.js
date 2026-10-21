(() => {
    document.documentElement.classList.add('page-loading');

    const removeLoader = () => {
        document.documentElement.classList.remove('page-loading');
    };

    if (document.readyState === 'interactive' || document.readyState === 'complete') {
        removeLoader();
    } else {
        document.addEventListener('DOMContentLoaded', removeLoader, { once: true });
    }

    // Safety timeout: dismiss quickly within 300ms so it never delays the user
    setTimeout(removeLoader, 300);
})();
