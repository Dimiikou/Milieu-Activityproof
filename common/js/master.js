// navbar color to black on scroll
window.onscroll = function() {
    if (document.body.scrollTop > 0 || document.documentElement.scrollTop > 0) {
        document.getElementById("nav").style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    } else {
        document.getElementById("nav").style.backgroundColor = "transparent";
    }
};