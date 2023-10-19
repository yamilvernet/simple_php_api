# Items

GET: Obtener una lista de todos los elementos "items" almacenados en la base de datos.

```
GET http://localhost/simple_api/index.php?resource=items
```

Response:
```
[
    {
        "id": 1,
        "nombre": "Intel i5 4 núcleos",
        "codigo": "GPUINTELI5"
    },
    {
        "id": 2,
        "nombre": "AMD Ryzen 7",
        "codigo": "GPUAMDRYZ7"
    },
    {
        "id": 3,
        "nombre": "NVIDIA GeForce RTX 3080",
        "codigo": "GPUNV3080"
    }
    // ... Otros elementos de ejemplo
]
```

POST: Crear un nuevo elemento "items" en la base de datos.
```
POST http://localhost/simple_api/index.php?resource=items
```
Request (JSON):
```
{
    "nombre": "Intel i7 8 núcleos",
    "codigo": "GPUINTELI7"
}
```
Response:
```
{
    "mensaje": "Item creado con éxito",
    "id": 4
}
```

PUT: Actualizar un elemento "items" existente en la base de datos. En este ejemplo, actualizaremos el elemento con id igual a 4.
```
PUT http://localhost/simple_api/index.php?resource=items
```

Request (JSON):
```
{
    "id": 4,
    "nombre": "Intel i7 10 núcleos",
    "codigo": "GPUINTELI7"
}
```
Response:
```
{
    "mensaje": "Item actualizado con éxito"
}
```
DELETE: Eliminar un elemento "items" existente en la base de datos. En este ejemplo, eliminaremos el elemento con id igual a 4.

```
DELETE http://localhost/simple_api/index.php?resource=items
```
Request (JSON):
```
{
    "id": 4
}
```
Response:
```
{
    "mensaje": "Item eliminado con éxito"
}
```

# Stock

POST: Crear un nuevo movimiento de stock en la base de datos.
```
POST http://localhost/simple_api/index.php?resource=stock
```
Request (JSON):
```
{
    "item_id": 1,
    "cantidad": 10,
    "precio": 199.99,
    "tipo_movimiento": "Entrada"
}
```
Response:
```
{
    "mensaje": "Movimiento de stock creado con éxito"
}
```
GET: Obtener la lista de movimientos de stock para un elemento "items" específico. En este ejemplo, obtendremos los movimientos de stock para el elemento con item_id igual a 1.
```
GET http://localhost/simple_api/index.php?resource=stock&item_id=1
```
Response:
```
[
    {
        "id": 1,
        "item_id": 1,
        "cantidad": 10,
        "precio": 199.99,
        "tipo_movimiento": "Entrada",
        "fecha": "2023-10-18 14:30:00"
    },
    {
        "id": 2,
        "item_id": 1,
        "cantidad": 5,
        "precio": 189.99,
        "tipo_movimiento": "Salida",
        "fecha": "2023-10-18 15:45:00"
    }
    // ... Otros movimientos de stock de ejemplo
]
```