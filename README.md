PHP-Login
=========

Ajuste del excelente trabajo de -> https://github.com/fethica/PHP-Login para tener un template 'estandar'.

Sistema simple y seguro de registro e inicio de sesión con PHP, JQuery (AJAX) y MySQL o PostgreSQL (a eleccion, leer mas abajo) usando Bootstrap 3 para el diseño de formularios así como PHP-Mailer para la verificación y confirmación de la cuenta del usuario.

<img src="https://raw.githubusercontent.com/fethica/PHP-Login/master/login/images/screenshot.png" alt="Login Page Screenshot" />

## Instalación

### Clonamos el repositorio (de manera recursiva a fin de incluir el submodulo de PHP-Mailer)
    $ git clone --recursive https://github.com/fethica/PHP-Login.git

### LOGIN Base MySQL - Creacion BD

Crear la base de datos "login" y crear las tablas "members" y "loginAttempts" :

```sql
CREATE TABLE `members` (
  `id` char(23) NOT NULL,
  `username` varchar(65) NOT NULL DEFAULT '',
  `password` varchar(65) NOT NULL DEFAULT '',
  `email` varchar(65) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `mod_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `loginAttempts` (
  `IP` varchar(20) NOT NULL,
  `Attempts` int(11) NOT NULL,
  `LastLogin` datetime NOT NULL,
  `Username` varchar(65) DEFAULT NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### LOGIN Base PostgreSQL - Creacion BD

Crear la base de datos "login" y crear las tablas "members" y "loginAttempts" :

```sql
CREATE TABLE members
(
  username character varying(65) NOT NULL DEFAULT ''::character varying,
  password character varying(65) NOT NULL DEFAULT ''::character varying,
  email character varying(65) NOT NULL,
  verified smallint NOT NULL DEFAULT 0,
  mod_timestamp timestamp without time zone NOT NULL DEFAULT now(),
  id character(23) NOT NULL,
  CONSTRAINT id PRIMARY KEY (id, username, email)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE members
  OWNER TO postgres;
  
  
CREATE SEQUENCE public.loginattempts_id_seq
    INCREMENT 1
    START 210
    MINVALUE 1
    MAXVALUE 9223372036854775807
    CACHE 1;

ALTER SEQUENCE public.loginattempts_id_seq
    OWNER TO postgres;  
  

CREATE TABLE loginattempts
(
  ip character varying(20) NOT NULL,
  attempts numeric(11,0) NOT NULL,
  lastlogin timestamp without time zone NOT NULL,
  username character varying(65) DEFAULT NULL::character varying,
  id integer NOT NULL DEFAULT nextval('loginattempts_id_seq'::regclass),
  CONSTRAINT "ID" PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE loginattempts
  OWNER TO postgres;
```
<i>En el archivo login/includes/dbconn.php, reeemplazar la linea 19 de:</i>

```php
$this->conn = new PDO('mysql:host=' . $host . ';dbname=' . $db_name . ';charset=utf8', $username, $password);
```
por

```php
$this->conn = new PDO('pgsql:host='.$host.';dbname='.$db_name.';user='.$username.';password='.$password);
```


### Configuramos la DB en el archivo `login/dbconf.php`
```php
<?php
    //Variables de Conexion a la Base de Datos
    $host = "localhost"; // Equipo
    $username = "user"; // Usuario MySql/PostgreSQL
    $password = "password"; // Password MySql/PostgreSQL
    $db_name = "login"; // Nombre de la BD

```

### Poner este codigo (de `index.php`) en la cabecera de cada pagina, o crear un archivo/template y hacer el correspondiente require_once :
> *** **Importante** *** Verifica si la variable de sesion usuario en $_SESSION fue establecida. Sino, redirige a la pagina de LOGIN. 

```php
<?php require "login/loginheader.php"; ?>
```

### Verificacion del Usuario y Contraseña usando jQuery (Ajax) :

Si el usuario ingresa el usuario y contraseña correcto, entonces `checklogin.php` envia como respuesta 'true', registra el usuario y contraseña en una sesion, y lo redirige a `index.php`
Si el usuario y/o contraseña son incorrectos, entonces  ´checklogin.php´ rsponde con "Usuario o contraseña incorrecta".


### Workflow de Registro/Login:
> 1) Creacion del nuevo usuario usando el formulario `signup.php`
> (nota: la validación ocurre tanto en el cliente como de lado del servidor)
> &nbsp;&nbsp;&nbsp;&nbsp;<b>La validación requiere: </b>
> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Contraseñas iguales de almenos 4 caracteres de largo
> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Direccion de email válida
> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Usuario no existente
> 2) La conntraseña se hashea y un nuevo GUID es generado para el ID de Usuario
> 3) Se agrega el usuario a la base de datos como 'no verificado'
> 4) Se envia un correo al email registrado (o al correo del MODERADOR $admin_email si se establecio) con un enlace de validación
> 5) El usuario (o MODERADOR) hace click en el enlace de verificacion que lo envia a `verifyuser.php` y verifica el usuario en la base de datos
> 6) El usuario verificado ya puede iniciar sesion


### Configuramos variables globales en el archivo `login/config.php`
<i>Leer los comentarios en el codigo para una descripcion de cada variable</i>

```php
<?php
    //Variable global con el nombre del sitio
    $site_name = 'Test Site';

    //Maximo de intentos de Login, despues inhabilita el usuario segun el tiempo definido
    $max_attempts = 5;
    //Tiempo punitorio (en segundos) para el usuario que haya alcanzado el maximo numero de intentos de Login
    $login_timeout = 300;

    //Correo del MODERADOR, sino dejar en blanco o comentar la linea
    $admin_email = '';

    //EMAIL SETTINGS
    //SEND TEST EMAILS THROUGH FORM TO https://www.mail-tester.com GENERATED ADDRESS FOR SPAM SCORE
    $from_email = 'youremail@domain.com'; //Webmaster email
    $from_name = 'Test Email'; //"From name" displayed on email

    //Find specific server settings at https://www.arclab.com/en/kb/email/list-of-smtp-and-pop3-servers-mailserver-list.html
    $mailServerType = 'smtp';
    //IF $mailServerType = 'smtp'
    $smtp_server = 'smtp.mail.domain.com';
    $smtp_user = 'youremail@domain.com';
    $smtp_pw = 'yourEmailPassword';
    $smtp_port = 465; //465 for ssl, 587 for tls, 25 for other
    $smtp_security = 'ssl';//ssl, tls or ''

    //HTML Messages shown before URL in emails (the more
    $verifymsg = 'Click this link to verify your new account!'; //Verify email message
    $active_email = 'Your new account is now active! Click this link to log in!';//Active email message
    //LOGIN FORM RESPONSE MESSAGES/ERRORS
    $signupthanks = 'Thank you for signing up! You will receive an email shortly confirming the verification of your account.';
    $activemsg = 'Your account has been verified! You may now login at <br><a href="'.$signin_url.'">'.$signin_url.'</a>';

    //IGNORE CODE BELOW THIS
```
