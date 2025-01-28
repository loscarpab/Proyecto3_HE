## Inyección SQL

a)

| Escribo los valores ...                                        | "                                                       |
| -------------------------------------------------------------- | ------------------------------------------------------- |
| En el campo ...                                                | Username                                                |
| Del formulario de la página ...                                | insert_player.php                                       |
| La consulta SQL que se ejecuta es ...                          | SELECT userId, password FROM users WHERE username = """ |
| Campos del formulario web utilizados en la consulta SQL ...    | Username                                                |
| Campos del formulario web no utilizados en la consulta SQL ... | Password                                                |
b)

| Explicación del ataque                                | El ataque consiste en repetir ...                                                        |
| ----------------------------------------------------- | ---------------------------------------------------------------------------------------- |
|                                                       | inicios de sesión mediante una inyección SQL mirando el id del usuario en vez del nombre |
|                                                       | ... utilizando en cada interacción una contraseña diferente del diccionario              |
| Campo de usuario con que el ataque ha tenido éxito    | " OR userId = 2 -- -                                                                     |
| Campo de contraseña con que el ataque ha tenido éxito | 1234                                                                                     |
c)

| Explicación del error ...                    | El código actual construye la consulta SQL concatenando directamente los valores proporcionados por el usuario, lo cual permite inyecciones SQL. |
| -------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------ |
| Solución: Cambiar la línea con el código ... | `$query = SQLite3::escapeString('SELECT userId, password FROM users WHERE username = "' . $user . '"');`                                         |
| ... por la siguiente línea ...               | ` $stmt = $db->prepare('SELECT userId, password FROM users WHERE username = :username'); $stmt->bindValue(':username', $user, SQLITE3_TEXT);`    |
- **Consulta preparada**: Se usa `$db->prepare` para evitar concatenar directamente valores en la consulta SQL.
- **Vinculación de valores**: Se usa `bindValue` para enlazar el parámetro `:username` con el valor del usuario de forma segura.
- **Protección contra inyección SQL**: Esto asegura que cualquier entrada del usuario sea tratada como un dato literal y no como código SQL.
## XSS
a)

| Introduzco el mensaje ...         | < script>alert('XSS ejecutado!')< /script> |
| --------------------------------- | ------------------------------------------ |
| En el formulario de la página ... | show_comments.php                          |
b)

| Explicación ... | En HTML, `&` se escribe como `&amp;` porque el ampersand es un carácter especial usado para entidades HTML. Esto evita errores al procesar el código. Por ejemplo:  <br>`?param1=valor1&param2=valor2` → en HTML es `?param1=valor1&amp;param2=valor2`. |
| --------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |

c)

| ¿Cuál es el problema?                     | Los datos del comentario  `body` pueden contener código malicioso (XSS) y no están escapados.                                                                                         |
| ----------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Sustituyo el código de las/las líneas ... | `echo "<h4>" . $row['username'] . "</h4>"` <br>y <br>`echo "<p>commented: " . $row['body'] . "</p>";`.                                                                                |
| ... por el siguiente código ...           | `echo "<h4>" . htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') . "</h4>";`  <br>y<br>`echo "<p>commented: " . htmlspecialchars($row['body'], ENT_QUOTES, 'UTF-8') . "</p>";`. |

## Control de acceso, autenticación y sesiones de usuarios
a)
- **Inyección SQL (SQL Injection):**
    Usar `SQLite3::escapeString` no es completamente seguro, ya que no previene todos los tipos de inyección SQL. Aunque `escapeString` puede prevenir caracteres especiales, una forma más segura es usar **consultas preparadas**.
    
- **Almacenamiento inseguro de contraseñas:**
    Se están almacenando las contraseñas en texto plano, lo cual es muy inseguro. Si alguien tiene acceso a la base de datos, las contraseñas estarán completamente expuestas. Para solucionarlo se pueden hashear contraseñas con una función segura, como password_hash() en PHP. También se puede usar password_verify() para comparar la contraseña ingresada con el hash almacenado.
- **Validación de entradas de usuario:**
    No se están validando adecuadamente los datos de entrada, como el nombre de usuario. Esto podría permitir la introducción de caracteres no deseados o maliciosos. Para solucionarlo se pueden validar las entradas del usuario antes de procesarlas. Asegurándose de que el nombre de usuario no contenga caracteres especiales que puedan interferir con la base de datos o el HTML.
    ![[Pasted image 20250127115245.png]]
    
b)
- Protección contra ataques de **fijación de sesión**:
    
    - Se debe asegurarse de que la cookie de sesión no se pueda secuestrar ni manipular. Usar una cookie de sesión segura y configurada adecuadamente es clave para evitar la suplantación de sesiones.
- **Autenticación basada en sesiones en lugar de cookies permanentes**:
    
    - Las cookies de autenticación (como `user` y `password`) pueden ser manipuladas fácilmente. En lugar de almacenar credenciales en cookies, es mucho más seguro utilizar **sesiones PHP** para almacenar el estado del usuario una vez autenticado.
- **Regeneración de ID de sesión**:
    
    - Tras el login exitoso, se debe regenerar el ID de sesión para evitar ataques de fijación de sesión. Esto evita que un atacante pueda predecir o reutilizar un ID de sesión viejo.
![[Pasted image 20250127120343.png]]
c)
- **Restricción de acceso solo a usuarios autenticados**: La página de registro no debería ser accesible para los usuarios que ya están registrados o autenticados. Deberíamos asegurarnos de que solo los usuarios no registrados puedan ver y acceder a la página de registro. Si un usuario está autenticado, se redirige a otra página, como la lista de jugadores `list_players.php`).
    
- **Redirección en caso de intento de acceso a la página de registro por un usuario ya autenticado**: Si un usuario ya está autenticado y trata de acceder al formulario de registro, debe ser redirigido a la página principal o al área correspondiente.
d)
	**Configurar `.htaccess` para denegar acceso a la carpeta `private`**: Si no puedes mover la carpeta fuera de la raíz pública o no deseas hacerlo, otra opción es configurar un archivo `.htaccess` en la carpeta `private` para impedir el acceso directo desde el navegador.

- En el archivo `.htaccess` dentro de la carpeta `private`, agrega las siguientes líneas:
```bash
# Denegar acceso a todos los archivos en la carpeta private
Order Deny,Allow
Deny from all
```
## Servidores web
Las medidas que implementaría son:
- **Mantener el software actualizado**: Asegurándose de que el sistema operativo, el servidor web (Apache), y las aplicaciones estén siempre actualizados con los últimos parches de seguridad.
- **Configuración segura del servidor**
	- Minimizando servicios: Desactiva servicios y módulos innecesarios para reducir la superficie de ataque.
	- Permisos restrictivos: Asegurándose de que los archivos y directorios tengan los permisos adecuados (por ejemplo, `chmod 640` para archivos sensibles).
- **Firewall (WAF)**: Implementa un Web Application Firewall para filtrar tráfico malicioso, como ataques SQL Injection o XSS.

## CSRF
a)

| En el campo ... | Team name de la página de editar jugador                                                      |
| --------------- | --------------------------------------------------------------------------------------------- |
| Introduzco ...  | ```OWASP<br><a href="http://web.pagos/donate.php?amount=100&receiver=attacker">Profile</a>``` |

b)
Si no tuviera que pulsar un botón sería más eficiente, por eso, se pone un comentario con el siguiente código ```<script>fetch('http://web.pagos/donate.php?amount=100&receiver=attacker')</script>```
c)
Que el usuario esté autenticado