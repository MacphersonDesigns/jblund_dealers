/**
 * Signature Pad Integration for NDA Acceptance
 * 
 * Uses Signature Pad library for digital signature capture
 * @link https://github.com/szimek/signature_pad
 */

(function($) {
    'use strict';

    // Initialize signature pad when DOM is ready
    $(document).ready(function() {
        initSignaturePad();
    });

    function initSignaturePad() {
        const canvas = document.getElementById('signature-canvas');
        
        if (!canvas) {
            return; // Not on NDA page
        }

        // Check if SignaturePad is loaded
        if (typeof SignaturePad === 'undefined') {
            console.error('SignaturePad library not loaded');
            return;
        }

        // Initialize signature pad
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1,
            maxWidth: 2.5,
            throttle: 16,
            minDistance: 5
        });

        // Responsive canvas sizing
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const wrapper = canvas.parentElement;
            
            canvas.width = wrapper.offsetWidth * ratio;
            canvas.height = 200 * ratio;
            canvas.style.width = wrapper.offsetWidth + 'px';
            canvas.style.height = '200px';
            
            canvas.getContext('2d').scale(ratio, ratio);
            signaturePad.clear(); // Clear on resize to prevent distortion
        }

        // Initial size
        resizeCanvas();

        // Resize on window resize (debounced)
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(resizeCanvas, 250);
        });

        // Clear button
        const clearButton = document.getElementById('clear-signature');
        if (clearButton) {
            clearButton.disabled = false;
            clearButton.addEventListener('click', function(e) {
                e.preventDefault();
                signaturePad.clear();
                updateSubmitButton();
            });
        }

        // Track signature drawing for submit button state
        signaturePad.addEventListener('beginStroke', function() {
            updateSubmitButton();
        });

        signaturePad.addEventListener('endStroke', function() {
            updateSubmitButton();
        });

        // Form submission
        const form = document.getElementById('nda-acceptance-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const signatureData = document.getElementById('signature_data');
                const checkbox = document.getElementById('jblund_accept_nda');

                // Validate checkbox
                if (!checkbox.checked) {
                    e.preventDefault();
                    alert('Please check the box to confirm you have read and agree to the terms.');
                    checkbox.focus();
                    return false;
                }

                // Validate signature
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    alert('Please provide your signature before submitting.');
                    return false;
                }

                // Save signature data as base64 PNG
                signatureData.value = signaturePad.toDataURL('image/png');
            });
        }

        // Update submit button state
        function updateSubmitButton() {
            const submitButton = document.getElementById('submit-nda');
            const checkbox = document.getElementById('jblund_accept_nda');
            
            if (submitButton && checkbox) {
                const hasSignature = !signaturePad.isEmpty();
                const isChecked = checkbox.checked;
                
                if (hasSignature && isChecked) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('disabled');
                } else {
                    submitButton.disabled = true;
                    submitButton.classList.add('disabled');
                }
            }
        }

        // Listen to checkbox changes
        const checkbox = document.getElementById('jblund_accept_nda');
        if (checkbox) {
            checkbox.addEventListener('change', updateSubmitButton);
        }

        // Initial button state
        updateSubmitButton();
    }

})(jQuery);
