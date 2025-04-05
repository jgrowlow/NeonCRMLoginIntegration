<?php

namespace Hcc\NeonLogin;

class Config
{
    public static function getNeonCRMConfig()
    {
        return [
            'api_url' => 'https://api.neoncrm.com/v2',
            'api_key' => getenv('NEONCRM_API_KEY'),
            'org_id'  => getenv('NEONCRM_ORG_ID'),
        ];
    }
    public static function getFlarumConfig()
    {
        return [
            'redirect_url' => getenv('FLARUM_REDIRECT_URL') ?: '/forum',
        ];
    }
}
