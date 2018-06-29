<?php
/**
 * Template Name: Home page
 *
 * @package accueilalaferme
 */

$can_admin = is_admin() || current_user_can('administrator');

if(isset($_GET['user_id']))
    if($can_admin) {
        if($_GET['user_id'] != $curPerson->data['pk'])
            $curPerson = new \AccueilALaFerme\User($DB, $_GET['user_id'], null);
    }
    else
        \AccueilALaFerme\Flash::setFlashAndRedirect("Vous n'avez pas les droits pour éditer d'autres personnes", 'danger', 'profil');

if (!empty($_SESSION['url'])) {
    $url = implode('?', $_SESSION['url']);
    unset($_SESSION['url']);
    header('Location:'.$root.$url);
    die();
}

if (!empty($curPerson->groups)) {
    $group_id = current(array_keys($curPerson->groups));
    $curGroup = new \AccueilALaFerme\Group($group_id, $DB);
} else $curGroup = null;

// Récupérer les inscriptions présentes
$groupSql = !empty($group_id) ? 'pg.group_id = :group_id and ' : 'p.pk = :p_id and';
$data = !empty($group_id) ? ['group_id' => $group_id] : ['p_id' => $curPerson->data['pk']];
$res = $DB->query("SELECT p.firstname, p.lastname, r.arrival_date, r.departure_date, r.event_id
                    FROM registration r
                        LEFT JOIN person p ON p.pk = r.person_id
                        LEFT JOIN person_has_group pg ON p.pk = pg.person_id and pg.was_removed is null
                    where $groupSql r.will_come=1
                    order by r.event_id, pg.group_id, pg.group_link_pk, p.pk", $data);
$register = [];
foreach ($res as $row) {
    $row['arrival_date'] = substr($row['arrival_date'], 0, 10);
    $row['departure_date'] = substr($row['departure_date'], 0, 10);
    if (!isset($register[$row['event_id']]))
        $register[$row['event_id']] = [];
    $register[$row['event_id']][] = $row;
}

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
                    <h4>Bienvenue <?= $curPerson->data['firstname'] ?> !</h4>
                    <p>Vous retrouverez ici votre profil vous permettant de gérer votre famille/compte et de vous inscrire aux évènements de la ferme.</p>
				</div><!-- .page-content -->

                <div class="row">
                    <div class="col-sm-7">
                        <h4>Prochains événements</h4>
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Nom</th>
                                    <th>Option</th>
                                    <th>Participants</th>
                                    <?php if (current_user_can('administrator')): ?>
                                        <th>Admin</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td><?= substr($event['start_date'], 0, 10) ?></td>
                                        <td><?= substr($event['end_date'], 0, 10) ?></td>
                                        <td><?= $event['name'] ?></td>
                                        <td ><a style="height: auto;" href="<?= get_bloginfo('url').'/event/register?event_id='.$event['pk']?><?= ($can_admin && isset($_GET['user_id'])) ? '&user_id='.$curPerson->data['pk'] : "" ?>" class="btn btn-<?= !empty($register[$event['pk']]) ? 'success':'primary' ?> btn-xs"><?= !empty($register[$event['pk']]) ? "éditer":"s'inscrire" ?></a></td>
                                        <td>
                                            <?php if (!empty($register[$event['pk']])): ?>
                                                <?= implode(', ', array_map(function($d){return '<span title="'.$d['firstname'].' '.$d['lastname']."\n arrivée: ".$d['arrival_date']."\n départ: ".$d['departure_date']."\n".'">'.$d['firstname'].'</span>';}, $register[$event['pk']])) ?>
                                            <?php endif ?>
                                        </td>
                                        <?php if (current_user_can('administrator')): ?>
                                            <td>
                                                <small><a href="<?= get_bloginfo('url').'/event/guests?event_id='.$event['pk'] ?>" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-list"></span> invités</a></small>
                                                <small><a href="<?= get_bloginfo('url').'/user_list?event_id='.$event['pk'] ?>" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-list"></span> utilisateurs sans place</a></small>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-5">
                        <?php if (empty($curGroup)): ?>
                            <h4>Ma famille / mon groupe</h4>
                            <p>
                                Remplissez votre famille ou groupe afin de faciliter l'inscriptions aux événements ou l'accès à nos nouvelles.
                            </p>
                            <p><a href="<?= get_bloginfo('url').'/famille' ?><?= (isset($_GET['user_id']) && ($can_admin)) ? '?user_id=' . $_GET['user_id'] : "" ?>" class="btn btn-primary">C'est parti !</a></p>
                        <?php else: ?>
                            <h4>Ma famille <a href="<?= get_bloginfo('url').'/famille' ?><?=($can_admin && isset($_GET['user_id'])) ? "?group_id=" . $group_id : "" ?>" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-pencil"></span></a></h4>
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