<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$contrasena = '7969';
$base_datos = 'simple_api';

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die('Error de conexión a la base de datos: ' . $conexion->connect_error);
}

// Clase Item para manipular items
class Item {
    public $id;
    public $nombre;
    public $codigo;

    public function __construct($id, $nombre, $codigo) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->codigo = $codigo;
    }

    public function obtenerMovimientosStock() {
        global $conexion;

        // Consulta SQL para obtener movimientos de stock para este item
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
}

// Crear un nuevo item
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

// Modificar un item
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

// Eliminar un item
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

// Listar todos los items
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

function getItemPorID($item_id) {
    global $conexion;

    // Consulta SQL para buscar un item por su ID
    $query = "SELECT id, nombre, codigo FROM items WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Inicializa un objeto Item con los datos obtenidos de la base de datos
        $item = new Item($row['id'],$row['nombre'],$row['codigo']);
        return $item;
    } else {
        return null; // No se encontró un item con el ID proporcionado
    }
}

// Crear un nuevo movimiento de stock
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



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si es una solicitud POST, asumimos que estamos creando un nuevo recurso

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
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Si es una solicitud PUT, asumimos que estamos actualizando un recurso

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
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Si es una solicitud DELETE, asumimos que estamos eliminando un recurso

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
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Si es una solicitud GET, asumimos que estamos obteniendo una lista de recursos

    $resource = $_GET['resource'];

    if ($resource === 'items') {
        $items = listarItems();
        echo json_encode($items);
    } elseif ($resource === 'stock') {
        $item_id = $_GET['item_id'];

        if ($item_id) {
            // Buscar el item por su ID
            $item = getItemPorID($item_id); // Define esta función para obtener un item por su ID

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
} else {
    echo json_encode(array('mensaje' => 'Método no permitido'));
}

$conexion->close();