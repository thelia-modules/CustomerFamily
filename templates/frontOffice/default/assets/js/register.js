$('#customer_family_code_select').change(customerFamilyShowProFields);

/* Number for the customer family panel. */
$(document).ready(function (e) {
	$('#number-customer-family').text($('#register-customer-family').prevAll('fieldset').length + 1);
	customerFamilyShowProFields();
});

/* Hiding professional fields if the customer is not a professional one. */
function customerFamilyShowProFields() {
	var data_code = $('#customer_family_code_select option:selected').attr('data-code');
	$('#customer-family-extra-fields').css(
		'display',
		(data_code === 'particular' || data_code === 'none') ? 'none' : 'block'
	);
}
