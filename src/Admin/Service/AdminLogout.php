<?php

namespace App\Admin\Service;

require_once __DIR__ . '/../../Config/Path.php';

class AdminLogout
{
    // ----- admin logout done -----
    public function logout(): void
    {
        unset($_SESSION['admin']);
        // $_SESSION = [];
        // if (isset($_COOKIE[session_name()])) {
        //     setcookie(
        //         session_name(),
        //         '',
        //         time() - 42000,
        //         '/'
        //     );
        // }
        // session_destroy();
    }
}
