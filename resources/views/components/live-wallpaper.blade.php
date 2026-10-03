<!-- AstroVani Fullscreen Live Background Wallpaper Video & Constellation Canvas -->
<div id="astro-wallpaper-wrapper" class="astro-wallpaper-wrapper" aria-hidden="true">
    <video id="astro-wallpaper-video" class="astro-wallpaper-video" autoplay muted loop playsinline preload="auto">
        <!-- Managed dynamically by astro-live-wallpaper.js -->
    </video>
    <canvas id="astro-starfield-canvas" class="astro-starfield-canvas"></canvas>
    <div id="astro-wallpaper-overlay" class="astro-wallpaper-overlay"></div>
</div>

<!-- Floating Live Wallpaper Settings Trigger -->
<button type="button" id="astro-wallpaper-toggle-btn" class="astro-wallpaper-control-toggle is-pulsing" title="Live Cosmic Wallpaper Controls" aria-label="Toggle Live Wallpaper Settings">
    <i class="bi bi-stars"></i>
</button>

<!-- Live Wallpaper Settings Modal/Pod -->
<div id="astro-wallpaper-panel" class="astro-wallpaper-panel" role="dialog" aria-labelledby="astro-wallpaper-title">
    <div class="astro-wallpaper-panel-header">
        <h6 id="astro-wallpaper-title">
            <span class="astro-wallpaper-badge" id="astro-wallpaper-badge"></span>
            <i class="bi bi-camera-reels text-warning"></i> Cosmic Wallpaper
        </h6>
        <button type="button" id="astro-wallpaper-close-btn" class="astro-wallpaper-close-btn" aria-label="Close Wallpaper Settings">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Theme Selection -->
    <div class="small text-white-50 mb-2 fw-semibold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.8px;">
        Celestial Theme
    </div>
    <div class="astro-wallpaper-themes">
        <button type="button" class="astro-wallpaper-theme-btn" data-theme="nebula">
            <i class="bi bi-stars text-warning"></i> Nebula
        </button>
        <button type="button" class="astro-wallpaper-theme-btn" data-theme="galaxy">
            <i class="bi bi-brightness-high text-info"></i> Galactic
        </button>
        <button type="button" class="astro-wallpaper-theme-btn" data-theme="zodiac" style="grid-column: span 2;">
            <i class="bi bi-gem text-warning"></i> Vedic Starlight
        </button>
    </div>

    <!-- Brightness/Opacity Slider -->
    <div class="astro-wallpaper-control-group">
        <label for="astro-wallpaper-opacity-slider">
            <span>Video Intensity</span>
            <span id="astro-wallpaper-opacity-val" class="text-warning">65%</span>
        </label>
        <input type="range" id="astro-wallpaper-opacity-slider" class="astro-wallpaper-slider" min="15" max="100" value="65" step="5">
    </div>

    <!-- Quick Action Toggles -->
    <div class="astro-wallpaper-actions">
        <button type="button" id="astro-wallpaper-play-btn" class="astro-wallpaper-action-btn" title="Toggle Play / Pause">
            <i class="bi bi-pause-fill"></i> Pause
        </button>
        <button type="button" id="astro-wallpaper-particles-btn" class="astro-wallpaper-action-btn active" title="Toggle Constellation FX">
            <i class="bi bi-stars"></i> FX On
        </button>
        <button type="button" id="astro-wallpaper-sound-btn" class="astro-wallpaper-action-btn" title="Toggle 432Hz Sacred Ambient Chime">
            <i class="bi bi-volume-mute"></i> Sound
        </button>
    </div>
</div>
