/**
 * AstroVani — Live Background Wallpaper Video & Celestial Constellation Engine
 * High-performance, resilient cosmic video background system with procedural starfield and sacred Vedic geometry.
 */
(function () {
    'use strict';

    // Theme definitions with verified ultra-reliable cosmic video sources
    const WALLPAPER_THEMES = {
        nebula: {
            id: 'nebula',
            name: 'Cosmic Nebula',
            icon: 'bi-stars',
            sources: [
                { src: 'https://svs.gsfc.nasa.gov/vis/a030000/a030000/a030020/stars-2mass_vertical_pan_1080p.mp4', type: 'video/mp4' },
                { src: 'https://svs.gsfc.nasa.gov/vis/a030000/a030000/a030020/stars-2mass_vertical_pan.webmhd.webm', type: 'video/webm' }
            ],
            colorTint: 'radial-gradient(circle at 50% 25%, rgba(26, 11, 46, 0.42) 0%, rgba(15, 5, 26, 0.72) 65%, rgba(9, 2, 18, 0.88) 100%)',
            particleColor: 'rgba(245, 176, 65, 0.85)'
        },
        galaxy: {
            id: 'galaxy',
            name: 'Galactic Plane',
            icon: 'bi-brightness-high',
            sources: [
                { src: 'https://svs.gsfc.nasa.gov/vis/a030000/a030000/a030020/2_MASS_Galactic_Plane_1280x720.mp4', type: 'video/mp4' },
                { src: 'https://svs.gsfc.nasa.gov/vis/a030000/a030000/a030020/2_MASS_Galactic_Plane_1280x720.webmhd.webm', type: 'video/webm' }
            ],
            colorTint: 'radial-gradient(circle at 40% 30%, rgba(20, 15, 55, 0.45) 0%, rgba(10, 5, 30, 0.75) 65%, rgba(6, 2, 15, 0.92) 100%)',
            particleColor: 'rgba(180, 195, 255, 0.85)'
        },
        zodiac: {
            id: 'zodiac',
            name: 'Vedic Starlight',
            icon: 'bi-gem',
            sources: [
                { src: 'https://svs.gsfc.nasa.gov/vis/a030000/a030000/a030020/stars-2mass_vertical_pan_1080p.mp4', type: 'video/mp4' }
            ],
            colorTint: 'radial-gradient(circle at 50% 35%, rgba(45, 18, 77, 0.45) 0%, rgba(26, 11, 46, 0.75) 65%, rgba(10, 4, 18, 0.92) 100%)',
            particleColor: 'rgba(255, 215, 0, 0.9)'
        }
    };

    // Default configuration
    const STORAGE_KEY = 'astrovani_wallpaper_pref_v1';
    let state = {
        theme: 'nebula',
        opacity: 65,
        isPlaying: true,
        particlesEnabled: true,
        soundEnabled: false
    };

    // Load saved settings from localStorage
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            state = Object.assign(state, JSON.parse(saved));
        }
    } catch (e) {
        console.warn('AstroVani Wallpaper: localStorage unavailable', e);
    }

    // Save helper
    function saveState() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch (e) {}
    }

    // Web Audio Chime Synthesizer (432Hz Sacred Cosmic Frequency)
    let audioCtx = null;
    let chimeInterval = null;

    function initAudio() {
        if (!audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                audioCtx = new AudioContext();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }

    function playSacredChime() {
        if (!state.soundEnabled || !audioCtx) return;
        try {
            const now = audioCtx.currentTime;
            // 432 Hz harmonic scale notes (A4 432Hz, E5 648Hz, C#5 540Hz)
            const notes = [432, 540, 648];
            const baseFreq = notes[Math.floor(Math.random() * notes.length)];

            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(baseFreq, now);

            // Gentle harmonic envelope
            gain.gain.setValueAtTime(0, now);
            gain.gain.linearRampToValueAtTime(0.04, now + 0.3);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + 4.5);

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.start(now);
            osc.stop(now + 4.6);
        } catch (e) {}
    }

    function toggleSound() {
        initAudio();
        state.soundEnabled = !state.soundEnabled;
        saveState();

        if (state.soundEnabled) {
            playSacredChime();
            if (!chimeInterval) {
                chimeInterval = setInterval(playSacredChime, 14000);
            }
        } else {
            if (chimeInterval) {
                clearInterval(chimeInterval);
                chimeInterval = null;
            }
        }
        updateControlUI();
    }

    // Live Video Elements
    let videoEl = null;
    let overlayEl = null;
    let canvasEl = null;
    let ctx = null;
    let animFrameId = null;

    // Canvas particle engine data
    const particles = [];
    const NUM_PARTICLES = 130;
    const shootingStars = [];
    let mouse = { x: -1000, y: -1000 };
    let zodiacRotation = 0;

    class Particle {
        constructor(w, h) {
            this.reset(w, h);
        }

        reset(w, h) {
            this.x = Math.random() * w;
            this.y = Math.random() * h;
            this.size = Math.random() * 2.2 + 0.5;
            this.baseSize = this.size;
            this.vx = (Math.random() - 0.5) * 0.35;
            this.vy = (Math.random() - 0.5) * 0.35;
            this.alpha = Math.random() * 0.7 + 0.3;
            this.twinkleSpeed = Math.random() * 0.03 + 0.008;
            this.twinklePhase = Math.random() * Math.PI * 2;
        }

        update(w, h) {
            this.x += this.vx;
            this.y += this.vy;
            this.twinklePhase += this.twinkleSpeed;

            if (this.x < 0) this.x = w;
            if (this.x > w) this.x = 0;
            if (this.y < 0) this.y = h;
            if (this.y > h) this.y = 0;

            // Mouse proximity interaction
            const dx = this.x - mouse.x;
            const dy = this.y - mouse.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < 120) {
                const angle = Math.atan2(dy, dx);
                this.x += Math.cos(angle) * 1.5;
                this.y += Math.sin(angle) * 1.5;
                this.size = this.baseSize * 1.6;
            } else {
                this.size = this.baseSize;
            }
        }

        draw(ctx) {
            const currentAlpha = Math.max(0.1, this.alpha + Math.sin(this.twinklePhase) * 0.35);
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(245, 176, 65, ${currentAlpha})`;
            ctx.fill();
        }
    }

    class ShootingStar {
        constructor(w, h) {
            this.reset(w, h);
        }

        reset(w, h) {
            this.x = Math.random() * w * 0.8;
            this.y = Math.random() * h * 0.4;
            this.len = Math.random() * 80 + 50;
            this.speed = Math.random() * 8 + 6;
            this.angle = Math.PI / 4 + (Math.random() - 0.5) * 0.2;
            this.vx = Math.cos(this.angle) * this.speed;
            this.vy = Math.sin(this.angle) * this.speed;
            this.opacity = 1;
            this.active = true;
        }

        update() {
            this.x += this.vx;
            this.y += this.vy;
            this.opacity -= 0.02;
            if (this.opacity <= 0) {
                this.active = false;
            }
        }

        draw(ctx) {
            if (!this.active) return;
            const tailX = this.x - Math.cos(this.angle) * this.len;
            const tailY = this.y - Math.sin(this.angle) * this.len;

            const grad = ctx.createLinearGradient(tailX, tailY, this.x, this.y);
            grad.addColorStop(0, 'rgba(245, 176, 65, 0)');
            grad.addColorStop(1, `rgba(255, 255, 255, ${this.opacity})`);

            ctx.beginPath();
            ctx.moveTo(tailX, tailY);
            ctx.lineTo(this.x, this.y);
            ctx.strokeStyle = grad;
            ctx.lineWidth = 1.6;
            ctx.stroke();
        }
    }

    // Sacred Vedic Celestial Mandala Ring
    function drawZodiacMandala(ctx, w, h) {
        zodiacRotation += 0.0006;
        const cx = w * 0.5;
        const cy = h * 0.42;
        const r1 = Math.min(w, h) * 0.28;
        const r2 = r1 * 1.35;

        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(zodiacRotation);

        // Concentric faint rings
        ctx.strokeStyle = 'rgba(245, 176, 65, 0.08)';
        ctx.lineWidth = 1.2;
        ctx.beginPath();
        ctx.arc(0, 0, r1, 0, Math.PI * 2);
        ctx.stroke();

        ctx.strokeStyle = 'rgba(245, 176, 65, 0.05)';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.arc(0, 0, r2, 0, Math.PI * 2);
        ctx.stroke();

        // 12 Astrological Ray Nodes (Rashis)
        for (let i = 0; i < 12; i++) {
            const angle = (i * Math.PI * 2) / 12;
            const x1 = Math.cos(angle) * r1;
            const y1 = Math.sin(angle) * r1;
            const x2 = Math.cos(angle) * r2;
            const y2 = Math.sin(angle) * r2;

            // Radiating Ray
            ctx.strokeStyle = 'rgba(245, 176, 65, 0.07)';
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();

            // Node star
            ctx.fillStyle = 'rgba(245, 176, 65, 0.16)';
            ctx.beginPath();
            ctx.arc(x2, y2, 2.4, 0, Math.PI * 2);
            ctx.fill();
        }

        ctx.restore();
    }

    function initCanvas() {
        canvasEl = document.getElementById('astro-starfield-canvas');
        if (!canvasEl) return;
        ctx = canvasEl.getContext('2d');

        function resize() {
            if (!canvasEl) return;
            canvasEl.width = window.innerWidth;
            canvasEl.height = window.innerHeight;
            particles.length = 0;
            for (let i = 0; i < NUM_PARTICLES; i++) {
                particles.push(new Particle(canvasEl.width, canvasEl.height));
            }
        }

        window.addEventListener('resize', resize);
        resize();

        window.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        }, { passive: true });

        // Trigger occasional shooting stars
        setInterval(() => {
            if (state.particlesEnabled && canvasEl) {
                shootingStars.push(new ShootingStar(canvasEl.width, canvasEl.height));
            }
        }, 5500);

        function loop() {
            if (!ctx || !canvasEl) return;
            ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);

            if (state.particlesEnabled) {
                const w = canvasEl.width;
                const h = canvasEl.height;

                // Draw Sacred Vedic Zodiac background geometry
                drawZodiacMandala(ctx, w, h);

                // Draw connecting constellation lines
                for (let i = 0; i < particles.length; i++) {
                    const p1 = particles[i];
                    p1.update(w, h);
                    p1.draw(ctx);

                    for (let j = i + 1; j < particles.length; j++) {
                        const p2 = particles[j];
                        const dx = p1.x - p2.x;
                        const dy = p1.y - p2.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        if (dist < 85) {
                            const lineAlpha = (1 - dist / 85) * 0.22;
                            ctx.beginPath();
                            ctx.moveTo(p1.x, p1.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.strokeStyle = `rgba(245, 176, 65, ${lineAlpha})`;
                            ctx.lineWidth = 0.6;
                            ctx.stroke();
                        }
                    }
                }

                // Update & draw shooting stars
                for (let i = shootingStars.length - 1; i >= 0; i--) {
                    const s = shootingStars[i];
                    s.update();
                    s.draw(ctx);
                    if (!s.active) {
                        shootingStars.splice(i, 1);
                    }
                }
            }

            animFrameId = requestAnimationFrame(loop);
        }

        loop();
    }

    // Video Engine
    function setWallpaperTheme(themeKey) {
        const theme = WALLPAPER_THEMES[themeKey] || WALLPAPER_THEMES.nebula;
        state.theme = theme.id;
        saveState();

        if (overlayEl) {
            overlayEl.style.background = theme.colorTint;
        }

        if (videoEl) {
            // Apply fade transition
            videoEl.classList.add('fade-out');

            setTimeout(() => {
                while (videoEl.firstChild) {
                    videoEl.removeChild(videoEl.firstChild);
                }

                theme.sources.forEach(srcObj => {
                    const src = document.createElement('source');
                    src.src = srcObj.src;
                    src.type = srcObj.type;
                    videoEl.appendChild(src);
                });

                videoEl.load();
                if (state.isPlaying) {
                    const playPromise = videoEl.play();
                    if (playPromise !== undefined) {
                        playPromise.catch(() => {
                            // Autoplay policy prevented; retry on first gesture
                            const resumeOnGesture = () => {
                                videoEl.play();
                                document.removeEventListener('click', resumeOnGesture);
                                document.removeEventListener('touchstart', resumeOnGesture);
                            };
                            document.addEventListener('click', resumeOnGesture, { once: true });
                            document.addEventListener('touchstart', resumeOnGesture, { once: true });
                        });
                    }
                }
                videoEl.classList.remove('fade-out');
            }, 350);
        }

        updateControlUI();
    }

    function setWallpaperOpacity(val) {
        const num = parseInt(val, 10);
        state.opacity = isNaN(num) ? 65 : Math.max(10, Math.min(100, num));
        saveState();

        if (videoEl) {
            videoEl.style.opacity = (state.opacity / 100).toString();
        }
        updateControlUI();
    }

    function togglePlayPause() {
        if (!videoEl) return;
        if (state.isPlaying) {
            videoEl.pause();
            state.isPlaying = false;
        } else {
            videoEl.play();
            state.isPlaying = true;
        }
        saveState();
        updateControlUI();
    }

    function toggleParticles() {
        state.particlesEnabled = !state.particlesEnabled;
        saveState();
        if (canvasEl) {
            canvasEl.style.opacity = state.particlesEnabled ? '0.85' : '0';
        }
        updateControlUI();
    }

    // UI Controls Rendering
    function updateControlUI() {
        // Theme buttons
        document.querySelectorAll('.astro-wallpaper-theme-btn').forEach(btn => {
            const theme = btn.getAttribute('data-theme');
            if (theme === state.theme) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Sliders
        const slider = document.getElementById('astro-wallpaper-opacity-slider');
        const sliderVal = document.getElementById('astro-wallpaper-opacity-val');
        if (slider) slider.value = state.opacity;
        if (sliderVal) sliderVal.textContent = state.opacity + '%';

        // Play/Pause button
        const playBtn = document.getElementById('astro-wallpaper-play-btn');
        if (playBtn) {
            if (state.isPlaying) {
                playBtn.innerHTML = '<i class="bi bi-pause-fill"></i> Pause';
                playBtn.classList.remove('active');
            } else {
                playBtn.innerHTML = '<i class="bi bi-play-fill"></i> Play';
                playBtn.classList.add('active');
            }
        }

        // Particles button
        const partBtn = document.getElementById('astro-wallpaper-particles-btn');
        if (partBtn) {
            if (state.particlesEnabled) {
                partBtn.innerHTML = '<i class="bi bi-stars"></i> FX On';
                partBtn.classList.add('active');
            } else {
                partBtn.innerHTML = '<i class="bi bi-stars"></i> FX Off';
                partBtn.classList.remove('active');
            }
        }

        // Sound button
        const soundBtn = document.getElementById('astro-wallpaper-sound-btn');
        if (soundBtn) {
            if (state.soundEnabled) {
                soundBtn.innerHTML = '<i class="bi bi-volume-up-fill"></i> 432Hz On';
                soundBtn.classList.add('active');
            } else {
                soundBtn.innerHTML = '<i class="bi bi-volume-mute"></i> Sound Off';
                soundBtn.classList.remove('active');
            }
        }

        // Status badge
        const badge = document.getElementById('astro-wallpaper-badge');
        if (badge) {
            badge.className = 'astro-wallpaper-badge ' + (state.isPlaying ? '' : 'paused');
        }
    }

    function initUI() {
        const toggleBtn = document.getElementById('astro-wallpaper-toggle-btn');
        const panel = document.getElementById('astro-wallpaper-panel');
        const closeBtn = document.getElementById('astro-wallpaper-close-btn');

        if (toggleBtn && panel) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                panel.classList.toggle('is-open');
                toggleBtn.classList.remove('is-pulsing');
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    panel.classList.remove('is-open');
                });
            }

            document.addEventListener('click', (e) => {
                if (!panel.contains(e.target) && !toggleBtn.contains(e.target)) {
                    panel.classList.remove('is-open');
                }
            });
        }

        // Theme clicks
        document.querySelectorAll('.astro-wallpaper-theme-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const theme = this.getAttribute('data-theme');
                setWallpaperTheme(theme);
            });
        });

        // Opacity slider
        const slider = document.getElementById('astro-wallpaper-opacity-slider');
        if (slider) {
            slider.addEventListener('input', function () {
                setWallpaperOpacity(this.value);
            });
        }

        // Play/Pause
        const playBtn = document.getElementById('astro-wallpaper-play-btn');
        if (playBtn) {
            playBtn.addEventListener('click', togglePlayPause);
        }

        // Particles
        const partBtn = document.getElementById('astro-wallpaper-particles-btn');
        if (partBtn) {
            partBtn.addEventListener('click', toggleParticles);
        }

        // Sound
        const soundBtn = document.getElementById('astro-wallpaper-sound-btn');
        if (soundBtn) {
            soundBtn.addEventListener('click', toggleSound);
        }

        updateControlUI();
    }

    // Master Initialization
    function init() {
        videoEl = document.getElementById('astro-wallpaper-video');
        overlayEl = document.getElementById('astro-wallpaper-overlay');

        if (videoEl) {
            videoEl.style.opacity = (state.opacity / 100).toString();
            setWallpaperTheme(state.theme);
        }

        initCanvas();
        initUI();

        // Autoplay guarantee on user interaction
        const startAutoplay = () => {
            if (videoEl && state.isPlaying && videoEl.paused) {
                videoEl.play().catch(() => {});
            }
        };
        window.addEventListener('click', startAutoplay, { once: true });
        window.addEventListener('scroll', startAutoplay, { once: true });
    }

    // Run on DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose public API for external triggers
    window.AstroWallpaper = {
        setTheme: setWallpaperTheme,
        setOpacity: setWallpaperOpacity,
        togglePlay: togglePlayPause,
        toggleSound: toggleSound,
        getState: () => ({ ...state })
    };
})();
