const images = import.meta.glob(
    '../images/hong-kong-view/*.{jpg,jpeg,png,webp}',
    {
        eager: true,
        query: '?url',
        import: 'default',
    },
);

const urls = Object.values(images);

function initHeroSlideshow() {
    const container = document.getElementById('hero-slideshow');

    if (container === null || urls.length === 0) {
        return;
    }

    const cycleSeconds = 64;
    const slotSeconds = cycleSeconds / urls.length;

    urls.forEach((url, index) => {
        const slide = document.createElement('div');
        slide.className = 'hero-slide';
        slide.style.backgroundImage = `url('${url}')`;
        slide.style.animationDelay = `-${(index * slotSeconds).toFixed(3)}s`;
        container.appendChild(slide);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroSlideshow);
} else {
    initHeroSlideshow();
}
