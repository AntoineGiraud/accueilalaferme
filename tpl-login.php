<?php
/**
 * Template Name: Connexion
 *
 * @package Sydney
 */


if (!empty($_POST)) {
    $userWP = wp_signon($_POST);
    if (is_wp_error($userWP))
        $error_msg = $userWP->get_error_message();
    else
        header('Location:profil');
}

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth">
		<main id="main" class="site-main hentry page" role="main">
            <header class="entry-header">
                <h1 class="title-post entry-title">
                    Connexion
                    <?php if ($userWP->ID && !empty($curPerson->data['is_allowed'])): ?>
                        <small>Vous êtes déjà connecté : <a href="profil" class="btn btn-primary btn-xs">mon profil</a></small>
                    <?php endif ?>
                </h1>
            </header>

                <?php if (!empty($error_msg)): ?>
                    <p class="alert alert-danger"><?= $error_msg ?></p>
                <?php endif ?>

				<div class="page-content">
                    <form class="form-horizontal" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                        <div class="form-group">
                            <label for="user_login" class="col-sm-2 control-label">Identifiant</label>
                            <div class="col-sm-10">
                                <input type="text" name="user_login" class="form-control" id="user_login" placeholder="Identifiant">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_password" class="col-sm-2 control-label">Mot de passe</label>
                            <div class="col-sm-10">
                                <input type="password" name="user_password" class="form-control" id="user_password" placeholder="Mot de passe">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10 checkbox">
                                <label>
                                  <input type="checkbox" name="remember" value="1"> Se souvenir de moi
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                          <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success">Se connecter</button>
                          </div>
                        </div>
                    </form>
				</div><!-- .page-content -->

		</main><!-- #main -->
	</div><!-- #primary -->

    <?php do_action('sydney_after_content'); ?>
<?php get_footer(); ?>
