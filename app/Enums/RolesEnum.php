<?php

namespace App\Enums;

enum RolesEnum: int {
    case SUPER_ADMIN = 1;
    case ADMIN = 2;
    case USER = 3;
}
