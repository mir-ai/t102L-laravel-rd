<?php

namespace App\Lib\Mir;

use Exception;

/**
 * Amazon Pollyでの音声合成
 */
class MirTextToSpeechAmazon
{
    /**
     * Amazon Pollyで音声合成ファイルを作成し、一時ファイルに保存して返す。
     *
     * @param string $ssml
     * @param string $amzn_lang_code
     * @param string $voice_id
     * @param boolean $quality_high
     * @param string $sample_rate
     * @return string
     */
    public static function generatePollySpeechFile(
        string $ssml = '<speak>Hi</speak>',
        string $amzn_lang_code = 'en-US',
        string $voice_id = 'Ruth',
        bool $quality_high = true,
        string $sample_rate = '22050',
    ): string
    {
        $content = self::getAmazonPollyContent(
            ssml: $ssml,
            amzn_lang_code: $amzn_lang_code,
            voice_id: $voice_id,
            quality_high: $quality_high,
            sample_rate: $sample_rate
        );

        $speech_local_abs = MirTmpFile::save('polly', 'mp3', $content);

        $speech_app_path = MirUtil::fullPathToAppPath(
            $speech_local_abs
        );

        //logger("generatePollySpeechFile {$speech_local_abs} {$speech_app_path} {$ssml}");

        return $speech_app_path;
    }

    /**
     * Get binary body of generated speech file.
     *
     * @param string $ssml
     * @param string $amzn_lang_code
     * @param string $voice_id
     * @param boolean $quality_high
     * @param string $sample_rate
     * @return mixed
     */
    public static function getAmazonPollyContent(
        string $ssml = '<speak>Hi</speak>',
        string $amzn_lang_code = 'en-US',
        string $voice_id = 'Ruth',
        bool $quality_high = true,
        string $sample_rate = '22050',        
    ): mixed
    {
        $engine = ($quality_high) ? 'neural' : 'standard';

        if (! $voice_id) {
            $voice_id = match($amzn_lang_code) {
                'ja-JP' => 'Takumi',
                'en-US' => 'Ruth',
                'cmn-CN' => 'Zhiyu',
                'ko-KR' => 'Seoyeon',
                'default' => 'Takumi',
            };
            $engine = 'neural';
        }

        logger("amazonPolly {$engine} {$amzn_lang_code} {$voice_id} {$ssml}");

        try {
            $polly = \AWS::createClient('polly');
            $result_polly = $polly->synthesizeSpeech([
                "OutputFormat" => "mp3",
                "Text" => $ssml,
                "TextType" => "ssml",
                "VoiceId" => $voice_id,
                "Engine" => $engine,
                "LanguageCode" => $amzn_lang_code,
                "SampleRate" => (string)$sample_rate,
            ]);

            $contents = $result_polly->get("AudioStream")->getContents();

        } catch (Exception $e) {

            MirUtil::logAlert("[ERR] AWS::createClient('polly'): ssml={$ssml}, lang={$amzn_lang_code}, voice_id={$voice_id}: {$e}");

        }

        if (empty($contents)) {
            MirUtil::error_abort("[ERR] AWS::createClient('polly'): ssml={$ssml}, lang={$amzn_lang_code}, voice_id={$voice_id}: ");
        }

        return $contents;
    }    
}
