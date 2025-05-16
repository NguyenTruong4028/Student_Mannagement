
function validateForm(formId) {
    const form = document.getElementById(formId);
    const password = form.querySelector('input[name="password"]').value;
    const confirmPassword = form.querySelector('input[name="confirm_password"]').value;

    if (password !== confirmPassword) {
        alert('Mật khẩu và xác nhận mật khẩu không khớp!');
        return false;
    }

    if (password.length < 6) {
        alert('Mật khẩu phải có ít nhất 6 ký tự!');
        return false;
    }

    return true;
}