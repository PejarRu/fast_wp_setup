# WP Fast Setup - Guía de Diagnóstico y Solución de Problemas

## � Configuración Segura de Google Drive

### Métodos de Configuración (Orden de Prioridad)

El sistema lee las credenciales de Google Drive en este orden:

1. **Archivo `.env`** (Recomendado para desarrollo local)
2. **Opciones de WordPress** (Para producción)
3. **Constantes por defecto** (Fallback)

### 1. Configuración con Archivo .env (Desarrollo Local)

```bash
# Copia el archivo de ejemplo
cp .env.example .env

# Edita el archivo .env con tus credenciales
nano .env
```

Contenido del archivo `.env`:
```env
GOOGLE_DRIVE_API_KEY=tu_api_key_aqui
GOOGLE_DRIVE_FOLDER_ID=tu_folder_id_aqui
```

### 2. Configuración desde WordPress Admin

1. Ve al panel de administración de WordPress
2. Navega a **WP Fast Setup**
3. En la pestaña **Plugins**, configura:
   - **API Key de Google Drive**: Tu clave de API
   - **ID de la Carpeta de Google Drive**: El ID de tu carpeta

### 3. Configuración por Constantes (Fallback)

Las constantes por defecto en `wp-fast-setup-installer.php` ahora se cargan automáticamente desde el archivo `.env` si existe. Si no hay archivo `.env`, las constantes estarán vacías:

```php
define('WP_FAST_SETUP_DEFAULT_API_KEY', $_ENV['GOOGLE_DRIVE_API_KEY'] ?? '');
define('WP_FAST_SETUP_DEFAULT_FOLDER_ID', $_ENV['GOOGLE_DRIVE_FOLDER_ID'] ?? '');
```

**Nota:** El archivo `wp-fast-setup-installer.php` está incluido en `.gitignore` para evitar commits accidentales de credenciales.

## �️ Importación Automática de Medios

### Cómo Funciona

El plugin incluye una funcionalidad automática para importar imágenes desde la carpeta `assets/images/` a la galería de medios de WordPress.

### Configuración

1. **Ubicación de imágenes**: `wp-fast-setup/assets/images/`
2. **Formatos soportados**: PNG, JPG, JPEG, GIF, WEBP
3. **Importación automática**: Se ejecuta al activar el plugin
4. **Prevención de duplicados**: Las imágenes ya existentes no se duplican

### Uso desde el Panel de Administración

1. Ve al panel de administración de WordPress
2. Navega a **WP Fast Setup**
3. Haz clic en la pestaña **🖼️ Medios**
4. Verás un resumen de imágenes disponibles e importadas
5. Haz clic en **"🖼️ Importar Imágenes a Galería"** para importar manualmente
6. Las imágenes aparecerán en **Medios > Biblioteca**

### Agregar Nuevas Imágenes

1. Coloca tus archivos de imagen en `wp-fast-setup/assets/images/`
2. Ve a la pestaña **Medios** en WP Fast Setup
3. Haz clic en **"Importar Imágenes a Galería"**
4. Las nuevas imágenes se importarán automáticamente

### Limpieza

Si necesitas eliminar las imágenes importadas:
- En la pestaña **Medios**, haz clic en **"🗑️ Eliminar Imágenes Importadas"**
- Esto eliminará todas las imágenes que fueron importadas por el plugin

**Nota:** Esta acción no se puede deshacer. Las imágenes se eliminarán permanentemente de la galería de medios.
- AJAX completo para todas las operaciones
- Diagnóstico mejorado de Google Drive
- Instalación de plugins desde múltiples fuentes
- Sistema de respaldo con archivos ZIP locales

## 🐛 Diagnóstico de Google Drive

### Problema: "Google Drive no es accesible"

### 1. Ejecutar Diagnóstico Automático

```bash
cd /Users/anton/Desktop/savourprojects_local/fast_wp_setup
php test-google-drive.php
```

### 2. Configurar Credenciales

**Opción A: Archivo .env (Recomendado)**
```bash
cp .env.example .env
nano .env
```

**Opción B: Desde WordPress Admin**
1. Ve a WP Fast Setup → Plugins
2. Configura los campos de Google Drive
3. Los valores se guardan automáticamente

**Opción C: Edición directa (No recomendado)**
Solo para desarrollo, edita `test-google-drive.php` temporalmente:
```php
$api_key = 'TU_API_KEY_DE_GOOGLE_DRIVE';
$folder_id = 'TU_FOLDER_ID_DE_GOOGLE_DRIVE';
```

### 3. Verificar Resultados

El script te dirá exactamente qué está fallando:
- ✅ **Conexión exitosa**: El problema está en WordPress
- ❌ **API Key inválida**: Revisa tus credenciales de Google Cloud Console
- ❌ **Folder ID incorrecto**: Asegúrate de usar el ID de la carpeta, no la URL
- ❌ **Permisos insuficientes**: La carpeta debe ser pública o tener permisos adecuados

### 4. Soluciones Comunes

#### a) Obtener API Key correcta:
1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea o selecciona un proyecto
3. Habilita la Google Drive API
4. Crea credenciales (API Key)
5. Configura restricciones si es necesario

#### b) Obtener Folder ID:
1. Abre tu carpeta de Google Drive
2. La URL será: `https://drive.google.com/drive/folders/FOLDER_ID`
3. Copia solo la parte `FOLDER_ID` (después del último `/`)

#### c) Configurar permisos:
1. Haz clic derecho en la carpeta
2. Selecciona "Compartir"
3. Cambia los permisos a "Cualquier persona con el enlace puede ver"

## 📦 Sistema de Plugins

### Fuentes de Plugins Disponibles:

1. **Repositorio WordPress.org** (30+ plugins)
2. **Archivos ZIP locales** (18 archivos premium)
3. **Google Drive** (archivos ZIP en la nube)

### Plugins Locales Disponibles:

- Advanced Custom Fields Pro v6.3.11
- Complianz Privacy Suite 7.4.1
- Elementor Pro
- WP Rocket 3.18.1.3
- Yoast SEO Premium
- WP Smush Pro
- Y muchos más...

## � Notas de Seguridad

⚠️ **Importante:** Nunca commits archivos con credenciales reales a git.

### Archivos Ignorados (.gitignore)
- `.env` - Contiene credenciales reales
- `wp-fast-setup-installer.php` - Puede contener datos sensibles durante desarrollo
- `test-google-drive.php` - Puede contener datos sensibles durante desarrollo
- `wp-config.php` - Configuración de WordPress

### Mejores Prácticas
1. Usa `.env.example` como plantilla para desarrollo local
2. Configura credenciales desde el panel de WordPress en producción
3. Nunca hardcodees credenciales en archivos versionados
4. Revisa `.gitignore` antes de hacer commits

## �🚀 Uso del Sistema

### Instalación Normal:
1. Ve al panel de administración de WordPress
2. Busca "WP Fast Setup"
3. Selecciona los plugins que quieres instalar
4. Haz clic en "Instalar Plugins Seleccionados"

### Instalación con Diagnóstico:
1. Si Google Drive falla, el sistema automáticamente usa archivos locales
2. Revisa los mensajes de error para soluciones específicas
3. Usa el script de diagnóstico para troubleshooting avanzado

## 🔍 Logs y Debugging

### Verificar Instalación:
```bash
# Ver logs de WordPress
tail -f /ruta/a/wp-content/debug.log
```

### Verificar Archivos:
```bash
# Listar archivos ZIP disponibles
ls -la zip-files/
```

### Verificar Configuración:
```bash
# Ver configuración actual
php -r "echo file_get_contents('includes/plugins-list.json');" | jq .
```

## ⚡ Optimizaciones Recientes

- **AJAX completo**: Todas las operaciones son asíncronas
- **Manejo de errores mejorado**: Mensajes específicos y útiles
- **Sistema de respaldo**: Funciona sin Google Drive
- **Detección automática**: Encuentra archivos de plugin automáticamente
- **Timeout aumentado**: Mejor tolerancia a conexiones lentas

## 📞 Soporte

Si encuentras problemas:

1. **Ejecuta el diagnóstico**: `php test-google-drive.php`
2. **Revisa los logs**: Busca errores en debug.log
3. **Verifica permisos**: Asegúrate de que los archivos ZIP sean legibles
4. **Actualiza credenciales**: Confirma que API Key y Folder ID sean correctos

---

**Estado**: ✅ Sistema operativo y funcional
**Última actualización**: Septiembre 2025
**Versión**: 2.0 - Con diagnóstico mejorado