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
        Schema::dropIfExists('calificacions');

        Schema::create('calificacions', function (Blueprint $table) {
            $table->id();
            $table->string('TipoPrueba')->nullable();
            $table->text('prompUsado')->nullable();
            $table->float('valor')->nullable();
            $table->float('valorIA')->nullable();

            $table->float('valor_Resumen')->nullable();
            $table->float('valor_Introduccion')->nullable();
            $table->float('valor_Discusion')->nullable();
            $table->float('valor_Conclusiones')->nullable();
            $table->float('valor_Metodologia')->nullable();

            $table->integer('tokens')->nullable();

            $table->bigInteger('libre_id')->nullable();
            $table->string('Modelo_de_libre')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();//quien la realizo el articulo u otro
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); //restrict | set null

            $table->unsignedBigInteger('QuienCalifico')->nullable();
            $table->foreign('QuienCalifico')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); //restrict | set null
            $table->unsignedBigInteger('UniCarreraMateria')->nullable();
            $table->unsignedBigInteger('UniCarreraMateriaID')->nullable();

            $table->timestamps();
        });

        Schema::dropIfExists('articulos');

        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('nick');
            $table->string('version')->default(1);

            $table->text('Portada')->nullable();
            $table->text('Resumen')->nullable();
            $table->integer('Resumen_integer')->default(0);
            $table->text('Resumen_ia')->nullable();
            $table->text('Resumen_final')->nullable();
            $table->text('Resumen_correcciones')->nullable();
            $table->text('Palabras_Clave', "32000")->nullable();
            $table->text('Introduccion', "32000")->nullable();
            $table->integer('Introduccion_integer')->default(0);
            $table->text('Introduccion_ia', "32000")->nullable();
            $table->text('Introduccion_final', "32000")->nullable();
            $table->text('Introduccion_correcciones', "32000")->nullable();
            $table->text('Revision_de_la_Literatura', "32000")->nullable();
            $table->text('Metodologia', "32000")->nullable();
            $table->text('Metodologia_ia', "32000")->nullable();
            $table->text('Metodologia_final', "32000")->nullable();
            $table->text('Metodologia_correcciones', "32000")->nullable();
            $table->integer('Metodologia_integer')->default(1);
            $table->text('Resultados', "32000")->nullable();
            $table->text('Discusion', "32000")->nullable();
            $table->integer('Discusion_integer')->default(0);
            $table->text('Discusion_ia', "32000")->nullable();
            $table->text('Discusion_final', "32000")->nullable();
            $table->text('Discusion_correcciones', "32000")->nullable();
            $table->text('Conclusiones', "32000")->nullable();
            $table->integer('Conclusiones_integer')->default(0);
            $table->text('Conclusiones_ia', "32000")->nullable();
            $table->text('Conclusiones_final', "32000")->nullable();
            $table->text('Conclusiones_correcciones', "32000")->nullable();
            $table->text('Agradecimientos', "32000")->nullable();
            $table->text('Referencias', "32000")->nullable();
            $table->text('Anexos_o_Apendices', "32000")->nullable();

            $table->float('Resumen_critica')->nullable();
            $table->float('Introduccion_critica')->nullable();
            $table->float('Discusion_critica')->nullable();
            $table->float('Conclusiones_critica')->nullable();
            $table->float('Metodologia_critica')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); //restrict | set null

            $table->unsignedBigInteger('universidad_id');
            $table->foreign('universidad_id')
                ->references('id')
                ->on('universidads')
                ->onDelete('restrict'); //restrict | set null

            $table->unsignedBigInteger('carrera_id');
            $table->foreign('carrera_id')
                ->references('id')
                ->on('carreras')
                ->onDelete('restrict');

            $table->unsignedBigInteger('materia_id');
            $table->foreign('materia_id')
                ->references('id')
                ->on('materias')
                ->onDelete('restrict');

            $table->unsignedBigInteger('libre_id')->nullable();
            $table->string('Modelo_de_libre')->nullable();
            $table->string('tipo')->nullable(); //saber si es resumen
            $table->text('Critica_string')->nullable();
            $table->text('Referencias_ai')->nullable(); //sugerencias de la ia
            $table->text('links_ai')->nullable(); //sugerencias de la ia
            $table->timestamps();
        });

        Schema::dropIfExists('archivos');

        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('NombreOriginal');
            $table->integer('peso');
            $table->string('type');

            $table->Text('Resumen1')->nullable();
            $table->Text('Resumen2')->nullable();
            $table->Text('Resumen3')->nullable();
            $table->Text('Resumen4')->nullable();
            //scispace
            $table->Text('resumen_2_lineas')->nullable();
            $table->Text('aportes')->nullable();
            $table->Text('articulosRelacionados')->nullable();
            $table->Text('implicacionPracticas')->nullable();
            $table->string('StringcampoAbierto1')->nullable();
            $table->Text('campoAbierto1')->nullable();
            $table->string('StringcampoAbierto2')->nullable();
            $table->Text('campoAbierto2')->nullable();

            $table->unsignedBigInteger('materia_id')->nullable();
            $table->foreign('materia_id')
                ->references('id')
                ->on('materias')
                ->onDelete('restrict');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            // ->onDelete('set null');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
