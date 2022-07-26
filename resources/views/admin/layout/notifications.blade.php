<button type="button" @click="closeToast()" x-data="app" x-show="open" x-transition.duration.300ms class="fixed top-4 right-4 z-50 rounded-md bg-green-500 px-4 py-2 text-white transition hover:bg-green-600">
      <div class="flex items-center space-x-2">
        <span class="text-3xl"><i class="bx bx-check"></i></span>
        <p class="font-bold">Item Created Successfully!</p>
      </div>
</button>
<script>
    let timertoast;
    document.addEventListener("alpine:init", () => {
        Alpine.data("app", () => ({
            open: true,
            openToast() {
                if (this.open) return;
                this.open = true;
                // reset time to 0 second
                clearTimeout(timertoast);

                // auto close toast after 10 seconds
                timertoast = setTimeout(() => {
                    this.open = false;
                }, 10000);
            },

            closeToast() {
                this.open = false;
            },
        }));
    });
</script>