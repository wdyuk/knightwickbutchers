<?php

$homepage_popup = homepage_popup_get_config();
$homepage_popup_image = homepage_popup_get_image_url();
$homepage_popup_has_content = (
    $homepage_popup['enabled'] === '1' &&
    (
        strlen(trim(strip_tags($homepage_popup['content']))) > 0 ||
        strlen(trim($homepage_popup['title'])) > 0 ||
        $homepage_popup_image !== false
    )
);

if (!$homepage_popup_has_content) {
    return;
}

$homepage_popup_version = !empty($homepage_popup['updated_at']) ? $homepage_popup['updated_at'] : '1';
$homepage_popup_title = trim($homepage_popup['title']);
$homepage_popup_label = ($homepage_popup_title !== '') ? '' : ' aria-label="Homepage popup"';

?>
<div class="homepage-popup" id="homepage-popup" data-version="<?php echo htmlspecialchars($homepage_popup_version, ENT_QUOTES, 'UTF-8'); ?>" hidden>
    <div class="homepage-popup__backdrop" data-homepage-popup-close="1"></div>
    <div class="homepage-popup__dialog" role="dialog" aria-modal="true"<?php echo ($homepage_popup_title !== '') ? ' aria-labelledby="homepage-popup-title"' : $homepage_popup_label; ?>>
        <button class="homepage-popup__close" type="button" aria-label="Close popup" data-homepage-popup-close="1">&times;</button>
        <?php if ($homepage_popup_image !== false): ?>
            <div class="homepage-popup__media">
                <img src="<?php echo htmlspecialchars($homepage_popup_image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($homepage_popup_title, ENT_QUOTES, 'UTF-8'); ?>" />
            </div>
        <?php endif; ?>
        <div class="homepage-popup__body">
            <?php if ($homepage_popup_title !== ''): ?>
                <h2 id="homepage-popup-title"><?php echo htmlspecialchars($homepage_popup_title, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php endif; ?>
            <?php if (strlen(trim($homepage_popup['content'])) > 0): ?>
                <div class="homepage-popup__content"><?php echo $homepage_popup['content']; ?></div>
            <?php endif; ?>
            <?php if (strlen(trim($homepage_popup['button_text'])) > 0 && strlen(trim($homepage_popup['button_url'])) > 0): ?>
                <a class="homepage-popup__button" href="<?php echo htmlspecialchars($homepage_popup['button_url'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($homepage_popup['button_text'], ENT_QUOTES, 'UTF-8'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>
<style>
    .homepage-popup[hidden] {
        display: none !important;
    }

    .homepage-popup {
        position: fixed;
        inset: 0;
        z-index: 9999;
    }

    .homepage-popup__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(18, 24, 33, 0.68);
    }

    .homepage-popup__dialog {
        position: relative;
        z-index: 1;
        width: calc(100% - 32px);
        max-width: 680px;
        max-height: calc(100vh - 48px);
        margin: 24px auto;
        overflow-y: auto;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.22);
    }

    .homepage-popup__close {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 2;
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 999px;
        background: rgba(18, 24, 33, 0.08);
        color: #121821;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
    }

    .homepage-popup__media img {
        display: block;
        width: 100%;
        height: auto;
        border-radius: 18px 18px 0 0;
    }

    .homepage-popup__body {
        padding: 32px 28px;
        color: #222222;
    }

    .homepage-popup__body h2 {
        margin: 0 0 16px;
        font-size: 32px;
        line-height: 1.15;
    }

    .homepage-popup__content {
        font-size: 16px;
        line-height: 1.7;
    }

    .homepage-popup__content > *:first-child {
        margin-top: 0;
    }

    .homepage-popup__content > *:last-child {
        margin-bottom: 0;
    }

    .homepage-popup__button {
        display: inline-block;
        margin-top: 24px;
        padding: 12px 22px;
        border-radius: 999px;
        background: #b51f29;
        color: #ffffff;
        text-decoration: none;
        font-weight: 700;
    }

    body.homepage-popup-open {
        overflow: hidden;
    }

    @media (max-width: 767px) {
        .homepage-popup__dialog {
            width: calc(100% - 20px);
            max-height: calc(100vh - 20px);
            margin: 10px auto;
        }

        .homepage-popup__body {
            padding: 24px 18px;
        }

        .homepage-popup__body h2 {
            font-size: 26px;
        }
    }
</style>
<script>
    (function() {
        var popup = document.getElementById('homepage-popup');
        if (!popup) {
            return;
        }

        var storageKey = 'homepagePopupDismissedVersion';
        var version = popup.getAttribute('data-version') || '1';

        try {
            if (window.localStorage && window.localStorage.getItem(storageKey) === version) {
                return;
            }
        } catch (error) {
        }

        function closePopup() {
            popup.setAttribute('hidden', 'hidden');
            document.body.classList.remove('homepage-popup-open');

            try {
                if (window.localStorage) {
                    window.localStorage.setItem(storageKey, version);
                }
            } catch (error) {
            }
        }

        function openPopup() {
            popup.removeAttribute('hidden');
            document.body.classList.add('homepage-popup-open');
        }

        popup.addEventListener('click', function(event) {
            if (event.target && event.target.getAttribute('data-homepage-popup-close') === '1') {
                closePopup();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !popup.hasAttribute('hidden')) {
                closePopup();
            }
        });

        window.setTimeout(openPopup, 250);
    })();
</script>
