<?php
/**
 * Template Name: Profil
 *
 * @package Sydney
 */
global $curPerson;

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

if (!empty($curPerson->groups))
    $group_id = current(array_keys($curPerson->groups));
else $group_id = null;
if ($group_id && in_array($group_id, $curPerson->canManageGroupIds))
    $curGroup = new \AccueilALaFerme\Group($group_id, $DB);
else $curGroup = null;

if ($curGroup)
    $persons = $curGroup->persons;
else
    $persons = [$curPerson->data];


get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth" ng-app="app" ng-controller="PageCtrl">
		<main id="main" class="site-main hentry page" role="main" ng-controller="EventRegisterCtrl">
            <header class="entry-header">
                <h1 class="title-post entry-title"><?= $event['name'] ?></h1>
                <p>Du <?= substr($event['start_date'], 0, 10) ?> au <?= substr($event['end_date'], 0, 10) ?></p>
            </header>
                <?php if (!empty($error_msg)): ?>
                    <p class="alert alert-danger"><?= $error_msg ?></p>
                <?php endif ?>

                <form class="form-horizontal" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                    <fieldset>
                        <legend>Participants</legend>
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Lien</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Anniversaire</th>
                                    <th>Participation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="member in group.persons track by $index" ng-class="{'success':member.pk==<?= $curPerson->data['pk'] ?>}">
                                    <td>
                                        <input type="hidden" name="persons[{{$index}}][pk]" value="{{member.pk}}">
                                        {{member.link}}
                                    </td>
                                    <td>{{member.firstname}}</td>
                                    <td>{{member.lastname}}</td>
                                    <td>{{member.birthday}}</td>
                                    <td><label>
                                            <input type="checkbox" value="1" ng-model="member.will_come" name="persons[{{$index}}][will_come]">
                                    </label></td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                    <hr>
                    <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success">Mettre à jour</button>
                      </div>
                    </div>
                </form>
		</main><!-- #main -->
	</div><!-- #primary -->
    <?php
      global $js_for_layout;
      $js_for_layout = [
        'angularjs',
        'angularjs_accueilalaferme/app.js',
        'angularjs_accueilalaferme/controllers/PageCtrl.js',
        'var groupData = '.json_encode([
            'persons' => $persons
        ]).';',
        'angularjs_accueilalaferme/controllers/EventRegisterCtrl.js'
      ];
    do_action('sydney_after_content'); ?>
<?php get_footer(); ?>