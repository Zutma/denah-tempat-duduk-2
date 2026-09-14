function seatMapApp() {
    return {
        selectedSeatId: null,
        activeModalData: null,
        searchQuery: (window.__seatMapData && window.__seatMapData.searchQuery) ? window.__seatMapData.searchQuery : '',
        graduatesList: (window.__seatMapData && Array.isArray(window.__seatMapData.graduatesList)) ? window.__seatMapData.graduatesList : [],
        searchedSeatIdClicked: null,
        filteredGraduates: [],
        searchedSeatIds: [],

        init() {
            // Watcher cerdas: Hanya menghitung ulang hasil pencarian jika ketikan berubah (Bebas Lag 100%)
            this.$watch('searchQuery', (val) => {
                this.searchedSeatIdClicked = null;
                const q = (val || '').toString().trim().toLowerCase();

                if (!q || !Array.isArray(this.graduatesList)) {
                    this.filteredGraduates = [];
                    this.searchedSeatIds = [];
                    return;
                }

                // Filter data wisudawan
                this.filteredGraduates = this.graduatesList.filter(g => {
                    if (!g) return false;
                    const nameMatch = g.name && g.name.toString().toLowerCase().includes(q);
                    const nrpMatch = g.nrp && g.nrp.toString().toLowerCase().includes(q);
                    const seatMatch = g.seat_code && g.seat_code.toString().toLowerCase().includes(q);
                    return nameMatch || nrpMatch || seatMatch;
                });

                // Set array ID kursi yang di-highlight
                this.searchedSeatIds = this.filteredGraduates.map(g => Number(g.seat_id)).filter(Boolean);
            });

            // Trigger pencarian pertama kali jika ada query awal dari URL/backend
            if (this.searchQuery.trim() !== '') {
                this.$nextTick(() => {
                    const q = this.searchQuery.trim().toLowerCase();
                    this.filteredGraduates = this.graduatesList.filter(g => {
                        return (g.name && g.name.toString().toLowerCase().includes(q))
                            || (g.nrp && g.nrp.toString().toLowerCase().includes(q))
                            || (g.seat_code && g.seat_code.toString().toLowerCase().includes(q));
                    });
                    this.searchedSeatIds = this.filteredGraduates.map(g => Number(g.seat_id)).filter(Boolean);
                });
            }
        },

        selectSeat(seatId, data) {
            if (!data) return;
            const targetId = Number(seatId);
            this.selectedSeatId = (this.selectedSeatId === targetId) ? null : targetId;
            this.activeModalData = this.selectedSeatId ? data : null;
        },

        focusSeat(seatId, data) {
            if (!seatId) return;
            const targetId = Number(seatId);
            this.selectedSeatId = targetId;
            this.activeModalData = data;
            this.searchedSeatIdClicked = targetId;
            this.searchedSeatIds = [targetId];

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
            this.searchedSeatIds = [];
        }
    };
}