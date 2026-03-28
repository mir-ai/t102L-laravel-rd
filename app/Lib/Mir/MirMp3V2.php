<?php namespace App\Lib\Mir;

use Exception;
use Illuminate\Support\Facades\Storage;

class MirMp3V2 {

    public static function noise_reduct_and_mp3($in_local_abs, $remove_first_sec = 0) : string
    {
        $out_local_abs_mp3 = "{$in_local_abs}.mp3";

        $stdout = MirMp3V2::exec_ffmpeg(
            "-i {$in_local_abs} -af \"afftdn=nf=-25,highpass=f=200,lowpass=f=3000\" -ss {$remove_first_sec} -acodec libmp3lame -ac 1 -ar 22050 -ab 128k {$out_local_abs_mp3} 2>&1",
            "noise_reduct_and_convert_mp3"
        );

        if (empty($stdout)) {
            throw new Exception("MirMp3V2::noise_reduct_and_mp3 could not make mp3 from {$in_local_abs}");
        }

        return $out_local_abs_mp3;
    }
    
    public static function to_wav($in_local_abs) : string
    {
        $out_local_abs_mp3 = "{$in_local_abs}.wav";

        $stdout = MirMp3V2::exec_ffmpeg(
            "-i {$in_local_abs} -y -af \"afftdn=nf=-25,highpass=f=200,lowpass=f=3000\" -ac 1 -acodec pcm_s16le -ar 16000 {$out_local_abs_mp3} 2>&1",
            "speech_recognition_wav"
        );

        if (empty($stdout)) {
            throw new Exception("MirMp3V2::to_wav could not make wav from {$in_local_abs}");
        }

        return $out_local_abs_mp3;
    }
    

    public static function adjust_rate_volume(string $in_local_abs_path, int $target_volume_db = -1, string $sample_rate = '22050')
    {
        $max_volume = MirMp3V2::detect_max_volume($in_local_abs_path);

        //logger("max_volume = {$max_volume}");

        if ($max_volume < -26) {
            // ほぼ無音の音源は、増幅しない（無音ノイズが増幅されて配信されるのを防止する）
            $max_volume = -1;
        }

        // adjust max_volume
        $delta_dB = ($target_volume_db - $max_volume);

        //logger("new_volume = {$new_volume}");

        $out_abs_path = MirMp3V2::change_volume($in_local_abs_path, $delta_dB, $sample_rate);

        //logger("[CHG_VOL] {$max_volume}dB + {$delta_dB}dB for {$out_abs_path}");

        return $out_abs_path;
    }

    public static function detect_max_volume(string $local_abs_path)
    {
        $stdout = MirMp3V2::exec_ffmpeg(
            "-i {$local_abs_path} -vn -af volumedetect -f null - 2>&1",
            "volume_detect"
        );

        if (empty($stdout)) {
            return -1;
        }

        $max_volume = MirMp3V2::detect_volume($stdout);

        return $max_volume;
    }

    public static function change_volume(string $in_abs_path, int $delta_dB, string $sample_rate)
    {

        $out_abs_path = str_replace('.', "_{$delta_dB}dB.", $in_abs_path);

        $output = MirMp3V2::exec_ffmpeg(
            "-i {$in_abs_path} -af volume={$delta_dB}dB -acodec libmp3lame -ac 1 -ar {$sample_rate} -ab 128k {$out_abs_path} 2>&1",
            "change volume"
        );

        if ($output == null) {
            return $in_abs_path;
        }

        return $out_abs_path;
    }

    private static function exec_ffmpeg($cmd, $comment = '') : array
    {
        $output = [];

        $ffmpeg = storage_path('bin/ffmpeg');

        if (config('_env.LOCAL_FFMPEG_BIN')) {
            $ffmpeg = config('_env.LOCAL_FFMPEG_BIN');
        }

        if (empty($ffmpeg)) {
            MirUtil::error_abort("FFMPEG_BIN not defined in .env");
        }

        $exec_cmd = "{$ffmpeg} -y {$cmd}";

        $s = hrtime(true);
        exec($exec_cmd, $output, $retcode);
        $elapsed_ms = (hrtime(true) - $s) / 1e+6;

        //logger("[FFMPEG] ret={$retcode} [{$exec_cmd}] elapsed_ms={$elapsed_ms}");

        //logger("number_format((float)$esapsed_ms/1000/1000/1000) . " FFMPEG_RUN {$comment} {$cmd}");

        if ($retcode !== 0) {
            MirUtil::logAlert("[ERR] ffmpeg {$comment} [RET={$retcode}] {$exec_cmd} OUT=" . implode(',', $output));
            return [];
        }

        return $output;
    }

    public static function exec_sox($cmd, $comment = '') : array
    {
        $output = [];

        $sox = storage_path('bin/sox');

        // Linux 
        if (file_exists('/usr/local/bin/sox')) {
            $sox = '/usr/local/bin/sox';
        }

        if (config('_env.LOCAL_SOX_BIN')) {
            $sox = config('_env.LOCAL_SOX_BIN');
        }

        if (empty($sox)) {
            MirUtil::error_abort("LOCAL_SOX_BIN not defined in .env");
        }

        $exec_cmd = "{$sox} {$cmd}";

        $s = hrtime(true);
        exec($exec_cmd, $output, $retcode);
        $elapsed_ms = (hrtime(true) - $s) / 1e+6;

        logger("[SOX] ret={$retcode} [{$exec_cmd}] elapsed_ms={$elapsed_ms}");

        //logger("number_format((float)$esapsed_ms/1000/1000/1000) . " FFMPEG_RUN {$comment} {$cmd}");

        if ($retcode !== 0) {
            MirUtil::logAlert("[ERR] sox {$comment} [RET={$retcode}] {$exec_cmd} OUT=" . implode(',', $output));
            return [];
        }

        return $output;
    }

    public static function join_mp3(array $mp3_local_abspaths, $out_local_abspath)
    {
        $concat_content = "";

        if (! $mp3_local_abspaths) {
            return '';
        }

        foreach ($mp3_local_abspaths as $mp3_abspath) {
            if ($mp3_abspath && is_file($mp3_abspath)) {
                $concat_content .= "file '{$mp3_abspath}'\n";
            } else {
                // logger("[SKIP] MirMp3V3::join_mp3 skip include mp3 file not exist. '{$mp3_abspath}'");
            }
        }
        $concat_abs_filename = MirTmpFile::save('concat', 'txt', $concat_content);

        $output = MirMp3V2::exec_ffmpeg(
            "-f concat -safe 0 -i {$concat_abs_filename} -c copy {$out_local_abspath} 2>&1",
            "join_mp3"
        );

        MirTmpFile::safe_unlink($concat_abs_filename);

        if ($output == null) {
            MirUtil::error_abort("[ERR] join_mp3 2 Could not join mp3");
        }

        //logger("joined_mp3 {$out_local_abspath}");

        return $out_local_abspath;
    }    

    public static function get_duration_ms(string $in_abspath) : string
    {
        $output = MirMp3V2::exec_ffmpeg(
            "-i {$in_abspath} -f null - 2>&1",
            "detect_duration"
        );

        if ($output == null) {
            return 66; // いつか動かなくなったら常にLINEの音源が66秒になるので、ここを見つけて直して
        }
        
        $duration_sec = 0;
        if (preg_match('/Duration: (\d\d):(\d\d):(\d\d).(\d\d)/', implode('', $output), $matches)) {
            [$all, $h, $m, $s, $ds] = $matches;
            $duration_sec = intval($h) * 60 * 60 + intval($m) * 60 + intval($s);
        }

        $duration_ms = $duration_sec * 1000;
        return $duration_ms;
    }

    public static function convert_m4a(string $in_local_abs_path) : string {

        $tmp_abspath = str_replace('.mp3', ".m4a", $in_local_abs_path);

        $output = MirMp3V2::exec_ffmpeg(
            "-i {$in_local_abs_path} -c:a aac -vn {$tmp_abspath} 2>&1",
            "mp3 to m4a"
        );

        if ($output == null) {
            $tmp_abspath = $in_local_abs_path;
        }

        return $tmp_abspath;
    }

    public static function detect_volume(array $stdout)
    {
        // max_volumeは瞬間的なノイズの最高値を検知してしまうので、上位20%のときのdBを取得するように変更。
        /*
        stdout

        [Parsed_volumedetect_0 @ 0x2c0d540] n_samples: 1413846
        [Parsed_volumedetect_0 @ 0x2c0d540] mean_volume: -31.3 dB
        [Parsed_volumedetect_0 @ 0x2c0d540] max_volume: -14.4 dB
        [Parsed_volumedetect_0 @ 0x2c0d540] histogram_14db: 10
        [Parsed_volumedetect_0 @ 0x2c0d540] histogram_15db: 120
        [Parsed_volumedetect_0 @ 0x2c0d540] histogram_16db: 398
        [Parsed_volumedetect_0 @ 0x2c0d540] histogram_17db: 1037
        */

        $max_volume1 = -1;
        $db_count = [];
        $cnt_ttl = 0;
        foreach ($stdout as $line) {
            if (strpos($line, 'max_volume') !== false) {
                // Modify if ffmpeg version has changed.
                list($a, $b, $c, $d, $e, $f) = explode(' ', $line);
                if (is_numeric($e)) {
                    $max_volume1 = $e;
                }
            }

            if (preg_match('/histogram_(\d+)db: (\d+)/', $line, $matches)) {
                #logger("DETECTVOL {$line}");
                $dB = $matches[1];
                $cnt = $matches[2];
                $db_count[$dB] = $cnt;
                $cnt_ttl += $cnt;
            }
        }

        if (empty($db_count)) {
            return $max_volume1;
        }

        $cnt_sum = 0;
        $threash = $cnt_ttl * 0.5;
        foreach ($db_count as $dB => $cnt) {
            $cnt_sum += $cnt;

            if ($cnt_sum > $threash) {
                $v = $dB * -1;
                return $dB * -1;
            }
        }

        #logger("DETECTVOL VOL={$max_volume1}");

        return $max_volume1;
    }

    public static function trim_silent_mp3($in_local_abs, $silence_detect_sec = 0, $silence_detect_threshold = 0) : string
    {
        if (! $silence_detect_sec) {
            $silence_detect_sec = config('_env.CATCHER_SILENCE_DETECT_SEC');
        }

        if (! $silence_detect_threshold) {
            $silence_detect_threshold = config('_env.CATCHER_SILENCE_DETECT_THRESHOLD');
        }

        $out_local_abs_mp3 = str_replace(
            '.mp3',
            '_trimmed.mp3',
            $in_local_abs
        );

        $stdout = MirMp3V2::exec_sox(
            "{$in_local_abs} {$out_local_abs_mp3} silence 1 {$silence_detect_sec} {$silence_detect_threshold} reverse silence 1 {$silence_detect_sec} {$silence_detect_threshold} reverse 2>&1",
            "trim silence"
        );

        if ($stdout) {
            MirUtil::logAlert("MirMp3V2::trim_silent_mp3 could not trim mp3 from {$in_local_abs}:", $stdout);
        }

        if (! file_exists($out_local_abs_mp3)) {
            return $in_local_abs;
        }

        return $out_local_abs_mp3;
    }
}
