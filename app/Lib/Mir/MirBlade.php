<?php

namespace App\Lib\Mir;

use Illuminate\Support\Facades\Blade;

/**
 * Bladeに独自コマンド追加
 */
class MirBlade
{
    // Blade コマンド

    /**
     * 相対パスで指定可能な blade include コマンド
     * 
     */
    public static function relativeInclude($args, $app)
    {
        $args = Blade::stripParentheses($args);
        $viewBasePath = Blade::getPath() ?? '';
        foreach ($app['config']['view.paths'] as $path) {
            if (substr($viewBasePath, 0, strlen($path)) === $path) {
                $viewBasePath = substr($viewBasePath, strlen($path));
                break;
            }
        }

        $viewBasePath = dirname(trim($viewBasePath, '\/'));
        $args = substr_replace($args, $viewBasePath . '.', 1, 0);
        return "<?php echo \$__env->make({$args}, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
    }
}
