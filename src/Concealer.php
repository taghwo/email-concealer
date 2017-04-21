<?php

namespace Spatie\EmailConcealer;

class Concealer
{
    const REGEX = '/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i';

    /** @var string */
    protected $domain = 'example.com';

    public static function create()
    {
        return new static();
    }

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function domain(string $domain)
    {
        $this->domain = $domain;

        return $this;
    }

    public function conceal(string $string): string
    {
        $emails = $this->extractEmails($string);

        return ConcealedEmailCollection::make($this->domain, $emails)
            ->reduce(function (string $string, string $concealedEmail, string $originalEmail) {
                return str_replace($originalEmail, $concealedEmail, $string);
            }, $string);
    }

    protected function extractEmails(string $string): array
    {
        $matches = [];
        preg_match_all(static::REGEX, $string, $matches);

        if (! $matches) {
            return [];
        }

        return $matches[0] ?? [];
    }
}