function raffleApp() {
    return {
        selectedFormId: '',
        selectedPrizeId: '',
        candidates: [],
        prizes: [],
        stats: {
            totalCandidates: 0,
            totalPrizes: 0,
            availableCandidates: 0,
            availablePrizes: 0
        },
        isLoading: false,
        isSpinning: false,
        isFullscreen: false,
        currentDisplayName: '---- ----',
        winner: null,
        animationInterval: null,

        init() {
            document.addEventListener('fullscreenchange', () => {
                this.isFullscreen = !!document.fullscreenElement;
            });
        },

        toggleFullscreen() {
            const elem = document.getElementById('raffle-container');
            if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        },

        fetchData() {
            if (!this.selectedFormId) {
                this.resetState();
                return;
            }

            this.isLoading = true;
            this.resetState();

            fetch(`/admin/forms/${this.selectedFormId}/candidates`)
                .then(response => response.json())
                .then(data => {
                    this.candidates = data.candidates;
                    this.prizes = data.prizes;
                    this.stats = data.stats || {
                        totalCandidates: data.candidates.length,
                        totalPrizes: data.prizes.length,
                        availableCandidates: data.candidates.length,
                        availablePrizes: data.prizes.length
                    };

                    if (this.prizes.length > 0) {
                        this.selectedPrizeId = this.prizes[0].id;
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    alert('Gagal memuat data undian.');
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        resetState() {
            this.candidates = [];
            this.prizes = [];
            this.selectedPrizeId = '';
            this.stats = {
                totalCandidates: 0,
                totalPrizes: 0,
                availableCandidates: 0,
                availablePrizes: 0
            };
            this.resetRaffle();
        },

        resetRaffle() {
            this.winner = null;
            this.currentDisplayName = '---- ----';
            this.isSpinning = false;
            if (this.animationInterval) clearInterval(this.animationInterval);
        },

        getSelectedPrizeName() {
            const prize = this.prizes.find(p => p.id == this.selectedPrizeId);
            return prize ? prize.name : null;
        },

        isValidToPlay() {
            return this.selectedFormId && this.selectedPrizeId && this.candidates.length > 0;
        },

        getRandomNames(count) {

            if (this.candidates.length === 0) return [];
            let names = [];
            for (let i = 0; i < count; i++) {
                names.push(this.candidates[Math.floor(Math.random() * this.candidates.length)].name);
            }
            return names;
        },

        startRaffle() {
            if (!this.isValidToPlay()) return;

            this.isSpinning = true;
            this.winner = null;


            this.animationInterval = setInterval(() => {
                const randomIndex = Math.floor(Math.random() * this.candidates.length);

                this.currentDisplayName = this.candidates[randomIndex].name;
            }, 80);
        },

        stopRaffle() {
            if (!this.isSpinning) return;

            clearInterval(this.animationInterval);


            const randomIndex = Math.floor(Math.random() * this.candidates.length);
            this.winner = this.candidates[randomIndex];
            this.currentDisplayName = this.winner.name;
            this.isSpinning = false;


            this.fireConfetti();
        },

        fireConfetti() {
            var duration = 3 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

            function randomInOut(min, max) {
                return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function () {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInOut(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInOut(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        }
    }
}
