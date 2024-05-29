"use strict";

/**
 * @param {HTMLFormElement} element
 */
function sort(element) {
    const search = new URLSearchParams(location.search);
    search.set("sort", element.value);
    location.search = search.toString();
}

function dismissModal() {
    document.getElementById("modal")?.classList.remove("show-modal");
}
