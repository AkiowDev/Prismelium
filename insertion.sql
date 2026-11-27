-- =====================================================================
-- SCRIPT D'INSERTION DES DONNÉES (MySQL / XAMPP)
-- =====================================================================

INSERT INTO RESSOURCE (Id_Res, Nom_Res, Dispo_Res, Type_Res, Poids_Res, Stockage_Res) VALUES
(1,'Serveur de collecte',1,'Serveur',12,'Datacenter'),
(2,'S stockage reponses',1,'Stockage',2,'Cloud'),
(3,'Ordinateur test',1,'Informatique',2,'Salle test'),
(4,'Smartphone Android',1,'Mobile',0.2,'Stock mobile'),
(5,'iPhone test',1,'Mobile',0.2,'Stock mobile'),
(6,'Casque VR',1,'Materiel',0.5,'Salle immersive'),
(7,'Webcam participant',1,'Materiel',0.3,'Stock materiel'),
(8,'Microphone',1,'Materiel',0.4,'Stock materiel'),
(9,'Tablette',1,'Mobile',0.5,'Stock mobile'),
(10,'Ecran test',1,'Informatique',4,'Salle test'),
(11,'Routeur internet',1,'Reseau',0.6,'Infrastructure'),
(12,'Camera enregistrement',1,'Materiel',1,'Salle test');

INSERT INTO ROLE (Id_Role, Nom_Role) VALUES
(1,'Enseignant-Chercheur'),
(2,'Gestionnaire'),
(3,'Participant'),
(4,'Responsable equipe de recherche'),
(5,'Directeur de laboratoire'),
(6,'Administrateur'),
(7,'Visiteur');

INSERT INTO EQUIPE (Id_Equipe, Nom_Equipe) VALUES
(1,'Equipe Arrakis'),
(2,'Equipe Mordor'),
(3,'Equipe Tatooine'),
(4,'Equipe Hyrule'),
(5,'Equipe Tamriel'),
(6,'Equipe Gotham'),
(7,'Equipe Westeros'),
(8,'Equipe Azeroth'),
(9,'Equipe Vulcain'),
(10,'Equipe LV426');

INSERT INTO PERMISSION (Id_Perm, Nom_Perm, Desc_Perm) VALUES
(1,'Creer compte','Creer des comptes utilisateurs'),
(2,'Modifier compte','Modifier les profils'),
(3,'Supprimer compte','Supprimer un compte'),
(4,'Creer experimentations','Publier des etudes'),
(5,'Modifier experimentations','Modifier les etudes'),
(6,'Cloturer experimentations','Cloturer les etudes'),
(7,'Consulter reponses','Acceder aux reponses'),
(8,'Exporter reponses','Exporter les donnees'),
(9,'Creer questionnaires','Creer des questionnaires'),
(10,'Modifier questionnaires','Modifier les questions'),
(11,'Gerer inscriptions','Gerer les participants'),
(12,'Gerer recompenses','Gerer les remunerations'),
(13,'Gerer ressources','Administrer les ressources'),
(14,'Consulter journaux','Consulter les logs'),
(15,'Gerer permissions','Administrer les droits');

INSERT INTO CONTRAINTE (Id_Contrainte, Nom_Contrainte, Desc_Contrainte) VALUES
(1,'Age minimum','Participant ayant cette age minimum'),
(2,'Intervalle Age','Participant doit etre entre cette age'),
(3,'Francais','Comprendre le francais'),
(4,'Anglais','Comprendre l anglais'),
(5,'Gaucher','Le participant doit etre gaucher'),
(6,'Femme','Le participant doit etre une femme'),
(7,'Droitier','Le participant doit etre droitier'),
(8,'Epilepsie','La personne ne doit pas etre epileptique'),
(9,'Homme','Le participant doit etre un homme'),
(10,'Sans lunettes','Le participant doit etre sans lunettes');

INSERT INTO UTILISATEUR (Id_User, Nom_User, Prenom_User, Birth_User, Mail_User, Mdp_User, Tel_User, Desc_User, Genre_User, Lang_User, Mode_User, Date_Insc, Rgpd_User, Id_Equipe, Id_Role) VALUES
(1,'Martin','Alice','1995-04-12','alice.martin@example.com','1234','0600000001','Administratrice du site','Femme','Francais','Clair','2025-01-10',1,1,1),
(2,'Bernard','Lucas','1998-07-21','lucas.bernard@example.com','6789','0600000002','Gestion des experimentations','Homme','Francais','Sombre','2025-01-12',1,2,2),
(3,'Petit','Emma','1992-02-05','emma.petit@example.com','0000','0600000003','Analyse des resultats','Femme','Francais','Clair','2025-01-15',1,6,3),
(4,'Robert','Hugo','1990-11-18','hugo.robert@example.com','1111','0600000004','Developpeur plateforme','Homme','Francais','Sombre','2025-01-18',1,4,3),
(5,'Richard','Lea','2000-06-09','lea.richard@example.com','2222','0600000005','Participante reguliere','Femme','Francais','Clair','2025-01-20',0,3,3),
(6,'Durand','Nathan','1989-09-30','nathan.durand@example.com','3333','0600000006','Chercheur partenaire','Homme','Francais','Clair','2025-01-22',1,9,2),
(7,'Dubois','Ines','1997-03-14','ines.dubois@example.com','4444','0600000007','Moderatrice','Femme','Francais','Sombre','2025-01-25',1,8,4),
(8,'Moreau','Adam','1994-12-01','adam.moreau@example.com','5555','0600000008','Participant premium','Homme','Francais','Clair','2025-01-28',1,3,5),
(9,'Laurent','Jade','1996-05-25','jade.laurent@example.com','6666','0600000009','Support participants','Femme','Francais','Clair','2025-02-01',1,9,4),
(10,'Simon','Louis','1993-08-16','louis.simon@example.com','7777','0600000010','Participant','Homme','Francais','Sombre','2025-02-03',1,3,1),
(11,'Michel','Manon','1991-01-07','manon.michel@example.com','8888','0600000011','Responsable RGPD','Femme','Francais','Clair','2025-02-05',1,10,1),
(12,'Garcia','Paul','1999-10-22','paul.garcia@example.com','9999','0600000012','Participant','Homme','Francais','Sombre','2025-02-08',0,3,6),
(13,'David','Camille','1995-06-11','camille.david@example.com','4321','0600000013','Chercheuse UX','Femme','Francais','Clair','2025-02-10',1,7,3),
(14,'Bertrand','Noah','2001-02-27','noah.bertrand@example.com','9876','0600000014','Participant','Homme','Francais','Sombre','2025-02-12',1,3,6),
(15,'Roux','Sarah','1997-04-19','sarah.roux@example.com','1379','0600000015','Participant premium','Femme','Francais','Clair','2025-02-15',0,3,3);

INSERT INTO EXPERIMENTATION (Id_Exp, Nom_Exp, Desc_Exp, Date_Debut_Exp, Date_Fin_Exp, Statut_Exp, Type_Retribution, Montant_Retribution, Id_Equipe) VALUES
(1,'Test interface web','Evaluation de navigation et ergonomie web','2025-03-01 09:00:00','2025-03-31 18:00:00','Terminee','Bon cadeau',10,3),
(2,'Audit securite application','Evaluation de vulnerabilites applicatives','2025-04-01 09:00:00','2025-04-30 18:00:00','Terminee','Points',100,6),
(3,'Test application mobile','Evaluation de performances et ergonomie mobile','2025-05-01 09:00:00','2025-05-31 18:00:00','Terminee','Bon cadeau',15,4),
(4,'Etude moteur de recherche','Evaluation de pertinence et vitesse de recherche','2025-06-01 09:00:00','2025-06-30 18:00:00','Terminee','Virement',20,5),
(5,'Test assistant IA','Evaluation de reponses generees par IA','2025-07-01 09:00:00','2025-07-31 18:00:00','Terminee','Points',150,5),
(6,'Benchmark base de donnees','Comparaison de performances SQL','2025-08-01 09:00:00','2025-08-31 18:00:00','Terminee','Bon cadeau',10,10),
(8,'Evaluation logiciel de gestion','Test fonctionnel d un outil de gestion','2025-10-01 09:00:00','2025-10-31 18:00:00','Terminee','Virement',25,9),
(10,'Test jeu video','Evaluation de performances et experience de jeu','2025-12-01 09:00:00','2025-12-31 18:00:00','Terminee','Points',120,9),
(11,'Etude reconnaissance vocale','Test de precision d un systeme vocal','2026-01-01 09:00:00','2026-01-31 18:00:00','Terminee','Bon cadeau',15,5),
(12,'Test systeme embarque','Evaluation d un dispositif informatique embarque','2026-02-01 09:00:00','2026-02-28 18:00:00','Terminee','Points',60,4),
(13,'Test accessibilite logiciel','Evaluation de l accessibilite d un logiciel','2026-03-01 09:00:00','2026-03-31 18:00:00','Terminee','Virement',20,10),
(14,'Test outil de developpement','Evaluation d un environnement de programmation','2026-04-01 09:00:00','2026-04-30 18:00:00','En cours','Bon cadeau',15,3);

INSERT INTO LIMITES (Id_Exp, Id_Contrainte, Val_Min, Val_Max) VALUES
(1,1,25,NULL),(2,2,20,30),(3,2,20,30),
(4,4,NULL,NULL),(5,8,NULL,NULL),(8,10,NULL,NULL),
(10,1,25,NULL),(11,2,20,30),(12,9,NULL,NULL),
(13,6,NULL,NULL),(13,4,NULL,NULL),(1,2,NULL,NULL);

INSERT INTO LIVRABLE (Id_Liv, Nom_Liv, Date_Liv, Type_Liv, Id_Exp, Id_User) VALUES
(1,'Resultats interface mobile','2025-03-28','Donnees',1,3),
(2,'Synthese habitudes numeriques','2025-04-25','Rapport',2,3),
(3,'Resultats moteur recherche','2025-05-28','Donnees',3,6),
(4,'Synthese experience achat','2025-06-25','Rapport',4,6),
(5,'Resultats assistant virtuel','2025-07-28','Donnees',5,3),
(9,'Resultats habitudes alimentaires','2025-11-25','Donnees',8,3),
(10,'Synthese plateforme video','2025-12-28','Rapport',10,3),
(11,'Resultats usage IA','2026-01-28','Donnees',11,6),
(12,'Synthese inscription en ligne','2026-02-25','Rapport',12,6),
(13,'Resultats satisfaction client','2026-03-28','Donnees',13,3);

INSERT INTO SESSION (Id_Ses, Date_Ses, Debut_Ses, Fin_Ses, Statut_Ses, Nb_Place_Max, Id_Exp) VALUES
(1,'2025-03-05','09:00:00','10:00:00','Terminee',100,1),
(2,'2025-04-05','10:00:00','11:00:00','Terminee',150,2),
(3,'2025-05-05','11:00:00','12:00:00','Terminee',100,3),
(4,'2025-06-05','13:00:00','14:00:00','Terminee',80,4),
(5,'2025-07-05','14:00:00','15:00:00','Terminee',100,5),
(6,'2025-08-05','09:00:00','10:00:00','Terminee',120,8),
(7,'2025-09-05','10:00:00','11:00:00','Terminee',100,5),
(8,'2025-10-05','11:00:00','12:00:00','Terminee',80,3),
(9,'2025-11-05','13:00:00','14:00:00','Terminee',150,8),
(10,'2025-12-05','14:00:00','15:00:00','Terminee',100,10),
(11,'2026-01-05','09:00:00','10:00:00','Terminee',120,11),
(12,'2026-02-05','10:00:00','11:00:00','Terminee',100,12),
(13,'2026-03-05','11:00:00','12:00:00','Terminee',150,13),
(14,'2026-04-05','13:00:00','14:00:00','En attente',100,1),
(15,'2026-05-05','14:00:00','15:00:00','Planifiee',150,10),
(16,'2026-06-01', '09:00:00', '12:00:00', 'Planifiee', 50, 14);

INSERT INTO QUESTIONNAIRE (Id_Quest, Nom_Quest, Ano_Quest, Id_Exp) VALUES
(1,'Interface mobile',1,1),
(2,'Habitudes numeriques',1,2),
(3,'Moteur de recherche',1,3),
(4,'Experience achat',1,4),
(5,'Assistant virtuel',1,5),
(9,'Habitudes alimentaires',1,8),
(10,'Plateforme video',1,10),
(11,'Usage IA',1,11),
(12,'Inscription en ligne',1,12),
(13,'Satisfaction client',1,13),
(14,'Ressenti outil',1,14);

INSERT INTO JOURNAL_LOG (Id_Log, Type_Log, Date_Log, IP_log, Erreur_Log, Id_User) VALUES
(1,'Connexion','2025-03-01 08:30:00','192.0.2.1',NULL,1),
(2,'Inscription','2025-03-02 09:15:00','192.0.2.2',NULL,5),
(3,'Participation','2025-03-03 10:00:00','192.0.2.3',NULL,8),
(4,'Questionnaire','2025-03-04 11:20:00','192.0.2.4',NULL,10),
(5,'Connexion','2025-03-05 12:10:00','192.0.2.5',NULL,12),
(6,'Recompense','2025-03-06 13:30:00','192.0.2.6',NULL,14),
(7,'Connexion','2025-03-07 14:00:00','192.0.2.7',NULL,15),
(8,'Inscription','2025-03-08 15:45:00','192.0.2.8',NULL,5),
(9,'Participation','2025-03-09 16:00:00','192.0.2.9',NULL,8),
(10,'Questionnaire','2025-03-10 08:15:00','192.0.2.10',NULL,10),
(11,'Connexion','2025-03-11 09:30:00','192.0.2.11',NULL,12),
(12,'Messagerie','2025-03-12 10:45:00','192.0.2.12',NULL,14),
(13,'Connexion','2025-03-13 11:00:00','192.0.2.13',NULL,15),
(14,'Erreur','2025-03-14 12:30:00','192.0.2.14','Reponse invalide',5),
(15,'Connexion','2025-03-15 13:15:00','192.0.2.15',NULL,8);

INSERT INTO DOCUMENT (Id_Doc, Nom_Doc, Type_Doc, Poids_Doc, Url_Doc, Id_Exp) VALUES
(1,'Notice etude mobile.pdf','pdf',250,'https://example.com/doc1',1),
(2,'Protocole numerique.pdf','pdf',180,'https://example.com/doc2',2),
(3,'Spec recherche.pdf','pdf',320,'https://example.com/doc3',3),
(4,'Parcours achat.pdf','pdf',150,'https://example.com/doc4',4),
(5,'Protocole assistant.pdf','pdf',200,'https://example.com/doc5',5),
(9,'Questionnaire alimentaire.pdf','pdf',100,'https://example.com/doc9',8),
(10,'Protocole video.mp4','Video',15000,'https://example.com/doc10',10),
(11,'Etude IA.pdf','pdf',400,'https://example.com/doc11',11),
(12,'Test inscription.pdf','pdf',120,'https://example.com/doc12',12),
(13,'Questionnaire satisfaction.pdf','pdf',170,'https://example.com/doc13',13);

INSERT INTO QUESTION (Id_Question, Contenu_Question, Id_Quest) VALUES
(1,'Cette application est-elle facile a utiliser ?',1),
(2,'Combien de temps passez-vous en ligne ?',2),
(3,'Les resultats de recherche sont-ils pertinents ?',3),
(4,'Le parcours d achat est-il simple ?',4),
(5,'L assistant virtuel vous aide-t-il ?',5),
(9,'Preparez-vous vos repas a domicile ?',9),
(10,'La navigation video est-elle fluide ?',10),
(11,'Utilisez-vous des outils d intelligence artificielle ?',11),
(12,'Le formulaire d inscription est-il clair ?',12),
(13,'Recommanderiez-vous ce service ?',13),
(14,'La prise en main était-elle intuitive ?',14),
(15,'Recommanderiez-vous cet outil ?',14);

INSERT INTO REPONSE (Id_User, Id_Question, Contenu_Rep) VALUES
(3,1,'Oui'),
(4,2,'Trois heures'),
(5,3,'Oui'),
(13,4,'A ameliorer'),
(15,5,'Oui'),
(3,9,'Souvent'),
(4,10,'Oui'),
(5,11,'Oui'),
(13,12,'Oui'),
(15,13,'Oui');

INSERT INTO ADMINISTRATION (Id_Role, Id_Perm) VALUES
(1,1),(1,2),(1,3),(1,4),(1,5),
(1,6),(1,7),(1,8),(1,9),(1,10),
(6,11),(6,12),(6,13),(6,14),(6,15);

INSERT INTO RESTRICTION (Id_User, Id_Contrainte) VALUES
(1,4),(2,5),(3,6),(4,9),(5,8),
(6,3),(7,5),(8,7),(9,8),(10,9),
(11,4),(12,9),(13,10),(14,10),(15,7);

INSERT INTO MESSAGERIE (Id_Expediteur, Id_Destinataire, Date_Msg, Contenu_Msg) VALUES
(1, 2, '2025-03-01 09:00:00', 'Une nouvelle experimentation est publiee.'),
(2, 3, '2025-03-02 10:00:00', 'Le questionnaire est pret.'),
(3, 5, '2025-03-03 11:00:00', 'Votre participation est enregistree.'),
(4, 6, '2025-03-04 12:00:00', 'Les resultats sont disponibles.'),
(5, 8, '2025-03-05 13:00:00', 'Merci pour votre participation.'),
(6, 10, '2025-03-06 14:00:00', 'Votre recompense est validee.'),
(7, 12, '2025-03-07 15:00:00', 'Une etude correspondant a votre profil est ouverte.'),
(8, 14, '2025-03-08 16:00:00', 'Votre inscription est confirmee.'),
(9, 15, '2025-03-09 09:00:00', 'Votre questionnaire a ete recu.'),
(10, 5, '2025-03-10 10:00:00', 'Votre recompense est disponible.'),
(11, 8, '2025-03-11 11:00:00', 'Une nouvelle etude vous interesse-t-elle ?'),
(12, 10, '2025-03-12 12:00:00', 'Votre participation a ete validee.'),
(13, 12, '2025-03-13 13:00:00', 'Merci de completer votre profil.'),
(14, 14, '2025-03-14 14:00:00', 'Votre recompense est en cours de traitement.'),
(15, 1, '2025-03-15 15:00:00', 'Le tableau de bord a ete mis a jour.');

INSERT INTO INSCRIPTION (Id_User, Id_Ses, Statut_Insc, Date_Demande, Retrib_Valid) VALUES
(5,1,'Validee','2025-03-01 09:00:00',1),
(8,2,'Validee','2025-04-01 09:00:00',1),
(10,3,'Validee','2025-05-01 09:00:00',1),
(12,4,'Validee','2025-06-01 09:00:00',0),
(14,5,'Validee','2025-07-01 09:00:00',1),
(15,6,'Validee','2025-08-01 09:00:00',1),
(5,7,'Validee','2025-09-01 09:00:00',1),
(8,8,'Validee','2025-10-01 09:00:00',1),
(10,9,'Validee','2025-11-01 09:00:00',1),
(12,10,'Validee','2025-12-01 09:00:00',1),
(14,11,'Validee','2026-01-01 09:00:00',0),
(15,12,'Attente gestionnaire','2026-02-01 09:00:00',1),
(5,13,'Attente gestionnaire','2026-03-01 09:00:00',1),
(8,14,'En attente','2026-04-01 09:00:00',0),
(10,15,'En attente','2026-05-01 09:00:00',0),
(4, 16, 'En attente', NOW(), 0);

INSERT INTO RESERVATION (Id_Res, Id_Ses) VALUES
(1,1),(2,2),(3,3),(4,4),(5,5),
(6,1),(7,5),(8,3),(9,9),(10,10),
(1,11),(3,12),(5,13),(8,10),(12,13),(4,15);