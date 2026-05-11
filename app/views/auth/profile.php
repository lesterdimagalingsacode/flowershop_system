<?php
// app/views/auth/profile.php
// Variables: $user, $errors, $oldData
$firstName  = htmlspecialchars($oldData['first_name']  ?? $user['first_name']  ?? '');
$middleName = htmlspecialchars($oldData['middle_name'] ?? $user['middle_name'] ?? '');
$lastName   = htmlspecialchars($oldData['last_name']   ?? $user['last_name']   ?? '');
$phone      = htmlspecialchars($oldData['phone']       ?? $user['phone']       ?? '');
$address    = htmlspecialchars($oldData['address']     ?? $user['address']     ?? '');
$email      = htmlspecialchars($user['email'] ?? '');

function profErr(array $errors, string $field): string {
    if (!empty($errors[$field])) {
        return '<p class="text-red-500 text-xs mt-1">' . htmlspecialchars($errors[$field][0]) . '</p>';
    }
    return '';
}
?>

<div class="min-h-screen bg-[#faf9f7] py-10 px-4">
  <div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-3 mb-2">
      <a href="/shop" class="text-gray-400 hover:text-gray-600 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </a>
      <div>
        <h1 class="text-2xl font-semibold text-gray-800" style="font-family:'Georgia',serif;">My Profile</h1>
        <p class="text-sm text-gray-500">Manage your account details</p>
      </div>
    </div>

    <!-- Flash message -->
    <?php
    $flash = \Session::getFlash('message');
    if ($flash):
        $color = match($flash['type'] ?? 'info') {
            'success' => 'bg-green-50 border-green-200 text-green-700',
            'error'   => 'bg-red-50 border-red-200 text-red-700',
            default   => 'bg-blue-50 border-blue-200 text-blue-700',
        };
    ?>
    <div class="border rounded-lg px-4 py-3 text-sm <?= $color ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <!-- ── Profile Info Card ───────────────── -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
        <span class="text-lg">👤</span> Personal Information
      </h2>

      <form method="POST" action="/profile" class="space-y-4">
        <?= csrf_field() ?>

        <!-- Name row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">First Name <span class="text-red-400">*</span></label>
            <input type="text" name="first_name" value="<?= $firstName ?>"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition <?= !empty($errors['first_name']) ? 'border-red-400' : '' ?>">
            <?= profErr($errors, 'first_name') ?>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Middle Name</label>
            <input type="text" name="middle_name" value="<?= $middleName ?>"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Last Name <span class="text-red-400">*</span></label>
            <input type="text" name="last_name" value="<?= $lastName ?>"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition <?= !empty($errors['last_name']) ? 'border-red-400' : '' ?>">
            <?= profErr($errors, 'last_name') ?>
          </div>
        </div>

        <!-- Email (read-only) -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Email Address</label>
          <input type="email" value="<?= $email ?>" disabled
            class="w-full border border-gray-100 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
          <p class="text-xs text-gray-400 mt-1">Email cannot be changed.</p>
        </div>

        <!-- Phone -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Phone Number</label>
          <input type="text" name="phone" value="<?= $phone ?>" placeholder="e.g. 09171234567"
            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition <?= !empty($errors['phone']) ? 'border-red-400' : '' ?>">
          <?= profErr($errors, 'phone') ?>
        </div>

        <!-- Address -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Default Address</label>
          <textarea name="address" rows="2" placeholder="House/Unit No., Street, Barangay, City"
            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition resize-none"><?= $address ?></textarea>
        </div>

        <div class="flex justify-end pt-1">
          <button type="submit"
            class="bg-forest hover:bg-pine text-white text-sm font-medium tracking-widest uppercase px-6 py-2.5 rounded-lg transition-colors duration-200">
            Save Changes
          </button>
        </div>
      </form>
    </div>

    <!-- ── Change Password Card ───────────── -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
        <span class="text-lg">🔒</span> Change Password
      </h2>

      <form method="POST" action="/profile/password" id="profilePasswordForm" class="space-y-4" novalidate>
        <?= csrf_field() ?>

        <!-- Current Password -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Current Password <span class="text-red-400">*</span></label>
          <div class="relative">
            <input type="password" id="current_password" name="current_password" placeholder="Enter current password"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition <?= !empty($errors['current_password']) ? 'border-red-400' : '' ?>">
            <button type="button" onclick="togglePassword('current_password', this)"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" tabindex="-1" aria-label="Show password">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
          </div>
          <?= profErr($errors, 'current_password') ?>
        </div>

        <!-- New Password -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">New Password <span class="text-red-400">*</span></label>
          <div class="relative">
            <input type="password" id="prof_new_password" name="new_password" placeholder="At least 8 characters"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition <?= !empty($errors['new_password']) ? 'border-red-400' : '' ?>">
            <button type="button" onclick="togglePassword('prof_new_password', this)"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" tabindex="-1" aria-label="Show password">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
          </div>
          <?= profErr($errors, 'new_password') ?>

          <!-- Strength bars -->
          <div class="flex gap-1 mt-2">
            <div class="h-1 flex-1 rounded-full bg-gray-200" id="prof-bar1"></div>
            <div class="h-1 flex-1 rounded-full bg-gray-200" id="prof-bar2"></div>
            <div class="h-1 flex-1 rounded-full bg-gray-200" id="prof-bar3"></div>
            <div class="h-1 flex-1 rounded-full bg-gray-200" id="prof-bar4"></div>
          </div>
          <p id="profStrengthLabel" class="text-xs mt-1 hidden"></p>

          <!-- Requirements checklist -->
          <ul id="profPwChecklist" class="mt-2 space-y-1 hidden">
            <li id="prof-req-length"  class="flex items-center gap-2 text-xs text-gray-400">
              <span class="prof-req-icon w-3 text-center">○</span> At least 8 characters
            </li>
            <li id="prof-req-upper"   class="flex items-center gap-2 text-xs text-gray-400">
              <span class="prof-req-icon w-3 text-center">○</span> One uppercase letter (A–Z)
            </li>
            <li id="prof-req-number"  class="flex items-center gap-2 text-xs text-gray-400">
              <span class="prof-req-icon w-3 text-center">○</span> One number (0–9)
            </li>
            <li id="prof-req-special" class="flex items-center gap-2 text-xs text-gray-400">
              <span class="prof-req-icon w-3 text-center">○</span> One special character (!@#$…)
            </li>
          </ul>
        </div>

        <!-- Confirm New Password -->
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Confirm New Password <span class="text-red-400">*</span></label>
          <div class="relative">
            <input type="password" id="prof_new_password_confirmation" name="new_password_confirmation" placeholder="Repeat new password"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition">
            <button type="button" onclick="togglePassword('prof_new_password_confirmation', this)"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" tabindex="-1" aria-label="Show password">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
          </div>
        </div>

        <div class="flex justify-end pt-1">
          <button type="submit"
            class="bg-forest hover:bg-pine text-white text-sm font-medium tracking-widest uppercase px-6 py-2.5 rounded-lg transition-colors duration-200">
            Update Password
          </button>
        </div>
      </form>
    </div>

    <!-- ── Quick Links ────────────────────── -->
    <div class="flex gap-3 text-sm">
      <a href="<?= APP_URL ?>/orders" class="flex-1 bg-white border border-gray-200 rounded-xl px-4 py-3 text-center hover:shadow-sm transition text-gray-600 hover:text-forest font-medium">
        My Orders
      </a>
      <a href="<?= APP_URL ?>/shop" class="flex-1 bg-forest hover:bg-pine text-white text-sm font-medium rounded-xl px-4 py-3 text-center hover:shadow-sm transition tracking-widest uppercase">
        Browse Shop
      </a>
    </div>

  </div>
</div>

<script>
// ── Toggle show/hide password ─────────────────
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
}

// ── Password strength + checklist ────────────
document.getElementById('prof_new_password').addEventListener('input', function () {
    const val    = this.value;
    const bars   = [
        document.getElementById('prof-bar1'),
        document.getElementById('prof-bar2'),
        document.getElementById('prof-bar3'),
        document.getElementById('prof-bar4'),
    ];
    const colors      = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'];
    const labels      = ['Weak', 'Fair', 'Good', 'Strong'];
    const labelColors = ['#f87171', '#fb923c', '#facc15', '#22c55e'];

    const checks = {
        length:  val.length >= 8,
        upper:   /[A-Z]/.test(val),
        number:  /[0-9]/.test(val),
        special: /[^A-Za-z0-9]/.test(val),
    };

    const strength = Object.values(checks).filter(Boolean).length;

    // Update bars
    bars.forEach((bar, i) => {
        bar.className = 'h-1 flex-1 rounded-full ' +
            (i < strength ? colors[strength - 1] : 'bg-gray-200');
    });

    // Update strength label
    const label = document.getElementById('profStrengthLabel');
    if (val.length > 0) {
        label.style.color = labelColors[strength - 1] ?? '#9ca3af';
        label.textContent  = (labels[strength - 1] ?? 'Weak') + ' password';
        label.classList.remove('hidden');
    } else {
        label.classList.add('hidden');
    }

    // Show/hide checklist
    document.getElementById('profPwChecklist').classList.toggle('hidden', val.length === 0);

    // Update each requirement row
    const reqMap = {
        'prof-req-length':  checks.length,
        'prof-req-upper':   checks.upper,
        'prof-req-number':  checks.number,
        'prof-req-special': checks.special,
    };

    Object.entries(reqMap).forEach(([id, passed]) => {
        const li   = document.getElementById(id);
        const icon = li.querySelector('.prof-req-icon');
        if (passed) {
            li.style.color   = '#16a34a';
            icon.textContent = '✓';
        } else {
            li.style.color   = '';
            icon.textContent = '○';
        }
    });
});

// ── Frontend validation on submit ────────────
document.getElementById('profilePasswordForm').addEventListener('submit', function (e) {
    const newPw   = document.getElementById('prof_new_password').value;
    const confirm = document.getElementById('prof_new_password_confirmation').value;
    if (newPw && newPw !== confirm) {
        e.preventDefault();
        alert('New passwords do not match.');
        return;
    }
    if (newPw && newPw.length < 8) {
        e.preventDefault();
        alert('New password must be at least 8 characters.');
    }
});
</script>