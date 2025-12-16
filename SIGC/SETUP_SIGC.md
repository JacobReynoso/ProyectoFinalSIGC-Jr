# Configurar VirtualHost y hosts para SIGC

Archivos creados en el proyecto:
- `config/sigc-vhost.conf` — bloque de VirtualHost listo.
- `scripts/setup-sigc.ps1` — script PowerShell para aplicar los cambios (debe ejecutarse como Administrador).

Cómo usar (Windows, ejecutar como Administrador):

1. Abrir PowerShell como Administrador en la carpeta del proyecto `C:\xampp\htdocs\SIGC`.
2. Ejecutar:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup-sigc.ps1
```

El script hará copia de seguridad de los archivos modificados y añadirá el VirtualHost, descomentará la inclusión en `httpd.conf` y añadirá la entrada en `C:\Windows\System32\drivers\etc\hosts`.

Si prefieres no ejecutar el script, copia manualmente el contenido de `config/sigc-vhost.conf` en `C:\xampp\apache\conf\extra\httpd-vhosts.conf` y añade `127.0.0.1 sigc.local` en tu archivo `hosts`.
