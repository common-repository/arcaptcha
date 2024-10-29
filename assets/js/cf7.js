document.addEventListener("DOMContentLoaded", function () {
  var ARCaptchaCF7 = document.querySelector(".wpcf7");

  var ARCaptchaResetCF7 = function (event) {
    loadWidget();
  };

  ARCaptchaCF7.addEventListener("wpcf7invalid", ARCaptchaResetCF7, false);
  ARCaptchaCF7.addEventListener("wpcf7spam", ARCaptchaResetCF7, false);
  ARCaptchaCF7.addEventListener("wpcf7mailsent", ARCaptchaResetCF7, false);
  ARCaptchaCF7.addEventListener("wpcf7mailfailed", ARCaptchaResetCF7, false);
  ARCaptchaCF7.addEventListener("wpcf7submit", ARCaptchaResetCF7, false);
});
