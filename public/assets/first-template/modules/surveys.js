$(function() {

    $('input[name="send_survey_to[leads]"]').on('change', function() {
        $('.leads-statuses').slideToggle();
    });

    $('input[name="send_survey_to[clients]"]').on('change', function() {
        $('.customer-groups').slideToggle();
    });

    $('.survey-customer-groups input').on('change', function() {
        if ($('.survey-customer-groups input:checked').length > 0) {
            $('#ml_customers_all').prop('checked', false);
        }
    });

    $('.survey-lead-status input').on('change', function() {
        if ($('.survey-lead-status input:checked').length > 0) {
            $('#ml_leads_all').prop('checked', false);
        }
    });

    $('#ml_customers_all').on('change', function() {
        if ($(this).prop('checked') !== false) {
            $('.survey-customer-groups input').prop('checked', false);
        }
    });

    $('#ml_leads_all').on('change', function() {
        if ($(this).prop('checked') !== false) {
            $('.survey-lead-status input').prop('checked', false);
        }
    });


    // Init questions sortable

    // Add merge field
    $('.add_email_list_custom_field_to_survey').on('click', function(e) {
        e.preventDefault();
        tinymce.get('description').execCommand('mceInsertContent', false, $(this).data('slug'));
    });

});

function survey_toggle_full_view() {
    $('#survey-add-edit-wrapper').toggleClass('hide');
    $('#survey_questions_wrapper').toggleClass('col-md-12');
    $('#survey_questions_wrapper').toggleClass('col-md-7');
}
// New survey question
    
function add_survey_question(type, surveyid) {
    $.ajax({
        type: "POST",
        url: admin_url + 'surveys/add_question',
        data: { surveyid: surveyid, type: type, _token: csrf_token },
        success: function (response) {
            response = JSON.parse(response);
            question_area = '<li>';
            question_area += '<div class="form-group question">';
            question_area += '<hr />';
            question_area += '<div class="custom-control custom-checkbox mb-2 required">';
            question_area += '<input id="req_'+response.data.questionid+'" type="checkbox" class="custom-control-input" data-question_required="' + response.data.questionid + '" name="required[]" onchange="update_question(this,\'' + type + '\',' + response.data.questionid + ')">';
            question_area += '<label for="req_'+response.data.questionid+'" class="custom-control-label">' + response.data.survey_question_required + '</label>';
            question_area += '</div>';
            question_area += '<div class="row">';
            question_area += '<div class="col-lg-6">';
            question_area += '<input type="hidden" value="" name="order[]">';
            // used only to identify input key no saved in database
            question_area += '<label for="' + response.data.questionid + '" class="control-label display-block">' + response.data.survey_question_string + '</label>';
            question_area += '</div>';
            question_area += '<div class="col-lg-6">';
            question_area += '<div class="text-right">';
            question_area += '<a href="#"onclick="remove_question_from_database(this,' + response.data.questionid + '); return false;" data-toggle="tooltip"title=""><i class="mdi mdi-table-remove text-danger"></i></a>';
            question_area += '<a href="#"onclick="update_question(this, ' + type + ', ' + response.data.questionid + '); return false;"><i class="mdi mdi-table-refresh text-success question_update"data-toggle="tooltip"title="Muat ulang"></i></a>';
            question_area += '</div>';
            question_area += '</div>';
            question_area += '</div>';
            question_area += '<input type="text" onblur="update_question(this,\'' + type + '\',' + response.data.questionid + ');" data-questionid="' + response.data.questionid + '" class="form-control questionid">';
            if (type == 'textarea') {
                question_area += '<textarea class="form-control mt-2" disabled="disabled" rows="4">' + response.data.survey_question_only_for_preview + '</textarea>';
            } else if (type == 'checkbox' || type == 'radio') {
                question_area += '<div class="row mt-2 custom-questions">';
                box_description_icon_class = 'fa-plus';
                box_description_function = 'add_box_description_to_database(this,' + response.data.questionid + ',' + surveyid + '); return false;';
                question_area += '<div class="box_area">';
                question_area += '<div class="col-md-12">';
                question_area += '<a href="#" class="add_remove_action survey_add_more_box" onclick="' + box_description_function + '"><i class="fa ' + box_description_icon_class + '"></i></a>';
                question_area += '<div class="' + type + ' ' + type + '-primary">';
                if (type == 'checkbox') {
                    question_area += '<div class="custom-control custom-checkbox mb-2">';
                    question_area += '<input class="custom-control-input" type="' + type + '" disabled="disabled"/>';
                    question_area += '<label class="custom-control-label"><input onblur="update_question(this,\'' + type + '\',' + response.data.questionid + ');" type="text" data-box-descriptionid="' + response.data.questionboxdescriptionid + '" class="survey_input_box_description form-control form-control-sm"></label>';
                    question_area += '</div>';
                } else if (type == 'radio') {
                    question_area += '<div class="custom-control custom-radio mb-2">';
                    question_area += '<input class="custom-control-input" type="' + type + '" disabled="disabled"/>';
                    question_area += '<label class="custom-control-label"><input onblur="update_question(this,\'' + type + '\',' + response.data.questionid + ');" type="text" data-box-descriptionid="' + response.data.questionboxdescriptionid + '" class="survey_input_box_description form-control form-control-sm"></label>';
                    question_area += '</div>';
                }
                question_area += '</div>';
                question_area += '</div>';
                question_area += '</div>';
                // end box row
                question_area += '</div>';
            } else {
                question_area += '<input type="text" onchange="update_question(this,\'' + type + '\',' + response.data.questionid + ');" class="form-control mt-2" disabled="disabled" value="' + response.data.survey_question_only_for_preview + '">';
            }
            question_area += '</div>';
            question_area += '</li>';
            $('#survey_questions').append(question_area);
            $('html,body').animate({
                    scrollTop: $("#survey_questions li:last-child").offset().top
                },
            'slow');
            update_questions_order();
        },
        error: function () {
            alert('There something wrong.')
        },
    });
}
// Update question when user click on reload button


// Add more boxes to already added question // checkbox // radio box
function add_more_boxes(question, boxdescriptionid) {
    var box = $(question).parents('.custom-questions').clone();
    $(question).parents('.question').find('.custom-questions').last().after(box);
    $(box).find('i').removeClass('fa-plus').addClass('fa-minus').addClass('text-danger');
    $(box).find('input.survey_input_box_description').val('');
    $(box).find('input.survey_input_box_description').attr('data-box-descriptionid', boxdescriptionid);
    $(box).find('input.survey_input_box_description').focus();
    $(box).find('.add_remove_action').attr('onclick', 'remove_box_description_from_database(this,' + boxdescriptionid + '); return false;')
    update_questions_order();

}

// Remove question box description  // checkbox // radio box
function remove_box_description_from_database(question, questionboxdescriptionid) {
    data = {};
    data.questionid = questionboxdescriptionid;
    data._token = csrf_token;
    $.ajax({
        type: "POST",
        url: admin_url + 'surveys/delete_box_description',
        data: data,
        success: function (response) {
            $(question).parents('.custom-questions').remove();
        },
        error: function (response) {
            alert('There something wrong.')
        },
    });
}
// Add question box description  // checkbox // radio box
function add_box_description_to_database(question, questionid, surveyid) {
    data = {};
    data.surveyid = surveyid;
    data.questionid = questionid;
    data._token = csrf_token;
    $.ajax({
        type: "POST",
        url: admin_url + 'surveys/add_box_description',
        data: data,
        success: function (response) {
            response = JSON.parse(response);
            add_more_boxes(question, response.data.boxdescriptionid);
        },
        error: function (response) {
            alert('There something wrong.')
        },
    });
}