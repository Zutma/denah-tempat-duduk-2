function seatMapApp() {
    return {
        selectedSeatId: null,
        activeModalData: null,
        searchQuery: window.__seatMapData?.searchQuery || '',
        graduatesList: window.__seatMapData?.graduatesList || [],
        searchedSeatIdClicked: null,

        get filteredGraduates() {
            const q = this.searchQuery.trim().toLowerCase();
            if (!q) return [];
            return this.graduatesList.filter(g => {
                return (g.name && g.name.toLowerCase().includes(q))
                    || (g.nrp && g.nrp.toLowerCase().includes(q))
                    || (g.seat_code && g.seat_code.toLowerCase().includes(q));
            });
        },

        get searchedSeatIds() {
            if (this.searchedSeatIdClicked) return [this.searchedSeatIdClicked];
            if (!this.searchQuery.trim()) return [];
            return this.filteredGraduates.map(g => g.seat_id);
        },

        selectSeat(seatId, data) {
            if (!data) return;
            this.selectedSeatId = (this.selectedSeatId === seatId) ? null : seatId;
            this.activeModalData = this.selectedSeatId ? data : null;
        },

        focusSeat(seatId, data) {
            if (!seatId) return;
            this.selectedSeatId = seatId;
            this.activeModalData = data;
            this.searchedSeatIdClicked = seatId;
            this.$nextTick(() => {
                const el = document.getElementById(`seat-${seatId}`);
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
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
        }
    }
}