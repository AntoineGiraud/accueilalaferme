<?php
/**
 * Template Name: Form edit family/group
 *
 * @package accueilalaferme
 */
global $curPerson;

$editSomeoneElse = false;
if(!empty($_GET['user_id'])) {
    if(is_admin() || current_user_can('administrator'))
        if($_GET['user_id'] != $curPerson->data['pk']) {
            $editSomeoneElse = true;
            $curPerson = new \AccueilALaFerme\User($DB, $_GET['user_id'], null);
        }
}
if (!empty($_GET['group_id']))
    $group_id = $_GET['group_id']*1;
else if (!empty($curPerson->groups))
    $group_id = current(array_keys($curPerson->groups));
else
    $group_id = null;
if ($group_id) {
    if(is_admin() || current_user_can('administrator'))
        $curGroup = new \AccueilALaFerme\Group($group_id, $DB);
    elseif (in_array($group_id, $curPerson->canManageGroupIds))
        $curGroup = new \AccueilALaFerme\Group($group_id, $DB);
    else if (isset($curPerson->groups[$group_id]))
        \AccueilALaFerme\Flash::setFlashAndRedirect("Vous n'avez pas les droits d'édition de votre groupe.", 'warning', 'profil');
    else
        \AccueilALaFerme\Flash::setFlashAndRedirect("Pas de groupe trouvé avec l'id #".$group_id, 'warning', 'profil');
} else
    $curGroup = new \AccueilALaFerme\Group($group_id, $DB, $curPerson);


if (!empty($_POST)) {
    if (!empty($_POST['group_id']))
        $group_id = $_POST['group_id']*1;
    try {
        $curGroup->saveGroup($_POST);
        // Porter vers wordpress les MAJ dans les personnes
        if( (!is_admin() && !current_user_can('administrator')) || (!isset($_GET['user_id']) && !isset($_GET['group_id'])) )
            if (isset($curGroup->persons[$curPerson->data['pk']])) {
                $majCurUser = $curGroup->persons[$curPerson->data['pk']];
                $update = [];
                $userWP = wp_get_current_user();
                if ($userWP->user_email != $majCurUser['email'])
                    $update['user_email'] = $majCurUser['email'];
                if ($userWP->first_name != $majCurUser['firstname'])
                    $update['first_name'] = $majCurUser['firstname'];
                if ($userWP->last_name != $majCurUser['lastname'])
                    $update['last_name'] = $majCurUser['lastname'];
                if (!empty($update)) {
                    $update['ID'] = $userWP->ID;
                    $res = wp_update_user( $update );
                    if ( is_wp_error( $res ) ) { // rollback
                        $error_msg = $res->get_error_message();
                        $curPerson = new \AccueilALaFerme\User($DB, $curPerson->data['pk'], $userWP->user_email, $userWP->first_name, $userWP->last_name);
                        header('Location:'.$root.'family');die();
                    }
                }
            }
        if(is_admin() || current_user_can('administrator')) {
            $url_get_link = empty($_GET['event_id']) ? "?" : "&";
            if(isset($_GET['user_id']))
                $url_extension = $url_get_link . "user_id=" . $_GET['user_id'];
            elseif(isset($_GET['group_id'])) {
                $url_extension = $url_get_link . "user_id=";
                foreach($curGroup->persons as $person)
                    if($person['can_manage'] ==1) {
                        $url_extension .= $person['pk'];
                        break;
                    }
            }
            else
                $url_extension = '';
        }
        else
            $url_extension = '';

        $url = (!empty($_GET['event_id']) ? 'event/register?event_id='.$_GET['event_id'] : 'profil') . $url_extension;
        header('Location:'.$root.$url);
        die();
    } catch (Exception $e) {
        $error_msg = $e->getMessage();
        $e = $curGroup->errors['other'];
        if (!empty($e['persons_errors']))
            $e['persons_errors'] = 'Erreurs champs personnes : '.implode(', ', array_keys($e['persons_errors']));
        $error_msg .= '<br>'.implode('<br>', $e);
    }
}

$editSomeoneElse = !isset($curGroup->persons[$curPerson->data['pk']]);

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth" ng-app="app" ng-controller="PageCtrl">
		<main id="main" class="site-main hentry page" role="main" ng-controller="FamilyCtrl">
            <header class="entry-header">
                <h1 class="title-post entry-title"><?= empty($editSomeoneElse) ? "{{{'1':'Ma famille', '0':'Mon groupe'}[group.prop.is_family]}}" : '<span class="text-warning">Editer '.($curGroup->is_family?'Famille':'Groupe').' '.$curGroup->prop['name'].'</span>' ?></h1>
            </header>
                <?php if (!empty($error_msg)): ?>
                    <p class="alert alert-danger"><?= $error_msg ?></p>
                <?php endif ?>

				<div class="page-content">
                    <p>Vous pouvez mettre à jour les informations de votre {{{'1':'famille', '0':'groupe'}[group.prop.is_family]}} depuis cette page.</p>
				</div><!-- .page-content -->

                <form class="form-horizontal" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                    <fieldset>
                        <legend>Description générale {{{'1':'de la famille', '0':'du groupe'}[group.prop.is_family]}} <em>{{group.prop.name}}</em></legend>
                        <input type="hidden" name="pk" value="{{group.prop.pk}}">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Statut</label>
                            <div class="col-sm-10">
                                <label class="radio-inline">
                                  <input type="radio" name="is_family" ng-model="group.prop.is_family" value="1"> Famille
                                </label>
                                <label class="radio-inline">
                                  <input type="radio" name="is_family" ng-model="group.prop.is_family" value="0"> Groupe
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="name">Nom</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" id="name" placeholder="Age" ng-model="group.prop.name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="phone">Téléphone</label>
                            <div class="col-sm-10">
                                <input type="text" name="phone" class="form-control" id="phone" placeholder="Téléphone" ng-model="group.prop.phone">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Adresse</label>
                            <input type="hidden" name="address[pk]" value="{{group.prop.address.pk}}">
                            <div class="col-sm-10">
                              <div class="row">
                                <div class="col-sm-8">
                                    <input type="text" name="address[street]" title="address : street" class="form-control" placeholder="Adresse" ng-model="group.prop.address.street">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="address[city]" title="address : city" class="form-control" placeholder="Ville" ng-model="group.prop.address.city">
                                </div>
                              </div><br>
                              <div class="row">
                                <div class="col-sm-4">
                                    <input type="text" name="address[postal_code]" title="address : postal_code" class="form-control" placeholder="Code postal" ng-model="group.prop.address.postal_code">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="address[region]" title="address : region" class="form-control" placeholder="Région" ng-model="group.prop.address.region">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="address[country]" title="address : country" class="form-control" placeholder="Pays" ng-model="group.prop.address.country">
                                </div>
                              </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Composition {{{'1':'de la famille', '0':'du groupe'}[group.is_family]}}</legend>
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Lien</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Anniversaire</th>
                                    <th>Droits<br>gestion</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="member in group.persons track by $index" ng-class="{'success':member.pk==<?= $curPerson->data['pk'] ?>}">
                                    <td ng-class="{'success':'pere' == member.link || 'mere' == member.link || 'fils' == member.link || 'fille' == member.link, 'warning':'pere' != member.link && 'mere' != member.link && 'fils' != member.link && 'fille' != member.link}">
                                        <input type="hidden" name="persons[{{$index}}][pk]" value="{{member.pk}}">
                                        <select class="form-control" name="persons[{{$index}}][link]" ng-model="member.link">
                                            <optgroup label="Parents">
                                              <option value="pere">Père</option>
                                              <option value="mere">Mère</option>
                                            </optgroup>
                                            <optgroup label="Enfants">
                                              <option value="fils">Fils</option>
                                              <option value="fille">Fille</option>
                                            </optgroup>
                                            <optgroup label="Autre">
                                              <option value="homme">Homme</option>
                                              <option value="femme">Femme</option>
                                              <option value="friend_boy">Garçon</option>
                                              <option value="friend_girl">Fille</option>
                                            </optgroup>
                                        </select>
                                    </td>
                                    <td ng-class="{'has-error':member.errors.firstname}">
                                        <input type="text" name="persons[{{$index}}][firstname]" ng-model="member.firstname" class="form-control" placeholder="Prénom" required="required">
                                    </td>
                                    <td ng-class="{'has-error':member.errors.lastname}">
                                        <input type="text" name="persons[{{$index}}][lastname]" ng-model="member.lastname" class="form-control" placeholder="Nom" required="required">
                                    </td>
                                    <td>
                                        <input type="email" name="persons[{{$index}}][email]" ng-model="member.email" class="form-control" placeholder="Email">
                                    </td>
                                    <td>
                                        <input type="text" name="persons[{{$index}}][phone]" ng-model="member.phone" class="form-control" placeholder="Téléphone">
                                    </td>
                                    <td ng-class="{'has-error':member.errors.birthday}">
                                        <p class="input-group" ng-init="bd_cal_open = false;">
                                          <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" ng-click="bd_cal_open=true;"><i class="glyphicon glyphicon-calendar"></i></button>
                                          </span>
                                          <input type="text" maxlength="10" ng-focus="bd_cal_open=true;" name="persons[{{$index}}][birthday]" ng-model="member.birthday" class="form-control" uib-datepicker-popup is-open="bd_cal_open" datepicker-options="dateOptions" close-text="Close" placeholder="yyyy-mm-dd" />
                                        </p>
                                    </td>
                                    <td>
                                        <input type="hidden" name="persons[{{$index}}][can_manage]" value="{{member.can_manage}}" ng-show="member.pk==<?= $curPerson->data['pk'] ?>">
                                        <label ng-hide="member.pk==<?= $curPerson->data['pk'] ?>">
                                            <input type="hidden" ng-model="member.can_manage" value="{{member.can_manage*1}}" name="persons[{{$index}}][can_manage]">
                                            <a class="btn btn-xs btn-success" ng-click="member.can_manage=!member.can_manage" ng-show="member.can_manage">oui</a>
                                            <a class="btn btn-xs btn-danger" ng-click="member.can_manage=!member.can_manage" ng-hide="member.can_manage">non</a>
                                        </label>
                                    </td>
                                    <td>
                                        <a ng-hide="member.pk==<?= $curPerson->data['pk'] ?>" title="retirer" ng-click="removePerson($index)"><span class="glyphicon glyphicon-remove"></span></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8"><a style="cursor: pointer;" title="Ajout d'une personne" ng-click="addPerson()"><span class="glyphicon glyphicon-plus"></span></a></td>
                                </tr>
                            </tbody>
                        </table>
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
            'prop' => $curGroup->prop,
            'persons' => array_values($curGroup->persons)
        ]).';',
        'angularjs_accueilalaferme/controllers/FamilyCtrl.js'
      ];
    do_action('sydney_after_content'); ?>
<?php get_footer(); ?>