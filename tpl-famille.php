<?php
/**
 * Template Name: Profil
 *
 * @package Sydney
 */

$user = wp_get_current_user();
if (!$user->ID)
    header('Location:login');

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth">
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
                            <label class="col-sm-2 control-label" for="familly_name">Nom famille</label>
                            <div class="col-sm-10">
                                <input type="text" name="familly_name" class="form-control" id="familly_name" placeholder="Age" value="<?= $user->last_name ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="adresse_familly_name">Adresse</label>
                            <div class="col-sm-10">
                              <div class="row">
                                <div class="col-sm-8">
                                    <input type="text" name="adresse[familly_name]" class="form-control" id="adresse_familly_name" placeholder="Adresse" value="">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="adresse[ville]" class="form-control" id="adresse_ville" placeholder="Ville" value="">
                                </div>
                              </div><br>
                              <div class="row">
                                <div class="col-sm-4">
                                    <input type="text" name="adresse[code_postal]" class="form-control" id="adresse_code_postal" placeholder="Code postal" value="">
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
                                    <th>Anniversaire</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control" name="composition[0][lien]">
                                            <optgroup label="Parents">
                                              <option>Père</option>
                                              <option>Mère</option>
                                            </optgroup>
                                            <optgroup label="Enfants">
                                              <option>Fils</option>
                                              <option>Fille</option>
                                            </optgroup>
                                            <optgroup label="Invités - enfant">
                                              <option>Garçon</option>
                                              <option>Fille</option>
                                            </optgroup>
                                            <optgroup label="Autre">
                                              <option>Homme</option>
                                              <option>Femme</option>
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
                                        <input type="date" name="composition[0][anniversaire]" class="form-control" placeholder="Anniversaire">
                                    </td>
                                    <td>
                                        <a href="#"><span class="glyphicon glyphicon-remove"></span></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <select class="form-control" name="composition[0][lien]">
                                            <optgroup label="Parents">
                                              <option>Père</option>
                                              <option>Mère</option>
                                            </optgroup>
                                            <optgroup label="Enfants">
                                              <option>Fils</option>
                                              <option>Fille</option>
                                            </optgroup>
                                            <optgroup label="Invités - enfant">
                                              <option>Garçon</option>
                                              <option>Fille</option>
                                            </optgroup>
                                            <optgroup label="Autre">
                                              <option>Homme</option>
                                              <option>Femme</option>
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
                                        <input type="date" name="composition[0][anniversaire]" class="form-control" placeholder="Anniversaire">
                                    </td>
                                    <td>
                                        <a href="#" title="retirer"><span class="glyphicon glyphicon-remove"></span></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5"><a href="#" title="Ajout d'une personne"><span class="glyphicon glyphicon-plus"></span></a></td>
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
    <?php do_action('sydney_after_content'); ?>
<?php get_footer(); ?>