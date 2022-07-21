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
    public function up()
    {
        Schema::create('turns', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedBigInteger('game_id');
            $table->unsignedBigInteger('player_id');
            $table->enum('location', [1, 2, 3, 4, 5, 6, 7, 8, 9])->nullable();
            $table->enum('type', ['x', 'o'])->nullable();
            $table->timestamps();

            $table->foreign('player_id')
                ->on('users')->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('game_id')
                ->on('games')->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('turns');
    }
};
