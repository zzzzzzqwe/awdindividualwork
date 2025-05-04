/**
 * Обработчик переключения видимости пароля на странице регистрации.
 *
 * После загрузки страницы ищет кнопку и поле ввода пароля.
 * Переключает тип поля и текст иконки при клике.
 */
document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("toggleRegPassword");
    const passwordInput = document.getElementById("reg-password");

    if (toggleButton && passwordInput) {
        toggleButton.addEventListener("click", function () {
            const type = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = type;
            this.textContent = type === "password" ? "🙉" : "🙈";
        });
    }
});
