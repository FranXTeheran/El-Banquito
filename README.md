# El Banquito 🏦

Sistema de ahorro grupal familiar desarrollado con PHP, MySQL y Tailwind CSS.

## ¿Qué es El Banquito?
El Banquito es una plataforma web que permite a grupos familiares gestionar sus ahorros semanales de forma organizada. Cada miembro registra su aporte semanal y el administrador lo aprueba o rechaza, enviando una notificación SMS automática.

## Funcionalidades
- Registro y login con roles (admin/miembro)
- El administrador crea y gestiona los miembros
- Aportes semanales con restricción de un aporte por semana
- Aprobación o rechazo de aportes por el administrador
- Notificaciones SMS automáticas via Twilio al aprobar/rechazar
- Dashboard personal con ahorro acumulado
- Visualización del fondo grupal "La Vaquita"
- Historial de aportes por miembro
- Cambio de contraseña temporal obligatorio
- Diseño responsive con Tailwind CSS

## Tecnologías
![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)
![Tailwind](https://img.shields.io/badge/Tailwind-CSS-38B2AC?logo=tailwind-css)
![Twilio](https://img.shields.io/badge/Twilio-SMS-F22F46?logo=twilio)

## Instalación
1. Clona el repositorio
2. Copia config/database.example.php a config/database.php y configura tus credenciales
3. Copia config/twilio.example.php a config/twilio.php y configura tus credenciales de Twilio
4. Importa la base de datos con el script SQL
5. Corre el servidor con: php -S localhost:8000

## Estructura del proyecto
- views/ — Vistas PHP
- process/ — Lógica de procesamiento
- config/ — Configuración de BD y Twilio
- img/ — Imágenes y favicon
