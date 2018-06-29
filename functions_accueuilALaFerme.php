<?php
/**
 * accueilalaferme functions and definitions
 *
 * @package accueilalaferme
 */

//////////////////////////////////
// Fonctions Accueil à la ferme //
//////////////////////////////////

function getAge($anniversaire, $comparison_date=null) {
    date_default_timezone_set('America/Toronto');
    $date = new DateTime($anniversaire);
    $comparison_date = isset($comparison_date) ? new Datetime($comparison_date) : new Datetime();
    $interval = $comparison_date->diff($date);
    return $interval->y;
}

global $js_for_layout;
require __DIR__ . '/class/Flash.php';
add_action('send_headers', 'site_router');
function site_router() {
    if(!isset ($_SESSION)){session_start();}
    global $root;
    $conf = require __DIR__ . '/conf.php';
    $root = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    $url = substr($_SERVER['REQUEST_URI'], strlen($root));
    $url = explode('?', $url, 2);
    $url_path = $url[0];

    require __DIR__ . '/class/DB.php';
    require __DIR__ . '/class/User.php';
    require __DIR__ . '/class/Group.php';
    require __DIR__ . '/class/Address.php';

    $confSQL = $conf['confSQL'];
    $DB = new \AccueilALaFerme\DB($confSQL['sql_host'], $confSQL['sql_user'], $confSQL['sql_pass'], $confSQL['sql_db']);
    $userWP = wp_get_current_user();
    global $curPerson;
    if ($userWP->ID)
        $curPerson = new \AccueilALaFerme\User($DB, null, $userWP->user_email, $userWP->first_name, $userWP->last_name);

    if (!current_user_can('administrator') && !is_admin())
        add_filter('show_admin_bar', '__return_false');

    if (in_array($url_path, ['login', 'register', 'logout', 'famille', 'profil', 'event/register', 'event/guests', 'user_list'])
        || strpos($url_path, 'event') === 0
        ) {
        add_filter('show_admin_bar', '__return_false');

        $page = $url[0];
        if (!$userWP->ID && !in_array($page, ['login', 'register', 'logout'])) {
            $_SESSION['url'] = $url;
            \AccueilALaFerme\Flash::setFlashAndRedirect("Vous devez être connecté pour accéder à l'espace membre.", 'danger', 'login');
        }
        // Auth pages
        if ($page == 'login') {
            require __DIR__ .'/templates/auth/login.php'; die();
        } else if ($page == 'register') {
            require __DIR__ .'/templates/auth/register.php'; die();
        } else if ($page == 'logout') {
            wp_logout();
            header('Location:'.$root); die();
        }

        // if (empty($curPerson->data['is_allowed']))
        //     \AccueilALaFerme\Flash::setFlashAndRedirect("Votre compte est en attente d'approbation. Vous ne pouvez pas accéder à la partie privée du site internet.", 'warning', 'login');
        if ($page == 'profil')
            require __DIR__ .'/templates/profil.php';
        else if ($page == 'famille')
            require __DIR__ .'/templates/famille.php';
        else if ($page == 'event/register')
            require __DIR__ .'/templates/event/register.php';
        else if ($page == 'event/guests')
            require __DIR__ .'/templates/event/guests.php';
        else if ($page == 'user_list')
            require __DIR__ .'/templates/user_list.php';
        else
            \AccueilALaFerme\Flash::setFlashAndRedirect("Page <em>$page</em> inconnue", 'warning', 'profil');
        die();
    }
}

function add_last_nav_item($items) {
    $blogUrl = get_bloginfo('url');
    $userWP = wp_get_current_user();
    global $curPerson;
    ob_start(); ?>
    <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?= $blogUrl ?>/profil">Membres</a>
        <ul class="sub-menu">
            <?php if (!$userWP->ID): ?>
                <li id="se-connecter" class="menu-item menu-item-type-post_type menu-item-object-page se-connecter"><a href="<?= $blogUrl ?>/login"><span class="glyphicon glyphicon-log-in"></span>&nbsp;&nbsp; Se connecter</a></li>
                <li id="register" class="menu-item menu-item-type-post_type menu-item-object-page register"><a href="<?= $blogUrl ?>/register"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp; S'inscrire</a></li>
            <?php else: ?>
                <li id="profil" class="menu-item menu-item-type-post_type menu-item-object-page profil"><a href="<?= $blogUrl ?>/profil"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp; Profil <small><em><?= $userWP->first_name ?></em></small></a></li>
                <li id="famille" class="menu-item menu-item-type-post_type menu-item-object-page famille"><a href="<?= $blogUrl ?>/famille"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;
                    <?php if (!empty($curPerson->groups)): $curGroup = current($curPerson->groups); ?>
                        <?= $curGroup['is_family']?'Famille':'Group' ?> <small><em><?= $curGroup['name'] ?></em></small>
                    <?php else: ?>
                        Créer ma famille/groupe
                    <?php endif; ?>
                </a></li>
                <li id="se-déconnecter" class="menu-item menu-item-type-post_type menu-item-object-page se-déconnecter"><a href="<?= $blogUrl ?>/logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp;&nbsp; Se déconnecter</a></li>
            <?php endif ?>
        </ul>
    </li>
    <?php if (current_user_can('administrator') || is_admin()): ?>
    <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="<?= $blogUrl ?>/profil">Gestion</a>
        <ul class="sub-menu">
            <li id="evenements" class="menu-item menu-item-type-post_type menu-item-object-page evenements"><a href="<?= $blogUrl ?>/event/list"><span class="glyphicon glyphicon-list"></span>&nbsp;&nbsp; Evénements</a></li>
            <li id="familles" class="menu-item menu-item-type-post_type menu-item-object-page familles"><a href="<?= $blogUrl ?>/famille/list"><span class="glyphicon glyphicon-list"></span>&nbsp;&nbsp; Familles</a></li>
            <li id="personnes" class="menu-item menu-item-type-post_type menu-item-object-page personnes"><a href="<?= $blogUrl ?>/event/guests?event_id=4"><span class="glyphicon glyphicon-list"></span>&nbsp;&nbsp; Personnes</a></li>
            <li id="utilisateurs" class="menu-item menu-item-type-post_type menu-item-object-page utilisateurs"><a href="<?= $blogUrl ?>/user_list"><span class="glyphicon glyphicon-list"></span>&nbsp;&nbsp; Utilisateurs</a></li>
            <li id="utilisateurs" class="menu-item menu-item-type-post_type menu-item-object-page utilisateurs"><a href="<?= $blogUrl ?>/register"><span class="glyphicon glyphicon-list"></span>&nbsp;&nbsp; Ajout utilisateur</a></li>
            <li id="utilisateurs" class="menu-item menu-item-type-post_type menu-item-object-page utilisateurs"><a href="<?= $blogUrl ?>/wp-admin"><span class="glyphicon glyphicon-king"></span>&nbsp;&nbsp; Admin Wordpress</a></li>
        </ul>
    </li>
    <?php endif ?>
    <?php
    $out = ob_get_contents();
    ob_end_clean();
    return $items .= $out;
}
add_filter('wp_nav_menu_items','add_last_nav_item');
