<?php

namespace Toolkit\models;

use Toolkit\models\OptionPage;

class Config extends OptionPage
{
    const ID = "config";
    const PARAMS = [
        "page_title" => "Configuration",
        "menu_title" => "Configuration",
        "redirect" => false,
        "position" => 2,
        "icon_url" => "dashicons-laptop",
    ];
}
