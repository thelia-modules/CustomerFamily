$(document).ready(function () {
    $(document).on('click', '.save-price', function (e) {
        e.preventDefault();

        var $button = $(this);
        var $row = $button.closest('.customer-family-price-row');

        var customerFamilyId = $row.data('customer-family-id');
        var pseId = $row.data('pse-id');
        var price = $row.find('.standard-price-input').val();

        if (!customerFamilyId || !pseId) {
            return;
        }

        savePrice($button, customerFamilyId, pseId, 'price', price, $row.find('.standard-taxed-price-input'));
    });

    // Action sur le bouton save du prix promo
    $(document).on('click', '.save-promo-price', function (e) {
        e.preventDefault();

        var $button = $(this);
        var $row = $button.closest('.customer-family-price-row');

        var customerFamilyId = $row.data('customer-family-id');
        var pseId = $row.data('pse-id');
        var promoPrice = $row.find('.promo-price-input').val();

        if (!customerFamilyId || !pseId) {
            return;
        }

        savePrice($button, customerFamilyId, pseId, 'promo_price', promoPrice, $row.find('.promo-taxed-price-input'));
    });

    $('.automatic_price_field_family').typeWatch({
        captureLength: 1,
        wait         : 300,
        callback     : function () {
            var price = $(this).val();
            $(this).val(price);
            var productId = $(this).data('product-id');
            update_price($(this).val(), $(this).data('price-type'), $(this).data('rel-price'), productId);
        }
    });

    function update_price(price, price_type, dest_field_id, productId) {
        var tax_rule_id = $('#tax_rule_field').val();

        if (tax_rule_id != "") {

            var operation;

            if (price_type.indexOf('with-tax') != -1)
                operation = 'from_tax';
            else if (price_type.indexOf('without-tax') != -1)
                operation = 'to_tax';
            else
                operation = '';

            $.ajax({
                url      : '/admin/product/calculate-price',
                data     : {
                    price      : price,
                    action     : operation,
                    product_id : productId
                },
                type     : 'get',
                dataType : 'json',
                success  : function(json) {
                    var $destField = $('#' + dest_field_id);
                    if ($destField.length > 0) {
                        $destField.val(json.result);
                    } else {
                        console.error('Champ de destination non trouvé:', dest_field_id);
                    }
                },
                error : function(jqXHR, textStatus, errorThrown) {
                    alert("{intl l='Failed to get prices. Please try again.'} (" +errorThrown+ ")");
                }
            });
        }
    }

    function savePrice($button, customerFamilyId, pseId, priceType, priceValue, $taxedInput) {
        var originalHtml = $button.html();

        $button.prop('disabled', true).html('<span class="glyphicon glyphicon-refresh glyphicon-spin"></span>');

        $.ajax({
            url: '/admin/CustomerFamily/ajax/save-price',
            method: 'GET',
            dataType: 'json',
            data: {
                customer_family_id: customerFamilyId,
                pse_id: pseId,
                price_type: priceType,
                price_value: priceValue
            },
            success: function (response) {
                console.log(response);

                if (response.success) {
                    // Succès
                    $button.removeClass('btn-default').addClass('btn-success');
                    $button.html('<span class="glyphicon glyphicon-ok"></span>');

                    // Mettre à jour le prix taxé si fourni
                    if (response.taxed_price && $taxedInput.length) {
                        $taxedInput.val(response.taxed_price);
                    }

                    // Remettre le bouton normal après 2 secondes
                    setTimeout(function () {
                        $button.removeClass('btn-success').addClass('btn-default');
                        $button.html(originalHtml);
                        $button.prop('disabled', false);
                    }, 2000);

                } else {
                    // Erreur
                    $button.removeClass('btn-default').addClass('btn-danger');
                    $button.html('<span class="glyphicon glyphicon-exclamation-sign"></span>');
                    alert('Erreur: ' + (response.message || 'Impossible de sauvegarder le prix'));

                    setTimeout(function () {
                        $button.removeClass('btn-danger').addClass('btn-default');
                        $button.html(originalHtml);
                        $button.prop('disabled', false);
                    }, 3000);
                }
            },
            error: function (xhr, status, error) {
                $button.removeClass('btn-default').addClass('btn-danger');
                $button.html('<span class="glyphicon glyphicon-exclamation-sign"></span>');
                alert('Erreur de connexion: ' + error);

                setTimeout(function () {
                    $button.removeClass('btn-danger').addClass('btn-default');
                    $button.html(originalHtml);
                    $button.prop('disabled', false);
                }, 3000);
            }
        });
    }
});
