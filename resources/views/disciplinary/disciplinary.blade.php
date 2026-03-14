@extends('layouts.front-app')
@section('title')
{{Auth::user()->access[Route::current()->action["as"]]["user_type"]}} - Disciplinary Action
@stop
@section("styles")
<style>
    th{
        text-align: center;
    }
    .btn-check{
        display:none;
    }
</style>
@stop
@section("content")
@if(preg_match("/R/i", Auth::user()->access[Route::current()->action["as"]]["access"])=="0")
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
<div class="page-wrapper">
    <div class="content container-fluid">

        {{-- Page Header --}}
        <div class="page-name mb-4">
            <h4 class="m-0">Disciplinary Action</h4>
            <label>{{date('D, d M Y')}}</label>
        </div>

        {{-- Table Card --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Disciplinary Action List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="da_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Case Number</th>
                                        <th>NTE Case Number</th>
                                        <th>Employee</th>
                                        <th>Sanction</th>
                                        <th>Date Issued</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loaded via AJAX --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- View DA Modal --}}
<div class="modal fade" id="modal_view_da" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Disciplinary Action — <span id="view_da_case_number"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="view_da_body">
                {{-- Loaded via AJAX --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endif

{{-- Success Modal --}}
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" style="color: white;">✓ Success</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="successMessage" style="text-align: center; padding: 30px;">
                <!-- Message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@stop
@section("scripts")
<script>
// Function to show success modal
function showSuccessModal(message) {
    $('#successMessage').html(message);
    $('#successModal').modal('show');
    setTimeout(function() {
        $('#successModal').modal('hide');
    }, 3000);
}

$(document).ready(function(){

    // Initialize DataTable
    var da_table = $('#da_table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("da.list") }}',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'case_number' },
            { data: 'nte_case_number', render: function(data){
                return data ?? '<span class="text-muted">N/A</span>';
            }},
            { data: 'employee_name' },
            { data: 'sanction', render: function(data){
                var labels = {
                    'written_warning' : '<span class="badge badge-info">Written Warning</span>',
                    'suspension'      : '<span class="badge badge-warning">Suspension</span>',
                    'demotion'        : '<span class="badge badge-secondary">Demotion</span>',
                    'termination'     : '<span class="badge badge-danger">Termination</span>',
                    'reprimand'       : '<span class="badge badge-dark">Reprimand</span>',
                    'others'          : '<span class="badge badge-light">Others</span>'
                };
                return labels[data] ?? data;
            }},
            { data: 'date_issued' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    // View DA
    $(document).on('click', '.btn_view_da', function(){
        var id = $(this).data('id');

        HoldOn.open({ theme: 'sk-circle' });

        $.ajax({
            url: '/da/view/' + id,
            type: 'GET',
            success: function(response){
                HoldOn.close();
                if(response.success){
                    var da = response.data;

                    var sanction_labels = {
                        'written_warning' : 'Written Warning',
                        'suspension'      : 'Suspension',
                        'demotion'        : 'Demotion',
                        'termination'     : 'Termination',
                        'reprimand'       : 'Reprimand',
                        'others'          : 'Others'
                    };

                    $('#view_da_case_number').text(da.case_number);
                    $('#view_da_body').html(`
                        <div class="row">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Case Number</label>
                                <p>${da.case_number}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">NTE Case Number</label>
                                <p>${da.nte_case_number ?? 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Employee</label>
                                <p>${da.employee_name}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Date Issued</label>
                                <p>${da.date_issued ?? 'N/A'}</p>
                            </div>
                            <div class="col-md-12">
                                <label class="font-weight-bold">Case Details</label>
                                <p>${da.case_details}</p>
                            </div>
                            <div class="col-md-12">
                                <label class="font-weight-bold">Remarks</label>
                                <p>${da.remarks ?? 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Sanction</label>
                                <p>${sanction_labels[da.sanction] ?? da.sanction}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Sanction Details</label>
                                <p>${da.sanction_details ?? 'N/A'}</p>
                            </div>
                        </div>
                    `);

                    $('#modal_view_da').modal('show');
                } else {
                    $.notify({ message: response.message }, { type: 'danger' });
                }
            },
            error: function(){
                HoldOn.close();
                $.notify({ message: 'Something went wrong. Please try again.' }, { type: 'danger' });
            }
        });
    });

    // Delete DA
    $(document).on('click', '.btn_delete_da', function(){
        var id = $(this).data('id');

        $.confirm({
            title: 'Delete Disciplinary Action',
            content: 'Are you sure you want to delete this Disciplinary Action? The NTE will be reopened.',
            type: 'red',
            buttons: {
                confirm: {
                    text: 'Yes, Delete',
                    btnClass: 'btn-danger',
                    action: function(){
                        HoldOn.open({ theme: 'sk-circle' });

                        $.ajax({
                            url: '/da/delete/' + id,
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response){
                                HoldOn.close();
                                if(response.success){
                                    da_table.ajax.reload();
                                    showSuccessModal('<strong>The Disciplinary Action was Successfully Deleted</strong>');
                                } else {
                                    $.notify('Error: ' + response.message, {type:'danger', icon:'close'});
                                }
                            },
                            error: function(){
                                HoldOn.close();
                                $.notify('Something went wrong. Please try again.', {type:'danger', icon:'close'});
                            }
                        });
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-secondary'
                }
            }
        });
    });

});
</script>
@stop