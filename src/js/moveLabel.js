function moveLabel() {
    var elem = document.getElementById("animate");
    var pos = 0;
    var id = setInterval(frame, 20);
    function frame() {
        if (pos == 300) {
            clearInterval(id);
        } else {
            pos++;
            elem.style.top = pos + 'px';
            elem.style.left = pos + 'px';
        }
    }
}