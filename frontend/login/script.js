const con1 = document.querySelector(".container1");
const con2 = document.querySelector("#container2");

const signup = document.querySelector("#b3");
const loginFromRegister = document.querySelector("#b4");

signup.addEventListener("click", () => {
    con1.style.display = "none";
    con2.style.display = "flex";
});

loginFromRegister.addEventListener("click", () => {
    con2.style.display = "none";
    con1.style.display = "flex";
});