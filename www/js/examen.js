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
         var data = reponse.getElementsByTagName('data').item(0);
         if (erreurs.length > 0) {
            for (var i = 0 ; i < erreurs.length ; i++) {
               popup.add(erreurs[i].firstChild.value, popup.ERROR);
            }
         } else {
            var tbody = tr.parentNode;
            var newTr = document.createElement('tr');
            var tds = Array(
                    document.createElement('td'),
                    document.createElement('td'),
                    document.createElement('td'),
                    document.createElement('td'),
                    document.createElement('td')
            );
            var aLogin = document.createElement('a');
            aLogin.innerHTML = data.getElementsByTagName('login')[0].firstChild.nodeValue;
            aLogin.href = '/prof/étudiant/' + data.getElementsByTagName('idUtil')[0].firstChild.nodeValue + '/profil';
            tds[0].appendChild(aLogin);
            tds[1].appendChild(document.createTextNode(data.getElementsByTagName('prenom')[0].firstChild.nodeValue));
            tds[2].appendChild(document.createTextNode(data.getElementsByTagName('nom')[0].firstChild.nodeValue));
            tds[3].appendChild(document.createTextNode(data.getElementsByTagName('note')[0].firstChild.nodeValue));
            var aModif = document.createElement('a');
            aModif.href = '/prof/' + data.getElementsByTagName('promo')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('module')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('matiere')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('idExam')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('numEtudiant')[0].firstChild.nodeValue + '/modifier';
            var imgModif = document.createElement('img');
            imgModif.src= '/img/prof/note_edit.png';
            imgModif.title = 'Modifier la note';
            imgModif.alt= 'Modifier la note';
            aModif.appendChild(imgModif);
            var aDelete = document.createElement('a');
            aDelete.href = '/prof/' + data.getElementsByTagName('promo')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('module')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('matiere')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('idExam')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('numEtudiant')[0].firstChild.nodeValue + '/supprimer';
            var imgDelete = document.createElement('img');
            imgDelete.src= '/img/prof/note_delete.png';
            imgDelete.title = 'Supprimer la note';
            imgDelete.alt= 'Suprpimer la note';
            aDelete.appendChild(imgDelete);
            tds[4].appendChild(aModif);
            tds[4].appendChild(aDelete);
            for (var i = 0 ; i < tds.length ; i++) {
               newTr.appendChild(tds[i]);
            }
            tbody.replaceChild(newTr, tr);
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