<?php
/**
 * Language Prompt - Floating popup for first-time visitors
 * Only shows if no language cookie is set
 * Omniwallet CMS
 */

// Only show if no language has been selected yet (no cookie)
$hasLanguageCookie = isset($_COOKIE['lang']);
if ($hasLanguageCookie) {
    return; // Don't show if user already selected a language
}

// Detect browser language for suggestion
$browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'es', 0, 2);
$suggestedLang = in_array($browserLang, $supportedLangs) ? $browserLang : 'es';
$suggestedLangInfo = $availableLangs[$suggestedLang] ?? $availableLangs['es'];

// Get clean path for language URL generation
$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
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

// Function to generate language URL with prefix
$getLangUrl = function($langCode) use ($cleanPath) {
    if ($langCode === 'es') {
        return $cleanPath; // Spanish (default) doesn't need prefix
    }
    return '/' . $langCode . $cleanPath;
};
?>

<!-- Language Selection Prompt -->
<div id="langPrompt" class="lang-prompt">
    <div class="lang-prompt-content">
        <button type="button" class="lang-prompt-close" aria-label="Cerrar" onclick="closeLangPrompt()">
            <i class="fas fa-times"></i>
        </button>

        <div class="lang-prompt-icon">
            <i class="fas fa-globe"></i>
        </div>

        <p class="lang-prompt-title">
            <?php if ($suggestedLang !== 'es'): ?>
                <?= $suggestedLangInfo['flag'] ?> <?= $suggestedLang === 'en' ? 'View in English?' : ($suggestedLang === 'it' ? 'Visualizza in italiano?' : ($suggestedLang === 'fr' ? 'Voir en français?' : ($suggestedLang === 'de' ? 'Auf Deutsch anzeigen?' : 'Cambiar idioma?'))) ?>
            <?php else: ?>
                🌐 Selecciona tu idioma
            <?php endif; ?>
        </p>

        <div class="lang-prompt-options">
            <?php foreach ($availableLangs as $code => $lang): ?>
                <a href="<?= $getLangUrl($code) ?>"
                   class="lang-prompt-option <?= $code === $suggestedLang ? 'suggested' : '' ?>"
                   onclick="selectLanguage('<?= $code ?>')">
                    <span class="lang-flag"><?= $lang['flag'] ?></span>
                    <span class="lang-name"><?= $lang['native'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.lang-prompt {
    position: fixed;
    bottom: 100px;
    left: 24px;
    z-index: 998;
    animation: langPromptSlideIn 0.4s ease-out;
}

@keyframes langPromptSlideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.lang-prompt.hiding {
    animation: langPromptSlideOut 0.3s ease-in forwards;
}

@keyframes langPromptSlideOut {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(20px);
    }
}

.lang-prompt-content {
    position: relative;
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    max-width: 280px;
    border: 1px solid var(--color-gray-100);
}

.lang-prompt-close {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 28px;
    height: 28px;
    border: none;
    background: var(--color-gray-100);
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-gray-500);
    transition: all 0.2s ease;
}

.lang-prompt-close:hover {
    background: var(--color-gray-200);
    color: var(--color-gray-700);
}

.lang-prompt-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--color-primary-light), var(--color-primary));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}

.lang-prompt-icon i {
    font-size: 24px;
    color: white;
}

.lang-prompt-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--color-dark);
    margin-bottom: 16px;
}

.lang-prompt-options {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.lang-prompt-option {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    border-radius: 8px;
    text-decoration: none;
    color: var(--color-dark);
    font-size: 13px;
    transition: all 0.2s ease;
}

.lang-prompt-option:hover {
    background: var(--color-gray-100);
    border-color: var(--color-gray-300);
}

.lang-prompt-option.suggested {
    background: var(--color-primary-light);
    border-color: var(--color-primary);
    color: var(--color-primary-dark);
}

.lang-prompt-option .lang-flag {
    font-size: 16px;
}

.lang-prompt-option .lang-name {
    font-weight: 500;
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .lang-prompt {
        left: 16px;
        right: 16px;
        bottom: 80px;
    }

    .lang-prompt-content {
        max-width: none;
    }

    .lang-prompt-options {
        justify-content: center;
    }
}
</style>

<script>
function closeLangPrompt() {
    const prompt = document.getElementById('langPrompt');
    if (prompt) {
        prompt.classList.add('hiding');
        setTimeout(() => {
            prompt.remove();
        }, 300);
        // Set a temporary cookie so it doesn't show again this session
        document.cookie = 'lang_prompt_dismissed=1; path=/; max-age=86400'; // 24 hours
    }
}

function selectLanguage(code) {
    // Cookie will be set by the server on page load
    closeLangPrompt();
}

// Auto-hide prompt after 15 seconds
setTimeout(function() {
    const prompt = document.getElementById('langPrompt');
    if (prompt && !prompt.classList.contains('hiding')) {
        closeLangPrompt();
    }
}, 15000);

// Check if already dismissed this session
if (document.cookie.includes('lang_prompt_dismissed=1')) {
    const prompt = document.getElementById('langPrompt');
    if (prompt) prompt.remove();
}
</script>
