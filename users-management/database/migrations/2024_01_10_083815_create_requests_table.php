<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('submit_username');            
            $table->string('identity', 9)->unique();          
            $table->string('first_name');          
            $table->string('last_name');          
            $table->string('phone', 10);          
            $table->string('email')->nullable();          
            $table->string('unit');      
            $table->string('sub');    
            $table->enum('authentication_type', ['Microsoft auth', 'phone call']);    
            $table->enum('service_type', ['regular', 'reserve', 'consultant', 'extenal']);
            $table->integer('validity')->default(365);    
            $table->enum('status', ['new', 'approve', 'denied'])->default('new'); 
            $table->string('description');    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
