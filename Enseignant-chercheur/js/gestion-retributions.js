const rewardDialog = document.querySelector('#overlay-retribution');
const rewardUserId = document.querySelector('#retribution-user-id');
const rewardSessionId = document.querySelector('#retribution-session-id');

if (rewardDialog instanceof HTMLDialogElement
    && rewardUserId instanceof HTMLInputElement
    && rewardSessionId instanceof HTMLInputElement) {
    document.querySelectorAll('[data-user-id][data-session-id]').forEach(card => {
        card.addEventListener('click', () => {
            rewardUserId.value = card.dataset.userId || '';
            rewardSessionId.value = card.dataset.sessionId || '';
            rewardDialog.showModal();
        });
    });

    rewardDialog.addEventListener('cancel', () => rewardDialog.close());

    rewardDialog.addEventListener('click', event => {
        if (event.target === rewardDialog) {
            rewardDialog.close();
        }
    });
}
