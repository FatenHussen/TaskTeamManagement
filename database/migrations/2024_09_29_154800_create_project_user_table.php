<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectUserTable extends Migration
{
    public function up()
    {        Schema::create('project_user', function (Blueprint $table) {

        $table->id();
        $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->enum('role', ['manager', 'developer', 'tester']);
        $table->integer('contribution_hours')->default(0);
        $table->timestamp('last_activity')->nullable();
        $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_user');
    }
}
