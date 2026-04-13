document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu toggle
    const btnOpen = document.getElementById('btnOpenMenu');
    const btnClose = document.getElementById('btnCloseMenu');
    const overlay = document.getElementById('full-menu-overlay');
    const body = document.body;

    if (btnOpen && btnClose && overlay) {
        btnOpen.addEventListener('click', function(e) {
            e.preventDefault();
            overlay.classList.add('active');
            body.style.overflow = 'hidden';
        });

        btnClose.addEventListener('click', function() {
            overlay.classList.remove('active');
            body.style.overflow = '';
        });

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                body.style.overflow = '';
            }
        });
    }

    // Sticky header
    const header = document.getElementById('siteHeader');
    const placeholder = document.getElementById('header-placeholder');
    const scrollThreshold = 100;

    if (header && placeholder) {
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            if (currentScroll > scrollThreshold) {
                header.classList.add('is-fixed');
                header.classList.add('hide-top');
                placeholder.style.display = 'block';
            } else {
                header.classList.remove('is-fixed');
                header.classList.remove('hide-top');
                placeholder.style.display = 'none';
            }
        });
    }

    // Date & Time
    function updateDateTime() {
        const timeEl = document.getElementById('date-time');
        if (!timeEl) return;
        const now = new Date();
        const weekdays = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        const dayName = weekdays[now.getDay()];
        const date = now.toLocaleDateString('vi-VN');
        timeEl.innerText = `${dayName}, ${date}`;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // Weather API
    const city = 'Hanoi';
    const apiKey = '8356bc5ee72baf34994e81f6bdb35652';
    fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&lang=vi&appid=${apiKey}`)
        .then(res => res.json())
        .then(data => {
            if (data.cod !== 200) return;
            const locEl = document.getElementById('location');
            const tempEl = document.getElementById('temperature');
            const iconEl = document.getElementById('weather-icon');
            if(locEl) locEl.innerText = data.name;
            if(tempEl) tempEl.innerText = Math.round(data.main.temp) + '°C';
            if(iconEl) iconEl.src = `https://openweathermap.org/img/wn/${data.weather[0].icon}.png`;
        }).catch(err => console.log('Weather fetch error:', err));
});
