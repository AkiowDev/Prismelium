
<div class="rediger_message">

    <h2>Nouveau message</h2>

    <form action="rediger.php" method="POST">

        <label for="destinataire">Destinataire :</label>
        <input
            type="email"
            id="destinataire"
            name="destinataire"
            placeholder="Adresse mail du destinataire"
            required
        >

        <label for="contenu">Message :</label>
        <textarea
            id="contenu"
            name="contenu"
            placeholder="Écris ton message ici..."
            required
        ></textarea>

        <button type="submit" name="envoyer">Envoyer</button>

    </form>

</div>