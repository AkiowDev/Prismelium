const addRestrictionButton = document.querySelector('#ajouter-restriction');
const customRestrictions = document.querySelector('#restrictions-personnalisees');
const consentFile = document.querySelector('#fiche-consentement');
const consentFileName = document.querySelector('#nom-fiche-consentement');
const resourceCount = document.querySelector('#compteur-ressources');
const associateResourceButton = document.querySelector('#associer-ressource');
const resourceFiles = document.querySelector('#fichiers-ressources');
const resourceList = document.querySelector('#liste-ressources');
const emptyResourceMessage = document.querySelector('#message-ressources-vides');
const existingResourceCount = Number(document.querySelector('#ressources-associees').dataset.existingCount || 0);
const existingDocumentCheckboxes = document.querySelectorAll('input[name="documents_a_supprimer[]"]');
const digitalResources = new Map();

addRestrictionButton.addEventListener('click', () => {
    const row = document.createElement('label');
    const input = document.createElement('input');
    const number = customRestrictions.children.length + 1;

    row.className = 'restriction-personnalisee';
    row.append(`Autre restriction ${number}`);
    input.type = 'text';
    input.name = 'restriction_personnalisee[]';
    input.maxLength = 50;
    input.autocomplete = 'off';
    input.setAttribute('aria-label', `Préciser la restriction ${number}`);
    row.append(input);
    customRestrictions.append(row);
    input.focus();
});

consentFile.addEventListener('change', () => {
    const [file] = consentFile.files;
    consentFileName.textContent = file ? file.name : 'Aucun fichier sélectionné';
});

const syncResourceInput = () => {
    const transfer = new DataTransfer();
    digitalResources.forEach(file => transfer.items.add(file));
    resourceFiles.files = transfer.files;
};

const renderResources = () => {
    resourceList.replaceChildren();

    Array.from(digitalResources.entries()).forEach(([key, file]) => {
        const item = document.createElement('li');
        const card = document.createElement('article');
        const icon = document.createElement('span');
        const information = document.createElement('span');
        const name = document.createElement('span');
        const type = document.createElement('small');
        const removeButton = document.createElement('button');
        const extension = file.name.split('.').pop();

        card.className = 'carte-ressource';
        icon.className = 'carte-ressource__icone';
        icon.setAttribute('aria-hidden', 'true');
        icon.textContent = extension.toUpperCase().slice(0, 4);
        information.className = 'carte-ressource__informations';
        name.textContent = file.name;
        type.textContent = 'Ressource numérique';
        information.append(name, type);
        removeButton.className = 'carte-ressource__retirer';
        removeButton.type = 'button';
        removeButton.textContent = '×';
        removeButton.setAttribute('aria-label', `Retirer ${file.name}`);
        removeButton.addEventListener('click', () => {
            digitalResources.delete(key);
            syncResourceInput();
            renderResources();
        });
        card.append(icon, information, removeButton);
        item.append(card);
        resourceList.append(item);
    });

    const markedForDeletion = Array.from(existingDocumentCheckboxes)
        .filter(checkbox => checkbox.checked)
        .length;
    const totalResources = existingResourceCount - markedForDeletion + digitalResources.size;
    const hasResources = totalResources > 0;
    emptyResourceMessage.hidden = hasResources;
    resourceCount.textContent = hasResources
        ? `${totalResources} document${totalResources > 1 ? 's' : ''} associé${totalResources > 1 ? 's' : ''}`
        : '';
};

associateResourceButton.addEventListener('click', () => resourceFiles.click());

resourceFiles.addEventListener('change', () => {
    Array.from(resourceFiles.files).forEach(file => {
        const key = `${file.name}:${file.size}:${file.lastModified}`;
        if (!digitalResources.has(key)) {
            digitalResources.set(key, file);
        }
    });

    syncResourceInput();
    renderResources();
});

existingDocumentCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', renderResources);
});
