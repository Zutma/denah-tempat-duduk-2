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

            const centerSeat = () => {
                const container = document.getElementById('denahContainer');
                const seat = document.getElementById(`seat-${targetId}`);

                if (container && seat) {
                    const containerRect = container.getBoundingClientRect();
                    const seatRect = seat.getBoundingClientRect();

                    // Hitung pergeseran scroll yang tepat agar titik pusat kursi berada persis di pusat visual viewport
                    const seatCenterX = seatRect.left + (seatRect.width / 2);
                    const seatCenterY = seatRect.top + (seatRect.height / 2);

                    const containerCenterX = containerRect.left + (containerRect.width / 2);
                    const containerCenterY = containerRect.top + (containerRect.height / 2);

                    const diffX = seatCenterX - containerCenterX;
                    const diffY = seatCenterY - containerCenterY;

                    const newScrollLeft = container.scrollLeft + diffX;
                    const newScrollTop = container.scrollTop + diffY;

                    container.scrollTo({
                        top: Math.max(0, newScrollTop),
                        left: Math.max(0, newScrollLeft),
                        behavior: 'smooth'
                    });
                }
            };

            this.$nextTick(() => {
                const currentZoom = (window.getZoomScale && typeof window.getZoomScale === 'function') 
                    ? window.getZoomScale() 
                    : 1.0;

                // Batas Minimum Zoom (120% / 1.2)
                const TARGET_MIN_ZOOM = 1.2;

                if (currentZoom < TARGET_MIN_ZOOM && window.setZoomScale && typeof window.setZoomScale === 'function') {
                    // Zoom-in otomatis ke 120% dengan callback smooth centering setelah zoom selesai
                    window.setZoomScale(TARGET_MIN_ZOOM, () => {
                        setTimeout(centerSeat, 50);
                    });
                } else {
                    // Jika zoom sudah >= 120%, pertahankan zoom aktif dan langsung centering
                    centerSeat();
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
        },
        
        formatSeatDisplay(data) {
            if (!data) return '-';
            
            // Jika data sudah berisi properti terpisah
            const row = data.row || data.seat_code?.charAt(0) || '';
            const side = data.side || (data.side_type === 'left' ? 'Kiri' : 'Kanan');
            const number = data.seat_number || data.seat_code?.replace(/[^0-9]/g, '') || '';

            return `${row} - ${side} - ${number}`;
        }
    };
}