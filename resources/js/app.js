import './bootstrap';
import './main';
import './landing';
import Alpine from 'alpinejs';
import.meta.glob([
    '../img/**',
    '../fonts/**',
]);

window.Alpine = Alpine;

Alpine.start();
