<?php
/**
 * Template Name: List event guests
 *
 * @package accueilalaferme
 */
global $curPerson;
$blogUrl = get_bloginfo('url');

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
$event['start_date'] = substr($event['start_date'], 0, 10);
$event['end_date'] = substr($event['end_date'], 0, 10);

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
                        LEFT JOIN person_has_group pg ON p.pk = pg.person_id and pg.was_removed is null
                        LEFT JOIN groupe g ON g.pk = pg.group_id
                    where event_id = :event_id and r.will_come=1
                    order by pg.group_id, pg.group_link_pk, p.pk", ['event_id' => $event_id]);
foreach ($res as $row) {
    $row['arrival_date'] = substr($row['arrival_date'], 0, 10);
    $row['departure_date'] = substr($row['departure_date'], 0, 10);
    $row['age'] = getAge($row['birthday'], $row['departure_date']);

    if (!isset($personRegistrations[$row['pk']]))
        $personRegistrations[$row['pk']] = $row;
    else // on désactive la plus vieille inscriptions car doublon !
        $DB->query('UPDATE registration SET will_come = 0 WHERE pk = '.$row['pk']);
}

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth" ng-app="app" ng-controller="PageCtrl">
		<main ng-controller="EventListCtrl" id="main" class="site-main hentry page" role="main" >
            <header class="entry-header">
                <h1 class="title-post entry-title"><?= $event['name'] ?></h1>
                <p>Du <?= $event['start_date'] ?> au <?= $event['end_date'] ?></p>
            </header>
            <div>
                <h4>Participants à l'événement <small>{{count}}/<?= count($personRegistrations) ?></small></h4>
                <div class="row form-group">
                    <h5>Filtre sur l'âge :</h5>
                    <div class="form-group">
                        <div class="col-sm-4 row">
                            <div class="col-sm-5">
                                <label class="col-sm-2 control-label text-nowrap" for="age_debut">Borne inférieure Âge</label>
                            </div>
                            <div class="col-sm-4">
                                <input id="age_debut" type="number" ng-model="filter.age_debut" name="age_debut">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4 row">
                            <div class="col-sm-5">
                                <label class="col-sm-2 control-label text-nowrap" for="age_fin">Borne supérieure Âge</label>
                            </div>
                            <div class="col-sm-4">
                                <input id="age_fin" type="number" ng-model="filter.age_fin" name="age_fin">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <h5>Filtre sur les dates :</h5>
                    <div class="form-group">
                        <div class="col-sm-3 row">
                            <div class="col-sm-5">
                                <label class="col-sm-2 control-label text-nowrap" for="arrivee">Arrivée le</label>
                            </div>
                            <div class="col-sm-4">
                                <input min="<?=$event['start_date']?>" max="<?=$event['end_date']?>" ng-model="filter.arrivee" id="arrivee" type="date" name="arrivee">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3 row">
                            <div class="col-sm-5">
                                <label class="col-sm-2 control-label text-nowrap" for="depart">Départ le</label>
                            </div>
                            <div class="col-sm-4">
                                <input min="<?=$event['start_date']?>" max="<?=$event['end_date']?>" ng-model="filter.depart" id="depart" type="date" name="depart">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3 row">
                            <div class="col-sm-5">
                                <label class="col-sm-2 control-label text-nowrap" for="present">Présent le</label>
                            </div>
                            <div class="col-sm-4">
                                <input min="<?=$event['start_date']?>" max="<?=$event['end_date']?>" ng-model="filter.present" id="present" type="date" ng-model="filter.present" name="present">
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

                <table class="table table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th>mail</th>
                            <th>prénom</th>
                            <th>nom</th>
                            <th>anniversaire</th>
                            <th>téléphone</th>
                            <th>Options</th>
                            <th>arrivée</th>
                            <th>départ</th>
                            <th>enregistrement</th>
                            <th>groupe/famille</th>
                            <th>nom groupe</th>
                            <th>éditer</th>
                        </tr>
                    </thead>
                    <tbody ng-init="show={show:true}">
                        <tr ng-repeat="person in persons | filter:show">
                            <td>{{ person['email'] }}</td>
                            <td>{{ person['firstname'] }}</td>
                            <td>{{ person['lastname'] }}</td>
                            <td>{{ person['birthday']}} <em>({{person['age']}})</em></td>
                            <td>{{ person['phone'] }}</td>
                            <td>{{ person['comment'] }}</td>
                            <td>{{ person['arrival_date'] }}</td>
                            <td>{{ person['departure_date'] }}</td>
                            <td>{{ person['register_date'] }}</td>
                            <td>{{ person['is_family'] }} <code>{{ person['group_id'] }} </code></td>
                            <td>{{ person['group_name'] }}</td>
                            <td><a type="button" href="<?= $blogUrl ?>/event/register?event_id=2&user_id={{person['pk']}}" class="glyphicon glyphicon-edit"></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
		</main><!-- #main -->
	</div><!-- #primary -->
    <?php
      global $js_for_layout;
      $js_for_layout = [
        'var persons = ' . json_encode(array_values(array_map(function($d){
            foreach (['firstname','lastname','group_name'] as $field)
                $d[$field] = stripslashes($d[$field]);
            $d['comment'] = empty($d['comment']) ? '' : stripslashes(implode(" ; ", json_decode($d['comment'], true)));
            $d['is_family'] = $d['is_family'] === null ? 'Individuel' : ($d['is_family'] ==1 ? 'famille' : 'groupe');
            $d['show']=true;
            return $d;
        }, $personRegistrations))) . ';',
        'angularjs',
        'angularjs_accueilalaferme/app.js',
        'angularjs_accueilalaferme/controllers/PageCtrl.js',
        'angularjs_accueilalaferme/controllers/EventListCtrl.js'
      ];
    do_action('sydney_after_content'); ?>
<?php get_footer(); ?>