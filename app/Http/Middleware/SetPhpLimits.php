<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPhpLimits
{
    /**
     * Aumenta os limites de PHP para uploads pesados e processamentos longos.
     *
     * ATENÇÃO:
     *   - memory_limit e max_execution_time funcionam normalmente via ini_set() em runtime.
     *   - upload_max_filesize e post_max_size SÃO ignorados aqui: o PHP lê esses valores
     *     antes de processar qualquer requisição, portanto ini_set() não tem efeito sobre eles.
     *     Para alterá-los, use public/.user.ini (PHP-FPM / artisan serve) ou public/.htaccess (Apache).
     */
    public function handle(Request $request, Closure $next): Response
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');

        return $next($request);
    }
}
