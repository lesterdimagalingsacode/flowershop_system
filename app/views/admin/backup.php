<?php $title = 'Backup & Restore'; ?>

<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-1">Database</p>
        <h1 class="text-2xl text-text" style="font-family: var(--font-display);">Backup & Restore</h1>
        <p class="text-sm text-muted mt-1">Download a password-protected backup or restore from a previous one.</p>
    </div>

    <!-- ── Backup Password Info ── -->
    <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 flex items-start gap-3">
        <span class="text-amber-500 text-lg flex-shrink-0">🔐</span>
        <div>
            <p class="text-sm font-semibold text-amber-800 mb-0.5">Backup Password</p>
            <p class="text-xs text-amber-700 leading-relaxed">
                All backup files are encrypted with AES-256.
                The password is required to restore.
                Current password: <code class="bg-amber-100 px-1.5 py-0.5 rounded font-mono"><?= e($zipPassword) ?></code>
            </p>
        </div>
    </div>

    <!-- ── Backup ── -->
    <div class="bg-white border border-border rounded-2xl p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-text mb-1" style="font-family: var(--font-display);">Download Backup</h2>
                <p class="text-sm text-muted leading-relaxed">
                    Exports the entire database + all images as a password-protected
                    <code class="text-xs bg-cream px-1.5 py-0.5 rounded">.zip</code> file.
                    Store it somewhere safe — Google Drive, your local machine, etc.
                </p>
            </div>
            <a href="<?= APP_URL ?>/admin/backup/download"
               class="flex-shrink-0 inline-flex items-center gap-2 bg-forest hover:bg-pine text-white text-sm font-medium px-5 py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Backup
            </a>
        </div>
    </div>

    <!-- ── Restore ── -->
    <div class="bg-white border border-border rounded-2xl p-6">
        <h2 class="font-semibold text-text mb-1" style="font-family: var(--font-display);">Restore from Backup</h2>
        <p class="text-sm text-muted leading-relaxed mb-5">
            Upload a <code class="text-xs bg-cream px-1.5 py-0.5 rounded">.zip</code> backup file to restore the database and images.
            <span class="text-red-500 font-medium">This will overwrite all current data.</span>
        </p>

        <form method="POST"
              action="<?= APP_URL ?>/admin/backup/restore"
              enctype="multipart/form-data"
              onsubmit="return confirm('⚠️ Are you sure? This will overwrite all current data and images.')">
            <?= csrf_field() ?>

            <!-- Drop zone -->
            <div class="border-2 border-dashed border-border rounded-xl p-8 text-center mb-4 hover:border-forest transition-colors"
                 id="dropzone">
                <svg class="w-10 h-10 text-muted mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-sm text-muted mb-2">Drop your <strong>.zip</strong> backup file here or</p>
                <label for="backup_file"
                       class="cursor-pointer text-sm font-medium text-forest hover:text-pine transition-colors underline underline-offset-2">
                    browse to upload
                </label>
                <input type="file" name="backup_file" id="backup_file"
                       accept=".zip" class="hidden"
                       onchange="showFileName(this)">
                <p id="fileName" class="text-xs text-muted mt-3 hidden"></p>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-xs font-medium text-text tracking-widest uppercase mb-2">
                    Backup Password <span class="text-red-400">*</span>
                </label>
                <input type="password" name="zip_password"
                       placeholder="Enter backup password"
                       class="w-full bg-white border border-border rounded-xl px-4 py-3 text-text text-sm
                              placeholder-muted focus:outline-none focus:border-forest focus:ring-2
                              focus:ring-forest/20 transition">
            </div>

            <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-3 rounded-full transition-all">
                ⚠️ Restore Database & Images
            </button>
        </form>
    </div>

    <!-- ── Previous Backups ── -->
    <?php if (!empty($backups)): ?>
    <div class="bg-white border border-border rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h2 class="font-semibold text-text" style="font-family: var(--font-display);">Saved Backups</h2>
            <p class="text-xs text-muted mt-0.5">Stored in <code>storage/backups/</code> on the server.</p>
        </div>
        <div class="divide-y divide-border">
            <?php foreach ($backups as $backup): ?>
            <div class="flex items-center justify-between px-6 py-4 gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-text truncate"><?= e($backup['name']) ?></p>
                    <p class="text-xs text-muted mt-0.5"><?= $backup['created'] ?> · <?= $backup['size'] ?></p>
                </div>
                <a href="<?= APP_URL ?>/admin/backup/download?file=<?= urlencode($backup['name']) ?>"
                   class="flex-shrink-0 text-xs font-medium text-forest hover:text-pine transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
function showFileName(input) {
    const label = document.getElementById('fileName');
    if (input.files.length > 0) {
        label.textContent = '📦 ' + input.files[0].name;
        label.classList.remove('hidden');
    }
}

const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('backup_file');

dropzone.addEventListener('dragover', e => {
    e.preventDefault();
    dropzone.classList.add('border-forest', 'bg-cream');
});

dropzone.addEventListener('dragleave', () => {
    dropzone.classList.remove('border-forest', 'bg-cream');
});

dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('border-forest', 'bg-cream');
    const file = e.dataTransfer.files[0];
    if (file && file.name.endsWith('.zip')) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        showFileName(fileInput);
    } else {
        Toast.error('Only .zip backup files are accepted.');
    }
});
</script>