document.addEventListener("DOMContentLoaded", function () {
    const otpInputs = document.querySelectorAll(".otp-input");
    const otpBox = document.getElementById("otp-box");
  
    otpInputs.forEach((input, index) => {
        input.addEventListener("input", (e) => {
            if (e.target.value.length === 1) {
                if (index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            }
        });
  
        input.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && e.target.value.length === 0) {
                if (index > 0) {
                    otpInputs[index - 1].focus();
                }
            }
        });
    });
  
    // Shake animation when invalid OTP
    if (otpBox.classList.contains("shake")) {
        setTimeout(() => {
            otpBox.classList.remove("shake");
        }, 500);
    }
  });
  