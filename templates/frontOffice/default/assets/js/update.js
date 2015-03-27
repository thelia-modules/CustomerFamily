/* Hiding professional fields if the customer is not a professional one. */
function customerFamilyShowProFields() {
	var data_code = $('#customer_family_code_select option:selected').attr('data-code');
	$('#customer-family-extra-fields').css(
		'display',
		(data_code === 'particular' || data_code === 'none') ? 'none' : 'block'
	);
}

$('#customer_family_code_select').change(customerFamilyShowProFields);
$(document).ready(customerFamilyShowProFields);
