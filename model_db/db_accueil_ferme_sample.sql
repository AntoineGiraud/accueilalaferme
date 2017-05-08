
INSERT INTO `group_link` (pk, slug, name, cat, is_male, is_adult, is_family) VALUES
(NULL, 'pere', 'Père', 'Parents', 1, 1, 1),
(NULL, 'mere', 'Mère', 'Parents', 0, 1, 1),
(NULL, 'fils', 'Fils', 'Enfants', 1, 0, 1),
(NULL, 'fille', 'Fille', 'Enfants', 0, 0, 1),
(NULL, 'friend_boy', 'Garçon', 'Autre', 1, 0, 0),
(NULL, 'friend_girl', 'Fille', 'Autre', 0, 0, 0),
(NULL, 'homme', 'Homme', 'Autre', 1, 1, 0),
(NULL, 'femme', 'Femme', 'Autre', 0, 1, 0);
