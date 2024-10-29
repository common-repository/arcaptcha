var current_script = document.currentScript;
function checkDisplay(className) {
  var element = document.querySelector(`.digits_login_form .${className}`);

  return element.style.display !== "none";
}
document.addEventListener("DOMContentLoaded", function () {
  var wrapperClasses = ["digloginpage", "forgot", "register"];

  wrapperClasses.forEach((className) => {
    var wrapper = document.querySelector(
      `.${className} .digits_fields_wrapper`
    );

    if (wrapper) {
      var arcaptchaElement = document.createElement("div");

      arcaptchaElement.className = "arcaptcha";

      arcaptchaElement.style.padding = "5px 0";

      arcaptchaElement.setAttribute(
        "data-site-key",
        current_script.getAttribute("data-site-key")
      );

      wrapper.appendChild(arcaptchaElement);
    }
  });
});
