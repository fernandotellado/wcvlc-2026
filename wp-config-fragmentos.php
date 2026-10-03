<?php
/**
 * Fragmentos para wp-config.php
 * Charla «Optimización web (avanzada) para pobres» · WordCamp Valencia 2026
 *
 * Esto NO es un archivo para subir tal cual. Copia las líneas que necesites
 * y pégalas en tu wp-config.php, justo encima de la línea que dice
 * «/* That's all, stop editing! *\/» (o «¡Eso es todo, deja de editar!»
 * si tu instalación está en español).
 *
 * Antes de tocar wp-config.php, haz una copia del archivo.
 */

/*
 * Diapositiva 31 · La cámara llena de táperes
 * Limita las revisiones que WordPress guarda de cada entrada o página.
 * Con 5 tienes margen para deshacer un desastre sin llenar la base de datos.
 * Solo afecta a las revisiones nuevas; las que ya existen no se borran.
 */
define( 'WP_POST_REVISIONS', 5 );

/*
 * Diapositiva 25 · El inventario, con el bar cerrado
 * Apaga el cron que WordPress dispara con las visitas.
 *
 * ¡OJO! Activa esta línea SOLO después de crear un cron real en el panel
 * de tu hosting (tienes la orden en el README). Si la activas sin cron real,
 * dejarán de ejecutarse las tareas programadas: entradas programadas,
 * copias de seguridad, actualizaciones automáticas, correos, etc.
 */
define( 'DISABLE_WP_CRON', true );
