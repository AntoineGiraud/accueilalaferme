<?php
/**
 * Template Name: Profil
 *
 * @package Sydney
 */

$family = [
    'family_name' => $userWP->last_name,
    'adresse' => [
        'pk' => null,
        'street' => '547 avenue de la République',
        'postal_code' => '59700',
        'city' => 'Marcq-en-Baroeul',
        'region' => 'Nord',
        'country' => 'France'
    ],
    'members' => [
        [
            'id' => null,
            'firstname' => 'Antoine',
            'lastname' => 'Giraud',
            'birthday' => '1992-10-08',
            'email' => 'antoine.giraud@outlook.com',
            'is_member' => 1,
            'can_manage' => 1,
            'link' => 'fils'
        ],
        [
            'id' => null,
            'firstname' => 'Corentin',
            'lastname' => 'Giraud',
            'birthday' => '1997-07-29',
            'email' => 'antoine.giraud@outlook.com',
            'is_member' => 1,
            'can_manage' => 1,
            'link' => 'fils'
        ]
    ]
];

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth" ng-app="app" ng-controller="PageCtrl">
		<main id="main" class="site-main hentry page" role="main">
            <header class="entry-header">
                <h1 class="title-post entry-title">Ma famille</h1>
            </header>
                <?php if (!empty($error_msg)): ?>
                    <p class="alert alert-danger"><?= $error_msg ?></p>
                <?php endif ?>

				<div class="page-content">
                    <p>Vous pouvez mettre à jour les informations de votre famille depuis cette page.</p>
				</div><!-- .page-content -->

                <form class="form-horizontal" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                    <fieldset>
                        <legend>Description générale de la famille</legend>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="family_name">Nom famille</label>
                            <div class="col-sm-10">
                                <input type="text" name="family_name" class="form-control" id="family_name" placeholder="Age" value="<?= $userWP->last_name ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="adresse_family_name">Adresse</label>
                            <div class="col-sm-10">
                              <div class="row">
                                <div class="col-sm-8">
                                    <input type="text" name="adresse[street]" class="form-control" id="adresse_family_name" placeholder="Adresse" value="">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="adresse[city]" class="form-control" id="adresse_city" placeholder="Ville" value="">
                                </div>
                              </div><br>
                              <div class="row">
                                <div class="col-sm-4">
                                    <input type="text" name="adresse[postal_code]" class="form-control" id="adresse_postal_code" placeholder="Code postal" value="">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="adresse[region]" class="form-control" id="adresse_region" placeholder="Région" value="">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="adresse[pays]" class="form-control" id="adresse_pays" placeholder="Pays" value="">
                                </div>
                              </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Composition de la famille</legend>
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Lien</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Anniversaire</th>
                                    <th>Droits gestion</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control" name="composition[0][lien]">
                                            <optgroup label="Parents">
                                              <option value="pere">Père</option>
                                              <option value="mere">Mère</option>
                                            </optgroup>
                                            <optgroup label="Enfants">
                                              <option value="fils">Fils</option>
                                              <option value="fille">Fille</option>
                                            </optgroup>
                                            <optgroup label="Invités - enfant">
                                              <option value="friend_boy">Garçon</option>
                                              <option value="friend_girl">Fille</option>
                                            </optgroup>
                                            <optgroup label="Autre">
                                              <option value="homme">Homme</option>
                                              <option value="femme">Femme</option>
                                            </optgroup>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="composition[0][prenom]" class="form-control" placeholder="Prénom">
                                    </td>
                                    <td>
                                        <input type="text" name="composition[0][nom]" class="form-control" placeholder="Nom">
                                    </td>
                                    <td>
                                        <input type="text" name="composition[0][email]" class="form-control" placeholder="Email">
                                    </td>
                                    <td>
                                        <p class="input-group" ng-init="bd_cal_open = false;">
                                          <span class="input-group-btn">
                                            <button type="button" class="btn btn-default" ng-click="bd_cal_open=true;"><i class="glyphicon glyphicon-calendar"></i></button>
                                          </span>
                                          <input type="text" maxlength="10" name="composition[0][anniversaire]" class="form-control" uib-datepicker-popup ng-model="dt" is-open="bd_cal_open" datepicker-options="dateOptions" close-text="Close" placeholder="yyyy-mm-dd"/>
                                        </p>
                                    </td>
                                    <td>

                                        <label>
                                            <input type="checkbox" value="1" name="composition[0][can_edit]">
                                        </label>
                                    </td>
                                    <td>
                                        <a href="#"><span class="glyphicon glyphicon-remove"></span></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7"><a href="#" title="Ajout d'une personne"><span class="glyphicon glyphicon-plus"></span></a></td>
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
      ];
    do_action('sydney_after_content'); ?>
<?php get_footer(); ?>