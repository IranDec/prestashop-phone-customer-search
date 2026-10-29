;(function () {
  'use strict';

  function start() {
    var customerInput = document.getElementById('customer-search-input');
    if (!customerInput || !window.phoneCustomerSearchEndpoint || document.getElementById('phone-customer-search')) return;

    var panel = document.createElement('section');
    panel.id = 'phone-customer-search';
    panel.className = 'pcs-panel';
    panel.innerHTML = '<div class="pcs-heading"><span class="pcs-icon" aria-hidden="true">☎</span>' +
      '<div><strong>جستجو با شماره موبایل</strong><small>شماره را با ارقام فارسی یا انگلیسی وارد کنید</small></div></div>' +
      '<input id="phone-customer-search-input" type="tel" class="form-control pcs-input" autocomplete="off" inputmode="tel" ' +
      'aria-label="جستجوی مشتری با شماره موبایل" placeholder="مثلاً ۰۹۱۲۱۲۳۴۵۶۷">' +
      '<div id="phone-customer-search-results" class="pcs-results" role="status" aria-live="polite"></div>' +
      '<small class="pcs-hint">جستجو در شمارهٔ موبایل ثبت‌شده در نشانی مشتری انجام می‌شود.</small>';
    customerInput.parentNode.insertBefore(panel, customerInput.nextSibling);

    var phoneInput = panel.querySelector('#phone-customer-search-input');
    var results = panel.querySelector('#phone-customer-search-results');
    var timer;
    var requestId = 0;
    var activeController = null;
    var digitMap = {
      '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4', '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9',
      '٠': '0', '١': '1', '٢': '2', '٣': '3', '٤': '4', '٥': '5', '٦': '6', '٧': '7', '٨': '8', '٩': '9'
    };

    function normalizePhone(value) {
      return value.replace(/[۰-۹٠-٩]/g, function (digit) { return digitMap[digit]; }).replace(/\D/g, '');
    }

    function setMessage(message, className) {
      results.textContent = '';
      var item = document.createElement('div');
      item.className = 'pcs-message' + (className ? ' ' + className : '');
      item.textContent = message;
      results.appendChild(item);
    }

    function render(customers) {
      results.textContent = '';
      if (!customers.length) {
        setMessage('مشتری با این شماره پیدا نشد.');
        return;
      }
      customers.forEach(function (customer) {
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'pcs-customer';
        var name = document.createElement('strong');
        name.textContent = [customer.firstname, customer.lastname].filter(Boolean).join(' ');
        var phone = document.createElement('span');
        phone.className = 'pcs-phone';
        phone.textContent = customer.phone_mobile || '';
        var email = document.createElement('small');
        email.textContent = customer.email || '';
        button.appendChild(name);
        button.appendChild(phone);
        button.appendChild(email);
        button.addEventListener('click', function () {
          customerInput.value = customer.email;
          customerInput.dispatchEvent(new Event('input', { bubbles: true }));
          customerInput.dispatchEvent(new Event('keyup', { bubbles: true }));
          customerInput.focus();
          setMessage('مشتری انتخاب شد؛ نتیجهٔ آن را در جستجوی اصلی انتخاب کنید.', 'pcs-success');
        });
        results.appendChild(button);
      });
    }

    phoneInput.addEventListener('input', function () {
      window.clearTimeout(timer);
      var thisRequest = ++requestId;
      if (activeController) activeController.abort();
      var query = normalizePhone(phoneInput.value.trim());
      results.textContent = '';
      if (query.length < 7) {
        if (query.length) setMessage('برای جلوگیری از جستجوی سنگین، حداقل ۷ رقم وارد کنید.');
        return;
      }
      setMessage('در حال جستجو…', 'pcs-loading');
      timer = window.setTimeout(function () {
        activeController = typeof AbortController !== 'undefined' ? new AbortController() : null;
        var options = { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } };
        if (activeController) options.signal = activeController.signal;
        var url = window.phoneCustomerSearchEndpoint + '&ajax=1&action=Search&query=' + encodeURIComponent(query);
        fetch(url, options)
          .then(function (response) {
            if (!response.ok) throw new Error('Search failed');
            return response.json();
          })
          .then(function (data) {
            if (thisRequest === requestId) render(data.customers || []);
          })
          .catch(function (error) {
            if (thisRequest === requestId && error.name !== 'AbortError') setMessage('جستجو انجام نشد؛ صفحه را تازه‌سازی کنید.', 'pcs-error');
          });
      }, 450);
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
  else start();
  window.setTimeout(start, 1200);
})();
