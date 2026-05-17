<?php // app/views/partials/chatbot.php ?>

<?php
// ─────────────────────────────────────────────
// Petal and Soul — AI Chatbot Widget
// Include just before the closing </body> tag
// ─────────────────────────────────────────────
?>

<style>
  @keyframes pulse-dot {
    0%, 100% { transform: scale(1); opacity: 1; }
    50%       { transform: scale(1.4); opacity: 0.6; }
  }
  @keyframes bounce-dot {
    0%, 60%, 100% { transform: translateY(0); }
    30%           { transform: translateY(-5px); }
  }
  @keyframes msg-in {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .chat-msg-anim   { animation: msg-in 0.2s ease forwards; }
  .typing-dot      { animation: bounce-dot 1.1s infinite; }
  .typing-dot:nth-child(2) { animation-delay: 0.18s; }
  .typing-dot:nth-child(3) { animation-delay: 0.36s; }
  .pulse-dot       { animation: pulse-dot 2s infinite; }

  #chat-messages::-webkit-scrollbar       { width: 4px; }
  #chat-messages::-webkit-scrollbar-thumb { background: #fde8ed; border-radius: 4px; }

  #chat-window {
    transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), opacity 0.2s ease;
    transform-origin: bottom right;
  }
  #chat-window.chat-open {
    transform: scale(1) translateY(0);
    opacity: 1;
    pointer-events: all;
  }
  #chat-window.chat-closed {
    transform: scale(0.85) translateY(20px);
    opacity: 0;
    pointer-events: none;
  }

  .chat-product-card { text-decoration: none; }
  .chat-product-card:hover { transform: translateY(-1px); }

  .chat-add-btn {
    flex-shrink: 0;
    font-size: 11px;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 20px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    background: linear-gradient(135deg, #e8718a, #c9556d);
    color: white;
  }
  .chat-add-btn:hover  { opacity: 0.88; transform: scale(1.05); }
  .chat-add-btn:active { transform: scale(0.97); }
  .chat-add-btn.added  { background: #2ecc71; }
</style>

<!-- ── Bubble Button ───────────────────────── -->
<button id="chat-bubble"
  class="fixed bottom-6 right-6 z-[9999] w-14 h-14 rounded-full flex items-center justify-center border-0 cursor-pointer"
  style="background: linear-gradient(135deg, #e8718a, #c9556d); box-shadow: 0 8px 24px rgba(232,113,138,0.4);"
  aria-label="Open chat">
  <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
  </svg>
  <span id="chat-notif-dot"
    class="pulse-dot absolute top-0.5 right-0.5 w-3 h-3 rounded-full border-2 border-white"
    style="background:#ff3b5c;"></span>
</button>

<!-- ── Chat Window ─────────────────────────── -->
<div id="chat-window"
  class="chat-closed fixed bottom-24 right-6 z-[9998] flex flex-col overflow-hidden rounded-2xl bg-white"
  style="width:360px; max-height:520px; box-shadow:0 8px 40px rgba(232,113,138,0.25);"
  role="dialog" aria-label="Petal and Soul Chat">

  <!-- Header -->
  <div class="flex items-center gap-3 px-4 py-3 text-white"
    style="background: linear-gradient(135deg, #e8718a, #c9556d);">
    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg flex-shrink-0"
      style="background:rgba(255,255,255,0.22);">🌸</div>
    <div class="flex-1 min-w-0">
      <p class="text-sm font-bold tracking-wide leading-tight">Petal and Soul</p>
      <p class="text-[11px] opacity-85 flex items-center gap-1">
        <span class="inline-block w-1.5 h-1.5 rounded-full" style="background:#7dff9b;"></span>
        Online now
      </p>
    </div>
    <button id="chat-close"
      class="p-1 rounded-full opacity-80 hover:opacity-100 transition-opacity border-0 bg-transparent text-white cursor-pointer"
      aria-label="Close chat">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </div>

  <!-- Messages -->
  <div id="chat-messages"
    class="flex-1 overflow-y-auto px-3.5 py-4 flex flex-col gap-2.5"
    style="scroll-behavior:smooth; background:#fafafa;">
  </div>

  <!-- Quick Replies -->
  <div id="chat-quick-replies"
    class="flex flex-wrap gap-1.5 px-3.5 pb-3 pt-1.5"
    style="background:#fafafa;">
    <button class="quick-reply-btn text-xs px-3 py-1.5 rounded-full cursor-pointer transition-all border"
      style="background:#fde8ed; border-color:rgba(232,113,138,0.3); color:#c9556d;"
      data-msg="What flowers do you have?">💐 Flowers</button>
    <button class="quick-reply-btn text-xs px-3 py-1.5 rounded-full cursor-pointer transition-all border"
      style="background:#fde8ed; border-color:rgba(232,113,138,0.3); color:#c9556d;"
      data-msg="How does delivery work?">🚚 Delivery</button>
    <button class="quick-reply-btn text-xs px-3 py-1.5 rounded-full cursor-pointer transition-all border"
      style="background:#fde8ed; border-color:rgba(232,113,138,0.3); color:#c9556d;"
      data-msg="What is your return policy?">🔄 Returns</button>
    <button class="quick-reply-btn text-xs px-3 py-1.5 rounded-full cursor-pointer transition-all border"
      style="background:#fde8ed; border-color:rgba(232,113,138,0.3); color:#c9556d;"
      data-msg="Recommend a bouquet for a birthday?">🎂 Birthday</button>
  </div>

  <!-- Input -->
  <div class="flex items-center gap-2 px-3.5 py-3 bg-white border-t border-gray-100">
    <input id="chat-input"
      type="text"
      placeholder="Type a message..."
      maxlength="500"
      autocomplete="off"
      class="flex-1 text-sm px-4 py-2 rounded-full outline-none border transition-colors"
      style="border-color:#ececec; color:#1a1a2e; height:40px;"
      onfocus="this.style.borderColor='#e8718a'"
      onblur="this.style.borderColor='#ececec'"/>
    <button id="chat-send"
      class="flex-shrink-0 flex items-center justify-center rounded-full border-0 cursor-pointer transition-transform hover:scale-110 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
      style="width:38px; height:38px; background:linear-gradient(135deg,#e8718a,#c9556d); box-shadow:0 3px 10px rgba(232,113,138,0.4);"
      aria-label="Send">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
      </svg>
    </button>
  </div>
</div>

<script>
(function () {
  'use strict';

  var STORAGE_KEY = 'petal_chat_session';

  var bubble       = document.getElementById('chat-bubble');
  var chatWindow   = document.getElementById('chat-window');
  var closeBtn     = document.getElementById('chat-close');
  var messagesEl   = document.getElementById('chat-messages');
  var inputEl      = document.getElementById('chat-input');
  var sendBtn      = document.getElementById('chat-send');
  var quickReplies = document.getElementById('chat-quick-replies');
  var notifDot     = document.getElementById('chat-notif-dot');

  var BASE_URL = '<?= defined("APP_URL") ? rtrim(APP_URL, "/") : "" ?>';

  var STATUS_COLORS = {
    'Pending':    '#f39c12',
    'Confirmed':  '#3498db',
    'Processing': '#9b59b6',
    'Ready':      '#1abc9c',
    'Delivered':  '#2ecc71',
    'Cancelled':  '#e74c3c',
  };

  // ── Session persistence ──────────────────────
  function loadSession() {
    try {
      var raw = sessionStorage.getItem(STORAGE_KEY);
      if (!raw) return null;
      var s = JSON.parse(raw);
      // Expire after 2 hours
      if (Date.now() - s.savedAt > 2 * 60 * 60 * 1000) {
        sessionStorage.removeItem(STORAGE_KEY);
        return null;
      }
      return s;
    } catch (e) { return null; }
  }

  function saveSession() {
    try {
      sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
        history:    chatHistory,
        chatEnded:  chatEnded,
        hasGreeted: hasGreeted,
        savedAt:    Date.now(),
      }));
    } catch (e) {}
  }

  function clearSession() {
    try { sessionStorage.removeItem(STORAGE_KEY); } catch (e) {}
  }

  // ── State ─────────────────────────────────────
  var session    = loadSession();
  var chatHistory = session ? session.history    : [];
  var hasGreeted  = session ? session.hasGreeted : false;
  var chatEnded   = session ? session.chatEnded  : false;
  var isOpen      = false;

  // ── Simple Markdown renderer ─────────────────
  function renderMarkdown(text) {
    var safe = text
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');

    return safe
      .replace(/\*\*(.*?)\*\*/g, '<strong style="color:#c9556d;">$1</strong>')
      .replace(/\*(.*?)\*/g,     '<em>$1</em>')
      .replace(/\n\* (.*?)(?=\n|$)/g, '\n<li style="margin-left:16px;list-style:disc;">$1</li>')
      .replace(/(<li.*<\/li>)/s, '<ul style="margin:4px 0;">$1</ul>')
      .replace(/\n/g, '<br>');
  }

  // ── Restore chat history on load ────────────
  function restoreMessages() {
    if (!session || chatHistory.length === 0) return;

    // Re-render messages from history (text only, no cards)
    chatHistory.forEach(function (turn) {
      if (turn.role === 'user') {
        addUserMessage(turn.text, true);
      } else {
        addBotMessage(turn.text, true);
      }
    });

    if (chatEnded) {
      inputEl.disabled    = true;
      sendBtn.disabled    = true;
      inputEl.placeholder = 'Chat has ended.';
      inputEl.style.background = '#f9f9f9';
      quickReplies.style.display = 'none';
    }
  }

  // ── Toggle chat window ───────────────────────
  function toggleChat() {
    isOpen = !isOpen;
    if (isOpen) {
      chatWindow.classList.replace('chat-closed', 'chat-open');
      notifDot.style.display = 'none';
      if (!hasGreeted) {
        hasGreeted = true;
        setTimeout(function () {
          addBotMessage("Hi po! 🌸 Welcome to Petal and Soul! I'm here to help you with flowers, bouquets, delivery info, and more. How can I assist you today?");
          saveSession();
        }, 350);
      }
      if (!chatEnded) inputEl.focus();
      setTimeout(scrollBottom, 50);
    } else {
      chatWindow.classList.replace('chat-open', 'chat-closed');
    }
  }

  bubble.addEventListener('click', toggleChat);
  closeBtn.addEventListener('click', toggleChat);

  document.addEventListener('click', function (e) {
    if (isOpen && !chatWindow.contains(e.target) && e.target !== bubble) {
      toggleChat();
    }
  });

  // ── Add messages ─────────────────────────────
  function addBotMessage(text, silent) {
    var div = document.createElement('div');
    div.className = (silent ? '' : 'chat-msg-anim ') + 'self-start max-w-[82%] rounded-2xl rounded-bl-sm px-3.5 py-2.5 text-sm leading-relaxed';
    div.style.cssText = 'background:#f0f0f2; color:#1a1a2e; word-break:break-word;';
    div.innerHTML = renderMarkdown(text);
    messagesEl.appendChild(div);
    if (!silent) scrollBottom();
    return div;
  }

  function addUserMessage(text, silent) {
    var div = document.createElement('div');
    div.className = (silent ? '' : 'chat-msg-anim ') + 'self-end max-w-[82%] rounded-2xl rounded-br-sm px-3.5 py-2.5 text-sm leading-relaxed text-white';
    div.style.cssText = 'background:linear-gradient(135deg,#e8718a,#c9556d); word-break:break-word;';
    div.textContent = text;
    messagesEl.appendChild(div);
    if (!silent) scrollBottom();
  }

  function addTypingIndicator() {
    var div = document.createElement('div');
    div.id  = 'chat-typing';
    div.className = 'self-start flex items-center gap-1 px-3.5 py-3 rounded-2xl rounded-bl-sm';
    div.style.background = '#f0f0f2';
    div.innerHTML = '<span class="typing-dot w-2 h-2 rounded-full" style="background:#e8718a;"></span>'
                  + '<span class="typing-dot w-2 h-2 rounded-full" style="background:#e8718a;"></span>'
                  + '<span class="typing-dot w-2 h-2 rounded-full" style="background:#e8718a;"></span>';
    messagesEl.appendChild(div);
    scrollBottom();
  }

  function removeTypingIndicator() {
    var el = document.getElementById('chat-typing');
    if (el) el.remove();
  }

  function scrollBottom() {
    messagesEl.scrollTop = messagesEl.scrollHeight;
  }

  // ── Lock chat (after profanity) ──────────────
  function lockChat() {
    chatEnded = true;
    addBotMessage("If you'd like to continue, please refresh the page to start a new conversation. 🌸");
    inputEl.disabled    = true;
    sendBtn.disabled    = true;
    inputEl.placeholder = 'Chat has ended.';
    inputEl.style.background = '#f9f9f9';
    quickReplies.style.display = 'none';
    clearSession(); // clear session so next page load starts fresh
  }

  // ── Add to Cart from chat ────────────────────
  function addToCartFromChat(productId, btn) {
    btn.disabled  = true;
    btn.textContent = '...';

    // OrderController::addToCart() reads $_POST via $this->post()
    // X-Requested-With triggers isAjax() so it returns JSON
    var body = new URLSearchParams({ product_id: productId, quantity: 1 });

    fetch(BASE_URL + '/shop/cart/add', {
      method:      'POST',
      headers:     {
        'Content-Type':     'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body:        body.toString(),
      credentials: 'same-origin',
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.success || data.status === 'success') {
        btn.textContent = '✔ Added';
        btn.classList.add('added');
        var badge = document.querySelector('[data-cart-count]');
        if (badge && data.cart_count !== undefined) {
          badge.textContent = data.cart_count;
        }
      } else {
        btn.textContent = data.message || 'Failed';
        setTimeout(function () { btn.textContent = 'Add'; btn.disabled = false; }, 2000);
      }
    })
    .catch(function () {
      btn.textContent = 'Add';
      btn.disabled = false;
    });
  }

  // ── Render product card list ─────────────────
  function renderProductCards(products) {
    if (!products || products.length === 0) return;

    var wrapper = document.createElement('div');
    wrapper.className = 'chat-msg-anim self-start w-full flex flex-col gap-2';
    wrapper.style.maxWidth = '95%';

    products.forEach(function (p) {
      var card = document.createElement('div');
      card.className = 'flex items-center gap-2.5 p-2 rounded-xl border transition-all';
      card.style.cssText = 'background:#fff; border-color:#f3d0d9; box-shadow:0 1px 4px rgba(232,113,138,0.1);';

      // Thumbnail (clickable link)
      var a = document.createElement('a');
      a.href   = p.url;
      a.target = '_blank';
      a.rel    = 'noopener';
      a.style.flexShrink = '0';

      var img = document.createElement('img');
      img.src   = p.image || '';
      img.alt   = p.name;
      img.className = 'rounded-lg object-cover';
      img.style.cssText = 'width:52px; height:52px; background:#fde8ed; flex-shrink:0;';
      img.onerror = function () { this.style.visibility = 'hidden'; };
      a.appendChild(img);

      // Info
      var info = document.createElement('div');
      info.className = 'flex-1 min-w-0';
      info.innerHTML = '<p class="text-sm font-semibold truncate" style="color:#1a1a2e;">' + p.name + '</p>'
        + '<p class="text-xs font-bold" style="color:#e8718a;">' + p.price + '</p>'
        + '<p class="text-[11px]" style="color:' + (p.in_stock ? '#2ecc71' : '#e74c3c') + ';">'
        + (p.in_stock ? '✔ In Stock' : '✖ Out of Stock') + '</p>';

      // Add to Cart button
      var btn = document.createElement('button');
      btn.className   = 'chat-add-btn';
      btn.textContent = 'Add';
      if (!p.in_stock) {
        btn.disabled = true;
        btn.style.background = '#ccc';
      } else {
        btn.addEventListener('click', function () {
          addToCartFromChat(p.id, btn);
        });
      }

      card.appendChild(a);
      card.appendChild(info);
      card.appendChild(btn);
      wrapper.appendChild(card);
    });

    messagesEl.appendChild(wrapper);
    scrollBottom();
  }

  // ── Product cards (browse / recommend) ───────
  async function showProductCards(filterNames) {
    try {
      var res = await fetch(BASE_URL + '/chatbot/products', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ names: filterNames || [] })
      });
      var data = await res.json();

      if (!data.products || data.products.length === 0) {
        addBotMessage("Sorry po, walang available products ngayon. 🌸");
        return;
      }

      if (!filterNames || filterNames.length === 0) {
        addBotMessage("Here are our flowers and bouquets po! 🌸 Click any to view details.");
      }

      renderProductCards(data.products);
    } catch (e) {
      addBotMessage("Sorry po, hindi ko ma-load ang products ngayon. Please try again! 🌸");
    }
  }

  // ── Order cards ──────────────────────────────
  async function showOrderCards(userMessage) {
    try {
      var res = await fetch(BASE_URL + '/chatbot/orders', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ message: userMessage || '' })
      });
      var data = await res.json();

      if (data.error === 'not_logged_in') {
        addBotMessage("Please log in muna po para makita ang inyong orders. 🌸");
        return;
      }

      if (!data.orders || data.orders.length === 0) {
        addBotMessage("Wala pa kayong orders po. Start shopping! 💐");
        return;
      }

      var label = data.orders.length === 1
        ? "Here is your order po! 📦"
        : "Here are your recent orders po! 📦";
      addBotMessage(label);

      var wrapper = document.createElement('div');
      wrapper.className = 'chat-msg-anim self-start w-full flex flex-col gap-2';
      wrapper.style.maxWidth = '95%';

      data.orders.forEach(function (o) {
        var color = STATUS_COLORS[o.status] || '#888';
        var card  = document.createElement('div');
        card.className = 'rounded-xl p-3 border';
        card.style.cssText = 'background:#fff; border-color:#f3d0d9;';
        card.innerHTML =
          '<div class="flex items-center justify-between mb-1">'
            + '<span class="text-sm font-bold" style="color:#1a1a2e;">' + o.order_number + '</span>'
            + '<span class="text-[11px] font-semibold px-2 py-0.5 rounded-full" style="color:' + color + ';background:' + color + '22;">' + o.status + '</span>'
          + '</div>'
          + '<p class="text-xs" style="color:#888;">'
            + o.date + ' &nbsp;·&nbsp; ' + o.items + ' item(s) &nbsp;·&nbsp; '
            + '<span class="font-semibold" style="color:#e8718a;">' + o.total + '</span>'
          + '</p>';
        wrapper.appendChild(card);
      });

      messagesEl.appendChild(wrapper);
      scrollBottom();
    } catch (e) {
      addBotMessage("Sorry po, hindi ko ma-load ang orders. Please try again! 🌸");
    }
  }

  // ── Send message ─────────────────────────────
  async function sendMessage(text) {
    if (!text || chatEnded || sendBtn.disabled) return;

    inputEl.value    = '';
    sendBtn.disabled = true;
    quickReplies.style.display = 'none';

    addUserMessage(text);
    chatHistory.push({ role: 'user', text: text });
    saveSession();

    addTypingIndicator();

    try {
      var res = await fetch(BASE_URL + '/chatbot/message', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ message: text, history: chatHistory.slice(-6) }),
      });

      var data = await res.json();
      removeTypingIndicator();

      if (data.action === 'show_products') {
        await showProductCards([]);

      } else if (data.action === 'show_recommended') {
        if (data.reply && data.reply.trim()) {
          addBotMessage(data.reply.trim());
          chatHistory.push({ role: 'model', text: data.reply.trim() });
          saveSession();
        }
        await showProductCards(data.names || []);

      } else if (data.action === 'show_orders') {
        await showOrderCards(text);

      } else if (data.reply) {
        addBotMessage(data.reply);
        chatHistory.push({ role: 'model', text: data.reply });
        saveSession();
        if (data.ended) lockChat();

      } else {
        addBotMessage("Sorry po, I'm having trouble connecting. Please try again! 🌸");
      }

    } catch (err) {
      removeTypingIndicator();
      addBotMessage("Sorry po, something went wrong. Please try again! 🌸");
    }

    if (!chatEnded) {
      sendBtn.disabled = false;
      inputEl.focus();
    }
  }

  // ── Event listeners ──────────────────────────
  sendBtn.addEventListener('click', function () {
    sendMessage(inputEl.value.trim());
  });

  inputEl.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage(inputEl.value.trim());
    }
  });

  document.querySelectorAll('.quick-reply-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      sendMessage(btn.getAttribute('data-msg'));
    });
    btn.addEventListener('mouseover', function () {
      btn.style.background = '#e8718a';
      btn.style.color      = '#fff';
    });
    btn.addEventListener('mouseout', function () {
      btn.style.background = '#fde8ed';
      btn.style.color      = '#c9556d';
    });
  });

  // ── Restore previous session on page load ───
  restoreMessages();

})();
</script>