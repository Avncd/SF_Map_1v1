<?php

function twentyfifteenchild_theme_setup()
{

    load_child_theme_textdomain( 'twentyfifteenchild', get_stylesheet_directory() . '/languages' );

    add_action( 'wp_enqueue_scripts', 'twentyfifteenchild_theme_enqueue_styles' );
	function twentyfifteenchild_theme_enqueue_styles() {

	    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

	    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
	    wp_enqueue_style( 'child-style',
	        get_stylesheet_directory_uri() . '/style.css',
	        array( $parent_style ),
	        wp_get_theme()->get('Version')
	    );


	    wp_enqueue_style( 'custom', get_stylesheet_directory_uri().'/css/custom.css' );
    	wp_enqueue_script( 'modernizr',  get_stylesheet_directory_uri() . '/js/modernizr.js', array( 'jquery' ), '1.0', true );

    	wp_enqueue_script( 'plugins',  get_stylesheet_directory_uri() . '/js/plugins.js', array( 'jquery' ), '1.0', true );

    	wp_enqueue_script( 'custom',  get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ), '1.0', true );

		wp_enqueue_script( 'ajax-call',  get_stylesheet_directory_uri() . '/js/ajax-call.js', array( 'jquery' ), '1.0', true );

		wp_localize_script( 'ajax-call', 'ajaxcall', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		));
	}

}
add_action( 'after_setup_theme', 'twentyfifteenchild_theme_setup' );


add_action( 'wp_ajax_nopriv_ajax_get_post_by_month', 'ajax_get_post_by_month' );
add_action( 'wp_ajax_ajax_get_post_by_month', 'ajax_get_post_by_month' );
function ajax_get_post_by_month()
{
    $month = (int) $_POST['month'];

	$args = array(
		'date_query' => array(
			array(
				'month' => $month
			),
		),
	);
	$q = new WP_Query( $args );
	if( $q->have_posts() ) {
		while( $q->have_posts() ) {

			$q->the_post();

			$cat_collection = get_the_category();
			?>
			<li class="ajax-post-call" id="postID<?php echo get_the_ID(); ?>" data-post-id="<?php echo get_the_ID(); ?>">
				<a href="#" class="lists">
					<div class="mask img-holder cover-img">
						<?php the_post_thumbnail(); ?>
					</div>
					<span class="title"><?php the_title(); ?></span>
					<span class="cats"><?php echo $cat_collection[0]->name; ?></span>
				</a>
			</li>
			<?php
		}

	}
	else
	{
		?>
		<li class="not-found">
			<a href="#">
				<span class="title">No Post Found !</span>
			</a>
		</li>
		<?php
	}

    die();
}


add_action( 'wp_ajax_nopriv_ajax_get_post_by_id', 'ajax_get_post_by_id' );
add_action( 'wp_ajax_ajax_get_post_by_id', 'ajax_get_post_by_id' );
function ajax_get_post_by_id()
{
    $post_id = (int) $_POST['post_id'];

	$args = array( 'p' => $post_id );
	$q = new WP_Query( $args );
	if( $q->have_posts() ) {
		while( $q->have_posts() ) {

			$q->the_post();

			$cat_collection = get_the_category();
			?>
			<div class="scroll-container">
				<div id="bar" class="bar">
					<ul class="padded">
						<li class="blue" style="color:#101e2a">Location : <?php echo $cat_collection[0]->name; ?></li>
						<li class="address">
						</li>
					</ul>
				</div>

				<h1 class="padded blue"><?php the_title(); ?></h1>
				<div class="featured-mask img-holder cover-img">
					<?php the_post_thumbnail(); ?>
				</div>

				<div class="content padded">
					<div class="column">
						<?php the_content(); ?>
					</div>
				</div>

				<div class="layout"></div>
			</div>
			<?php
		}

	}
	else
	{
		?>
		<li class="not-found">
			<a href="#">
				<span class="title">No Post Found !</span>
			</a>
		</li>
		<?php
	}

    die();
}
