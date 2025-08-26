-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 22-08-2025 a las 15:46:51
-- Versión del servidor: 8.0.30
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `exam_system`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `options`
--

CREATE TABLE `options` (
  `id` int UNSIGNED NOT NULL,
  `question_id` int UNSIGNED NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, 'El primero devuelve una lista y el segundo un solo elemento', 0),
(2, 1, 'El primero devuelve un solo elemento y el segundo una lista', 1),
(3, 1, 'Ambos devuelven siempre listas', 0),
(4, 1, 'Ambos devuelven siempre un único elemento', 0),
(5, 2, 'Implicit Wait', 0),
(6, 2, 'Explicit Wait', 1),
(7, 2, 'Thread.sleep', 0),
(8, 2, 'Hardcoding', 0),
(9, 4, 'Scrapy', 1),
(10, 4, 'BeautifulSoup', 1),
(11, 4, 'Pandas', 0),
(12, 4, 'TensorFlow', 0),
(13, 5, 'API que expone solo métodos GET', 0),
(14, 5, 'API que sigue principios de arquitectura REST, con recursos y métodos HTTP adecuados', 1),
(15, 5, 'API que solo funciona con bases de datos relacionales', 0),
(16, 5, 'API que se ejecuta únicamente en contenedores Docker', 0),
(17, 7, 'docker build', 0),
(18, 7, 'docker run -it', 0),
(19, 7, 'docker run -d', 1),
(20, 7, 'docker-compose up', 0),
(21, 8, 'Jenkins', 0),
(22, 8, 'GitHub Actions', 0),
(23, 8, 'Kubernetes', 0),
(24, 8, 'Photoshop', 1),
(25, 10, 'Cierra la pestaña actual, pero deja el navegador abierto', 0),
(26, 10, 'Cierra todo el navegador y finaliza el proceso', 1),
(27, 10, 'Reinicia la sesión del driver', 0),
(28, 10, 'Recarga la página en curso', 0),
(29, 11, 'Try/Except', 0),
(30, 11, 'Context Manager (with ... as ...)', 1),
(31, 11, 'Global variables', 0),
(32, 11, 'Import dinámico', 0),
(33, 12, 'Nombres de variables descriptivos', 1),
(34, 12, 'Métodos cortos y claros', 1),
(35, 12, 'Comentarios excesivos en cada línea', 0),
(36, 12, 'Uso de estructuras anidadas complejas', 0),
(37, 14, 'PyTest', 0),
(38, 14, 'unittest', 1),
(39, 14, 'JUnit', 0),
(40, 14, 'Mocha', 0),
(41, 15, 'Una función que modifica variables globales', 0),
(42, 15, 'Una función que siempre retorna el mismo resultado con los mismos argumentos y no tiene efectos secundarios', 1),
(43, 15, 'Una función que imprime datos en consola', 0),
(44, 15, 'Una función que usa librerías externas', 0),
(45, 17, 'git branch -b', 0),
(46, 17, 'git checkout -b', 1),
(47, 17, 'git switch', 0),
(48, 17, 'git commit -b', 0),
(49, 18, 'Pruebas unitarias', 1),
(50, 18, 'Pruebas de integración', 1),
(51, 18, 'Pruebas manuales en explorador', 0),
(52, 18, 'Pruebas funcionales', 1),
(53, 20, 'Marco de trabajo basado en tableros visuales para gestionar flujo de trabajo', 1),
(54, 20, 'Metodología enfocada en grandes entregas mensuales', 0),
(55, 20, 'Práctica exclusiva de documentación técnica', 0),
(56, 20, 'Proceso para generar imágenes de contenedores', 0),
(57, 21, 'Un sistema operativo', 0),
(58, 21, 'Una herramienta de virtualización', 0),
(59, 21, 'Una plataforma para desarrollar, enviar y ejecutar aplicaciones en contenedores', 1),
(60, 21, 'Un lenguaje de programación', 0),
(61, 22, 'Almacenar datos', 0),
(62, 22, 'Ejecutar aplicaciones de manera aislada', 1),
(63, 22, 'Proteger el sistema operativo', 0),
(64, 22, 'Crear redes', 0),
(65, 23, 'docker run', 0),
(66, 23, 'docker build', 1),
(67, 23, 'docker create', 0),
(68, 23, 'docker image', 0),
(69, 24, 'Dockerfile', 1),
(70, 24, 'docker-compose.yml', 0),
(71, 24, 'config.json', 0),
(72, 24, 'image.txt', 0),
(73, 25, 'docker start', 0),
(74, 25, 'docker run', 1),
(75, 25, 'docker launch', 0),
(76, 25, 'docker execute', 0),
(77, 26, 'Un sistema de archivos', 0),
(78, 26, 'Un registro de imágenes Docker', 1),
(79, 26, 'Un entorno de desarrollo', 0),
(80, 26, 'Un tipo de contenedor', 0),
(81, 27, 'Los contenedores son más pesados', 0),
(82, 27, 'Las máquinas virtuales comparten el kernel del sistema operativo', 0),
(83, 27, 'Los contenedores son más ligeros y comparten el kernel del sistema operativo', 1),
(84, 27, 'No hay diferencia', 0),
(85, 28, 'docker ps', 1),
(86, 28, 'docker list', 0),
(87, 28, 'docker show', 0),
(88, 28, 'docker containers', 0),
(89, 29, 'Un tipo de imagen', 0),
(90, 29, 'Un método para almacenar datos persistentes', 1),
(91, 29, 'Un contenedor en ejecución', 0),
(92, 29, 'Un comando de Docker', 0),
(93, 30, 'Dockerfile', 0),
(94, 30, 'docker-compose.yml', 1),
(95, 30, 'config.yaml', 0),
(96, 30, 'app.json', 0),
(97, 31, 'docker stop', 1),
(98, 31, 'docker pause', 0),
(99, 31, 'docker halt', 0),
(100, 31, 'docker end', 0),
(101, 32, 'Un contenedor en ejecución', 0),
(102, 32, 'Un archivo que contiene el sistema operativo', 0),
(103, 32, 'Un paquete liviano y ejecutable que incluye todo lo necesario para ejecutar una aplicación', 1),
(104, 32, 'Un tipo de red', 0),
(105, 33, 'docker remove', 0),
(106, 33, 'docker delete', 0),
(107, 33, 'docker rm', 1),
(108, 33, 'docker clean', 0),
(109, 34, 'Un archivo de configuración para redes', 0),
(110, 34, 'Un script que contiene instrucciones para construir una imagen Docker', 1),
(111, 34, 'Un contenedor en ejecución', 0),
(112, 34, 'Un registro de imágenes', 0),
(113, 35, 'Son más lentos que las máquinas virtuales', 0),
(114, 35, 'Pueden ejecutarse en cualquier sistema operativo', 0),
(115, 35, 'Son efímeros y pueden ser creados y destruidos rápidamente', 1),
(116, 35, 'No pueden compartir recursos del sistema', 0),
(117, 36, 'docker logs', 1),
(118, 36, 'docker view', 0),
(119, 36, 'docker show', 0),
(120, 36, 'docker output', 0),
(121, 37, 'Un sistema de gestión de bases de datos', 0),
(122, 37, 'Un sistema de control de versiones', 1),
(123, 37, 'Un lenguaje de programación', 0),
(124, 37, 'Un sistema operativo', 0),
(125, 38, 'git start', 0),
(126, 38, 'git init', 1),
(127, 38, 'git create', 0),
(128, 38, 'git new', 0),
(129, 39, 'git add', 1),
(130, 39, 'git commit', 0),
(131, 39, 'git push', 0),
(132, 39, 'git stage', 0),
(133, 40, 'Guardar cambios en el repositorio local', 1),
(134, 40, 'Sincronizar cambios con el repositorio remoto', 0),
(135, 40, 'Crear una nueva rama', 0),
(136, 40, 'Eliminar archivos del repositorio', 0),
(137, 41, 'git log', 1),
(138, 41, 'git history', 0),
(139, 41, 'git show', 0),
(140, 41, 'git status', 0),
(141, 42, 'Crea un nuevo repositorio vacío', 0),
(142, 42, 'Copia un repositorio existente a tu máquina local', 1),
(143, 42, 'Sincroniza cambios con el repositorio remoto', 0),
(144, 42, 'Elimina un repositorio', 0),
(145, 43, 'Es la rama principal donde se desarrollan nuevas características', 1),
(146, 43, 'Es una rama de prueba', 0),
(147, 43, 'Es una rama de respaldo', 0),
(148, 43, 'No tiene ninguna función específica', 0),
(149, 44, 'git switch', 1),
(150, 44, 'git change', 0),
(151, 44, 'git move', 0),
(152, 44, 'git branch', 0),
(153, 45, 'Enviar cambios al repositorio remoto', 1),
(154, 45, 'Descargar cambios del repositorio remoto', 0),
(155, 45, 'Crear una nueva rama', 0),
(156, 45, 'Eliminar archivos del repositorio', 0),
(157, 46, 'git merge', 1),
(158, 46, 'git combine', 0),
(159, 46, 'git join', 0),
(160, 46, 'git integrate', 0),
(161, 47, 'Cuando dos ramas tienen cambios incompatibles', 1),
(162, 47, 'Cuando no hay cambios en las ramas', 0),
(163, 47, 'Cuando se eliminan archivos', 0),
(164, 47, 'Cuando se crea una nueva rama', 0),
(165, 48, 'git remove', 0),
(166, 48, 'git delete', 0),
(167, 48, 'git branch -d', 1),
(168, 48, 'git branch -r', 0),
(169, 49, 'Una copia de un repositorio que permite realizar cambios sin afectar el original', 1),
(170, 49, 'Un comando para fusionar ramas', 0),
(171, 49, 'Un tipo de conflicto', 0),
(172, 49, 'Un sistema de control de versiones', 0),
(173, 50, 'git status', 1),
(174, 50, 'git check', 0),
(175, 50, 'git info', 0),
(176, 50, 'git files', 0),
(177, 51, 'Un cambio en el código que se guarda en el repositorio', 1),
(178, 51, 'Un archivo que se agrega al repositorio', 0),
(179, 51, 'Una rama en el repositorio', 0),
(180, 51, 'Un conflicto de fusión', 0),
(181, 52, 'git undo', 0),
(182, 52, 'git revert', 1),
(183, 52, 'git rollback', 0),
(184, 52, 'git reset', 0),
(185, 53, 'Un sistema operativo', 1),
(186, 53, 'Un software de edición de imágenes', 0),
(187, 53, 'Un navegador web', 0),
(188, 53, 'Un tipo de hardware', 0),
(189, 54, 'Shell', 0),
(190, 54, 'Kernel', 1),
(191, 54, 'GUI', 0),
(192, 54, 'Daemon', 0),
(193, 55, 'ls', 1),
(194, 55, 'dir', 0),
(195, 55, 'list', 0),
(196, 55, 'show', 0),
(197, 56, 'RPM', 0),
(198, 56, 'APT', 1),
(199, 56, 'YUM', 0),
(200, 56, 'Zypper', 0),
(201, 57, '/etc/passwd', 1),
(202, 57, '/etc/users', 0),
(203, 57, '/usr/passwd', 0),
(204, 57, '/var/users', 0),
(205, 58, 'switch', 0),
(206, 58, 'change', 0),
(207, 58, 'su', 1),
(208, 58, 'user', 0),
(209, 59, '.sh', 1),
(210, 59, '.exe', 0),
(211, 59, '.bat', 0),
(212, 59, '.cmd', 0),
(213, 60, 'cp', 1),
(214, 60, 'copy', 0),
(215, 60, 'mv', 0),
(216, 60, 'clone', 0),
(217, 61, 'Cambiar el propietario', 0),
(218, 61, 'Cambiar el modo de acceso', 1),
(219, 61, 'Cambiar el directorio', 0),
(220, 61, 'Cambiar el nombre', 0),
(221, 62, 'Buscar texto en archivos', 1),
(222, 62, 'Copiar archivos', 0),
(223, 62, 'Mover archivos', 0),
(224, 62, 'Eliminar archivos', 0),
(225, 63, 'cat', 1),
(226, 63, 'view', 0),
(227, 63, 'open', 0),
(228, 63, 'show', 0),
(229, 64, 'Un tipo de archivo', 0),
(230, 64, 'Un proceso en segundo plano', 1),
(231, 64, 'Un usuario especial', 0),
(232, 64, 'Un comando de red', 0),
(233, 65, 'rm', 1),
(234, 65, 'del', 0),
(235, 65, 'erase', 0),
(236, 65, 'remove', 0),
(237, 66, '/etc/network/interfaces', 1),
(238, 66, '/etc/netconfig', 0),
(239, 66, '/usr/network.conf', 0),
(240, 66, '/var/network', 0),
(241, 67, 'df', 1),
(242, 67, 'du', 0),
(243, 67, 'diskusage', 0),
(244, 67, 'space', 0),
(245, 68, 'Un tipo de software', 0),
(246, 68, 'Un método de organización de datos', 1),
(247, 68, 'Un hardware', 0),
(248, 68, 'Un comando', 0),
(249, 69, 'find', 1),
(250, 69, 'search', 0),
(251, 69, 'locate', 0),
(252, 69, 'grep', 0),
(253, 70, 'Super user do', 1),
(254, 70, 'Simple user do', 0),
(255, 70, 'Secure user do', 0),
(256, 70, 'System user do', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `questions`
--

CREATE TABLE `questions` (
  `id` int UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `type` enum('opcion_multiple','abierta') DEFAULT 'opcion_multiple',
  `block` varchar(50) DEFAULT 'General',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `questions`
--

INSERT INTO `questions` (`id`, `question_text`, `type`, `block`, `created_at`) VALUES
(1, 'En Selenium, ¿qué diferencia hay entre find_element_by_xpath() y find_elements_by_xpath()?', 'opcion_multiple', 'Bloque 1', '2025-08-22 13:37:31'),
(2, '¿Cuál de estas opciones es una buena práctica al usar Selenium para esperar la carga de elementos dinámicos en lugar de time.sleep()?', 'opcion_multiple', 'Bloque 1', '2025-08-22 13:37:31'),
(3, 'Explica en qué casos usarías BeautifulSoup junto con Selenium.', 'abierta', 'Bloque 1', '2025-08-22 13:37:31'),
(4, '¿Cuáles de las siguientes son librerías o frameworks en Python útiles para web scraping?', 'opcion_multiple', 'Bloque 1', '2025-08-22 13:37:31'),
(5, 'En el contexto de APIs, ¿qué significa RESTful?', 'opcion_multiple', 'Bloque 1', '2025-08-22 13:37:31'),
(6, '¿Cuál sería tu estrategia para almacenar los datos obtenidos con Selenium en una base de datos PostgreSQL?', 'abierta', 'Bloque 1', '2025-08-22 13:37:31'),
(7, 'En Docker, ¿qué comando se usa para ejecutar un contenedor en segundo plano?', 'opcion_multiple', 'Bloque 1', '2025-08-22 13:37:31'),
(8, 'En un flujo CI/CD, ¿qué herramienta de las siguientes NO está orientada a automatización de despliegues?', 'opcion_multiple', 'Bloque 1', '2025-08-22 13:37:31'),
(9, '¿Cómo expondrías en FastAPI un endpoint que devuelva todas las citas almacenadas en la base de datos?', 'abierta', 'Bloque 1', '2025-08-22 13:37:31'),
(10, '¿Qué significa la instrucción driver.quit() en Selenium?', 'opcion_multiple', 'Bloque 1', '2025-08-22 13:37:31'),
(11, '¿Cuál de las siguientes es la forma recomendada en Python para manejar recursos como archivos y conexiones?', 'opcion_multiple', 'Bloque 2', '2025-08-22 13:37:31'),
(12, '¿Qué principios están asociados con código limpio?', 'opcion_multiple', 'Bloque 2', '2025-08-22 13:37:31'),
(13, '¿Qué es PEP8 y por qué es importante en proyectos Python?', 'abierta', 'Bloque 2', '2025-08-22 13:37:31'),
(14, 'En pruebas unitarias con Python, ¿qué librería estándar se utiliza normalmente?', 'opcion_multiple', 'Bloque 2', '2025-08-22 13:37:31'),
(15, '¿Qué es una función pura en términos de buenas prácticas?', 'opcion_multiple', 'Bloque 2', '2025-08-22 13:37:31'),
(16, 'Explica qué significa la principio de responsabilidad única (SRP) en el desarrollo de software.', 'abierta', 'Bloque 2', '2025-08-22 13:37:31'),
(17, 'En Git, ¿qué comando se usa para crear una nueva rama y cambiarse a ella en un solo paso?', 'opcion_multiple', 'Bloque 2', '2025-08-22 13:37:31'),
(18, '¿Cuáles son ejemplos de pruebas automatizadas?', 'opcion_multiple', 'Bloque 2', '2025-08-22 13:37:31'),
(19, '¿Cuál es la diferencia entre try/except y try/finally en Python?', 'abierta', 'Bloque 2', '2025-08-22 13:37:31'),
(20, 'En metodologías ágiles, ¿qué significa Kanban?', 'opcion_multiple', 'Bloque 2', '2025-08-22 13:37:31'),
(21, '¿Qué es Docker?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(22, '¿Cuál es la función principal de un contenedor en Docker?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(23, '¿Qué comando se utiliza para crear una nueva imagen en Docker?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(24, '¿Cuál de los siguientes archivos se utiliza para definir la configuración de una imagen Docker?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(25, '¿Qué comando se utiliza para iniciar un contenedor en Docker?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(26, '¿Qué es Docker Hub?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(27, '¿Cuál es la diferencia principal entre un contenedor y una máquina virtual?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(28, '¿Qué comando se utiliza para listar todos los contenedores en ejecución?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(29, '¿Qué es un volumen en Docker?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(30, '¿Qué archivo se utiliza para definir y ejecutar aplicaciones multi-contenedor en Docker?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(31, '¿Cuál de los siguientes comandos se utiliza para detener un contenedor en ejecución?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(32, '¿Qué es un \"Docker Image\"?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(33, '¿Qué comando se utiliza para eliminar un contenedor detenido?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(34, '¿Qué es un \"Dockerfile\"?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(35, '¿Cuál de las siguientes afirmaciones es verdadera sobre los contenedores Docker?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(36, '¿Qué comando se utiliza para ver los logs de un contenedor?', 'opcion_multiple', 'General', '2025-08-22 14:06:17'),
(37, '¿Qué es Git?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(38, '¿Cuál es el comando para inicializar un nuevo repositorio Git?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(39, '¿Qué comando se utiliza para agregar cambios al área de preparación (staging area)?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(40, '¿Cuál es el propósito del comando git commit?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(41, '¿Qué comando se utiliza para ver el historial de commits?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(42, '¿Qué hace el comando git clone?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(43, '¿Cuál es la función de la rama master en Git?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(44, '¿Qué comando se utiliza para cambiar de rama en Git?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(45, '¿Qué significa el comando git push?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(46, '¿Qué comando se utiliza para fusionar ramas en Git?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(47, '¿Qué es un conflicto de fusión en Git?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(48, '¿Qué comando se utiliza para eliminar una rama en Git?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(49, '¿Qué es un \"fork\" en Git?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(50, '¿Qué comando se utiliza para ver el estado de los archivos en el repositorio?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(51, '¿Qué es un \"commit\" en Git?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(52, '¿Qué comando se utiliza para revertir un commit?', 'opcion_multiple', 'General', '2025-08-22 14:08:23'),
(53, '¿Qué es Linux?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(54, '¿Cuál es el núcleo de Linux?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(55, '¿Qué comando se utiliza para listar archivos en un directorio?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(56, '¿Cuál de los siguientes es un sistema de gestión de paquetes en Debian?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(57, '¿Qué archivo contiene la información de los usuarios en Linux?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(58, '¿Qué comando se utiliza para cambiar de usuario en Linux?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(59, '¿Cuál es la extensión típica de un script de shell?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(60, '¿Qué comando se utiliza para copiar archivos?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(61, '¿Qué significa \"chmod\" en Linux?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(62, '¿Cuál es el propósito del comando \"grep\"?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(63, '¿Qué comando se utiliza para ver el contenido de un archivo?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(64, '¿Qué es un \"daemon\" en Linux?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(65, '¿Qué comando se utiliza para eliminar archivos?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(66, '¿Qué archivo se utiliza para configurar la red en Linux?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(67, '¿Qué comando se utiliza para ver el uso del disco?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(68, '¿Qué es un \"sistema de archivos\" en Linux?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(69, '¿Qué comando se utiliza para buscar archivos en el sistema?', 'opcion_multiple', 'General', '2025-08-22 15:42:07'),
(70, '¿Qué significa \"sudo\"?', 'opcion_multiple', 'General', '2025-08-22 15:42:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('estudiante','admin') DEFAULT 'estudiante'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `email`, `password`, `created_at`, `role`) VALUES
(1, '', 'josejbohorquezd@gmail.com', '$2y$10$n.9giego4q29/xy.ayvDbOsJHya0gMqehL2iYY.GaP6hJH4g.AB4q', '2025-08-22 13:42:58', 'estudiante'),
(2, 'cami', 'masma@gmail.com', '$2y$10$XXRFA.Bq1yzrpPPp0O4A3.DQt72nI9BPT59V4hKUNPstflDFNsPse', '2025-08-22 13:43:11', 'estudiante'),
(3, 'chayane', 'abc@gmail.com', '$2y$10$1CN0.nbSi3nqXZLlxq2T4eZBmhTRxToEjcsgbHisS9mwuAodpASQu', '2025-08-22 13:56:35', 'estudiante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_answers`
--

CREATE TABLE `user_answers` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `question_id` int UNSIGNED NOT NULL,
  `option_id` int UNSIGNED DEFAULT NULL,
  `answer_text` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indices de la tabla `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `user_answers`
--
ALTER TABLE `user_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `option_id` (`option_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `options`
--
ALTER TABLE `options`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT de la tabla `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `user_answers`
--
ALTER TABLE `user_answers`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_answers`
--
ALTER TABLE `user_answers`
  ADD CONSTRAINT `user_answers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_answers_ibfk_3` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
