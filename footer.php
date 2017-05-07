<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Sydney
 */
?>
			</div>
		</div>
	</div><!-- #content -->

	<?php do_action('sydney_before_footer'); ?>

	<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
		<?php get_sidebar('footer'); ?>
	<?php endif; ?>

    <a class="go-top"><i class="fa fa-angle-up"></i></a>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info container">
            <h3>Accueil à la ferme</h3>
            <br>
            <br>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->

	<?php do_action('sydney_after_footer'); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

<?php
global $js_for_layout;
$root = get_template_directory_uri();

if (!empty($js_for_layout)) {
    foreach ($js_for_layout as $v) { ?>
      <?php if ($v == 'angularjs') { ?>
                <script src="<?= $root.'/js/lib/jquery.min.js' ?>"></script>
                <script src="<?= $root.'/css/bootstrap/js/bootstrap.min.js' ?>"></script>
                <script src="<?= $root.'/js/lib/angular.min.js' ?>"></script>
                <script src="<?= $root.'/js/lib/angular-sanitize.min.js' ?>"></script>
                <script src="<?= $root.'/js/lib/angular-touch.min.js' ?>"></script>
                <script src="<?= $root.'/js/lib/angular-animate.min.js' ?>"></script>
                <script src="<?= $root.'/js/lib/angular-locale_fr-fr.js' ?>"></script>
                <script src="<?= $root.'/js/lib/ui-bootstrap-tpls.min.js' ?>"></script>
      <?php } else if (file_exists(__DIR__.'/js/'.$v.'.js')) { ?>
                <script src="<?= $root.'/js/'.$v.'.js' ?>"></script>
      <?php } else if (file_exists(__DIR__.'/js/'.$v)) { ?>
                <script src="<?= $root ?>/js/<?= $v; ?>"></script>
      <?php } else if (false !== strpos($v, '<script type="text/javascript">')) { ?>
                <?= $v ?>
      <?php }else{ ?>
                <script type="text/javascript">
                  <?= $v ?>
                </script>
      <?php }
    }
} ?>
</body>
</html>
