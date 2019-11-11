function runReload() {
    setInterval('refreshPage()', 5000);
}

function refreshPage() {
    location.reload();
}

var url = window.location.href;

if (url.includes('disabled')) {
    runReload();
}
