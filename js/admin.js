jQuery(document).ready(function($) {

  var loadJS = {

    init : function() {
      this.test_pdf();
      this.pdf_bulk_download();

    //  this.download_btn();
    },

    test_pdf: function() {

      if ($("#tdl_test_pdf").length <= 0)
        return;

      $(document).on("click", "#tdl_test_pdf", function(event) {

        event.preventDefault();

        if (tdl_admin_data.post_id.length <= 0)
          return;

        var create_pdf_link = tdl_admin_data.create_pdf_link;

          var vars_data = {
            text_1 : $("#tinypea_dynamic_labels_tdl_test_txt_1").val(),
            text_2 : $("#tinypea_dynamic_labels_tdl_test_txt_2").val(),
            order_id : $("#tinypea_dynamic_labels_tdl_test_order_id").val(),
            postID : tdl_admin_data.post_id,
            view : 1
          }
          var query_param = $.param( vars_data );

          create_pdf_link += "/?"+query_param;

          var win = window.open(create_pdf_link, '_blank');

          if (win)
              win.focus();
          else
              alert('Please allow popups for this website');

      })

    },

    download_btn: function() {

      //$(".tdl_download_pdf").

      $(document).on("click", ".tdl_download_pdf", function(event) {
        event.preventDefault();

      })

    },

    pdf_bulk_download: function() {

      $(document).on("click", ".bulkactions #doaction", function(event) {

        event.preventDefault();
        
        if ($("#bulk-action-selector-top").find(":selected").text() != "Download PDF")
          $("#posts-filter").submit();

          $(".generating_pdf_notice").remove();

          var loading_html = '<div class="notice generating_pdf_notice notice-warning is-dismissible">';
          loading_html += '<p class="loading_notice_pdf"><img width="15" src="'+tdl_admin_data.tinypea_dynamic_labels_PLUGIN_URL+'ajax_loader_red_512.gif"> <strong>Generating PDFs!</strong></p>';
	        loading_html += '<button type="button" class="notice-dismiss">';
		      loading_html += '<span class="screen-reader-text">Dismiss this notice.</span></button></div>';

          $(".wp-header-end").after(loading_html);

          var postVals = [];
          $("#posts-filter").find("input[name='post[]']:checked").each(function() {
            postVals.push($(this).val());
          });

          if (postVals.length <= 0) return;

          var data = {
            'action': 'doBulkPDF',
            'values' : postVals
          };

          jQuery.post(tdl_admin_data.ajax_url, data, function(response) {
            response = $.parseJSON(response);

            if (response.length <= 0) return;

            $(".loading_notice_pdf").remove();

            var loading_txt = "<p class='loading_notice_pdf'>";
            console.log(response);
            $.each(response, function(ind, data_pdf) {
              loading_txt += "ID: "+data_pdf.id+" ";
              loading_txt += data_pdf.link;
              loading_txt += "<br>";
            });

            loading_txt += "<a href='"+tdl_admin_data.tinypea_dynamic_labels_PLUGIN_URL+"pdf/zip_ajax.php'>Download ZIP</a>";
            loading_txt += "</p>";

            $(".generating_pdf_notice").append(loading_txt);


          console.log(loading_txt);

          });

        //var formData = $("#posts-filter").find("input[name='post[]']").val();

      })




    }


  };


loadJS.init();



});
