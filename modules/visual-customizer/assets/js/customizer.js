/**
 * Visual Customizer JavaScript
 *
 * Handles real-time preview updates, mode switching, and AJAX operations
 *
 * @package JBLund_Dealers
 * @subpackage Visual_Customizer
 * @since 1.2.0
 */

(function ($) {
	"use strict";

	/**
	 * Visual Customizer Object
	 */
	const VisualCustomizer = {
		/**
		 * Initialize
		 */
		init: function () {
			this.initColorPickers();
			this.initModeToggle();
			this.initDeviceToggle();
			this.initControlListeners();
			this.initActionButtons();
			this.initCSSEditor();
			this.initLayoutPreview(); // Initialize layout on page load
		},

		/**
		 * Initialize color pickers
		 */
		initColorPickers: function () {
			$(".color-picker").wpColorPicker({
				change: function (event, ui) {
					const $input = $(event.target);
					const name = $input.attr("name");
					const color = ui.color.toString();

					// Update settings
					VisualCustomizer.updateSetting(name, color);

					// Update preview
					VisualCustomizer.updatePreview();
				},
				clear: function (event) {
					const $input = $(event.target);
					const name = $input.attr("name");

					// Update settings
					VisualCustomizer.updateSetting(name, "");

					// Update preview
					VisualCustomizer.updatePreview();
				},
			});
		},

		/**
		 * Initialize mode toggle
		 */
		initModeToggle: function () {
			$(".mode-btn").on("click", function () {
				const mode = $(this).data("mode");

				// Update button states
				$(".mode-btn").removeClass("active");
				$(this).addClass("active");

				// Show/hide mode content
				$(".mode-content").hide();
				$('.mode-content[data-mode="' + mode + '"]').show();
			});
		},

		/**
		 * Initialize device toggle for responsive preview
		 */
		initDeviceToggle: function () {
			$(".device-btn").on("click", function () {
				const device = $(this).data("device");

				// Update button states
				$(".device-btn").removeClass("active");
				$(this).addClass("active");

				// Update preview frame
				const $frame = $(".preview-frame");
				$frame.removeClass("desktop tablet mobile").addClass(device);
			});
		},

		/**
		 * Initialize control listeners
		 */
		initControlListeners: function () {
			// Range sliders
			$(".range-slider").on("input", function () {
				const $slider = $(this);
				const name = $slider.attr("name");
				const value = $slider.val();
				const unit = $slider.data("unit") || "";

				// Update display value
				$slider
					.closest(".control-range")
					.find(".range-value")
					.text(value + unit);

				// Update settings
				VisualCustomizer.updateSetting(name, value);

				// Update preview
				VisualCustomizer.updatePreview();
			});

			// Select controls
			$(".select-control").on("change", function () {
				const $select = $(this);
				const name = $select.attr("name");
				const value = $select.val();

				// Update settings
				VisualCustomizer.updateSetting(name, value);

				// Special handling for layout preview
				if (name === "preview_layout") {
					VisualCustomizer.updateLayoutPreview(value);
				}

				// Update preview
				VisualCustomizer.updatePreview();
			});
		},

		/**
		 * Initialize action buttons
		 */
		initActionButtons: function () {
			// Save button
			$("#save-settings").on("click", function () {
				VisualCustomizer.saveSettings();
			});

			// Reset button
			$("#reset-settings").on("click", function () {
				if (confirm(jblundCustomizer.strings.resetConfirm)) {
					VisualCustomizer.resetSettings();
				}
			});

			// Copy CSS button
			$("#copy-css").on("click", function () {
				VisualCustomizer.copyCSSToClipboard();
			});

			// Copy HTML button
			$("#copy-html").on("click", function () {
				VisualCustomizer.copyHTMLToClipboard();
			});
		},

		/**
		 * Initialize CSS editor
		 */
		initCSSEditor: function () {
			$("#custom-css-editor").on("input", function () {
				const css = $(this).val();

				// Update settings
				VisualCustomizer.updateSetting("custom_css", css);

				// Update preview
				VisualCustomizer.updatePreview();
			});
		},

		/**
		 * Update a single setting
		 */
		updateSetting: function (name, value) {
			const settings = this.getSettings();
			settings[name] = value;
			this.saveSettingsToInput(settings);
		},

		/**
		 * Get current settings from hidden input
		 */
		getSettings: function () {
			const json = $("#customizer-settings").val();
			try {
				return JSON.parse(json);
			} catch (e) {
				console.error("Error parsing settings:", e);
				return {};
			}
		},

		/**
		 * Save settings to hidden input
		 */
		saveSettingsToInput: function (settings) {
			$("#customizer-settings").val(JSON.stringify(settings));
		},

		/**
		 * Update preview with current settings
		 */
		updatePreview: function () {
			const settings = this.getSettings();
			const css = this.generateCSS(settings);

			// Update preview CSS
			$("#preview-custom-css").text(css);
		},

		/**
		 * Update layout preview
		 */
		updateLayoutPreview: function (layout) {
			const $grid = $(".dealers-grid");

			// Remove existing layout classes
			$grid.removeClass("layout-grid layout-list layout-compact");

			// Add new layout class
			if (layout && layout !== "grid") {
				$grid.addClass("layout-" + layout);
			} else {
				$grid.addClass("layout-grid");
			}
		},

		/**
		 * Initialize layout preview on page load
		 */
		initLayoutPreview: function () {
			const settings = this.getSettings();
			const layout = settings.preview_layout || "grid";
			this.updateLayoutPreview(layout);
		},

		/**
		 * Generate CSS from settings
		 */
		generateCSS: function (settings) {
			let css = "";

			// Colors
			if (settings.header_color) {
				css += `.dealer-card-header { background: ${settings.header_color} !important; }\n`;
			}
			if (settings.card_background) {
				css += `.dealer-card { background: ${settings.card_background} !important; }\n`;
			}
			if (settings.button_color) {
				css += `.dealer-website-button { background: ${settings.button_color} !important; }\n`;
			}
			if (settings.text_color) {
				css += `.dealer-card h3 { color: ${settings.text_color} !important; }\n`;
			}
			if (settings.secondary_text_color) {
				css += `.dealer-card-address, .dealer-card-phone { color: ${settings.secondary_text_color} !important; }\n`;
			}
			if (settings.border_color) {
				css += `.dealer-card { border-color: ${settings.border_color} !important; }\n`;
			}
			if (settings.button_text_color) {
				css += `.dealer-website-button { color: ${settings.button_text_color} !important; }\n`;
			}
			if (settings.icon_color) {
				css += `.dealer-services-icons { color: ${settings.icon_color} !important; }\n`;
			}
			if (settings.link_color) {
				css += `.dealer-card a { color: ${settings.link_color} !important; }\n`;
			}
			if (settings.hover_background) {
				css += `.dealer-card:hover { background: ${settings.hover_background} !important; }\n`;
			}

			// Typography
			if (settings.heading_font_size) {
				css += `.dealer-card h3 { font-size: ${settings.heading_font_size}px !important; }\n`;
			}
			if (settings.body_font_size) {
				css += `.dealer-card-address, .dealer-card-phone { font-size: ${settings.body_font_size}px !important; }\n`;
			}
			if (settings.heading_font_weight) {
				css += `.dealer-card h3 { font-weight: ${settings.heading_font_weight} !important; }\n`;
			}
			if (settings.line_height) {
				css += `.dealer-card p, .dealer-card span { line-height: ${settings.line_height} !important; }\n`;
			}

			// Spacing
			if (settings.card_padding !== undefined) {
				css += `.dealer-card { padding: ${settings.card_padding}px !important; }\n`;
			}
			if (settings.card_margin !== undefined) {
				css += `.dealer-card { margin: ${settings.card_margin}px !important; }\n`;
			}
			if (settings.grid_gap) {
				css += `.dealers-grid { gap: ${settings.grid_gap}px !important; }\n`;
			}
			if (settings.border_radius !== undefined) {
				css += `.dealer-card { border-radius: ${settings.border_radius}px !important; }\n`;
			}
			if (settings.border_width !== undefined) {
				css += `.dealer-card { border-width: ${settings.border_width}px !important; }\n`;
			}
			if (settings.border_style) {
				css += `.dealer-card { border-style: ${settings.border_style} !important; }\n`;
			}

			// Effects
			if (settings.box_shadow) {
				const shadows = {
					none: "none",
					light: "0 2px 4px rgba(0,0,0,0.1)",
					medium: "0 4px 8px rgba(0,0,0,0.15)",
					heavy: "0 8px 16px rgba(0,0,0,0.2)",
				};
				if (shadows[settings.box_shadow]) {
					css += `.dealer-card { box-shadow: ${
						shadows[settings.box_shadow]
					} !important; }\n`;
				}
			}

			if (settings.hover_effect) {
				switch (settings.hover_effect) {
					case "lift":
						css += `.dealer-card:hover { transform: translateY(-5px) !important; }\n`;
						break;
					case "scale":
						css += `.dealer-card:hover { transform: scale(1.02) !important; }\n`;
						break;
					case "shadow":
						css += `.dealer-card:hover { box-shadow: 0 12px 24px rgba(0,0,0,0.25) !important; }\n`;
						break;
				}
			}

			if (settings.transition_speed) {
				css += `.dealer-card { transition: all ${settings.transition_speed}s ease !important; }\n`;
			}

			if (settings.icon_size) {
				css += `.dealer-services-icons { font-size: ${settings.icon_size}px !important; }\n`;
			}

			// Custom CSS
			if (settings.custom_css) {
				css += "\n/* Custom CSS */\n" + settings.custom_css + "\n";
			}

			return css;
		},

		/**
		 * Save settings via AJAX
		 */
		saveSettings: function () {
			const $button = $("#save-settings");
			const settings = this.getSettings();

			// Disable button and show loading
			$button
				.prop("disabled", true)
				.html(
					'<span class="dashicons dashicons-update spin"></span> Saving...'
				);

			$.ajax({
				url: jblundCustomizer.ajaxUrl,
				type: "POST",
				data: {
					action: "save_customizer_settings",
					nonce: jblundCustomizer.nonce,
					settings: settings,
				},
				success: function (response) {
					if (response.success) {
						// Show success message
						VisualCustomizer.showNotice("success", response.data.message);
					} else {
						VisualCustomizer.showNotice(
							"error",
							response.data.message || jblundCustomizer.strings.error
						);
					}
				},
				error: function () {
					VisualCustomizer.showNotice("error", jblundCustomizer.strings.error);
				},
				complete: function () {
					// Re-enable button
					$button
						.prop("disabled", false)
						.html('<span class="dashicons dashicons-yes"></span> Save Changes');
				},
			});
		},

		/**
		 * Reset settings via AJAX
		 */
		resetSettings: function () {
			const $button = $("#reset-settings");

			// Disable button and show loading
			$button
				.prop("disabled", true)
				.html(
					'<span class="dashicons dashicons-update spin"></span> Resetting...'
				);

			$.ajax({
				url: jblundCustomizer.ajaxUrl,
				type: "POST",
				data: {
					action: "reset_customizer_settings",
					nonce: jblundCustomizer.nonce,
				},
				success: function (response) {
					if (response.success) {
						// Update settings
						VisualCustomizer.saveSettingsToInput(response.data.settings);

						// Reload page to show defaults
						location.reload();
					} else {
						VisualCustomizer.showNotice(
							"error",
							response.data.message || jblundCustomizer.strings.error
						);
					}
				},
				error: function () {
					VisualCustomizer.showNotice("error", jblundCustomizer.strings.error);
				},
				complete: function () {
					// Re-enable button
					$button
						.prop("disabled", false)
						.html(
							'<span class="dashicons dashicons-image-rotate"></span> Reset'
						);
				},
			});
		},

		/**
		 * Copy CSS to clipboard
		 */
		copyCSSToClipboard: function () {
			const css = $("#custom-css-editor").val();

			// Create temporary textarea
			const $temp = $("<textarea>");
			$("body").append($temp);
			$temp.val(css).select();
			document.execCommand("copy");
			$temp.remove();

			// Show success message
			this.showNotice("success", "CSS copied to clipboard!");
		},

		/**
		 * Copy HTML structure to clipboard
		 */
		copyHTMLToClipboard: function () {
			const html = $(".html-code-display code").text();

			// Create temporary textarea
			const $temp = $("<textarea>");
			$("body").append($temp);
			$temp.val(html).select();
			document.execCommand("copy");
			$temp.remove();

			// Show success message
			this.showNotice("success", "HTML structure copied to clipboard!");
		},

		/**
		 * Show admin notice
		 */
		showNotice: function (type, message) {
			const $notice = $("<div>")
				.addClass("notice notice-" + type + " is-dismissible")
				.html("<p>" + message + "</p>")
				.hide();

			$(".jblund-visual-customizer h1").after($notice);
			$notice.slideDown();

			// Auto-dismiss after 3 seconds
			setTimeout(function () {
				$notice.slideUp(function () {
					$(this).remove();
				});
			}, 3000);
		},
	};

	// Initialize on document ready
	$(document).ready(function () {
		VisualCustomizer.init();
	});
})(jQuery);
