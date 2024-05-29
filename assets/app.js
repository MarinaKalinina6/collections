import './bootstrap.js';
import 'bootstrap';

import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';
import './collection.js';

const darkModeSwitchInput = document.querySelector('input#darkModeSwitch');
const bodyTag = document.querySelector('[data-tag="all"]');

function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
}

const themeSwitch = () => {
    const currentState = bodyTag.getAttribute('data-bs-theme');

    switch (currentState) {
        case "light":
            bodyTag.setAttribute('data-bs-theme', "dark");
            setCookie('theme', 'dark', 30);
            break;
        default:
            bodyTag.setAttribute('data-bs-theme', "light");
            setCookie('theme', 'light', 30);
    }
};
darkModeSwitchInput.addEventListener('change', themeSwitch)

window.addEventListener('load', () => {
    const savedTheme = getCookie('theme');
    if (savedTheme) {
        bodyTag.setAttribute('data-bs-theme', savedTheme);
    }
});

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}







