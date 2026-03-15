<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblIncidentReportWitnesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::connection('intra_payroll')->hasTable('tbl_incident_report_witnesses')) {
            Schema::connection('intra_payroll')->create('tbl_incident_report_witnesses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('incident_report_id')->comment('tbl_incident_report.id');
                $table->unsignedBigInteger('employee_id')->comment('tbl_employee.id');
                $table->timestamp('date_added')->useCurrent();
                
                // Foreign keys
                $table->foreign('incident_report_id')
                    ->references('id')
                    ->on('intra_payroll.tbl_incident_report')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('intra_payroll')->dropIfExists('tbl_incident_report_witnesses');
    }
}
