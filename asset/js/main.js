// function showPage(pageId) {

//   // Reset reCAPTCHA if switching to a page that has it
//   if (
//     pageId === "login" ||
//     pageId === "register" ||
//     pageId === "forgot-password"
//   ) {
//     try {
//       grecaptcha.reset();
//     } catch (e) {
//       // reCAPTCHA might not be loaded yet
//     }
//   }
// }

// function validateForm(formId) {
//   // Basic form validation
//   const form = document.getElementById(formId);
//   const inputs = form.querySelectorAll("input");
//   let isValid = true;

//   inputs.forEach((input) => {
//     if (input.required && !input.value.trim()) {
//       input.style.borderColor = "#dc3545";
//       isValid = false;
//     } else {
//       input.style.borderColor = "#e0e0e0";
//     }
//   });

//   // Check reCAPTCHA
//   try {
//     const recaptchaResponse = grecaptcha.getResponse();
//     if (recaptchaResponse.length === 0) {
//       document.querySelector(".g-recaptcha").style.border = "1px solid #dc3545";
//       isValid = false;
//     } else {
//       document.querySelector(".g-recaptcha").style.border = "none";
//     }
//   } catch (e) {
//     // reCAPTCHA might not be on this form
//   }

//   return isValid;
// }
