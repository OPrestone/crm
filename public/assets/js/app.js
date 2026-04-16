/* ============================================
   ENTERPRISE CRM — APP JAVASCRIPT
   No build step, plain vanilla JS
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {

  // ─── Dark Mode Toggle ──────────────────────────
  const darkToggle = document.getElementById('darkModeToggle');
  const html = document.documentElement;
  const savedTheme = localStorage.getItem('crm-theme') || 'light';
  html.setAttribute('data-bs-theme', savedTheme);
  updateDarkIcon(savedTheme);

  if (darkToggle) {
    darkToggle.addEventListener('click', function() {
      const current = html.getAttribute('data-bs-theme');
      const next = current === 'dark' ? 'light' : 'dark';
      html.setAttribute('data-bs-theme', next);
      localStorage.setItem('crm-theme', next);
      updateDarkIcon(next);
    });
  }

  function updateDarkIcon(theme) {
    if (!darkToggle) return;
    const icon = darkToggle.querySelector('i');
    if (icon) {
      icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    }
  }

  // ─── Sidebar Toggle (mobile) ──────────────────
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.sidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('open');
      sidebarOverlay.classList.toggle('show');
    });
  }
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
      sidebar.classList.remove('open');
      sidebarOverlay.classList.remove('show');
    });
  }

  // ─── Auto-dismiss Alerts ──────────────────────
  document.querySelectorAll('.auto-dismiss').forEach(function(alert) {
    setTimeout(function() {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      bsAlert.close();
    }, 4000);
  });

  // ─── Lead Score Range Display ─────────────────
  const scoreRange = document.getElementById('lead_score');
  const scoreDisplay = document.getElementById('scoreDisplay');
  if (scoreRange && scoreDisplay) {
    scoreRange.addEventListener('input', function() {
      scoreDisplay.textContent = this.value;
    });
  }

  // ─── Confirm Delete ───────────────────────────
  window.confirmDelete = function(action, name) {
    if (!confirm(`Are you sure you want to delete "${name}"? This cannot be undone.`)) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;
    form.innerHTML = `
      <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
      <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
  };

  // ─── Invoice Line Items ───────────────────────
  let itemIndex = document.querySelectorAll('.invoice-item-row').length;
  const addItemBtn = document.getElementById('addItemBtn');
  const invoiceItems = document.getElementById('invoiceItems');

  if (addItemBtn && invoiceItems) {
    addItemBtn.addEventListener('click', function() {
      const row = document.createElement('div');
      row.className = 'row g-2 mb-2 invoice-item-row';
      row.innerHTML = `
        <div class="col-md-5"><input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="Description" required></div>
        <div class="col-md-2"><input type="number" name="items[${itemIndex}][quantity]" class="form-control item-qty" placeholder="Qty" value="1" min="0.01" step="0.01" required></div>
        <div class="col-md-2"><input type="number" name="items[${itemIndex}][unit_price]" class="form-control item-price" placeholder="Price" min="0" step="0.01" required></div>
        <div class="col-md-2"><input type="text" class="form-control item-total" placeholder="Total" readonly></div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-sm w-100 remove-item-btn"><i class="bi bi-trash"></i></button></div>
      `;
      invoiceItems.appendChild(row);
      bindItemRow(row);
      itemIndex++;
    });
  }

  // Bind existing rows
  document.querySelectorAll('.invoice-item-row').forEach(bindItemRow);

  function bindItemRow(row) {
    const qty = row.querySelector('.item-qty');
    const price = row.querySelector('.item-price');
    const total = row.querySelector('.item-total');
    const removeBtn = row.querySelector('.remove-item-btn');

    function updateRowTotal() {
      const q = parseFloat(qty?.value) || 0;
      const p = parseFloat(price?.value) || 0;
      if (total) total.value = (q * p).toFixed(2);
      updateInvoiceTotal();
    }

    if (qty) qty.addEventListener('input', updateRowTotal);
    if (price) price.addEventListener('input', updateRowTotal);
    if (removeBtn) {
      removeBtn.addEventListener('click', function() {
        if (document.querySelectorAll('.invoice-item-row').length > 1) {
          row.remove();
          updateInvoiceTotal();
        }
      });
    }
  }

  // Invoice total recalculation
  window.updateInvoiceTotal = function() {
    let subtotal = 0;
    document.querySelectorAll('.item-total').forEach(function(el) {
      subtotal += parseFloat(el.value) || 0;
    });
    const taxRate = parseFloat(document.getElementById('tax_rate')?.value) || 0;
    const discount = parseFloat(document.getElementById('discount')?.value) || 0;
    const tax = subtotal * (taxRate / 100);
    const total = Math.max(0, subtotal + tax - discount);

    const fmt = v => v.toFixed(2);
    const st = document.getElementById('subtotalDisplay');
    const td = document.getElementById('taxDisplay');
    const dd = document.getElementById('discountDisplay');
    const tot = document.getElementById('totalDisplay');
    if (st) st.textContent = fmt(subtotal);
    if (td) td.textContent = fmt(tax);
    if (dd) dd.textContent = fmt(discount);
    if (tot) tot.textContent = fmt(total);
  };

  // ─── Kanban Drag & Drop ───────────────────────
  let draggedCard = null;

  document.querySelectorAll('.kanban-card').forEach(function(card) {
    card.addEventListener('dragstart', function(e) {
      draggedCard = card;
      card.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
    });
    card.addEventListener('dragend', function() {
      card.classList.remove('dragging');
      draggedCard = null;
      document.querySelectorAll('.kanban-cards').forEach(c => c.classList.remove('drag-over'));
    });
  });

  document.querySelectorAll('.kanban-cards').forEach(function(col) {
    col.addEventListener('dragover', function(e) {
      e.preventDefault();
      col.classList.add('drag-over');
    });
    col.addEventListener('dragleave', function() {
      col.classList.remove('drag-over');
    });
    col.addEventListener('drop', function(e) {
      e.preventDefault();
      col.classList.remove('drag-over');
      if (!draggedCard) return;

      const newStageId = col.dataset.stageId;
      const oldCol = draggedCard.closest('.kanban-cards');
      if (oldCol === col) return;

      col.appendChild(draggedCard);
      updateColumnCount(oldCol);
      updateColumnCount(col);

      // AJAX update
      const id = draggedCard.dataset.id;
      const type = draggedCard.dataset.type;
      fetch('/kanban/update', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ id, stage_id: newStageId, type }),
      }).then(r => r.json()).catch(console.error);
    });
  });

  function updateColumnCount(col) {
    if (!col) return;
    const column = col.closest('.kanban-column');
    if (!column) return;
    const badge = column.querySelector('.badge');
    if (badge) badge.textContent = col.querySelectorAll('.kanban-card').length;
  }

  // ─── Pipeline Stage Modal Type ─────────────────
  document.querySelectorAll('[data-bs-target="#addStageModal"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const type = btn.dataset.type;
      if (type) {
        const sel = document.getElementById('stageTypeSelect');
        if (sel) sel.value = type;
      }
    });
  });

});
