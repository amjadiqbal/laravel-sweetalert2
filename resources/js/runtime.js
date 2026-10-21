/* laravel-sweetalert2 runtime: shows flashed popups, listens for "sweetalert2" events and handles data-swal-confirm. */
(function () {
  if (window.LaravelSweetAlert2) {
    return;
  }

  var CONFIRMED = 'swalConfirmed';
  var settings = { defaults: {}, toast: {}, confirm: {} };
  var chain = Promise.resolve();

  function warn(message) {
    if (window.console) {
      console.warn('[laravel-sweetalert2] ' + message);
    }
  }

  function swal() {
    if (window.Swal && typeof window.Swal.fire === 'function') {
      return window.Swal;
    }
    warn('SweetAlert2 is not loaded (window.Swal is missing). Check config/sweetalert2.php "assets".');
    return null;
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
    var s = window.Swal && typeof window.Swal.fire === 'function' ? window.Swal : null;
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

  var api = {
    configure: function (given) {
      given = given || {};
      ['defaults', 'toast', 'confirm'].forEach(function (key) {
        if (given[key]) settings[key] = given[key];
      });
      return api;
    },
    queue: queue,
    fire: fire,
    toast: function (given) {
      return queue([Object.assign({ toast: true }, given)]);
    }
  };

  window.LaravelSweetAlert2 = api;
})();
