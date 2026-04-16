let lineIndex = document.querySelectorAll('.line-item-row').length;

function recalculate() {
    let subtotal = 0;
    document.querySelectorAll('.line-item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.line-qty').value) || 0;
        const price = parseFloat(row.querySelector('.line-price').value) || 0;
        const disc = parseFloat(row.querySelector('.line-discount').value) || 0;
        const total = qty * price * (1 - disc / 100);
        row.querySelector('.line-total').value = total.toFixed(2);
        subtotal += total;
    });
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const taxRate = parseFloat(document.getElementById('taxRateInput').value) || 0;
    const taxable = subtotal - discount;
    const tax = taxable * taxRate / 100;
    const total = taxable + tax;
    document.getElementById('summarySubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('summaryTax').textContent = '$' + tax.toFixed(2);
    document.getElementById('summaryTotal').textContent = '$' + total.toFixed(2);
}

function addLine() {
    const tbody = document.getElementById('lineItems');
    const productOptions = `<option value="">— Pick product (optional) —</option>`
        + Array.from(document.querySelectorAll('.product-select')[0]?.querySelectorAll('option[value]:not([value=""])') || [])
            .map(o => `<option value="${o.value}" data-price="${o.dataset.price}" data-name="${o.dataset.name}">${o.textContent}</option>`)
            .join('');
    const tr = document.createElement('tr');
    tr.className = 'line-item-row';
    tr.dataset.index = lineIndex;
    tr.innerHTML = `
        <td>
            <select name="items[${lineIndex}][product_id]" class="form-select form-select-sm mb-1 product-select">${productOptions}</select>
            <input type="text" name="items[${lineIndex}][description]" class="form-control form-control-sm line-desc" placeholder="Description" required>
        </td>
        <td><input type="number" name="items[${lineIndex}][quantity]" class="form-control form-control-sm line-qty" value="1" min="0.01" step="0.01" required></td>
        <td><input type="number" name="items[${lineIndex}][unit_price]" class="form-control form-control-sm line-price" value="0" min="0" step="0.01" required></td>
        <td><input type="number" name="items[${lineIndex}][discount]" class="form-control form-control-sm line-discount" value="0" min="0" max="100" step="0.01"></td>
        <td><input type="text" class="form-control form-control-sm line-total bg-light" value="0.00" readonly></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-line"><i class="bi bi-x"></i></button></td>`;
    tbody.appendChild(tr);
    bindRow(tr);
    lineIndex++;
    recalculate();
}

function bindRow(row) {
    row.querySelectorAll('.line-qty, .line-price, .line-discount').forEach(el => el.addEventListener('input', recalculate));
    row.querySelector('.remove-line').addEventListener('click', () => { row.remove(); recalculate(); });
    row.querySelector('.product-select').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            row.querySelector('.line-desc').value = opt.dataset.name;
            row.querySelector('.line-price').value = parseFloat(opt.dataset.price).toFixed(2);
            recalculate();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.line-item-row').forEach(bindRow);
    document.getElementById('addLineBtn').addEventListener('click', addLine);
    document.getElementById('discountInput').addEventListener('input', recalculate);
    document.getElementById('taxRateInput').addEventListener('input', recalculate);
    recalculate();
});
