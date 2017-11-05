/*
* injects a confirmation dialog on saving news records 
* to prevent unwanted overwriting of recurrences
*
* Author Falk Röder <mail@falk-roeder.de>
* package TYPO3
* subpackage dated_news
*
* */
define(['jquery',
        'TYPO3/CMS/Backend/Modal',
        'TYPO3/CMS/Backend/Severity',
        'TYPO3/CMS/Backend/Icons',
        'TYPO3/CMS/Backend/SplitButtons'
        ], function($, Modal, Severity, Icons, Buttons) {

    var ConfirmationDialog = {};

    ConfirmationDialog.init = function() {

        document.querySelectorAll('.extbase-debugger').forEach(function(el){
            el.ondblclick = function(){removeDbtrees()};
        });
        function removeDbtrees (){
            document.querySelectorAll('.extbase-debugger').forEach(function(el){
                el.remove();
            });
        }

        Buttons.removeSpinner = function(){
            var $affectedButton,
                $splitButton = $('.t3js-splitbutton');

            if ($splitButton.length > 0) {

                $splitButton.find('button').prop('disabled', false);
                $affectedButton = $splitButton.children().first();
            }

            Icons.getIcon('actions-document-save', Icons.sizes.small).done(function(markup) {
                $affectedButton.find('.t3js-icon').replaceWith(markup);
            });
        };

        TBE_EDITOR.checkAndDoSubmit = function(sendAlert) {
            var checkOk = TBE_EDITOR.checkSubmit(sendAlert);
            if (checkOk) {
                ConfirmationDialog.updateBehavior = $('[name$="[recurrence_updated_behavior]"]').val();
                if(ConfirmationDialog.updateBehavior > 1) {
                    ConfirmationDialog.confirmationModal();
                } else {
                    TBE_EDITOR.submitForm();
                }
            }
        };
    };


    ConfirmationDialog.confirmationModal = function(){
        var $modalContent;
        var $modalContentText;
        var $modal;
        switch (ConfirmationDialog.updateBehavior) {
            case '2':
                $modalContentText = TYPO3.lang['datedNews.overwrite.all'];
                break;
            case '3':
                $modalContentText = TYPO3.lang['datedNews.overwrite.noneModified'];
                break;
            case '4':
                $modalContentText = TYPO3.lang['datedNews.overwrite.allFieldsAll'];
                break;
            case '5':
                $modalContentText = TYPO3.lang['datedNews.overwrite.allFieldsNoneModified'];
                break;
            case '6':
                $modalContentText = TYPO3.lang['datedNews.overwrite.changedFieldsAll'];
                break;
            case '7':
                $modalContentText = TYPO3.lang['datedNews.overwrite.changedFieldsNoneModified'];
                break;
        }

        $modalContent = $('<div style="font-size: large;"></div>').append(
            $('<p/>').html('<b>' + $modalContentText + '</b>')
        );
        $modal = Modal.confirm(TYPO3.lang['datedNews.modalHeader'], $modalContent, Severity.warning, [
            {
                text: $(this).data('button-close-text') || TYPO3.lang['file_upload.button.cancel'] || 'Cancel',
                active: true,
                btnClass: 'btn-default',
                name: 'cancel'
            },
            {
                text: $(this).data('button-ok-text') || TYPO3.lang['file_upload.button.continue'] || 'Continue with selected actions',
                btnClass: 'btn-warning',
                name: 'continue'
            }
        ], ['modal-inner-scroll']);

        $modal.on('button.clicked', function(e) {
            if (e.target.name === 'cancel') {
                Buttons.removeSpinner();
                Modal.dismiss();
            } else if (e.target.name === 'continue') {
                TBE_EDITOR.submitForm();
                Modal.dismiss();
            }
        });
    };

    $(ConfirmationDialog.init);
    // To let the module be a dependency of another module, we return our object
    return ConfirmationDialog;
});