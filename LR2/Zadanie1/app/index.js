document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("feedback-form");
    const resultDiv = document.getElementById("result");
    const nameInput = form.querySelector("input[name='name']");
    const emailInput = form.querySelector("input[name='email']");
    const phoneInput = form.querySelector("input[name='phone']");
    const errorMessages = form.querySelectorAll(".error-message");
  
    // Маска телефона
    phoneInput.addEventListener("input", () => {
      let value = phoneInput.value.replace(/\D/g, "");
      if (value.startsWith("8")) value = "7" + value.slice(1);
      if (!value.startsWith("7")) value = "7" + value;
  
      let formatted = "+7 (";
      if (value.length > 1) formatted += value.slice(1, 4);
      if (value.length >= 4) formatted += ") " + value.slice(4, 7);
      if (value.length >= 7) formatted += "-" + value.slice(7, 9);
      if (value.length >= 9) formatted += "-" + value.slice(9, 11);
      phoneInput.value = formatted;
    });
  
    form.addEventListener("submit", function (e) {
      e.preventDefault(); // Отключаем обычную отправку
  
      let valid = true;
  
      // Сброс ошибок
      errorMessages.forEach(msg => {
        msg.style.display = "none";
        msg.textContent = "";
      });
      form.querySelectorAll("input").forEach(input => input.classList.remove("error"));
  
      // Проверка ФИО
      const nameValue = nameInput.value.trim();
      const nameRegex = /^[А-Яа-яЁё\s\-]+$/;
      if (nameValue === "") {
        showError(nameInput, "Пожалуйста, введите ФИО");
        valid = false;
      } else if (!nameRegex.test(nameValue)) {
        showError(nameInput, "ФИО должно содержать только русские буквы");
        valid = false;
      }
  
      // Проверка Email
      const emailValue = emailInput.value.trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (emailValue === "") {
        showError(emailInput, "Пожалуйста, введите email");
        valid = false;
      } else if (!emailRegex.test(emailValue)) {
        showError(emailInput, "Некорректный email");
        valid = false;
      }
  
      // Проверка телефона
      const phoneValue = phoneInput.value.trim();
      const phoneDigits = phoneValue.replace(/\D/g, "");
      if (phoneValue === "") {
        showError(phoneInput, "Пожалуйста, введите номер телефона");
        valid = false;
      } else if (phoneDigits.length !== 11 || !phoneDigits.startsWith("7")) {
        showError(phoneInput, "Некорректный номер телефона");
        valid = false;
      }
  
      // Только если всё корректно — отправляем AJAX-запрос
      if (valid) {
        const formData = new FormData(form);
        fetch("form.php", {
          method: "POST",
          body: formData
        })
          .then(response => response.text())
          .then(data => {
            form.style.display = "none";
            resultDiv.innerHTML = data;
            resultDiv.style.display = "block";
          })
          .catch(() => {
            resultDiv.innerHTML = "Произошла ошибка при отправке.";
            resultDiv.style.display = "block";
          });
      }
    });
  
    function showError(input, message) {
      input.classList.add("error");
      const section = input.closest(".section");
      const errorMessage = section.querySelector(".error-message");
      if (errorMessage) {
        errorMessage.textContent = message;
        errorMessage.style.display = "block";
      }
    }
  });
  