@extends('layouts.app', [
'class' => '',
'elementActive' => 'viewprofile',
])

@section('content')
    <div class="content">

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">View Student Profile - Internship Placement</h4>
        </div>
        <div class="card-body">
                    <div class="table-wrapper">

            <button id="download-students" class="btn btn-primary mb-3">Download</button>
            <table id="studentstable" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Gender</th>

                        <th>ID Number</th>
                        <th>Institution</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>County</th>

                        <th>Town</th>
                        <th>Attachment From</th>
                        <th>Attachment To</th>
                        <th>Department</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
</div>
@endsection


