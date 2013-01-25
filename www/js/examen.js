/**
 * Fonctions gérant l'ajout d'une note via ajax
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */

function saisir_note(a) {
   var nouvelleEntree = a.parentNode.parentNode;
   nouvelleEntree.innerHTML = '<td><input type="text" name="login" /></td><td><input type="text" name="nom" /></td><td><input type="text" name="prenom" /></td><td><input type="text" name="note" value="ABS = absence justifiée" />/20</td><td><input type="reset" value="Annuler" onClick="reset_note(this);" /> <input type="submit" value="Ajouter" /></td>';
}

function reset_note(resetButton) {
   resetButton.parentNode.parentNode.innerHTML = '<td colspan="5"><a onClick="saisir_note(this);">Saisir une nouvelle note</a></td>';
}

function save_note(form, url) {
   var inputs = form.getElementsByTagName('input');
   var tr = inputs[0].parentNode.parentNode;
   var requete = '';
   for (var i = 0; i < inputs.length - 2; i++) {
      switch (inputs[i].name) {
         case 'login':
            requete += 'login=' + encodeURIComponent(inputs[i].value) + '&'; 
            break;
         case 'nom':
            requete += 'nom=' + encodeURIComponent(inputs[i].value)  + '&'; 
            break;
         case 'prenom':
            requete += 'prenom=' + encodeURIComponent(inputs[i].value) + '&'; 
            break;
         case 'note':
            requete += 'note=' + encodeURIComponent(inputs[i].value.replace(',', '.')); 
            break;
      }
   }
   var xhr = getXMLHttpRequest();
	
	if (xhr && xhr.readyState !== 0) {
		xhr.abort();
		delete xhr;
	}
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState === 4 && (xhr.status === 200 || xhr.status === 0)){
         var reponse = xhr.responseXML.getElementsByTagName('root').item(0);
         var erreurs = reponse.getElementsByTagName('erreur');
         var data = reponse.getElementsByTagName('td');
         if (erreurs.length > 0) {
            for (var i = 0 ; i < erreurs.length ; i++) {
               popup.add(erreurs[i].firstChild.value, popup.ERROR);
            }
         } else {
            tr.innerHTML = '';
            for (var i = 0 ; i < data.length ; i++) {
               tr.appendChild(data[i]);
            }
            popup.add('La note a été ajoutée avec succès.', popup.SUCCESS);
         }
		}
		else if (xhr.readyState === 3){
			popup.add('Veuillez patienter...', popup.INFORMATION);
		}
	};
	xhr.open('POST', url, true);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send(requete);
   return false;
}