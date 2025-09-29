<?php

namespace Toolkit\models;

// Prevent direct access.
defined('ABSPATH') or exit;

use Toolkit\models\AbstractSearch;

class Search extends AbstractSearch
{
    const TYPE = ["post", "page"];
}
