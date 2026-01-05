@extends('layouts.app', [
'class' => '',
'elementActive' => 'nfmsupport',
])
{{-- @php
dd($cached_closed_loans['openloans']);
@endphp --}}
@section('content')
<div class="form-group" id="overlay">
    <div class="spinner"></div>
</div>
<div class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">ALLOWS SCHOLARSHIP APPLICATION OF UPDATED INDEX</h4>
                    <!-- <h4 class="card-title">FETCH INDEX AND INSTITUTION  FROM HEF PORTAL AND UPDATES TO ID ON MOBILE PORTAL FOR APPLICANTS ALLOWED </h4> -->

                </div>
                <div class="card-body">

                    <form id="schidform" name="schidform">
                        @csrf
                        <div class="form-group">
                        <label for="indexumber">ACADEMIC YEAR</label>

<div class="row">

    <div class="col-md-9">
        <select name="applicantsubmittedetails" id="applicantsubmittedetails" class="form-control">
            @foreach ($cached_closed_loans['subsequentloansall'] as $names)
            <option value="{{ $names->name }}|{{ $names->productid }}|{{ $names->studentgrouping }}|{{ $names->academicyear }}|{{ $names->productcode }}">
                {{ $names->name }} {{ $names->type }} {{ $names->academicyear }}
            </option>
            @endforeach
        </select>


    </div>
</div>
</div>
                        <div class="form-group">
                        <label for="academicyear">KCSE YEAR</label>

                            <div class="row">

                                <div class="col-md-9">
                                    <select name="academicyear" id="academicyear" class="form-control">
                                        @php
                                        $startYear = 2020;
                                        $currentYear = date('Y');
                                        @endphp
                                        @for ($year = $startYear; $year <= $currentYear; $year++)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                            @endfor
                                    </select>



                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="indexumber">INDEX Number</label>
                            <input required type="number" class="form-control" id="indexnumber" name="indexnumber"
                                aria-describedby="indexnumber" placeholder="Enter index number">
                        </div>
                        <!-- <div class="form-group">
                            <div class="row">

                                <div class="col-md-9">
                                    <label for="idnumber">APPLICANT PHONE NUMBER</label>
                                    <input required type="number" min=12 class="form-control" id="phoneverify" name="phoneverify" aria-describedby="phoneverify" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div> -->

                      
                        <div class="form-group">
                            <label for="idumber">ID Number</label>
                            <input required type="number" class="form-control" id="idnumber" name="idnumber"
                                aria-describedby="idnumber" placeholder="Enter id number">
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>


            </div>

        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">WHITELIST MINOR SUBSEQUENT</h4>
                    <h4 class="card-title">ENTER PHONE NUMBER ON AX THAT IS ATTACHED TO THE MINOR</h4>
                    <h4 class="card-title">(IF MISSING ON AX)</h4>

                    <h4 class="card-title">ENTER PHONE NUMBER ON HEF PORTAL THAT IS ATTACHED TO THE MINOR</h4>


                </div>
                <div class="card-body">

                    <form id="minorverifyform" name="minorverifyform">
                        @csrf

                        <div class="form-group">
                        <label for="indexumber">ACADEMIC YEAR</label>

<div class="row">

    <div class="col-md-9">
        <select name="applicantsubmittedetails" id="applicantsubmittedetails" class="form-control">
            @foreach ($cached_closed_loans['subsequentloansall'] as $names)
            <option value="{{ $names->name }}|{{ $names->productid }}|{{ $names->studentgrouping }}|{{ $names->academicyear }}|{{ $names->productcode }}">
                {{ $names->name }} {{ $names->type }} {{ $names->academicyear }}
            </option>
            @endforeach
        </select>


    </div>
</div>
</div>
                        <div class="form-group">
                        <label for="academicyear">KCSE YEAR</label>

                            <div class="row">

                                <div class="col-md-9">
                                    <select name="academicyear" id="academicyear" class="form-control">
                                        @php
                                        $startYear = 2022;
                                        $currentYear = date('Y');
                                        @endphp
                                        @for ($year = $startYear; $year <= $currentYear; $year++)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                            @endfor
                                    </select>



                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="indexumber">INDEX Number</label>
                            <input required type="number" class="form-control" id="indexnumber" name="indexnumber"
                                aria-describedby="indexnumber" placeholder="Enter index number">
                        </div>




                        <div class="form-group">
                            <div class="row">

                                <div class="col-md-9">
                                    <label for="idnumber">APPLICANT PHONE NUMBER</label>
                                    <input required type="number" min=12 class="form-control" id="phoneverify" name="phoneverify" aria-describedby="phoneverify" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>

                      




                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">UPDATE/FETCH IPRS/PORTAL NAMES NEW FUNDING PORTAL APPLICANTS</h4>
                </div>
                <div class="card-body">

                    <form id="iprsidform" name="iprsidform">
                        @csrf
                        <div class="form-group">
                            <label for="idnumber">SERIAL Number(ID/MAISHA</label>
                            <input required type="number" class="form-control" id="idnumberiprs" name="idnumberiprs"
                                aria-describedby="idnumber" placeholder="Enter serial number">
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>


            </div>

        </div>
    </div>





    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> NEW FUNDING SUBSEQUENT APPLICANTS</h4>
                </div>

 <!-- <form id="importblockform" method="POST" enctype="multipart/form-data" class="btn btn-danger"> -->
  <form id="importblockform" method="POST" enctype="multipart/form-data" data-url="{{ route('import-block') }}">
 @csrf
                <input type="file" name="file">



            </form>
    @can('lending')

            <button id="btn-importblockform" type="submit" form="importblockform" class="btn btn-success"
                title="Import Project">
                <i class="cil-cloud-download"></i>IMPORT BLOCKED APPLICANTS</button> @endcan

 <div class="div_r">
                    <form action="{{ route('downloadblocktemplate') }}" method="GET" enctype="multipart/form-data"
                        class="d-flex">
                        @csrf

                        <button class="btn btn-primary">Download block template</button>

                    </form>
                    <br><br>
                </div>




                <div class="card-body">
                    <h6 class="card-title">BLOCKED APPLICANTS</h4>
                        <div class="table-wrapper">

                            <table class="table table-bordered table-striped  ref" id="datatableblocked" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>

                                        <th>IDNO</th>
                                        <th>REASON</th>

                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                        <th>ACADEMIC YEAR</th>


                                    </tr>
                                </thead>

                            </table>
                        </div>
                </div>

            </div>

        </div>
    </div>
    @can('audit')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">ADD MINOR NEW FUNDING PORTAL APPLICANTS</h4>
                </div>
                <div class="card-body">

                    <form id="minorform" name="minorform">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-9">

                                    <input required type="date" name="birthdatenfm" id="birthdatenfm" min="2003-01-01" max="2006-12-31" class="form-control datetimepicker"
                                        placeholder="Datetime Picker Here" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-9">

                                    <select name="academiclevelnfm" id="academiclevelnfm" class="form-control">
                                        @foreach ($cached_closed_loans['access_level'] as $level)
                                        <option value="{{ $level->level }}">{{ $level->level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-9">

                                    <select name="examyrnfm" id="examyrnfm" class="form-control">
                                        @foreach ($cached_closed_loans['exam_yr'] as $year)
                                        <option value="{{ $year->yr}}">{{ $year->yr }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <div class="row">

                                <div class="col-md-9">
                                    <label for="idnumber">KCSE INDEX NUMBER</label>
                                    <input required type="number" min=12 class="form-control" id="kcseindexnumber" name="kcseindexnumber"
                                        aria-describedby="kcseindexnumber" placeholder="Enter kcse index number">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>

            </div>

        </div>
    </div>

    @endcan
















</div>
@endsection