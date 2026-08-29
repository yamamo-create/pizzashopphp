<?php

namespace App\Customer\Service;

require_once __DIR__ . '/../../Config/Path.php';

class CustomerLogout
{
    // ----- customer logout done -----
    public function logout(): void
    {
        unset($_SESSION['customer']);
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
