<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = ['cities','districts', 'wards'];
        $foreignKeyCity = 'city_id';
        $foreignKeyDistrict = 'district_id';
        $foreignKeyWard = 'ward_id';

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbr')->unique()->nullable();
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) use($tableNames, $foreignKeyCity) {
            $table->id();
            $table->foreignId($foreignKeyCity)
                ->constrained($tableNames[0])
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('wards', function (Blueprint $table) use($tableNames, $foreignKeyDistrict){
            $table->id('id');
            $table->foreignId($foreignKeyDistrict)
                ->constrained($tableNames[1])
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('streets', function (Blueprint $table) use($tableNames, $foreignKeyWard){
            $table->id('id');
            $table->foreignId($foreignKeyWard)
                ->constrained($tableNames[2])
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->longText('name');
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
        Schema::dropIfExists('streets');
        Schema::dropIfExists('wards');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');
    }
}
