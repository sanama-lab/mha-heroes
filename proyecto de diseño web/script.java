// Obtiene todas las imágenes con la clase 'image-gray'
const images = document.querySelectorAll('.image-gray');

images.forEach(img => {
    img.addEventListener('click', () => {
        
        img.classList.toggle('clicked');
    });
});



.image-gray.clicked {
    border: 5px solid #555;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    opacity: 0.8;
}