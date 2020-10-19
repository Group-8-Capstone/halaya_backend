<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngredientsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement(
            "CREATE VIEW view_ingredients AS
            SELECT DISTINCT
                a.id, 
                a.ingredients_name, 
                a.ingredients_need_amount,
                i.ingredients_remaining,
                i.ingredients_status,
                u.used_ingredients_amount
                            
            FROM ingredients_amount AS a
            JOIN ingredients AS i ON a.id = i.ingredients_amount_id
            JOIN used_ingredients AS u ON a.id = u.ingredients_id;" 
        ); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('ingredients_view');
        DB::statement("DROP VIEW view_ingredients"); 
    }
}

