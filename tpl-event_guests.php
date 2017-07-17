<?php
/**
 * Template Name: Profil
 *
 * @package Sydney
 */
global $curPerson;

if (!is_admin() && !current_user_can('administrator'))
    \AccueilALaFerme\Flash::setFlashAndRedirect("Espace réservé à l'administration", 'warning', 'profil');

if (!empty($_GET['event_id']))
    $event_id = $_GET['event_id']*1;
else if (!empty($_POST['event_id']))
    $event_id = $_POST['event_id']*1;
if (!empty($event_id))
    $event = $DB->queryFirst("SELECT * FROM event WHERE pk = :pk", ['pk'=>$event_id]);
if (empty($event_id) || empty($event))
    \AccueilALaFerme\Flash::setFlashAndRedirect("Evénement inconnu", 'warning', 'profil');
else if (strtotime($event['end_date']) < time())
    \AccueilALaFerme\Flash::setFlashAndRedirect("Evénement terminé", 'warning', 'profil');

// Reservation options
$options = [];
$res = $DB->query("SELECT * FROM registration_options WHERE event_pk = :event_id", ['event_id' => $event_id]);
foreach ($res as $row) {
    if (!empty($row['parent_pk']) && !isset($options[$row['parent_pk']]))
        $options[$row['parent_pk']] = ['options' => [], 'pk' => $row['parent_pk']];
    else if (empty($row['parent_pk']) && !isset($options[$row['pk']]))
        $options[$row['pk']] = ['options' => [], 'pk' => $row['pk']];
    if (!empty($row['parent_pk']))
        $options[$row['parent_pk']]['options'][$row['pk']] = $row;
    else
        $options[$row['pk']]['prop'] = $row;
}

// Récupérer les inscriptions présentes
$personRegistrations = [];
$res = $DB->query("SELECT r.will_come, p.*, r.arrival_date, r.departure_date, r.register_date, r.comment, pg.group_id, g.name group_name, g.is_family
                    FROM person p
                        LEFT JOIN registration r ON p.pk = r.person_id
                        LEFT JOIN person_has_group pg ON p.pk = pg.person_id
                        LEFT JOIN groupe g ON g.pk = pg.group_id
                    where pg.was_removed is null
                    order by pg.group_id, pg.group_link_pk, p.pk"); // , ['event_id' => $event_id]); // WHERE -- event_id = :event_id
foreach ($res as $row) {
    $row['arrival_date'] = substr($row['arrival_date'], 0, 10);
    $row['departure_date'] = substr($row['departure_date'], 0, 10);
    if (!isset($personRegistrations[$row['pk']]))
        $personRegistrations[$row['pk']] = $row;
    else // on désactive la plus vieille inscriptions car doublon !
        $DB->query('UPDATE registration SET will_come = 0 WHERE pk = '.$row['pk']);
}


get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth" ng-app="app" ng-controller="PageCtrl">
		<main id="main" class="site-main hentry page" role="main" ng-controller="EventRegisterCtrl">
            <header class="entry-header">
                <h1 class="title-post entry-title"><?= $event['name'] ?></h1>
                <p>Du <?= substr($event['start_date'], 0, 10) ?> au <?= substr($event['end_date'], 0, 10) ?></p>
            </header>
            <div>
                <h4>Participants à l'événement <small><?= count($personRegistrations) ?></small></h4>
                <table class="table table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th>will come</th>
                            <th>mail</th>
                            <th>prénom</th>
                            <th>nom</th>
                            <th>anniversaire</th>
                            <th>téléphone</th>
                            <th>Options</th>
                            <th>arrivée</th>
                            <th>départ</th>
                            <th>enregistrement</th>
                            <th>groupe</th>
                            <th>nom groupe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($personRegistrations as $person): ?>
                                <tr>
                                    <td class="<?= $person['will_come'] == 1 ? 'success' : ($person['will_come']===null ? 'warning':'danger') ?>"><?= $person['will_come'] ?></td>
                                    <td><?= $person['email'] ?></td>
                                    <td><?= stripslashes($person['firstname']) ?></td>
                                    <td><?= stripslashes($person['lastname']) ?></td>
                                    <td><?= $person['birthday'] ?></td>
                                    <td><?= $person['phone'] ?></td>
                                    <td><?= empty($person['comment']) ? '' : implode(" ; ", json_decode($person['comment'], true)) ?></td>
                                    <td><?= $person['arrival_date'] ?></td>
                                    <td><?= $person['departure_date'] ?></td>
                                    <td><?= $person['register_date'] ?></td>
                                    <td><?= ($person['is_family'] ? 'famille' : 'groupe').' <code>'.$person['group_id'].'</code>' ?></td>
                                    <td><?= stripslashes($person['group_name']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tr>
                    </tbody>
                </table>
            </div>
		</main><!-- #main -->
	</div><!-- #primary -->
    <?php do_action('sydney_after_content'); ?>
<?php get_footer(); ?>