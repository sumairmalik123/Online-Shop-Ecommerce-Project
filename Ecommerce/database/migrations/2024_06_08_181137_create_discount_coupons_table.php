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
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id();
            //the discount coupon code 
            $table->string('code');
            //the human readale discount coupon code name
            $table->string('name')->nullable();
            //the discount coupon description
            $table->text('description')->nullable();  
            //max users this coupon has
            $table->integer('max_uses')->nullable();  
            //how many times a user can use this coupon
            $table->integer('max_uses_user')->nullable();
            //whether or not the coupon is a percentage or a fixed price
            $table->enum('type',['percent','fixed'])->default('fixed');
            //the amount to discount based on coupon
            $table->double('discount_amount',10,2)  ;
            //the amount to discount based on coupon
            $table->double('min_amount',10,2)->nullable();
            $table->integer('status')->default(1);

             // when the coupon begin
             $table->timestamp('starts_at')->nullable();
            // when the coupon end
            $table->timestamp('expire_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_coupons');
    }
};
