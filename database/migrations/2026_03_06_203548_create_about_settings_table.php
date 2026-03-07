<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Configuración principal de la página About ──────────────
        Schema::create('about_settings', function (Blueprint $table) {
            $table->id();

            // Hero
            $table->string('hero_label')->default('Quiénes Somos');
            $table->string('hero_title')->default('Nuestra Historia');
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_image')->nullable();         // storage path

            // Sobre nosotros
            $table->string('about_title')->default('Sobre Nosotros');
            $table->text('about_description')->nullable();
            $table->text('about_description_2')->nullable();
            $table->string('about_image')->nullable();

            // Misión / Visión / Valores
            $table->string('mission_title')->default('Nuestra Misión');
            $table->text('mission_text')->nullable();
            $table->string('vision_title')->default('Nuestra Visión');
            $table->text('vision_text')->nullable();
            $table->string('values_title')->default('Nuestros Valores');
            $table->text('values_text')->nullable();

            // Estadísticas (JSON: [{icon, value, label}])
            $table->json('stats')->nullable();

            // Línea de tiempo / Historia (JSON: [{year, title, description}])
            $table->json('timeline')->nullable();

            // ¿Por qué elegirnos? (JSON: [{icon, title, description}])
            $table->json('why_us')->nullable();

            // CTA final
            $table->string('cta_title')->default('¿Listo para empezar?');
            $table->text('cta_description')->nullable();
            $table->string('cta_btn_text')->default('Ver productos');
            $table->string('cta_btn_url')->default('/');
            $table->string('cta_btn2_text')->nullable();
            $table->string('cta_btn2_url')->nullable();

            $table->timestamps();
        });

        // ── Equipo ───────────────────────────────────────────────────
        Schema::create('about_team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();             // storage path
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('email')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_team_members');
        Schema::dropIfExists('about_settings');
    }
};