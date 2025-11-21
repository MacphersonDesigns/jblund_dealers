/**
 * Dealer Profile JavaScript
 *
 * Handles dynamic sub-location management and AJAX document uploads
 * for the dealer profile editing interface.
 *
 * @package JBLund_Dealers
 * @since 2.0.0
 */

(function($) {
	'use strict';

	// Sub-location index counter
	let sublocationIndex = $('.sublocation-row').length || 0;

	/**
	 * Initialize sub-location management
	 */
	function initSublocations() {
		// Add new sub-location
		$('#add-sublocation-btn').on('click', function(e) {
			e.preventDefault();
			
			const template = $('#sublocation-template').html();
			const newRow = template
				.replace(/{{INDEX}}/g, sublocationIndex)
				.replace(/{{NUMBER}}/g, sublocationIndex + 1);
			
			$('#sublocations-container').append(newRow);
			sublocationIndex++;
		});

		// Remove sub-location
		$(document).on('click', '.remove-sublocation-btn', function(e) {
			e.preventDefault();
			
			if (confirm('Are you sure you want to remove this location?')) {
				$(this).closest('.sublocation-row').fadeOut(300, function() {
					$(this).remove();
					updateSublocationNumbers();
				});
			}
		});
	}

	/**
	 * Update sub-location numbers after deletion
	 */
	function updateSublocationNumbers() {
		$('.sublocation-row').each(function(index) {
			$(this).find('h3').text('Location ' + (index + 1));
		});
	}

	/**
	 * Initialize document upload
	 */
	function initDocumentUpload() {
		const $uploadInput = $('#document-upload-input');
		const $dropzone = $('.upload-dropzone');

		// File input change
		$uploadInput.on('change', function(e) {
			handleFiles(e.target.files);
		});

		// Drag and drop
		$dropzone.on('dragover', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).addClass('dragover');
		});

		$dropzone.on('dragleave', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).removeClass('dragover');
		});

		$dropzone.on('drop', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).removeClass('dragover');
			
			const files = e.originalEvent.dataTransfer.files;
			handleFiles(files);
		});

		// Document deletion
		$(document).on('click', '.document-delete-btn', function(e) {
			e.preventDefault();
			
			const $btn = $(this);
			const docId = $btn.data('doc-id');
			const $docItem = $btn.closest('.document-item');

			if (!confirm('Are you sure you want to delete this document?')) {
				return;
			}

			$btn.prop('disabled', true).text('Deleting...');

			deleteDocument(docId, function(success) {
				if (success) {
					$docItem.fadeOut(300, function() {
						$(this).remove();
						
						// Hide document list if empty
						if ($('.document-item').length === 0) {
							$('.document-list').fadeOut();
						}
					});
				} else {
					alert('Error deleting document. Please try again.');
					$btn.prop('disabled', false).text('Delete');
				}
			});
		});
	}

	/**
	 * Handle file upload
	 */
	function handleFiles(files) {
		if (files.length === 0) return;

		// Validate files
		const validFiles = [];
		const maxSize = 10 * 1024 * 1024; // 10MB
		const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];

		for (let i = 0; i < files.length; i++) {
			const file = files[i];

			if (file.size > maxSize) {
				alert('File "' + file.name + '" is too large. Maximum size is 10MB.');
				continue;
			}

			if (!allowedTypes.includes(file.type)) {
				alert('File "' + file.name + '" is not a valid file type. Allowed: PDF, DOC, DOCX, JPG, PNG');
				continue;
			}

			validFiles.push(file);
		}

		if (validFiles.length === 0) return;

		// Upload files
		uploadFiles(validFiles);
	}

	/**
	 * Upload files via AJAX
	 */
	function uploadFiles(files) {
		const formData = new FormData();
		
		for (let i = 0; i < files.length; i++) {
			formData.append('files[]', files[i]);
		}
		
		formData.append('action', 'jblund_upload_dealer_document');
		formData.append('nonce', jblundDealerProfile.uploadNonce);

		// Show upload progress
		showUploadProgress();

		$.ajax({
			url: jblundDealerProfile.ajaxUrl,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				hideUploadProgress();

				if (response.success) {
					// Add uploaded documents to list
					response.data.documents.forEach(function(doc) {
						addDocumentToList(doc);
					});

					// Show success message
					showMessage('Documents uploaded successfully!', 'success');
				} else {
					showMessage(response.data.message || 'Upload failed. Please try again.', 'error');
				}
			},
			error: function() {
				hideUploadProgress();
				showMessage('Upload failed. Please try again.', 'error');
			}
		});
	}

	/**
	 * Delete document via AJAX
	 */
	function deleteDocument(docId, callback) {
		$.ajax({
			url: jblundDealerProfile.ajaxUrl,
			type: 'POST',
			data: {
				action: 'jblund_delete_dealer_document',
				nonce: jblundDealerProfile.deleteNonce,
				document_id: docId
			},
			success: function(response) {
				callback(response.success);
			},
			error: function() {
				callback(false);
			}
		});
	}

	/**
	 * Add document to list
	 */
	function addDocumentToList(doc) {
		let $list = $('.document-list');
		
		// Create list if it doesn't exist
		if ($list.length === 0) {
			$list = $('<div class="document-list"><h3>Uploaded Documents</h3></div>');
			$('#document-upload-area').append($list);
		}

		const $docItem = $(`
			<div class="document-item" data-doc-id="${doc.id}">
				<div class="document-info">
					<span class="document-icon">📄</span>
					<div class="document-details">
						<strong>${escapeHtml(doc.title)}</strong>
						<span class="document-meta">${doc.size} • ${doc.date}</span>
					</div>
				</div>
				<div class="document-actions">
					<a href="${doc.url}" target="_blank" class="document-view-btn">View</a>
					<button type="button" class="document-delete-btn" data-doc-id="${doc.id}">Delete</button>
				</div>
			</div>
		`);

		$list.append($docItem);
		$list.show();
	}

	/**
	 * Show upload progress indicator
	 */
	function showUploadProgress() {
		const $progress = $('<div class="upload-progress">Uploading files... <span class="spinner"></span></div>');
		$('#document-upload-area').prepend($progress);
	}

	/**
	 * Hide upload progress indicator
	 */
	function hideUploadProgress() {
		$('.upload-progress').remove();
	}

	/**
	 * Show message to user
	 */
	function showMessage(message, type) {
		const $message = $(`<div class="profile-message ${type}">${escapeHtml(message)}</div>`);
		$('.profile-header').after($message);

		// Auto-hide after 5 seconds
		setTimeout(function() {
			$message.fadeOut(300, function() {
				$(this).remove();
			});
		}, 5000);

		// Scroll to message
		$('html, body').animate({
			scrollTop: $message.offset().top - 100
		}, 300);
	}

	/**
	 * Escape HTML to prevent XSS
	 */
	function escapeHtml(text) {
		const map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
		};
		return text.replace(/[&<>"']/g, function(m) { return map[m]; });
	}

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		// Only run on dealer profile page
		if ($('.jblund-dealer-profile').length === 0) {
			return;
		}

		initSublocations();
		initDocumentUpload();
	});

})(jQuery);
