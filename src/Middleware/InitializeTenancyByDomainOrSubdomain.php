<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Closure;
use Illuminate\Support\Str;

class InitializeTenancyByDomainOrSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,bool $useRefererHeader = false)
    {
        $referrerHeaderHostname = null;

        if($useRefererHeader && $request->headers->has('HTTP_REFERRER') ){
            $referrerHeaderHostname = $request->getHeader('HTTP_REFERRER');
        }

        if ($this->isSubdomain($referrerHeaderHostname ?? $request->getHost())) {
            return app(InitializeTenancyBySubdomain::class)->handle($request, $next,isset($referrerHeaderHostname));
        } else {
            return app(InitializeTenancyByDomain::class)->handle($request, $next,isset($referrerHeaderHostname));
        }
    }

    protected function isSubdomain(string $hostname): bool
    {
        return Str::endsWith($hostname, config('tenancy.central_domains'));
    }
}
