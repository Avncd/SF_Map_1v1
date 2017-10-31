var pageLoaded = false;
var scrollTop = 0;
var scrollBottom = 0;
var nanoOptions = {
	contentClass: "scroll-container",
	sliderMaxHeight: 200,
	sliderMinHeight: 100
};

// Ready to go

(function($) {

	$(document).ready(function(){
		
		var $body = $("body");
		var $html = $("html");
		var siteTitle = $body.data("site-title");
		var siteURL = $body.data("site-url");
		var is404 = $body.is(".error404");
		var fromSingle = $body.is(".single-places");
		
		if(!Modernizr.mousehover)
		{
			var scrollClass = ".scroll-container";
			var $placeScroller = $("#places").find(".scroll-container");
		}
		else
		{
			var scrollClass = ".scroller";
			var $placeScroller = $("#places").find(".scroller");
		}
		
		// IE fixes
		var IEversion = detectIE();
		var oldIE = false;
		if(IEversion && IEversion <= 11){ oldIE = true; $html.addClass("old-ie"); }
		
		
		// Transit animation fallback for older browsers
		
		var slideDuration = 700;
		var opacityDuration = 600;
		
		var easeInOut = "ease-in-out";
		if(!Modernizr.csstransitions)
		{
			$.fn.transition = $.fn.animate;
			easeInOut = "easeInOutQuint";
			
			slideDuration = 0;
			opacityDuration = 0;
		}
		
		
		// Hammer defaults
		
		if(Modernizr.csstransitions && !Modernizr.mousehover)
		{
			Hammer.defaults.domEvents = true;
			Hammer.defaults.direction = Hammer.DIRECTION_HORIZONTAL;
		}
		
		
		// Prevent AJAX caching
		
		$.ajaxSetup ({
		    cache: false
		});

		
		// Get base URL for use in scripts
		
		var baseURL = location.protocol + "//" + location.hostname + (location.port && ":" + location.port);
		var siteURL = $body.data("site-url");
		var siteTitle = $body.data("site-title");
		
		
		// Init gallery
		
		var isAnimating = false;	
		var windowWidth = $(window).width();
		var windowHeight = $(window).height();
		var slideWidth = 0;
		
		if(windowWidth >= 768)
			var zoomedMap = false;
		else
			var zoomedMap = true;
		
		var isScrolling = false;
		var noScrollOnce = false;
		var lastScrollTop = 0;
		var prevScrollTop = 0;
		
		

		// Resize handler
		
		var $currentPlace;
		var currentMarker;
		
		handleResize = function(e){
			
			var resizeEvent = typeof e !== 'undefined' ? e : false;
				
			if(resizeEvent && pageLoaded)
				$html.addClass("resizing");
			
			var oldWindowWidth = windowWidth;		
			windowWidth = $(window).width();
			windowHeight = $(window).height();
					
			if(windowWidth >= 768)
				var zoomedMap = false;
			else
				var zoomedMap = true;
			
			$(".cover-img").coverImages();		
			$(".fit-img").fitImages();

			
			if(oldWindowWidth >= 768 && windowWidth < 768)
			{
				//initFlickity();
			}
			else if(windowWidth >= 768 && oldWindowWidth < 768)
			{
				$controlledScroll.flickity("destroy");
				
			}
				
			Waypoint.refreshAll();

			

			
			setTimeout(function(){
				$html.removeClass("resizing");
				
				/*if(windowWidth < 768)
					$body.height(window.innerHeight);*/
			}, 150);
			
		}
		
		if(!("onorientationchange" in window))
	  	{	
			$(window).resize(function() {
		  		handleResize(true);
			});
	  	}
	  	else
	  	{
	  		$(window).on('orientationchange', function(event){
		  		$html.addClass("resizing");

		        setTimeout(function(){
			        handleResize(true);
			        
			        setTimeout(function(){
			        	$html.removeClass("resizing");
			        }, 500);
		        }, 100);
		    });
		}
		
		// Thumbs padding
		
		$.fn.imgPadding = function() {		
			return this.each(function() {
				
				var $parent = $(this);
			
				$parent.find(".img-holder").each(function(){
					var $holder = $(this);
					
					if(!$holder.is(".padded") && !$holder.is(".no-padding"))
					{
						var $img = $holder.find("img, video");
						var ratio = $img.attr("height")/$img.attr("width");
						
						$holder.css("padding-bottom", ratio*100 + "%").addClass("padded");
					}
				});
				
				Waypoint.refreshAll();
			})
		}

		
		handleResize(false);
		
		
		// Load images
		
		$.fn.loadImages = function() {		
			return this.each(function() {
				
				var $parent = $(this);
				
				if($parent.is(".scroll-container"))
				{
					var $context = $parent;
				}
				else
				{
					var $context = $parent.find(".scroll-container");
					
					if(!$context.length)
						$context = $parent.parents(".scroll-container");
				}
				
				var context = $context[0];			
				
							
				$parent.find(".img-holder").each(function(index){
			   		var $box = $(this);
			   		var $img = $(this).find("img").eq(0);
			   		
			   		if($box.data("box-ratio"))
			   		{		   		
			   			$box.attr("style", "padding-bottom: " + ($box.data("box-ratio")*100).toFixed(2) + "%");
			   		}
			   		
			   		if($box.is(".cover-img"))
			   		{
			   			$box.coverImages();
			   		} else if($box.is(".fit-img"))
			   		{
			   			$box.fitImages();
			   		}
			   		
			   		$box.waypoint({
			   			handler: function(direction) {
			   				
			   				if($box.is(".cover-img"))
					   		{
					   			$box.coverImages();
					   		} else if($box.is(".fit-img"))
					   		{
					   			$box.fitImages();
					   		}
					   		
			   				$box.loadImage();
			   				this.destroy();
			   			},
			   			context: context,
			   			offset: "150%"
			   		});
			   	})
			});
		}
		
		$("#place, #places").loadImages();
		
		$("#bio-ads").loadImage();
		
		// Handle scroll
		
		// Scroll interval
		
		bindScrollStart = function(){			
			isScrolling = true;
			//prevScrollTop = $scroller.scrollTop();
			
			clearTimeout(scrollTimeout);		
		};
		
		/*bindScroll = function(){									
			isScrolling = true;
			scrollTop = $scroller.scrollTop();
		};*/
		
		var isHome = $body.is(".home");
		var $controlledScroll;
		var flickity;
		var selectedIndex;
		var markerClick = false;
		
		if(!Modernizr.mousehover)
		{
			$(window).on("touchmove", function(e){
				/*e.preventDefault();*/
			});
			
			$body.on("touchmove", ".scroller .scroll-container", function() {
				//e.stopPropagation();
				Waypoint.refreshAll();
			});/**/
			
			/*var delayedScroll;
			$("#places .scroll-container").on("scroll", function(e){		
									
				clearTimeout(delayedScroll);
				delayedScroll = setTimeout(function(){
					if(currentMarker)
						currentMarker.setIcon(placeIcon);
					
					if(!zIndex)
						zIndex = google.maps.Marker.MAX_ZINDEX;
					zIndex++;
									
					map.setCenter($currentPlace.data("lat"), $currentPlace.data("lng"));
					currentMarker = $currentPlace.data("marker");
					currentMarker.setIcon(placeIconMouse);
					currentMarker.setZIndex(zIndex);
				}, 200);
			});*/
		}
		
		
		// Handle keyboard navigation
	     
	    $(document).keydown(function(e) {    
			var isSingle = $body.is(".single-placess");
			
			switch(e.keyCode) { 
				case 39:
					// RIGHT
					
					break;
				case 37:
					// LEFT
							
					break;
				case 38:
					// UP
							 
					break;
				case 40:
					// DOWN
					
					break;
				case 27:
					// ESC
					
					if(isSingle)
					{
						ajaxInProgress = true;				
						human = true;
						
						$("#hamburger").trigger("click");				
					}
					
					if($html.is(".overlay-open"))
						closeOverlay();
													             
					break;
			     break;
	        }
	    });

		
		// Make external links open in new window
		
		$.fn.addTargets = function() {		
			return this.each(function() {
							
				var $container = $(this);
		
				$container.find("a[rel=external], a[href^='http:']:not([href*='" + window.location.host + "']), a[href^='https:']:not([href*='" + window.location.host + "'])").each(function(){
					$(this).attr("target", "_blank");
				});
				
				/*$container.find("a[href*='" + window.location.host + "']:not(.meta-link)").each(function(){
					$(this).addClass("internal");
				});*/
			});
		}
		
		$("body").addTargets();
		
		$(document).on("click", ".external", function(e){
			e.stopImmediatePropagation();
			e.stopPropagation();

			var newHref = $(this).attr("data-href");
			window.open(newHref, '_blank');
		});
		
		
		// AJAX nav
		
		var human = false;
		var historyPages = [];
		var ajaxInProgress = false;
		var ajaxHover = false;
		var fromArchive = false;
		
		
		// Overlay
		
		var $layers = null;
		var menuTimeout;
		var animatingMenu = false;
		
		$(document).on("click", "#toggle-overlay", function(e){

			if(!animatingMenu)
			{			
				e.preventDefault();
				animatingMenu = true;
										
				$("#top-menu, #bottom-menu").transition({ opacity: 0 }, slideDuration, easeInOut, function(){
					animatingMenu = false;
				});
				
				$("#overlay").transition({ x: -windowWidth + 44 }, slideDuration, "easeInOutQuint", function(){
					$html.addClass("overlay-open");
					
					$("#top-menu, #bottom-menu, #overlay").removeAttr("style");
				});
			}
			else if($html.is(".overlay-open") && !animatingMenu)
			{			
				closeOverlay();
			}
		});
		
		closeOverlay = function(){
			
			animatingMenu = true;
				
			//$("#hamburger").removeClass("closeburger");
			
			$("#bottom-menu, #top-menu").attr("style", "visibility: visible").transition({ opacity: 1 }, slideDuration, easeInOut, function(){
				animatingMenu = false;
			});
			
			$("#overlay").transition({ x: windowWidth - 44 }, slideDuration, "easeInOutQuint", function(){
				$html.removeClass("overlay-open");
				
				$("#bottom-menu, #top-menu, #overlay").removeAttr("style");
			});
		}
		
		$(document).on("click", "#about a", function(e){
			e.stopPropagation();
		});
		
		if(Modernizr.mousehover)
			$(document).on("click", "#about", function(){
				closeOverlay();
			});
		
		$(document).on("click", "#mobile-toggle-overlay", function(e){

			if(!animatingMenu)
			{			
				e.preventDefault();
				animatingMenu = true;
				
				$("#overlay").transition({ x: -windowWidth }, slideDuration, "easeInOutQuint", function(){
					$html.addClass("overlay-open");
					animatingMenu = false;
					
					$("#overlay").removeAttr("style");
				});
				
				$("#home-nav").transition({ opacity: 0 }, slideDuration/2, easeInOut, function(){
					$("#overlay-nav").attr("style", "visibility: visible").transition({ opacity: 1 }, slideDuration/2, easeInOut, function(){
						$("#overlay-nav, #home-nav").removeAttr("style");
					});
				});
			}
		});
		
		closeMobileOverlay = function(){
			
			animatingMenu = true;
			
			$("#overlay").transition({ x: windowWidth }, slideDuration, "easeInOutQuint", function(){
				$html.removeClass("overlay-open");
				animatingMenu = false;
				
				$("#overlay").removeAttr("style");
			});
			
			$("#overlay-nav").transition({ opacity: 0 }, slideDuration/2, easeInOut, function(){
				$("#home-nav").attr("style", "visibility: visible").transition({ opacity: 1 }, slideDuration/2, easeInOut, function(){
					$("#overlay-nav, #home-nav").removeAttr("style");
				});
			});
		}
		
		$(document).on("click", "#close-overlay", function(e){
			closeMobileOverlay();
		});
		
		
		$(document).on("click", "#mobile-toggle-filter", function(e){
			if(!animatingMenu)
			{
				animatingMenu = true;
				
				if(!$("#mobile-filter").is(".filter-open"))
				{
					$("#mobile-toggle-filter").addClass("active");
					
					$("#mobile-filter").transition({ y: 44 }, slideDuration/2, "easeOutQuint", function(){
						$("#mobile-filter").addClass("filter-open").removeAttr("style");
						animatingMenu = false;
					});
				}
				else
				{
					$("#mobile-toggle-filter").removeClass("active");
					
					$("#mobile-filter").transition({ y: 0 }, slideDuration/2, "easeOutQuint", function(){
						$("#mobile-filter").removeClass("filter-open").removeAttr("style");
						animatingMenu = false;
					});
				}
			}
		});
			
		/* MAP */
		
		
		
		
		if($("#single-directions").length)
			$("#directions").attr("href", $("#single-directions").attr("href"));



		// Search
		
		var searchSnapshot;
		

		
		$(document).on("submit", "#search-form", function(e){
			e.preventDefault();
			doSearch($("#search-box").val());
		});
		
		$(document).on("focus", "#search-box", function(e){
			var $obj = $(this);
			$obj.data("placeholder", $obj.attr("placeholder")).attr("placeholder", "");
		});
		
		$(document).on("blur", "#search-box", function(e){
			var $obj = $(this);
			$obj.attr("placeholder", $obj.data("placeholder"));
		});
		
		/*$(document).on("mouseleave", "#black-bar", function(e){
			if($("#search-box").is(":focus"))
				$("#search-box").blur();
		});*/
		
		/*$(document).on("keypress", "#search-box", function(e){
			
			if(e.keyCode == 13)
			{
				e.preventDefault();
			
				$("#search-box").blur();
				doSearch($("#search-box").val());
			}
		});*/	
		
		/*$(document).on("keyup", "#search-box", function(e){		
					
			if(lastSearch !== $("#search-box").val() && !Modernizr.touch)
				doSearch($("#search-box").val());
		
		});*/
		

	});

	$(window).on("load", function(){

		pageLoaded = true;
		
		setTimeout(function(){
			Waypoint.refreshAll();
			
			if($(window).width() < 768 && $("#intro").length)
				$("#intro").empty().remove();
		}, 150);
	});

}(jQuery));

