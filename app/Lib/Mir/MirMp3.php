<?php namespace App\Lib\Mir;

use Exception;
use Illuminate\Support\Facades\Storage;

class MirMp3 {

    public static function get_max_volume(string $in_abspath) : string
    {
        $output = MirMp3::exec_ffmpeg(
            "-i {$in_abspath} -vn -af volumedetect -f null - 2>&1",
            "volume detect"
        );

        if ($output == null) {
            return -1;
        }

        $max_volume = 0;
        foreach ($output as $line) {
            if (strpos($line, 'max_volume') !== false) {
                // Modify if ffmpeg version has changed.
                list($a, $b, $c, $d, $e, $f) = explode(' ', $line);
                if (is_numeric($e)) {
                    $max_volume = $e;
                }
            }
        }
        //logger("max_volume {$max_volume} {$in_abspath}");

        return $max_volume;
    }

    public static function change_volume(string $in_abspath, string $adjust_db, int $sampling_rate = 44100) : string
    {
        $out_abspath = str_replace('.', '_chg_vlm.', $in_abspath);

        $output = MirMp3::exec_ffmpeg(
            "-i {$in_abspath} -af volume={$adjust_db}dB -ar {$sampling_rate} -ab 128k {$out_abspath} 2>&1",
            "change volume"
        );

        if ($output == null) {
            return $in_abspath;
        }

        return $out_abspath;
    }

    public static function adjust_volume(string $in_abspath, int $target_volume_db = -1, int $sampling_rate = 44100) : string
    {
        // get current max_volume
        $max_volume = MirMp3::get_max_volume($in_abspath);

        if ($max_volume < -26) {
            // ほぼ無音の音源は、増幅しなくてもよいのでは
            $max_volume = -1;
        }

        // adjust max_volume
        $new_volume = ($target_volume_db - $max_volume);

        $out_abspath = MirMp3::change_volume($in_abspath, $new_volume, $sampling_rate);

        return $out_abspath;
    }

    public static function _wav2mp3($src_filename, $atempo = 1)
    {
        $dst_filename = str_replace('.wave', '.mp3', $src_filename);
        $dst_filename = str_replace('.wav', '.mp3', $dst_filename);

        if (! str_contains($dst_filename, '.mp3')) {
            $dst_filename = $dst_filename . '.mp3';
        }

        // 出力先が既に存在している場合は消す（ユニットテスト時などに発生）
        MirTmpFile::safe_unlink($dst_filename);

        $output = MirMp3::exec_ffmpeg(
            "-i {$src_filename} -af \"afftdn=nf=-25,highpass=f=200,lowpass=f=3000\" -vn -ac 1 -ar 44100 -ab 128k -acodec libmp3lame -f mp3 -af atempo={$atempo} {$dst_filename} 2>&1",
            "wav2mp3"
        );

        if ($output == null) {
            MirUtil::error_abort("[ERR] _wav2mp3 Could not make mp3 from {$src_filename}");
        }

        return $dst_filename;
    }

    public static function _m4a2mp3(string $in_abspath) : string {

        $tmp_abspath = str_replace('.m4a', ".mp3", $in_abspath);

        $output = MirMp3::exec_ffmpeg(
            "-i {$in_abspath} -af \"afftdn=nf=-25,highpass=f=200,lowpass=f=3000\" -c:v copy -c:a libmp3lame -ac 1 -ar 44100 -ab 128k {$tmp_abspath} 2>&1",
            "_m4a2mp3"
        );

        if ($output == null) {
            MirUtil::error_abort("[ERR] _m4a2mp3 Could not make mp3 from {$in_abspath}");
        }

        return $tmp_abspath;
    }

    public static function _aac2mp3(string $in_abspath) : string {

        $tmp_abspath = str_replace('.aac', ".mp3", $in_abspath);

        $output = MirMp3::exec_ffmpeg(
            "-i {$in_abspath} -af \"afftdn=nf=-25,highpass=f=200,lowpass=f=3000\" -c:v copy -c:a libmp3lame -ac 1 -ar 44100 -ab 128k {$tmp_abspath} 2>&1",
            "_aac2mp3"
        );

        if ($output == null) {
            MirUtil::error_abort("[ERR] _aac2mp3 Could not make mp3 from {$in_abspath}");
        }

        return $tmp_abspath;
    }

    public static function noise_reduction(string $in_abspath) : string {

        $tmp1_abspath = str_replace('.', '_nr1.', $in_abspath);

        $output = MirMp3::exec_ffmpeg(
            "-i {$in_abspath} -af \"afftdn=nf=-25,highpass=f=200,lowpass=f=3000\" -ac 1 -ar 44100 -ab 128k {$tmp1_abspath} 2>&1",
            "noise reduction"
        );

        if ($output == null) {
            $tmp1_abspath = $in_abspath;
        }

        return $tmp1_abspath;
    }

    public static function join_mp3(array $mp3_abspaths, $out_abspath)
    {
        $concat_content = "";
        foreach ($mp3_abspaths as $mp3_abspath) {
            if ($mp3_abspath && is_file($mp3_abspath)) {
                $concat_content .= "file '{$mp3_abspath}'\n";
            }
        }

        $concat_abs_filename = MirTmpFile::save('concat', 'txt', $concat_content);

        $output = MirMp3::exec_ffmpeg(
            "-f concat -safe 0 -i {$concat_abs_filename} -c copy {$out_abspath} 2>&1",
            "join_mp3"
        );

        MirTmpFile::safe_unlink($concat_abs_filename);
        /*
        Storage::disk('local')->delete(
            $contat_rel_filename
        );
        */

        if ($output == null) {
            MirUtil::error_abort("[ERR] join_mp3 1 Could not join mp3");
        }

        return $out_abspath;
    }

    public static function convert_m4a(string $in_abspath) : string {

        $tmp_abspath = str_replace('.mp3', ".m4a", $in_abspath);

        $output = MirMp3::exec_ffmpeg(
            "-i {$in_abspath} -c:a aac -vn {$tmp_abspath} 2>&1",
            "mp3 to m4a"
        );

        if ($output == null) {
            $tmp_abspath = $in_abspath;
        }

        return $tmp_abspath;
    }

    public static function get_duration_sec(string $in_abspath) : string
    {
        $output = MirMp3::exec_ffmpeg(
            "-i {$in_abspath} -f null - 2>&1",
            "detect duration"
        );

        if ($output == null) {
            return 66; // いつか動かなくなったら常にLINEの音源が66秒になるので、ここを見つけて直して
        }

        $duration_sec = 66; // いつか動かなくなったら常にLINEの音源が66秒になるので、ここを見つけて直して

        foreach ($output as $line) {
            $pos = strpos($line, 'time=');

            if ($pos !== false) {

                $line = substr($line, $pos);

                // size= N/A time=00:00:40.68 bitrate=N/A speed=1.41e+03x
                [$f] = sscanf($line, "time=%s");

                // 00:00:40.68
                list($h, $m, $s) = explode(':', $f);

                $duration_sec = intval($h) * 60 * 60 + intval($m) * 60 + floatval($s);
            }
        }
        return $duration_sec;
    }

    public static function speech_recognition_audio(string $in_abspath) : string
    {
        //logger("speech_recognition_audio {$in_abspath}");

        $out_abspath = $in_abspath;
        $out_abspath = str_replace('.mp3', ".wav", $out_abspath);
        $out_abspath = str_replace('.m4a', ".wav", $out_abspath);
        $out_abspath = str_replace('.aac', ".wav", $out_abspath);
        $out_abspath = str_replace('.wav', "_speech.wav", $out_abspath);

        $output = MirMp3::exec_ffmpeg(
            "-i {$in_abspath} -y -af \"afftdn=nf=-25,highpass=f=200,lowpass=f=3000\" -ac 1 -acodec pcm_s16le -ar 16000 {$out_abspath} 2>&1",
            "speech recognition wav"
        );

        return $out_abspath;
    }

    private static function exec_ffmpeg($cmd, $comment = '')
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

        $s =  hrtime(true);
        exec($exec_cmd, $output, $retcode);
        //logger("exec_ffmpeg cmd='{$exec_cmd}' retcode='{$retcode}'", $output);
        $esapsed_ms = hrtime(true) - $s;

        //logger("number_format((float)$esapsed_ms/1000/1000/1000) . " FFMPEG_RUN {$comment} {$cmd}");

        if ($retcode !== 0) {
            MirUtil::logAlert("[ERR] ffmpeg {$comment} [RET={$retcode}] {$exec_cmd} OUT=" . implode(',', $output));
            return null;
        }

        return $output;
    }

    /*
    \App\Lib\Mir\MirMp3::adjust_volume('/Users/obatasusumu/Desktop/wav_samp/org/c1.mp3');
    \App\Lib\Mir\MirMp3::get_max_volume('/Users/obatasusumu/Desktop/wav_samp/org/c1.mp3');
    \App\Lib\Mir\MirMp3::change_volume('/Users/obatasusumu/Desktop/wav_samp/org/c1.mp3', 0);
    \App\Lib\Mir\MirMp3::_wav2mp3('/Users/obatasusumu/Desktop/wav_samp/org/c5.wav', 1.5);
    \App\Lib\Mir\MirMp3::join_mp3(['/Users/obatasusumu/Desktop/wav_samp/org/c1.mp3', '/Users/obatasusumu/Desktop/wav_samp/org/c2.mp3'], '/Users/obatasusumu/Desktop/wav_samp/org/all.mp3');
    \App\Lib\Mir\MirMp3::noise_reduction('/Users/obatasusumu/Desktop/wav_samp/org/c4.mp3');
    */

}
