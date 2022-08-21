<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('account_number')->references('account_number')->on('accounts');
            //$table->integer('operetaion_type')->references('id')->on('operetaion_types');
            $table->text('operation_type'); // to do make seeder
            $table->decimal('amount', $precision = 8, $scale = 2);    
            $table->dateTime('created_at');
            
        });
        DB::statement('ALTER TABLE accounts_history ADD CONSTRAINT non_negative_amount CHECK (amount >= 0)'); // sadly constraints are not supported so we have to add it manually
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts_history');
    }
};
