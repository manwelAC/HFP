@extends('layouts.front-app')
@section('title')
{{Auth::user()->access[Route::current()->action["as"]]["user_type"]}} - Performance Evaluation
@stop
@section("styles")
<style>
    th {
        text-align: center;
    }
    .btn-check {
        display: none;
    }
    .dz-success-mark {
        display: none;
    }
    .dz-error-mark {
        display: none;
    }
    .rating-badge {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
        display: inline-block;
    }
    .rating-outstanding      { background-color: #1cc88a; color: #fff; }
    .rating-very_satisfactory{ background-color: #36b9cc; color: #fff; }
    .rating-satisfactory     { background-color: #4e73df; color: #fff; }
    .rating-unsatisfactory   { background-color: #e74a3b; color: #fff; }
    
    #modalView .table th,
    #modalView .table td {
        text-align: left;
    }
</style>
@stop

@section("content")
@if(preg_match("/R/i", Auth::user()->access[Route::current()->action["as"]]["access"]) == "0")

    {{Auth::user()->access[Route::current()->action["as"]]["access"]}}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="row">
                <div class="col-xl-12 col-sm-12 col-12 mb-4">
                    <div class="row">
                        <div class="col-xl-10 col-sm-8 col-12">
                            <label>YOU HAVE NO PRIVILEDGE ON THIS PAGE</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@else

<div class="page-wrapper" id="performance_eval_page">
    <div class="content container-fluid">

        {{-- Page Header --}}
        <div class="page-name mb-4">
            <h4 class="m-0">Performance Evaluation</h4>
            <label>{{date('D, d M Y')}}</label>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0">Records</h5>
                        @if(preg_match("/C/i", Auth::user()->access[Route::current()->action["as"]]["access"]))
                        <button class="btn btn-success btn-sm" id="btnAddNew">
                            <i class="fa fa-plus"></i> Add New
                        </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tblPerformanceEvaluation" class="table table-bordered table-hover table-striped w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Employee</th>
                                        <th width="18%">Rating</th>
                                        <th width="12%">Date Served</th>
                                        <th width="10%">Attachment</th>
                                        <th width="12%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     ADD MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="modalAddLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddLabel">
                    <i class="fa fa-plus-circle"></i> Add Performance Evaluation
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAdd" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" id="addEmployeeId" class="form-control select2-modal" required style="width:100%">
                                    <option value=""></option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ strtoupper($emp->last_name) }}, {{ $emp->first_name }} {{ $emp->middle_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Rating <span class="text-danger">*</span></label>
                                <select name="rating" class="form-control" required>
                                    <option value="">Select Rating</option>
                                    <option value="outstanding">Outstanding</option>
                                    <option value="very_satisfactory">Very Satisfactory</option>
                                    <option value="satisfactory">Satisfactory</option>
                                    <option value="unsatisfactory">Unsatisfactory</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date Served <span class="text-danger">*</span></label>
                                <input type="date" name="date_served" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Performance Details <span class="text-danger">*</span></label>
                                <textarea name="performance_details" class="form-control" rows="4" required
                                    placeholder="Describe the employee's performance..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2"
                                    placeholder="Optional remarks..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>
                                    Attachment
                                    <small class="text-muted">(Optional &mdash; any file type, max 20MB)</small>
                                </label>
                                <input type="file" name="attachment" class="form-control-file">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     EDIT MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">
                    <i class="fa fa-pencil"></i> Edit Performance Evaluation
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEdit" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="editId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" id="editEmployeeId" class="form-control select2-modal" required style="width:100%">
                                    <option value=""></option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ strtoupper($emp->last_name) }}, {{ $emp->first_name }} {{ $emp->middle_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Rating <span class="text-danger">*</span></label>
                                <select name="rating" id="editRating" class="form-control" required>
                                    <option value="">-- Select Rating --</option>
                                    <option value="outstanding">Outstanding</option>
                                    <option value="very_satisfactory">Very Satisfactory</option>
                                    <option value="satisfactory">Satisfactory</option>
                                    <option value="unsatisfactory">Unsatisfactory</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date Served <span class="text-danger">*</span></label>
                                <input type="date" name="date_served" id="editDateServed" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Performance Details <span class="text-danger">*</span></label>
                                <textarea name="performance_details" id="editPerformanceDetails"
                                    class="form-control" rows="4" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" id="editRemarks" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>
                                    Attachment
                                    <small class="text-muted">(Leave blank to keep existing file)</small>
                                </label>
                                <div id="editCurrentAttachment" class="mb-2"></div>
                                <input type="file" name="attachment" class="form-control-file">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     VIEW MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalView" tabindex="-1" role="dialog" aria-labelledby="modalViewLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalViewLabel">
                    View Performance Evaluation
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-sm">
                    <tr>
                        <th width="30%">Employee</th>
                        <td id="viewEmployee"></td>
                    </tr>
                    <tr>
                        <th>Rating</th>
                        <td id="viewRating"></td>
                    </tr>
                    <tr>
                        <th>Date Served</th>
                        <td id="viewDateServed"></td>
                    </tr>
                    <tr>
                        <th>Performance Details</th>
                        <td id="viewPerformanceDetails" style="white-space: pre-wrap;"></td>
                    </tr>
                    <tr>
                        <th>Remarks</th>
                        <td id="viewRemarks"></td>
                    </tr>
                    <tr>
                        <th>Attachment</th>
                        <td id="viewAttachment"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     SUCCESS MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalSuccess" tabindex="-1" role="dialog" aria-labelledby="modalSuccessLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalSuccessLabel">
                    <i class="fa fa-check-circle"></i> Success
                </h5>
            </div>
            <div class="modal-body">
                <p id="successMessage"></p>
            </div>
        </div>
    </div>
</div>

@endif
@stop


@section("scripts")
<script src="{{asset_with_env('plugins/highcharts/highcharts.js')}}"></script>
<script src="{{asset_with_env('plugins/highcharts/variable-pie.js')}}"></script>
<script src="{{asset_with_env('plugins/highcharts/exporting.js')}}"></script>
<script src="{{asset_with_env('plugins/highcharts/export-data.js')}}"></script>
<script src="{{asset_with_env('plugins/highcharts/accessibility.js')}}"></script>

<script>
$(document).ready(function () {

    // ── Access flags (resolved server-side, safe to embed) ───────────────────
    var canCreate = {{ preg_match("/C/i", Auth::user()->access[Route::current()->action["as"]]["access"]) ? 'true' : 'false' }};
    var canUpdate = {{ preg_match("/U/i", Auth::user()->access[Route::current()->action["as"]]["access"]) ? 'true' : 'false' }};
    var canDelete = {{ preg_match("/D/i", Auth::user()->access[Route::current()->action["as"]]["access"]) ? 'true' : 'false' }};

    // ── Rating badge helper ───────────────────────────────────────────────────
    function ratingBadge(rating) {
        var labels = {
            outstanding:        'Outstanding',
            very_satisfactory:  'Very Satisfactory',
            satisfactory:       'Satisfactory',
            unsatisfactory:     'Unsatisfactory'
        };
        var label = labels[rating] || rating;
        return '<span class="rating-badge rating-' + rating + '">' + label + '</span>';
    }

    // ── DataTable ─────────────────────────────────────────────────────────────
    var table = $('#tblPerformanceEvaluation').DataTable({
        ajax: {
            url: '{{ route("performance_evaluation.list") }}',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            {
                data: null,
                orderable: false,
                render: function (data, type, row, meta) { return meta.row + 1; }
            },
            { data: 'employee_name' },
            {
                data: 'rating',
                render: function (data) { return ratingBadge(data); }
            },
            { data: 'date_served' },
            {
                data: 'attachment',
                orderable: false,
                render: function (data) {
                    if (!data) return '<span class="text-muted">—</span>';
                    var filename = data.split('/').pop();
                    return '<a href="/' + data + '" target="_blank" class="btn btn-xs btn-default">'
                         + '<i class="fa fa-paperclip"></i> ' + filename
                         + '</a>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    var viewBtn = '<button class="btn btn-xs btn-info mr-1 btnView" data-id="' + row.id + '" title="View">'
                                + '<i class="fa fa-eye"></i></button>';

                    var editBtn = '';
                    if (canUpdate) {
                        editBtn = '<button class="btn btn-xs btn-warning mr-1 btnEdit" data-id="' + row.id + '" title="Edit">'
                                + '<i class="fa fa-pen"></i></button>';
                    }

                    var delBtn = '';
                    if (canDelete) {
                        delBtn = '<button class="btn btn-xs btn-danger btnDelete" data-id="' + row.id + '" title="Delete">'
                               + '<i class="fa fa-trash"></i></button>';
                    }

                    return viewBtn + editBtn + delBtn;
                }
            }
        ],
        order: [[3, 'desc']],
        responsive: true,
        language: {
            emptyTable: 'No records found.'
        }
    });

    // ── Success Modal Helper ──────────────────────────────────────────────────
    function showSuccessModal(message) {
        $('#successMessage').text(message);
        $('#modalSuccess').modal('show');
        setTimeout(function () {
            $('#modalSuccess').modal('hide');
        }, 3000);
    }

    // ── Select2 (bound to modal to avoid z-index issues) ─────────────────────
    $('#modalAdd, #modalEdit').on('shown.bs.modal', function () {
        $(this).find('.select2-modal').select2({
            dropdownParent: $(this),
            width: '100%',
            allowClear: true,
            placeholder: 'Search and select an employee...',
            minimumInputLength: 0,
            tags: false
        });
    });

    // ── ADD ───────────────────────────────────────────────────────────────────
    $('#btnAddNew').on('click', function () {
        $('#formAdd')[0].reset();
        $('#addEmployeeId').val(null).trigger('change');
        $('#modalAdd').modal('show');
    });

    $('#formAdd').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        HoldOn.open({ theme: 'sk-circle', message: 'Saving...' });

        $.ajax({
            url: '{{ route("performance_evaluation.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                HoldOn.close();
                if (res.success) {
                    $('#modalAdd').modal('hide');
                    table.ajax.reload(null, false);
                    showSuccessModal(res.message);
                } else {
                    $.notify(res.message, { style: 'bootstrap', className: 'danger' });
                }
            },
            error: function (xhr) {
                HoldOn.close();
                var msg = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                $.notify(msg, { style: 'bootstrap', className: 'danger' });
            }
        });
    });

    // ── VIEW ──────────────────────────────────────────────────────────────────
    $(document).on('click', '.btnView', function () {
        var id = $(this).data('id');
        HoldOn.open({ theme: 'sk-circle', message: 'Loading...' });

        $.get('{{ url("performance_evaluation") }}/' + id, function (res) {
            HoldOn.close();
            if (res.success) {
                var d = res.data;
                $('#viewEmployee').text(d.employee_name);
                $('#viewRating').html(ratingBadge(d.rating));
                $('#viewDateServed').text(d.date_served);
                $('#viewPerformanceDetails').text(d.performance_details);
                $('#viewRemarks').text(d.remarks || '—');

                if (d.attachment) {
                    var filename = d.attachment.split('/').pop();
                    $('#viewAttachment').html(
                        '<a href="/' + d.attachment + '" target="_blank">'
                        + '<i class="fa fa-paperclip"></i> ' + filename + '</a>'
                    );
                } else {
                    $('#viewAttachment').text('—');
                }

                $('#modalView').modal('show');
            } else {
                $.notify('Could not load record.', { style: 'bootstrap', className: 'danger' });
            }
        }).fail(function () {
            HoldOn.close();
            $.notify('An error occurred while loading the record.', { style: 'bootstrap', className: 'danger' });
        });
    });

    // ── EDIT ──────────────────────────────────────────────────────────────────
    $(document).on('click', '.btnEdit', function () {
        var id = $(this).data('id');
        HoldOn.open({ theme: 'sk-circle', message: 'Loading...' });

        $.get('{{ url("performance_evaluation") }}/' + id + '/edit', function (res) {
            HoldOn.close();
            if (res.success) {
                var d = res.data;
                $('#editId').val(d.id);
                $('#editEmployeeId').val(d.employee_id).trigger('change');
                $('#editRating').val(d.rating);
                $('#editDateServed').val(d.date_served);
                $('#editPerformanceDetails').val(d.performance_details);
                $('#editRemarks').val(d.remarks);

                if (d.attachment) {
                    var filename = d.attachment.split('/').pop();
                    $('#editCurrentAttachment').html(
                        '<small><i class="fa fa-paperclip"></i> Current: '
                        + '<a href="/' + d.attachment + '" target="_blank">' + filename + '</a></small>'
                    );
                } else {
                    $('#editCurrentAttachment').html('');
                }

                $('#modalEdit').modal('show');
            } else {
                $.notify('Could not load record.', { style: 'bootstrap', className: 'danger' });
            }
        }).fail(function () {
            HoldOn.close();
            $.notify('An error occurred while loading the record.', { style: 'bootstrap', className: 'danger' });
        });
    });

    $('#formEdit').on('submit', function (e) {
        e.preventDefault();
        var id = $('#editId').val();
        var formData = new FormData(this);
        formData.append('_method', 'PUT'); // Laravel PUT spoofing via POST
        HoldOn.open({ theme: 'sk-circle', message: 'Updating...' });

        $.ajax({
            url: '{{ url("performance_evaluation") }}/' + id,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                HoldOn.close();
                if (res.success) {
                    $('#modalEdit').modal('hide');
                    table.ajax.reload(null, false);
                    showSuccessModal(res.message);
                } else {
                    $.notify(res.message, { style: 'bootstrap', className: 'danger' });
                }
            },
            error: function (xhr) {
                HoldOn.close();
                var msg = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                $.notify(msg, { style: 'bootstrap', className: 'danger' });
            }
        });
    });

    // ── DELETE ────────────────────────────────────────────────────────────────
    $(document).on('click', '.btnDelete', function () {
        var id = $(this).data('id');

        $.confirm({
            title: 'Delete Record',
            content: 'Are you sure you want to delete this performance evaluation? This action cannot be undone.',
            type: 'red',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: 'Yes, Delete',
                    btnClass: 'btn-danger',
                    action: function () {
                        HoldOn.open({ theme: 'sk-circle', message: 'Deleting...' });

                        $.ajax({
                            url: '{{ url("performance_evaluation") }}/' + id,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function (res) {
                                HoldOn.close();
                                if (res.success) {
                                    table.ajax.reload(null, false);
                                    showSuccessModal(res.message);
                                } else {
                                    $.notify(res.message, { style: 'bootstrap', className: 'danger' });
                                }
                            },
                            error: function () {
                                HoldOn.close();
                                $.notify('An error occurred while deleting.', { style: 'bootstrap', className: 'danger' });
                            }
                        });
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-default'
                }
            }
        });
    });

});
</script>
@stop