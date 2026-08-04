<?php
/**
 * @var string $csrfToken
 * @var string $nonce
 */
?>

<?php if (isset($error)): ?>
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form id="url-form" method="post" action="" novalidate class="space-y-4">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <div>
        <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL to shorten</label>
        <input type="url" id="url" name="url" placeholder="https://example.com" required maxlength="2048"
            aria-describedby="url-error"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <p id="url-error" class="hidden mt-1 text-sm text-red-600"></p>
    </div>
    <div>
        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expires at (optional)</label>
        <input type="datetime-local" id="expires_at" name="expires_at" maxlength="19"
            aria-describedby="expires_at-error"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <p id="expires_at-error" class="hidden mt-1 text-sm text-red-600"></p>
    </div>
    <button type="submit" id="submit-button" class="w-full bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">Shorten</button>
</form>

<div id="result" class="hidden mt-4 p-3 rounded break-all"></div>

<script src="/js/index.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8') ?>" defer></script>