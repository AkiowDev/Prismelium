const enrollmentCards = document.querySelectorAll('[data-inscription]');
const enrollmentDialog = document.querySelector('#overlay-inscription');
const decisionForm = document.querySelector('#decision-inscription');
const profileToggle = document.querySelector('#bouton-profil');
const profileDetails = document.querySelector('#overlay-profil-detail');
const consentList = document.querySelector('#overlay-documents');
const emptyConsentMessage = document.querySelector('#overlay-documents-vides');

const profileFields = {
    userId: document.querySelector('#overlay-user-id'),
    sessionId: document.querySelector('#overlay-session-id'),
    name: document.querySelector('#overlay-nom'),
    age: document.querySelector('#overlay-age'),
    email: document.querySelector('#overlay-email'),
    experiment: document.querySelector('#overlay-experience'),
    session: document.querySelector('#overlay-session'),
    phone: document.querySelector('#overlay-telephone'),
    gender: document.querySelector('#overlay-genre'),
    language: document.querySelector('#overlay-langue'),
    description: document.querySelector('#overlay-description'),
};

const clearConsentDocuments = () => {
    consentList.replaceChildren();
    emptyConsentMessage.hidden = false;
};

enrollmentCards.forEach(card => {
    card.addEventListener('click', () => {
        const enrollment = JSON.parse(card.dataset.inscription);

        profileFields.userId.value = enrollment.userId;
        profileFields.sessionId.value = enrollment.sessionId;
        profileFields.name.textContent = enrollment.name;
        profileFields.age.textContent = enrollment.age;
        profileFields.email.textContent = enrollment.email;
        profileFields.email.href = `mailto:${enrollment.email}`;
        profileFields.experiment.textContent = enrollment.experiment;
        profileFields.session.textContent = enrollment.session;
        profileFields.phone.textContent = enrollment.phone || 'Non renseigné';
        profileFields.gender.textContent = enrollment.gender || 'Non renseigné';
        profileFields.language.textContent = enrollment.language || 'Non renseignée';
        profileFields.description.textContent = enrollment.description || 'Aucune description renseignée.';
        profileDetails.hidden = true;
        profileToggle.setAttribute('aria-expanded', 'false');
        profileToggle.textContent = 'Voir profile';
        clearConsentDocuments();

        enrollment.documents.forEach(document => {
            const item = document.createElement('li');
            if (document.href) {
                const link = document.createElement('a');
                link.href = document.href;
                link.textContent = document.name;
                if (/^https?:/i.test(document.href)) {
                    link.target = '_blank';
                    link.rel = 'noopener noreferrer';
                }
                item.append(link);
            } else {
                item.textContent = document.name;
            }
            const type = document.createElement('small');
            type.textContent = ` (${document.type})`;
            item.append(type);
            consentList.append(item);
        });

        emptyConsentMessage.hidden = enrollment.documents.length > 0;
        enrollmentDialog.showModal();
        profileToggle.focus();
    });
});

profileToggle.addEventListener('click', () => {
    const expanded = profileToggle.getAttribute('aria-expanded') === 'true';
    profileToggle.setAttribute('aria-expanded', String(!expanded));
    profileDetails.hidden = expanded;
});

decisionForm.addEventListener('submit', event => {
    const action = event.submitter ? event.submitter.value : '';
    const fullName = profileFields.name.textContent;
    const message = action === 'accept'
        ? `Valider la demande d’inscription de ${fullName} ?`
        : `Refuser la demande d’inscription de ${fullName} ?`;

    if (!window.confirm(message)) {
        event.preventDefault();
    }
});

enrollmentDialog.addEventListener('click', event => {
    if (event.target === enrollmentDialog) {
        enrollmentDialog.close();
    }
});
