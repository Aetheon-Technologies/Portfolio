/**
 * main.js — Portfolio entry point
 * Imports and initialises all feature modules.
 */
import { initNav }           from './modules/nav.js';
import { initScrollReveal }  from './modules/scroll-reveal.js';
import { initProjectFilter } from './modules/project-filter.js';
import { initTypewriter }    from './modules/typewriter.js';
import { initContactForm }   from './modules/contact-form.js';

document.addEventListener('DOMContentLoaded', () => {
    initNav();
    initScrollReveal();
    initProjectFilter();
    initTypewriter();
    initContactForm();
});
