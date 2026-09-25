# Guía para Ámbar — Cómo entrar al sistema

Este proyecto corre **en tu propia PC**. No necesitas internet ni conectarte a la PC de Abel.

## Paso 1. Abre XAMPP

Abre XAMPP (busca "XAMPP" en el menú inicio) y dale **Start** solo a **MySQL**.
Apache no hace falta, déjalo apagado.

## Paso 2. Enciende el sistema

Dentro de la carpeta del proyecto (donde descomprimiste el ZIP), **haz doble clic en `setup.bat`**:

1. Se abre una ventana negra (consola) y corre sola: crea/verifica la base de datos y deja el sistema encendido.
2. Cuando termine, verás que la ventana se queda esperando: **eso significa que el sistema está activo**.
3. **No cierres esa ventana** mientras uses el sistema.

> Si prefieres hacerlo a mano, también funciona: abre la ventana de PowerShell aquí, escribe
> `powershell -ExecutionPolicy Bypass -File .\setup.ps1`, espera a que diga "Base de datos lista",
> y luego escribe `C:\xampp\php\php.exe -S 0.0.0.0:8090 backend\router.php`.

## Paso 3. Entra al sistema

Abre tu navegador (Chrome, Edge, etc.) y entra a:

```
http://localhost:8090
```

## Para terminar

Cierra la ventana negra (con **Ctrl + C** o la X). También puedes apagar MySQL desde XAMPP.

## Datos importantes

- **Base de datos**: el `setup.ps1` la crea solita. Los datos de práctica (puntos, salas de quiz) de cada quien se guardan en su propia PC.
- Si viste un error tipo "El puerto 8090 ya está en uso", es porque el servidor ya está encendido: solo abre `http://localhost:8090`.
- Si algo se rompe, cierra la ventana y vuelve a hacer doble clic en `setup.bat`. Si el problema persiste, úsalo así: `setup.bat -Reset` (borra y recrea la base de datos).