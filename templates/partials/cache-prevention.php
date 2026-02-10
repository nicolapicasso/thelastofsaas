<?php
/**
 * Cache Prevention Script
 * Include this before </body> in templates that need cache prevention
 */
?>
<script>
(function() {
    var pageInstanceId = '<?= bin2hex(random_bytes(8)) ?>';
    var pageLoadTime = Date.now();
    var storageKey = 'page_' + window.location.pathname;
    var storedInstance = sessionStorage.getItem(storageKey);

    if (storedInstance && storedInstance !== pageInstanceId) {
        sessionStorage.setItem(storageKey, pageInstanceId);
        window.location.reload(true);
    } else {
        sessionStorage.setItem(storageKey, pageInstanceId);
    }

    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload(true);
        }
    });

    window.addEventListener('popstate', function() {
        window.location.reload(true);
    });

    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible' && Date.now() - pageLoadTime > 30000) {
            window.location.reload(true);
        }
    });

    window.addEventListener('focus', function() {
        if (Date.now() - pageLoadTime > 60000) {
            window.location.reload(true);
        }
    });
})();
</script>
