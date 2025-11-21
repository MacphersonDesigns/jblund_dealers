jQuery(document).ready(function ($) {
	// Toggle between basic and advanced editor
	$("#advanced_editor_toggle").on("change", function () {
		const isAdvanced = $(this).is(":checked");

		if (isAdvanced) {
			$("#basic_editor").hide();
			$("#advanced_editor").show();
			$("#editor_mode").val("advanced");
		} else {
			$("#basic_editor").show();
			$("#advanced_editor").hide();
			$("#editor_mode").val("basic");
		}
	});

	// Start with basic editor visible
	$("#basic_editor").show();
	$("#advanced_editor").hide();
});
