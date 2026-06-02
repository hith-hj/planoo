<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Customer;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Category::class);
            $table->string('name');
            $table->text('description');
            $table->boolean('is_active');
            $table->boolean('is_full');
            $table->integer('event_duration');
            $table->integer('capacity');
            $table->integer('rate')->default(0);
            $table->integer('admission_fee')->nullable();
            $table->integer('withdrawal_fee')->nullable();
            $table->tinyInteger('status');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_event', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Event::class);
            $table->foreignIdFor(Customer::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('customer_event');
    }
};
