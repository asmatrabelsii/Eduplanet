var video = document.getElementById("myVideo");
if (localStorage.getItem('lastPlayedTime') !== null) {
    var lastPlayedTime = localStorage.getItem('lastPlayedTime');
    video.currentTime = lastPlayedTime;
}
video.addEventListener('pause', function () {
    localStorage.setItem('lastPlayedTime', video.currentTime);
});
video.addEventListener('ended', function () {
    localStorage.setItem('lastPlayedTime', 0);
});