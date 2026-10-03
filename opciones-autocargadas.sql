-- ---------------------------------------------------------------------
-- Opciones autocargadas · Diapositiva 30 · Te recito la carta de vinos entera
-- Charla «Optimización web (avanzada) para pobres» · WordCamp Valencia 2026
--
-- Consultas de solo lectura: miran, no borran nada.
-- Ejecútalas en phpMyAdmin (o similar) desde el panel de tu hosting.
-- Si tu prefijo de tablas no es wp_, cámbialo (lo tienes en wp-config.php,
-- en la variable $table_prefix).
--
-- Desde WordPress 6.6 el campo autoload puede valer 'yes', 'on', 'auto' o
-- 'auto-on' cuando la opción se carga en cada visita; por eso van los cuatro.
-- ---------------------------------------------------------------------

-- 1) Cuánto pesa en total lo que se carga en cada visita.
--    Salud del sitio avisa a partir de unos 800 KB.
SELECT ROUND( SUM( LENGTH( option_value ) ) / 1024 ) AS kb_autocargados
FROM wp_options
WHERE autoload IN ( 'yes', 'on', 'auto', 'auto-on' );

-- 2) Las 20 opciones autocargadas que más pesan.
--    Si ves nombres de plugins que ya desinstalaste, ahí está la basura.
SELECT option_name,
       ROUND( LENGTH( option_value ) / 1024, 1 ) AS kb,
       autoload
FROM wp_options
WHERE autoload IN ( 'yes', 'on', 'auto', 'auto-on' )
ORDER BY LENGTH( option_value ) DESC
LIMIT 20;

-- Para limpiar, mejor no hacerlo a mano con un DELETE: usa el plugin gratuito
-- AAA Option Optimizer (https://wordpress.org/plugins/aaa-option-optimizer/),
-- que te dice qué opciones autocargadas se usan de verdad y cuáles no, y te
-- deja quitarles la autocarga o borrarlas. Haz una copia de la base de datos antes.
