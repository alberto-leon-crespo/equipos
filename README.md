# Arrancar el proyecto

```symfony serve --port=3000```

## Listado de jugadores

```GET /v1/es/players```

### Filtros disponibles

Cualquier campo se puede filtrar mediante el nombre que se recibe en la respuesta.

```GET /v1/es/players?id=1```

```GET /v1/es/players?name=Alberto```

```GET /v1/es/players?age=28```

Si un campo devuelto es un objeto, se debe indicar el siguiente nivel de profundida mediante un punto

```GET /v1/es/players?team.id=1```

```GET /v1/es/players?team.name=Real Madrid```

## Detalle de un Jugador

```GET /v1/es/players/:player_id```

## Crear un jugador

```POST /v1/es/players```

## Actualizar un jugador

```PUT  /v1/es/players/:player_id```

## Eliminar un Jugador

```DELETE /v1/es/players/:player_id```