<?php

namespace App\Admin\Service;

require_once __DIR__ . '/../../Config/Path.php';

class AdminTimeout
{
    // ----- admin timeout done -----
    public function logoutDueToTimeout(): void
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
