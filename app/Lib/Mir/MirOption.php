<?php namespace App\Lib\Mir;

use App\DataAccess\ServiceDa;
use App\DataAccess\UserDa;

class MirOption
{
    public static function get(string $option_type, string $prompt = '選択', string $code = '')
    {
        $kvs = self::getKvs(
            option_type: $option_type, 
        );

        // 先頭に「選択」（または $prompt ）をつける
        $kvs = array_reverse($kvs, true);
        $kvs[''] = $prompt;
        $kvs = array_reverse($kvs, true);

        return $kvs;
    }

    public static function getLabel(string $option_type, string|null $code = ''): string
    {
        $label = match ($option_type) {
            // 'groups.group_names_by_ids_csv' => GroupDa::getGroupNamesByIdsCsv($code),
            // 'users.get_name_by_id' => UserDa::getNameById($code),
            // 'depts.get_name_by_id' => DeptDa::getNameById($code),
            default => "getLabel {$option_type} not found",
        };

        return $label;
    }

    public static function getKvs(string $option_type, string|null $code = ''): array
    {
        $kvs = match ($option_type) {
            'services.key_names' => ServiceDa::getNamesByCodes($code),
            'users.id_names' => UserDa::getNamesByIds($code),
            'http_status' => config('_const.labels.http_status'),
            'http_method' => config('_const.labels.http_method'),
            'log_level' => config('_const.labels.log_level'),
            'table_names' => config('_const.labels.table_names'),
            'op_mode' => config('_const.labels.op_mode'),
            'lang_code' => config('_const.labels.lang_code'),
            'prefectures' => config('_const.labels.prefectures'),
            
            // 'pi_devices.device_type' => config('_const.device_types'),
            // 'pi_devices.pi_device_type' => config('_const.pi_device_types'),
            // 'pi_devices.ship_status' => config('_const.pi_ship_statuses'),
            // 'pi_devices.catcher_ship_status' => config('_const.catcher_ship_statuses'),
            // 'pi_devices.test' => config('_const.test_stage_labels'),
            // 'pi_devices.watch' => config('_const.watch_labels'),
            // 'pi_devices.active' => config('_const.active_labels'),
            // 'pi_devices.ccb_commands' => config('_const.ccb_commands'),
            // 'pi_devices.catcher_commands' => config('_const.catcher_commands'),
            // 'pi_devices.ccb_central_commands' => config('_const.ccb_central_commands'),
            // 'pi_devices.name_by_cpuid' => PiDeviceDa::getNamesByCpuId(),
            // 'lang.speech_lang_codes' => config('_const.speech_lang_codes'),
            // 'lang.lang_intl_names' => config('_const.lang_intl_names'),
            // 'lang.lang_short_names' => config('_const.lang_short_names'),
            // 'users.allowed_apps' => config('_const.allowed_apps'),
            // 'users.allowed_apps_v4' => config('_const.allowed_apps_v4'),
            // 'users.roles' => config('_const.roles'),
            // 'users.roles_v4' => config('_const.roles_v4'),
            // 'users.names' => UserDa::getUserNames(),
            // 'users.name' => UserDa::getUserNames(),
            // 'radio_cities.city_long_code' => RadioCityDa::getCityLongNameFromCode($option_type),
            // 'auto_reply_logs.called_types' => AutoReplyLogDa::getCalledNoByType(),
            // 'hospital.subjects' => HospitalDa::getSubjectNames(),
            // 'hospital.names' => HospitalDa::getHospitalKvs(),
            // 'fire_actions.action_names' => FireActionDa::getActionNameKvs(),
            // 'fire_areas.area_names' => FireAreaDa::getAreaNameKvs(),
            // 'groups.group_name_by_code' => GroupDa::getGroupNameByCodeKvs($code),
            // 'groups.group_name_by_id' => GroupDa::getGroupNameByIdKvs($code),
            // 'groups.group_full_names_by_id' => GroupDa::getGroupFullNamesByIdKvs($code),
            // 'groups.group_full_names_by_code' => GroupDa::getGroupFullNamesByCodeKvs($code),
            // 'group_categories.names_by_code' => GroupCategoryDa::getNamesByCodeKvs(),            
            // 'depts.names_by_code' => DeptDa::getNameByCodeKvs(),
            // 'depts.names_by_id' => DeptDa::getNameByIdKvs(),
            // 'depts.tels_by_id' => DeptDa::getTelByIdKvs(),
            // 'depts.tel_exts_by_id' => DeptDa::getTelExtByIdKvs(),
            // 'templates.category_by_category' => TemplateDa::getCategoryNamesByCategoryNameKvs(),
            // 'templates.full_names_by_id' => TemplateDa::getTemplateFullNamesByIdKvs(),
            // 'templates.name_by_code' => TemplateDa::getTemplateNamesByCodeKvs(),
            // 'clients.reg_types' => config('_const.clients_reg_type_view'),
            // 'stored_audio_files.melody_codes' => config('_const.melody_codes'),
            // 'speakers.areas' => SpeakerDa::getAreas(),
            // 'stored_audio_files.chime_codes' => StoredAudioFileDa::getAvailableChimeCodes(),
            // 'shelters.names_by_id' => ShelterDa::getNamesByIds(),
            // default => [],
            // 'months.2yr' => self::getMonths2Yr(),
            // 'device.name_by_code' => MmsUtil::getEnabledDeviceNamesByCode(with_cbs_child: true),
            // 'device.report_devices' => MmsUtil::getReportDeviceNamesByCode(),
            default => [],
        };
        
        return $kvs;
    }

    public static function getLabelFromCode(string $type, string $code): string
    {
        $label = match ($type) {
            // 'pi_devices.device_type' => config("_const.device_types.{$code}"),
            // 'pi_devices.pi_device_type' => config("_const.pi_device_types.{$code}"),
            // 'pi_devices.pi_device_types_abbreb' => config("_const.pi_device_types_abbreb.{$code}"),
            // 'pi_devices.ship_status' => config("_const.pi_ship_statuses.{$code}"),
            // 'pi_devices.test' => config("_const.test_stage_labels.{$code}"),
            // 'pi_devices.watch' => config("_const.watch_labels.{$code}"),
            // 'pi_devices.active' => config("_const.active_labels.{$code}"),
            // 'users.name' => UserDa::getNameById($code),
            // 'auto_reply_logs.called_type' => AutoReplyLogDa::getCalledTypeByNo($code),
            // 'fire_actions.action_names' => FireActionDa::getActionNameById($code),
            // 'fire_areas.area_names' => FireAreaDa::getAreaNameById($code),
            // 'groups.group_name_by_id' => GroupDa::getGroupNameById(intval($code)),
            // 'groups.group_name_by_code' => GroupDa::getGroupNameByCode($code),
            // 'group_categories.names_by_code' => GroupCategoryDa::getNameByCode($code),            
            default => '',
        };

        return $label;
    }
}
