const darkTheme = "/wp-content/themes/cocoon-1.5.0/css/darkmode.css";
const btn = document.querySelector("#mode-toggle");
const currentTheme = localStorage.getItem("mode");
const theme = document.querySelector("#theme-mode");
const isLight = window.matchMedia('(prefers-color-scheme: light)').matches;
const lightMode = () => {
  theme.href = "";
  btn.checked = false;
}
const darkMode = () => {
  theme.href = darkTheme;
  btn.checked = true
}
if (currentTheme == "light") {
  lightMode();
}
else if (currentTheme == "dark") {
  darkMode();
}
// 消すと初回はライトモード
else {
  if (isLight) {
    lightMode();
  } else if (!isLight) {
    darkMode();
  }
}
const toggleDark = () => {
  if (theme.getAttribute("href") == "") {
    darkMode();
  } else {
    lightMode();
  }
  let style = "light";
  if (theme.getAttribute("href") == darkTheme) {
    darkMode();
    style = "dark";
  }
  localStorage.setItem("mode", style);
}
btn.addEventListener("click", toggleDark);
//開発モード
// let keyPress = 81;
// window.addEventListener("keydown", checkKeyPress);
// function checkKeyPress(key) {
//   if (key.keyCode === keyPress) {
//     toggleDark();
//   }
// }
