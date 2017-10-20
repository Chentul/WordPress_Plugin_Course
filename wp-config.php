<?php
/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'udemy_wordpress_plugin_course');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'root');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', 'root');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8mb4');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'Qv9]taKq{6FSW=8w::gL&3wtGA_{bzh,lXq1RU^Y|1?E$r..CwsVgg2q >[{6xs$');
define('SECURE_AUTH_KEY', ')j2PP*X$W>dxDj>1NW^7Z(n3)1q%4TFnFDt08Jfrt?>n]FJX^_D$@xzxIszpo*$~');
define('LOGGED_IN_KEY', 'uH)t*Un2QhZwlq30 &_ r>C_Y#(0]OdT<SY4]OJxS[<[5gzU %Bn4;(p[0hDFGC-');
define('NONCE_KEY', '`x_iI?Cf&IYKHp  u?5%_+/R`87j]8BPPvypI$]z;>5?taT^-(8Q<5%]NY2~Z~83');
define('AUTH_SALT', '(Q6c,zO*M4|bII) Ea}IbV/6aMRV#}Z{5{>Oz`isM=%_m9(Ao1~!l*?*|xp~]Y?{');
define('SECURE_AUTH_SALT', '#yyY;4ECV.BbH/=AqZgTuG^,alvREs5XA:(MfGcojF!o(<l5o-Jh=3Xu0LGP{@Cc');
define('LOGGED_IN_SALT', 'h=JkL|r`O4bZn-)n7%>)4:$/7{,9-N:`b%[i?wj>=E!dD@;](g67RRhYDK#hH6Hb');
define('NONCE_SALT', 'P|U!V14AWX/$}QX^H4FMs.5VQyvzB6Y+aW3Kk6Uy]+_MOxD.jnjXK&.3sU_Rk 1;');

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

