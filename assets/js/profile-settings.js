let avatarCropper = null;
let bgCropper = null;
let croppingInProgress = false;

// Open cropper with existing image or file input
function openCropper(type, useCurrent = false) {
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
    
    // Устанавливаем класс для правильного отображения аватара
    if (type === 'avatar') {
        cropImage.classList.add('round-avatar');
    } else {
        cropImage.classList.remove('round-avatar');
    }
    
    cropImage.src = src;
    
    // Уничтожаем предыдущий кроппер, если он существует
    if (type === 'avatar' && avatarCropper) {
        avatarCropper.destroy();
        avatarCropper = null;
    } else if (type === 'bg' && bgCropper) {
        bgCropper.destroy();
        bgCropper = null;
    }
    
    // Создаем новый кроппер в зависимости от типа
    if (type === 'avatar') {
        avatarCropper = new Cropper(cropImage, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            checkCrossOrigin: false,
            zoomable: true,
            movable: true,
            rotatable: false,
            scalable: false
        });
    } else {
        bgCropper = new Cropper(cropImage, {
            aspectRatio: 4,
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            checkCrossOrigin: false,
            zoomable: true,
            movable: true,
            rotatable: false,
            scalable: false
        });
    }
    
    // Сохраняем тип текущего кроппера
    cropImage.setAttribute('data-type', type);
    croppingInProgress = true;
}

function closeCropper() {
    const cropImage = document.getElementById('cropperImage');
    const type = cropImage.getAttribute('data-type');
    
    // Уничтожаем соответствующий кроппер
    if (type === 'avatar' && avatarCropper) {
        avatarCropper.destroy();
        avatarCropper = null;
    } else if (type === 'bg' && bgCropper) {
        bgCropper.destroy();
        bgCropper = null;
    }
    
    document.getElementById('cropModal').style.display = 'none';
    croppingInProgress = false;
}

function applyCrop() {
    const cropImage = document.getElementById('cropperImage');
    const type = cropImage.getAttribute('data-type');
    
    let cropperInstance = null;
    if (type === 'avatar') {
        cropperInstance = avatarCropper;
    } else if (type === 'bg') {
        cropperInstance = bgCropper;
    }
    
    if (!cropperInstance) return;
    
    cropperInstance.getCroppedCanvas().toBlob(blob => {
        const file = new File([blob], type + '.png', { type: 'image/png' });
        const data = new DataTransfer();
        data.items.add(file);
        const input = document.getElementById(type + 'Input');
        input.files = data.files;

        const imgURL = URL.createObjectURL(blob);
        const preview = document.getElementById(type + 'Image');
        preview.src = imgURL;

        document.getElementById('remove_' + type).value = '0';

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
