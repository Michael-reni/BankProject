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
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('account_number')->default(DB::raw('gen_random_uuid()')); // for sake of simplicity this field uuid will represent account number. 
            $table->text('name');
            $table->text('surname');
            $table->decimal('balance', $precision = 8, $scale = 2);  
            $table->timestamps();    
        });
        DB::statement('ALTER TABLE accounts ADD CONSTRAINT non_negative_balance CHECK (balance >= 0)'); // sadly constraints are not supported so we have to add it manually
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
