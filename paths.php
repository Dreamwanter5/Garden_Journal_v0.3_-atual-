<?php
/**
 * CONFIGURAÇÃO DE CAMINHOS ABSOLUTOS
 * Para uso em todo o projeto Garden Journal
 */

// 1. Definir caminho absoluto para a raiz do projeto
define('ROOT_PATH', realpath(dirname(__FILE__)));

// 2. Definir caminhos para diretórios principais
define('DAOS_PATH', ROOT_PATH . '/DAOs');
define('ENTIDADES_PATH', ROOT_PATH . '/Entidades');
define('CONTROLLERS_PATH', ROOT_PATH . '/Controllers');
define('VIEWS_PATH', ROOT_PATH . '/Garden_Journal_Cadastro/views');

// 3. Definir caminhos para subdiretórios específicos
define('HTML_PATH', VIEWS_PATH . '/HTML');
define('JS_PATH', VIEWS_PATH . '/JS');
define('CSS_PATH', VIEWS_PATH . '/CSS');
define('IMG_PATH', VIEWS_PATH . '/IMG');
// Adicione esta função antes do autoloader
function safe_require($path) {
    if (file_exists($path)) {
        require_once $path;
    } else {
        error_log("Arquivo não encontrado: $path");
        throw new Exception("Arquivo essencial não encontrado: $path");
    }
}

// E modifique o autoloader para:


// 4. Configurar autoload para classes
spl_autoload_register(function ($className) {
    $classMap = [
        'BaseDAO' => DAOS_PATH . '/baseDao.php',
        'UsuarioDAO' => DAOS_PATH . '/usuarioDao.php',
        'NotaDAO' => DAOS_PATH . '/notaDao.php',
        'CategoriaDAO' => DAOS_PATH . '/categoriaDao.php',
        'Usuario' => ENTIDADES_PATH . '/usuario.php',
        'UsuarioController' => CONTROLLERS_PATH . '/usuarioController.php'
    ];

    if (isset($classMap[$className])) {
        safe_require($classMap[$className]);
    } else {
        // Tentar carregar por convenção de nomes
        $possiblePaths = [
            DAOS_PATH . "/$className.php",
            DAOS_PATH . "/" . strtolower($className) . ".php",
            ENTIDADES_PATH . "/$className.php",
            CONTROLLERS_PATH . "/$className.php"
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                safe_require($path);
                return;
            }
        }
        error_log("Classe não encontrada: $className");
    }
});

// 5. Configurações de sessão segura
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400, // 1 dia
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => true,     // Apenas HTTPS
        'httponly' => true,    // Apenas HTTP
        'samesite' => 'Strict'
    ]);
    session_start();
}

// 6. Configurações de exibição de erros (apenas desenvolvimento)
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// 7. Função utilitária para gerar URLs absolutas
function asset($path) {
    $baseUrl = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://{$_SERVER['HTTP_HOST']}";
    $projectPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT_PATH);
    return "{$baseUrl}{$projectPath}/{$path}";
}

// 8. Constantes úteis para frontend
define('BASE_URL', asset(''));
define('JS_URL', asset('Garden_Journal_Cadastro/views/JS'));
define('CSS_URL', asset('Garden_Journal_Cadastro/views/CSS'));
define('IMG_URL', asset('Garden_Journal_Cadastro/views/IMG'));