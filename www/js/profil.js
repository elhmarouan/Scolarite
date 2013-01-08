function display_profil(idRole) {
   idRole = parseInt(idRole);
   var fieldsetProf = document.getElementById('profilProf');
   var fieldsetEtudiant = document.getElementById('profilEtudiant');
   if (idRole === 2) {
      fieldsetProf.style.display = 'block';
      fieldsetEtudiant.style.display = 'none';
   } else if (idRole === 3) {
      fieldsetProf.style.display = 'none';
      fieldsetEtudiant.style.display = 'block';
   } else {
      fieldsetProf.style.display = 'none';
      fieldsetEtudiant.style.display = 'none';
   }
}