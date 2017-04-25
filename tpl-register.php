<?php
/**
 * Template Name: Inscription
 *
 * @package Sydney
 */

if (!empty($_POST)) {
    $d = $_POST;
    if ($d['user_pass'] != $d['user_pass2'])
        $error_msg = 'Les 2 mots de passes ne correspondent pas.';
    else if (!is_email($d['user_email']))
        $error_msg = 'Veuillez entrer un email valide.';
    else if (empty($d['user_pass']) || empty($d['user_login']) || empty($d['first_name']) || empty($d['last_name']))
        $error_msg = 'Veuillez remplir tous les champs.';
    else {
        $user = wp_insert_user([
            'user_login' => $d['user_login'],
            'user_email' => $d['user_email'],
            'first_name' => $d['first_name'],
            'last_name' => $d['last_name'],
            'user_pass' => $d['user_pass'],
            'user_registered' => date('Y-m-d H:i:s')
        ]);
        if (is_wp_error($user)) {
            $error_msg = $user->get_error_message();
        } else {
            // add_user_meta($user, 'cp', 'code postal ?'); // champ perso - get_user_meta()
            $msg = 'Vous êtes désormais inscrit au site Accueil à la ferme :)';
            $headers = 'From:'.get_option('admin_email')."\r\n";
            wp_mail($d['user_email'], 'Inscription réussie à Accueil à la ferme', $msg, $headers);
            wp_signon([
                'user_login' => $d['user_login'],
                'user_password' => $d['user_pass']
            ]);
            header('Location:profil');
        }
    }
}

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth">
		<main id="main" class="site-main" role="main">

				<h1>Inscription</h1>

                <?php if (!empty($error_msg)): ?>
                    <p class="alert alert-danger"><?= $error_msg ?></p>
                <?php endif ?>

				<div class="page-content">
                    <form class="form" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                        <div class="form-group">
                            <label for="user_login">Votre identifiant</label>
                            <input type="text" value="<?= !empty($d['user_login'])?$d['user_login']:'' ?>" name="user_login" class="form-control" id="user_login" placeholder="Identifiant">
                        </div>
                        <div class="form-group">
                            <label for="user_email">Votre email</label>
                            <input type="email" value="<?= !empty($d['user_email'])?$d['user_email']:'' ?>" name="user_email" class="form-control" id="user_email" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="first_name">Votre prénom</label>
                            <input type="text" value="<?= !empty($d['first_name'])?$d['first_name']:'' ?>" name="first_name" class="form-control" id="first_name" placeholder="Prénom">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Votre nom</label>
                            <input type="text" value="<?= !empty($d['last_name'])?$d['last_name']:'' ?>" name="last_name" class="form-control" id="last_name" placeholder="Nom">
                        </div>
                        <div class="form-group">
                            <label for="user_pass">Votre mot de passe</label>
                            <input type="password" name="user_pass" class="form-control" id="user_pass" placeholder="Mot de passe">
                        </div>
                        <div class="form-group">
                            <label for="user_pass2">Confirmez votre mot de passe</label>
                            <input type="password" name="user_pass2" class="form-control" id="user_pass2" placeholder="Mot de passe">
                        </div>
                        <button type="submit" class="btn btn-default">S'inscrire</button>
                    </form>
				</div><!-- .page-content -->

		</main><!-- #main -->
	</div><!-- #primary -->

    <?php do_action('sydney_after_content'); ?>
<?php get_footer(); ?>
