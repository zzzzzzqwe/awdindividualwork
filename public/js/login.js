/**
 * Обработчик переключения видимости пароля на странице входа.
 *
 * Меняет тип input с "password" на "text" и обратно,
 * обновляя также текст иконки на кнопке.
 */

document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordField = document.getElementById('password');
    const type = passwordField.type === 'password' ? 'text' : 'password';
    passwordField.type = type;
    this.textContent = type === 'password' ? '🙉' : '🙈';
});
