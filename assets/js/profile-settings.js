let cropper = null;
let currentTarget = null;
let croppingInProgress = false;

// Open cropper with existing image or file input
function openCropper(type, useCurrent = false) {
    currentTarget = type;
    const input = document.getElementById(type + 'Input');
    const imgElement = document.getElementById(type + 'Image');
    const defaultSrc = type === 'avatar' ? '/uploads/default-avatar.png' : '/uploads/default-bg.jpg';

    if (useCurrent) {
        const src = imgElement.src;
        if (src && src !== window.location.origin + defaultSrc) {
            showCropper(src, type);
        } else {
            alert('Нет изображения для обрезки.');
        }
    } else {
        input.click();
    }
}

// Handle file selection
document.getElementById('avatarInput').addEventListener('change', e => loadAndCrop(e, 'avatar'));
document.getElementById('bgInput').addEventListener('change', e => loadAndCrop(e, 'bg'));

function loadAndCrop(event, type) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => showCropper(e.target.result, type);
    reader.readAsDataURL(file);
}

function showCropper(src, type) {
    const modal = document.getElementById('cropModal');
    const cropImage = document.getElementById('cropperImage');
    modal.style.display = 'block';
    cropImage.src = src;
    cropper = new Cropper(cropImage, {
        aspectRatio: type === 'avatar' ? 1 : 4,
        viewMode: 1,
    });
    croppingInProgress = true;
}

function closeCropper() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    document.getElementById('cropModal').style.display = 'none';
    croppingInProgress = false;
}

function applyCrop() {
    if (!cropper || !currentTarget) return;
    cropper.getCroppedCanvas().toBlob(blob => {
        const file = new File([blob], currentTarget + '.png', { type: 'image/png' });
        const data = new DataTransfer();
        data.items.add(file);
        const input = document.getElementById(currentTarget + 'Input');
        input.files = data.files;

        const imgURL = URL.createObjectURL(blob);
        const preview = document.getElementById(currentTarget + 'Image');
        preview.src = imgURL;

        document.getElementById('remove_' + currentTarget).value = '0';

        closeCropper();
    }, 'image/png');
}

// Remove image and reset to default
function removeImage(type) {
    const img = document.getElementById(type + 'Image');
    const defaultSrc = type === 'avatar' ? '/uploads/default-avatar.png' : '/uploads/default-bg.jpg';
    img.src = defaultSrc;
    document.getElementById(type + 'Input').value = '';
    document.getElementById('remove_' + type).value = '1';
}

// Form submission spinner
document.getElementById('profileForm').addEventListener('submit', e => {
    if (croppingInProgress) {
        e.preventDefault();
        alert('Дождитесь завершения обрезки.');
        return;
    }
    document.getElementById('upload-spinner').style.display = 'block';
});
