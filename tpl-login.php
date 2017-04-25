<?php
/**
 * Template Name: Connexion
 *
 * @package Sydney
 */

if (!empty($_POST)) {
    $user = wp_signon($_POST);
    if (is_wp_error($user)) {
        $error_msg = $user->get_error_message();
    } else
        header('Location:profil');
} else {
    $user = wp_get_current_user();
    if ($user->ID)
        header('Location:profil');
}

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth">
		<main id="main" class="site-main" role="main">

				<h1>Connexion</h1>

                <?php if (!empty($error_msg)): ?>
                    <p class="alert alert-danger"><?= $error_msg ?></p>
                <?php endif ?>

				<div class="page-content">
                    <form class="form" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                        <div class="form-group">
                            <label for="user_login">Votre identifiant</label>
                            <input type="text" name="user_login" class="form-control" id="user_login" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="user_password">Votre mot de passe</label>
                            <input type="password" name="user_password" class="form-control" id="user_password" placeholder="Password">
                        </div>
                        <div class="checkbox">
                            <label>
                              <input type="checkbox" name="remember" value="1"> Se souvenir de moi
                            </label>
                        </div>
                        <button type="submit" class="btn btn-default">Se connecter</button>
                    </form>
				</div><!-- .page-content -->

		</main><!-- #main -->
	</div><!-- #primary -->

    <?php do_action('sydney_after_content'); ?>
<?php get_footer(); ?>
