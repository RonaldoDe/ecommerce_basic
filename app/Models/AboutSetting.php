<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSetting extends Model
{
    protected $table = 'about_settings';

    protected $fillable = [
        'hero_label',
        'hero_title',
        'hero_subtitle',
        'hero_image',

        'about_title',
        'about_description',
        'about_description_2',
        'about_image',

        'mission_title',
        'mission_text',
        'vision_title',
        'vision_text',
        'values_title',
        'values_text',

        'stats',
        'timeline',
        'why_us',

        'cta_title',
        'cta_description',
        'cta_btn_text',
        'cta_btn_url',
        'cta_btn2_text',
        'cta_btn2_url',
    ];

    protected $casts = [
        'stats'    => 'array',
        'timeline' => 'array',
        'why_us'   => 'array',
    ];

    /**
     * Obtener el registro único (singleton).
     * Si no existe, crea uno con valores por defecto.
     */
    public static function getSingleton(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'hero_label'    => 'Quiénes Somos',
                'hero_title'    => 'Nuestra Historia',
                'hero_subtitle' => 'Conoce al equipo detrás de la tienda y nuestra pasión por llevarte los mejores productos.',
                'about_title'   => 'Sobre Nosotros',
                'about_description' => 'Somos una tienda comprometida con la calidad y la satisfacción del cliente.',
                'mission_title' => 'Nuestra Misión',
                'mission_text'  => 'Conectar a las personas con productos de calidad a precios justos.',
                'vision_title'  => 'Nuestra Visión',
                'vision_text'   => 'Ser la tienda de referencia en el país, reconocida por su servicio y calidad.',
                'values_title'  => 'Nuestros Valores',
                'values_text'   => 'Honestidad, compromiso, innovación y servicio al cliente.',
                'stats' => [
                    ['icon' => 'bi-people-fill',       'value' => '10,000+', 'label' => 'Clientes felices'],
                    ['icon' => 'bi-box-seam',          'value' => '5,000+',  'label' => 'Productos disponibles'],
                    ['icon' => 'bi-truck',             'value' => '50,000+', 'label' => 'Pedidos entregados'],
                    ['icon' => 'bi-star-fill',         'value' => '4.9',     'label' => 'Calificación promedio'],
                ],
                'why_us' => [
                    ['icon' => 'bi-shield-check',   'title' => 'Calidad garantizada',   'description' => 'Todos nuestros productos pasan por rigurosos controles de calidad.'],
                    ['icon' => 'bi-truck',          'title' => 'Envío rápido',          'description' => 'Entregamos en todo el país en tiempo récord.'],
                    ['icon' => 'bi-headset',        'title' => 'Soporte 24/7',          'description' => 'Nuestro equipo está disponible para ayudarte en cualquier momento.'],
                    ['icon' => 'bi-arrow-return-left','title' => 'Devoluciones fáciles','description' => 'Política de devoluciones sin complicaciones en 30 días.'],
                ],
                'cta_title'       => '¿Listo para explorar?',
                'cta_description' => 'Descubre nuestra selección de productos y encuentra exactamente lo que buscas.',
                'cta_btn_text'    => 'Ver productos',
                'cta_btn_url'     => '/',
                'cta_btn2_text'   => 'Contáctanos',
                'cta_btn2_url'    => '/contact',
            ]
        );
    }
}