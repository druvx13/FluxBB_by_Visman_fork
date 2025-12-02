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

    function initThemeToggle() {
        // Create toggle button
        var button = document.createElement('button');
        button.id = 'theme-toggle';
        button.innerText = 'Toggle Dark/Light Mode';
        button.style.position = 'fixed';
        button.style.bottom = '20px';
        button.style.right = '20px';
        button.style.padding = '10px 15px';
        button.style.backgroundColor = '#333';
        button.style.color = '#fff';
        button.style.border = 'none';
        button.style.borderRadius = '5px';
        button.style.cursor = 'pointer';
        button.style.zIndex = '9999';
        button.style.opacity = '0.8';
        button.onmouseover = function() { this.style.opacity = '1'; };
        button.onmouseout = function() { this.style.opacity = '0.8'; };

        document.body.appendChild(button);

        // Get theme from local storage, default to 'light'
        var currentTheme = localStorage.getItem('theme') || 'light';

        // Apply initial theme to the body
        document.body.classList.add(currentTheme + '-mode');

        button.addEventListener('click', function() {
            if (document.body.classList.contains('light-mode')) {
                document.body.classList.remove('light-mode');
                document.body.classList.add('dark-mode');
                currentTheme = 'dark';
            } else {
                document.body.classList.remove('dark-mode');
                document.body.classList.add('light-mode');
                currentTheme = 'light';
            }
            // Save the new theme to local storage
            localStorage.setItem('theme', currentTheme);
        });
    }

    // Lazy load logic
    document.addEventListener('DOMContentLoaded', function() {
        var codeBlocks = document.querySelectorAll('pre code');
        if (codeBlocks.length > 0) {

            // Load base highlight style (default) - overrides in highlight_fixes.css will handle theme
            loadCSS(highlightPath + 'default.min.css', 'hljs-base');

            // Load layout and theme fixes
            loadCSS('style/imports/highlight_fixes.css', 'hljs-fixes');

            // Initialize Theme Toggle
            initThemeToggle();

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
                    // Load prism CSS if fallback
                    loadCSS(prismPath + 'prism.min.css', 'prism-base');

                    loadScript(prismPath + 'prism.min.js', function() {
                        initPrismJS();
                    }, function() {
                        console.error('Both highlighters failed to load.');
                    });
                }
            );
        }
    });

})();
