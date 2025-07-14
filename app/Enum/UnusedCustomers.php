<?php

namespace App\Enum;

use Illuminate\Validation\Rules\Exists;

enum UnusedCustomers : string
{

    case ACCOUNT = 'account';
    case ACCOUNT_CONSOLE = 'account-console';
    case ADMIN_CLI = 'admin-cli';
    case BROKER = 'broker';
    case REALM_MANAGEMENT = 'realm-management';
    case SECURITY_ADMIN_CONSOLE = 'security-admin-console';
    case _SYSTEM = '_system';

    public static function unusedCustomers(string $name){
        switch ($name){
            case self::ACCOUNT->value :
                return true;
                break;
            case self::ACCOUNT_CONSOLE->value :
                return true;
                break;
            case self::ADMIN_CLI->value :
                return true;
                break;
            case self::BROKER->value :
                return true;
                break;
            case self::REALM_MANAGEMENT->value :
                return true;
                break;
            case self::SECURITY_ADMIN_CONSOLE->value :
                return true;
                break;
            case self::_SYSTEM->value :
                return true;
                break;
            default:
                return false;
            break;
        }

    }
}

