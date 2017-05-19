<?php
/**
 * accueilalaferme functions and definitions
 *
 * @package accueilalaferme
 */

//////////////////////////////////
// Fonctions Accueil à la ferme //
//////////////////////////////////

global $js_for_layout;
require __DIR__ . '/class/Flash.php';
add_action('send_headers', 'site_router');
function site_router() {
    if(!isset ($_SESSION)){session_start();}
    global $root;
    $conf = require __DIR__ . '/conf.php';
    $root = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    $url = str_replace($root, '', $_SERVER['REQUEST_URI']);
    $url = explode('?', $url, 2);
    $url_path = $url[0];

    if (in_array($url_path, ['login', 'register', 'logout', 'famille', 'profil', 'event_register'])) {
        add_filter('show_admin_bar', '__return_false');
        require __DIR__ . '/class/DB.php';
        require __DIR__ . '/class/User.php';
        require __DIR__ . '/class/Group.php';
        require __DIR__ . '/class/Address.php';

        $confSQL = $conf['confSQL'];
        $DB = new \AccueilALaFerme\DB($confSQL['sql_host'], $confSQL['sql_user'], $confSQL['sql_pass'], $confSQL['sql_db']);

        $page = $url[0];
        $userWP = wp_get_current_user();
        if (!$userWP->ID && in_array($page, ['famille', 'profil', 'event_register']))
            \AccueilALaFerme\Flash::setFlashAndRedirect("Vous devez être connecté pour accéder à l'espace membre.", 'danger', 'login');
        // Auth pages
        if ($page == 'login') {
            require 'tpl-login.php'; die();
        } else if ($page == 'register') {
            require 'tpl-register.php'; die();
        } else if ($page == 'logout') {
            wp_logout();
            header('Location:'.$root); die();
        }

        global $curPerson;
        $curPerson = new \AccueilALaFerme\User($DB, null, $userWP->user_email, $userWP->first_name, $userWP->last_name);
        if (empty($curPerson->data['is_allowed']))
            \AccueilALaFerme\Flash::setFlashAndRedirect("Votre compte est en attente d'approbation. Vous ne pouvez pas accéder à la partie privée du site internet.", 'warning', 'login');
        if ($page == 'profil') {
            require 'tpl-profil.php';
        } else if ($page == 'famille') {
            require 'tpl-famille.php';
        } else if ($page == 'event_register') {
            require 'tpl-event_register.php';
        }
        die();
    }
}

function add_last_nav_item($items) {
    $blogUrl = get_bloginfo('url');
    $userWP = wp_get_current_user();
    global $curPerson;
    ob_start(); ?>
    <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children"><a href="#">Membres</a>
        <ul class="sub-menu" style="display: none;">
            <?php if (!$userWP->ID): ?>
                <li id="se-connecter" class="menu-item menu-item-type-custom menu-item-object-custom se-connecter"><a href="<?= $blogUrl ?>/login"><span class="glyphicon glyphicon-log-in"></span>&nbsp;&nbsp; Se connecter</a></li>
                <li id="register" class="menu-item menu-item-type-custom menu-item-object-custom register"><a href="<?= $blogUrl ?>/register"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp; S'inscrire</a></li>
            <?php else: ?>
                <li id="profil" class="menu-item menu-item-type-custom menu-item-object-custom profil"><a href="<?= $blogUrl ?>/profil"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp; Profil <small><em><?= $userWP->first_name ?></em></small></a></li>
                <li id="famille" class="menu-item menu-item-type-custom menu-item-object-custom famille"><a href="<?= $blogUrl ?>/famille"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;
                    <?php if (!empty($curPerson->groups)): $curGroup = current($curPerson->groups); ?>
                        <?= $curGroup['is_family']?'Famille':'Group' ?> <small><em><?= $curGroup['name'] ?></em></small>
                    <?php else: ?>
                        Créer ma famille/groupe
                    <?php endif; ?>
                </a></li>
                <li id="se-déconnecter" class="menu-item menu-item-type-custom menu-item-object-custom se-déconnecter"><a href="<?= $blogUrl ?>/logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp;&nbsp; Se déconnecter</a></li>
            <?php endif ?>
        </ul>
    </li>
    <?php
    $out = ob_get_contents();
    ob_end_clean();
    return $items .= $out;
}
add_filter('wp_nav_menu_items','add_last_nav_item');

//////////////////
// Sydney stuff //
//////////////////

if ( ! function_exists( 'sydney_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function sydney_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Sydney, use a find and replace
	 * to change 'sydney' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'sydney', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Content width
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 1170; /* pixels */
	}

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size('sydney-large-thumb', 830);
	add_image_size('sydney-medium-thumb', 550, 400, true);
	add_image_size('sydney-small-thumb', 230);
	add_image_size('sydney-service-thumb', 350);
	add_image_size('sydney-mas-thumb', 480);

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'sydney' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'sydney_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // sydney_setup
add_action( 'after_setup_theme', 'sydney_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function sydney_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'sydney' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	//Footer widget areas
	$widget_areas = get_theme_mod('footer_widget_areas', '3');
	for ($i=1; $i<=$widget_areas; $i++) {
		register_sidebar( array(
			'name'          => __( 'Footer ', 'sydney' ) . $i,
			'id'            => 'footer-' . $i,
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}

	//Register the front page widgets
	if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
		register_widget( 'Sydney_List' );
		register_widget( 'Sydney_Services_Type_A' );
		register_widget( 'Sydney_Services_Type_B' );
		register_widget( 'Sydney_Facts' );
		register_widget( 'Sydney_Clients' );
		register_widget( 'Sydney_Testimonials' );
		register_widget( 'Sydney_Skills' );
		register_widget( 'Sydney_Action' );
		register_widget( 'Sydney_Video_Widget' );
		register_widget( 'Sydney_Social_Profile' );
		register_widget( 'Sydney_Employees' );
		register_widget( 'Sydney_Latest_News' );
		register_widget( 'Sydney_Contact_Info' );
		register_widget( 'Sydney_Portfolio' );
	}

}
add_action( 'widgets_init', 'sydney_widgets_init' );

/**
 * Load the front page widgets.
 */
if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
	require get_template_directory() . "/widgets/fp-list.php";
	require get_template_directory() . "/widgets/fp-services-type-a.php";
	require get_template_directory() . "/widgets/fp-services-type-b.php";
	require get_template_directory() . "/widgets/fp-facts.php";
	require get_template_directory() . "/widgets/fp-clients.php";
	require get_template_directory() . "/widgets/fp-testimonials.php";
	require get_template_directory() . "/widgets/fp-skills.php";
	require get_template_directory() . "/widgets/fp-call-to-action.php";
	require get_template_directory() . "/widgets/video-widget.php";
	require get_template_directory() . "/widgets/fp-social.php";
	require get_template_directory() . "/widgets/fp-employees.php";
	require get_template_directory() . "/widgets/fp-latest-news.php";
	require get_template_directory() . "/widgets/fp-portfolio.php";
	require get_template_directory() . "/widgets/contact-info.php";
}

/**
 * Enqueue scripts and styles.
 */
function sydney_scripts() {

	wp_enqueue_style( 'sydney-fonts', esc_url( sydney_google_fonts() ), array(), null );

	wp_enqueue_style( 'sydney-style', get_stylesheet_uri(), '', '20170329' );

	wp_enqueue_style( 'sydney-font-awesome', get_template_directory_uri() . '/fonts/font-awesome.min.css' );

    wp_enqueue_style( 'sydney-ie9', get_template_directory_uri() . '/css/ie9.css', array( 'sydney-style' ) );
	wp_enqueue_style( 'accueilalaferme-style', get_template_directory_uri() . '/css/style.css', array( 'sydney-style' ) );
	wp_style_add_data( 'sydney-ie9', 'conditional', 'lte IE 9' );

    wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/css/bootstrap/js/bootstrap.min.js', array('jquery'),'', true );
	wp_enqueue_script( 'sydney-scripts', get_template_directory_uri() . '/js/scripts.js', array('jquery'),'', true );

	wp_enqueue_script( 'sydney-main', get_template_directory_uri() . '/js/main.min.js', array('jquery'),'20170329', true );

	wp_enqueue_script( 'sydney-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( get_theme_mod('blog_layout') == 'masonry-layout' && (is_home() || is_archive()) ) {

		wp_enqueue_script( 'sydney-masonry-init', get_template_directory_uri() . '/js/masonry-init.js', array('masonry'),'', true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sydney_scripts' );

/**
 * Fonts
 */
if ( !function_exists('sydney_google_fonts') ) :
function sydney_google_fonts() {
	$body_font 		= get_theme_mod('body_font_name', 'Source+Sans+Pro:400,400italic,600');
	$headings_font 	= get_theme_mod('headings_font_name', 'Raleway:400,500,600');

	$fonts     		= array();
	$fonts[] 		= esc_attr( str_replace( '+', ' ', $body_font ) );
	$fonts[] 		= esc_attr( str_replace( '+', ' ', $headings_font ) );

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) )
		), 'https://fonts.googleapis.com/css' );
	}

	return $fonts_url;
}
endif;

/**
 * Enqueue Bootstrap
 */
function sydney_enqueue_bootstrap() {
	wp_enqueue_style( 'sydney-bootstrap', get_template_directory_uri() . '/css/bootstrap/css/bootstrap.min.css', array(), true );
}
add_action( 'wp_enqueue_scripts', 'sydney_enqueue_bootstrap', 9 );

/**
 * Change the excerpt length
 */
function sydney_excerpt_length( $length ) {

  $excerpt = get_theme_mod('exc_lenght', '55');
  return $excerpt;

}
add_filter( 'excerpt_length', 'sydney_excerpt_length', 999 );

/**
 * Blog layout
 */
function sydney_blog_layout() {
	$layout = get_theme_mod('blog_layout','classic');
	return $layout;
}

/**
 * Menu fallback
 */
function sydney_menu_fallback() {
	if ( current_user_can('edit_theme_options') ) {
		echo '<a class="menu-fallback" href="' . admin_url('nav-menus.php') . '">' . __( 'Create your menu here', 'sydney' ) . '</a>';
	}
}

/**
 * Header image overlay
 */
function sydney_header_overlay() {
	$overlay = get_theme_mod( 'hide_overlay', 0);
	if ( !$overlay ) {
		echo '<div class="overlay"></div>';
	}
}

/**
 * Header video
 */
function sydney_header_video() {

	if ( !function_exists('the_custom_header_markup') ) {
		return;
	}

	$front_header_type 	= get_theme_mod( 'front_header_type' );
	$site_header_type 	= get_theme_mod( 'site_header_type' );

	if ( ( get_theme_mod('front_header_type') == 'core-video' && is_front_page() || get_theme_mod('site_header_type') == 'core-video' && !is_front_page() ) ) {
		the_custom_header_markup();
	}
}

/**
 * Polylang compatibility
 */
if ( function_exists('pll_register_string') ) :
function sydney_polylang() {
	for ( $i=1; $i<=5; $i++) {
		pll_register_string('Slide title ' . $i, get_theme_mod('slider_title_' . $i), 'Sydney');
		pll_register_string('Slide subtitle ' . $i, get_theme_mod('slider_subtitle_' . $i), 'Sydney');
	}
	pll_register_string('Slider button text', get_theme_mod('slider_button_text'), 'Sydney');
	pll_register_string('Slider button URL', get_theme_mod('slider_button_url'), 'Sydney');
}
add_action( 'admin_init', 'sydney_polylang' );
endif;

/**
 * Preloader
 */
function sydney_preloader() {
	?>
	<div class="preloader">
	    <div class="spinner">
	        <div class="pre-bounce1"></div>
	        <div class="pre-bounce2"></div>
	    </div>
	</div>
	<?php
}
add_action('sydney_before_site', 'sydney_preloader');

/**
 * Header clone
 */
function sydney_header_clone() {

	$front_header_type 	= get_theme_mod('front_header_type','slider');
	$site_header_type 	=get_theme_mod('site_header_type');

	if ( ( $front_header_type == 'nothing' && is_front_page() ) || ( $site_header_type == 'nothing' && !is_front_page() ) ) { ?>

	<div class="header-clone"></div>

	<?php }
}
add_action('sydney_before_header', 'sydney_header_clone');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Page builder support
 */
require get_template_directory() . '/inc/page-builder.php';

/**
 * Slider
 */
require get_template_directory() . '/inc/slider.php';

/**
 * Styles
 */
require get_template_directory() . '/inc/styles.php';

/**
 * Theme info
 */
require get_template_directory() . '/inc/theme-info.php';

/**
 * Woocommerce basic integration
 */
require get_template_directory() . '/inc/woocommerce.php';

/**
 * Upsell
 */
require get_template_directory() . '/inc/upsell/class-customize.php';

/**
 * Demo content
 */
require_once dirname( __FILE__ ) . '/demo-content/setup.php';

/**
 *TGM Plugin activation.
 */
require_once dirname( __FILE__ ) . '/plugins/class-tgm-plugin-activation.php';

// add_action( 'tgmpa_register', 'sydney_recommend_plugin' );
// function sydney_recommend_plugin() {

 //    $plugins[] = array(
 //            'name'               => 'Page Builder by SiteOrigin',
 //            'slug'               => 'siteorigin-panels',
 //            'required'           => false,
 //    );

	// if ( !function_exists('wpcf_init') ) {
	//     $plugins[] = array(
	// 	        'name'               => 'Sydney Toolbox - custom posts and fields for the Sydney theme',
	// 	        'slug'               => 'sydney-toolbox',
	// 	        'required'           => false,
	// 	);
	// }

    // tgmpa( $plugins);

// }


