<?php

    $domain = "messages";
    bindtextdomain($domain, "Modules/rules/locale");
    bind_textdomain_codeset($domain, 'UTF-8');

    $menu_dropdown[] = array('name'=> dgettext($domain, "Rules"), 'path'=>"rules/list" , 'session'=>"read", 'order' => 2 );