<?php
/**
 * Template Name: Form event registration
 *
 * @package accueilalaferme
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

$group_id = !empty($curPerson->groups) ? current(array_keys($curPerson->groups)) : null;
$curGroup = ($group_id && in_array($group_id, $curPerson->canManageGroupIds)) ? new \AccueilALaFerme\Group($group_id, $DB) : null;
$persons = $curGroup ? array_values($curGroup->persons) : [$curPerson->data];
$person_ids = array_map(function($v){ return $v['pk']; }, $persons);

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
$res = $DB->query("SELECT * FROM registration WHERE event_id = :event_id AND person_id IN (".implode(',', $person_ids).") ORDER BY update_date DESC", ['event_id' => $event_id]);
foreach ($res as $row) {
    $row['arrival_date'] = substr($row['arrival_date'], 0, 10);
    $row['departure_date'] = substr($row['departure_date'], 0, 10);
    if (!isset($personRegistrations[$row['person_id']]))
        $personRegistrations[$row['person_id']] = $row;
    else // on désactive la plus vieille inscriptions car doublon !
        $DB->query('UPDATE registration SET will_come = 0 WHERE pk = '.$row['pk']);
}

$curPersonResa = isset($personRegistrations[$curPerson->data['pk']]) ? $personRegistrations[$curPerson->data['pk']] : [];
if (!empty($curPersonResa['comment']))
    $curPersonResa['comment'] = json_decode($curPersonResa['comment'], true);
foreach ($persons as $k => $val) {
    if (isset($personRegistrations[$val['pk']])) {
        $resa = $personRegistrations[$val['pk']];
        $persons[$k]['will_come'] = $resa['will_come']*1;
        $persons[$k]['arrival_date'] = substr($resa['arrival_date'], 0, 10);
        $persons[$k]['departure_date'] = substr($resa['departure_date'], 0, 10);
    }
}

if (!empty($_POST)) {
    $error_msg = [];
    $person_ids = [];
    foreach ($_POST['persons'] as $key => $person) {
        $_POST['persons'][$key]['will_come'] = !empty($person['will_come'])*1;
        if (empty($person['pk'] || empty($group_id) && $curPerson->data['pk'] != $person['pk']) || !empty($curGroup) && !isset($curGroup->persons[$person['pk']]))
            $error_msg['Personne(s) inconnue(s)...'] = 1;
        else if (!empty($person['will_come'])) {
            $persons[$key]['errors'] = [];
            if (empty($person['departure_date'])) {
                $error_msg["Indiquez les dates d'arrivée et départ"] = 1;
                $persons[$key]['errors']['departure_date'] = true;
            } else if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $person['departure_date'])) {
                $error_msg["Mauvais format de date"] = 1;
                $persons[$key]['errors']['departure_date'] = true;
            }
            if (empty($person['arrival_date'])) {
                $error_msg["Indiquez les dates d'arrivée et départ"] = 1;
                $persons[$key]['errors']['arrival_date'] = true;
            } else if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $person['arrival_date'])) {
                $error_msg["Mauvais format de date"] = 1;
                $persons[$key]['errors']['arrival_date'] = true;
            } else if (strtotime($person['arrival_date']) > strtotime($person['departure_date'])) {
                $error_msg["Date d'arrivée postérieure à la date de départ."] = 1;
                $persons[$key]['errors']['arrival_date'] = true;
            }
        } else {
            $_POST['persons'][$key]['arrival_date'] = null;
            $_POST['persons'][$key]['departure_date'] = null;
            $person_ids[] = $person['pk'];
        }
    }
    if (empty($error_msg)) {
        $maj = false;
        foreach ($_POST['persons'] as $key => $new) {
            $comment = $new['pk'] == $curPerson->data['pk'] ? json_encode($_POST['attr']) : null;

            if (isset($personRegistrations[$new['pk']])) { // update address
                $old = $personRegistrations[$new['pk']];
                $update = [];
                $d = [];
                if ($new['will_come']*1 !== $old['will_come']*1) $update['will_come'] = 'will_come = '.($new['will_come']*1).'';
                if ($new['arrival_date'] !== $old['arrival_date']) $update['arrival_date'] = 'arrival_date = '.(empty($new['arrival_date'])?'null':'"'.$new['arrival_date'].'"').'';
                if ($new['departure_date'] !== $old['departure_date']) $update['departure_date'] = 'departure_date = '.(empty($new['departure_date'])?'null':'"'.$new['departure_date'].'"').'';
                if ($comment !== $old['comment']) {$update['comment'] = 'comment = :comment'; $d['comment'] = $comment;}
                if (!empty($update)) {
                    $DB->query("UPDATE registration SET ".implode(', ', $update).", update_date=NOW() WHERE pk = ".$old['pk'], $d);
                    $maj = true;
                }
            } else if ($new['will_come']) { // Insert address
                $new['pk'] = $DB->query(
                        "INSERT INTO `registration` (`event_id`, `person_id`, `will_come`, `arrival_date`, `departure_date`, `register_date`, `update_date`, `comment`)
                        VALUES (:event_id, :person_id, :will_come, :arrival_date, :departure_date, NOW(), NOW(), :comment)", [
                            'event_id' => $event_id,
                            'person_id' => $new['pk'],
                            'will_come' => $new['will_come']*1,
                            'arrival_date' => empty($new['arrival_date']) ? null : $new['arrival_date'],
                            'departure_date' => empty($new['departure_date']) ? null : $new['departure_date'],
                            'comment' => empty($comment) ? null : $comment
                        ]);
                $maj = true;
            }
        }
        if ($maj)
            \AccueilALaFerme\Flash::setFlashAndRedirect("Sauvegarde effectuée de l'inscription à <code>".$event['name']."</code>", 'success', 'profil');
        else
            \AccueilALaFerme\Flash::setFlashAndRedirect("Pas de mise à jour", 'info', 'profil');
    } else
        $error_msg = implode('<br>', array_keys($error_msg));
}

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth" ng-app="app" ng-controller="PageCtrl">
		<main id="main" class="site-main hentry page" role="main" ng-controller="EventRegisterCtrl">
            <header class="entry-header">
                <h1 class="title-post entry-title"><?= $event['name'] ?> <?php if (current_user_can('administrator')): ?>
                    <small><a href="<?= get_bloginfo('url').'/event_guests?event_id='.$event['pk'] ?>" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-list"></span> Liste invités</a></small>
                <?php endif ?></h1>
                <p>Du <?= substr($event['start_date'], 0, 10) ?> au <?= substr($event['end_date'], 0, 10) ?></p>
            </header>
                <?php if (!empty($error_msg)): ?>
                    <p class="alert alert-danger"><?= $error_msg ?></p>
                <?php endif ?>
                <p class="alert alert-warning">N.B. : Pour les familles n’ayant jamais vécu un des événements à la ferme, nous vous demandons de prendre contact personnellement avec nous avant de vous inscrire... (418)289-3705 Merci.</p>

                <form class="form-horizontal" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                    <fieldset>
                        <legend>Participants <small><a href="<?= get_bloginfo('url').'/famille?event_id='.$event['pk'] ?>" class="btn btn-info btn-xs"><?= empty($curGroup)?'Créer groupe/famille':'Editer '.($curGroup->prop['is_family']?'famille':'groupe') ?></a></small></legend>
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Lien</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Anniversaire</th>
                                    <th>Arrivée</th>
                                    <th>Départ</th>
                                    <th>Participation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="member in group.persons track by $index" ng-class="{'success':member.will_come}">
                                    <td ng-class="{'success':'pere' == member.link || 'mere' == member.link || 'fils' == member.link || 'fille' == member.link, 'warning':'pere' != member.link && 'mere' != member.link && 'fils' != member.link && 'fille' != member.link}">
                                        <input type="hidden" name="persons[{{$index}}][pk]" value="{{member.pk}}">
                                        {{member.link}}
                                    </td>
                                    <td>{{member.firstname}}</td>
                                    <td>{{member.lastname}}</td>
                                    <td>{{member.birthday}}</td>
                                    <td ng-class="{'has-error':member.errors.arrival_date}">
                                        <p class="input-group" ng-init="arr_cal_open = false;">
                                          <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" ng-click="arr_cal_open=true;"><i class="glyphicon glyphicon-calendar"></i></button>
                                          </span>
                                          <input type="text" maxlength="10" ng-focus="arr_cal_open=true;" name="persons[{{$index}}][arrival_date]" ng-model="member.arrival_date" class="form-control" uib-datepicker-popup ng-model="dt" is-open="arr_cal_open" datepicker-options="dateOptions" close-text="Close" placeholder="yyyy-mm-dd" ng-disabled="!member.will_come"/>
                                        </p>
                                    </td>
                                    <td ng-class="{'has-error':member.errors.departure_date}">
                                        <p class="input-group" ng-init="dep_cal_open = false;">
                                          <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" ng-click="dep_cal_open=true;"><i class="glyphicon glyphicon-calendar"></i></button>
                                          </span>
                                          <input type="text" maxlength="10" ng-focus="dep_cal_open=true;" name="persons[{{$index}}][departure_date]" ng-model="member.departure_date" class="form-control" uib-datepicker-popup ng-model="dt" is-open="dep_cal_open" datepicker-options="dateOptions" close-text="Close" placeholder="yyyy-mm-dd" ng-disabled="!member.will_come"/>
                                        </p>
                                    </td>
                                    <td><label>
                                            <input type="hidden" value="{{member.will_come*1}}" ng-model="member.will_come" name="persons[{{$index}}][will_come]">
                                            <a class="btn btn-xs btn-success" ng-click="member.will_come=!member.will_come" ng-show="member.will_come">oui</a>
                                            <a class="btn btn-xs btn-danger" ng-click="member.will_come=!member.will_come" ng-hide="member.will_come">non</a>
                                    </label></td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend>Autre</legend>
                        <?php foreach ($options as $pk => $opt):
                            $curResaOption = !empty($curPersonResa['comment'][$opt['prop']['slug']]) ? $curPersonResa['comment'][$opt['prop']['slug']] : '';?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="<?= $opt['prop']['slug'] ?>"><?= $opt['prop']['name'] ?></label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="attr[<?= $opt['prop']['slug'] ?>]">
                                        <?php foreach ($opt['options'] as $o): ?>
                                            <option value="<?= $o['slug'] ?>" <?= $curResaOption == $o['slug'] ? 'selected' : '' ?>><?= $o['name'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <?php if (!empty($opt['prop']['description'])): ?>
                                        <span class="helt-text"><?= $opt['prop']['description'] ?></span>
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endforeach ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="comment">Commentaire</label>
                            <div class="col-sm-10">
                                <textarea name="attr[comment]" class="form-control" id="comment" rows="5" placeholder="Commentaire à notre intention"><?= !empty($curPersonResa['comment']['comment']) ? stripslashes($curPersonResa['comment']['comment']) : '' ?></textarea>
                                <span class="helt-text">Allergies , besoins spécifiques (électricité requise, aide pour parents seuls,  etc.)/.<br>Si l’un de vos enfants aimerait participer à la musique pour les messes ou si vous pensez préparer une animation pour le vendredi soir.</span>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success">Sauvegarder</button>
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
        ]).';
        var start_date = "'.substr($event['start_date'], 0, 10).'";
        var end_date = "'.substr($event['end_date'], 0, 10).'";',
        'angularjs_accueilalaferme/controllers/EventRegisterCtrl.js'
      ];
    do_action('sydney_after_content'); ?>
<?php get_footer(); ?>