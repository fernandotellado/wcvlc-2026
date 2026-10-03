# Optimización web (avanzada) para pobres

Recursos de la charla de Fernando Tellado en **WordCamp Valencia 2026**.

En la charla no salió ni una línea de código en pantalla, y fue a propósito. Todo lo que allí se nombró está aquí, con el mismo orden de la charla: se recorre el bar de fuera hacia dentro, primero la calle (Cloudflare), luego el local (tu hosting), después la cocina (WordPress, `wp-config.php` y `.htaccess`) y, al final, el pinche (los plugins).

Y la norma de la casa, que vale para todo lo que viene: **cada cosa, lo más arriba posible, y una sola vez**. Si algo ya lo hace Cloudflare o tu hosting, no lo repitas con un plugin, que tres cachés encima de otra son tres camareros cobrando la misma mesa.

> Antes de tocar `wp-config.php`, `.htaccess` o la base de datos, haz una copia de seguridad. Siempre.

## Archivos de esta carpeta

| Archivo | Qué es | Diapositiva |
|---|---|---|
| [`cloudflare-regla-cache-html.txt`](cloudflare-regla-cache-html.txt) | Regla de Cloudflare para cachear el HTML sin servir el carrito de uno a otro | 16 y 17 |
| [`wp-config-fragmentos.php`](wp-config-fragmentos.php) | Las líneas para limitar revisiones y apagar el cron por visitas | 25 y 31 |
| [`opciones-autocargadas.sql`](opciones-autocargadas.sql) | Consultas para ver cuánto pesan las opciones autocargadas | 30 |
| [`cache-navegador.htaccess`](cache-navegador.htaccess) | Caché del navegador para Apache y LiteSpeed | 35 |
| [`ayudawp-speculative-loading-moderate.php`](ayudawp-speculative-loading-moderate.php) | Opcional: mu-plugin que adelanta la carga especulativa, si no quieres instalar el plugin oficial | 28 |

---

## 1. La calle: Cloudflare (plan gratuito)

### Caché del HTML · diapositivas 16 y 17

Por defecto Cloudflare guarda tus imágenes, estilos y scripts, pero no la página. Con una regla de caché, que el plan gratuito permite, guarda también el HTML y la visita no llega a tu servidor.

Se crea en **Cloudflare › tu dominio › Almacenamiento en caché › Cache Rules › Crear regla**. La regla completa, paso a paso y con las exclusiones (escritorio, usuarios conectados, carrito, caja y cuenta de WooCommerce) y cómo comprobar que funciona, está en [`cloudflare-regla-cache-html.txt`](cloudflare-regla-cache-html.txt).

Si tienes tienda y no lo tienes claro, no lo hagas y deja la caché de página al hosting. En una web corporativa o un blog, adelante.

### Early Hints · diapositiva 18

Es gratis pero viene apagado. En el panel de Cloudflare está en **Speed › Settings › Content Optimization › Early Hints**. Un interruptor y listo.

### Speed Brain

Hace precarga de la página siguiente, como la carga especulativa de WordPress. En el plan gratuito viene activado de serie y solo funciona con páginas que Cloudflare ya tiene en caché. No hay que tocar nada.

### Bots de IA · diapositiva 19

En **AI Crawl Control**, en el menú lateral del panel de tu dominio, tienes las políticas de bots de IA por tipo (búsqueda, agentes y entrenamiento). Desde el 15 de septiembre de 2026, en los sitios del plan gratuito que no lo hayan configurado, Cloudflare bloquea por defecto entrenamiento y agentes en las páginas con anuncios.

Ojo con bloquear de más: si quieres salir en las respuestas de los buscadores con IA, deja pasar los de búsqueda. Y no, el `robots.txt` no los para; esto sí.

### Auto Minify · diapositiva 20

Ya no existe. Cloudflare lo quitó en agosto de 2024, así que si un tutorial te dice que lo actives, está desactualizado.

---

## 2. El local: tu hosting

### Versión de PHP · diapositiva 22

Se cambia en el panel de tu hosting y es gratis. Sube a la versión más reciente que admitan tu tema y tus plugins, probándolo antes en una copia o en un entorno de pruebas. Qué versión usas ahora te lo dice **Herramientas › Salud del sitio › Información › Servidor**.

### Caché de página del hosting · diapositiva 23

Muchos hostings la traen y está apagada. Si la tienes, actívala y no pongas otra encima. Salud del sitio te dice si detecta caché de página.

### Caché de objetos · diapositiva 24

Si tu hosting ofrece Redis o Memcached, actívalo en su panel y conecta WordPress con el plugin [Redis Object Cache](https://wordpress.org/plugins/redis-cache/) (o el que te indique tu hosting). En un blog con caché de página la mejora es pequeña; donde se nota es en el escritorio, en WooCommerce y con usuarios conectados.

### Cron real · diapositiva 25

El cron de WordPress se dispara con las visitas. Si tu web tiene tareas pesadas, es mejor un cron de verdad, en dos pasos y en este orden:

**Paso 1.** Crea la tarea en el panel de tu hosting (apartado «Cron», «Tareas programadas» o similar). Cada 15 minutos suele bastar:

```
*/15 * * * * wget -q -O /dev/null "https://tudominio.com/wp-cron.php?doing_wp_cron"
```

O, si tu hosting tiene WP-CLI:

```
*/15 * * * * cd /ruta/a/tu/wordpress && wp cron event run --due-now > /dev/null 2>&1
```

**Paso 2.** Solo cuando el paso 1 funcione, apaga el cron por visitas con la línea `DISABLE_WP_CRON` de [`wp-config-fragmentos.php`](wp-config-fragmentos.php).

Para ver qué tareas tienes y si se ejecutan, usa [WP Crontrol](https://wordpress.org/plugins/wp-crontrol/) antes de tocar nada.

---

## 3. La cocina: WordPress, wp-config.php y .htaccess

### Actualizar · diapositiva 27

La optimización más barata que existe. WordPress 6.9 trajo de serie, entre otras cosas, la carga de estilos de bloques solo cuando se usan también en temas clásicos y el cron fuera del camino de la respuesta. Viene activo sin tocar nada.

### Carga especulativa · diapositiva 28

Desde WordPress 6.8 viene de serie para los visitantes (siempre que uses enlaces permanentes bonitos), pero en modo conservador: precarga cuando ya estás pulsando el enlace.

Para adelantarla a cuando pasas el ratón por encima, instala el plugin oficial [Speculative Loading](https://wordpress.org/plugins/speculation-rules/) del equipo de rendimiento de WordPress. Se configura en **Ajustes › Lectura**.

Si prefieres no instalar otro plugin, tienes el mu-plugin [`ayudawp-speculative-loading-moderate.php`](ayudawp-speculative-loading-moderate.php): súbelo a `wp-content/mu-plugins/` y hace lo mismo. Uno u otro, no los dos.

Más precarga es más trabajo para el servidor, así que en un hosting justito quédate en el modo de serie.

### Salud del sitio · diapositiva 29

**Herramientas › Salud del sitio**. Te dice si tienes caché de página, si te falta la de objetos, qué PHP usas y si tus opciones autocargadas pesan demasiado. Antes de comprar nada, pasa la inspección.

### Opciones autocargadas · diapositiva 30

Son ajustes que WordPress carga en cada visita, los uses o no, y muchos plugins desinstalados dejan los suyos ahí. Desde WordPress 6.6, Salud del sitio avisa cuando pasan de unos 800 KB.

- Para ver cuánto pesan y cuáles son: [`opciones-autocargadas.sql`](opciones-autocargadas.sql) (solo consulta, no borra nada).
- Para limpiarlas sin hacerlo a mano: [AAA Option Optimizer](https://wordpress.org/plugins/aaa-option-optimizer/), gratuito.

### Revisiones · diapositiva 31

La línea `WP_POST_REVISIONS` de [`wp-config-fragmentos.php`](wp-config-fragmentos.php) limita las revisiones nuevas a 5. No las quites todas, que una revisión te salva un disgusto.

Las que ya tienes no se borran solas. Con WP-CLI, y después de exportar la base de datos:

```
wp db export copia-antes-de-revisiones.sql
wp post list --post_type=revision --format=count
wp post delete $(wp post list --post_type=revision --format=ids) --force
```

A la velocidad de la portada esto le afecta poco. A la base de datos, a las copias de seguridad y al escritorio, bastante.

### Imágenes · diapositivas 32 y 33

Aquí no hace falta código:

- WordPress ya reduce lo que pasa de 2560 píxeles, genera tamaños, sirve el adecuado a cada pantalla, aplaza la carga de lo que no se ve y prioriza la imagen principal.
- Tu parte es subir la imagen al tamaño al que se va a ver, no la foto de 6.000 píxeles del móvil.
- Si la exportas ya en WebP o AVIF antes de subirla, WordPress genera todos los tamaños en ese formato. Para AVIF, tu servidor tiene que soportarlo; lo ves en **Salud del sitio › Información**, en el apartado de medios.

### Fuentes · diapositiva 34

- **Tema de bloques:** desde WordPress 6.5, la biblioteca de fuentes (**Apariencia › Editor › Estilos › Tipografía**) instala las fuentes de Google en tu propio servidor.
- **Tema clásico:** aquí sí hace falta un plugin que las sirva en local (DietPress, por ejemplo, lo hace).

### Caché del navegador · diapositiva 35

Las líneas para Apache y LiteSpeed están en [`cache-navegador.htaccess`](cache-navegador.htaccess). Antes comprueba que no lo hace ya Cloudflare o tu hosting; en el archivo te explico cómo mirarlo.

---

## 4. El pinche: plugins

Los plugins entran solo para lo que no se ha podido resolver arriba.

| Para qué | Plugin | Diapositiva |
|---|---|---|
| Medir antes de tocar nada: consultas lentas, qué plugin tarda más | [Query Monitor](https://wordpress.org/plugins/query-monitor/) | 37 |
| CSS crítico y JavaScript diferido | [Autoptimize](https://wordpress.org/plugins/autoptimize/) | 40 |
| Todo lo anterior en uno, más poner WordPress a dieta | [DietPress](https://wordpress.org/plugins/wpo-tweaks/) | 41 a 43 |

Sin plugin, solo con sentido común:

- **Scripts de terceros (diapositiva 38).** Chat, píxeles, mapas, vídeos incrustados, banner de cookies… Haz inventario. Lo que no usas, fuera, y lo que sí, que cargue cuando haga falta.
- **Recursos solo donde se usan (diapositiva 39).** El formulario de contacto no tiene que cargar sus estilos y scripts en todas las páginas. DietPress lo resuelve para los plugins más habituales.

Y el aviso de la diapositiva 40: el CSS crítico y el JavaScript diferido son lo que más webs rompe. Activa, mira y prueba antes de irte a casa.

### Sobre DietPress

Es un plugin mío y gratuito, sin cuentas, sin telemetría y sin versión de pago: [DietPress](https://es.wordpress.org/plugins/wpo-tweaks/) · [dietpress.dev](https://dietpress.dev/). El dato de la diapositiva 42 (de 17 a 732 visitas por segundo en el mismo servidor, solo con la caché de página) está medido en una portada y publicado en la ficha del plugin. Tu web dará otra cifra, pero el orden de magnitud es ese.

Si tu hosting o Cloudflare ya cachean la página, no actives también su caché de página. Norma de la casa.

---

## El menú del día · diapositiva 45

| | |
|---|---|
| **Primero** | Cloudflare |
| **Segundo** | Tu hosting, bien usado |
| **Postre** | WordPress al día, con su `wp-config.php` y su `.htaccess` |
| **Pan, bebida y café** | DietPress (o los pinches gratuitos que prefieras) |
| **Precio** | 0,00 €, IVA incluido |

---

## Licencia

El código de esta carpeta se publica bajo licencia GPLv2 o posterior, la misma de WordPress. Úsalo, cámbialo y compártelo.

## Más

- [Ayuda WordPress](https://ayudawp.com): tutoriales y documentación.
- [Servicios de AyudaWP](https://servicios.ayudawp.com): si prefieres que te lo hagamos nosotros.
- [Canal de YouTube](https://www.youtube.com/AyudaWordPressES).
