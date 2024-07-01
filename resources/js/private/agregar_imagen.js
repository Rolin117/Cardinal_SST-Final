document.getElementById('addImageIcon').addEventListener('click', function () {
    document.getElementById('inputGroupFile01').click(); 
});

document.getElementById('inputGroupFile01').addEventListener('change', function (event) {
    if (event.target.files && event.target.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var output = document.getElementById('imagePreview');
            var placeholderIcon = document.getElementById('addImageIcon');
            output.src = e.target.result;
            output.style.display = 'block';
            placeholderIcon.style.display = 'none'; 
        };
        reader.readAsDataURL(event.target.files[0]);
    }
});
