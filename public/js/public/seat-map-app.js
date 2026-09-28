function seatMapApp() {
    return {
        selectedSeatId: null,
        activeModalData: null,
        searchQuery: (window.__seatMapData && window.__seatMapData.searchQuery) ? window.__seatMapData.searchQuery : '',
        graduatesList: (window.__seatMapData && Array.isArray(window.__seatMapData.graduatesList)) ? window.__seatMapData.graduatesList : [],
        searchedSeatIdClicked: null,
        filteredGraduates: [],
        searchedSeatIds: new Set(), // Memakai Set untuk O(1) lookup di Alpine x-bind class
        showDropdown: false,

        // State Infinite Scroll langsung di Dropdown
        dropdownLimit: 10,

        gradMap: null,
        debounceTimer: null,

        init() {
            // 1. Build Index Map O(1) sekali di awal
            this.buildGradMap();

            // 2. Watcher dengan Debounce (Menunggu ketikan selesai 150ms agar tidak lag)
            this.$watch('searchQuery', (val) => {
                clearTimeout(this.debounceTimer);
                this.searchedSeatIdClicked = null;

                this.debounceTimer = setTimeout(() => {
                    this.executeSearch(val);
                }, 150); // Delay optimal agar pengetikan terasa sangat responsif
            });

            // 3. Filter awal jika ada query bawaan dari URL/backend
            if (this.searchQuery.trim() !== '') {
                this.$nextTick(() => {
                    this.executeSearch(this.searchQuery);
                });
            }
        },

        buildGradMap() {
            // Menggunakan Map native JS untuk alokasi memori dan lookup tercepat
            this.gradMap = new Map();
            if (Array.isArray(this.graduatesList)) {
                for (let i = 0; i < this.graduatesList.length; i++) {
                    const g = this.graduatesList[i];
                    if (g && g.seat_id) {
                        this.gradMap.set(Number(g.seat_id), g);
                    }
                }
            }
        },

        executeSearch(query) {
            const q = (query || '').toString().trim().toLowerCase();

            // Reset limit dropdown ke 10 item awal untuk batch highlight pertama
            this.dropdownLimit = 10;

            // Batasi minimal 2 karakter agar tidak memuat ratusan data sampah saat baru mengetik 1 huruf
            if (!q || q.length < 2 || !Array.isArray(this.graduatesList)) {
                this.filteredGraduates = [];
                this.searchedSeatIds = new Set();
                this.showDropdown = false;
                return;
            }

            const primaryMatches = [];   // Cocok Nama & NRP (Prioritas Utama Teratas)
            const secondaryMatches = []; // Cocok Kode Kursi, Prodi, dan Fakultas

            for (let i = 0; i < this.graduatesList.length; i++) {
                const g = this.graduatesList[i];
                if (!g) continue;

                const nameMatch = g.name && g.name.toString().toLowerCase().includes(q);
                const nrpMatch = g.nrp && g.nrp.toString().toLowerCase().includes(q);
                const seatMatch = g.seat_code && g.seat_code.toString().toLowerCase().includes(q);
                const prodiMatch = g.prodi && g.prodi.toString().toLowerCase().includes(q);
                const facultyMatch = (g.faculty && g.faculty.toString().toLowerCase().includes(q)) ||
                                     (g.faculty_name && g.faculty_name.toString().toLowerCase().includes(q));

                if (nameMatch || nrpMatch) {
                    primaryMatches.push(g);
                } else if (seatMatch || prodiMatch || facultyMatch) {
                    secondaryMatches.push(g);
                }
            }

            // Gabungkan dengan prioritas utama (Nama & NRP) di urutan paling atas
            this.filteredGraduates = [...primaryMatches, ...secondaryMatches];
            // Batch-first highlight: Hanya highlight seat yang tampil di batch awal dropdown
            this.updateSearchedSeatIds();
            this.showDropdown = true;
        },

        toTitleCase(str) {
            if (!str) return '';
            return str.toString().toLowerCase().replace(/(?:^|\s|-|\/)\S/g, function(m) {
                return m.toUpperCase();
            });
        },

        updateSearchedSeatIds() {
            const visibleGrads = this.filteredGraduates.slice(0, this.dropdownLimit);
            const matchedSeatIds = new Set();
            for (let i = 0; i < visibleGrads.length; i++) {
                if (visibleGrads[i].seat_id) {
                    matchedSeatIds.add(Number(visibleGrads[i].seat_id));
                }
            }
            this.searchedSeatIds = matchedSeatIds;
        },

        handleDropdownScroll(e) {
            const target = e.target;
            // Jika scroll pengguna mendekati 30px dari bawah dropdown, muat 10 data & highlight tambahan (Lazy-load highlight)
            if (target.scrollTop + target.clientHeight >= target.scrollHeight - 30) {
                if (this.dropdownLimit < this.filteredGraduates.length) {
                    this.dropdownLimit += 10;
                    this.updateSearchedSeatIds();
                }
            }
        },

        getGradBySeatId(seatId) {
            if (!this.gradMap) this.buildGradMap();
            return this.gradMap.get(Number(seatId)) || null;
        },

        selectSeat(seatId) {
            const targetId = Number(seatId);
            if (!targetId) return;

            const gradData = this.getGradBySeatId(targetId);
            if (!gradData) return; // Kursi kosong

            // Jika mengeklik kursi baru / kursi hasil pencarian, langsung buka modalnya dalam 1x klik
            if (this.selectedSeatId !== targetId || !this.activeModalData) {
                this.selectedSeatId = targetId;
                this.activeModalData = gradData;
            } else {
                // Jika mengeklik kursi yang sudah terbuka modalnya secara persis, baru lakukan toggle (tutup)
                this.selectedSeatId = null;
                this.activeModalData = null;
            }
        },

        focusSeat(seatId, data) {
            if (!seatId) return;
            const targetId = Number(seatId);

            this.selectedSeatId = targetId;
            this.searchedSeatIdClicked = targetId;
            this.searchedSeatIds = new Set([targetId]);
            
            // SOLUSI: Tutup dropdown secara otomatis saat item diklik agar denah tempat duduk tidak pernah terhalang!
            this.showDropdown = false;

            this.$nextTick(() => {
                const container = document.getElementById('denahContainer');
                const seat = document.getElementById(`seat-${targetId}`);

                if (container && seat) {
                    const containerRect = container.getBoundingClientRect();
                    const seatRect = seat.getBoundingClientRect();

                    // Header Offset aman (~120px) agar posisi kursi di-scroll persis di bawah header
                    const headerOffset = 120;

                    const newScrollTop = container.scrollTop + (seatRect.top - containerRect.top) - headerOffset;
                    const newScrollLeft = container.scrollLeft + (seatRect.left - containerRect.left) - (containerRect.width / 2) + (seatRect.width / 2);

                    container.scrollTo({
                        top: Math.max(0, newScrollTop),
                        left: Math.max(0, newScrollLeft),
                        behavior: 'smooth'
                    });
                }
            });
        },

        handleFocus() {
            if (this.searchQuery.trim() !== '') {
                this.showDropdown = true;
            }
        },

        closeModal() {
            this.activeModalData = null;
            this.selectedSeatId = null;
        },

        clearSelection() {
            this.selectedSeatId = null;
            this.activeModalData = null;
            this.searchQuery = '';
            this.searchedSeatIdClicked = null;
            this.filteredGraduates = [];
            this.searchedSeatIds = new Set();
            this.showDropdown = false;
        },

        // Helper untuk Alpine.js template (O(1) Instant Check)
        isSeatSearched(seatId) {
            return this.searchedSeatIds.has(Number(seatId));
        }
    };
}