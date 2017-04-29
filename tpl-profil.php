<?php
/**
 * Template Name: Profil
 *
 * @package Sydney
 */

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
                    <h4>Bienvenue <?= $user->first_name ?> !</h4>
                    <p>Vous retrouverez ici votre profil vous permettant de gérer votre famille/compte et de vous inscrire aux évènements de la ferme.</p>
				</div><!-- .page-content -->

                <h4>MAJ informations</h4>
                <form class="form" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                    <div class="form-group">
                        <label for="user_age">Votre age</label>
                        <input type="text" name="user_age" class="form-control" id="user_age" placeholder="Age" value="<?= get_user_meta(get_current_user_id(), 'user_age', true) ?>">
                    </div>
                    <button type="submit" class="btn btn-default">Valider</button>
                </form>
		</main><!-- #main -->
	</div><!-- #primary -->
    <?php do_action('sydney_after_content'); ?>
<?php get_footer(); ?>