# Simple API con PHP

Este código maneja solicitudes HTTP para crear, actualizar, eliminar y recuperar elementos y sus movimientos de stock a través de una API RESTful. También se utilizan funciones para interactuar con la base de datos, específicamente para realizar consultas SQL utilizando la extensión mysqli. Los datos se manejan en formato JSON tanto en las solicitudes como en las respuestas.

```
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

- ini_set se utiliza para configurar opciones de PHP en tiempo de ejecución. 
- En este caso, se establece display_errors en 1 para mostrar los errores en la salida.

```
$host = 'localhost';
$usuario = 'root';
$contrasena = '7969';
$base_datos = 'simple_api';

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die('Error de conexión a la base de datos: ' . $conexion->connect_error);
}
```


- Estas líneas establecen la información de conexión a la base de datos. 
- Se utiliza la extensión mysqli para interactuar con MySQL.
$host es la dirección del servidor de la base de datos.
$usuario y $contrasena son las credenciales de acceso a la base de datos.
$base_datos es el nombre de la base de datos a la que se conecta.
- Se crea una nueva instancia de la clase mysqli para establecer una conexión a la base de datos. 
- Si hay un error de conexión, se muestra un mensaje de error y se termina la ejecución del script.

```

class Item {
    public $id;
    public $nombre;
    public $codigo;

    public function __construct($id, $nombre, $codigo) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->codigo = $codigo;
    }
}
```

- Se define una clase llamada Item para representar los elementos.
- La clase tiene tres propiedades públicas: id, nombre y codigo.
- El constructor __construct se utiliza para inicializar los valores de estas propiedades cuando se crea un objeto Item.
```

public function obtenerMovimientosStock() {
    global $conexion;

    $query = "SELECT id, item_id, cantidad, precio, tipo_movimiento, fecha FROM stock_entries WHERE item_id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $this->id);
    $stmt->execute();
    $result = $stmt->get_result();

    $stock_entries = array();

    while ($row = $result->fetch_assoc()) {
        $stock_entry = array(
            'id' => $row['id'],
            'item_id' => $row['item_id'],
            'cantidad' => $row['cantidad'],
            'precio' => $row['precio'],
            'tipo_movimiento' => $row['tipo_movimiento'],
            'fecha' => $row['fecha']
        );

        $stock_entries[] = $stock_entry;
    }

    return $stock_entries;
}
```

- El método obtenerMovimientosStock se utiliza para obtener los movimientos de stock asociados a un elemento.
- Se ejecuta una consulta SQL para seleccionar datos de la tabla stock_entries donde el campo item_id coincide con el id del elemento actual ($this->id).
- Se prepara la consulta SQL, y bind_param se utiliza para vincular el valor del id del elemento.
- Luego, se ejecuta la consulta, y get_result se usa para obtener los resultados.
- Se recorren las filas de resultados, se crean arrays asociativos para representar los movimientos de stock y se almacenan en un array $stock_entries. Este array se devuelve al final.

```
function crearItem($nombre, $codigo) {
    global $conexion;

    $stmt = $conexion->prepare("INSERT INTO items (nombre, codigo) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre, $codigo);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
```

- La función crearItem se utiliza para agregar un nuevo elemento a la base de datos.
- Se prepara una consulta SQL para insertar valores en la tabla items con los valores de $nombre y $codigo.
bind_param se utiliza para vincular los valores de $nombre y $codigo a la consulta.
- Si la consulta se ejecuta con éxito, la función devuelve true, de lo contrario, devuelve false.
```
function modificarItem($id, $nombre, $codigo) {
    global $conexion;

    $stmt = $conexion->prepare("UPDATE items SET nombre = ?, codigo = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nombre, $codigo, $id);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
```
- La función modificarItem se utiliza para actualizar un elemento existente en la base de datos.
- Se prepara una consulta SQL para actualizar la tabla items, estableciendo los campos nombre y codigo en los valores proporcionados, donde el campo id coincide con el valor de $id.
bind_param se usa para vincular los valores de $nombre, $codigo y $id a la consulta.
- Si la consulta se ejecuta con éxito, la función devuelve true, de lo contrario, devuelve false.
```
function eliminarItem($id) {
    global $conexion;

    $stmt = $conexion->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
```
- La función eliminarItem se utiliza para eliminar un elemento de la base de datos.
- Se prepara una consulta SQL para eliminar registros de la tabla items donde el campo id coincide con el valor de $id.
bind_param se utiliza para vincular el valor de $id a la consulta.
- Si la consulta se ejecuta con éxito, la función devuelve true, de lo contrario, devuelve false.
```
function listarItems() {
    global $conexion;

    $query = "SELECT id, nombre, codigo FROM items";
    $result = $conexion->query($query);

    $items = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $item = new Item($row['id'], $row['nombre'], $row['codigo']);
            $items[] = $item;
        }
    }

    return $items;
}
```
- La función listarItems se utiliza para obtener una lista de todos los elementos almacenados en la base de datos.
- Se ejecuta una consulta SQL para seleccionar id, nombre y codigo de la tabla items.
- Los resultados se almacenan en un array llamado $items. Si hay elementos en la base de datos, se recorren los resultados, se crean objetos Item y se agregan al array.
- La función devuelve el array con los elementos.
```
function getItemPorID($item_id) {
    global $conexion;

    $query = "SELECT id, nombre, codigo FROM items WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $item = new Item($row['id'], $row['nombre'], $row['codigo']);
        return $item;
    } else {
        return null;
    }
}
```
- La función getItemPorID se utiliza para obtener un elemento específico por su id.
- Se ejecuta una consulta SQL que selecciona id, nombre y codigo de la tabla items donde el campo id coincide con el valor de $item_id.
bind_param se utiliza para vincular el valor de $item_id a la consulta.
- Si se encuentra un elemento con el id proporcionado, se crea un objeto Item y se devuelve. Si no se encuentra, la función devuelve null.
```
function crearMovimientoStock($item_id, $cantidad, $precio, $tipo_movimiento) {
    global $conexion;

    $stmt = $conexion->prepare("INSERT INTO stock_entries (item_id, cantidad, precio, tipo_movimiento) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $item_id, $cantidad, $precio, $tipo_movimiento);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
```
- La función crearMovimientoStock se utiliza para agregar un nuevo movimiento de stock a la base de datos.
- Se ejecuta una consulta SQL para insertar valores en la tabla stock_entries con los valores de $item_id, $cantidad, $precio y $tipo_movimiento.
bind_param se utiliza para vincular los valores a la consulta.
- Si la consulta se ejecuta con éxito, la función devuelve true, de lo contrario, devuelve false.


# Solicitudes HTTP.
```
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resource = $_GET['resource'];
    $data = json_decode(file_get_contents("php://input"), true);
    $resultado = null;

    if ($resource === 'items') {
        $nombre = $data['nombre'];
        $codigo = $data['codigo'];
        if (crearItem($nombre, $codigo)) {
            $resultado = array('mensaje' => 'Item creado con éxito', 'id' => $conexion->insert_id);
        } else {
            $resultado = array('mensaje' => 'Error al crear el item');
        }
    } elseif ($resource === 'stock') {
        $item_id = $data['item_id'];
        $cantidad = $data['cantidad'];
        $precio = $data['precio'];
        $tipo_movimiento = $data['tipo_movimiento'];
        if (crearMovimientoStock($item_id, $cantidad, $precio, $tipo_movimiento)) {
            $resultado = array('mensaje' => 'Movimiento de stock creado con éxito');
        } else {
            $resultado = array('mensaje' => 'Error al crear el movimiento de stock');
        }
    } else {
        $resultado = array('mensaje' => 'Recurso no válido');
    }

    echo json_encode($resultado);
}
```

- Este bloque de código maneja solicitudes HTTP POST. Las solicitudes POST se utilizan para crear nuevos recursos.
Se verifica el recurso que se está creando a partir de la variable $_GET['resource'].
- Los datos se obtienen de la solicitud POST en formato JSON mediante file_get_contents("php://input"). Esto se utiliza para leer el cuerpo de la solicitud, que contiene los datos en formato JSON.
- Los datos se decodifican con json_decode para obtener un array asociativo en la variable $data.
- Dependiendo del recurso (en este caso, "items" o "stock"), se ejecuta la función correspondiente (crearItem o crearMovimientoStock) y se crea una respuesta en formato JSON ($resultado) que contiene un mensaje y, en algunos casos, datos adicionales (como el id del elemento creado).
- La respuesta se convierte en formato JSON mediante json_encode y se envía como respuesta HTTP.
```
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $resource = $_GET['resource'];
    $data = json_decode(file_get_contents("php://input"), true);
    $resultado = null;

    if ($resource === 'items') {
        $id = $data['id'];
        $nombre = $data['nombre'];
        $codigo = $data['codigo'];
        if (modificarItem($id, $nombre, $codigo)) {
            $resultado = array('mensaje' => 'Item actualizado con éxito');
        } else {
            $resultado = array('mensaje' => 'Error al actualizar el item');
        }
    } else {
        $resultado = array('mensaje' => 'Recurso no válido');
    }

    echo json_encode($resultado);
}
```
- Este bloque de código maneja solicitudes HTTP PUT. Las solicitudes PUT se utilizan para actualizar recursos existentes.
- Se verifica el recurso que se está actualizando a partir de la variable $_GET['resource'].
Se obtienen los datos del recurso de la misma manera que en las solicitudes POST: leyendo el cuerpo de la solicitud en formato JSON.
- Dependiendo del recurso (en este caso, solo "items"), se llama a la función correspondiente (modificarItem) para actualizar el elemento en la base de datos.
- Se construye una respuesta en formato JSON ($resultado) que contiene un mensaje que indica si la operación se realizó con éxito o si hubo un error.
- La respuesta se envía de vuelta al cliente en formato JSON.
```
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $resource = $_GET['resource'];
    $data = json_decode(file_get_contents("php://input"), true);
    $resultado = null;

    if ($resource === 'items') {
        $id = $data['id'];
        if (eliminarItem($id)) {
            $resultado = array('mensaje' => 'Item eliminado con éxito');
        } else {
            $resultado = array('mensaje' => 'Error al eliminar el item');
        }
    } else {
        $resultado = array('mensaje' => 'Recurso no válido');
    }

    echo json_encode($resultado);
}
```
- Este bloque de código maneja solicitudes HTTP DELETE. Las solicitudes DELETE se utilizan para eliminar recursos.
- Se verifica el recurso que se está eliminando a partir de la variable $_GET['resource'].
- Se obtienen los datos del recurso a eliminar desde la solicitud en formato JSON.
- Dependiendo del recurso (en este caso, solo "items"), se llama a la función eliminarItem para eliminar el elemento correspondiente en la base de datos.
- Se construye una respuesta en formato JSON ($resultado) que contiene un mensaje que indica si la operación se realizó con éxito o si hubo un error.
- La respuesta se envía al cliente en formato JSON.
```
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $resource = $_GET['resource'];

    if ($resource === 'items') {
        $items = listarItems();
        echo json_encode($items);
    } elseif ($resource === 'stock') {
        $item_id = $_GET['item_id'];

        if ($item_id) {
            $item = getItemPorID($item_id);

            if ($item) {
                $stock_entries = $item->obtenerMovimientosStock();
                echo json_encode($stock_entries);
            } else {
                echo json_encode(array('mensaje' => 'Error: No se encontró el item con el ID proporcionado.'));
            }
        } else {
            echo json_encode(array('mensaje' => 'Error: Debes proporcionar un ID de item para obtener los movimientos de stock.'));
        }
    }
}
```
- Este bloque de código maneja solicitudes HTTP GET. Las solicitudes GET se utilizan para recuperar recursos o información.
- Se verifica el recurso solicitado a partir de la variable $_GET['resource'].
- Si se solicita el recurso "items", se llama a la función listarItems para obtener la lista de elementos y se envía la respuesta en formato JSON.
- Si se solicita el recurso "stock", se verifica si se proporciona un item_id. Si se proporciona, se llama a la función getItemPorID para obtener el elemento específico y sus movimientos de stock, y se envía la respuesta en formato JSON.
- Si no se proporciona un item_id, se envía una respuesta de error en formato JSON indicando que se debe proporcionar un ID de elemento.
- Si no se encuentra un elemento con el ID proporcionado, se envía una respuesta de error en formato JSON.

```
else {
    echo json_encode(array('mensaje' => 'Método no permitido'));
}
```
Si no se cumple ninguna de las condiciones anteriores (ningún método HTTP válido o recurso válido), se envía una respuesta de error en formato JSON indicando que el método no está permitido.

```
$conexion->close();

```

Finalmente, se cierra la conexión a la base de datos.