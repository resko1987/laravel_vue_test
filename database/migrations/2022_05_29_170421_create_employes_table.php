<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('employes')) {
            Schema::create('employes', function (Blueprint $table) {
                $table->id();
                $table->string('fio');
                $table->foreignId('department_id')->constrained('departments')->nullable();
                $table->foreignId('job_id')->constrained('jobs');
                $table->foreignId('parent_id')->nullable();
                $table->index('department_id');
                $table->index('job_id');
                $table->index('parent_id');
//                $table->foreign('department_id')->references('id')->on('departments');
//                $table->foreign('job_id')->references('id')->on('jobs');
//                $table->foreign('director_id')->references('id')->on('directors');
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {


        Schema::dropIfExists('employes');
    }
};
