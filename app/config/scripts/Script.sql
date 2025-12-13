-- ====================== USERS =============================
-- admin : rôle admin
-- user : directeur du labo
USE TDW;
-- INSERT INTO users (username, password, nom, prenom, email, grade, poste, role, statut)
-- VALUES
-- ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'System', 'admin@lmcs.dz', 'Administrateur', 'Administrateur système', 'admin', 'actif'),
-- ('user', '$2y$10$0GeXHfQ5HXgGvQhxsd8neOP4x4H2KqX3MIcT8NdrklawEjc9RQFiy', 'Benali', 'Ahmed', 'j.doe@lmcs.dz', 'Professeur', 'Directeur du laboratoire', 'enseignant-chercheur', 'actif'),
-- ('jdupont', '$2y$10$CwTycUXWue0Thq9StjUM0uJ8bgQfVwBv1j0s8ZCoJj/6kFZ3Yx5e', 'Djabri', 'Samira', 'j.dupont@lmcs.dz', 'MCA', 'Chercheuse IA', 'enseignant-chercheur', 'actif');

-- ============================================================
-- Insertion des PARTENAIRES
-- ============================================================

-- INSERT INTO partenaires (nom, type, logo, site_web, description, pays, date_partenariat)
-- VALUES 
-- (
--    'Université de Paris-Saclay',
--    'universite',
--    'Logo_Universite_Paris-Saclay.png',
--    'https://www.universite-paris-saclay.fr',
--    'Partenariat stratégique dans le domaine de l\'intelligence artificielle et du machine learning. Collaboration sur des projets de recherche conjoints et échanges d\'étudiants doctorants.',
--    'France',
--    '2022-03-15'
-- ),
-- (
--    'Microsoft Research',
--    'entreprise',
--    'Microsoft_logo.png',
--    'https://www.microsoft.com/research',
--    'Partenariat technologique pour le développement d\'outils de cloud computing et d\'intelligence artificielle appliquée. Accès aux infrastructures Azure pour nos projets de recherche.',
--    'États-Unis',
--    '2023-01-10'
-- ),
-- (
--    'Centre National de Recherche Scientifique (CNRS)',
--    'organisme',
--    'Logo_CNRS.png',
--    'https://www.cnrs.fr',
--    'Collaboration institutionnelle pour le financement de projets de recherche fondamentale en informatique, robotique et vision par ordinateur.',
--    'France',
--    '2020-09-20'
-- ),
-- (
--    'Polytechnique Montréal',
--    'universite',
--    'polymtl_logo.png',
--    'https://www.polymtl.ca',
--    'Échanges académiques et projets de recherche collaborative en cybersécurité et réseaux intelligents. Programme de double diplôme pour doctorants.',
--    'Canada',
--    '2021-11-05'
-- ),
-- (
--    'Siemens Digital Industries',
--    'entreprise',
--    'siemens_logo.png',
--    'https://www.siemens.com/digital',
--    'Partenariat industriel axé sur l\'IoT, l\'industrie 4.0 et les systèmes cyber-physiques. Financement de thèses CIFRE et accès aux plateformes industrielles.',
--    'Allemagne',
--    '2022-06-18'
-- ),
-- (
--    'Agence Nationale de la Recherche (ANR)',
--    'organisme',
--    'anr_logo.png',
--    'https://anr.fr',
--    'Organisme de financement pour nos projets de recherche. Soutien financier obtenu pour plusieurs projets dans le domaine de l\'IA explicable et de la sécurité des systèmes.',
--    'France',
--    '2019-04-12'
-- );

-- ============================================================
-- Insertion des ACTUALITÉS
-- ============================================================

INSERT INTO actualites (titre, contenu, type_actualite_id, image, detail, date_publication, afficher_diaporama, ordre_diaporama)
VALUES 
(
    'Lancement du projet AICARE : Intelligence Artificielle pour la Santé',
    'Notre laboratoire annonce le lancement officiel du projet AICARE, financé par l\'ANR à hauteur de 1,2M€. Ce projet ambitieux vise à développer des algorithmes d\'apprentissage profond pour l\'aide au diagnostic médical. En collaboration avec le CHU local et l\'Université de Paris-Saclay, nous travaillerons sur l\'analyse d\'images médicales et la prédiction de pathologies. Le projet s\'étendra sur 4 ans et impliquera 3 doctorants, 2 post-doctorants et 5 chercheurs permanents.',
    1,
    'aicare_project.jpg',
    'Le projet AICARE (Artificial Intelligence for Computer-Aided medical caRE) représente une avancée significative dans l\'application de l\'intelligence artificielle au domaine médical. Doté d\'un financement de 1,2 million d\'euros sur 4 ans par l\'Agence Nationale de la Recherche, ce projet multidisciplinaire réunit des experts en IA, en imagerie médicale et en médecine clinique. Les objectifs principaux incluent le développement d\'algorithmes de deep learning capables d\'analyser automatiquement des images radiologiques, scanner et IRM pour assister les médecins dans le diagnostic précoce de pathologies complexes. Le projet bénéficiera de l\'expertise du CHU local qui mettra à disposition une base de données anonymisée de plus de 100 000 images médicales, ainsi que du partenariat avec l\'Université de Paris-Saclay pour l\'aspect recherche fondamentale. L\'équipe projet comprendra 3 doctorants travaillant sur différents aspects (architecture des réseaux neuronaux, traitement d\'images, validation clinique), 2 post-doctorants et 5 chercheurs permanents. Des comités d\'éthique ont déjà validé le protocole de recherche garantissant la protection des données patients.',
    '2025-11-15 10:00:00',
    TRUE,
    1
),
(
    'Publication majeure dans Nature Machine Intelligence',
    'Le Dr. Sarah Martin et son équipe publient leurs travaux révolutionnaires sur l\'IA explicable dans la prestigieuse revue Nature Machine Intelligence. L\'article intitulé "Transparent Deep Learning: A Novel Framework for Interpretable Neural Networks" présente une nouvelle approche permettant de comprendre les décisions prises par les réseaux de neurones profonds. Cette avancée majeure pourrait avoir des implications importantes pour l\'utilisation de l\'IA dans des domaines critiques comme la santé et la justice.',
    2, 
    'nature_publication.jpg',
    'L\'article publié dans Nature Machine Intelligence (Impact Factor: 25.898) marque une étape importante dans le domaine de l\'IA explicable (XAI). Les travaux du Dr. Sarah Martin et de son équipe de 8 chercheurs proposent un nouveau framework baptisé "TransparentDL" qui permet de visualiser et d\'interpréter les processus de décision des réseaux de neurones profonds en temps réel. Contrairement aux approches existantes qui se limitent à des explications post-hoc, TransparentDL intègre l\'interprétabilité dès la conception de l\'architecture neuronale. Les résultats expérimentaux montrent une amélioration de 34% de l\'interprétabilité sans perte significative de performance (moins de 2%). Cette innovation répond à une problématique cruciale : comment faire confiance aux décisions prises par l\'IA dans des contextes où les erreurs peuvent avoir des conséquences graves ? L\'article a déjà suscité un vif intérêt dans la communauté scientifique avec plus de 150 citations en pré-publication. Le code source sera publié en open-source sur GitHub, et une démonstration interactive sera présentée lors de la conférence NeurIPS 2026.',
    '2025-11-28 14:30:00',
    TRUE,
    2
),
( 
    'Conférence Internationale sur la Robotique Mobile - Inscriptions ouvertes',
    'Notre laboratoire a l\'honneur d\'accueillir la 8ème Conférence Internationale sur la Robotique Mobile (CIRM 2025) du 15 au 17 janvier 2026. Cet événement majeur réunira plus de 200 chercheurs du monde entier pour discuter des dernières avancées en robotique autonome, navigation intelligente et interaction homme-robot. Les inscriptions sont désormais ouvertes avec un tarif préférentiel jusqu\'au 20 décembre. Programme détaillé disponible sur le site de la conférence.',
    3,
    'cirm2025_banner.jpg',
    'La Conférence Internationale sur la Robotique Mobile (CIRM) est l\'un des événements phares dans le domaine de la robotique autonome. Pour sa 8ème édition, notre laboratoire est fier d\'accueillir plus de 200 participants internationaux provenant de 35 pays. Le programme scientifique s\'articule autour de 3 keynotes de chercheurs renommés : Prof. Wolfram Burgard (Université de Fribourg) sur le SLAM, Prof. Sethu Vijayakumar (Université d\'Édimbourg) sur l\'apprentissage par renforcement, et Dr. Cynthia Breazeal (MIT) sur l\'interaction homme-robot. La conférence comprendra 40 présentations orales sélectionnées parmi 180 soumissions (taux d\'acceptation de 22%), 60 posters, 4 ateliers thématiques et des démonstrations de robots en conditions réelles. Les thématiques couvrent la navigation autonome, la perception 3D, les systèmes multi-robots, la manipulation robotique et les applications industrielles. Les inscriptions early-bird sont ouvertes jusqu\'au 20 décembre 2025 avec une réduction de 30%. Une soirée de gala est prévue le 16 janvier au Centre de Congrès avec démonstrations robotiques et networking.',
    '2025-12-01 09:00:00',
    TRUE,
    3
),
(
    'Soutenance de thèse : Omar Benzaid - Deep Reinforcement Learning',
    'Omar Benzaid soutiendra sa thèse intitulée "Apprentissage par renforcement profond pour la navigation autonome de robots mobiles en environnements dynamiques" le vendredi 20 décembre 2025 à 14h00 dans l\'amphithéâtre Pierre Curie. Sous la direction du Pr. Jean Dupont, cette thèse présente des contributions significatives à l\'application du deep reinforcement learning dans des contextes robotiques complexes. La soutenance sera suivie d\'un pot de thèse. Tous les membres du laboratoire sont invités à y assister.',
    4,
    'soutenance_benzaid.jpg',
    'Omar Benzaid défendra sa thèse de doctorat en Informatique après 3 années de recherche intensive. Ses travaux portent sur l\'utilisation d\'algorithmes d\'apprentissage par renforcement profond (Deep Q-Learning, Proximal Policy Optimization, Soft Actor-Critic) pour permettre à des robots mobiles de naviguer de manière autonome dans des environnements dynamiques et imprévisibles. Les contributions principales incluent : (1) un nouvel algorithme hybride combinant apprentissage par renforcement et planification de trajectoire, (2) une approche de transfert d\'apprentissage permettant de réduire le temps d\'entraînement de 60%, (3) une validation expérimentale sur des robots réels (TurtleBot3, Jackal) dans différents scénarios (entrepôts, espaces publics). La thèse a donné lieu à 5 publications dans des conférences internationales (ICRA, IROS, RSS) et 2 articles de journal. Le jury est composé de 6 membres dont le Pr. Jean Dupont (directeur de thèse), Dr. Sophie Bernard (co-encadrante), Prof. Marc Toussaint (Université de Stuttgart, rapporteur), Prof. Angela Schoellig (Université de Toronto, rapporteure), et deux examinateurs. La soutenance aura lieu le 20 décembre 2025 à 14h00 en amphithéâtre Pierre Curie, suivie d\'un pot de thèse à 17h00.',
    '2025-12-04 08:30:00',
    FALSE,
    0
),
(
    'Nouveau partenariat stratégique avec IBM Research',
    'Le laboratoire est fier d\'annoncer la signature d\'un accord de collaboration majeur avec IBM Research pour une durée de 5 ans. Ce partenariat porte sur le développement de solutions d\'IA quantique et de calcul haute performance. IBM mettra à disposition ses infrastructures quantum computing et ses experts pour accompagner nos projets de recherche. Trois bourses de thèse seront financées dès 2026 dans le cadre de ce partenariat. Une première réunion de lancement est prévue le 15 janvier avec la visite d\'une délégation d\'IBM comprenant des chercheurs seniors et des responsables R&D. Ce partenariat ouvre des perspectives passionnantes pour nos travaux sur l\'optimisation combinatoire et la cryptographie quantique.',
    5,
    'ibm_partnership.jpg',
    'L\'accord de partenariat signé avec IBM Research représente une opportunité exceptionnelle pour notre laboratoire. D\'une durée de 5 ans renouvelable, ce partenariat stratégique permettra à nos chercheurs d\'accéder aux infrastructures de calcul quantique d\'IBM (IBM Quantum System One avec processeurs de 127 qubits) via le cloud IBM Quantum Network. Les axes de collaboration incluent : (1) Développement d\'algorithmes quantiques pour l\'optimisation combinatoire avec applications en logistique et planification, (2) Recherche en cryptographie post-quantique pour sécuriser les communications futures, (3) Exploration du machine learning quantique pour l\'accélération de l\'entraînement de modèles IA. IBM s\'engage à financer 3 bourses de thèse de 3 ans chacune (budget total de 450 000€), à détacher 2 chercheurs seniors pour co-encadrer les travaux, et à organiser 2 workshops annuels réunissant chercheurs académiques et industriels. Notre laboratoire contribuera par son expertise en algorithmique et intelligence artificielle. La réunion de lancement prévue le 15 janvier 2026 sera l\'occasion de définir précisément les premiers sujets de thèse et d\'établir la feuille de route pour les 18 premiers mois. Ce partenariat positionne notre laboratoire comme acteur clé de la recherche en informatique quantique en France.',
    '2025-12-05 11:00:00',
    TRUE,
    4
),
(
    'Trois doctorants du laboratoire primés au Concours National de Thèses',
    'Excellente nouvelle pour notre laboratoire ! Trois de nos doctorants ont été récompensés lors du 12ème Concours National de Thèses en Informatique organisé par l\'Association Française d\'Informatique (AFI). Leila Amrani a remporté le Prix d\'Excellence pour ses travaux sur les réseaux de neurones génératifs appliqués à la synthèse d\'images médicales. Karim Bencheikh a obtenu le 2ème prix dans la catégorie "Systèmes Intelligents" pour sa thèse sur les algorithmes d\'apprentissage fédéré. Enfin, Fatima Zohra Kaci a reçu une mention spéciale du jury pour ses recherches innovantes en traitement automatique du langage naturel pour les langues peu dotées. Ces distinctions témoignent de l\'excellence de la recherche menée au sein de notre équipe et du talent de nos jeunes chercheurs. Toutes nos félicitations à eux ainsi qu\'à leurs directeurs de thèse !',
    2,
    'concours_theses.jpg',
    'Le 12ème Concours National de Thèses en Informatique, organisé par l\'Association Française d\'Informatique (AFI), a récompensé l\'excellence de trois doctorants de notre laboratoire parmi 250 candidatures. Leila Amrani a reçu le Prix d\'Excellence (dotation de 5000€) pour sa thèse "Réseaux Adverses Génératifs Conditionnels pour la Synthèse Réaliste d\'Images Médicales". Ses travaux permettent de générer des images médicales synthétiques de haute qualité pour augmenter les bases de données d\'entraînement, tout en préservant la confidentialité des patients. Son approche a été validée sur des données de scanner et IRM avec une qualité jugée équivalente aux images réelles par des radiologues experts. Karim Bencheikh a obtenu le 2ème prix (3000€) dans la catégorie "Systèmes Intelligents" pour sa thèse sur l\'apprentissage fédéré, proposant un algorithme innovant permettant d\'entraîner des modèles d\'IA distribués tout en garantissant la confidentialité différentielle. Fatima Zohra Kaci a reçu une mention spéciale pour ses recherches pionnières sur le traitement automatique de l\'amazigh et de l\'arabe algérien, langues traditionnellement peu représentées dans les technologies du langage. La cérémonie de remise des prix aura lieu le 15 janvier 2026 à Paris.',
    '2025-12-03 16:45:00',
    FALSE,
    0
);

-- ============================================================
-- Insertion des ÉVÉNEMENTS
-- ============================================================

-- INSERT INTO evenements (titre, type_evenement_id, description, lieu, date_debut, date_fin, organisateur_id, capacite_max, is_Scientifique, statut)
-- VALUES 
-- (
--    'Atelier Pratique : Introduction à TensorFlow 2.0',
--    1, -- atelier
--    'Atelier de formation pratique destiné aux doctorants et étudiants en master. Au programme : installation et configuration de TensorFlow, construction de réseaux de neurones, entraînement de modèles et déploiement. Les participants travailleront sur des cas pratiques concrets avec des jeux de données réels. Prérequis : connaissances de base en Python et apprentissage automatique. Places limitées à 25 participants.',
--    'Salle de TP A-304',
--    '2025-12-16 09:00:00',
--    '2025-12-16 17:00:00',
--    NULL,
--    25,
--    FALSE,
--    'a_venir'
-- ),
-- (
--    'Séminaire : Éthique et IA - Enjeux et Perspectives',
--    2,
--    'Séminaire animé par le Pr. Marie Lefebvre, philosophe spécialisée en éthique des technologies. Discussion sur les implications éthiques de l\'intelligence artificielle : biais algorithmiques, transparence des décisions automatisées, protection de la vie privée et impact sociétal. Session de questions-réponses et débat ouvert avec le public.',
--    'Amphithéâtre Marie Curie',
--    '2025-12-10 14:00:00',
--    '2025-12-10 16:30:00',
--    NULL,
--    150,
--    TRUE,
--    'a_venir'
-- ),
-- (
--    'Conférence Internationale sur la Robotique Mobile (CIRM 2025)',
--    3,
--    '8ème édition de la conférence internationale dédiée à la robotique mobile. Thématiques : navigation autonome, SLAM, perception 3D, apprentissage par renforcement pour robots, systèmes multi-robots. 3 keynotes de chercheurs renommés, 40 présentations orales, 60 posters, ateliers techniques et démonstrations de robots. Soirée de gala le 16 janvier.',
--    'Centre de Congrès Universitaire',
--    '2026-01-15 08:30:00',
--    '2026-01-17 18:00:00',
--    NULL,
--    250,
--    TRUE,
--    'a_venir'
-- ),
-- (
--    'Soutenance de thèse : Omar Benzaid',
--    4,
--    'Thèse de doctorat en Informatique - Spécialité : Intelligence Artificielle et Robotique. Titre : "Apprentissage par renforcement profond pour la navigation autonome de robots mobiles en environnements dynamiques". Jury composé de 6 membres dont 2 rapporteurs internationaux. Direction de thèse : Pr. Jean Dupont. Co-encadrement : Dr. Sophie Bernard.',
--    'Amphithéâtre Pierre Curie',
--    '2025-12-20 14:00:00',
--    '2025-12-20 17:00:00',
--    NULL,
--    80,
--    TRUE,
--    'a_venir'
-- ),
-- (
--    'Journée Portes Ouvertes du Laboratoire',
--    1,
--    'Découvrez nos activités de recherche ! Visites guidées des plateformes expérimentales, démonstrations de robots autonomes, présentation des projets en cours, rencontres avec les chercheurs et doctorants. Événement ouvert au grand public, lycéens et étudiants. Ateliers ludiques pour découvrir l\'IA et la programmation. Restauration sur place.',
--    'Bâtiment du Laboratoire - Tous les espaces',
--    '2026-01-25 10:00:00',
--    '2026-01-25 18:00:00',
--    NULL,
--    200,
--    FALSE,
--    'a_venir'
-- ),
-- (
--    'Workshop : Computer Vision et Deep Learning',
--    1,
--    'Workshop intensif de 2 jours sur la vision par ordinateur avec deep learning. Jour 1 : CNNs, architectures modernes (ResNet, EfficientNet, Vision Transformers). Jour 2 : Détection d\'objets (YOLO, Faster R-CNN), segmentation sémantique, applications pratiques. Intervenants : Dr. Pierre Rousseau et Dr. Alice Chen. Certificat de participation délivré.',
--    'Salle de formation B-201',
--    '2026-02-10 09:00:00',
--    '2026-02-11 17:00:00',
--    NULL,
--    30,
--    TRUE,
--    'a_venir'
-- );