(function() {
    var config = window.fluxbb_code_config || {};
    var highlightPath = 'style/external/highlight/';
    var prismPath = 'style/external/prism/';

    function loadScript(src, onSuccess, onError) {
        var script = document.createElement('script');
        script.src = src;
        script.onload = onSuccess;
        script.onerror = onError;
        document.head.appendChild(script);
    }

    function loadCSS(href, id) {
        if (id && document.getElementById(id)) return;
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        if (id) link.id = id;
        document.head.appendChild(link);
    }

    function initHighlightJS() {
        // Configure languages if whitelist exists
        if (config.whitelist && window.hljs) {
            window.hljs.configure({ languages: config.whitelist });
        }

        window.hljs.highlightAll();
        addCopyButtons();
    }

    function initPrismJS() {
        // Prism automatically highlights on load usually, but we might need to rerun if lazy loaded
        if (window.Prism) {
            window.Prism.highlightAll();
            addCopyButtons();
        }
    }

    function addCopyButtons() {
        var blocks = document.querySelectorAll('pre code');
        blocks.forEach(function(block) {
            var pre = block.parentNode;
            if (pre.querySelector('.code-copy-btn')) return;

            var btn = document.createElement('button');
            btn.className = 'code-copy-btn';
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>';
            btn.title = 'Copy to clipboard';

            // Inline styles for simplicity and self-containment
            btn.style.position = 'absolute';
            btn.style.top = '5px';
            btn.style.right = '5px';
            btn.style.background = 'rgba(255,255,255,0.7)';
            btn.style.border = '1px solid #ccc';
            btn.style.borderRadius = '4px';
            btn.style.cursor = 'pointer';
            btn.style.padding = '4px';
            btn.style.lineHeight = '0';
            btn.style.opacity = '0.5';
            btn.style.transition = 'opacity 0.2s';

            btn.onmouseover = function() { btn.style.opacity = '1'; };
            btn.onmouseout = function() { btn.style.opacity = '0.5'; };

            btn.onclick = function() {
                var text = block.innerText; // Use innerText to get newlines correctly
                navigator.clipboard.writeText(text).then(function() {
                    var originalHTML = btn.innerHTML;
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                    setTimeout(function() {
                        btn.innerHTML = originalHTML;
                    }, 2000);
                });
            };

            // Ensure pre has relative positioning
            if (getComputedStyle(pre).position === 'static') {
                pre.style.position = 'relative';
            }

            pre.appendChild(btn);
        });
    }

    function applyTheme() {
        // Detect dark mode
        var isDark = false;
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            isDark = true;
        }
        // Also check for CSS variable if present (custom forum integration)
        // e.g. if (--bg-color) is dark? Hard to tell.
        // We will stick to media query + checking for specific class on body if any
        if (document.body.classList.contains('dark') || document.body.classList.contains('dark-theme')) {
            isDark = true;
        }

        var themeFile = isDark ? 'dark.min.css' : 'default.min.css';
        var prismTheme = isDark ? 'prism-dark.min.css' : 'prism.min.css';

        // Remove existing theme links if any (for switching)
        var existing = document.getElementById('hljs-theme');
        if (existing) existing.remove();

        // For Highlight.js
        if (!window.usingPrismFallback) {
             loadCSS(highlightPath + themeFile, 'hljs-theme');
        } else {
             loadCSS(prismPath + prismTheme, 'prism-theme');
        }

        // Load layout fixes
        loadCSS('style/imports/highlight_fixes.css', 'hljs-fixes');
    }

    // Lazy load logic
    document.addEventListener('DOMContentLoaded', function() {
        var codeBlocks = document.querySelectorAll('pre code');
        if (codeBlocks.length > 0) {
            applyTheme();

            // Try Highlight.js
            loadScript(highlightPath + 'highlight.min.js',
                function() {
                    // Success
                    initHighlightJS();
                },
                function() {
                    // Fallback to Prism
                    console.warn('Highlight.js failed to load, falling back to Prism.js');
                    window.usingPrismFallback = true;
                    applyTheme(); // Switch theme to prism
                    loadScript(prismPath + 'prism.min.js', function() {
                        initPrismJS();
                    }, function() {
                        console.error('Both highlighters failed to load.');
                    });
                }
            );

            // Watch for theme changes
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);
            }
        }
    });

})();
