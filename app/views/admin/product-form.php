<?php $title = $isEdit ? 'Edit Product' : 'Add Product'; ?>

<div class="max-w-3xl space-y-5">

    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="<?= APP_URL ?>/admin/products"
           class="p-2 rounded-xl text-muted hover:text-forest hover:bg-white border border-transparent hover:border-border transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <p class="text-xs tracking-[0.2em] uppercase text-gold font-medium mb-0.5">Catalog</p>
            <h2 class="text-xl font-bold text-forest" style="font-family: var(--font-display);">
                <?= $isEdit ? 'Edit Product' : 'Add New Product' ?>
            </h2>
        </div>
    </div>

    <form method="POST"
          action="<?= APP_URL ?>/admin/products/<?= $isEdit ? $product['id'] . '/edit' : 'create' ?>"
          enctype="multipart/form-data"
          class="space-y-5">

        <?= csrf_field() ?>

        <!-- Basic Info -->
        <div class="bg-white border border-border rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-text border-b border-border pb-3">Basic Information</h3>

            <!-- Name -->
            <div>
                <label class="block text-xs font-medium text-muted mb-1.5">
                    Product Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required
                       value="<?= e($product['name'] ?? '') ?>"
                       placeholder="e.g. Sunflower Bouquet"
                       class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-xs font-medium text-muted mb-1.5">
                    Category <span class="text-red-500">*</span>
                </label>
                <select name="category_id" required
                        class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
                    <option value="">Select a category…</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-medium text-muted mb-1.5">Description</label>
                <textarea name="description" rows="4"
                          placeholder="Describe the product…"
                          class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream resize-none"><?= e($product['description'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Pricing & Stock -->
        <div class="bg-white border border-border rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-text border-b border-border pb-3">Pricing & Stock</h3>

            <div class="grid grid-cols-3 gap-4">
                <!-- Price -->
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">
                        Price (₱) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="price" required min="1" step="0.01"
                           value="<?= e($product['price'] ?? '') ?>"
                           placeholder="0.00"
                           class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
                </div>

                <!-- Stock -->
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Stock Quantity</label>
                    <input type="number" name="stock" min="0"
                           value="<?= e($product['stock'] ?? 0) ?>"
                           class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
                </div>

                <!-- Low stock alert -->
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Low Stock Alert</label>
                    <input type="number" name="low_stock_alert" min="0"
                           value="<?= e($product['low_stock_alert'] ?? 5) ?>"
                           class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-forest/20 bg-cream">
                    <p class="text-[0.65rem] text-muted mt-1">Warn when stock ≤ this number</p>
                </div>
            </div>
        </div>

        <!-- Image -->
        <div class="bg-white border border-border rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-text border-b border-border pb-3">Product Image</h3>

            <?php if ($isEdit && $product['image']): ?>
                <div class="flex items-center gap-4 p-3 bg-cream rounded-xl border border-border">
                    <img src="<?= APP_URL ?>/images/products/<?= e($product['image']) ?>"
                         alt="Current image"
                         class="w-16 h-16 object-cover rounded-lg border border-border">
                    <div>
                        <p class="text-xs font-medium text-text">Current image</p>
                        <p class="text-xs text-muted"><?= e($product['image']) ?></p>
                        <p class="text-xs text-muted mt-0.5">Upload a new image below to replace it.</p>
                    </div>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-xs font-medium text-muted mb-1.5">
                    <?= $isEdit ? 'Replace Image' : 'Upload Image' ?>
                </label>
                <div class="relative">
                    <input type="file" name="image" id="image-input"
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div id="upload-area"
                         class="border-2 border-dashed border-border rounded-xl px-6 py-8 text-center hover:border-forest/40 transition-colors">
                        <div class="text-2xl mb-2">📷</div>
                        <p class="text-sm text-muted" id="upload-label">
                            <?= $isEdit ? 'Click to replace image' : 'Click to upload image' ?>
                        </p>
                        <p class="text-xs text-muted/60 mt-1">JPG, PNG, WEBP, GIF · Max 3MB</p>
                    </div>
                    <div id="image-preview" class="hidden mt-3">
                        <img id="preview-img" src="" alt="Preview"
                             class="w-32 h-32 object-cover rounded-xl border border-border">
                        <p class="text-xs text-muted mt-1" id="preview-name"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visibility -->
        <div class="bg-white border border-border rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-text border-b border-border pb-3 mb-4">Visibility</h3>

            <!-- Hidden fallback — must be BEFORE the checkbox -->
            <input type="hidden" name="is_active" value="0">

            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="sr-only peer">
                    <div class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:bg-forest transition-colors"></div>
                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-medium text-text">Active / Visible</p>
                    <p class="text-xs text-muted">When off, this product is hidden from the shop.</p>
                </div>
            </label>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="bg-forest hover:bg-pine text-white text-sm font-semibold px-7 py-2.5 rounded-full transition-all hover:-translate-y-px shadow-sm">
                <?= $isEdit ? 'Save Changes' : 'Create Product' ?>
            </button>
            <a href="<?= APP_URL ?>/admin/products"
               class="text-sm text-muted hover:text-forest transition-colors px-4 py-2.5">
                Cancel
            </a>
        </div>

    </form>
</div>

<script>
// Image preview
document.getElementById('image-input').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (ev) => {
        document.getElementById('preview-img').src = ev.target.result;
        document.getElementById('preview-name').textContent = file.name;
        document.getElementById('image-preview').classList.remove('hidden');
        document.getElementById('upload-label').textContent = 'Image selected';
    };
    reader.readAsDataURL(file);
});
</script>