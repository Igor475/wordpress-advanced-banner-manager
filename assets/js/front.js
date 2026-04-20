(function ($) {
  "use strict";

  function parseBool(value) {
    if (typeof value === "boolean") return value;
    if (typeof value === "number") return value === 1;
    if (!value && value !== 0) return false;
    return String(value).toLowerCase() === "true" || String(value) === "1";
  }

  function getAjaxUrl() {
    if (typeof AB_Front_Ajax !== "undefined" && AB_Front_Ajax.ajax_url) {
      return AB_Front_Ajax.ajax_url;
    }

    if (typeof AB_Front !== "undefined" && AB_Front.ajax_url) {
      return AB_Front.ajax_url;
    }

    if (typeof ajaxurl !== "undefined") {
      return ajaxurl;
    }

    return "";
  }

  function getAjaxNonce() {
    if (typeof AB_Front_Ajax !== "undefined" && AB_Front_Ajax.nonce) {
      return AB_Front_Ajax.nonce;
    }

    return "";
  }

  $(function () {
    var ajaxUrl = getAjaxUrl();
    var nonce = getAjaxNonce();

    $(".ab-banners.ab-carousel").each(function () {
      var $wrap = $(this);

      if ($wrap.data("ab-initialized")) return;
      $wrap.data("ab-initialized", true);

      var $slides = $wrap.find(".ab-slides .ab-banner-item");
      if ($slides.length === 0) return;

      var interval = parseInt($wrap.data("interval"), 10) || 5000;
      var autoplay = parseBool($wrap.data("autoplay"));
      var arrows = parseBool($wrap.data("arrows"));
      var dots = parseBool($wrap.data("dots"));
      var idx = $slides.filter(".active").length ? $slides.filter(".active").index() : 0;
      var timer = null;
      var animSpeed = 400;

      function showSlide(i) {
        if (i === idx) return;

        idx = i;

        $slides
          .stop(true, true)
          .removeClass("active")
          .hide()
          .eq(idx)
          .fadeIn(animSpeed)
          .addClass("active");

        if (dots) {
          var $dots = $wrap.find(".ab-dotx");
          $dots.removeClass("active");
          if ($dots.eq(idx).length) {
            $dots.eq(idx).addClass("active");
          }
        }
      }

      function startAutoplay() {
        stopAutoplay();
        if (autoplay && $slides.length > 1) {
          timer = setInterval(function () {
            showSlide((idx + 1) % $slides.length);
          }, interval);
        }
      }

      function stopAutoplay() {
        if (timer) {
          clearInterval(timer);
          timer = null;
        }
      }

      if ($slides.filter(".active").length === 0) {
        $slides.hide().eq(0).show().addClass("active");
        idx = 0;
        if (dots) {
          $wrap.find(".ab-dot, .ab-dotx").removeClass("active").eq(0).addClass("active");
        }
      } else {
        $slides.hide().removeClass("active").eq(idx).show().addClass("active");
      }

      if (arrows) {
        $wrap.find(".ab-prev, .ab-prevx").off(".abCarousel").on("click.abCarousel", function (e) {
          e.preventDefault();
          stopAutoplay();
          showSlide((idx - 1 + $slides.length) % $slides.length);
          startAutoplay();
        });

        $wrap.find(".ab-next, .ab-nextx").off(".abCarousel").on("click.abCarousel", function (e) {
          e.preventDefault();
          stopAutoplay();
          showSlide((idx + 1) % $slides.length);
          startAutoplay();
        });
      }

      if (dots) {
        $wrap.find(".ab-dotx").off(".abCarousel").on("click.abCarousel", function () {
          var i = parseInt($(this).data("index"), 10);
          if (isNaN(i)) return;
          stopAutoplay();
          showSlide(i);
          startAutoplay();
        });
      }

      startAutoplay();
    });

    $(document).on("click", ".ab-click", function (e) {
      var itemId = $(this).data("id");
      var targetUrl = $(this).attr("href");

      if (!itemId || !ajaxUrl) {
        return;
      }

      e.preventDefault();

      $.post(ajaxUrl, {
        action: "ab_track_click",
        nonce: nonce,
        item_id: itemId,
      }).always(function () {
        if (targetUrl) {
          window.open(targetUrl, "_blank");
        }
      });
    });

    $(".ab-banner-item").each(function () {
      var itemId = $(this).data("id");
      if (!itemId || !ajaxUrl) return;

      $.post(ajaxUrl, {
        action: "ab_register_view",
        nonce: nonce,
        item_id: itemId,
      });
    });
  });
})(jQuery);
