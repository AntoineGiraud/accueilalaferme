<?php
/**
 * Template Name: Registration
 *
 * @package accueilalaferme
 */

if (!empty($_POST)) {
    $d = $_POST;
    if ($d['user_pass'] != $d['user_pass2'])
        $error_msg = 'Les 2 mots de passes ne correspondent pas.';
    else if (!is_email($d['user_email']))
        $error_msg = 'Veuillez entrer un email valide.';
    else if (!empty($d['birthday']) && !preg_match('/[0-9]{4}-[0-1]?[0-9]{1}-[0-3]?[0-9]{1}/', $d['birthday']))
        $error_msg = 'Entrez une date au format yyyy-mm-dd';
    else if (empty($d['user_pass']) || empty($d['user_login']) || empty($d['first_name']) || empty($d['last_name']))
        $error_msg = 'Veuillez remplir tous les champs.';
    else {
        foreach (['user_login','user_email','first_name','last_name','phone','birthday'] as $key)
            $d[$key] = trim($d[$key]);
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
            if (empty($d['birthday'])) $d['birthday'] = null;
            $curPerson = new \AccueilALaFerme\User($DB, null, $d['user_email'], $d['first_name'], $d['last_name'], $d['birthday'], $d['phone']);
            // add_user_meta($user, 'cp', 'code postal ?'); // champ perso - get_user_meta()
            $msg = 'Vous êtes désormais inscrit au site Accueil à la ferme :)';
            $headers = 'From:'.get_option('admin_email')."\r\n";
            wp_mail($d['user_email'], 'Inscription réussie à Accueil à la ferme', $msg, $headers);
            wp_signon([
                'user_login' => $d['user_login'],
                'user_password' => $d['user_pass']
            ]);
            if ($_SESSION['url'][0] == "event/register") {
                unset($_SESSION['url']);
                \AccueilALaFerme\Flash::setFlashAndRedirect("Avant de continuer vers l'enregistrement à l'événement, veuillez renseigner votre famille ou groupe.<br><em>Si vous êtes seul: vous pouvez <a href=\"".$root.implode('?', $_SESSION['url'])."\">continuer vers l'événement</a></em>", 'success', 'famille?'.$_SESSION['url'][1]);
            }
            header('Location:'.$root.'profil');
            die();
        }
    }
}

get_header();
    do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area fullwidth" ng-app="app" ng-controller="PageCtrl">
		<main id="main" class="site-main hentry page" role="main">
            <header class="entry-header">
                <h1 class="title-post entry-title">Inscription</h1>
            </header>
                <?php if (!empty($error_msg)): ?>
                    <p class="alert alert-danger"><?= $error_msg ?></p>
                <?php endif ?>
                <?php if (!empty($_SESSION['url'])): ?>
                    <p class="alert alert-info">Avant de continuer<?= $_SESSION['url'][0] == "event/register" ? " vers l'enregistrement à l'événement" : '' ?>, veuillez vous inscrire à accueil à la ferme.</p>
                <?php endif ?>

				<div class="page-content">
                    <form class="form-horizontal" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="user_login">Identifiant</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?= !empty($d['user_login'])?$d['user_login']:'' ?>" name="user_login" class="form-control" id="user_login" placeholder="Identifiant" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="user_email">Email</label>
                            <div class="col-sm-10">
                                <input type="email" value="<?= !empty($d['user_email'])?$d['user_email']:'' ?>" name="user_email" class="form-control" id="user_email" placeholder="Email" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="first_name">Prénom</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?= !empty($d['first_name'])?$d['first_name']:'' ?>" name="first_name" class="form-control" id="first_name" placeholder="Prénom" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="last_name">Nom</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?= !empty($d['last_name'])?$d['last_name']:'' ?>" name="last_name" class="form-control" id="last_name" placeholder="Nom" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="phone">Téléphone</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?= !empty($d['phone'])?$d['phone']:'' ?>" name="phone" class="form-control" id="phone" placeholder="Téléphone">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="birthday">Anniversaire</label>
                            <div class="col-sm-10">
                                <p class="input-group" ng-init="bd_cal_open = false;">
                                  <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" ng-click="bd_cal_open=true;"><i class="glyphicon glyphicon-calendar"></i></button>
                                  </span>
                                  <input type="date" value="<?= !empty($d['birthday'])?$d['birthday']:'' ?>" maxlength="10" name="birthday" id="birthday" class="form-control" uib-datepicker-popup ng-model="dt" is-open="bd_cal_open" datepicker-options="dateOptions" close-text="Close" placeholder="yyyy-mm-dd"/>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="user_pass">Mot de passe</label>
                            <div class="col-sm-10">
                                <input type="password" name="user_pass" class="form-control" id="user_pass" placeholder="Mot de passe" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="user_pass2">Confirmez mot de passe</label>
                            <div class="col-sm-10">
                                <input type="password" name="user_pass2" class="form-control" id="user_pass2" placeholder="Mot de passe" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                          <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success">S'inscrire</button>
                          </div>
                        </div>
                    </form>
				</div><!-- .page-content -->

		</main><!-- #main -->
	</div><!-- #primary -->

    <?php
      global $js_for_layout;
      $js_for_layout = [
        'angularjs',
        'angularjs_accueilalaferme/app.js',
        'angularjs_accueilalaferme/controllers/PageCtrl.js',
      ];
    do_action('sydney_after_content');
get_footer();
?>