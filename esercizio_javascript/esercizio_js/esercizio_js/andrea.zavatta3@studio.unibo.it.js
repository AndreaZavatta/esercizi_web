let images = document.querySelectorAll('.slider .slider-image img');
images.forEach(e => e.onclick = () => {
    images.forEach(el => el.classList.remove('current'));
    e.classList.add('current');
    images.forEach(el =>el.style.visibility = "hidden");
    e.style.visibility = "visible";
    e.previousElementSibling ? e.previousElementSibling.style.visibility = "visible": null;
    e.nextElementSibling ? e.nextElementSibling.style.visibility = "visible": null;
});
images[0].click();
