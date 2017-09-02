jQuery(document).ready(function($) {


  var stripe_features = {

    the_validation: function() {

          var this_obj = this;

          $('[data-numeric]').payment('restrictNumeric');
          $('.cc-number').payment('formatCardNumber');
          $('.cc-exp').payment('formatCardExpiry');
          $('.cc-cvc').payment('formatCardCVC');

          $.fn.toggleInputError = function(erred) {
            this.parent('.form-group').toggleClass('has-error', erred);
            return this;
          };
          //$('form#onSitePaymentForm').submit(function(e) {
          $('.btn_join_now').click(function(e) {
            e.preventDefault();
            var cardType = $.payment.cardType($('.cc-number').val());
            $('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));
            $('.cc-exp').toggleInputError(!$.payment.validateCardExpiry($('.cc-exp').payment('cardExpiryVal')));
            $('.cc-cvc').toggleInputError(!$.payment.validateCardCVC($('.cc-cvc').val(), cardType));
            $('.cc-brand').text(cardType);
            $('.validation').removeClass('text-danger text-success');
            $('.validation').addClass($('.has-error').length ? 'text-danger' : 'text-success');

            //var the_form_ins = $(this);
            var the_form_ins = $("form.s2_stripe_funnel_form")

            this_obj.process_for_token(e, the_form_ins, this_obj);

          });

    },

    the_response_handler: function(status, response) {

      //console.log(response);

            // Grab the form:
       var $form = $('form.s2_stripe_funnel_form');

       if (response.error) { // Problem!

         // Show the errors on the form:
         $form.find('.').text(response.error.message);
         $form.find('.submit').prop('disabled', false); // Re-enable submission

       } else { // Token was created!

         // Get the token ID:
         var token = response.id;

         // Insert the token ID into the form so it gets submitted to the server:

         if ($form.find("input[name='stripeToken']").length > 0)
          $form.find("input[name='stripeToken']").remove();

         $form.append($('<input type="hidden" name="stripeToken">').val(token));
         console.log(token);
         // Submit the form:
         //$form.get(0).submit();

         var form_data = $form.serialize();

         var data = {
             'action': 'form_submission_with_token',
             'form_data': form_data      // We pass php values differently!
           };
           // We can also pass the url value separately from ajaxurl for front end AJAX implementations
          //  jQuery.post(plugin_s2stripe.ajax_url, data, function(response) {
          //    console.log(response);
          //  });

          $.ajax({
           type: "POST",
           url: plugin_s2stripe.ajax_url,
           data: data,
           dataType: "json",
           success: function(data) {
               //var obj = jQuery.parseJSON(data); if the dataType is not specified as json uncomment this
               // do what ever you want with the server response
              // data = $.parseJSON(data);
              //console.log(data);

               if (data.stat == 0) {
                alert(data.msg);
                return;
               }

               if (data.stat != 1)
                return;

                window.location.href = data.link_to_option_page;

               //$(".the_form_stripes2").html(data);
               //$form.find('.submit').prop('disabled', false); // Re-enable submission

           }

         });

       }

    },

    process_for_token: function (e = "", the_form_ins = "", this_obj = "") {


      var cc_exp = $('.cc-exp').val();

      cc_exp = cc_exp.split("/");

      var err = 0;

      try {
        cc_exp_month = ( ( (typeof cc_exp[0] != "undefined") || (cc_exp[0].length > 0) ) ? cc_exp[0] : "" );
      } catch (e) {
        cc_exp_month = "";
        err = 1;
      }

      try {
        cc_exp_year = ( ( (typeof cc_exp[1] != "undefined") || (cc_exp[1].length > 0) ) ? cc_exp[1] : "" );
      } catch (e) {
        cc_exp_year = "";
        err = 1;

      }

      cc_exp_month = cc_exp_month.replace(/ /g,'');

      cc_exp_year = cc_exp_year.replace(/ /g,'');

      //console.log(cc_exp_month);
      //console.log(cc_exp_year);

      if (err === 1)
        return;

      //the_form_ins.find('.submit').prop('disabled', true);
      try {
        Stripe.card.createToken({
        number: $('.cc-number').val(),
        cvc: $('.cc-cvc').val(),
        exp_month: cc_exp_month,
        exp_year: cc_exp_year
      }, this_obj.the_response_handler);

      } catch (e) {
        console.log("err");
        console.log(e);

      }


    },

    stripe_funnel_subscription: function() {

      $(document).on("click", ".stripe_funnel_subscription", function(event) {
        event.preventDefault();

      })

    },

    form_features: function() {
      if ($(".single_group_div").find("input[type='text']").length <= 0)
        return;

        $(".single_group_div").find("input[type='text']")
        .focusin(function() {

          $(this).parent().next().find("input[type='text']").css("border-top", "0 solid #d0d0d0");

        })
        .focusout(function() {

          $(this).parent().next().find("input[type='text']").css("border-top", "1px solid #d0d0d0");

        });

        $(".single_group_div").find("select")
        .focusin(function() {

          $(this).parent().next().find("select").css("border-top", "0 solid #d0d0d0");

        })
        .focusout(function() {

          $(this).parent().next().find("select").css("border-top", "1px solid #d0d0d0");

        });

        $(".single_group_div").find("input[type='tel']")
        .focusin(function() {

          $(this).parent().next().find("input[type='tel']").css("border-top", "0 solid #d0d0d0");

        })
        .focusout(function() {

          $(this).parent().next().find("input[type='tel']").css("border-top", "1px solid #d0d0d0");

        });

        $(".single_group_div").find("input[type='password']")
        .focusin(function() {

          $(this).parent().next().find("input[type='password']").css("border-top", "0 solid #d0d0d0");

        })
        .focusout(function() {

          $(this).parent().next().find("input[type='password']").css("border-top", "1px solid #d0d0d0");

        });

    }

  }

  stripe_features.the_validation();
  //stripe_features.stripe_funnel_subscription();
  stripe_features.form_features();

})
