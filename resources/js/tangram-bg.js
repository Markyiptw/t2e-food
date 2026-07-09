import tangramUrl from '../images/tangram-bg.svg?url';

function initTangramBg() {
    const targets = document.querySelectorAll('[data-tangram-bg]');

    targets.forEach((target) => {
        const img = document.createElement('img');
        img.src = tangramUrl;
        img.alt = '';
        img.className =
            'pointer-events-none absolute inset-0 h-full w-full';
        img.setAttribute('aria-hidden', 'true');
        target.appendChild(img);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTangramBg);
} else {
    initTangramBg();
}
