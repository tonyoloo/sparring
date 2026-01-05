@extends('layouts.app', [
'class' => '',
'elementActive' => 'studentprofile',
])

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Student Profile - Internship Placement</h4>
        </div>
        <div class="card-body">
            <!-- Step Navigation -->
            <ul class="nav nav-tabs" id="stepTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">Personal Details</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="institution-tab" data-toggle="tab" href="#institution" role="tab" aria-controls="institution" aria-selected="false">Institution Details</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="company-tab" data-toggle="tab" href="#company" role="tab" aria-controls="company" aria-selected="false">Company Details</a>
                </li>
            </ul>
            <form id="studentProfileForm" method="POST" action="#">
                @csrf
                <div class="tab-content mt-3" id="stepTabsContent">
                    <!-- Step 1: Personal Details -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ isset($user) ? $user->name : '' }}" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ isset($user) ? $user->email : '' }}" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_number">ID Number</label>
                            <input type="text" class="form-control" id="id_number" name="id_number" required>
                        </div>
                        <button type="button" class="btn btn-primary float-right" id="toInstitution">Next</button>
                    </div>
                    <!-- Step 2: Institution Details -->
                    <div class="tab-pane fade" id="institution" role="tabpanel" aria-labelledby="institution-tab">
                        <div class="form-group">
                            <label for="institution_name">Institution Name</label>
                            <input type="text" class="form-control" id="institution_name" name="institution_name" required autocomplete="off">
                            <div id="institutionList" class="list-group" style="position: absolute; z-index: 1000;"></div>
                        </div>
                        <div class="form-group">
                            <label for="course">Course</label>
                            <input type="text" class="form-control" id="course" name="course" required autocomplete="off">
                            <div id="courseList" class="list-group" style="position: absolute; z-index: 1000;"></div>
                        </div>
                        <div class="form-group">
                            <label for="year_of_study">Year of Study</label>
                            <select class="form-control" id="year_of_study" name="year_of_study" required>
                                <option value="">Select Year</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <!-- Add more years as needed -->
                            </select>
                        </div>
                        <button type="button" class="btn btn-secondary" id="backToPersonal">Back</button>
                        <button type="button" class="btn btn-primary float-right" id="toCompany">Next</button>
                    </div>
                    <!-- Step 3: Company Details -->
                    <div class="tab-pane fade" id="company" role="tabpanel" aria-labelledby="company-tab">
                        <div class="form-group">
                            <label for="county">County</label>
                            <input type="text" class="form-control" id="county" name="county" required autocomplete="off">
                            <div id="countyList" class="list-group" style="position: absolute; z-index: 1000;"></div>
                        </div>
                        <div class="form-group">
                            <label for="town">Town</label>
                            <input type="text" class="form-control" id="town" name="town" required autocomplete="off">
                            <div id="townList" class="list-group" style="position: absolute; z-index: 1000;"></div>
                        </div>
                        <div class="form-group">
                            <label>Attachment Period</label>
                            <div class="row">
                                <div class="col">
                                    <input type="date" class="form-control" id="attachment_from" name="attachment_from" required placeholder="From">
                                </div>
                                <div class="col">
                                    <input type="date" class="form-control" id="attachment_to" name="attachment_to" required placeholder="To">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="department">Department/Section</label>
                            <select class="form-control" id="department" name="department" required>
                                <option value="">Select Department/Section</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="IT">IT</option>
                                <option value="Marketing">Marketing</option>
                                <!-- Add more departments as needed -->
                            </select>
                        </div>
                        <button type="button" class="btn btn-secondary" id="backToInstitution">Back</button>
                        <button type="submit" class="btn btn-success float-right">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Step validation helpers
    function validateStep(stepId) {
        let valid = true;
        $(stepId + ' [required]').each(function() {
            if (!$(this).val() || $(this).val() === '') {
                valid = false;
            }
        });
        return valid;
    }

    // Initial state: disable Next buttons
    $('#toInstitution').prop('disabled', true);
    $('#toCompany').prop('disabled', true);

    // Validate Personal Step
    $('#personal [required]').on('input change', function() {
        $('#toInstitution').prop('disabled', !validateStep('#personal'));
    });

    // Next to Institution
    $('#toInstitution').click(function() {
        if (validateStep('#personal')) {
            $('#institution-tab').tab('show');
        }
    });
    // Back to Personal
    $('#backToPersonal').click(function() {
        $('#personal-tab').tab('show');
    });

    // Validate Institution Step
    $('#institution [required]').on('input change', function() {
        $('#toCompany').prop('disabled', !validateStep('#institution'));
    });

    // Next to Company
    $('#toCompany').click(function() {
        if (validateStep('#institution')) {
            $('#company-tab').tab('show');
        }
    });
    // Back to Institution
    $('#backToInstitution').click(function() {
        $('#institution-tab').tab('show');
    });

    // On page load, check if steps are already filled (for browser autofill)
    $('#toInstitution').prop('disabled', !validateStep('#personal'));
    $('#toCompany').prop('disabled', !validateStep('#institution'));

    // AJAX form submission
    $('#studentProfileForm').submit(function(e) {
        e.preventDefault();
        if (!validateStep('#company')) {
            alert('Please fill all required fields in Company Details.');
            return;
        }
        var form = $(this);
        var formData = form.serialize();
        $.ajax({
            url: '/submit-student',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    form[0].reset();
                    $('#personal-tab').tab('show');
                    $('#toInstitution').prop('disabled', true);
                    $('#toCompany').prop('disabled', true);
                    $('<div class="alert alert-success mt-3">Profile submitted successfully!</div>')
                        .insertBefore('#studentProfileForm').delay(3000).fadeOut();
                } else {
                    $('<div class="alert alert-danger mt-3">Submission failed. Please try again.</div>')
                        .insertBefore('#studentProfileForm').delay(3000).fadeOut();
                }
            },
            error: function(xhr) {
                let msg = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).join('<br>');
                }
                $('<div class="alert alert-danger mt-3">' + msg + '</div>')
                    .insertBefore('#studentProfileForm').delay(4000).fadeOut();
            }
        });
    });

    // Autocomplete for institution_name
    let typingTimer;
    const doneTypingInterval = 300; // ms
    const $input = $('#institution_name');
    const $list = $('#institutionList');

    $input.on('keyup', function() {
        clearTimeout(typingTimer);
        const query = $(this).val();
        if (query.length < 2) {
            $list.empty().hide();
            return;
        }
        typingTimer = setTimeout(function() {
            $.ajax({
                url: '/api/institutions',
                data: { q: query },
                success: function(data) {
                    $list.empty();
                    if (data.length) {
                        data.forEach(function(item) {
                            $list.append(
                                `<a href="#" class="list-group-item list-group-item-action" data-name="${item.INSTITUTIONNAME}">${item.INSTITUTIONNAME}</a>`
                            );
                        });
                        $list.show();
                    } else {
                        $list.hide();
                    }
                }
            });
        }, doneTypingInterval);
    });

    $list.on('click', '.list-group-item', function(e) {
        e.preventDefault();
        $input.val($(this).data('name'));
        $list.empty().hide();
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('#institution_name, #institutionList').length) {
            $list.empty().hide();
        }
    });

    // Autocomplete for course
    let typingTimerCourse;
    const $inputCourse = $('#course');
    const $listCourse = $('#courseList');
    $inputCourse.on('keyup', function() {
        clearTimeout(typingTimerCourse);
        const query = $(this).val();
        if (query.length < 2) {
            $listCourse.empty().hide();
            return;
        }
        typingTimerCourse = setTimeout(function() {
            $.ajax({
                url: '/api/courses',
                data: { q: query },
                success: function(data) {
                    $listCourse.empty();
                    if (data.length) {
                        data.forEach(function(item) {
                            $listCourse.append(
                                `<a href=\"#\" class=\"list-group-item list-group-item-action\" data-name=\"${item.COURSEDESCRIPTION}\">${item.COURSEDESCRIPTION}</a>`
                            );
                        });
                        $listCourse.show();
                    } else {
                        $listCourse.hide();
                    }
                }
            });
        }, doneTypingInterval);
    });
    $listCourse.on('click', '.list-group-item', function(e) {
        e.preventDefault();
        $inputCourse.val($(this).data('name'));
        $listCourse.empty().hide();
    });
    $(document).click(function(e) {
        if (!$(e.target).closest('#course, #courseList').length) {
            $listCourse.empty().hide();
        }
    });

    // Autocomplete for county
    let typingTimerCounty;
    const $inputCounty = $('#county');
    const $listCounty = $('#countyList');
    $inputCounty.on('keyup', function() {
        clearTimeout(typingTimerCounty);
        const query = $(this).val();
        if (query.length < 2) {
            $listCounty.empty().hide();
            return;
        }
        typingTimerCounty = setTimeout(function() {
            $.ajax({
                url: '/api/counties',
                data: { q: query },
                success: function(data) {
                    $listCounty.empty();
                    if (data.length) {
                        data.forEach(function(item) {
                            $listCounty.append(
                                `<a href=\"#\" class=\"list-group-item list-group-item-action\" data-name=\"${item.county_name}\" data-id=\"${item.county_id}\">${item.county_name}</a>`
                            );
                        });
                        $listCounty.show();
                    } else {
                        $listCounty.hide();
                    }
                }
            });
        }, doneTypingInterval);
    });
    let selectedCountyId = null;
    $listCounty.on('click', '.list-group-item', function(e) {
        e.preventDefault();
        $inputCounty.val($(this).data('name'));
        selectedCountyId = $(this).data('id');
        $listCounty.empty().hide();
        // Clear town input and list when county changes
        $inputTown.val('');
        $listTown.empty().hide();
    });
    $(document).click(function(e) {
        if (!$(e.target).closest('#county, #countyList').length) {
            $listCounty.empty().hide();
        }
    });

    // Autocomplete for town
    let typingTimerTown;
    const $inputTown = $('#town');
    const $listTown = $('#townList');
    $inputTown.on('keyup', function() {
        clearTimeout(typingTimerTown);
        const query = $(this).val();
        if (query.length < 2 || !selectedCountyId) {
            $listTown.empty().hide();
            return;
        }
        typingTimerTown = setTimeout(function() {
            $.ajax({
                url: '/api/towns-by-county',
                data: { q: query, county_id: selectedCountyId },
                success: function(data) {
                    $listTown.empty();
                    if (data.length) {
                        data.forEach(function(item) {
                            $listTown.append(
                                `<a href=\"#\" class=\"list-group-item list-group-item-action\" data-name=\"${item.town_name}\">${item.town_name}</a>`
                            );
                        });
                        $listTown.show();
                    } else {
                        $listTown.hide();
                    }
                }
            });
        }, doneTypingInterval);
    });
    $listTown.on('click', '.list-group-item', function(e) {
        e.preventDefault();
        $inputTown.val($(this).data('name'));
        $listTown.empty().hide();
    });
    $(document).click(function(e) {
        if (!$(e.target).closest('#town, #townList').length) {
            $listTown.empty().hide();
        }
    });
});
</script>
@endpush