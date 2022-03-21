require('./bootstrap');
require('alpinejs');

function livewire_init() {
    const tooltips = $('[data-toggle="tooltip"]');
    tooltips.tooltip('dispose');
    tooltips.tooltip({
        placement: 'top',
        boundary: 'window',
    });
}

document.addEventListener('livewire:load', function () {
    livewire_init();

    document.addEventListener('livewire:update', function () {
        livewire_init();
    })
})

