(() => {
    const minimumLoadingTime = 5000;
    const loadingStartedAt = performance.now();

    document.documentElement.classList.add('page-loading');

    window.addEventListener('load', () => {
        const remainingTime = Math.max(
            0,
            minimumLoadingTime - (performance.now() - loadingStartedAt)
        );

        window.setTimeout(() => {
            document.documentElement.classList.remove('page-loading');
        }, remainingTime);
    }, { once: true });
})();
