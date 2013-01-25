/**
 * Fonctions gérant l'ajout d'une note via ajax
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */

//Formulaire d'ajout d'une nouvelle note
function add_note(a) {
   var nouvelleEntree = a.parentNode.parentNode;
   nouvelleEntree.innerHTML = '<td><input type="text" name="login" /></td><td><input type="text" name="nom" /></td><td><input type="text" name="prenom" /></td><td><input type="text" name="note" value="ABS = absence justifiée" />/20</td><td><input type="reset" value="Annuler" onClick="reset_add(this);" /> <input type="submit" value="Ajouter" /></td>';
}

//Formulaire de modification d'une note
function edit_note(a) {
   var tdNote = a.parentNode.parentNode.getElementsByTagName('td')[3];
   tdNote.innerHTML = tdNote.innerHTML.replace(/^\s+|\s+$/g, '');
   var input = document.createElement('input');
   input.type = 'text';
   input.name = 'note';
   input.value = (tdNote.innerHTML !== 'Absence justifiée') ? tdNote.innerHTML.replace('/20', '') : 'ABS';
   tdNote.replaceChild(input, tdNote.firstChild);
   tdNote.appendChild(document.createTextNode('/20'));
   a.parentNode.innerHTML = '<input type="hidden" name="numEtudiant" value="' + a.parentNode.lastChild.href.replace(document.URL + '/', '').replace('/supprimer', '') + '" /> <input type="reset" value="Annuler" onClick="reset_edit(this);" /> <input type="submit" value="Modifier" />';
}

function reset_add(resetButton) {
   resetButton.parentNode.parentNode.innerHTML = '<td colspan="5"><a onClick="add_note(this);">Saisir une nouvelle note</a></td>';
}

function reset_edit(resetButton) {
   resetButton.parentNode.parentNode.getElementsByTagName('td')[3].innerHTML = (resetButton.parentNode.parentNode.getElementsByTagName('td')[3].firstChild.value !== 'ABS') ? resetButton.parentNode.parentNode.getElementsByTagName('td')[3].firstChild.value + '/20' : 'Absence justifiée';
   resetButton.parentNode.innerHTML = '<a onClick="edit_note(this);"><img src="/img/prof/note_edit.png" alt="Modifier la note" title="Modifier la note" /></a> <a href="' + document.URL + '/' + resetButton.parentNode.getElementsByTagName('input')[0].value + '/supprimer" onClick="return confim(\'Voulez-vous vraiment supprimer cette note ?\');"><img src="/img/prof/note_delete.png" alt="Supprimer la note" title="Supprimer la note" /></a>';
}

function save_note(form, url) {
   var inputs = form.getElementsByTagName('input');
   var tr = inputs[0].parentNode.parentNode;
   var requete = '';
   var isEdit;
   if (inputs.length === 6) {
      /**
       * Si c'est un ajout
       */
      for (var i = 0; i < inputs.length - 2; i++) {
         switch (inputs[i].name) {
            case 'login':
               requete += 'login=' + encodeURIComponent(inputs[i].value) + '&';
               break;
            case 'nom':
               requete += 'nom=' + encodeURIComponent(inputs[i].value) + '&';
               break;
            case 'prenom':
               requete += 'prenom=' + encodeURIComponent(inputs[i].value) + '&';
               break;
            case 'note':
               requete += 'note=' + encodeURIComponent(inputs[i].value.replace(',', '.'));
               break;
         }
      }
      isEdit = false;
   } else {
      /**
       * Si c'est une modification
       */
      requete = 'note=' + encodeURIComponent(inputs[0].value.replace(',', '.')) + '&numEtudiant=' + encodeURIComponent(inputs[1].value);
      isEdit = true;
   }
   var xhr = getXMLHttpRequest();

   if (xhr && xhr.readyState !== 0) {
      xhr.abort();
      delete xhr;
   }
   var waitPopupId;
   xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && (xhr.status === 200 || xhr.status === 0)) {
         popup.delete(waitPopupId);
         var reponse = xhr.responseXML.getElementsByTagName('root').item(0);
         var erreurs = reponse.getElementsByTagName('erreur');
         if (erreurs.length > 0) {
            //Récupération des erreurs et ajout des notifications
            for (var i = 0; i < erreurs.length; i++) {
               popup.add(erreurs[i].firstChild.nodeValue, popup.ERROR);
            }
         } else {
            if (isEdit) {
               //Si l'édition s'est bien déroulée
               reset_edit(inputs[2]);
               popup.add('La note a été modifiée avec succès.', popup.SUCCESS);
            } else {
               //Si l'ajout s'est bien déroulé
               var data = reponse.getElementsByTagName('data').item(0);
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
               imgModif.src = '/img/prof/note_edit.png';
               imgModif.title = 'Modifier la note';
               imgModif.alt = 'Modifier la note';
               aModif.appendChild(imgModif);
               var aDelete = document.createElement('a');
               aDelete.href = '/prof/' + data.getElementsByTagName('promo')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('module')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('matiere')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('idExam')[0].firstChild.nodeValue + '/' + data.getElementsByTagName('numEtudiant')[0].firstChild.nodeValue + '/supprimer';
               var imgDelete = document.createElement('img');
               imgDelete.src = '/img/prof/note_delete.png';
               imgDelete.title = 'Supprimer la note';
               imgDelete.alt = 'Suprpimer la note';
               aDelete.appendChild(imgDelete);
               tds[4].appendChild(aModif);
               tds[4].appendChild(aDelete);
               for (var i = 0; i < tds.length; i++) {
                  newTr.appendChild(tds[i]);
               }
               tbody.replaceChild(newTr, tr);
               var newAddTr = document.createElement('tr');
               newAddTr.innerHTML = '<td colspan="5"><a onClick="add_note(this);">Saisir une nouvelle note</a></td>';
               tbody.appendChild(newAddTr);
               popup.add('La note a été ajoutée avec succès.', popup.SUCCESS);
            }
         }
      }
      else if (xhr.readyState === 3) {
         waitPopupId = popup.add('Veuillez patienter...', popup.INFORMATION);
      }
   };
   xhr.open('POST', url, true);
   xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
   xhr.send(requete);
   return false;
}