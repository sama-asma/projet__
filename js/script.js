// Menu Toggle
let toggle = document.querySelector(".toggle");
let navigation = document.querySelector(".navigation");
let main = document.querySelector(".main-content");

toggle.onclick = function () {
  navigation.classList.toggle("active");
  main.classList.toggle("active");
};

// add hovered class to selected list item
let list = document.querySelectorAll(".navigation li");

function activeLink() {
  list.forEach((item) => {
    item.classList.remove("hovered");
  });
  this.classList.add("hovered");
}
// afficher les sous buttons de finance
document.querySelector(".finance").addEventListener("click", function() {
  let subButtons = document.getElementById("finance-options");
  
  // Vérifier l'état actuel
  if (subButtons.style.display === "none") {
      subButtons.style.display = "flex"; // Afficher les sous-boutons
  } else {
      subButtons.style.display = "none"; // Masquer les sous-boutons
  }
});
// afficher les sous buttons d'individu
document.querySelector(".individu").addEventListener("click", function() {
  let subButtons = document.getElementById("individu-options");
  
  // Vérifier l'état actuel
  if (subButtons.style.display === "none") {
      subButtons.style.display = "flex"; // Afficher les sous-boutons
  } else {
      subButtons.style.display = "none"; // Masquer les sous-boutons
  }
});
