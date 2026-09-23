function seatMapApp() {
    return {
        selectedSeatId: null,
        activeModalData: null,
        searchQuery: (window.__seatMapData && window.__seatMapData.searchQuery) ? window.__seatMapData.searchQuery : '',
        graduatesList: (window.__seatMapData && Array.isArray(window.__seatMapData.graduatesList)) ? window.__seatMapData.graduatesList : [],
        searchedSeatIdClicked: null,
        filteredGraduates: [],
        searchedSeatIds: new Set(), // Memakai Set untuk O(1) lookup di Alpine x-bind class

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

            if (!q || !Array.isArray(this.graduatesList)) {
                this.filteredGraduates = [];
                this.searchedSeatIds = new Set();
                return;
            }

            const matchedGrads = [];
            const matchedSeatIds = new Set();

            for (let i = 0; i < this.graduatesList.length; i++) {
                const g = this.graduatesList[i];
                if (!g) continue;

                const nameMatch = g.name && g.name.toString().toLowerCase().includes(q);
                const nrpMatch = g.nrp && g.nrp.toString().toLowerCase().includes(q);
                const seatMatch = g.seat_code && g.seat_code.toString().toLowerCase().includes(q);

                if (nameMatch || nrpMatch || seatMatch) {
                    matchedGrads.push(g);
                    if (g.seat_id) {
                        matchedSeatIds.add(Number(g.seat_id));
                    }
                }
            }

            this.filteredGraduates = matchedGrads;
            this.searchedSeatIds = matchedSeatIds;
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

            this.selectedSeatId = (this.selectedSeatId === targetId) ? null : targetId;
            this.activeModalData = this.selectedSeatId ? gradData : null;
        },

        focusSeat(seatId, data) {
            if (!seatId) return;
            const targetId = Number(seatId);

            this.selectedSeatId = targetId;
            this.searchedSeatIdClicked = targetId;
            this.searchedSeatIds = new Set([targetId]);

            this.$nextTick(() => {
                const el = document.getElementById(`seat-${targetId}`);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
                }
            });
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
        },

        // Helper untuk Alpine.js template (O(1) Instant Check)
        isSeatSearched(seatId) {
            return this.searchedSeatIds.has(Number(seatId));
        }
    };
}