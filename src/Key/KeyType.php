<?php

declare(strict_types=1);

namespace CoRex\Config\Key;

enum KeyType
{
    case MIXED;
    case STRING_OR_NULL;
    case STRING;
    case INT_OR_NULL;
    case INT;
    case BOOL_OR_NULL;
    case BOOL;
    case TRANSLATED_BOOL_OR_NULL;
    case TRANSLATED_BOOL;
    case DOUBLE_OR_NULL;
    case DOUBLE;
    case ARRAY_OR_NULL;
    case ARRAY;
    case LIST_OR_NULL;
    case LIST;
}