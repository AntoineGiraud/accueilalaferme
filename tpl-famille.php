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
                    <h4>Bienvenue <?= $user->first_name ?> !</h4>
                    <p>Vous pouvez mettre à jour les informations de votre famille depuis cette page.</p>
				</div><!-- .page-content -->

                <form class="form-horizontal" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                    <fieldset>
                        <legend>Description</legend>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="user_age">Votre age</label>
                            <div class="col-sm-10">
                                <input type="text" name="user_age" class="form-control" id="user_age" placeholder="Age" value="<?= get_user_meta(get_current_user_id(), 'user_age', true) ?>">
                            </div>
                        </div>
                    </fieldset>
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