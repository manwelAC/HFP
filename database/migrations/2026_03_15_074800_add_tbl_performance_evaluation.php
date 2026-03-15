<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTblPerformanceEvaluation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('intra_payroll')->create('tbl_performance_evaluation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->comment('tbl_employee.id');
            $table->text('performance_details');
            $table->enum('rating', ['outstanding', 'very_satisfactory', 'satisfactory', 'unsatisfactory']);
            $table->text('remarks')->nullable();
            $table->date('date_served');
            $table->string('attachment', 255)->nullable()->comment('Stored in public/uploads/performance_evaluation/');
            $table->timestamp('date_updated')->useCurrent()->useCurrentOnUpdate();
            $table->datetime('date_created')->nullable();
            $table->integer('user_id_added');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::connection('intra_payroll')->dropIfExists('tbl_performance_evaluation');
    }
}
