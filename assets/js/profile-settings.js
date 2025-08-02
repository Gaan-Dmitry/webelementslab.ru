let cropper = null;
let currentTarget = null;

function openCropper(type, useCurrent = false) {
	currentTarget = type;
	const input = document.getElementById(type + 'Input');

	// Если обрезаем уже выбранное изображение (без выбора нового файла)
	if (useCurrent) {
		const img = document.getElementById(type + 'Image');
		const src = img?.src;

		if (src && !src.includes('placeholder')) {
			document.getElementById('cropperImage').src = src;
			document.getElementById('cropModal').style.display = 'flex';
			cropper = new Cropper(document.getElementById('cropperImage'), {
				aspectRatio: type === 'avatar' ? 1 : 4 / 1,
				viewMode: 1,
			});
		} else {
			alert('Нет изображения для обрезки.');
		}
		return;
	}

	// Иначе — загружаем новый файл
	input.click();

	input.onchange = function () {
		if (this.files && this.files[0]) {
			const reader = new FileReader();
			reader.onload = function (e) {
				document.getElementById('cropperImage').src = e.target.result;
				document.getElementById('cropModal').style.display = 'flex';
				cropper = new Cropper(document.getElementById('cropperImage'), {
					aspectRatio: type === 'avatar' ? 1 : 4 / 1,
					viewMode: 1,
				});
			};
			reader.readAsDataURL(this.files[0]);
		}
	};
}

function closeCropper() {
	if (cropper) {
		cropper.destroy();
		cropper = null;
	}
	document.getElementById('cropModal').style.display = 'none';
}

function applyCrop() {
	if (cropper && currentTarget) {
		closeCropper(); // Скрываем модалку сразу

		cropper.getCroppedCanvas().toBlob(blob => {
			const file = new File([blob], `${currentTarget}.png`, {
				type: 'image/png',
			});
			const dataTransfer = new DataTransfer();
			dataTransfer.items.add(file);
			const input = document.getElementById(currentTarget + 'Input');
			input.files = dataTransfer.files;

			const imgURL = URL.createObjectURL(blob);
			if (currentTarget === 'avatar') {
				document.getElementById('avatarImage').src = imgURL;
				document.getElementById(
					'avatarPreview'
				).innerHTML = `<img src="${imgURL}" alt="avatar">`;
				document.getElementById('avatarFileName').textContent =
					file.name;
				document.getElementById('remove_avatar').value = '0';
			} else {
				document.getElementById('bgImage').src = imgURL;
				document.getElementById(
					'bgPreview'
				).innerHTML = `<img src="${imgURL}" alt="bg">`;
				document.getElementById('bgFileName').textContent = file.name;
				document.getElementById('remove_bg').value = '0';
			}
		}, 'image/png');
	}
}

function removeImage(type) {
	if (type === 'avatar') {
		const defaultAvatar = '/uploads/default-avatar.png';
		document.getElementById('avatarImage').src = defaultAvatar;
		document.getElementById('avatarPreview').innerHTML = `<img src="${defaultAvatar}" alt="avatar">`;
		document.getElementById('avatarFileName').textContent = '/uploads/default-avatar.png';
		document.getElementById('avatarInput').value = '';
		document.getElementById('remove_avatar').value = '1';
	} else {
		const defaultBg = '/uploads/default-bg.jpg';
		document.getElementById('bgImage').src = defaultBg;
		document.getElementById('bgPreview').innerHTML = `<img src="${defaultBg}" alt="bg">`;
		document.getElementById('bgFileName').textContent = '/uploads/default-bg.jpg';
		document.getElementById('bgInput').value = '';
		document.getElementById('remove_bg').value = '1';
	}
}


document.addEventListener('DOMContentLoaded', () => {
	const avatarInput = document.getElementById('avatarInput');
	const bgInput = document.getElementById('bgInput');
	const spinner = document.getElementById('upload-spinner');
	const saveBtn = document.getElementById('save-btn');

	avatarInput.addEventListener('change', () => {
		const file = avatarInput.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = e => {
				document.getElementById(
					'avatarPreview'
				).innerHTML = `<img src="${e.target.result}" alt="avatar">`;
				document.getElementById('avatarFileName').textContent =
					file.name;
				document.getElementById('remove_avatar').value = '0';
			};
			reader.readAsDataURL(file);
		}
	});

	bgInput.addEventListener('change', () => {
		const file = bgInput.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = e => {
				document.getElementById(
					'bgPreview'
				).innerHTML = `<img src="${e.target.result}" alt="bg">`;
				document.getElementById('bgFileName').textContent = file.name;
				document.getElementById('remove_bg').value = '0';
			};
			reader.readAsDataURL(file);
		}
	});

	const form = document.getElementById('profileForm');
	form.addEventListener('submit', () => {
		spinner.style.display = 'block';
		saveBtn.disabled = true;
	});
});
