// =========================================================
//  Paper Dashboard - v2.0.0
// =========================================================
//
//  Product Page: https://www.creative-tim.com/product/paper-dashboard
//  Copyright 2019 Creative Tim (https://www.creative-tim.com)
//  UPDIVISION (https://updivision.com)
//  Licensed under MIT (https://github.com/creativetimofficial/paper-dashboard/blob/master/LICENSE)
//
//  Coded by Creative Tim & UPDIVISION
//
// =========================================================
//
// The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

(function() {
  var isWindows = navigator.platform.indexOf('Win') > -1 ? true : false;

  if (isWindows) {
    // if we are on windows OS we activate the perfectScrollbar function
    jQuery('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

    jQuery('html').addClass('perfect-scrollbar-on');
  } else {
    jQuery('html').addClass('perfect-scrollbar-off');
  }
})();

var transparent = true;
var transparentDemo = true;
var fixedTop = false;

var navbar_initialized = false;
var backgroundOrange = false;
var sidebar_mini_active = false;
var toggle_initialized = false;

var seq = 0, delays = 80, durations = 500;
var seq2 = 0, delays2 = 80, durations2 = 500;

jQuery(document).ready(function($) {

  if (jQuery('.full-screen-map').length == 0 && jQuery('.bd-docs').length == 0) {
    // On click navbar-collapse the menu will be white not transparent
    jQuery('.collapse').on('show.bs.collapse', function() {
      jQuery(this).closest('.navbar').removeClass('navbar-transparent').addClass('bg-white');
    }).on('hide.bs.collapse', function() {
      jQuery(this).closest('.navbar').addClass('navbar-transparent').removeClass('bg-white');
    });
  }

  paperDashboard.initMinimizeSidebar();

  var $navbar = jQuery('.navbar[color-on-scroll]');
  var scroll_distance = $navbar.attr('color-on-scroll') || 500;

  // Check if we have the class "navbar-color-on-scroll" then add the function to remove the class "navbar-transparent" so it will transform to a plain color.
  if (jQuery('.navbar[color-on-scroll]').length != 0) {
    paperDashboard.checkScrollForTransparentNavbar();
    jQuery(window).on('scroll', paperDashboard.checkScrollForTransparentNavbar);
  }

  jQuery('.form-control').on("focus", function() {
    jQuery(this).parent('.input-group').addClass("input-group-focus");
  }).on("blur", function() {
    jQuery(this).parent(".input-group").removeClass("input-group-focus");
  });

  // Activate bootstrapSwitch
  jQuery('.bootstrap-switch').each(function() {
    var $this = jQuery(this);
    var data_on_label = $this.data('on-label') || '';
    var data_off_label = $this.data('off-label') || '';

    $this.bootstrapSwitch({
      onText: data_on_label,
      offText: data_off_label
    });
  });
});

jQuery(document).on('click', '.navbar-toggle', function() {
  var $toggle = jQuery(this);

  if (paperDashboard.misc.navbar_menu_visible == 1) {
    jQuery('html').removeClass('nav-open');
    paperDashboard.misc.navbar_menu_visible = 0;
    setTimeout(function() {
      $toggle.removeClass('toggled');
      jQuery('#bodyClick').remove();
    }, 550);

  } else {
    setTimeout(function() {
      $toggle.addClass('toggled');
    }, 580);

    var div = '<div id="bodyClick"></div>';
    jQuery(div).appendTo('body').click(function() {
      jQuery('html').removeClass('nav-open');
      paperDashboard.misc.navbar_menu_visible = 0;
      setTimeout(function() {
        $toggle.removeClass('toggled');
        jQuery('#bodyClick').remove();
      }, 550);
    });

    jQuery('html').addClass('nav-open');
    paperDashboard.misc.navbar_menu_visible = 1;
  }
});

jQuery(window).resize(function() {
  // reset the seq for charts drawing animations
  seq = seq2 = 0;

  if (jQuery('.full-screen-map').length == 0 && jQuery('.bd-docs').length == 0) {
    var $navbar = jQuery('.navbar');
    var isExpanded = jQuery('.navbar').find('[data-toggle="collapse"]').attr("aria-expanded");
    if ($navbar.hasClass('bg-white') && jQuery(window).width() > 991) {
      $navbar.removeClass('bg-white').addClass('navbar-transparent');
    } else if ($navbar.hasClass('navbar-transparent') && jQuery(window).width() < 991 && isExpanded != "false") {
      $navbar.addClass('bg-white').removeClass('navbar-transparent');
    }
  }
});

var paperDashboard = {
  misc: {
    navbar_menu_visible: 0
  },

  initMinimizeSidebar: function() {
    if (jQuery('.sidebar-mini').length != 0) {
      sidebar_mini_active = true;
    }

    jQuery('#minimizeSidebar').click(function() {
      var $btn = jQuery(this);

      if (sidebar_mini_active == true) {
        jQuery('body').addClass('sidebar-mini');
        sidebar_mini_active = true;
        paperDashboard.showSidebarMessage('Sidebar mini activated...');
      } else {
        jQuery('body').removeClass('sidebar-mini');
        sidebar_mini_active = false;
        paperDashboard.showSidebarMessage('Sidebar mini deactivated...');
      }

      // we simulate the window Resize so the charts will get updated in realtime.
      var simulateWindowResize = setInterval(function() {
        window.dispatchEvent(new Event('resize'));
      }, 180);

      // we stop the simulation of Window Resize after the animations are completed
      setTimeout(function() {
        clearInterval(simulateWindowResize);
      }, 1000);
    });
  },

  showSidebarMessage: function(message) {
    try {
      jQuery.notify({
        icon: "now-ui-icons ui-1_bell-53",
        message: message
      }, {
        type: 'info',
        timer: 4000,
        placement: {
          from: 'top',
          align: 'right'
        }
      });
    } catch (e) {
      console.log('Notify library is missing, please make sure you have the notifications library added.');
    }
  }
};

function hexToRGB(hex, alpha) {
  var r = parseInt(hex.slice(1, 3), 16),
    g = parseInt(hex.slice(3, 5), 16),
    b = parseInt(hex.slice(5, 7), 16);

  if (alpha) {
    return "rgba(" + r + ", " + g + ", " + b + ", " + alpha + ")";
  } else {
    return "rgb(" + r + ", " + g + ", " + b + ")";
  }
}
