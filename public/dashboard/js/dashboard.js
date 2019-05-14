$(document).ready(function(e) {
    $.uploadPreview({
        input_field: '#image-upload', // Default: .image-upload
        preview_box: '#image-preview', // Default: .image-preview
        label_field: '#image-label', // Default: .image-label
        label_default: 'Choose File', // Default: Choose File
        label_selected: 'Change File', // Default: Change File
        no_label: false // Default: false
    });

    $.fn.confirmDelete = function(url) {
        var conf = confirm('Are you sure to delete ?');
        if (conf) {
            window.location.replace(url);
        }
    };

    $.fn.confirmDeleteOption = function(url, option) {
        var conf = confirm('Are you sure to delete ' + option + ' ?');
        if (conf) {
            window.location.replace(url);
        }
    };

    $.fn.speedPost = function(url, data, messages, formId, isPut) {
        $.ajax({
            url: url,
            type: isPut ? 'PUT' : 'POST',
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                $.Notification.notify(
                    'success',
                    'button right',
                    messages.success.header,
                    messages.success.body
                );
                if (formId) {
                    $(this).formReset(formId);
                }
            },
            error: function(data) {
                if (data.status == 422) {
                    $.Notification.notify(
                        'error',
                        'bottom right',
                        messages.error.header,
                        messages.error.body
                    );
                }
                if (data.status >= 500) {
                    $.Notification.notify(
                        'warning',
                        'button right',
                        messages.warning.header,
                        messages.warning.body
                    );
                }
            }
        });
    };

    $.fn.registerConfigForm = function(el, endpoint, label) {
        $(el).on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $(this).speedPost(
                endpoint,
                formData,
                (message = {
                    success: {
                        header: label + ' saved successfully',
                        body: label + ' saved successfully'
                    },
                    error: {
                        header: 'Internal Server Error',
                        body: 'Internal Server Error'
                    },
                    warning: {
                        header: 'Internal Server Error',
                        body: 'Internal server error'
                    }
                })
            );
        });
    };

    var imageBackground = '../../img_assets/avatar.png';

    $.fn.formReset = function(formId) {
        formId.get(0).reset();
        $('#image-preview').css(
            'background-image',
            'url(' + imageBackground + ')'
        );
    };

    $('.ladda-button').ladda('bind', { timeout: 2000 });

    // Bind progress buttons and simulate loading progress
    Ladda.bind('.progress-demo .ladda-button', {
        callback: function(instance) {
            var progress = 0;
            var interval = setInterval(function() {
                progress = Math.min(progress + Math.random() * 0.1, 1);
                instance.setProgress(progress);

                if (progress === 1) {
                    instance.stop();
                    clearInterval(interval);
                }
            }, 200);
        }
    });

    var l = $('.ladda-button-demo').ladda();

    l.click(function() {
        // Start loading
        l.ladda('start');

        // Timeout example
        // Do something in backend and then stop ladda
        setTimeout(function() {
            l.ladda('stop');
        }, 12000);
    });
});
