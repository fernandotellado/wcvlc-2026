<?php
/**
 * Plugin Name:       Carga especulativa moderada
 * Description:       Modifica la carga especulativa que hace WordPress para que haga la precarga de manera moderada (comienza cuando el visitante pasa el cursor sobre un enlace). Alternativa opcional al plugin oficial de Carga Especulativa.
 * Version:           1.0.0
 * Author:            Fernando Tellado
 * Author URI:        https://ayudawp.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.8
 * Requires PHP:      7.4
 *
 * Uso: sube este archivo a wp-content/mu-plugins/ (crea la carpeta si no existe).
 * No lo uses junto con el plugin de Carga Especulativa: elige uno.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cambia la configuración de la carga especulativa del núcleo.
 *
 * El núcleo pasa null cuando la carga especulativa está deshabilitada (usuarios conectados,
 * enlaces permanentes simples), por lo que en ese caso el valor es devuelto sin cambios.
 *
 * @param array|null $config rray asociativo con las claves 'mode' y 'eagerness', o null.
 * @return array|null
 */
function ayudawp_speculative_loading_moderate( $config ) {
	if ( ! is_array( $config ) ) {
		return $config;
	}

	$config['mode']      = 'prerender';
	$config['eagerness'] = 'moderate';

	return $config;
}
add_filter( 'wp_speculation_rules_configuration', 'ayudawp_speculative_loading_moderate' );
