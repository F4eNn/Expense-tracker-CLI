<?php

namespace App\Enums;

enum CMD_options: string
{
    
    case Description = "description";
    case Amount = 'amount';
    case Category = 'category';
    case Update = "update";
    case Add = "add";
    case Delete = 'delete';
    case All = 'all';
    case Summary = "summary";
    case Month = 'month';
    case ID = 'id';
    case Limit = 'limit';
}
