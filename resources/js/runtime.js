/* laravel-sweetalert2 runtime: shows flashed popups, listens for "sweetalert2" events, handles data-swal-confirm,
   styles Livewire's wire:confirm, and offers promise dialogs (LaravelSwal.confirm/prompt/alert, Alpine $swal). */
(function () {
  if (window.LaravelSweetAlert2) {
    return;
  }

  var CONFIRMED = 'swalConfirmed';
  var NATIVE = 'data-swal-native';
  var ESCAPED = ['confirmButtonText', 'denyButtonText', 'cancelButtonText', 'footer'];
  var settings = { defaults: {}, toast: {}, confirm: {}, prompt: {}, livewireConfirm: true };
  var chain = Promise.resolve();

  function warn(message) {
    if (window.console) {
      console.warn('[laravel-sweetalert2] ' + message);
    }
  }

  function loaded() {
    return window.Swal && typeof window.Swal.fire === 'function' ? window.Swal : null;
  }

  function swal() {
    var s = loaded();
    if (!s) warn('SweetAlert2 is not loaded (window.Swal is missing). Check config/sweetalert2.php "assets".');
    return s;
  }

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function options(given) {
    given = given || {};
    return Object.assign({}, settings.defaults, given.toast ? settings.toast : {}, given);
  }

  function fire(given) {
    var s = swal();
    return s ? s.fire(options(given)) : Promise.resolve(null);
  }

  // SweetAlert2 shows one popup at a time, so queued popups open one after another.
  function queue(list) {
    (list || []).forEach(function (given) {
      chain = chain.then(function () {
        return fire(given);
      }).catch(function (error) {
        warn(error);
      });
    });
    return chain;
  }

  /*
   * Promise dialogs. Text, not HTML: a string title is sent as titleText, labels and footer are
   * escaped, and line breaks in text are kept. Only an explicit `html` option is passed through.
   */
  function safe(given) {
    var o = Object.assign({}, given || {});
    if (typeof o.title === 'string') {
      if (o.titleText == null) o.titleText = o.title;
      delete o.title;
    }
    ESCAPED.forEach(function (key) {
      if (typeof o[key] === 'string') o[key] = escapeHtml(o[key]);
    });
    if (typeof o.text === 'string' && o.text.indexOf('\n') !== -1 && o.html == null) {
      o.html = escapeHtml(o.text).replace(/\n/g, '<br>');
      delete o.text;
    }
    return o;
  }

  // A string message: the first line is the title, the rest is the text.
  function message(given) {
    if (given == null) return {};
    if (typeof given !== 'string') return given;
    var lines = given.split('\n');
    var o = { titleText: lines[0] };
    var rest = lines.slice(1).join('\n');
    if (rest !== '') o.text = rest;
    return o;
  }

  function plain(given) {
    return typeof given === 'string' ? given : (given && (given.titleText || given.title || given.text)) || '';
  }

  function dialog(base, msg, extra) {
    return Object.assign({}, settings.defaults, base, safe(message(msg)), safe(extra));
  }

  // Resolves true when confirmed, false otherwise.
  function confirmDialog(msg, extra) {
    var s = swal();
    if (!s) return Promise.resolve(window.confirm(plain(msg) || 'Are you sure?'));
    return s.fire(dialog(settings.confirm, msg || 'Are you sure?', extra)).then(function (result) {
      return Boolean(result && result.isConfirmed);
    });
  }

  // Resolves the entered text, or null when cancelled (like window.prompt). The second argument is a
  // default value (string) or SweetAlert2 options.
  function promptDialog(msg, extra) {
    if (typeof extra === 'string') extra = { inputValue: extra };
    var s = swal();
    if (!s) return Promise.resolve(window.prompt(plain(msg), (extra && extra.inputValue) || ''));
    var o = dialog(settings.prompt, msg, extra);
    if (!o.didOpen) {
      // Like window.prompt: the default value is selected, so typing replaces it.
      o.didOpen = function () {
        var input = s.getInput();
        if (input && typeof input.select === 'function') input.select();
      };
    }
    return s.fire(o).then(function (result) {
      return result && result.isConfirmed ? String(result.value == null ? '' : result.value) : null;
    });
  }

  // Resolves (with undefined) once the dialog is closed.
  function alertDialog(msg, extra) {
    var s = swal();
    if (!s) {
      window.alert(plain(msg));
      return Promise.resolve();
    }
    return s.fire(dialog({}, msg, extra)).then(function () {});
  }

  function confirmOptions(el) {
    var data = el.dataset;
    var o = Object.assign({}, settings.defaults, settings.confirm);
    o.titleText = data.swalConfirm || o.titleText || 'Are you sure?';
    if (data.swalText) o.text = data.swalText;
    if (data.swalIcon) o.icon = data.swalIcon;
    if (data.swalConfirmButton) o.confirmButtonText = escapeHtml(data.swalConfirmButton);
    if (data.swalCancelButton) o.cancelButtonText = escapeHtml(data.swalCancelButton);
    return o;
  }

  function ask(el, proceed) {
    var s = loaded();
    if (!s) {
      if (window.confirm(el.dataset.swalConfirm || 'Are you sure?')) proceed();
      return;
    }
    s.fire(confirmOptions(el)).then(function (result) {
      if (result && result.isConfirmed) proceed();
    });
  }

  // Capture phase, so the dialog runs before Livewire, Alpine or the browser act on the click.
  document.addEventListener('click', function (event) {
    var el = event.target && event.target.closest ? event.target.closest('[data-swal-confirm]') : null;
    if (!el || el.tagName === 'FORM') return;
    if (el.dataset[CONFIRMED]) {
      delete el.dataset[CONFIRMED];
      return;
    }
    event.preventDefault();
    event.stopImmediatePropagation();
    ask(el, function () {
      el.dataset[CONFIRMED] = '1';
      el.click();
    });
  }, true);

  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!form || !form.matches || !form.matches('form[data-swal-confirm]')) return;
    if (form.dataset[CONFIRMED]) {
      delete form.dataset[CONFIRMED];
      return;
    }
    event.preventDefault();
    event.stopImmediatePropagation();
    var submitter = event.submitter && event.submitter.form === form ? event.submitter : undefined;
    ask(form, function () {
      form.dataset[CONFIRMED] = '1';
      if (typeof form.requestSubmit === 'function') {
        form.requestSubmit(submitter);
      } else {
        delete form.dataset[CONFIRMED];
        form.submit();
      }
    });
  }, true);

  // Livewire: $this->dispatch('sweetalert2', title: 'Saved'). Alpine: $dispatch('sweetalert2', { title: 'Saved' }).
  window.addEventListener('sweetalert2', function (event) {
    var detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
    queue([detail || {}]);
  });

  /*
   * Livewire wire:confirm and wire:confirm.prompt (Livewire 3 and 4). Livewire asks
   * el.__livewire_confirm(action, instead) before running the action; this swaps the browser's
   * confirm()/prompt() for SweetAlert2 and keeps Livewire's rules: "Are you sure?" when empty,
   * "\n" as a line break, "question|expected" for the prompt, and the warning when the expectation
   * is missing. Opt out with config('sweetalert2.livewire_confirm') or data-swal-native on the element
   * or a parent.
   */
  function styleWireConfirm(el, rawName, expression) {
    if (!el) return;
    var key = rawName + '=' + expression;
    // Already ours and unchanged. When Livewire re-inits the directive it puts its own function back.
    if (el.__livewire_confirm && el.__livewire_confirm === el.__laravelSweetAlert2Wrapper && el.__laravelSweetAlert2Key === key) return;

    var native = el.__livewire_confirm && el.__livewire_confirm !== el.__laravelSweetAlert2Wrapper
      ? el.__livewire_confirm
      : el.__laravelSweetAlert2Native;
    var msg = String(expression == null ? '' : expression).replaceAll('\\n', '\n');
    var shouldPrompt = rawName.split('.').slice(1).indexOf('prompt') !== -1;
    if (msg === '') msg = 'Are you sure?';

    el.__laravelSweetAlert2Native = native;
    el.__laravelSweetAlert2Key = key;
    el.__livewire_confirm = el.__laravelSweetAlert2Wrapper = function (action, instead) {
      if (!settings.livewireConfirm || el.closest('[' + NATIVE + ']') || !loaded()) {
        if (typeof native === 'function') return native(action, instead);
        return window.confirm(msg) ? action() : instead();
      }

      if (shouldPrompt) {
        var parts = msg.split('|');
        if (!parts[1]) {
          console.warn('Livewire: Must provide expectation with wire:confirm.prompt');
          return;
        }
        instead();
        promptDialog(parts[0]).then(function (input) {
          if (input === parts[1]) action();
        });
        return;
      }

      instead();
      confirmDialog(msg).then(function (yes) {
        if (yes) action();
      });
    };
  }

  function sweepWireConfirm() {
    document.querySelectorAll('[wire\\:confirm], [wire\\:confirm\\.prompt]').forEach(function (el) {
      var name = el.hasAttribute('wire:confirm.prompt') ? 'wire:confirm.prompt' : 'wire:confirm';
      if (el.__livewire_confirm) styleWireConfirm(el, name, el.getAttribute(name));
    });
  }

  var hooked = false;
  function hookLivewire(Livewire) {
    if (hooked || !Livewire || typeof Livewire.hook !== 'function') return;
    hooked = true;
    // Runs after Livewire's own confirm directive, for every element (including morphed-in ones).
    Livewire.hook('directive.init', function (payload) {
      var directive = payload && payload.directive;
      if (directive && directive.value === 'confirm') {
        styleWireConfirm(payload.el, directive.rawName || directive.raw || 'wire:confirm', directive.expression);
      }
    });
    sweepWireConfirm();
  }

  if (window.Livewire) hookLivewire(window.Livewire);
  document.addEventListener('livewire:init', function () { hookLivewire(window.Livewire); });
  document.addEventListener('livewire:navigated', sweepWireConfirm);

  var api = {
    configure: function (given) {
      given = given || {};
      ['defaults', 'toast', 'confirm', 'prompt'].forEach(function (key) {
        if (given[key]) settings[key] = given[key];
      });
      if (typeof given.livewireConfirm === 'boolean') settings.livewireConfirm = given.livewireConfirm;
      return api;
    },
    queue: queue,
    fire: fire,
    toast: function (given) {
      return queue([Object.assign({ toast: true }, given)]);
    },
    confirm: confirmDialog,
    prompt: promptDialog,
    alert: alertDialog
  };

  var magic = {
    confirm: confirmDialog,
    prompt: promptDialog,
    alert: alertDialog,
    fire: fire,
    toast: api.toast
  };

  var magicRegistered = false;
  function registerMagic(Alpine) {
    if (magicRegistered || !Alpine || typeof Alpine.magic !== 'function') return;
    magicRegistered = true;
    Alpine.magic('swal', function () {
      return magic;
    });
  }

  if (window.Alpine) registerMagic(window.Alpine);
  document.addEventListener('alpine:init', function () { registerMagic(window.Alpine); });

  window.LaravelSweetAlert2 = api;
  window.LaravelSwal = api;
})();
