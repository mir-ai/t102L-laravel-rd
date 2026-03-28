<?php namespace App\Lib\Mms;

class MmsRedisKey {

    public static function redis_job_self_check_key(string $v) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-job-self-check-" . $v;
    }

    public static function redis_subscribe_distinct_count_key($device) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$device}-subscribe-distinct-count";
    }
    
    public static function redis_count_target_user_key($device, $group_ids, $everyone) : string
    {
        $device = strtoupper($device);
        if ($everyone) {
            $group_ids = ['*'];
        }
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$device}-count-target-user-count-group-" . implode('|', $group_ids);
    }
    
    public static function redis_bosai_is_this_mine_key($message_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-bosai-is-this-mine-{$message_id}";
    }

    // IP子局の、放送権利調整用
    // スピーカーごとに２台のCPUがあるが、常に１台からしか鳴らさないようにする。
    public static function redis_ccb_play_approve_key($message_id, $speaker_id) : string
    {
        logger("redis_ccb_play_approve_key message_id={$message_id}, speaker_id={$speaker_id}");
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-ccb-play-approve-{$message_id}-{$speaker_id}";
    }

    // IP子局の、放送権利調整用
    // あるスピーカーについて一度あるCPUに渡したら、一定時間はそのCPUにしか許可しないようにする。
    // (放送中に次の放送が来た場合に、２台から鳴らさないように) 
    public static function redis_ccb_play_speaker_key($speaker_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-ccb-play-speaker-{$speaker_id}";
    }

    public static function redis_bosai_is_this_mine_val() : string
    {
        return 'This is yours';
    }

    public static function redis_ccb_play_approve_val() : string
    {
        return 'This is yours';
    }

    public static function redis_bosai_is_this_mine_expires() : string
    {
        // 10分間
        return 60 * 10;
    }

    public static function redis_catcher_db_current() : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-catcher-db-current";
    }
    
    // 団体サーバ側の pi_devices.id を使う場合の redis キー。（2025年1月以前）
    public static function redis_catcher_remote_power_off_on($pi_device_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$pi_device_id}-power-off-on";
    }

    public static function redis_catcher_remote_reboot($pi_device_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$pi_device_id}-force-reboot";
    }

    public static function redis_catcher_remote_record($pi_device_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$pi_device_id}-force-record";
    }

    public static function redis_catcher_remote_mic_boost($pi_device_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$pi_device_id}-mic-boost";
    }

    public static function redis_catcher_remote_ping($pi_device_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$pi_device_id}-ping";
    }

    // セントラルサーバ側の pi_devices.id を使う場合の redis キー。（2025年2月以降はこちらを使いたい。）
    public static function redis_catcher_remote_power_off_on_v2($pi_device_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$pi_device_id}-power-off-on-v2";
    }

    public static function redis_catcher_remote_reboot_v2($pi_device_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$pi_device_id}-force-reboot-v2";
    }

    public static function redis_catcher_remote_record_v2($pi_device_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$pi_device_id}-force-record-v2";
    }

    public static function redis_catcher_remote_mic_boost_v2($pi_device_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$pi_device_id}-mic-boost-v2";
    }

    public static function redis_catcher_remote_ping_v2($pi_device_id) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-{$pi_device_id}-ping-v2";
    }


    public static function redis_kitting_actions($ip) : string
    {
        return config('_env.APP_CODE') . "-kitting-action-{$ip}";
    }

    public static function redis_arara_mail_status($arara_delivery_id) : string
    {
        return config('_env.APP_CODE') . "-arara-delivery-{$arara_delivery_id}";
    }

    public static function redis_arara_host($arara_delivery_id) : string
    {
        return config('_env.APP_CODE') . "-arara-host-{$arara_delivery_id}";
    }

    public static function redis_arara_mail_progress($message_id) : string
    {
        return config('_env.APP_CODE') . "-arara-progress-{$message_id}";
    }

    public static function redis_arara_is_delivery_done($arara_delivery_id) : string
    {
        return config('_env.APP_CODE') . "-arara-done-{$arara_delivery_id}";
    }

    public static function redis_tel_called($telno, $test) : string
    {
        return config('_env.APP_CODE') . "-tel-called-{$telno}-{$test}";
    }

    public static function redis_sms_called($telno, $test): string
    {
        return config('_env.APP_CODE') . "-sms-called-{$telno}-{$test}";
    }

    public static function onair_devices_kv(): string
    {
        return config('_env.APP_CODE') . "-onair-devices";
    }

    public static function redis_easy_jp_access_token(): string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-easy-jp-access-token";
    }

    public static function redis_return_url_key() : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-return-url";
    }

    public static function redis_code_verifier_key() : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-code-verifier";
    }
    
    public static function redis_updating_db() : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-updating-db";
    }

    public static function redis_test_cbs($career) : string
    {
        return config('_env.APP_CODE') . "-" . config('_env.CITY_CODE') . "-test-cbs-{$career}";
    }
    
    public static function redis_raise_jalert_key() : string
    {
        return config('_env.APP_CODE') . "-raise-jalert-key";
    }

    public static function redis_launch_countdown() : string
    {
        return config('_env.APP_CODE') . "-launch-countdown";
    }

    public static function redis_shared_email_ses_api_concurrent() : string
    {
        return 'shared-email-ses-api-concurrent';
    }

    public static function redis_lte_city_name_key(int $box_id): string
    {
        return "lte-city_name-for-boxid-{$box_id}";
    }

    public static function redis_lte_data_key(int $box_id): string
    {
        return "lte-data-key-for-boxid-{$box_id}";
    }

    public static function redis_lte_should_measure_download_ms_key(int $central_pi_device_id): string
    {
        return "lte-speaker-name-key-for-central-pi-deviceid-{$central_pi_device_id}";
    }
}
