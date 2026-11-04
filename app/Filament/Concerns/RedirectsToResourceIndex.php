<?php

namespace App\Filament\Concerns;

/**
 * Sends the admin back to the resource listing after Create/Edit, preserving the
 * listing query string (search, filters, page, sort) when the previous URL is a
 * same-host URL for this resource's index page.
 */
trait RedirectsToResourceIndex
{
    protected function getRedirectUrl(): string
    {
        $previousUrl = $this->previousUrl ?? null;

        if (is_string($previousUrl) && $this->isSafeResourceIndexUrl($previousUrl)) {
            return $previousUrl;
        }

        return $this->getResourceUrl();
    }

    protected function isSafeResourceIndexUrl(string $url): bool
    {
        $previousScheme = parse_url($url, PHP_URL_SCHEME);
        $previousHost = parse_url($url, PHP_URL_HOST);
        $previousPath = parse_url($url, PHP_URL_PATH);

        $indexUrl = $this->getResourceUrl();
        $indexHost = parse_url($indexUrl, PHP_URL_HOST);
        $indexPath = parse_url($indexUrl, PHP_URL_PATH);

        if (! is_string($previousPath) || ! is_string($indexPath)) {
            return false;
        }

        if (filled($previousScheme) && ! in_array(strtolower($previousScheme), ['http', 'https'], true)) {
            return false;
        }

        $allowedHosts = array_filter([$indexHost, request()->getHost()]);

        if (filled($previousHost) && ! in_array($previousHost, $allowedHosts, true)) {
            return false;
        }

        return rtrim($previousPath, '/') === rtrim($indexPath, '/');
    }
}
