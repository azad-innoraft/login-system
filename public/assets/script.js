function showError(input, message) {
    let error = input.parentElement.querySelector('.client-error');

    if (!error) {
        error = document.createElement('small');
        error.className = 'client-error';
        input.parentElement.appendChild(error);
    }

    error.textContent = message;
}

function clearErrors(form) {
    form.querySelectorAll('.client-error').forEach(function(error) {
        error.remove();
    });
}

function isEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function(event) {
        clearErrors(form);
        let valid = true;

        form.querySelectorAll('[required]').forEach(function(input) {
            if (!input.value.trim()) {
                showError(input, 'This field is required!');
                valid = false;
            }
        });

        form.querySelectorAll('input[type="email"]').forEach(function(input) {
            if (input.value.trim() && !isEmail(input.value.trim())) {
                showError(input, 'Enter a valid email!');
                valid = false;
            }
        });

        const firstName = form.querySelector('input[name="first_name"]');
        if (firstName && firstName.value.trim() && !/^[A-Za-z]+$/.test(firstName.value.trim())) {
            showError(firstName, 'Only alphabets allowed.');
            valid = false;
        }

        const lastName = form.querySelector('input[name="last_name"]');
        if (lastName && lastName.value.trim() && !/^[A-Za-z]+$/.test(lastName.value.trim())) {
            showError(lastName, 'Only alphabets allowed.');
            valid = false;
        }

        const phone = form.querySelector('input[name="phone"]');
        if (phone && phone.value.trim() && !/^(?:\+91|91)?[6-9]\d{9}$/.test(phone.value.trim())) {
            showError(phone, 'Phone must start with +91 and 10 digits.');
            valid = false;
        }

        const marks = form.querySelector('textarea[name="marks"]');
        if (marks && marks.value.trim()) {
            marks.value.trim().split('\n').forEach(function(line) {
                const parts = line.trim().split('|');

                if (parts.length !== 2 || !parts[0].trim() || !/^\d+(\.\d+)?$/.test(parts[1].trim())) {
                    showError(marks, 'Use format Subject|Marks');
                    valid = false;
                }
            });
        }

        const image = form.querySelector('input[name="image"]');
        if (image && image.files.length > 0) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

            if (!allowedTypes.includes(image.files[0].type)) {
                showError(image, 'Only JPG, JPEG, PNG allowed.');
                valid = false;
            }
        }

        const password = form.querySelector('input[name="password"]');
        const confirmPassword = form.querySelector('input[name="confirm_password"]');
        if (password && password.value && password.value.length < 6) {
            showError(password, 'Password must contain at least 6 letters!');
            valid = false;
        }
        if (password && confirmPassword && password.value !== confirmPassword.value) {
            showError(confirmPassword, 'Confirm password does not match!');
            valid = false;
        }

        if (!valid) {
            event.preventDefault();
        }
    });
});
