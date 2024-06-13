function changeSrc() {
    let image = document.getElementById('open-nav');
    if (image.src.includes('burgerClose.png')) {
        image.src = '../img/burgerOpen.png';
    } else {
        image.src = '../img/burgerClose.png';
    }
}

function classChange(class1, class2, elementId) {
    let element = document.getElementById(elementId);
    if (element.classList.contains(class1)) {
        element.classList.replace(class1, class2);
    } else {
        element.classList.replace(class2, class1);
    }
}
