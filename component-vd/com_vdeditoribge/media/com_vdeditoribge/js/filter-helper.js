/**
 * @package     IBGE\Component\VdEditorIbge\Administrator
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
document.addEventListener('DOMContentLoaded', function() {
    const strings = Joomla.getOptions('com_vdeditoribge.strings');

    function setupFilter(textareaId, filterInputId, placeholderText) {
        const textarea = document.getElementById(textareaId);
        if (!textarea) {
            // console.warn('Textarea not found for filter: ' + textareaId);
            return;
        }
        let originalJsonString = textarea.value; // Store the initial JSON string

        let filterInput = document.getElementById(filterInputId);
        if (!filterInput) {
            filterInput = document.createElement('input');
            filterInput.setAttribute('type', 'text');
            filterInput.setAttribute('id', filterInputId);
            filterInput.setAttribute('placeholder', placeholderText);
            filterInput.classList.add('form-control', 'mb-2'); // Bootstrap classes
            // filterInput.setAttribute('style', 'width: 90%; margin-bottom: 5px; display: block;');
            textarea.parentNode.insertBefore(filterInput, textarea);
        }

        filterInput.addEventListener('keyup', function() {
            const searchText = this.value.trim().toLowerCase();
            try {
                // Always parse the original JSON string to avoid corruption from successive filters
                const originalArray = JSON.parse(originalJsonString);
                if (Array.isArray(originalArray)) {
                    if (searchText === '') {
                        textarea.value = JSON.stringify(originalArray, null, 2); // Restore with formatting
                        return;
                    }
                    const filteredArray = originalArray.filter(function(id) {
                        return String(id).toLowerCase().includes(searchText);
                    });
                    textarea.value = JSON.stringify(filteredArray, null, 2);
                }
            } catch (e) {
                // If the original (or current) JSON is invalid, do nothing or show error
                // console.error('Error parsing JSON for filter: ', e);
            }
        });

        // Update originalJsonString if the textarea is manually modified and is valid JSON
        textarea.addEventListener('change', function() { // 'change' or 'input' might be better
            try {
                JSON.parse(textarea.value); // Check if valid JSON
                originalJsonString = textarea.value; // Update the base for filtering
            } catch (e) {
                // JSON manually typed is invalid, don't update originalJsonString
            }
        });
    }

    if (strings) {
        setupFilter('jform_menufilter_json', 'menufilter_live_filter', strings.COM_VDEDITORIBGE_FILTER_MENU_IDS_PLACEHOLDER);
        setupFilter('jform_categoryfilter_json', 'categoryfilter_live_filter', strings.COM_VDEDITORIBGE_FILTER_CATEGORY_IDS_PLACEHOLDER);
    } else {
        console.error('VDEDITORIBGE: Joomla.getOptions(\'com_vdeditoribge.strings\') not found.');
    }

    // Override Joomla.submitbutton for pre-submission JSON validation
    // Ensure this part of the script runs after Joomla's core scripts have defined Joomla.submitbutton
    if (typeof Joomla !== 'undefined' && typeof Joomla.submitbutton === 'function') {
        const originalSubmitButton = Joomla.submitbutton;
        Joomla.submitbutton = function(task) {
            if (task === 'virtualdomain.cancel' || task.endsWith('.cancel')) { // More generic cancel
                originalSubmitButton(task, document.getElementById('item-form'));
                return;
            }

            if (document.formvalidator && !document.formvalidator.isValid(document.getElementById('item-form'))) {
                // Joomla's formvalidator usually shows its own messages.
                // If not, or for an additional alert:
                // if (strings && strings.JGLOBAL_VALIDATION_FORM_FAILED) {
                //     alert(strings.JGLOBAL_VALIDATION_FORM_FAILED);
                // } else {
                //     alert('Form validation failed.');
                // }
                return false;
            }

            let menufilterJson = document.getElementById('jform_menufilter_json').value;
            let categoryfilterJson = document.getElementById('jform_categoryfilter_json').value;
            let isValid = true;

            try {
                JSON.parse(menufilterJson);
            } catch (e) {
                alert((strings?.COM_VDEDITORIBGE_ERROR_MENUFILTER_INVALID_JSON || 'Menu Filter JSON is invalid') + ': ' + e.message);
                isValid = false;
            }
            try {
                JSON.parse(categoryfilterJson);
            } catch (e) {
                alert((strings?.COM_VDEDITORIBGE_ERROR_CATEGORYFILTER_INVALID_JSON || 'Category Filter JSON is invalid') + ': ' + e.message);
                isValid = false;
            }

            if (isValid) {
                originalSubmitButton(task, document.getElementById('item-form'));
            } else {
                // Optionally, indicate validation failure more clearly if needed
                // For example, by focusing on the first invalid field or adding error classes.
                return false;
            }
        };
    } else {
         console.warn('VDEDITORIBGE: Joomla.submitbutton not found, JSON pre-validation on submit may not work.');
    }
});
