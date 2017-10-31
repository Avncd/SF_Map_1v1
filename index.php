<!DOCTYPE html>
<html class="no-js">

<head>
	<meta charset="utf-8" />

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />


<?php wp_head(); ?>
<script src="http://sailfuture.org/map/wp-content/themes/twentyfifteen-child_103/twentyfifteen-child/js/leaflet.js"></script>
<script src='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.js'></script>
<link href='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.css' rel='stylesheet' />
<script src='http://sailfuture.org/map/wp-content/themes/twentyfifteen-child_103/twentyfifteen-child/js/reqwest.min.js'></script>
<script src='http://sailfuture.org/map/wp-content/themes/twentyfifteen-child_103/twentyfifteen-child/js/Leaflet.MakiMarkers.js'></script>
<script src='http://sailfuture.org/map/wp-content/themes/twentyfifteen-child_103/twentyfifteen-child/js/Leaflet.SpotTracker.js'></script>
<script src="http://sailfuture.org/map/wp-content/themes/twentyfifteen-child_103/twentyfifteen-child/js/jquery.joyride.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js"></script>
<link rel="stylesheet" href="http://cdn.jsdelivr.net/jquery.joyride/2.1/joyride.css" />

<style>
.css-icon {
	color:red;
}
</style>

<script>
function startTime() {
	var today = new Date();
	var h = today.getHours();
	var m = today.getMinutes();
	var s = today.getSeconds();
	m = checkTime(m);
	s = checkTime(s);
	document.getElementById('txt').innerHTML =
	h + ":" + m + ":" + s;
	var t = setTimeout(startTime, 500);
}
function checkTime(i) {
	if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
	return i;
}
</script>

</head>

<body onload="startTime()">
<body onload="startDate()"></body>
<body>
	<div class="map" id="map"></div>
	<div id="place" class="place">
		<div id="place-content" class="scroller">

		</div>
		<a id="close-place" class="close-place"></a>
	</div>

	<div id="places" class="places">
		<div class="scroller">
			<div class="scroll-container">
				<ul id="places-list">

					<?php
						$args = array(
							'date_query' => array(
								array(
									//'month' => 1
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
									<a href="#">
										<div class="mask img-holder cover-img loading">
											<?php the_post_thumbnail(); ?>
										</div>
										<span class="title"><?php the_title(); ?></span>
										<span class="cats"><?php echo $cat_collection[0]->name; ?></span>
									</a>
								</li>
								<?php
							}

						}
					?>
				</ul>
			</div>
		</div>
	</div>

	<div id="overlay" class="overlay">
		<div class="scroller">
			<div class="scroll-container">
				<div id="about" class="about">
					<div class="table">
						<div class="table-cell">
							<div class="centered">

								<!-- <h1>WELCOME</h1>
								<div class="intro-text">Thanks for your Interesting</div> -->

							</div>
						</div>
					</div>
					<div class="copyright">

					</div>
				</div>

			</div>
		</div>


		<!--<nav id="top-menu" class="menu top-menu">
			<ul>
				<li><a id="toggle-overlay">About</a></li>
			</ul>
		</nav>-->

		<nav id="bottom-menu" class="menu bottom-menu">
			<ul>
				<li>
					<a href="#" class="post-month-ajax-call active" data-month-id="0">All</a>
				</li>
				<li>
					<a href="#" class="post-month-ajax-call" data-month-id="10">October '17</a>
				</li>
				<li>
					<a href="#" class="post-month-ajax-call" data-month-id="11">November</a>
				</li>
				<li>
					<a href="#" class="post-month-ajax-call" data-month-id="12">December</a>
				</li>
				<li>
					<a href="#" class="post-month-ajax-call" data-month-id="1">January '18</a>
				</li>
			</ul>
		</nav>
	</div>

	<nav id="mobile-menu" class="menu mobile-menu">
		<div id="home-nav" class="mobile-nav home-nav">
			<a class="mobile-logo"><span></span></a>
			<ul>
				<li class="mobile-filter-button"><a id="mobile-toggle-filter">Filter</a></li>
				<li class="mobile-search-button hidden">
					<form id="search-form" role="search" method="get" class="search-form" action="" autocomplete="off">
						<input id="search-box" type="text" placeholder="Search" value="" />
						<input type="submit" class="search-submit" value="Search">
					</form>
				</li>
			</ul>
		</div>
		<div id="single-nav" class="mobile-nav single-nav">
			<a id="close-mobile" class="close-mobile"></a>
			<ul>
				<li class="directions"><a id="directions" rel="external">Directions</a></li>
			</ul>
		</div>
		<div id="overlay-nav" class="mobile-nav overlay-nav">
			<a id="close-overlay" class="close-mobile"></a>
		</div>
	</nav>

	<nav id="mobile-filter" class="menu mobile-filter">
		<ul>
			<li>
				<a href="#" class="post-month-ajax-call active" data-month-id="0">All</a>
			</li>
			<li>
				<a href="#" class="post-month-ajax-call" data-month-id="10">October '17</a>
			</li>
			<li>
				<a href="#" class="post-month-ajax-call" data-month-id="11">November</a>
			</li>
			<li>
				<a href="#" class="post-month-ajax-call" data-month-id="12">December</a>
			</li>
			<li>
				<a href="#" class="post-month-ajax-call" data-month-id="1">January '18</a>
			</li>
		</ul>
	</nav>

	<div id="intro" class="full intro">
		<div id="intro-content" class="table">
			<div class="table-cell">
				<h1>WELCOME</h1>
				<div class="intro-text">Thanks for your Interesting</div>
			</div>
		</div>
	</div>

	<div class="map-top-header">
		<div class="div-block"><a href="http://sailfuture.org" class="link-block w-inline-block"></a></div>
		<div class="div-block-2"><a href="https://www.facebook.com/SailFuture/" class="facebook twitter w-inline-block"></a><a href="https://www.instagram.com/sailfuture/" target="_blank" class="facebook instagram w-inline-block"></a></div>
	</div>
	<div class="map-bottom-header">
		<div class="text-block">SailFuture Live Tracker</div>
		<div id="txt" class="txt"></div>
		<div id="date" class="date">
			<?php
$blogtime = current_time( 'mysql' );
list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = preg_split( '([^0-9])', $blogtime );
?>
			<div class="html-embed w-embed w-script">
				<script> document.write(new Date().toLocaleDateString()); </script>
			</div>
		</div>
	</div>


<script type="text/javascript">

	(function($) {

		$('.intro').css({
			'left' : '2000px',
			'transition' : '2s',
			'display' : 'none'
		});

		$('#close-place').click(function(){
			$('#place').css({
				'left' : '2000px',
				'transition' : '3s'
			});
		});

		$('#place').css({
			'left' : '2000px'
		});

	}(jQuery));

</script>

<script>
L.mapbox.accessToken = 'pk.eyJ1IjoiaHRob21wc28iLCJhIjoiLVhDOWJNcyJ9.6iETtw-YLSk5DqSDDpyKSg';

var map = L.map('map', {
	center: [51.505, -0.09],
	maxZoom: 14,
	minZoom: 7,
	zoomControl: false,
}).setView([51.505, -0.09], 13);

new L.Control.Zoom({ position: 'bottomright' }).addTo(map);


jQuery(document).ready(function($) {

	<?php if ( have_posts() ) : ?>
	    <?php while ( have_posts() ) : the_post(); ?>

				var marker = new L.marker([<?php the_field('lat'); ?>,<?php the_field('long'); ?>]).on('click', markerOnClick).addTo(map);

				function markerOnClick()
				{
					$(marker._icon).addClass('ajax-post-call').attr('id', 'postID<?php echo get_the_ID(); ?>').attr('data-post-id', '<?php echo get_the_ID(); ?>');
				}

	    <?php endwhile; ?>
	<?php endif; ?>
});




// document.getElementById('fit').addEventListener('click', function() {
//     // map.setView(e.target.getLatLng(),10)
// 		// map.fitBounds(.getBounds());
// 		// map.panTo(new L.LatLng(32.958984,-5.353521));
// });


// Use styleLayer to add a Mapbox style created in Mapbox Studio
L.mapbox.styleLayer('mapbox://styles/hthompso/cj8hjpq0i27qn2rpqe6y2dzvg').addTo(map);
L.MakiMarkers.accessToken = "pk.eyJ1IjoiaHRob21wc28iLCJhIjoiLVhDOWJNcyJ9.6iETtw-YLSk5DqSDDpyKSg"

L.spotTracker('0Ja4Ivs1BMTNaXf3R5ac3QJgWqtfIMUnt', {
    api: 'https://hthompso.carto.com/api/v2/sql',
    url: "{api}?q=SELECT * FROM spotted WHERE feed_id='{feed}' ORDER BY timestamp",
    liveUrl: "{api}?q=SELECT * FROM spotted WHERE feed_id='{feed}' AND timestamp > {timestamp} ORDER BY timestamp",
		fitBounds: 'true',
    OK: {
      icon: L.MakiMarkers.icon({ icon: 'building',  color: '#145291', size: 'm' }),
      title: 'Hytte'
    },
    CUSTOM: {
      icon: L.MakiMarkers.icon({ icon: 'campsite', color: '#145291', size: 'm' }),
      title: 'Telt'
    }
    // onClick: function (evt) {
    //   var message = evt.message;
    //   evt.layer.bindPopup(getPlace(message) + getTime(message.timestamp)).openPopup();
    // }
  }).addTo(map);

    // Define an icon called cssIcon
var cssIcon = L.divIcon({
  // Specify a class name we can refer to in CSS.
  className: 'css-icon',
  // Set marker width and height
  iconSize: [60, 60]
});

// map.fitBounds(spotTracker.getBounds());



// marker.on('click', function() {
//     $(marker._icon).addClass('ajax-post-call');
// });

// var marker1 = L.marker([51.497, -0.09], {
//   title: "marker_1"
// }).addTo(map).bindPopup("Marker 1").on('click', clickZoom);

  // do what ever you want to do here
// var marker = L.marker([<?php the_field('lat'); ?>,<?php the_field('long'); ?>], {icon: cssIcon}).addTo(map).bindPopup("<?php the_title(); ?><br><?php the_field('lat') ?><?php the_field('long');?>").openPopup().on('click', clickZoom);

</script>

<?php wp_footer(); ?>
</body>

</html>
