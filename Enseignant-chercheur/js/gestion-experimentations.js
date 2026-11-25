const selectionForm = document.querySelector('#selection-actions');
const selectedExperiments = document.querySelectorAll('input[name="selected_experiments[]"]');
const selectionCount = document.querySelector('#compteur-selection');
const actionButtons = selectionForm.querySelectorAll('button[name="action"]');

const updateSelection = () => {
    const selectedCount = Array.from(selectedExperiments)
        .filter(checkbox => checkbox.checked)
        .length;

    actionButtons.forEach(button => {
        button.disabled = selectedCount === 0;
    });

    selectionCount.textContent = selectedCount === 0
        ? 'Aucune expérimentation sélectionnée.'
        : `${selectedCount} expérimentation${selectedCount > 1 ? 's' : ''} sélectionnée${selectedCount > 1 ? 's' : ''}.`;
};

selectedExperiments.forEach(checkbox => {
    checkbox.addEventListener('change', updateSelection);
});

selectionForm.addEventListener('submit', event => {
    if (!event.submitter) {
        return;
    }

    const selectedCount = Array.from(selectedExperiments)
        .filter(checkbox => checkbox.checked)
        .length;
    const confirmation = event.submitter.value === 'delete'
        ? `Supprimer définitivement ces ${selectedCount} expérimentation${selectedCount > 1 ? 's' : ''} et leurs données liées ? Cette action est irréversible.`
        : `Archiver ${selectedCount} expérimentation${selectedCount > 1 ? 's' : ''} sélectionnée${selectedCount > 1 ? 's' : ''} ? Vous pourrez toujours les retrouver avec le filtre des expérimentations archivées.`;

    if (!window.confirm(confirmation)) {
        event.preventDefault();
    }
});

updateSelection();
