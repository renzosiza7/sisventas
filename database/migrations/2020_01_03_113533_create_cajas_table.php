<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('monto_inicial', 11, 2);
            $table->decimal('total_ventas', 11, 2)->nullable();
            $table->decimal('total_servicios', 11, 2)->nullable();
            $table->decimal('total_mov_entrada', 11, 2)->nullable();
            $table->decimal('total_mov_salida', 11, 2)->nullable();
            $table->decimal('total_gastos', 11, 2)->nullable();
            $table->decimal('saldo_sistema', 11, 2)->nullable();
            $table->decimal('saldo_caja', 11, 2)->nullable();
            $table->decimal('diferencia', 11, 2)->nullable();
            $table->enum('estado', ['Abierto', 'Cerrado']);                        
            $table->integer('idcajero');            
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
        Schema::dropIfExists('cajas');
    }
}
