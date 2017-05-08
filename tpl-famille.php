<?php
/**
 * Template Name: Profil
 *
 * @package Sydney
 */

$group = [
    'is_family' => '1',
    'name' => $userWP->last_name,
    'phone' => '+335',
    'address' => [
        'pk' => null,
        'street' => '2800 avenue Willowdale',
        'postal_code' => '17147',
        'city' => 'Lille',
        'region' => 'Nord',
        'country' => 'France'
    ],
    'persons' => [
        [
            'pk' => null,
            'firstname' => 'Antoine',
            'lastname' => 'Giraud',
            'email' => 'antoine@outlook.com',
            'phone' => '+514',
            'birthday' => '2002-01-08',
            'can_manage' => 1,
            'link' => 'fils'
        ],
        [
            'pk' => null,
            'firstname' => 'Frangin',
            'lastname' => 'Giraud',
            'email' => 'frangin@outlook.com',
            'phone' => '07...',
            'birthday' => '',
            'can_manage' => 1,
            'link' => 'fils'
        ]
    ]
];

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth" ng-app="app" ng-controller="PageCtrl">
		<main id="main" class="site-main hentry page" role="main" ng-controller="FamilyCtrl">
            <header class="entry-header">
                <h1 class="title-post entry-title">{{{'1':'Ma famille', '0':'Mon groupe'}[group.is_family]}}</h1>
            </header>
                <?php if (!empty($error_msg)): ?>
                    <p class="alert alert-danger"><?= $error_msg ?></p>
                <?php endif ?>

				<div class="page-content">
                    <p>Vous pouvez mettre à jour les informations de votre {{{'1':'famille', '0':'groupe'}[group.is_family]}} depuis cette page.</p>
				</div><!-- .page-content -->

                <form class="form-horizontal" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                    <fieldset>
                        <legend>Description générale {{{'1':'de la famille', '0':'du groupe'}[group.is_family]}}</legend>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Statut</label>
                            <div class="col-sm-10">
                                <label class="radio-inline">
                                  <input type="radio" name="is_family" ng-model="group.is_family" value="1"> Famille
                                </label>
                                <label class="radio-inline">
                                  <input type="radio" name="is_family" ng-model="group.is_family" value="0"> Groupe
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="family_name">Nom</label>
                            <div class="col-sm-10">
                                <input type="text" name="family_name" class="form-control" id="family_name" placeholder="Age" ng-model="group.name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="phone">Téléphone</label>
                            <div class="col-sm-10">
                                <input type="text" name="phone" class="form-control" id="phone" placeholder="Téléphone" ng-model="group.phone">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Adresse</label>
                            <input type="hidden" name="address[pk]" ng-model="group.address.pk">
                            <div class="col-sm-10">
                              <div class="row">
                                <div class="col-sm-8">
                                    <input type="text" name="address[street]" title="address : street" class="form-control" placeholder="Adresse" ng-model="group.address.street">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="address[city]" title="address : city" class="form-control" placeholder="Ville" ng-model="group.address.city">
                                </div>
                              </div><br>
                              <div class="row">
                                <div class="col-sm-4">
                                    <input type="text" name="address[postal_code]" title="address : postal_code" class="form-control" placeholder="Code postal" ng-model="group.address.postal_code">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="address[region]" title="address : region" class="form-control" placeholder="Région" ng-model="group.address.region">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="address[country]" title="address : country" class="form-control" placeholder="Pays" ng-model="group.address.country">
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
                                <tr ng-repeat="member in group.persons track by $index">
                                    <td>
                                        <input type="hidden" name="persons[{{$index}}][pk]" ng-model="member.pk">
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
                                    <td>
                                        <input type="text" name="persons[{{$index}}][prenom]" ng-model="member.firstname" class="form-control" placeholder="Prénom">
                                    </td>
                                    <td>
                                        <input type="text" name="persons[{{$index}}][nom]" ng-model="member.lastname" class="form-control" placeholder="Nom">
                                    </td>
                                    <td>
                                        <input type="text" name="persons[{{$index}}][email]" ng-model="member.email" class="form-control" placeholder="Email">
                                    </td>
                                    <td>
                                        <input type="text" name="persons[{{$index}}][phone]" ng-model="member.phone" class="form-control" placeholder="Téléphone">
                                    </td>
                                    <td>
                                        <p class="input-group" ng-init="bd_cal_open = false;">
                                          <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" ng-click="bd_cal_open=true;"><i class="glyphicon glyphicon-calendar"></i></button>
                                          </span>
                                          <input type="text" maxlength="10" name="persons[{{$index}}][anniversaire]" ng-model="member.birthday" class="form-control" uib-datepicker-popup ng-model="dt" is-open="bd_cal_open" datepicker-options="dateOptions" close-text="Close" placeholder="yyyy-mm-dd"/>
                                        </p>
                                    </td>
                                    <td>
                                        <label>
                                            <input type="checkbox" value="1" ng-model="member.can_manage" name="persons[{{$index}}][can_manage]">
                                        </label>
                                    </td>
                                    <td>
                                        <a title="retirer" ng-click="removePerson($index)"><span class="glyphicon glyphicon-remove"></span></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8"><a title="Ajout d'une personne" ng-click="addPerson()"><span class="glyphicon glyphicon-plus"></span></a></td>
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
if ($_POST) {
    var_dump($_POST);
}
      global $js_for_layout;
      $js_for_layout = [
        'angularjs',
        'angularjs_accueilalaferme/app.js',
        'angularjs_accueilalaferme/controllers/PageCtrl.js',
        'var groupData = '.json_encode($group).';',
        'angularjs_accueilalaferme/controllers/FamilyCtrl.js'
      ];
    do_action('sydney_after_content'); ?>
<?php get_footer(); ?>