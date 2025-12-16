#!/bin/bash
# Script para importar la BD en XAMPP (macOS/Linux)

DB_NAME="sigc"
DB_USER="root"
DB_PASS=""

echo "========================================="
echo "SIGC - Importador de Base de Datos"
echo "========================================="

# Detectar XAMPP_BIN según el sistema operativo
if [ -f "/opt/homebrew/opt/mysql/bin/mysql" ]; then
    # macOS con Homebrew MySQL (prioridad: está corriendo)
    XAMPP_BIN="/opt/homebrew/opt/mysql/bin"
    echo "✓ Detectado MySQL via Homebrew en macOS"
elif [ -f "/Applications/XAMPP/xamppfiles/bin/mysql" ]; then
    # macOS con XAMPP
    XAMPP_BIN="/Applications/XAMPP/xamppfiles/bin"
    echo "✓ Detectado XAMPP en macOS"
elif [ -f "/opt/xampp/bin/mysql" ]; then
    # Linux
    XAMPP_BIN="/opt/xampp/bin"
    echo "✓ Detectado XAMPP en Linux"
else
    echo "✗ Error: No se encontró mysql en:"
    echo "  - /opt/homebrew/opt/mysql/bin/ (Homebrew macOS)"
    echo "  - /Applications/XAMPP/xamppfiles/bin/ (XAMPP macOS)"
    echo "  - /opt/xampp/bin/ (XAMPP Linux)"
    echo ""
    echo "Verifica que MySQL está instalado correctamente"
    exit 1
fi

# Verificar si MySQL está corriendo
echo "Verificando si MySQL está corriendo..."
if ! $XAMPP_BIN/mysql -u$DB_USER -p$DB_PASS --skip-password -e "SELECT 1" > /dev/null 2>&1; then
    # Intentar sin especificar contraseña
    if ! $XAMPP_BIN/mysql -u$DB_USER -e "SELECT 1" > /dev/null 2>&1; then
        echo ""
        echo "✗ MySQL no está corriendo"
        echo ""
        echo "Por favor, inicia XAMPP primero:"
        echo ""
        echo "Opción 1 (Recomendado): Abre XAMPP Control Panel"
        echo "  - Ve a /Applications"
        echo "  - Haz doble clic en: manager-osx.app"
        echo "  - Haz clic en 'Start' para MySQL"
        echo ""
        echo "Opción 2 (Terminal):"
        echo "  sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start"
        echo ""
        echo "Luego intenta nuevamente"
        exit 1
    fi
fi

echo "✓ MySQL está corriendo"
echo ""

# Crear base de datos e importar
echo "Importando base de datos '$DB_NAME'..."

# Si la contraseña está vacía, no usar -p
if [ -z "$DB_PASS" ]; then
    # Crear BD primero
    $XAMPP_BIN/mysql -u$DB_USER -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    # Luego importar SQL
    $XAMPP_BIN/mysql -u$DB_USER $DB_NAME < ./sql/ddl_sigc.sql
else
    # Crear BD primero
    $XAMPP_BIN/mysql -u$DB_USER -p$DB_PASS -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    # Luego importar SQL
    $XAMPP_BIN/mysql -u$DB_USER -p$DB_PASS $DB_NAME < ./sql/ddl_sigc.sql
fi

if [ $? -eq 0 ]; then
    echo "✓ Base de datos importada exitosamente"
    echo "Puedes acceder a la aplicación en: http://localhost"
else
    echo "✗ Error al importar la base de datos"
    exit 1
fi
