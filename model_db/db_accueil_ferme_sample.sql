
INSERT INTO `group_link` (pk, slug, name, cat, is_male, is_adult, is_family) VALUES
(NULL, 'pere', 'Père', 'Parents', 1, 1, 1),
(NULL, 'mere', 'Mère', 'Parents', 0, 1, 1),
(NULL, 'fils', 'Fils', 'Enfants', 1, 0, 1),
(NULL, 'fille', 'Fille', 'Enfants', 0, 0, 1),
(NULL, 'friend_boy', 'Garçon', 'Autre', 1, 0, 0),
(NULL, 'friend_girl', 'Fille', 'Autre', 0, 0, 0),
(NULL, 'homme', 'Homme', 'Autre', 1, 1, 0),
(NULL, 'femme', 'Femme', 'Autre', 0, 1, 0);


INSERT INTO `event_cat` (`pk`, `slug`, `name`, `description`) VALUES
(1, 'camping-des-familles', 'Camping des familles', NULL),
(2, 'montee-pascale', 'Montée Pascale', NULL),
(3, 'toussaint', 'Toussaint', NULL),
(4, 'reveillon', 'Réveillon - Jour de l''an', NULL),
(5, 'noel', 'Noël', NULL);

INSERT INTO `event` (`pk`, `slug`, `name`, `start_date`, `end_date`, `description`, `event_cat_pk`) VALUES
(NULL, 'paques-2016', 'Pâques 2016', '2017-04-13 15:00:00', '2017-04-17 15:00:00', NULL, '2'),
(NULL, 'camping-familles-2017', 'Camping des familles 2017', '2017-07-27 14:00:00', '2017-07-30 15:00:00', NULL, '1');