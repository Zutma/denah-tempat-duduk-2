// komponan alpine buat ngatur form generator baris kursi
function seatRowManager(config = {}) {
    return {
        showBulkForm: config.hasErrors || false,
        allLetters: config.allLetters || [],
        usedLetters: config.usedLetters || [],
        rows: [],
        init() {
            if (this.showBulkForm && this.rows.length === 0) this.addRow();
        },
        toggleBulkForm() {
            this.showBulkForm = !this.showBulkForm;
            if (this.showBulkForm && this.rows.length === 0) this.addRow();
        },
        cancelBulkForm() {
            this.showBulkForm = false;
            this.rows = [];
        },
        addRow() {
            // cari abjad berikutnya yang belum dipakai
            let lastRow = this.rows.length > 0 ? this.rows[this.rows.length - 1].row : '';
            let currentIndex = lastRow ? this.allLetters.indexOf(lastRow) : -1;
            let nextLetter = '';
            for (let i = currentIndex + 1; i < this.allLetters.length; i++) {
                let letter = this.allLetters[i];
                if (!this.usedLetters.includes(letter) && !this.rows.some(r => r.row === letter)) {
                    nextLetter = letter;
                    break;
                }
            }
            if (!nextLetter) {
                nextLetter = this.allLetters.find(l => !this.usedLetters.includes(l) && !this.rows.some(r => r.row === l)) || 'A';
            }
            this.rows.push({ row: nextLetter, left_capacity: 20, right_capacity: 20 });
        },
        removeRow(index) {
            this.rows.splice(index, 1);
            if (this.rows.length === 0) this.showBulkForm = false;
        }
    };
}

// listener filter & checkbox tabel baris
function initSeatRowSearch() {
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
    const bulkToolbar = document.getElementById('bulkToolbar');
    const selectedCount = document.getElementById('selectedCount');
    const searchInput = document.getElementById('searchInput');

    // pencarian baris realtime
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            document.querySelectorAll('.seat-row-item').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }

    // toggle toolbar hapus masal
    window.updateToolbarState = function() {
        const checkedCount = document.querySelectorAll('.rowCheckbox:checked').length;
        if (bulkToolbar) {
            bulkToolbar.classList.toggle('hidden', checkedCount === 0);
            bulkToolbar.classList.toggle('flex', checkedCount > 0);
        }
        if (selectedCount) selectedCount.textContent = checkedCount;
    };

    window.unselectAll = function() {
        if (selectAll) selectAll.checked = false;
        rowCheckboxes.forEach(cb => cb.checked = false);
        window.updateToolbarState();
    };

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            window.updateToolbarState();
        });
        rowCheckboxes.forEach(cb => cb.addEventListener('change', window.updateToolbarState));
    }
}

// submit hapus masal baris terpilih
function submitSeatRowBulkDelete() {
    const checked = document.querySelectorAll('.rowCheckbox:checked');
    if (checked.length === 0) return;
    if (confirm(`Yakin mau hapus ${checked.length} baris kursi terpilih?`)) {
        const form = document.getElementById('bulkDeleteForm');
        form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
        checked.forEach(cb => {
            const ids = cb.value.split(',');
            ids.forEach(id => {
                if (id.trim() !== '') {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id.trim();
                    form.appendChild(input);
                }
            });
        });
        form.submit();
    }
}

