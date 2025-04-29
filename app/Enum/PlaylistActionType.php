<?php

namespace App\Enum;

enum PlaylistActionType: string
{
    case Like = 'like';
    case Comment = 'comment';
    case Download = 'download';
}
