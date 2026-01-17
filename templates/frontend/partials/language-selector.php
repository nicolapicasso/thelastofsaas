<?php
/**
 * Language Selector Component
 * Uses URL prefixes for SEO-friendly language switching
 * Omniwallet CMS
 */

$currentLangInfo = $availableLangs[$currentLang] ?? ['name' => 'Español', 'flag' => '🇪🇸'];
$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';

// Remove query string for clean path
$pathOnly = parse_url($currentUrl, PHP_URL_PATH) ?? '/';

// Remove any existing language prefix from the path
$supportedLangCodes = array_keys($availableLangs);
$segments = explode('/', trim($pathOnly, '/'));
if (!empty($segments[0]) && in_array($segments[0], $supportedLangCodes)) {
    array_shift($segments);
}
$cleanPath = '/' . implode('/', $segments);
if ($cleanPath === '/') {
    $cleanPath = '/';
}

// Function to generate language URL
$getLangUrl = function($langCode) use ($cleanPath) {
    if ($langCode === 'es') {
        return $cleanPath; // Spanish (default) doesn't need prefix
    }
    return '/' . $langCode . $cleanPath;
};
?>

<div class="language-selector">
    <button class="lang-toggle" aria-label="Seleccionar idioma" aria-expanded="false">
        <span class="lang-flag"><?= $currentLangInfo['flag'] ?></span>
        <span class="lang-code"><?= strtoupper($currentLang) ?></span>
        <i class="fas fa-chevron-down"></i>
    </button>
    <div class="lang-dropdown">
        <?php foreach ($availableLangs as $code => $lang): ?>
            <a href="<?= $getLangUrl($code) ?>"
               class="lang-option <?= $code === $currentLang ? 'active' : '' ?>"
               <?= $code === $currentLang ? 'aria-current="true"' : '' ?>>
                <span class="lang-flag"><?= $lang['flag'] ?></span>
                <span class="lang-name"><?= $lang['native'] ?></span>
                <?php if ($code === $currentLang): ?>
                    <i class="fas fa-check"></i>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<style>
.language-selector {
    position: relative;
}

.lang-toggle {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: transparent;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    cursor: pointer;
    font-size: 14px;
    color: var(--color-dark);
    transition: all 0.2s ease;
}

.lang-toggle:hover {
    background-color: var(--color-gray-50);
    border-color: var(--color-gray-300);
}

.lang-toggle .fa-chevron-down {
    font-size: 10px;
    transition: transform 0.2s ease;
}

.language-selector.open .lang-toggle .fa-chevron-down {
    transform: rotate(180deg);
}

.lang-flag {
    font-size: 16px;
    line-height: 1;
}

.lang-code {
    font-weight: 500;
}

.lang-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    right: 0;
    min-width: 160px;
    background: white;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: all 0.2s ease;
    z-index: 1000;
}

.language-selector.open .lang-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.lang-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    color: var(--color-dark);
    text-decoration: none;
    transition: background-color 0.15s ease;
}

.lang-option:first-child {
    border-radius: var(--radius-md) var(--radius-md) 0 0;
}

.lang-option:last-child {
    border-radius: 0 0 var(--radius-md) var(--radius-md);
}

.lang-option:hover {
    background-color: var(--color-gray-50);
}

.lang-option.active {
    background-color: var(--color-primary-light);
    color: var(--color-primary-dark);
}

.lang-option .lang-name {
    flex: 1;
    font-size: 14px;
}

.lang-option .fa-check {
    font-size: 12px;
    color: var(--color-primary);
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .lang-toggle .lang-code {
        display: none;
    }

    .lang-dropdown {
        right: auto;
        left: 0;
    }
}
</style>

<script>
(function() {
    const selector = document.querySelector('.language-selector');
    if (!selector) return;

    const toggle = selector.querySelector('.lang-toggle');

    toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        selector.classList.toggle('open');
        toggle.setAttribute('aria-expanded', selector.classList.contains('open'));
    });

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!selector.contains(e.target)) {
            selector.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });

    // Close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            selector.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
})();
</script>
