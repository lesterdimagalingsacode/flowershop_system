<?php
// app/views/shop/contact.php
// Layout: main
?>

<div class="max-w-2xl mx-auto px-4 py-12">

    <!-- Header -->
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-pink-100 mb-4">
            <svg class="w-7 h-7 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Get in Touch</h1>
        <p class="text-gray-500 mt-2">Have a question or concern? We'd love to hear from you.</p>
    </div>

    <!-- Flash message -->
    <?php if ($flash = Session::getFlash()): ?>
        <div class="mb-6 px-4 py-3 rounded-lg text-sm font-medium
            <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Contact Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        <form method="POST" action="/contact" class="space-y-5">
            <?= csrf_field() ?>

            <!-- Name + Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="name">
                        Full Name <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>"
                        placeholder="Maria Santos"
                        required
                        class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="email">
                        Email Address <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>"
                        placeholder="maria@example.com"
                        required
                        class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
                    />
                </div>
            </div>

            <!-- Order # (optional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="order_id">
                    Order Number
                    <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">#</span>
                    <input
                        type="number"
                        id="order_id"
                        name="order_id"
                        min="1"
                        placeholder="e.g. 1042"
                        class="w-full pl-7 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
                    />
                </div>
                <p class="text-xs text-gray-400 mt-1">Fill this in if your message is about a specific order.</p>
            </div>

            <!-- Message -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="message">
                    Message <span class="text-red-400">*</span>
                </label>
                <textarea
                    id="message"
                    name="message"
                    rows="5"
                    placeholder="Tell us how we can help…"
                    required
                    minlength="10"
                    class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400 resize-none"
                ></textarea>
                <p class="text-xs text-gray-400 mt-1">Minimum 10 characters.</p>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="w-full py-3 bg-pink-500 hover:bg-pink-600 text-white font-semibold rounded-lg transition text-sm"
            >
                Send Message
            </button>

        </form>
    </div>

    <!-- Info cards -->
    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4 text-center text-sm text-gray-500">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <svg class="w-5 h-5 mx-auto mb-2 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
            </svg>
            <p class="font-medium text-gray-700">Response Time</p>
            <p>Within 24 hours on business days</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <svg class="w-5 h-5 mx-auto mb-2 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a2 2 0 0 1-2.828 0L6.343 16.657A8 8 0 1 1 17.657 16.657z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
            </svg>
            <p class="font-medium text-gray-700">Location</p>
            <p>Baler, Aurora, Philippines</p>
        </div>
    </div>

</div>