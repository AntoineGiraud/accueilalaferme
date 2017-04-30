<?php
/**
 * Template Name: Profil
 *
 * @package Sydney
 */

function getAge($anniversaire) {
    $date = new DateTime($anniversaire);
    $now = new DateTime();
    $interval = $now->diff($date);
    return $interval->y;
}

$user = wp_get_current_user();
if (!$user->ID)
    header('Location:login');

if (!empty($_POST['user_age']))
    update_user_meta(get_current_user_id(), 'user_age', $_POST['user_age']*1);

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth">
		<main id="main" class="site-main hentry page" role="main">
            <header class="entry-header">
                <h1 class="title-post entry-title">Mes informations</h1>
            </header>
                <?php if (!empty($error_msg)): ?>
                    <p class="alert alert-danger"><?= $error_msg ?></p>
                <?php endif ?>

				<div class="page-content">
                    <h4>Bienvenue <?= $user->user_firstname ?> !</h4>
                    <p>Vous retrouverez ici votre profil vous permettant de gérer votre famille/compte et de vous inscrire aux évènements de la ferme.</p>
				</div><!-- .page-content -->

                <div class="row">
                    <div class="col-sm-6">
                        <h4>Prochains événements</h4>
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Nom</th>
                                    <th>Lieu</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2017-07-21</td>
                                    <td>2017-07-24</td>
                                    <td>Camping des familles</td>
                                    <td>Jardins de St-Georges</td>
                                    <td><a href="#"><span class="glyphicon glyphicon-plus"></span></a></td>
                                </tr>
                                <tr>
                                    <td>2017-05-03</td>
                                    <td>2017-05-04</td>
                                    <td>Pentecôte</td>
                                    <td>Jardins de St-Georges</td>
                                    <td><a href="#"><span class="glyphicon glyphicon-pencil"></span></a></td>
                                </tr>
                                <tr>
                                    <td>2017-04-15</td>
                                    <td>2017-04-18</td>
                                    <td>Pâques</td>
                                    <td>Jardins de St-Georges</td>
                                    <td><a href="#"><span class="glyphicon glyphicon-info-sign"></span></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <h4>Ma famille</h4>
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Lien</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Anniversaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Père</td>
                                    <td>François</td>
                                    <td>Giraud</td>
                                    <td>1964-05-08 <em>(<?= getAge('1964-05-08') ?> ans)</em></td>
                                </tr>
                                <tr>
                                    <td>Mère</td>
                                    <td>Gabrièle</td>
                                    <td>Amossé</td>
                                    <td>1964-12-21 <em>(<?= getAge('1964-12-21') ?> ans)</em></td>
                                </tr>
                                <tr>
                                    <td>Fils</td>
                                    <td>Antoine</td>
                                    <td>Giraud</td>
                                    <td>1992-10-08 <em>(<?= getAge('1992-10-08') ?> ans)</em></td>
                                </tr>
                                <tr>
                                    <td>Fils</td>
                                    <td>Corentin</td>
                                    <td>Giraud</td>
                                    <td>1994-07-29 <em>(<?= getAge('1994-07-29') ?> ans)</em></td>
                                </tr>
                                <tr>
                                    <td>Fils</td>
                                    <td>Grégoire</td>
                                    <td>Giraud</td>
                                    <td>1997-11-05 <em>(<?= getAge('1997-11-05') ?> ans)</em></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

		</main><!-- #main -->
	</div><!-- #primary -->
    <?php do_action('sydney_after_content'); ?>
<?php get_footer(); ?>