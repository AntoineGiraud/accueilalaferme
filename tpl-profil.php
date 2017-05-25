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

if (!empty($curPerson->groups)) {
    $group_id = current(array_keys($curPerson->groups));
    $curGroup = new \AccueilALaFerme\Group($group_id, $DB);
} else $curGroup = null;

$events = $DB->query("SELECT * FROM event WHERE end_date >= NOW()");

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
                    <h4>Bienvenue <?= $userWP->user_firstname ?> !</h4>
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
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td><?= substr($event['start_date'], 0, 10) ?></td>
                                        <td><?= substr($event['end_date'], 0, 10) ?></td>
                                        <td><?= $event['name'] ?></td>
                                        <td><a href="<?= get_bloginfo('url').'/event_register?event_id='.$event['pk'] ?>"><span class="glyphicon glyphicon-info-sign"></span></a></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <?php if (empty($curGroup)): ?>
                            <h4>Ma famille / mon groupe</h4>
                            <p>
                                Remplissez votre famille ou groupe afin de faciliter l'inscriptions aux événements ou l'accès à nos nouvelles.
                            </p>
                            <p><a href="<?= get_bloginfo('url').'/famille' ?>" class="btn btn-primary">C'est parti !</a></p>
                        <?php else: ?>
                            <h4>Ma famille <a href="<?= get_bloginfo('url').'/famille' ?>" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-pencil"></span></a></h4>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>Role</th>
                                        <th>Lien</th>
                                        <th>Prénom</th>
                                        <th>Nom</th>
                                        <th>Anniversaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($curGroup->persons as $elem): ?>
                                        <tr>
                                            <td><?= $elem['can_manage']?'<span class="glyphicon glyphicon-king"></span>':'' ?></td>
                                            <td class="<?= in_array($elem['link'], ['pere', 'mere', 'fils', 'fille'])?'success':'warning' ?>"><?= $elem['link_name'] ?></td>
                                            <td><?= $elem['firstname'] ?></td>
                                            <td><?= $elem['lastname'] ?></td>
                                            <td><?= !empty($elem['birthday']) ? $elem['birthday'].' <em>('. getAge($elem['birthday']) .' ans)</em>' : '' ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php endif ?>
                    </div>
                </div>

		</main><!-- #main -->
	</div><!-- #primary -->
    <?php do_action('sydney_after_content'); ?>
<?php get_footer(); ?>