<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('file_name')->nullable();
            $table->string('alt')->nullable();
            $table->string('by_user')->nullable();
            $table->string('description')->nullable();
            $table->string('original_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_size')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('file_ext')->nullable();
            $table->tinyInteger('private')->default(0);
            $table->timestamp('last_modified')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
