document.addEventListener("DOMContentLoaded", function () {
  const timeInputs = document.querySelectorAll(".ab-time");

  timeInputs.forEach((input) => {
    input.addEventListener("input", function () {
      let value = this.value.replace(/\D/g, "");
      if (value.length >= 5) {
        this.value =
          value.substring(0, 2) +
          ":" +
          value.substring(2, 4) +
          ":" +
          value.substring(4, 6);
      } else if (value.length >= 3) {
        this.value = value.substring(0, 2) + ":" + value.substring(2, 4);
      } else {
        this.value = value;
      }
    });

    input.addEventListener("blur", function () {
      let parts = this.value.split(":");
      let h = Math.min(parseInt(parts[0], 10) || 0, 23);
      let m = Math.min(parseInt(parts[1], 10) || 0, 59);
      let s = Math.min(parseInt(parts[2], 10) || 0, 59);

      if (parts.length === 3) {
        this.value =
          String(h).padStart(2, "0") +
          ":" +
          String(m).padStart(2, "0") +
          ":" +
          String(s).padStart(2, "0");
      } else if (parts.length === 2) {
        this.value =
          String(h).padStart(2, "0") + ":" + String(m).padStart(2, "0") + ":00";
      } else if (parts.length === 1 && this.value.length > 0) {
        this.value = String(h).padStart(2, "0") + ":00:00";
      }
    });
  });

  const dateInputs = document.querySelectorAll(".ab-date");

  dateInputs.forEach((input) => {
    input.addEventListener("input", function () {
      let value = this.value.replace(/\D/g, "");
      if (value.length >= 5) {
        this.value = value.substring(0, 4) + "-" + value.substring(4, 6) + "-" + value.substring(6, 8);
      } else if (value.length >= 3) {
        this.value = value.substring(0, 4) + "-" + value.substring(4, 6);
      } else {
        this.value = value;
      }
    });
  });
});

jQuery(document).ready(function ($) {
  const $itemsTable = $("#ab-items-table");
  const $itemsList = $("#ab-items-list");

  function initPickers(scope) {
    scope.find(".ab-date").datepicker({ dateFormat: "yy-mm-dd" });
    scope.find(".ab-time").timepicker({ timeFormat: "HH:mm:ss" });
  }

  function createPreviewMarkup(type) {
    return '<div class="ab-preview-wrap"><span class="ab-preview-placeholder">Selecione a imagem ' + type + '</span></div>';
  }

  function updateOrdering() {
    $itemsList.find(".ab-item-row").each(function (index) {
      $(this).find('input[name="ordering[]"]').val(index + 1);
      $(this).find(".ab-item-index").text(index + 1);
    });
  }

  function initSortable() {
    $itemsList.sortable({
      handle: ".ab-handle",
      axis: "y",
      update: updateOrdering,
    });
  }

  function getPreviewUrl(attachment) {
    if (attachment.sizes && attachment.sizes.medium) {
      return attachment.sizes.medium.url;
    }
    if (attachment.sizes && attachment.sizes.thumbnail) {
      return attachment.sizes.thumbnail.url;
    }
    if (attachment.type === "image") {
      return attachment.url;
    }
    return attachment.icon || attachment.url;
  }

  function buildItemRow() {
    return $(
      '<tr class="ab-item-row">' +
        '<td>' +
          '<div class="ab-item-card">' +
            '<div class="ab-item-header">' +
              '<div class="ab-item-title-group">' +
                '<span class="ab-handle" role="button" tabindex="0" aria-label="Reordenar item">☰</span>' +
                '<strong class="ab-item-title">Item <span class="ab-item-index">0</span></strong>' +
              '</div>' +
              '<div class="ab-item-meta">' +
                '<span class="ab-stat-pill">Views: <strong>0</strong></span>' +
                '<span class="ab-stat-pill">Cliques: <strong>0</strong></span>' +
                '<button type="button" class="button button-link-delete ab-remove">Remover item</button>' +
              '</div>' +
              '<input type="hidden" class="ordering" name="ordering[]" value="0">' +
              '<input type="hidden" name="item_id[]" value="">' +
            '</div>' +
            '<div class="ab-item-grid">' +
              '<div class="ab-image-field">' +
                '<label class="ab-field-label">Imagem Desktop</label>' +
                '<div class="ab-image-panel">' +
                  createPreviewMarkup("desktop") +
                  '<input type="hidden" name="image_id[]" value="">' +
                  '<div class="ab-image-actions">' +
                    '<button type="button" class="button ab-upload">Selecionar</button>' +
                    '<button type="button" class="button ab-remove-image">Remover</button>' +
                  '</div>' +
                '</div>' +
              '</div>' +
              '<div class="ab-image-field">' +
                '<label class="ab-field-label">Imagem Mobile</label>' +
                '<div class="ab-image-panel">' +
                  createPreviewMarkup("mobile") +
                  '<input type="hidden" name="image_id_mobile[]" value="">' +
                  '<div class="ab-image-actions">' +
                    '<button type="button" class="button ab-upload-mobile">Selecionar</button>' +
                    '<button type="button" class="button ab-remove-image-mobile">Remover</button>' +
                  '</div>' +
                '</div>' +
              '</div>' +
              '<div class="ab-field ab-field-full">' +
                '<label class="ab-field-label">Link de destino</label>' +
                '<input type="url" name="url[]" placeholder="https://exemplo.com/produto/">' +
              '</div>' +
              '<div class="ab-field-group">' +
                '<label class="ab-field-label">Início da exibição</label>' +
                '<div class="ab-inline-fields">' +
                  '<input type="text" class="ab-date" name="start_date[]" placeholder="YYYY-MM-DD">' +
                  '<input type="text" class="ab-time" name="start_time[]" placeholder="HH:MM:SS">' +
                '</div>' +
              '</div>' +
              '<div class="ab-field-group">' +
                '<label class="ab-field-label">Fim da exibição</label>' +
                '<div class="ab-inline-fields">' +
                  '<input type="text" class="ab-date" name="end_date[]" placeholder="YYYY-MM-DD">' +
                  '<input type="text" class="ab-time" name="end_time[]" placeholder="HH:MM:SS">' +
                '</div>' +
              '</div>' +
              '<div class="ab-field-group ab-status-field">' +
                '<label class="ab-field-label">Status</label>' +
                '<select name="item_status[]">' +
                  '<option value="1">Ativo</option>' +
                  '<option value="0">Inativo</option>' +
                '</select>' +
              '</div>' +
            '</div>' +
          '</div>' +
        '</td>' +
      '</tr>'
    );
  }

  initPickers($(document));
  initSortable();
  updateOrdering();

  $(document).on("click", ".ab-upload, .ab-upload-mobile", function (e) {
    e.preventDefault();

    const button = $(this);
    const isMobile = button.hasClass("ab-upload-mobile");
    const previewClass = isMobile ? "ab-preview-mobile" : "ab-preview";
    const inputName = isMobile ? 'image_id_mobile[]' : 'image_id[]';
    const imageLabel = isMobile ? "Mobile" : "Desktop";

    const frame = wp.media({
      title: "Selecionar imagem " + imageLabel,
      multiple: false,
    });

    frame.on("select", function () {
      const attachment = frame.state().get("selection").first().toJSON();
      const previewUrl = getPreviewUrl(attachment);
      const panel = button.closest(".ab-image-panel");
      const wrap = panel.find(".ab-preview-wrap");

      panel.find('input[name="' + inputName + '"]').val(attachment.id);
      wrap.addClass("has-image").html('<img class="' + previewClass + '" src="' + previewUrl + '" alt="">');
    });

    frame.open();
  });

  $(document).on("click", ".ab-remove-image, .ab-remove-image-mobile", function (e) {
    e.preventDefault();

    const button = $(this);
    const isMobile = button.hasClass("ab-remove-image-mobile");
    const inputName = isMobile ? 'image_id_mobile[]' : 'image_id[]';
    const placeholderType = isMobile ? "mobile" : "desktop";
    const panel = button.closest(".ab-image-panel");

    panel.find('input[name="' + inputName + '"]').val("");
    panel.find(".ab-preview-wrap").removeClass("has-image").replaceWith(createPreviewMarkup(placeholderType));
  });

  $("#ab-add-item").on("click", function (e) {
    e.preventDefault();

    const $row = buildItemRow();
    $itemsList.append($row);
    initPickers($row);
    updateOrdering();
  });

  $(document).on("click", ".ab-remove", function (e) {
    e.preventDefault();
    $(this).closest(".ab-item-row").remove();
    updateOrdering();
  });

  $(document).on("click", ".ab-delete", function (e) {
    e.preventDefault();
    if (!confirm("Excluir este banner?")) return;
    $.post(
      ajaxurl,
      {
        action: "ab_delete_banner",
        nonce: AB_Admin_Ajax.nonce,
        id: $(this).data("id"),
      },
      function (response) {
        if (response.success) {
          location.reload();
        }
      }
    );
  });

  $(document).on("click", ".ab-duplicate", function (e) {
    e.preventDefault();
    $.post(
      ajaxurl,
      {
        action: "ab_duplicate_banner",
        nonce: AB_Admin_Ajax.nonce,
        id: $(this).data("id"),
      },
      function (response) {
        if (response.success) {
          location.reload();
        }
      }
    );
  });

  function updateQueryStringParameter(uri, key, value) {
    const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    const separator = uri.indexOf("?") !== -1 ? "&" : "?";
    if (uri.match(re)) {
      return uri.replace(re, "$1" + key + "=" + value + "$2");
    }
    return uri + separator + key + "=" + value;
  }

  $("#ab-banner-form").on("submit", function (e) {
    e.preventDefault();
    const $form = $(this);
    const data = $form.serialize();

    $.post(ajaxurl, data, function (res) {
      if (res && res.success) {
        $("#ab-banner-message")
          .removeClass()
          .addClass("notice notice-success")
          .html(
            "<p>" +
              (res.data && res.data.message ? res.data.message : "Salvo com sucesso!") +
              "</p>"
          )
          .fadeIn();

        $("html, body").animate({ scrollTop: 0 }, "fast");

        if (res.data && res.data.banner_id) {
          const bid = parseInt(res.data.banner_id, 10);
          $("#banner_id").val(bid);
          const newUrl = updateQueryStringParameter(window.location.href, "banner_id", bid);
          window.history.replaceState(null, "", newUrl);
        }

        setTimeout(function () {
          $("#ab-banner-message").fadeOut();
        }, 4000);
      } else {
        const errMsg = res && res.data && res.data.message ? res.data.message : "Erro ao salvar";
        $("#ab-banner-message")
          .removeClass()
          .addClass("notice notice-error")
          .html("<p>" + errMsg + "</p>")
          .fadeIn();

        $("html, body").animate({ scrollTop: 0 }, "fast");

        setTimeout(function () {
          $("#ab-banner-message").fadeOut();
        }, 4000);
      }
    }).fail(function (xhr, status, err) {
      $("#ab-banner-message")
        .removeClass()
        .addClass("notice notice-error")
        .html("<p>Erro inesperado. Verifique o console ou log do servidor.</p>")
        .fadeIn();

      $("html, body").animate({ scrollTop: 0 }, "fast");

      setTimeout(function () {
        $("#ab-banner-message").fadeOut();
      }, 4000);

      console.error("AB Save AJAX failed:", status, err, xhr.responseText);
    });
  });

  const toggle = document.getElementById("toggle-compact");
  if (toggle) {
    toggle.addEventListener("change", function () {
      $itemsTable.toggleClass("compact", this.checked);
    });
  }
});
